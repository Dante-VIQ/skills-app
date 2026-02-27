<?php
// app/Console/Commands/TrainRecommender.php

namespace App\Console\Commands;

use App\Models\LearningResource;
use App\Models\UserInteraction;
use App\Models\ContentCategory;
use Illuminate\Console\Command;
use Symfony\Component\Process\Process;

class TrainRecommender extends Command
{
    protected $signature = 'recommender:train 
                            {--force : Force training even if not needed}
                            {--output= : Output model path}';
    
    protected $description = 'Train the scikit-learn recommendation model';
    
    public function handle()
    {
        $this->info('🚀 Starting recommendation model training...');
        
        // 1. Gather content data
        $this->info('📚 Gathering content data...');
        $contentData = $this->getContentData();
        
        // 2. Gather interaction data
        $this->info('👥 Gathering interaction data...');
        $interactionData = $this->getInteractionData();
        
        // 3. Prepare input for Python
        $inputData = [
            'action' => 'train',
            'content_data' => $contentData,
            'interaction_data' => $interactionData,
            'output_path' => $this->option('output') ?? storage_path('app/models/skilldna_recommender.pkl')
        ];
        
        $inputFile = storage_path('app/temp/train_input_' . time() . '.json');
        file_put_contents($inputFile, json_encode($inputData));
        
        // 4. Run Python script
        $this->info('🐍 Running Python scikit-learn model...');
        
        $process = new Process([
            'python3',
            base_path('app/Services/Python/skilldna_recommender.py'),
            $inputFile
        ]);
        
        $process->setTimeout(300); // 5 minutes
        
        $process->run(function ($type, $buffer) {
            $this->output->write($buffer);
        });
        
        // 5. Clean up
        unlink($inputFile);
        
        if ($process->isSuccessful()) {
            $this->info('✅ Model trained successfully!');
            return Command::SUCCESS;
        } else {
            $this->error('❌ Training failed: ' . $process->getErrorOutput());
            return Command::FAILURE;
        }
    }
    
    protected function getContentData()
    {
        return LearningResource::with('category')
            ->where('is_published', true)
            ->get()
            ->map(function ($resource) {
                return [
                    'id' => $resource->id,
                    'title' => $resource->title,
                    'description' => $resource->description,
                    'category_name' => $resource->category->name ?? 'Uncategorized',
                    'tags' => $resource->tags ?? [],
                    'difficulty' => $resource->difficulty_level,
                    'content_type' => $resource->content_type,
                    'created_at' => $resource->created_at->timestamp
                ];
            })
            ->toArray();
    }
    
    protected function getInteractionData()
    {
        return UserInteraction::with('resource')
            ->where('created_at', '>=', now()->subDays(90))
            ->limit(10000)
            ->get()
            ->map(function ($interaction) {
                return [
                    'user_id' => $interaction->user_id,
                    'content_id' => $interaction->learning_resource_id,
                    'type' => $interaction->interaction_type,
                    'duration' => $interaction->duration,
                    'rating' => $interaction->rating,
                    'progress' => $interaction->progress_percentage,
                    'completed' => $interaction->completed,
                    'timestamp' => $interaction->created_at->timestamp
                ];
            })
            ->toArray();
    }
}