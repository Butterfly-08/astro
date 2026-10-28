<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\ProductCategory;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Ensure Categories exist in Database
        $categories = [
            'gemstones'       => ['name' => 'Gemstones', 'slug' => 'gemstones'],
            'rudraksha'       => ['name' => 'Rudraksha & Malas', 'slug' => 'rudraksha'],
            'yantras'         => ['name' => 'Yantras & Crystals', 'slug' => 'yantras'],
            'puja-essentials' => ['name' => 'Puja Essentials', 'slug' => 'puja-essentials'],
            'reports-texts'   => ['name' => 'Reports & Texts', 'slug' => 'reports-texts'],
            'idols'           => ['name' => 'Idols & Statues', 'slug' => 'idols'],
            'homam-kits'      => ['name' => 'Homam & Kits', 'slug' => 'homam-kits'],
        ];

        $categoryMap = [];
        foreach ($categories as $key => $catData) {
            $cat = ProductCategory::updateOrCreate(
                ['slug' => $catData['slug']],
                ['name' => $catData['name']]
            );
            $categoryMap[$key] = $cat->id;
        }

        // 2. Product items mapping with correct local images
        $products = [
            // Idols & Statues
            [
                'category_slug' => 'idols',
                'name' => 'Divine Copper Amman Idol',
                'slug' => 'divine-copper-amman-idol',
                'sku' => 'IDL-AMM-01',
                'short_description' => 'Handcrafted premium quality copper Amman Idol for home temple and positivity.',
                'description' => "Handcrafted divine Amman idol crafted from high quality copper. Brings immense positivity, energy and peace to your pooja room.",
                'price' => 1499.00,
                'sale_price' => 999.00,
                'stock' => 15,
                'image' => 'products/Amman Idol.jpg',
                'status' => 'active',
                'is_featured' => true,
                'rating_avg' => 4.9,
                'total_reviews' => 28,
            ],
            [
                'category_slug' => 'idols',
                'name' => 'Lord Krishna Metal Idol',
                'slug' => 'lord-krishna-metal-idol',
                'sku' => 'IDL-KRI-01',
                'short_description' => 'Beautiful brass/metal idol of Lord Krishna with flute.',
                'description' => "Intricately detailed metal idol of Lord Krishna playing flute. Ideal for altar placement and spiritual ambiance.",
                'price' => 1899.00,
                'sale_price' => 1399.00,
                'stock' => 10,
                'image' => 'products/Krishna Idol.jpg',
                'status' => 'active',
                'is_featured' => true,
                'rating_avg' => 4.8,
                'total_reviews' => 19,
            ],
            [
                'category_slug' => 'idols',
                'name' => 'Goddess Lakshmi Idol',
                'slug' => 'goddess-lakshmi-idol',
                'sku' => 'IDL-LAK-01',
                'short_description' => 'Graceful brass Goddess Lakshmi idol for wealth and abundance.',
                'description' => "Pure brass statue of Goddess Lakshmi. Brings financial prosperity, luck and wisdom to household and business.",
                'price' => 1699.00,
                'sale_price' => 1299.00,
                'stock' => 12,
                'image' => 'products/Lakshmi Idol.jpg',
                'status' => 'active',
                'is_featured' => true,
                'rating_avg' => 4.9,
                'total_reviews' => 35,
            ],
            [
                'category_slug' => 'idols',
                'name' => 'Lord Murugan Idol with Vel',
                'slug' => 'lord-murugan-idol-with-vel',
                'sku' => 'IDL-MUR-01',
                'short_description' => 'Exquisite divine Lord Murugan statue for courage and victory.',
                'description' => "Consecrated Lord Murugan idol crafted in heavy brass, complete with Vel for protection against obstacles.",
                'price' => 1799.00,
                'sale_price' => 1399.00,
                'stock' => 11,
                'image' => 'products/Murugan Idol.jpg',
                'status' => 'active',
                'is_featured' => false,
                'rating_avg' => 4.9,
                'total_reviews' => 42,
            ],
            [
                'category_slug' => 'idols',
                'name' => 'Auspicious Vinayagar Brass Idol',
                'slug' => 'auspicious-vinayagar-brass-idol',
                'sku' => 'IDL-VIN-01',
                'short_description' => 'Auspicious Ganesha idol to overcome all life obstacles.',
                'description' => "Heavy brass Lord Ganesha idol. Ideal for starting new ventures and daily pooja routines.",
                'price' => 1499.00,
                'sale_price' => 1099.00,
                'stock' => 15,
                'image' => 'products/Vinayagar Idol.jpg',
                'status' => 'active',
                'is_featured' => true,
                'rating_avg' => 5.0,
                'total_reviews' => 50,
            ],

            // Gemstones
            [
                'category_slug' => 'gemstones',
                'name' => 'Natural Certified Yellow Sapphire (Pukhraj)',
                'slug' => 'natural-certified-yellow-sapphire-pukhraj',
                'sku' => 'GEM-PUK-01',
                'short_description' => '100% natural, unheated Ceylon yellow sapphire for Brihaspati (Jupiter) strength.',
                'description' => "Natural certified Ceylon Yellow Sapphire (Pukhraj). Brings wisdom, financial prosperity, marital bliss, and professional success.",
                'price' => 24500.00,
                'sale_price' => 21999.00,
                'stock' => 5,
                'image' => 'products/Natural Yellow Sapphire.jpg',
                'status' => 'active',
                'is_featured' => true,
                'rating_avg' => 4.9,
                'total_reviews' => 48,
            ],
            [
                'category_slug' => 'gemstones',
                'name' => 'Natural Emerald Gemstone (Panna)',
                'slug' => 'natural-emerald-gemstone-panna',
                'sku' => 'GEM-PAN-01',
                'short_description' => 'Brilliant deep green natural emerald for Budh (Mercury) intellect and business.',
                'description' => "Vibrant emerald green color stone. Sharpens mathematical faculties, communication skills, and trading intuition.",
                'price' => 18000.00,
                'sale_price' => 15500.00,
                'stock' => 7,
                'image' => 'products/Natural Emerald Gemstone.jpg',
                'status' => 'active',
                'is_featured' => true,
                'rating_avg' => 4.9,
                'total_reviews' => 51,
            ],
            [
                'category_slug' => 'gemstones',
                'name' => 'Natural Certified Ruby Gemstone',
                'slug' => 'natural-certified-ruby-gemstone',
                'sku' => 'GEM-RUB-01',
                'short_description' => 'Pure natural ruby gemstone for Surya (Sun) strength and vitality.',
                'description' => "Authentic certified ruby gemstone. Boosts leadership, vital energy, and career recognition.",
                'price' => 3999.00,
                'sale_price' => 2999.00,
                'stock' => 5,
                'image' => 'products/Natural Ruby Gemstone.jpg',
                'status' => 'active',
                'is_featured' => false,
                'rating_avg' => 4.7,
                'total_reviews' => 30,
            ],

            // Rudraksha & Malas
            [
                'category_slug' => 'rudraksha',
                'name' => '5 Mukhi Nepali Rudraksha Mala (108+1 Beads)',
                'slug' => '5-mukhi-nepali-rudraksha-mala',
                'sku' => 'RUD-5MK-01',
                'short_description' => 'Authentic hand-knotted 108+1 Nepali Rudraksha garland for meditation.',
                'description' => "Premium quality 108+1 five-mukhi Nepali Rudraksha beads. Enhances mental peace, regulates anxiety, and awakens intuitive awareness.",
                'price' => 2499.00,
                'sale_price' => 1899.00,
                'stock' => 40,
                'image' => 'products/Five Mukhi Rudraksha.jpg',
                'status' => 'active',
                'is_featured' => true,
                'rating_avg' => 4.9,
                'total_reviews' => 142,
            ],
            [
                'category_slug' => 'rudraksha',
                'name' => 'Devotional Prayer Mala',
                'slug' => 'devotional-prayer-mala',
                'sku' => 'RUD-MAL-02',
                'short_description' => 'Sacred prayer beads for chanting mantras and daily meditation.',
                'description' => "High quality sacred prayer mala crafted for mantra japa and inner tranquility.",
                'price' => 499.00,
                'sale_price' => 349.00,
                'stock' => 30,
                'image' => 'products/Devotional Prayer Mala.jpg',
                'status' => 'active',
                'is_featured' => false,
                'rating_avg' => 4.6,
                'total_reviews' => 25,
            ],

            // Yantras & Crystals
            [
                'category_slug' => 'yantras',
                'name' => 'Energized Pure Copper Kuber Yantra',
                'slug' => 'energized-pure-copper-kuber-yantra',
                'sku' => 'YAN-KUB-01',
                'short_description' => 'Energized copper plate inscribed with Lord Kuber sacred geometric grid.',
                'description' => "Heavy pure copper yantra sheet dedicated to Lord Kubera. Eliminates unwarranted expenditure and safeguards financial growth.",
                'price' => 1450.00,
                'sale_price' => 1199.00,
                'stock' => 35,
                'image' => 'products/Kubera Yantra.jpg',
                'status' => 'active',
                'is_featured' => true,
                'rating_avg' => 4.7,
                'total_reviews' => 84,
            ],
            [
                'category_slug' => 'yantras',
                'name' => 'Natural Clear Quartz Crystal',
                'slug' => 'natural-clear-quartz-crystal',
                'sku' => 'CRY-CLR-01',
                'short_description' => 'Natural healing quartz crystal for mental clarity and positive energy.',
                'description' => "Pure healing crystal stone. Clears negative energy, balances chakras, and enhances spiritual aura.",
                'price' => 899.00,
                'sale_price' => 649.00,
                'stock' => 12,
                'image' => 'products/Clear Quartz Crystal.jpg',
                'status' => 'active',
                'is_featured' => false,
                'rating_avg' => 4.8,
                'total_reviews' => 31,
            ],
            [
                'category_slug' => 'yantras',
                'name' => 'Natural Amethyst Healing Crystal',
                'slug' => 'natural-amethyst-healing-crystal',
                'sku' => 'CRY-AME-01',
                'short_description' => 'Natural purple amethyst crystal for stress relief and peace.',
                'description' => "Raw purple amethyst stone known for immense calming properties, meditative focus, and aura purification.",
                'price' => 1199.00,
                'sale_price' => 899.00,
                'stock' => 15,
                'image' => 'products/Natural Amethyst Crystal.jpg',
                'status' => 'active',
                'is_featured' => false,
                'rating_avg' => 4.9,
                'total_reviews' => 40,
            ],

            // Puja Essentials
            [
                'category_slug' => 'puja-essentials',
                'name' => 'Traditional Heavy Brass Deepam',
                'slug' => 'traditional-heavy-brass-deepam',
                'sku' => 'PUJ-DEP-01',
                'short_description' => 'Traditional heavy brass oil lamp for daily temple lighting.',
                'description' => "High grade solid brass lamp engineered for continuous daily lighting during morning and evening prayers.",
                'price' => 699.00,
                'sale_price' => 499.00,
                'stock' => 25,
                'image' => 'products/Brass Deepam.jpg',
                'status' => 'active',
                'is_featured' => false,
                'rating_avg' => 4.8,
                'total_reviews' => 52,
            ],
            [
                'category_slug' => 'puja-essentials',
                'name' => 'Organic Pure Camphor Tablets',
                'slug' => 'organic-pure-camphor-tablets',
                'sku' => 'PUJ-CMP-01',
                'short_description' => 'Pure organic camphor tablets for daily aarti.',
                'description' => "100% pure bhimseni camphor tablets for aarti and clearing airborne impurities.",
                'price' => 299.00,
                'sale_price' => 199.00,
                'stock' => 50,
                'image' => 'products/Camphor Tablets.jpg',
                'status' => 'active',
                'is_featured' => false,
                'rating_avg' => 4.9,
                'total_reviews' => 95,
            ],
            [
                'category_slug' => 'puja-essentials',
                'name' => 'Resonant Brass Pooja Bell',
                'slug' => 'resonant-brass-pooja-bell',
                'sku' => 'PUJ-BEL-01',
                'short_description' => 'Heavy brass hand bell with pure acoustic sound vibration.',
                'description' => "Hand-cast brass pooja bell producing clear resonant sound vibrations that purify the surroundings during pooja.",
                'price' => 549.00,
                'sale_price' => 399.00,
                'stock' => 20,
                'image' => 'products/Pooja Bell.jpg',
                'status' => 'active',
                'is_featured' => false,
                'rating_avg' => 4.7,
                'total_reviews' => 18,
            ],

            // Homam Kits
            [
                'category_slug' => 'homam-kits',
                'name' => 'Complete Ayushya Homam Kit',
                'slug' => 'complete-ayushya-homam-kit',
                'sku' => 'HOM-AYU-01',
                'short_description' => 'Complete set of pure herbs and samagri for Ayushya homam.',
                'description' => "Curated organic samagri kit containing 30+ rare herbs required for performing Ayushya Homam at home.",
                'price' => 1999.00,
                'sale_price' => 1499.00,
                'stock' => 10,
                'image' => 'products/Ayushya Homam Kit.jpg',
                'status' => 'active',
                'is_featured' => true,
                'rating_avg' => 4.9,
                'total_reviews' => 44,
            ],
            [
                'category_slug' => 'homam-kits',
                'name' => 'Complete Pooja Combo Kit',
                'slug' => 'complete-pooja-combo-kit',
                'sku' => 'HOM-CMB-01',
                'short_description' => 'All-in-one sacred essentials kit for festivals and prayers.',
                'description' => "Comprehensive pooja kit containing kumkum, sandalwood, wicks, dhoop, ghee diyas, and essential sacred items.",
                'price' => 1299.00,
                'sale_price' => 999.00,
                'stock' => 20,
                'image' => 'products/Complete Pooja Combo Kit.jpg',
                'status' => 'active',
                'is_featured' => true,
                'rating_avg' => 4.8,
                'total_reviews' => 60,
            ],

            // Reports & Texts
            [
                'category_slug' => 'reports-texts',
                'name' => 'Personalized 55-Page Deluxe Janam Kundli & Dasha Report',
                'slug' => 'personalized-55-page-deluxe-janam-kundli-report',
                'sku' => 'REP-KUN-01',
                'short_description' => 'Comprehensive lifetime astrology forecast with divisional charts & custom remedies.',
                'description' => "Exhaustive 55+ page personalized horoscope prepared based on exact birth details. Includes Vimshottari Mahadasha timeline, Sade Sati analysis, and remedies.",
                'price' => 1299.00,
                'sale_price' => 999.00,
                'stock' => 100,
                'image' => 'products/Daily Devotional Combo.jpg',
                'status' => 'active',
                'is_featured' => true,
                'rating_avg' => 4.9,
                'total_reviews' => 210,
            ],
        ];

        foreach ($products as $pData) {
            $catSlug = $pData['category_slug'];
            unset($pData['category_slug']);

            // Safely assign mapped category_id or fallback to first category
            $pData['category_id'] = $categoryMap[$catSlug] ?? reset($categoryMap);

            Product::updateOrCreate(['sku' => $pData['sku']], $pData);
        }
    }
}