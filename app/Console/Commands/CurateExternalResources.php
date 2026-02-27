<?php

namespace App\Console\Commands;

use App\Models\CuratedResource;
use App\Models\EducationalResource;
use App\Services\ExternalResource\GeneralResourceCurator;
use App\Services\ExternalResource\KhanAcademyCurator;
use App\Services\ExternalResource\SourceManager;
use App\Services\ExternalResource\W3SchoolsCurator;
use Illuminate\Console\Command;

class CurateExternalResources extends Command
{
    protected $signature = 'resources:curate
                            {--source=all : Source to curate (w3schools, khan, all)}';

    protected $description = 'Curate external educational resources';

    public function handle(SourceManager $sourceManager, GeneralResourceCurator $curator)
    {
        $this->info('🎯 Starting external resource curation...');

        // Initialize sources if needed
        if (EducationalResource::count() === 0) {
            $this->info('Initializing educational sources...');
            $sourceManager->initializeSources();
            $this->info('✅ Sources initialized');
        }

        $source = $this->option('source');

        switch ($source) {
            case 'w3schools':
                $this->info('Curating W3Schools resources...');
                $count = app(W3SchoolsCurator::class)->curateTutorials();
                $this->info("✅ Added {$count} W3Schools resources");
                break;

            case 'khan':
                $this->info('Curating Khan Academy resources...');
                $count = app(KhanAcademyCurator::class)->curateTopics();
                $this->info("✅ Added {$count} Khan Academy resources");
                break;

            case 'all':
            default:
                $this->info('Curating all resources...');
                $results = $curator->curateAll();

                foreach ($results as $source => $count) {
                    $this->line("  - {$source}: {$count} resources");
                }
                break;
        }

        $this->info('✨ Curation complete!');

        // Show stats
        $total = CuratedResource::count();
        $bySource = CuratedResource::with('source')->get()->groupBy('source.name')->map->count();

        $this->table(['Source', 'Count'], $bySource->map(fn($count, $source) => [$source, $count])->values()->toArray());

        $this->info("Total curated resources: {$total}");

        return Command::SUCCESS;
    }
}
