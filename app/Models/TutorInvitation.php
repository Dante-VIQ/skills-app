<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TutorInvitation extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'email',
        'name',
        'notes',
        'created_by',
        'used',
        'used_by',
        'used_at',
        'expires_at',
    ];

    protected $casts = [
        'used' => 'boolean',
        'expires_at' => 'datetime',
        'used_at' => 'datetime',
    ];

    // Relationships
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'used_by');
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('used', false)
                    ->where(function ($q) {
                        $q->whereNull('expires_at')
                          ->orWhere('expires_at', '>', now());
                    });
    }

    public function scopeExpired($query)
    {
        return $query->where(function ($q) {
            $q->whereNotNull('expires_at')
              ->where('expires_at', '<=', now());
        });
    }

    public function scopeUsed($query)
    {
        return $query->where('used', true);
    }

    // Methods
    public function isValid()
    {
        return !$this->used && (!$this->expires_at || $this->expires_at > now());
    }

    public function markAsUsed($userId)
    {
        $this->update([
            'used' => true,
            'used_by' => $userId,
            'used_at' => now(),
        ]);
    }

    public static function generateCode()
    {
        do {
            $code = strtoupper(substr(md5(uniqid()), 0, 12));
        } while (self::where('code', $code)->exists());

        return $code;
    }
}