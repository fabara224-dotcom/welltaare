<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;

class CreateDeveloper extends Command
{
    protected $signature = 'welltaare:developpeur {email} {--name=Développeur} {--password=}';

    protected $description = 'Crée (ou met à jour) un compte développeur avec accès à l\'espace /gestion/dev';

    public function handle(): int
    {
        $email = strtolower($this->argument('email'));
        if (! filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->error('Adresse e-mail invalide.');

            return self::FAILURE;
        }

        $password = $this->option('password') ?: $this->secret('Mot de passe (8 caractères minimum)');
        if (strlen((string) $password) < 8) {
            $this->error('Le mot de passe doit contenir au moins 8 caractères.');

            return self::FAILURE;
        }

        User::updateOrCreate(['email' => $email], [
            'name' => $this->option('name'),
            'password' => $password,
            'is_admin' => true,
            'role' => 'developer',
        ]);

        $this->info("Compte développeur prêt : {$email}");
        $this->line('Connexion : '.url('/connexion').'  → menu « Développeur » dans l\'espace de gestion');

        return self::SUCCESS;
    }
}
