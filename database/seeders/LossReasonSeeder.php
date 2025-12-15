<?php

namespace Database\Seeders;

use App\Models\LossReason;
use Illuminate\Database\Seeder;

class LossReasonSeeder extends Seeder
{
    public function run(): void
    {
        $reasons = LossReason::getDefaultReasons();
        
        foreach ($reasons as $reason) {
            LossReason::create($reason);
        }
    }
}