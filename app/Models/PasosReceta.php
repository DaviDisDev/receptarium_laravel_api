<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PasosReceta extends Model
{
    protected $table = 'pasos_recetas'; // Nombre de la tabla

    protected $fillable = [
        'titulo', 'descripcion', 'ruta_imagen', 'receta_id'
    ];

    // Aquí podrías definir relaciones o métodos adicionales si es necesario
}
