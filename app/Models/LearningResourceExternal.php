<?php
// app/Models/LearningResourceExternal.php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class LearningResourceExternal extends Pivot
{
    protected $table = 'learning_resource_external';

    protected $fillable = [
        'learning_resource_id',
        'external_resource_id',
        'relationship_type',
        'display_order',
    ];

    protected $casts = [
        'display_order' => 'integer',
    ];

    /**
     * Get the learning resource
     */
    public function learningResource()
    {
        return $this->belongsTo(LearningResource::class);
    }

    /**
     * Get the external resource
     */
    public function externalResource()
    {
        return $this->belongsTo(CuratedResource::class, 'external_resource_id');
    }

    /**
     * Scope for primary resources
     */
    public function scopePrimary($query)
    {
        return $query->where('relationship_type', 'primary');
    }

    /**
     * Scope for supplemental resources
     */
    public function scopeSupplemental($query)
    {
        return $query->where('relationship_type', 'supplemental');
    }

    /**
     * Scope for prerequisites
     */
    public function scopePrerequisite($query)
    {
        return $query->where('relationship_type', 'prerequisite');
    }
}