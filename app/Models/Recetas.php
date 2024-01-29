<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\CategoriasRecetas;

class Recetas extends Model
{
    use HasFactory;
    protected $fillable = [
        'titulo',
        'ingredientes',
        'tiempo_preparacion',
        'num_personas',
        'descripcion',
        'rutaImagenPrincipal',
        'categoria_id',
        'user_id', // Incluyendo user_id en la lista de campos permitidos para asignación en masa
        // Otros campos permitidos aquí si los hay
    ];
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function categoria()
    {
        return $this->belongsTo(CategoriasRecetas::class, 'categoria_id');
    }
}
