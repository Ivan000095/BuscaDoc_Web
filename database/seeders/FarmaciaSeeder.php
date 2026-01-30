<?php

namespace Database\Seeders;

use App\Models\Farmacia;
use Illuminate\Database\Seeder;

class FarmaciaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Farmacia::factory()->count(5)->create();
    }
}
