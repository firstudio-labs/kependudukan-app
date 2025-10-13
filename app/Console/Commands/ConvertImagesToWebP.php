<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\BarangWarungku;
use App\Services\ImageConverterService;
use Illuminate\Support\Facades\Storage;

class ConvertImagesToWebP extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'images:convert-to-webp {--force : Force conversion even if WebP already exists}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Convert existing product images to WebP format';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->info('Starting image conversion to WebP...');
        
        $force = $this->option('force');
        $converted = 0;
        $skipped = 0;
        $errors = 0;

        // Get all BarangWarungku with images
        $products = BarangWarungku::whereNotNull('foto')->get();

        $this->info("Found {$products->count()} products with images");

        foreach ($products as $product) {
            $this->line("Processing product: {$product->nama_produk} (ID: {$product->id})");
            
            // Check if already WebP
            if (!$force && str_ends_with($product->foto, '.webp')) {
                $this->line("  - Already WebP, skipping");
                $skipped++;
                continue;
            }

            // Check if file exists
            if (!Storage::disk('public')->exists($product->foto)) {
                $this->error("  - File not found: {$product->foto}");
                $errors++;
                continue;
            }

            // Convert to WebP
            $newPath = ImageConverterService::convertExistingToWebP(
                $product->foto, 
                'warungku', 
                85, 
                800, 
                600
            );

            if ($newPath) {
                // Update database
                $oldPath = $product->foto;
                $product->foto = $newPath;
                $product->save();

                // Delete old file
                ImageConverterService::deleteImage($oldPath);

                $this->info("  - Converted: {$oldPath} -> {$newPath}");
                $converted++;
            } else {
                $this->error("  - Conversion failed for: {$product->foto}");
                $errors++;
            }
        }

        $this->info("\nConversion completed!");
        $this->info("Converted: {$converted}");
        $this->info("Skipped: {$skipped}");
        $this->info("Errors: {$errors}");

        return Command::SUCCESS;
    }
}
