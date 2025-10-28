<?php

namespace Database\Seeders;

use App\Models\Person;
use Illuminate\Database\Seeder;

class PeopleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $baseUrl = 'http://192.168.2.53:8000/storage/people';
        
        $people = [
            [
                'name' => 'Emma Wilson',
                'age' => 25,
                'location' => 'New York, NY',
                'bio' => 'Love hiking and coffee. Adventure seeker! ☕️🏔️',
                'pictures' => [$baseUrl . '/profile_1_690074bc8a123.jpg'],
            ],
            [
                'name' => 'Sofia Martinez',
                'age' => 23,
                'location' => 'Los Angeles, CA',
                'bio' => 'Artist and musician. Let\'s create something together 🎨🎵',
                'pictures' => [$baseUrl . '/profile_2_69006966e8760.jpg'],
            ],
            [
                'name' => 'Olivia Chen',
                'age' => 26,
                'location' => 'San Francisco, CA',
                'bio' => 'Tech enthusiast. Love traveling and good conversations',
                'pictures' => [$baseUrl . '/profile_3_690074c26a9e0.jpg'],
            ],
            [
                'name' => 'Isabella Rodriguez',
                'age' => 24,
                'location' => 'Miami, FL',
                'bio' => 'Beach lover. Yoga instructor 🧘‍♀️🌊',
                'pictures' => [$baseUrl . '/profile_4_690074c59f4b5.jpg'],
            ],
            [
                'name' => 'Ava Johnson',
                'age' => 27,
                'location' => 'Boston, MA',
                'bio' => 'Marketing professional. Foodie and traveler ✈️🍽️',
                'pictures' => [$baseUrl . '/profile_5_690074c92f3ec.jpg'],
            ],
            [
                'name' => 'Mia Thompson',
                'age' => 22,
                'location' => 'Chicago, IL',
                'bio' => 'Designer and photographer. Cat lover 📸🐱',
                'pictures' => [$baseUrl . '/profile_6_690074cc5bdfc.jpg'],
            ],
            [
                'name' => 'Charlotte Anderson',
                'age' => 25,
                'location' => 'Seattle, WA',
                'bio' => 'Software engineer. Coffee addict and book worm ☕📚',
                'pictures' => [$baseUrl . '/profile_7_690074cfbae9c.jpg'],
            ],
            [
                'name' => 'Sophia Lee',
                'age' => 24,
                'location' => 'Austin, TX',
                'bio' => 'Fitness instructor. Let\'s go on an adventure! 💪🌲',
                'pictures' => [$baseUrl . '/profile_8_690074d4bfbfb.jpg'],
            ],
            [
                'name' => 'Emily Garcia',
                'age' => 26,
                'location' => 'Denver, CO',
                'bio' => 'Writer and dreamer. Passionate about sustainability ✍️🌱',
                'pictures' => [$baseUrl . '/profile_9_690074d7fb3c9.jpg'],
            ],
            [
                'name' => 'Harper Brown',
                'age' => 23,
                'location' => 'Portland, OR',
                'bio' => 'Eco-warrior. Love farmers markets and indie films 🌍🎬',
                'pictures' => [$baseUrl . '/profile_10_690074dd32c05.jpg'],
            ],
            [
                'name' => 'Amelia Davis',
                'age' => 28,
                'location' => 'Washington, DC',
                'bio' => 'Lawyer and volunteer. Believe in making a difference ⚖️💙',
                'pictures' => [$baseUrl . '/profile_11_690074e0cb7b8.jpg'],
            ],
            [
                'name' => 'Evelyn Wilson',
                'age' => 25,
                'location' => 'Nashville, TN',
                'bio' => 'Music lover. Guitarist and singer 🎸🎤',
                'pictures' => [$baseUrl . '/profile_12_690074e469bfa.jpg'],
            ],
            [
                'name' => 'Grace Mitchell',
                'age' => 24,
                'location' => 'Atlanta, GA',
                'bio' => 'Love good vibes and great conversations 🌟',
                'pictures' => [$baseUrl . '/profile_13_690074e82a733.jpg'],
            ],
            [
                'name' => 'Luna Torres',
                'age' => 26,
                'location' => 'Phoenix, AZ',
                'bio' => 'Adventure enthusiast. Always up for road trips 🚗',
                'pictures' => [$baseUrl . '/profile_14_690074ebbb3c9.jpg'],
            ],
            [
                'name' => 'Victoria Harris',
                'age' => 25,
                'location' => 'Philadelphia, PA',
                'bio' => 'Entrepreneur and dreamer 💼✨',
                'pictures' => [$baseUrl . '/profile_15_690074ef554cf.jpg'],
            ],
            [
                'name' => 'Ruby Clark',
                'age' => 23,
                'location' => 'San Antonio, TX',
                'bio' => 'Yoga and wellness lover 🧘‍♀️',
                'pictures' => [$baseUrl . '/profile_16_690074f2e3c6b.jpg'],
            ],
            [
                'name' => 'Jasmine White',
                'age' => 27,
                'location' => 'San Diego, CA',
                'bio' => 'Beach life, best life 🏖️',
                'pictures' => [$baseUrl . '/profile_17_690074f68b1f9.jpg'],
            ],
            [
                'name' => 'Aurora Green',
                'age' => 24,
                'location' => 'Dallas, TX',
                'bio' => 'Foodie and travel blogger 🍽️✈️',
                'pictures' => [$baseUrl . '/profile_18_690074fa2b2fa.jpg'],
            ],
            [
                'name' => 'Stella Blue',
                'age' => 25,
                'location' => 'Austin, TX',
                'bio' => 'Artist and creative soul �',
                'pictures' => [$baseUrl . '/profile_19_690074fdb9308.jpg'],
            ],
            [
                'name' => 'Maya Lopez',
                'age' => 26,
                'location' => 'Houston, TX',
                'bio' => 'Passionate about life and people 💕',
                'pictures' => [$baseUrl . '/profile_20_690075015653c.jpg'],
            ],
        ];

        foreach ($people as $person) {
            Person::create($person);
        }
    }
}
