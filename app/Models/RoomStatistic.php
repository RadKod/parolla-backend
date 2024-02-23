<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RoomStatistic extends Model
{
    use HasFactory;

    protected $table = 'room_statistics';
    protected $fillable = [
        'fingerprint',
        'room_id',
        'game_result'
    ];

    protected $casts = [
        'game_result' => 'array'
    ];

    public function room(): BelongsTo
    {
        return $this->belongsTo(CustomQuestion::class, 'room_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'fingerprint', 'fingerprint');
    }
}
