<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\Team;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $team = Team::first();

        if ($team && $team->products()->count() === 0) {
            Product::factory(15)->create([
                'team_id' => $team->id,
            ]);
        }
    }
}
