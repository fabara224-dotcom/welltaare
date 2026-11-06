<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $admin = config('shop.admin');
        User::updateOrCreate(
            ['email' => $admin['email']],
            ['name' => $admin['name'], 'password' => $admin['password'], 'is_admin' => true, 'role' => 'admin']
        );

        // Compte développeur (optionnel) : DEV_EMAIL / DEV_PASSWORD dans .env
        if (env('DEV_EMAIL') && env('DEV_PASSWORD')) {
            User::updateOrCreate(
                ['email' => env('DEV_EMAIL')],
                ['name' => env('DEV_NAME', 'Développeur'), 'password' => env('DEV_PASSWORD'), 'is_admin' => true, 'role' => 'developer']
            );
        }

        $categories = [
            ['Robes élégantes', 'robes-elegantes', 'femme'],
            ['Robes sexy', 'robes-sexy', 'femme'],
            ['Robes de soirée', 'robes-de-soiree', 'femme'],
            ['Ensembles', 'ensembles-femme', 'femme'],
            ['Tops & corsets', 'tops-corsets', 'femme'],
            ['Tenues africaines femme', 'tenues-africaines-femme', 'femme'],
            ['Costumes africains', 'costumes-africains', 'homme'],
            ['Grands boubous', 'grands-boubous', 'homme'],
            ['Chemises & tuniques', 'chemises-tuniques', 'homme'],
        ];
        foreach ($categories as $i => [$name, $slug, $gender]) {
            Category::updateOrCreate(['slug' => $slug], ['name' => $name, 'gender' => $gender, 'position' => $i]);
        }

        if (filter_var(env('SEED_DEMO', false), FILTER_VALIDATE_BOOLEAN)) {
            $this->call(DemoProductSeeder::class);
        }
    }
}
