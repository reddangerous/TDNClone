<?php

namespace App\Console\Commands;

use App\Models\Person;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

class DownloadProfileImages extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:download-profile-images {--force : Force re-download all images}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Download real profile images from free APIs and store locally';

    /**
     * Image sources in order of preference
     */
    private array $imageSources = [
        'unsplash',       // Best quality
        'randomuser',     // Good variety
        'pravatar',       // Simple faces
        'thispersondoesnotexist',  // AI faces (when working)
        'dicebear',       // Fallback avatars
    ];

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $force = $this->option('force');
        
        $this->info('🖼️  Profile Image Downloader');
        $this->info(str_repeat('=', 50));

        // Get people to process
        if ($force) {
            $people = Person::all();
            $this->warn('Force mode: Re-downloading all images');
        } else {
            // Only download if no images or placeholder images
            $people = Person::where(function ($query) {
                $query->whereNull('pictures')
                    ->orWhere('pictures', 'json')
                    ->orWhere('pictures', '[]')
                    ->orWhereRaw("pictures LIKE '%placeholder%'")
                    ->orWhereRaw("pictures LIKE '%1x1%'");
            })->get();
        }

        if ($people->isEmpty()) {
            $this->info('✓ All profiles already have images!');
            return Command::SUCCESS;
        }

        $this->info("Processing " . count($people) . " profiles...\n");

        $successCount = 0;
        $failedCount = 0;

        foreach ($people as $index => $person) {
            $this->line("[$index/" . count($people) . "] {$person->name}");

            $imageUrl = $this->getWorkingImageUrl($index, $person->id);

            if (!$imageUrl) {
                $this->error("  ✗ Could not find working image source");
                $failedCount++;
                continue;
            }

            $storedPath = $this->downloadAndStoreImage($imageUrl, $person->id);

            if ($storedPath) {
                $person->update(['pictures' => json_encode([$storedPath])]);
                $this->line("  ✓ <fg=green>{$storedPath}</>");
                $successCount++;
            } else {
                $this->error("  ✗ Failed to store image");
                $failedCount++;
            }

            // Rate limiting
            sleep(1);
        }

        $this->info("\n" . str_repeat('=', 50));
        $this->info("Results:");
        $this->info("  ✓ Downloaded: <fg=green>{$successCount}</>");
        if ($failedCount > 0) {
            $this->warn("  ✗ Failed: {$failedCount}");
        }
        $this->info("\n📁 Images stored in: <fg=cyan>storage/app/public/people/</>");
        $this->info("🔗 Accessible at: <fg=cyan>http://192.168.2.53:8000/storage/people/{filename}</>");

        return Command::SUCCESS;
    }

    /**
     * Try multiple image sources until one works
     */
    private function getWorkingImageUrl(int $seed, int $personId): ?string
    {
        foreach ($this->imageSources as $source) {
            try {
                $url = $this->buildImageUrl($source, $seed, $personId);
                
                if ($this->isUrlAccessible($url)) {
                    $this->line("    Using: <fg=blue>{$source}</> source");
                    return $url;
                }
            } catch (\Exception $e) {
                // Try next source
                continue;
            }
        }

        return null;
    }

    /**
     * Build image URL based on source
     */
    private function buildImageUrl(string $source, int $seed, int $personId): string
    {
        return match ($source) {
            'unsplash' => $this->getUnsplashUrl($seed),
            'randomuser' => $this->getRandomUserUrl($seed),
            'pravatar' => $this->getPravatrUrl($seed),
            'thispersondoesnotexist' => $this->getThisPersonDoesNotExistUrl($seed),
            'dicebear' => $this->getDiceBearUrl($seed),
            default => '',
        };
    }

    /**
     * Unsplash - High quality, free, open source
     * Direct image URL (no API key required)
     */
    private function getUnsplashUrl(int $seed): string
    {
        $queries = ['portrait', 'woman', 'girl', 'face', 'headshot', 'avatar', 'profile'];
        $query = $queries[$seed % count($queries)];
        $randomNum = rand(1, 10000);
        
        return "https://source.unsplash.com/400x400/?{$query}&sig={$randomNum}";
    }

    /**
     * Random User - Free API with diverse faces
     */
    private function getRandomUserUrl(int $seed): string
    {
        $gender = ['women', 'men'][$seed % 2];
        $index = ($seed % 70) + 1;
        
        return "https://randomuser.me/api/portraits/{$gender}/{$index}.jpg";
    }

    /**
     * Pravatar - Simple avatar generation (based on hash)
     */
    private function getPravatrUrl(int $seed): string
    {
        return "https://i.pravatar.cc/400?img=" . ($seed % 70);
    }

    /**
     * This Person Does Not Exist - AI generated faces
     * May not work reliably
     */
    private function getThisPersonDoesNotExistUrl(int $seed): string
    {
        return "https://thispersondoesnotexist.com/image?seed={$seed}";
    }

    /**
     * DiceBear - Diverse avatar styles
     */
    private function getDiceBearUrl(int $seed): string
    {
        $styles = ['avataaars', 'big-ears', 'big-smile', 'pixel-art', 'personas'];
        $style = $styles[$seed % count($styles)];
        
        return "https://api.dicebear.com/7.x/{$style}/jpg?seed={$seed}&scale=80&backgroundColor=random";
    }

    /**
     * Quick check if URL is accessible
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
            $response = Http::timeout(30)->get($url);

            if ($response->failed()) {
                return null;
            }

            $imageContent = $response->body();

            if (empty($imageContent)) {
                return null;
            }

            // Generate unique filename
            $fileName = 'people/profile_' . $personId . '_' . uniqid() . '.jpg';

            // Store in public disk
            Storage::disk('public')->put($fileName, $imageContent);

            // Return accessible URL path
            return '/storage/' . $fileName;

        } catch (\Exception $e) {
            return null;
        }
    }
}
