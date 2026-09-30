<?php

namespace Database\Seeders;

use App\Models\Astrologer;
use App\Models\AstrologerAvailability;
use App\Models\Service;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class AstrologerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $astrologers = [
            [
                'display_name'     => 'Pandit Rajesh Sharma',
                'email'            => 'rajesh.sharma@astrovani.test',
                'phone'            => '+91 9811234567',
                'short_bio'        => 'Renowned Vedic astrologer with 20+ years of experience in Kundli analysis and Vastu consultations.',
                'bio'              => 'Pandit Rajesh Sharma has been practicing Vedic Astrology for over two decades. He holds a Jyotish Acharya degree from Banaras Hindu University and has guided thousands of families through life\'s pivotal moments. His specialties include marriage compatibility, career guidance, and remedial astrology.',
                'specializations'  => 'Vedic Astrology, Vastu Shastra, Kundli Matching',
                'languages'        => 'Hindi, English, Sanskrit',
                'experience_years' => 22,
                'education'        => 'Jyotish Acharya, BHU Varanasi',
                'chat_rate'        => 25.00,
                'call_rate'        => 30.00,
                'video_rate'       => 40.00,
                'rating_avg'       => 4.85,
                'total_reviews'    => 1842,
                'total_consultations' => 12500,
                'status'           => 'active',
                'is_featured'      => true,
                'is_available'     => true,
                'services'         => ['Vedic Astrology', 'Vastu Shastra'],
            ],
            [
                'display_name'     => 'Dr. Priya Malhotra',
                'email'            => 'priya.malhotra@astrovani.test',
                'phone'            => '+91 9922345678',
                'short_bio'        => 'Expert tarot reader and numerologist helping you navigate love, career and life decisions with clarity.',
                'bio'              => 'Dr. Priya Malhotra is a certified tarot reader and numerologist with 15 years of experience. She studied Tarot Symbolism at the School of Esoteric Arts, Mumbai, and holds a PhD in Psychology, which adds a unique therapeutic dimension to her readings.',
                'specializations'  => 'Tarot Reading, Numerology, Love & Relationships',
                'languages'        => 'English, Hindi, Marathi',
                'experience_years' => 15,
                'education'        => 'PhD Psychology, MSc Numerology',
                'chat_rate'        => 18.00,
                'call_rate'        => 22.00,
                'video_rate'       => 30.00,
                'rating_avg'       => 4.92,
                'total_reviews'    => 2310,
                'total_consultations' => 18000,
                'status'           => 'active',
                'is_featured'      => true,
                'is_available'     => true,
                'services'         => ['Tarot Reading', 'Numerology', 'Love & Relationship'],
            ],
            [
                'display_name'     => 'Acharya Suresh Gupta',
                'email'            => 'suresh.gupta@astrovani.test',
                'phone'            => '+91 9733456789',
                'short_bio'        => 'KP Astrology specialist known for precision timing of events and accurate future predictions.',
                'bio'              => 'Acharya Suresh Gupta is one of India\'s most accurate KP (Krishnamurti Paddhati) astrologers. With 18 years of dedicated practice, he specializes in predicting the exact timing of life events — from marriage and childbirth to career milestones and property acquisitions.',
                'specializations'  => 'KP Astrology, Vedic Astrology, Career Guidance',
                'languages'        => 'Hindi, English, Telugu',
                'experience_years' => 18,
                'education'        => 'KP Astrology Master, Jyotish Visharad',
                'chat_rate'        => 20.00,
                'call_rate'        => 25.00,
                'video_rate'       => 35.00,
                'rating_avg'       => 4.76,
                'total_reviews'    => 987,
                'total_consultations' => 8200,
                'status'           => 'active',
                'is_featured'      => false,
                'is_available'     => false,
                'services'         => ['KP Astrology', 'Vedic Astrology'],
            ],
            [
                'display_name'     => 'Jyoti Nair',
                'email'            => 'jyoti.nair@astrovani.test',
                'phone'            => '+91 9844567890',
                'short_bio'        => 'Palm reader and gemstone advisor with deep knowledge of remedial astrology for transformation.',
                'bio'              => 'Jyoti Nair has spent 10 years mastering the art of Palmistry and Gemstone Therapy. She studied under renowned masters in Kerala and has helped hundreds of clients through health challenges, relationship difficulties, and financial blocks using astrological gemstones and remedies.',
                'specializations'  => 'Palm Reading, Gemstone Recommendation, Numerology',
                'languages'        => 'English, Malayalam, Hindi, Tamil',
                'experience_years' => 10,
                'education'        => 'Diploma in Palmistry, Gemology Certificate',
                'chat_rate'        => 15.00,
                'call_rate'        => 18.00,
                'video_rate'       => 25.00,
                'rating_avg'       => 4.68,
                'total_reviews'    => 654,
                'total_consultations' => 4800,
                'status'           => 'active',
                'is_featured'      => true,
                'is_available'     => true,
                'services'         => ['Palm Reading', 'Gemstone Recommendation', 'Numerology'],
            ],
            [
                'display_name'     => 'Astrologer Amit Verma',
                'email'            => 'amit.verma@astrovani.test',
                'phone'            => '+91 9955678901',
                'short_bio'        => 'New astrologer specializing in modern Vedic techniques and youth-focused relationship counseling.',
                'bio'              => 'Amit Verma is a young and dynamic astrologer who blends traditional Vedic methods with modern psychological insights. He specializes in helping millennials navigate career decisions, love life challenges, and personal growth.',
                'specializations'  => 'Vedic Astrology, Love & Relationships',
                'languages'        => 'Hindi, English',
                'experience_years' => 3,
                'education'        => 'Jyotish Ratna',
                'chat_rate'        => 10.00,
                'call_rate'        => 12.00,
                'video_rate'       => 18.00,
                'rating_avg'       => 4.55,
                'total_reviews'    => 123,
                'total_consultations' => 850,
                'status'           => 'pending',
                'is_featured'      => false,
                'is_available'     => false,
                'services'         => ['Vedic Astrology', 'Love & Relationship'],
            ],
            [
                'display_name'     => 'Pandit Anand Shastri',
                'email'            => 'anand.shastri@astrovani.test',
                'phone'            => '+91 9877890123',
                'short_bio'        => 'Celebrated Kundli matching and Muhurat expert with 19 years of classical Vedic tradition.',
                'bio'              => 'Pandit Anand Shastri belongs to a distinguished lineage of Vedic scholars from Varanasi. With 19 years of practical consultation, he has guided more than 15,000 clients across India and abroad in marriage matchmaking, auspicious ceremony timing (Muhurat), and planetary dosha nivaran.',
                'specializations'  => 'Vedic Astrology, Kundli Matching, Muhurat',
                'languages'        => 'Hindi, English, Gujarati',
                'experience_years' => 19,
                'education'        => 'Acharya in Jyotish, Sampurnanand Sanskrit University',
                'chat_rate'        => 22.00,
                'call_rate'        => 28.00,
                'video_rate'       => 38.00,
                'rating_avg'       => 4.88,
                'total_reviews'    => 1420,
                'total_consultations' => 11000,
                'status'           => 'active',
                'is_featured'      => true,
                'is_available'     => true,
                'services'         => ['Vedic Astrology', 'Love & Relationship', 'Gemstone Recommendation'],
            ],
            [
                'display_name'     => 'Sunita Devi',
                'email'            => 'sunita.devi@astrovani.test',
                'phone'            => '+91 9766789012',
                'short_bio'        => 'Intuitive tarot reader and empath providing heartfelt guidance on love and life paths.',
                'bio'              => 'Sunita Devi combines intuitive tarot reading with aura cleansing insights. Over the past 9 years, she has helped clients find inner peace, resolve romantic turmoil, and embrace self-love with cosmic alignment.',
                'specializations'  => 'Tarot Reading, Love & Relationships, Numerology',
                'languages'        => 'Hindi, English, Punjabi',
                'experience_years' => 9,
                'education'        => 'Certified Master Tarot Reader, Angel Therapy Practitioner',
                'chat_rate'        => 14.00,
                'call_rate'        => 18.00,
                'video_rate'       => 24.00,
                'rating_avg'       => 4.79,
                'total_reviews'    => 840,
                'total_consultations' => 6100,
                'status'           => 'active',
                'is_featured'      => false,
                'is_available'     => true,
                'services'         => ['Tarot Reading', 'Love & Relationship', 'Numerology'],
            ],
            [
                'display_name'     => 'Acharya Vikramaditya',
                'email'            => 'vikramaditya@astrovani.test',
                'phone'            => '+91 9811987654',
                'short_bio'        => 'Master of KP Astrology and Corporate Vastu with 26 years guiding entrepreneurs and professionals.',
                'bio'              => 'Acharya Vikramaditya is widely consulted by business leaders, entrepreneurs, and professionals across the country. Specializing in high-precision KP Astrology and Commercial Vastu audits, his strategic remedies have helped countless businesses overcome stagnation and achieve remarkable growth.',
                'specializations'  => 'KP Astrology, Vastu Shastra, Business Astrology',
                'languages'        => 'Hindi, English, Bengali',
                'experience_years' => 26,
                'education'        => 'PhD in Astrological Sciences, Gold Medalist',
                'chat_rate'        => 35.00,
                'call_rate'        => 45.00,
                'video_rate'       => 60.00,
                'rating_avg'       => 4.95,
                'total_reviews'    => 3120,
                'total_consultations' => 21000,
                'status'           => 'active',
                'is_featured'      => true,
                'is_available'     => false,
                'services'         => ['KP Astrology', 'Vastu Shastra', 'Gemstone Recommendation'],
            ],
            [
                'display_name'     => 'Meera Krishnan',
                'email'            => 'meera.krishnan@astrovani.test',
                'phone'            => '+91 9944321098',
                'short_bio'        => 'Certified Numerologist & Palmistry master uncovering life cycles and career potential.',
                'bio'              => 'Meera Krishnan has dedicated 12 years to decoding the vibrational frequency of numbers and the ancient map of hand lines. Her consultations offer practical career navigation, lucky date selection, and name correction analysis for babies and corporate brands.',
                'specializations'  => 'Numerology, Palm Reading, Name Correction',
                'languages'        => 'English, Tamil, Hindi, Kannada',
                'experience_years' => 12,
                'education'        => 'MSc Applied Mathematics, Diploma in Vedic Numerology',
                'chat_rate'        => 16.00,
                'call_rate'        => 20.00,
                'video_rate'       => 28.00,
                'rating_avg'       => 4.81,
                'total_reviews'    => 1150,
                'total_consultations' => 7400,
                'status'           => 'active',
                'is_featured'      => false,
                'is_available'     => true,
                'services'         => ['Numerology', 'Palm Reading', 'Vedic Astrology'],
            ],
            [
                'display_name'     => 'Pandit Hemant Joshi',
                'email'            => 'hemant.joshi@astrovani.test',
                'phone'            => '+91 9822114477',
                'short_bio'        => 'Energetic Vedic practitioner and Vastu consultant applying classical texts to contemporary living.',
                'bio'              => 'Pandit Hemant Joshi holds a Shastri degree in Jyotish from Uttarakhand Sanskrit University. With 6 years of focused practice in domestic Vastu and birth chart analysis, he is applying for verified platform status on AstroVani.',
                'specializations'  => 'Vedic Astrology, Vastu Shastra',
                'languages'        => 'Hindi, English, Sanskrit',
                'experience_years' => 6,
                'education'        => 'Shastri (Jyotish), Uttarakhand Sanskrit University',
                'chat_rate'        => 12.00,
                'call_rate'        => 15.00,
                'video_rate'       => 20.00,
                'rating_avg'       => 4.60,
                'total_reviews'    => 88,
                'total_consultations' => 420,
                'status'           => 'pending',
                'is_featured'      => false,
                'is_available'     => false,
                'services'         => ['Vedic Astrology', 'Vastu Shastra'],
            ],
        ];

        $availabilityTemplate = [
            ['day_of_week' => 'monday',    'start_time' => '09:00', 'end_time' => '18:00'],
            ['day_of_week' => 'tuesday',   'start_time' => '09:00', 'end_time' => '18:00'],
            ['day_of_week' => 'wednesday', 'start_time' => '09:00', 'end_time' => '18:00'],
            ['day_of_week' => 'thursday',  'start_time' => '09:00', 'end_time' => '18:00'],
            ['day_of_week' => 'friday',    'start_time' => '09:00', 'end_time' => '17:00'],
            ['day_of_week' => 'saturday',  'start_time' => '10:00', 'end_time' => '15:00'],
        ];

        foreach ($astrologers as $data) {
            $serviceNames = $data['services'];
            unset($data['services']);

            $slug = Str::slug($data['display_name']) . '-' . Str::random(4);
            $data['slug'] = $slug;

            $astrologer = Astrologer::firstOrCreate(
                ['email' => $data['email']],
                $data
            );

            // Attach services
            $serviceIds = Service::whereIn('name', $serviceNames)->pluck('id');
            $astrologer->services()->syncWithoutDetaching($serviceIds);

            // Seed availability for active astrologers
            if ($astrologer->status === 'active' && $astrologer->availability()->count() === 0) {
                foreach ($availabilityTemplate as $slot) {
                    $astrologer->availability()->create([...$slot, 'is_active' => true]);
                }
            }
        }

        $this->command->info('✓ ' . count($astrologers) . ' astrologers seeded with services and availability.');
    }
}
