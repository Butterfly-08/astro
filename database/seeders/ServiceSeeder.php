<?php

namespace Database\Seeders;

use App\Models\Service;
use Illuminate\Database\Seeder;

class ServiceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $services = [
            [
                'name'              => 'Vedic Astrology',
                'icon'              => 'bi-stars',
                'short_description' => 'Ancient Indian astrology system for life insights and predictions.',
                'description'       => 'Vedic Astrology, also known as Jyotish Shastra, is one of the oldest sciences in the world. It provides deep insights into personality, relationships, career, health, and life events based on the position of celestial bodies at the time of birth.',
                'type'              => 'consultation',
                'status'            => 'active',
                'is_featured'       => true,
                'sort_order'        => 1,
            ],
            [
                'name'              => 'Tarot Reading',
                'icon'              => 'bi-emoji-sunglasses',
                'short_description' => 'Symbolic card readings for clarity on life questions.',
                'description'       => 'Tarot reading uses a deck of 78 cards, each with symbolic imagery, to provide guidance on relationships, career, finances, and spiritual growth. Our experienced tarot readers interpret the cards intuitively to provide meaningful insights.',
                'type'              => 'consultation',
                'status'            => 'active',
                'is_featured'       => true,
                'sort_order'        => 2,
            ],
            [
                'name'              => 'Numerology',
                'icon'              => 'bi-123',
                'short_description' => 'Discover life path, destiny & personality through numbers.',
                'description'       => 'Numerology is the mystical study of numbers and their influence on human life. By analyzing your name and birth date, a numerologist can reveal your life path number, destiny number, soul urge, and personality number.',
                'type'              => 'consultation',
                'status'            => 'active',
                'is_featured'       => false,
                'sort_order'        => 3,
            ],
            [
                'name'              => 'Vastu Shastra',
                'icon'              => 'bi-house-heart-fill',
                'short_description' => 'Harmonize your living & work spaces with ancient science.',
                'description'       => 'Vastu Shastra is the traditional Hindu science of architecture and spatial arrangement. Our Vastu consultants help you align your home or office energies to attract prosperity, health, and happiness.',
                'type'              => 'consultation',
                'status'            => 'active',
                'is_featured'       => false,
                'sort_order'        => 4,
            ],
            [
                'name'              => 'KP Astrology',
                'icon'              => 'bi-moon-stars-fill',
                'short_description' => 'Krishnamurti Paddhati — highly accurate predictive system.',
                'description'       => 'KP Astrology (Krishnamurti Paddhati) is a highly advanced system of astrological prediction developed by Late Prof. K.S. Krishnamurti. It is known for its precision in timing events and predictions.',
                'type'              => 'consultation',
                'status'            => 'active',
                'is_featured'       => false,
                'sort_order'        => 5,
            ],
            [
                'name'              => 'Palm Reading',
                'icon'              => 'bi-hand-index-thumb-fill',
                'short_description' => 'Read the lines of your hand to uncover your destiny.',
                'description'       => 'Palmistry or Chiromancy is the art of characterization and foretelling the future through the study of the palm. Our expert palmists analyze the lines, mounts, and shapes of your hands to reveal your personality and future.',
                'type'              => 'consultation',
                'status'            => 'active',
                'is_featured'       => false,
                'sort_order'        => 6,
            ],
            [
                'name'              => 'Love & Relationship',
                'icon'              => 'bi-heart-fill',
                'short_description' => 'Astrological guidance for relationships and compatibility.',
                'description'       => 'Our love and relationship astrology consultations help you understand your romantic compatibility, resolve relationship issues, and navigate marriage decisions with cosmic guidance.',
                'type'              => 'consultation',
                'status'            => 'active',
                'is_featured'       => true,
                'sort_order'        => 7,
            ],
            [
                'name'              => 'Gemstone Recommendation',
                'icon'              => 'bi-gem',
                'short_description' => 'Certified gemstone prescriptions for planetary remedies.',
                'description'       => 'Gemstones have been used for centuries as astrological remedies. Our expert astrologers analyze your birth chart and prescribe the right gemstones to strengthen beneficial planets and counteract malefic influences.',
                'type'              => 'both',
                'status'            => 'active',
                'is_featured'       => false,
                'sort_order'        => 8,
            ],
        ];

        foreach ($services as $serviceData) {
            Service::firstOrCreate(['name' => $serviceData['name']], $serviceData);
        }

        $this->command->info('✓ ' . count($services) . ' services seeded.');
    }
}
