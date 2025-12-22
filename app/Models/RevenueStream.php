<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RevenueStream extends Model
{
    use HasFactory;

    protected $fillable = [
        'source', 'amount', 'campaign_name', 'is_recurring'
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'is_recurring' => 'boolean',
    ];
}
