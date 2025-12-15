<?php

namespace Database\Seeders;

use App\Models\DealStage;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DealStageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Usamos updateOrCreate para que, si ya existen, no se dupliquen.
        DealStage::updateOrCreate(
            ['name' => 'Contacto Inicial'],
            ['order' => 1]
        );
        DealStage::updateOrCreate(
            ['name' => 'Propuesta Enviada'],
            ['order' => 2]
        );
        DealStage::updateOrCreate(
            ['name' => 'Negociación'],
            ['order' => 3]
        );
    }
}