<?php

namespace App\Services;

class ContentAttributionService
{
    /**
     * Generate attribution text based on source
     */
    public function getAttribution($resource)
    {
        $attributions = [
            'youtube' => "Content courtesy of {$resource->source_metadata['channel']} on YouTube. 
                          Watch the original video <a href='{$resource->external_url}' target='_blank'>here</a>.",
            
            'khan_academy' => "This material is adapted from Khan Academy's {$resource->title} course. 
                               Visit <a href='{$resource->external_url}' target='_blank'>Khan Academy</a> for the original.",
            
            'w3schools' => "Reference content from W3Schools. 
                           Learn more at <a href='{$resource->external_url}' target='_blank'>W3Schools.com</a>.",
            
            'original' => "Original SkillDNA content created by our expert educators."
        ];
        
        return $attributions[$resource->source_type] ?? "Source: {$resource->attribution}";
    }
    
    /**
     * Check if content usage complies with terms
     */
    public function validateUsage($resource)
    {
        $restrictedDomains = [
            'youtube.com' => [
                'allow_embed' => true,
                'require_attribution' => true,
                'max_excerpt_length' => null // Can embed full video
            ],
            'khanacademy.org' => [
                'allow_embed' => false,
                'require_attribution' => true,
                'max_excerpt_length' => 500 // characters
            ],
            'w3schools.com' => [
                'allow_embed' => false,
                'require_attribution' => true,
                'max_excerpt_length' => 300
            ]
        ];
        
        $domain = parse_url($resource->external_url, PHP_URL_HOST);
        $rules = $restrictedDomains[$domain] ?? ['allow_embed' => false, 'require_attribution' => true, 'max_excerpt_length' => 200];
        
        return [
            'can_embed' => $rules['allow_embed'],
            'must_credit' => $rules['require_attribution'],
            'excerpt_limit' => $rules['max_excerpt_length']
        ];
    }
}