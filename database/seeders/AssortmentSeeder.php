<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Faker\Factory as Faker;

class AssortmentSeeder extends Seeder
{

    public function run(): void
    {   
        $faker = Faker::create();
        $types = ['pizza', 'drink'];
        

        for ($i = 0; $i < 40; $i++) {
            DB::table('assortments')->insert([
                'name' => $faker->word,
                'price' => $faker->numberBetween(400, 1000),
                'type' => $types[array_rand($types)],
                'description' => $faker->sentence,
                'image_url' => $faker->imageUrl(640, 480, 'products', true),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
