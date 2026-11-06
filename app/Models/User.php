<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'is_admin',
        'role',
        'last_login_at',
        'phone',
        'city',
        'district',
        'address',
    ];

    public const ROLES = [
        'customer' => 'Client',
        'admin' => 'Administrateur (boutique)',
        'developer' => 'Développeur (accès complet)',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_admin' => 'boolean',
            'last_login_at' => 'datetime',
        ];
    }

    public function isDeveloper(): bool
    {
        return $this->is_admin && $this->role === 'developer';
    }

    public function roleLabel(): string
    {
        return self::ROLES[$this->role] ?? $this->role;
    }

    public function orders()
    {
        return $this->hasMany(Order::class)->latest();
    }

    public function designRequests()
    {
        return $this->hasMany(DesignRequest::class)->latest();
    }

    public function graphicRequests()
    {
        return $this->hasMany(GraphicRequest::class)->latest();
    }
}
