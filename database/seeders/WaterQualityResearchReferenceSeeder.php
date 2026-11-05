<?php

namespace Database\Seeders;

use App\Models\WaterQualityResearchReference;
use Illuminate\Database\Seeder;

class WaterQualityResearchReferenceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $references = config('water_quality.initial_research_references', []);

        foreach ($references as $reference) {
            WaterQualityResearchReference::updateOrCreate(
                ['source_id' => $reference['source_id']],
                $reference
            );
        }
    }
}
