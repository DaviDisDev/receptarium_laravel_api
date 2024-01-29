<?php

use App\Http\Controllers\RecetasController;
use App\Http\Controllers\AuthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('registerOrLogin', [AuthController::class, 'registerOrLogin']);

Route::middleware(['auth:sanctum'])->group(function () {

    Route::post('logout', [AuthController::class, 'logout']);
    Route::get('recetas', [RecetasController::class, 'index']);

    Route::get('ver-receta/{receta}', [RecetasController::class, 'showReceta']);

    Route::get('ver-pasos/{receta}', [RecetasController::class, 'ShowPasos']);

    Route::post('crear-receta', [RecetasController::class, 'create']);

    Route::put('/editar-paso/{paso}', [RecetasController::class, 'editarPaso']);

    Route::post('crear-pasos/{receta}', [RecetasController::class, 'crearPasos']);


    Route::delete('/recetas/{recetas}', [RecetasController::class, 'destroy']);

    Route::put('/recetas/{recetas}', [RecetasController::class, 'update']);

    Route::get('/recetas-categoria/{categoria_id}', [RecetasController::class, 'recetasEnCategoria']);
});
