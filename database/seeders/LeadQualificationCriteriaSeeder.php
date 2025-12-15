<?php

namespace Database\Seeders;

use App\Models\LeadQualificationCriteria;
use Illuminate\Database\Seeder;

class LeadQualificationCriteriaSeeder extends Seeder
{
    public function run(): void
    {
        $criteria = LeadQualificationCriteria::getDefaultCriteria();
        
        foreach ($criteria as $criterion) {
            LeadQualificationCriteria::create($criterion);
        }
    }
}