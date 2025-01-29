<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Faker\Factory as Faker;
use Illuminate\Support\Facades\DB;

class ProductSeed extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create();

        for ($i = 0; $i < 100; $i++) {
            DB::table('products')->insert([
                'name' => "Product $i",
                'description' => "Description of product $i",
                'price' => $faker->randomFloat(2, 1, 100), // Preço entre 1.00 e 100.00
                'stock' => $faker->numberBetween(1, 100), // Estoque entre 1 e 100
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
