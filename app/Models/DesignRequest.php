<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class DesignRequest extends Model
{
    public const STATUSES = [
        'nouvelle' => 'Nouvelle',
        'devis' => 'Devis envoyé',
        'acceptee' => 'Acceptée',
        'couture' => 'En couture',
        'prete' => 'Prête',
        'livree' => 'Livrée',
        'annulee' => 'Annulée',
    ];

    public const MEASUREMENTS = [
        'poitrine' => 'Tour de poitrine',
        'taille' => 'Tour de taille',
        'hanches' => 'Tour de hanches',
        'epaules' => 'Largeur d\'épaules',
        'longueur' => 'Longueur du vêtement',
        'manche' => 'Longueur de manche',
        'cou' => 'Tour de cou',
        'entrejambe' => 'Entrejambe',
    ];

    protected $fillable = [
        'user_id', 'reference', 'customer_name', 'customer_phone', 'customer_email', 'gender', 'garment_type', 'occasion',
        'fabric', 'description', 'measurements', 'budget', 'deadline', 'inspiration_path', 'status',
        'quote_amount', 'admin_note',
    ];

    protected function casts(): array
    {
        return ['measurements' => 'array', 'deadline' => 'date'];
    }

    public function getRouteKeyName(): string
    {
        return 'reference';
    }

    public function statusLabel(): string
    {
        return self::STATUSES[$this->status] ?? $this->status;
    }

    public function inspirationUrl(): ?string
    {
        return $this->inspiration_path ? Storage::disk('public')->url($this->inspiration_path) : null;
    }
}
