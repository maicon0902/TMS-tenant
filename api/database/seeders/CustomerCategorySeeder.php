<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\CustomerCategory;

class CustomerCategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = ['Gold', 'Silver', 'Bronze'];
        
        foreach ($categories as $category) {
            CustomerCategory::firstOrCreate(
                ['name' => $category],
                ['name' => $category]
            );
        }
    }
}

