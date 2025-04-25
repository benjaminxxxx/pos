<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;
    use HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'uuid',
        'name',
        'email',
        'password',
        'google_account_id'
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
        ];
    }

    /**
     * Get the user's initials
     */
    public function initials(): string
    {
        return Str::of($this->name)
            ->explode(' ')
            ->map(fn(string $name) => Str::of($name)->substr(0, 1))
            ->implode('');
    }
    protected static function boot()
    {
        parent::boot();

        // Generar un UUID automáticamente al crear un usuario
        static::creating(function ($user) {
            $user->uuid = Str::uuid();
        });
    }

    public function getMetodoLogueoAttribute()
    {
        if ($this->google_account_id) {
            return 'Google';
        }
        return 'Email + Contraseña';
    }

    public function negocios()
    {
        return $this->hasMany(Negocio::class, 'user_id', 'id');
    }
    public function sucursales()
    {
        return $this->hasManyThrough(
            Sucursal::class,      // Modelo destino
            Negocio::class,       // Modelo intermedio
            'user_id',                        // Clave foránea en Negocio que apunta a User
            'negocio_id',                     // Clave foránea en Sucursal que apunta a Negocio
            'id',                             // Clave local en User
            'id'                              // Clave local en Negocio
        )->orderBy('negocio_id');
    }
}
