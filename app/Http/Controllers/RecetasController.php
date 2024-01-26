<?php

namespace App\Http\Controllers;

use App\Models\Recetas;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use App\Models\CategoriasRecetas;
use Illuminate\Support\Facades\Validator;


class RecetasController extends Controller
{

    public function index(Request $request)
    {

        $categorias = $request->user()->categoriasRecetas()->select('categorias_recetas.id', 'categorias_recetas.nombre')->get();

        return response()->json($categorias, 200);
    }

    public function recetasEnCategoria(Request $request, $categoria_id)
    {
        if (!$categoria_id) {
            return response()->json(['error' => 'El parámetro categoria_id es obligatorio.'], 400);
        }

        $recetas = $request->user()->recetas()->where('categoria_id', $categoria_id)->get();

        return response()->json($recetas, 200);
    }

    public function create(Request $request)
    {
        $usuario = $request->user();
        //crear una nueva receta
        $validator = Validator::make($request->all(), [
            'titulo' => 'required|string',
            'ingredientes' => 'required|string',
            'tiempo_preparacion' => 'required|numeric',
            'descripcion' => 'required|string',
            'rutaImagenPrincipal' => 'nullable|string',
            'categoria_id' => 'required|exists:categorias_recetas,id',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors());
        }

        Recetas::create([
            'titulo' => $request->titulo,
            'ingredientes' => $request->ingredientes,
            'tiempo_preparacion' => $request->tiempo_preparacion,
            'descripcion' => $request->descripcion,
            'user_id' => $usuario->id,
            'categoria_id' => $request->categoria_id,
        ]);
        return response()->json([
            'status' => "ok",

        ]);
    }

    public function update(Recetas $recetas, Request $request)
    {

        if ($request->user()->id !== $recetas->user_id) {
            return response()->json([
                'message' => 'No va'
            ], 403);
        }
        $validator = Validator::make($request->all(), [
            'titulo' => 'string',
            'ingredientes' => 'string',
            'tiempo_preparacion' => 'numeric',
            'descripcion' => 'string',
            'rutaImagenPrincipal' => 'string',
            'categoria_id' => 'exists:categorias_recetas,id',
        ]);


        if ($validator->fails()) {
            return response()->json($validator->errors());
        }

        $recetas->update($request->all());

        return response()->json([
            'status' => 'ok',
            'message' => 'Receta actualizada exitosamente'
        ]);
    }

    public function destroy(Recetas $recetas, Request $request)
    {
        return response()->json(['user_id' => $recetas], 200);
        if ($request->user()->id !== $recetas->user_id) {
            return response()->json([
                'message' => 'No tienes permiso para eliminar esta receta'
            ], 403);
        }

        $recetas->delete();

        return response()->json([
            'message' => 'Se eliminó la receta correctamente'
        ], 200);
    }
}
