<?php

namespace App\Console\Commands;

use App\Models\Person;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

class FetchThisPersonDoesNotExistPeople extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:fetch-this-person-does-not-exist-people {--count=20 : Number of people to fetch}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fetch people from mock data and store images locally';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $count = $this->option('count');
        $this->info("Creating {$count} profiles with mock data and AI-generated avatar images...");

        $locations = [
            'New York, NY',
            'Los Angeles, CA',
            'Chicago, IL',
            'Houston, TX',
            'Phoenix, AZ',
            'Philadelphia, PA',
            'San Antonio, TX',
            'San Diego, CA',
            'Dallas, TX',
            'San Jose, CA',
            'Austin, TX',
            'Jacksonville, FL',
            'Miami, FL',
            'Denver, CO',
            'Seattle, WA',
            'Boston, MA',
            'Portland, OR',
            'Nashville, TN',
            'Atlanta, GA',
            'Washington, DC',
        ];

        $bios = [
            'Love hiking and coffee ☕️🏔️',
            'Artist and musician 🎨🎵',
            'Tech enthusiast. Love traveling',
            'Beach lover. Yoga instructor 🧘‍♀️🌊',
            'Marketing professional. Foodie ✈️🍽️',
            'Designer and photographer 📸',
            'Software engineer. Book worm 📚',
            'Fitness instructor. Adventure seeker 💪🌲',
            'Writer and dreamer 🌱',
            'Eco-warrior. Love farmers markets 🌍',
            'Lawyer and volunteer ⚖️💙',
            'Music lover. Guitarist 🎸🎤',
            'Chef by day, dreamer by night 👨‍🍳',
            'Entrepreneur and coffee addict ☕',
            'Yoga and meditation enthusiast 🧘',
            'Photography enthusiast 📷',
            'Traveler and foodie 🌍🍽️',
            'Book lover and author ✍️📖',
            'Fitness coach and health enthusiast 💪',
            'Artist and creative soul 🎨',
            'Dancer and choreographer 💃',
            'Nature lover and environmentalist 🌿',
            'Fashion enthusiast 👗',
            'Adventure seeker and rock climber 🧗',
            'Wine and food pairing enthusiast 🍷',
        ];

        $firstNames = [
            'Emma', 'Olivia', 'Ava', 'Sophia', 'Isabella', 'Mia', 'Charlotte', 'Amelia',
            'Harper', 'Evelyn', 'Abigail', 'Emily', 'Elizabeth', 'Mila', 'Ella', 'Avery',
            'Sofia', 'Camila', 'Aria', 'Scarlett', 'Victoria', 'Madison', 'Luna', 'Grace',
            'Chloe', 'Penelope', 'Layla', 'Riley', 'Zoey', 'Nora', 'Sarah', 'Jessica',
            'Rachel', 'Anna', 'Karen', 'Kathleen', 'Nancy', 'Lisa', 'Betty', 'Margaret',
            'Sandra', 'Ashley', 'Dorothy', 'Kimberly', 'Emily', 'Donna', 'Michelle',
        ];

        $lastNames = [
            'Smith', 'Johnson', 'Williams', 'Brown', 'Jones', 'Garcia', 'Miller', 'Davis',
            'Rodriguez', 'Martinez', 'Hernandez', 'Lopez', 'Gonzalez', 'Wilson', 'Anderson',
            'Thomas', 'Taylor', 'Moore', 'Jackson', 'Martin', 'Lee', 'Perez', 'Thompson',
            'White', 'Harris', 'Sanchez', 'Clark', 'Ramirez', 'Lewis', 'Robinson', 'Young',
            'Allen', 'King', 'Wright', 'Scott', 'Torres', 'Peterson', 'Phillips', 'Campbell',
            'Parker', 'Evans', 'Edwards', 'Collins', 'Reeves', 'Stewart', 'Morris',
        ];

        $createdCount = 0;
        $failedCount = 0;

        for ($i = 0; $i < $count; $i++) {
            try {
                $firstName = $firstNames[array_rand($firstNames)];
                $lastName = $lastNames[array_rand($lastNames)];
                $name = $firstName . ' ' . $lastName;
                $age = rand(20, 45);
                $location = $locations[array_rand($locations)];
                $bio = $bios[array_rand($bios)];

                $this->line("Creating person " . ($i + 1) . "/{$count}: {$name}");

                // Use DiceBear API for avatar images (reliable and doesn't require attribution)
                // This generates consistent, unique avatars based on a seed
                $seed = strtolower(str_replace(' ', '', $name)) . $i;
                $avatarUrl = "https://api.dicebear.com/7.x/avataaars/jpg?seed={$seed}&scale=80&backgroundColor=random";

                // Download avatar image
                $imageResponse = Http::timeout(30)->get($avatarUrl);
                if ($imageResponse->failed()) {
                    $this->warn("⚠ Failed to download avatar for {$name}, using placeholder");
                    // Create a simple placeholder image locally if download fails
                    $fileName = 'people/' . uniqid() . '.jpg';
                    $this->createPlaceholderImage($fileName);
                    $storedImageUrl = '/storage/' . $fileName;
                } else {
                    // Store image locally
                    $fileName = 'people/' . uniqid() . '.jpg';
                    Storage::disk('public')->put($fileName, $imageResponse->body());
                    $storedImageUrl = '/storage/' . $fileName;
                }

                // Create person record
                Person::create([
                    'name' => $name,
                    'age' => $age,
                    'location' => $location,
                    'bio' => $bio,
                    'pictures' => json_encode([$storedImageUrl]),
                    'likes_count' => 0,
                    'dislikes_count' => 0,
                ]);

                $this->line("✓ Created: {$name}, {$age} years old from {$location}");
                $createdCount++;

                // Small delay to avoid rate limiting
                usleep(500000); // 0.5 second

            } catch (\Exception $e) {
                $this->error("✗ Error creating person: " . $e->getMessage());
                $failedCount++;
            }
        }

        $this->info("\n========== Summary ==========");
        $this->info("✓ Created: {$createdCount} people");
        if ($failedCount > 0) {
            $this->warn("⚠ Failed: {$failedCount} people");
        }
        $this->info("All profiles are stored with local image paths in /storage/people/");
    }

    /**
     * Create a simple placeholder image in JPG format
     */
    private function createPlaceholderImage(string $filePath): void
    {
        // Create a simple colored placeholder image
        if (!extension_loaded('gd')) {
            // Fallback: create a simple base64 encoded placeholder
            $colors = ['ff6b6b', '4ecdc4', '45b7d1', 'f39c12', '9b59b6', '1abc9c', '3498db'];
            $color = $colors[array_rand($colors)];
            $initials = 'A';
            
            // Create a more reliable placeholder using a simple approach
            $data = [
                'initials' => $initials,
                'color' => $color,
                'timestamp' => time(),
            ];
            
            // Generate a simple image URL from a public service
            Storage::disk('public')->put($filePath, $this->generateSimplePlaceholder());
            return;
        }

        $width = 300;
        $height = 300;
        
        $image = imagecreatetruecolor($width, $height);
        
        // Random background color
        $bgColor = imagecolorallocate($image, rand(100, 255), rand(100, 255), rand(100, 255));
        imagefill($image, 0, 0, $bgColor);

        // Add circle in center
        $circleColor = imagecolorallocate($image, rand(50, 200), rand(50, 200), rand(50, 200));
        imagefilledellipse($image, $width / 2, $height / 2, 150, 150, $circleColor);

        // Save as JPG
        ob_start();
        imagejpeg($image, null, 85);
        $imageContent = ob_get_clean();
        imagedestroy($image);

        Storage::disk('public')->put($filePath, $imageContent);
    }

    /**
     * Generate a simple placeholder image as binary
     */
    private function generateSimplePlaceholder(): string
    {
        // Create minimal valid JPG (1x1 pixel)
        // This is a 1x1 blue pixel JPG
        return base64_decode(
            '/9j/4AAQSkZJRgABAQEAYABgAAD/2wBDAAgGBgcGBQgHBwcJCQgKDBQNDAsLDBkSEw8UHRofHh0aHBwgJC4nICIsIxwcKDcpLDAxNDQ0Hyc5PTgyPC4zNDL/2wBDAQkJCQwLDBgNDRgyIRwhMjIyMjIyMjIyMjIyMjIyMjIyMjIyMjIyMjIyMjIyMjIyMjIyMjIyMjIyMjIyMjIyMjL/wAARCAABAAEDASIAAhEBAxEB/8QAFQABAQAAAAAAAAAAAAAAAAAAAAv/xAAUEAEAAAAAAAAAAAAAAAAAAAAA/8VAFQEBAQAAAAAAAAAAAAAAAAAAAAX/xAAUEQEAAAAAAAAAAAAAAAAAAAAA/9oADAMBAAIRAxEAPwCwAA//Z'
        );
    }
}
