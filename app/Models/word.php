<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class word extends Model
{
    use HasFactory;
    protected $guarded = ['id'];
    protected $casts = [
        'remember' => 'boolean',
    ];

    public function user(): BelongsTo
        {
            return $this->belongsTo(User::class);
        }
}
