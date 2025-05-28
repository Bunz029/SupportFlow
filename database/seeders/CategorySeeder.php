<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            [
                'name' => 'Getting Started',
                'description' => 'Basic guides and introductory materials',
            ],
            [
                'name' => 'Account & Security',
                'description' => 'Account management and security related topics',
            ],
            [
                'name' => 'Technical Support',
                'description' => 'Technical issues and troubleshooting guides',
            ],
            [
                'name' => 'Features & How-to',
                'description' => 'Detailed feature guides and tutorials',
            ],
            [
                'name' => 'Billing & Subscription',
                'description' => 'Billing, payments, and subscription related topics',
            ],
            [
                'name' => 'FAQs',
                'description' => 'Frequently asked questions and answers',
            ],
            [
                'name' => 'Updates & News',
                'description' => 'Product updates, new features, and announcements',
            ],
            [
                'name' => 'Best Practices',
                'description' => 'Tips, recommendations, and best practices',
            ],
        ];

        foreach ($categories as $category) {
            Category::create($category);
        }
    }
} 