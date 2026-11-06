<?php

namespace App\Http\Controllers\Dev;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Setting;
use App\Models\User;
use App\Services\OrangeMoney;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

/**
 * Espace développeur : diagnostic, journaux d'erreurs, outils de maintenance,
 * comptes utilisateurs et lecture de la base de données.
 */
class DevController extends Controller
{
    private const HIDDEN_COLUMNS = ['password', 'remember_token', 'om_pay_token', 'om_notif_token', 'payload'];

    /* ---------------- Vue d'ensemble ---------------- */
    public function index(OrangeMoney $om)
    {
        $checks = [];
        $add = function ($label, $ok, $detail = '', $fix = null) use (&$checks) {
            $checks[] = compact('label', 'ok', 'detail', 'fix');
        };

        try {
            DB::connection()->getPdo();
            $add('Base de données', true, config('database.default').' · '.DB::connection()->getDatabaseName());
        } catch (\Throwable $e) {
            $add('Base de données', false, $e->getMessage());
        }

        $link = public_path('storage');
        $linkOk = is_link($link) && realpath($link) === realpath(storage_path('app/public'));
        $add('Lien des photos (public/storage)', $linkOk, $linkOk ? 'OK' : (file_exists($link) ? 'Existe mais ne pointe pas vers storage/app/public' : 'Absent'), $linkOk ? null : 'storage-link');

        foreach (['storage/framework/views', 'storage/framework/sessions', 'storage/framework/cache', 'storage/logs', 'storage/app/public', 'bootstrap/cache'] as $dir) {
            $ok = is_dir(base_path($dir)) && is_writable(base_path($dir));
            $add('Dossier '.$dir, $ok, $ok ? 'Accessible en écriture' : 'Manquant ou non modifiable', $ok ? null : 'make-dirs');
        }

        $toBytes = function ($v) {
            $v = trim((string) $v); $n = (int) $v;
            return match (strtolower(substr($v, -1))) { 'g' => $n * 1073741824, 'm' => $n * 1048576, 'k' => $n * 1024, default => $n };
        };
        $up = $toBytes(ini_get('upload_max_filesize'));
        $post = $toBytes(ini_get('post_max_size'));
        $okUp = $up >= 8 * 1048576 && $post >= 20 * 1048576;
        $add('Taille d\'envoi des fichiers (PHP)', $okUp, 'upload_max_filesize = '.ini_get('upload_max_filesize').', post_max_size = '.ini_get('post_max_size').($okUp ? '' : ' — conseillé : 10M et 40M dans php.ini (les photos sont déjà réduites automatiquement à l\'envoi)'));

        $add('Clé de chiffrement (APP_KEY)', (bool) config('app.key'), config('app.key') ? 'Définie' : 'Manquante : php artisan key:generate');
        $add('Mode debug', ! config('app.debug') || app()->environment('local'), config('app.debug') ? 'Activé'.(app()->environment('production') ? ' — à désactiver en production (APP_DEBUG=false)' : ' (normal en local)') : 'Désactivé');
        $add('Orange Money', $om->isConfigured(), $om->isConfigured() ? 'Configuré · mode '.config('services.orange_money.mode') : 'Identifiants ORANGE_MONEY_* à renseigner dans .env');

        $pending = $this->pendingMigrations();
        $add('Migrations', $pending === [], $pending === [] ? 'À jour' : count($pending).' migration(s) en attente : '.implode(', ', $pending), $pending === [] ? null : 'migrate');

        $logFile = storage_path('logs/laravel.log');
        $entries = $this->parseLog($logFile, 400);
        $errors24 = collect($entries)->filter(fn ($e) => in_array($e['level'], ['ERROR', 'CRITICAL', 'ALERT', 'EMERGENCY']) && strtotime($e['date']) > time() - 86400)->count();

        return view('dev.index', [
            'checks' => $checks,
            'info' => [
                'Application' => config('app.name'),
                'Environnement' => app()->environment(),
                'URL' => config('app.url'),
                'Laravel' => app()->version(),
                'PHP' => PHP_VERSION,
                'Serveur' => php_uname('s').' '.php_uname('r'),
                'Fuseau horaire' => config('app.timezone'),
                'Base de données' => config('database.default'),
                'Session / cache' => config('session.driver').' / '.config('cache.default'),
                'Limite upload PHP' => ini_get('upload_max_filesize').' (post : '.ini_get('post_max_size').')',
                'Mémoire PHP' => ini_get('memory_limit'),
                'Connexion (clients + admin)' => route('login'),
                'Espace de gestion' => url(config('shop.admin_path')),
                'Maintenance' => app()->isDownForMaintenance() ? 'ACTIVÉE' : 'Non',
            ],
            'counts' => $this->tableCounts(),
            'storage' => $this->dirSize(storage_path('app/public')),
            'logSize' => File::exists($logFile) ? File::size($logFile) : 0,
            'errors24' => $errors24,
            'lastErrors' => collect($entries)->whereIn('level', ['ERROR', 'CRITICAL', 'ALERT', 'EMERGENCY'])->take(5),
            'demoCount' => Product::where('sku', 'like', 'DEMO-%')->count(),
            'maintenanceSecret' => Setting::get('dev_maintenance_secret'),
        ]);
    }

    /* ---------------- Journaux ---------------- */
    public function logs(Request $request)
    {
        $level = strtoupper((string) $request->query('niveau'));
        $q = trim((string) $request->query('q'));
        $entries = collect($this->parseLog(storage_path('logs/laravel.log'), 1500))
            ->when($level, fn ($c) => $c->where('level', $level))
            ->when($q, fn ($c) => $c->filter(fn ($e) => Str::contains(Str::lower($e['message'].$e['trace']), Str::lower($q))));

        return view('dev.logs', [
            'entries' => $entries->take(200),
            'total' => $entries->count(),
            'levels' => ['ERROR', 'WARNING', 'INFO', 'DEBUG', 'CRITICAL'],
        ]);
    }

    public function clearLogs()
    {
        $file = storage_path('logs/laravel.log');
        if (File::exists($file)) {
            File::put($file, '');
        }

        return back()->with('status', 'Journal vidé.');
    }

    /* ---------------- Outils ---------------- */
    public function action(Request $request)
    {
        $action = $request->input('action');
        $out = '';

        try {
            switch ($action) {
                case 'clear-cache':
                    Artisan::call('optimize:clear');
                    Cache::forget('settings.all');
                    $msg = 'Caches vidés (configuration, routes, vues, cache applicatif).';
                    break;
                case 'optimize':
                    Artisan::call('optimize');
                    $msg = 'Application optimisée (configuration, routes et vues mises en cache). Pensez à vider les caches après chaque modification du code ou du .env.';
                    break;
                case 'storage-link':
                    $link = public_path('storage');
                    if (is_link($link)) {
                        unlink($link);
                    } elseif (is_dir($link)) {
                        File::deleteDirectory($link);
                    }
                    Artisan::call('storage:link');
                    $msg = 'Lien des photos recréé.';
                    break;
                case 'make-dirs':
                    foreach (['framework/views', 'framework/sessions', 'framework/cache/data', 'logs', 'app/public'] as $d) {
                        File::ensureDirectoryExists(storage_path($d));
                    }
                    File::ensureDirectoryExists(base_path('bootstrap/cache'));
                    $msg = 'Dossiers de stockage créés.';
                    break;
                case 'migrate':
                    Artisan::call('migrate', ['--force' => true]);
                    $msg = 'Migrations exécutées.';
                    break;
                case 'remove-demo':
                    $n = 0;
                    Product::where('sku', 'like', 'DEMO-%')->get()->each(function ($p) use (&$n) {
                        foreach ($p->images as $img) {
                            Storage::disk('public')->delete($img->path);
                        }
                        $p->delete();
                        $n++;
                    });
                    $msg = "{$n} produit(s) de démonstration supprimé(s).";
                    break;
                case 'maintenance-on':
                    $secret = Str::lower(Str::random(24));
                    Setting::put(['dev_maintenance_secret' => $secret]);
                    Artisan::call('down', ['--secret' => $secret, '--render' => 'errors::503']);

                    // Le cookie de contournement est posé en visitant /{secret}
                    return redirect('/'.$secret);
                case 'maintenance-off':
                    Artisan::call('up');
                    $msg = 'Site remis en ligne.';
                    break;
                case 'test-om':
                    $om = app(OrangeMoney::class);
                    if (! $om->isConfigured()) {
                        $msg = 'Orange Money n\'est pas configuré dans .env.';
                        break;
                    }
                    Cache::forget('orange_money.token');
                    $om->getToken();
                    $msg = 'Connexion Orange Money réussie : jeton obtenu.';
                    break;
                default:
                    return back()->withErrors(['action' => 'Action inconnue.']);
            }
            $out = trim(Artisan::output());
        } catch (\Throwable $e) {
            report($e);

            return back()->withErrors(['action' => 'Échec : '.$e->getMessage()]);
        }

        return back()->with('status', $msg)->with('output', $out);
    }

    /* ---------------- Comptes ---------------- */
    public function users()
    {
        return view('dev.users', ['users' => User::orderByDesc('is_admin')->orderBy('name')->get()]);
    }

    public function storeUser(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email', 'max:150', 'unique:users,email'],
            'role' => ['required', Rule::in(array_keys(User::ROLES))],
            'password' => ['required', Password::min(8)],
        ]);
        User::create($data + ['is_admin' => true]);

        return back()->with('status', 'Compte créé pour '.$data['email'].'.');
    }

    public function updateUser(Request $request, User $user)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'role' => ['required', Rule::in(array_keys(User::ROLES))],
            'password' => ['nullable', Password::min(8)],
            'is_admin' => ['nullable', 'boolean'],
        ]);
        if ($user->is($request->user()) && $data['role'] !== 'developer') {
            return back()->withErrors(['role' => 'Vous ne pouvez pas retirer votre propre accès développeur.']);
        }
        $user->name = $data['name'];
        $user->role = $data['role'];
        $user->is_admin = $user->is($request->user()) ? true : $request->boolean('is_admin');
        if (! empty($data['password'])) {
            $user->password = $data['password'];
        }
        $user->save();

        return back()->with('status', 'Compte '.$user->email.' mis à jour.');
    }

    public function destroyUser(Request $request, User $user)
    {
        if ($user->is($request->user())) {
            return back()->withErrors(['user' => 'Vous ne pouvez pas supprimer votre propre compte.']);
        }
        $user->delete();

        return back()->with('status', 'Compte supprimé.');
    }

    /* ---------------- Base de données (lecture seule) ---------------- */
    public function database(Request $request, ?string $table = null)
    {
        $tables = $this->tableCounts();
        $rows = null;
        $columns = [];

        if ($table) {
            abort_unless(array_key_exists($table, $tables), 404);
            $columns = array_values(array_diff(Schema::getColumnListing($table), self::HIDDEN_COLUMNS));
            $q = DB::table($table)->select($columns);
            if (in_array('id', $columns, true)) {
                $q->orderByDesc('id');
            }
            if (($s = trim((string) $request->query('q'))) !== '') {
                $q->where(function ($w) use ($columns, $s) {
                    foreach ($columns as $c) {
                        $w->orWhere($c, 'like', "%{$s}%");
                    }
                });
            }
            $rows = $q->paginate(25)->withQueryString();
        }

        return view('dev.database', compact('tables', 'table', 'rows', 'columns'));
    }

    /* ---------------- Utilitaires ---------------- */
    private function tableCounts(): array
    {
        $out = [];
        try {
            foreach (Schema::getTableListing(schemaQualified: false) as $t) {
                $t = (string) $t;
                if (str_starts_with($t, 'sqlite_')) {
                    continue;
                }
                $out[$t] = DB::table($t)->count();
            }
        } catch (\Throwable) {
            // base inaccessible : la vérification correspondante l'affiche
        }
        ksort($out);

        return $out;
    }

    private function pendingMigrations(): array
    {
        try {
            $ran = DB::table('migrations')->pluck('migration')->all();
        } catch (\Throwable) {
            return ['table migrations absente'];
        }
        $files = collect(File::files(database_path('migrations')))->map(fn ($f) => $f->getFilenameWithoutExtension());

        return $files->diff($ran)->values()->all();
    }

    private function dirSize(string $dir): int
    {
        if (! is_dir($dir)) {
            return 0;
        }
        $size = 0;
        foreach (File::allFiles($dir) as $f) {
            $size += $f->getSize();
        }

        return $size;
    }

    /** Lit la fin du fichier de log et découpe les entrées (les plus récentes d'abord). */
    private function parseLog(string $file, int $max): array
    {
        if (! File::exists($file) || File::size($file) === 0) {
            return [];
        }
        $size = File::size($file);
        $fh = fopen($file, 'r');
        $read = min($size, 2 * 1024 * 1024);
        fseek($fh, $size - $read);
        $content = fread($fh, $read);
        fclose($fh);

        $parts = preg_split('/^(?=\[\d{4}-\d{2}-\d{2}[ T]\d{2}:\d{2}:\d{2})/m', $content, -1, PREG_SPLIT_NO_EMPTY);
        $entries = [];
        foreach (array_reverse($parts) as $part) {
            if (! preg_match('/^\[([^\]]+)\]\s+\w+\.(\w+):\s?(.*)$/s', $part, $m)) {
                continue;
            }
            $lines = explode("\n", trim($m[3]));
            $entries[] = [
                'date' => substr($m[1], 0, 19),
                'level' => strtoupper($m[2]),
                'message' => Str::limit($lines[0], 600),
                'trace' => implode("\n", array_slice($lines, 1, 60)),
            ];
            if (count($entries) >= $max) {
                break;
            }
        }

        return $entries;
    }
}
