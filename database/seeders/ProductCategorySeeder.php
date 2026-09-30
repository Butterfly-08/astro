<?php

namespace Database\Seeders;

use App\Models\ProductCategory;
use Illuminate\Database\Seeder;

class ProductCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            [
                'name' => 'Certified Gemstones',
                'slug' => 'gemstones',
                'description' => '100% natural, untreated, lab-certified planetary gemstones (Navratna) energised for specific astrological remedies.',
                'icon' => 'bi bi-gem',
                'sort_order' => 1,
                'is_featured' => true,
                'status' => 'active',
            ],
            [
                'name' => 'Sacred Rudraksha',
                'slug' => 'rudraksha',
                'description' => 'Authentic Himalayan Nepali & Indonesian Rudraksha beads (1 to 14 Mukhi) with genuine lab test reports.',
                'icon' => 'bi bi-circle-fill',
                'sort_order' => 2,
                'is_featured' => true,
                'status' => 'active',
            ],
            [
                'name' => 'Sanctified Yantras',
                'slug' => 'yantras',
                'description' => 'Vedic sacred geometry copper & brass Yantras consecrated via Prana Pratishtha by senior priests.',
                'icon' => 'bi bi-triangle-fill',
                'sort_order' => 3,
                'is_featured' => true,
                'status' => 'active',
            ],
            [
                'name' => 'Puja & Ritual Essentials',
                'slug' => 'puja-essentials',
                'description' => 'Pure havan samagri, natural organic dhoop, brass diyas, and authentic Vedic ritual accessories.',
                'icon' => 'bi bi-fire',
                'sort_order' => 4,
                'is_featured' => true,
                'status' => 'active',
            ],
            [
                'name' => 'Kundli Reports & Astrological Books',
                'slug' => 'reports-texts',
                'description' => 'In-depth 50+ page personalized birth chart reports, Sade Sati analysis, and authentic Vedic astrology classics.',
                'icon' => 'bi bi-journal-text',
                'sort_order' => 5,
                'is_featured' => true,
                'status' => 'active',
            ],
            [
                'name' => 'Vastu & Spiritual Decor',
                'slug' => 'vastu-decor',
                'description' => 'Vastu harmonizing pyramids, authentic conches (Shankh), brass deity idols, and positive energy charms.',
                'icon' => 'bi bi-house-heart-fill',
                'sort_order' => 6,
                'is_featured' => false,
                'status' => 'active',
            ],
            [
                'name' => 'Healing Crystals & Japa Malas',
                'slug' => 'crystals-malas',
                'description' => '108-bead Japa Malas made of Sphatik, Tulsi, and sandalwood, along with chakra cleansing raw crystal points.',
                'icon' => 'bi bi-stars',
                'sort_order' => 7,
                'is_featured' => true,
                'status' => 'active',
            ],
        ];

        foreach ($categories as $cat) {
            ProductCategory::updateOrCreate(['slug' => $cat['slug']], $cat);
        }
    }
}
