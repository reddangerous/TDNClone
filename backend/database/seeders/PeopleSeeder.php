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
        $people = [
            [
                'name' => 'Emma Wilson',
                'age' => 25,
                'location' => 'New York, NY',
                'bio' => 'Love hiking and coffee. Adventure seeker! ☕️🏔️',
                'pictures' => ['https://api.example.com/images/emma1.jpg', 'https://api.example.com/images/emma2.jpg'],
            ],
            [
                'name' => 'Sofia Martinez',
                'age' => 23,
                'location' => 'Los Angeles, CA',
                'bio' => 'Artist and musician. Let\'s create something together 🎨🎵',
                'pictures' => ['https://api.example.com/images/sofia1.jpg'],
            ],
            [
                'name' => 'Olivia Chen',
                'age' => 26,
                'location' => 'San Francisco, CA',
                'bio' => 'Tech enthusiast. Love traveling and good conversations',
                'pictures' => ['https://api.example.com/images/olivia1.jpg', 'https://api.example.com/images/olivia2.jpg'],
            ],
            [
                'name' => 'Isabella Rodriguez',
                'age' => 24,
                'location' => 'Miami, FL',
                'bio' => 'Beach lover. Yoga instructor 🧘‍♀️🌊',
                'pictures' => ['https://api.example.com/images/isabella1.jpg'],
            ],
            [
                'name' => 'Ava Johnson',
                'age' => 27,
                'location' => 'Boston, MA',
                'bio' => 'Marketing professional. Foodie and traveler ✈️🍽️',
                'pictures' => ['https://api.example.com/images/ava1.jpg', 'https://api.example.com/images/ava2.jpg'],
            ],
            [
                'name' => 'Mia Thompson',
                'age' => 22,
                'location' => 'Chicago, IL',
                'bio' => 'Designer and photographer. Cat lover 📸🐱',
                'pictures' => ['https://api.example.com/images/mia1.jpg'],
            ],
            [
                'name' => 'Charlotte Anderson',
                'age' => 25,
                'location' => 'Seattle, WA',
                'bio' => 'Software engineer. Coffee addict and book worm ☕📚',
                'pictures' => ['https://api.example.com/images/charlotte1.jpg', 'https://api.example.com/images/charlotte2.jpg'],
            ],
            [
                'name' => 'Sophia Lee',
                'age' => 24,
                'location' => 'Austin, TX',
                'bio' => 'Fitness instructor. Let\'s go on an adventure! 💪🌲',
                'pictures' => ['https://api.example.com/images/sophia1.jpg'],
            ],
            [
                'name' => 'Emily Garcia',
                'age' => 26,
                'location' => 'Denver, CO',
                'bio' => 'Writer and dreamer. Passionate about sustainability ✍️🌱',
                'pictures' => ['https://api.example.com/images/emily1.jpg', 'https://api.example.com/images/emily2.jpg'],
            ],
            [
                'name' => 'Harper Brown',
                'age' => 23,
                'location' => 'Portland, OR',
                'bio' => 'Eco-warrior. Love farmers markets and indie films 🌍🎬',
                'pictures' => ['https://api.example.com/images/harper1.jpg'],
            ],
            [
                'name' => 'Amelia Davis',
                'age' => 28,
                'location' => 'Washington, DC',
                'bio' => 'Lawyer and volunteer. Believe in making a difference ⚖️💙',
                'pictures' => ['https://api.example.com/images/amelia1.jpg', 'https://api.example.com/images/amelia2.jpg'],
            ],
            [
                'name' => 'Evelyn Wilson',
                'age' => 25,
                'location' => 'Nashville, TN',
                'bio' => 'Music lover. Guitarist and singer 🎸🎤',
                'pictures' => ['https://api.example.com/images/evelyn1.jpg'],
            ],
        ];

        foreach ($people as $person) {
            Person::create($person);
        }
    }
}
