<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Seeder;

/**
 * Produits de démonstration (sans photos) pour voir le site rempli.
 * Supprimez-les depuis l'admin une fois vos vrais articles ajoutés.
 */
class DemoProductSeeder extends Seeder
{
    public function run(): void
    {
        $cat = fn (string $slug) => Category::where('slug', $slug)->value('id');

        $items = [
            ['robes-sexy', 'femme', 'Robe fendue en satin', 450000, null, ['S', 'M', 'L', 'XL'], ['Rouge', 'Noir', 'Champagne'], 'Robe longue ajustée en satin fluide, fente haute sur la jambe et fines bretelles croisées dans le dos.', true, true, 6],
            ['robes-sexy', 'femme', 'Robe moulante dos nu', 320000, 380000, ['S', 'M', 'L'], ['Bordeaux', 'Émeraude'], 'Maille stretch qui sculpte la silhouette, dos entièrement ouvert, longueur midi.', true, true, 8],
            ['robes-elegantes', 'femme', 'Robe longue drapée dorée', 520000, null, ['S', 'M', 'L', 'XL'], ['Or', 'Noir'], 'Robe longue en crêpe fluide, drapé à l\'épaule et ceinture dorée. Élégante pour les cérémonies.', true, true, 5],
            ['tenues-africaines-femme', 'femme', 'Ensemble pagne wax brodé', 420000, null, ['S', 'M', 'L', 'XL'], ['Wax noir & or', 'Wax bordeaux'], 'Haut ajusté et jupe longue en wax, broderies dorées au col. Tradition et modernité.', true, false, 6],
            ['robes-de-soiree', 'femme', 'Robe sirène à sequins', 780000, null, ['S', 'M', 'L', 'XL'], ['Or', 'Argent'], 'Robe sirène brodée de sequins, décolleté plongeant et traîne légère. Pour les grandes soirées et mariages.', false, true, 3],
            ['ensembles-femme', 'femme', 'Ensemble crop top & jupe en dentelle', 380000, 450000, ['S', 'M', 'L'], ['Noir', 'Blanc'], 'Top court et jupe crayon en dentelle doublée.', false, false, 5],
            ['tops-corsets', 'femme', 'Corset structuré en wax', 220000, null, ['S', 'M', 'L'], ['Wax rouge', 'Wax bleu'], 'Corset baleiné en tissu wax, laçage au dos. Se porte avec une jupe ou un pantalon taille haute.', true, false, 10],
            ['costumes-africains', 'homme', 'Costume 3 pièces en wax', 850000, null, ['M', 'L', 'XL', 'XXL'], ['Bleu & or', 'Vert & orange'], 'Veste cintrée, gilet et pantalon en wax, doublure satin. Finitions faites main.', true, true, 4],
            ['costumes-africains', 'homme', 'Costume col Mao brodé', 950000, 1100000, ['M', 'L', 'XL'], ['Noir', 'Bleu nuit'], 'Veste col Mao avec broderie ton sur ton au col et aux poignets, pantalon droit.', false, true, 3],
            ['grands-boubous', 'homme', 'Grand boubou brodé en bazin', 1200000, null, ['L', 'XL', 'XXL'], ['Blanc', 'Bleu ciel', 'Bordeaux'], 'Bazin riche, broderie main au col et sur la poitrine. Pantalon assorti inclus.', false, true, 2],
            ['chemises-tuniques', 'homme', 'Chemise col Mao motif kente', 250000, null, ['M', 'L', 'XL'], ['Kente or', 'Kente vert'], 'Coupe droite, manches longues, coton respirant.', false, false, 12],
        ];

        foreach ($items as $i => [$c, $g, $name, $price, $compare, $sizes, $colors, $desc, $new, $featured, $stock]) {
            Product::updateOrCreate(['name' => $name], [
                'category_id' => $cat($c), 'gender' => $g, 'description' => $desc,
                'price' => $price, 'compare_price' => $compare, 'sizes' => $sizes, 'colors' => $colors,
                'is_new' => $new, 'is_featured' => $featured, 'is_active' => true, 'stock' => $stock,
                'sku' => 'DEMO-'.str_pad($i + 1, 3, '0', STR_PAD_LEFT),
            ]);
        }
    }
}
