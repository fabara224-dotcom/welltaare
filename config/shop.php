<?php

return [
    'name' => env('SHOP_NAME', 'COMPLEXE WELLTAARÉ COLLECTION'),
    'currency' => 'GNF',

    // Compte administrateur créé par le seeder (changez le mot de passe après la première connexion)
    'admin' => [
        'name' => env('ADMIN_NAME', 'Administrateur'),
        'email' => env('ADMIN_EMAIL', 'admin@welltaare.com'),
        'password' => env('ADMIN_PASSWORD', 'ChangezMoi2026!'),
    ],

    // Adresse secrète de l'espace admin : /{ADMIN_PATH}/login
    'admin_path' => env('ADMIN_PATH', 'gestion'),
];
