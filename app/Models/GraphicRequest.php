<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

/** Demande de prestation infographie / design graphique */
class GraphicRequest extends Model
{
    public const SERVICES = [
        'logo' => 'Logo & identité visuelle',
        'print' => 'Flyers, affiches & brochures',
        'cartes' => 'Cartes de visite & papeterie',
        'baches' => 'Bâches, kakémonos & enseignes',
        'reseaux' => 'Visuels pour réseaux sociaux',
        'textile' => 'Motifs & visuels pour textile',
        'autre' => 'Autre projet graphique',
    ];

    public const STATUSES = [
        'nouvelle' => 'Nouvelle',
        'devis' => 'Devis envoyé',
        'en_cours' => 'En création',
        'validation' => 'En validation',
        'livree' => 'Livrée',
        'annulee' => 'Annulée',
    ];

    protected $fillable = [
        'user_id', 'reference', 'customer_name', 'customer_phone', 'customer_email', 'company', 'service', 'description',
        'budget', 'deadline', 'attachment_path', 'status', 'quote_amount', 'admin_note',
    ];

    protected function casts(): array
    {
        return ['deadline' => 'date'];
    }

    public function getRouteKeyName(): string
    {
        return 'reference';
    }

    public function serviceLabel(): string
    {
        return self::SERVICES[$this->service] ?? $this->service;
    }

    public function statusLabel(): string
    {
        return self::STATUSES[$this->status] ?? $this->status;
    }

    public function attachmentUrl(): ?string
    {
        return $this->attachment_path ? Storage::disk('public')->url($this->attachment_path) : null;
    }
}
