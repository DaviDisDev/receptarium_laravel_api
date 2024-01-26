<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;


class RecetasSedeer extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        DB::table('recetas')->insert([
            'titulo' => 'Pollo al horno con verduras',
            'ingredientes' => "Pollito de corona, cebolla",
            'preparacion' => "Ralladar la ceballa y picar el polllo",
            'tiempo_preparacion' => 20,
            'user_id' => 1
        ]);
    }
}
