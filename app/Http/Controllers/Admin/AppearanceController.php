<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

/** Admin → Apparence : logo et photos des grandes zones du site */
class AppearanceController extends Controller
{
    public const SLOTS = [
        'logo' => ['Logo', 'Remplace le nom écrit en haut du site. PNG transparent conseillé, fond sombre.', '3 / 1'],
        'hero_femme' => ['Accueil – grande photo Femme', 'Photo verticale (portrait), au moins 1200 px de haut.', '3 / 4'],
        'hero_homme' => ['Accueil – grande photo Homme', 'Photo verticale (portrait), au moins 1200 px de haut.', '3 / 4'],
        'univers_femme' => ['Univers – Robes élégantes & soirée', 'Photo verticale.', '3 / 4'],
        'univers_africain' => ['Univers – Collections africaines', 'Photo verticale.', '3 / 4'],
        'univers_mesure' => ['Univers – Création sur mesure', 'Photo verticale (atelier, couture…).', '3 / 4'],
        'univers_design' => ['Univers – Infographie & design', 'Photo ou visuel de vos créations graphiques.', '3 / 4'],
        'atelier' => ['Section atelier (accueil + page sur mesure)', 'Photo de l\'atelier ou d\'une création.', '4 / 5'],
        'studio' => ['Page design graphique', 'Exemple de vos réalisations graphiques.', '4 / 3'],
        'complexe' => ['Page Le Complexe', 'Photo de la boutique, de l\'équipe ou d\'une collection.', '4 / 5'],
        'auth' => ['Pages connexion / inscription', 'Photo d\'ambiance, verticale.', '3 / 4'],
        'contact' => ['Page contact', 'Façade ou intérieur de la boutique.', '4 / 3'],
    ];

    public function edit()
    {
        return view('admin.appearance', ['slots' => self::SLOTS]);
    }

    public function update(Request $request)
    {
        $request->validate([
            'slot' => ['required', 'in:'.implode(',', array_keys(self::SLOTS))],
            'image' => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:8192'],
        ], [
            'image.required' => 'Choisissez une image.',
            'image.image' => 'Le fichier doit être une image.',
            'image.uploaded' => 'Image trop lourde pour le serveur (limite PHP '.ini_get('upload_max_filesize').').',
        ]);
        $key = 'img_'.$request->input('slot');
        if ($old = Setting::get($key)) {
            Storage::disk('public')->delete($old);
        }
        Setting::put([$key => $request->file('image')->store('site', 'public')]);

        return back()->with('status', 'Image « '.self::SLOTS[$request->input('slot')][0].' » mise à jour.');
    }

    public function destroy(string $slot)
    {
        abort_unless(array_key_exists($slot, self::SLOTS), 404);
        if ($old = Setting::get('img_'.$slot)) {
            Storage::disk('public')->delete($old);
        }
        Setting::put(['img_'.$slot => null]);

        return back()->with('status', 'Image retirée : le motif doré par défaut est affiché.');
    }
}
