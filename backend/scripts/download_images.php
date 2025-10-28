#!/usr/bin/env php
<?php
/**
 * Image Downloader Script
 * Downloads real profile images from Unsplash and stores them locally
 * Updates the database with new image paths
 * 
 * Usage: php scripts/download_images.php
 */

// Set up Laravel bootstrap
define('LARAVEL_START', microtime(true));

require __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';

// Get the application instance
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);

// Run the app
$app->make(Illuminate\Contracts\Http\Kernel::class);

// Import necessary classes
use App\Models\Person;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Http;

class ImageDownloader
{
    private $storagePath = 'people';
    private $unsplashAccessKey = ''; // Optional: get from https://unsplash.com/developers
    private $searchQueries = [
        'portrait woman',
        'portrait girl',
        'woman face',
        'girl face',
        'woman smile',
        'girl smile',
        'woman outdoor',
        'girl outdoor',
        'woman casual',
        'girl casual',
    ];

    public function __construct()
    {
        // Initialize
    }

    /**
     * Download images for all people in database
     */
    public function downloadImagesForAllPeople(): void
    {
        $people = Person::all();
        
        if ($people->isEmpty()) {
            echo "No people found in database. Run seeder first.\n";
            return;
        }

        echo "Downloading images for " . count($people) . " people...\n";
        echo str_repeat("=", 50) . "\n";

        $successCount = 0;
        $failedCount = 0;

        foreach ($people as $index => $person) {
            echo "[$index/" . count($people) . "] Processing: {$person->name}\n";

            try {
                $imageUrl = $this->getRandomProfileImage($index);
                
                if (!$imageUrl) {
                    echo "  ⚠ Could not get image URL\n";
                    $failedCount++;
                    continue;
                }

                $storedPath = $this->downloadAndStoreImage($imageUrl, $person->id);

                if ($storedPath) {
                    // Update person record
                    $person->update([
                        'pictures' => json_encode([$storedPath])
                    ]);
                    echo "  ✓ Downloaded and stored: $storedPath\n";
                    $successCount++;
                } else {
                    echo "  ✗ Failed to store image\n";
                    $failedCount++;
                }

            } catch (\Exception $e) {
                echo "  ✗ Error: " . $e->getMessage() . "\n";
                $failedCount++;
            }

            // Add delay to respect API rate limits
            sleep(1);
        }

        echo "\n" . str_repeat("=", 50) . "\n";
        echo "Summary:\n";
        echo "  ✓ Success: $successCount\n";
        echo "  ✗ Failed: $failedCount\n";
        echo "Images stored in: storage/app/public/{$this->storagePath}/\n";
    }

    /**
     * Get a random profile image URL from multiple sources
     * Uses public APIs with no authentication requirement
     */
    private function getRandomProfileImage(int $seed): ?string
    {
        $sources = [
            $this->getUnsplashImage($seed),
            $this->getRandomUserImage($seed),
            $this->getUIFacesImage($seed),
            $this->getThisPersonDoesNotExistImage($seed),
            $this->getDiceBearImage($seed),
        ];

        // Try each source until one works
        foreach ($sources as $url) {
            if ($url && $this->isUrlAccessible($url)) {
                return $url;
            }
        }

        return null;
    }

    /**
     * Get image from Unsplash (free, open-source)
     * https://unsplash.com/developers
     */
    private function getUnsplashImage(int $seed): string
    {
        $query = $this->searchQueries[$seed % count($this->searchQueries)];
        $randomNum = rand(1, 10000);
        
        // Unsplash Free API (no key required, but limited to 50/hour)
        // Using direct image URLs that don't require attribution for basic use
        return "https://source.unsplash.com/400x400/?portrait,woman&sig={$randomNum}";
    }

    /**
     * Get image from Random User API (free)
     * https://randomuser.me/
     */
    private function getRandomUserImage(int $seed): string
    {
        // Returns consistent random profile images
        return "https://randomuser.me/api/portraits/women/" . (($seed % 70) + 1) . ".jpg";
    }

    /**
     * Get image from UI Faces API (free)
     * https://www.uifaces.co/
     */
    private function getUIFacesImage(int $seed): string
    {
        // Their free API endpoint for random faces
        return "https://i.pravatar.cc/400?img=" . ($seed % 70);
    }

    /**
     * Get image from This Person Does Not Exist (when working)
     */
    private function getThisPersonDoesNotExistImage(int $seed): string
    {
        return "https://thispersondoesnotexist.com/image?seed={$seed}";
    }

    /**
     * Get image from DiceBear
     */
    private function getDiceBearImage(int $seed): string
    {
        return "https://api.dicebear.com/7.x/avataaars/jpg?seed={$seed}&scale=80";
    }

    /**
     * Check if URL is accessible (returns 200)
     */
    private function isUrlAccessible(string $url): bool
    {
        try {
            $response = Http::timeout(5)->head($url);
            return $response->status() === 200;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Download image and store locally
     */
    private function downloadAndStoreImage(string $url, int $personId): ?string
    {
        try {
            echo "    Downloading from: $url\n";
            
            $response = Http::timeout(30)->get($url);

            if ($response->failed()) {
                echo "    ✗ HTTP Error: " . $response->status() . "\n";
                return null;
            }

            $imageContent = $response->body();

            if (empty($imageContent)) {
                echo "    ✗ Empty image content\n";
                return null;
            }

            // Generate unique filename
            $fileName = $this->storagePath . '/profile_' . $personId . '_' . uniqid() . '.jpg';

            // Store in public disk
            Storage::disk('public')->put($fileName, $imageContent);

            // Return accessible URL path
            return '/storage/' . $fileName;

        } catch (\Exception $e) {
            echo "    ✗ Exception: " . $e->getMessage() . "\n";
            return null;
        }
    }
}

// Run the downloader
try {
    $downloader = new ImageDownloader();
    $downloader->downloadImagesForAllPeople();
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    exit(1);
}
