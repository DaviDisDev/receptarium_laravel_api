<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\HasMany; // Importa la clase HasMany
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    public function recetas(): HasMany
    {
        return $this->hasMany(Recetas::class);
    }

    public function categoriasRecetas(): HasManyThrough
    {
        return $this->hasManyThrough(
            CategoriasRecetas::class,
            Recetas::class,
            'user_id', // Clave foránea en la tabla recetas
            'id',       // Clave primaria en la tabla categorias_recetas
            'id',       // Clave primaria local en la tabla users
            'categoria_id' // Clave foránea en la tabla categorias_recetas
        );
    }
}
