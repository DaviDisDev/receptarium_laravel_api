<?php

namespace App\Http\Controllers;

use App\Models\Recetas;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use App\Models\CategoriasRecetas;
use Illuminate\Support\Facades\Validator;
use App\Models\PasosReceta;

class RecetasController extends Controller
{

    public function index(Request $request)
    {

        $categorias = $request->user()->categoriasRecetas()->select('categorias_recetas.id', 'categorias_recetas.nombre')->get();

        return response()->json($categorias, 200);
    }

    public function showReceta(Request $request, $idReceta)
    {
        if (!$idReceta) {
            return response()->json(['error' => 'El parámetro idReceta es obligatorio.'], 400);
        }

        // Obtenemos la receta por su ID
        $receta = Recetas::find($idReceta);

        // Verificamos si la receta existe
        if (!$receta) {
            return response()->json(['error' => 'Receta no encontrada.'], 404);
        }

        // Verificamos si el usuario tiene permisos para ver la receta
        if ($request->user()->id !== $receta->user_id) {
            return response()->json([
                'message' => 'No tiene permiso para ver esta receta'
            ], 403);
        }

        $pasosReceta = $receta->pasos_receta;
        $nombreCategoria = $receta->categoria->nombre;
        // Devolvemos la receta junto con los pasos, o una lista vacía si no hay pasos
        return response()->json([
            'receta' => $receta,
            'pasos' => $pasosReceta ?? [],
        ], 200);
    }

    public function crearPasos(Request $request, $idReceta)
    {
        $validator = Validator::make($request->all(), [
            'titulo' => 'required|string',
            'descripcion' => 'required|string',
            'ruta_imagen' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 400);
        }

        // Crear un nuevo paso de receta
        PasosReceta::create([
            'titulo' => $request->titulo,
            'descripcion' => $request->descripcion,
            'ruta_imagen' => $request->ruta_imagen,
            'receta_id' => $idReceta, // Asociar el paso a la receta
        ]);

        return response()->json(['message' => 'Paso de receta creado exitosamente'], 200);
    }

    public function ShowPasos(Request $request, $idReceta)
    {


        // Buscar los pasos asociados a la receta por su ID
        $pasos = PasosReceta::where('receta_id', $idReceta)->get();

        // Verificar si hay pasos asociados a la receta
        if ($pasos->isEmpty()) {
            return response()->json(['error' => 'No se encontraron pasos asociados a la receta.'], 404);
        }

        return response()->json(['pasos' => $pasos], 200);
    }

    public function editarPaso(Request $request, $idPaso)
    {
        // Buscar el paso por su ID
        $paso = PasosReceta::find($idPaso);

        // Verificar si el paso existe
        if (!$paso) {
            return response()->json(['error' => 'El paso no fue encontrado.'], 404);
        }

        // Definir reglas de validación
        $rules = [
            'titulo' => 'string',
            'descripcion' => 'string',
            'ruta_imagen' => 'nullable|string',
        ];

        // Si se proporciona un campo, validar solo ese campo
        if ($request->has('titulo')) {
            $rules['titulo'] = 'required|string';
        }
        if ($request->has('descripcion')) {
            $rules['descripcion'] = 'required|string';
        }

        // Crear el validador con las reglas
        $validator = Validator::make($request->all(), $rules);

        // Comprobar si la validación falla
        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 400);
        }

        // Actualizar los campos del paso con los nuevos valores proporcionados
        if ($request->has('titulo')) {
            $paso->titulo = $request->titulo;
        }
        if ($request->has('descripcion')) {
            $paso->descripcion = $request->descripcion;
        }
        if ($request->has('ruta_imagen')) {
            $paso->ruta_imagen = $request->ruta_imagen;
        }
        $paso->save();

        // Retornar una respuesta JSON indicando que el paso fue editado exitosamente
        return response()->json(['message' => 'Paso editado exitosamente.'], 200);
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
            'num_personas' => 'required|numeric',
            'tiempo_preparacion' => 'required|string',
            'descripcion' => 'required|string',
            'rutaImagenPrincipal' => 'nullable|string',
            'categoria_id' => 'required|exists:categorias_recetas,id',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors());
        }

        $nuevaReceta = Recetas::create([
            'titulo' => $request->titulo,
            'ingredientes' => $request->ingredientes,
            'tiempo_preparacion' => $request->tiempo_preparacion,
            'num_personas' => $request->num_personas,
            'descripcion' => $request->descripcion,
            'user_id' => $usuario->id,
            'categoria_id' => $request->categoria_id,
        ]);
        return response()->json([
            'status' => "ok",
            'id_receta' => $nuevaReceta->id,
        ]);
    }

    public function update(Recetas $recetas, Request $request)
    {

        if ($request->user()->id !== $recetas->user_id) {
            return response()->json([
                'message' => 'No tiene permiso para modificar esta receta'
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
