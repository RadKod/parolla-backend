<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CustomQuestion extends Model
{
    use HasFactory;
    protected $table = 'custom_questions';
    protected $fillable = [
        'created_at', 'updated_at', 'room', 'title', 'is_public', 'qa_list', 'view_count', 'lang',
        'is_anon', 'fingerprint'
    ];
    protected $casts = [
        'is_public' => 'boolean',
        'qa_list' => 'array'
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'fingerprint', 'fingerprint');
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class, 'room_id');
    }
}
