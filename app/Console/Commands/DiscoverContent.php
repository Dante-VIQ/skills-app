<?php
// app/Console/Commands/DiscoverContent.php

namespace App\Console\Commands;

use App\Models\User;
use App\Models\UserInterest;
use App\Services\Discovery\AutomatedContentDiscovery;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class DiscoverContent extends Command
{
    protected $signature = 'content:discover 
                            {--user= : Discover for specific user}
                            {--batch=50 : Number of users per batch}';
    
    protected $description = 'Automatically discover content for users based on interests';

    protected $discoveryService;

    public function __construct(AutomatedContentDiscovery $discoveryService)
    {
        parent::__construct();
        $this->discoveryService = $discoveryService;
    }

    public function handle()
    {
        $startTime = microtime(true);
        
        if ($userId = $this->option('user')) {
            // Discover for single user
            $this->discoverForUser($userId);
        } else {
            // Discover for all users with stale interests
            $this->discoverForAllUsers();
        }
        
        $duration = round(microtime(true) - $startTime, 2);
        $this->info("Content discovery completed in {$duration} seconds");
        
        return Command::SUCCESS;
    }

    protected function discoverForUser($userId)
    {
        $user = User::find($userId);
        
        if (!$user) {
            $this->error("User {$userId} not found");
            return;
        }
        
        $this->info("Discovering content for user: {$user->name}");
        
        $results = $this->discoveryService->discoverForUser($user, 50);
        
        $this->info("Found " . $results->count() . " new content items");
        
        Log::info('Content discovery completed for user', [
            'user_id' => $userId,
            'items_found' => $results->count()
        ]);
    }

    protected function discoverForAllUsers()
    {
        $batchSize = $this->option('batch');
        
        // Get users with interests that need updating
        $users = User::whereHas('interests', function ($query) {
            $query->needsDiscovery();
        })->limit($batchSize)->get();
        
        $this->info("Discovering content for {$users->count()} users");
        
        $bar = $this->output->createProgressBar($users->count());
        $bar->start();
        
        foreach ($users as $user) {
            try {
                $this->discoveryService->discoverForUser($user, 30);
            } catch (\Exception $e) {
                Log::error('Content discovery failed for user', [
                    'user_id' => $user->id,
                    'error' => $e->getMessage()
                ]);
            }
            
            $bar->advance();
        }
        
        $bar->finish();
        $this->newLine();
    }
}