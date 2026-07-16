<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Expense extends Model
{
    use HasFactory;

    public const CATEGORY_COFFEE = 'coffee';
    public const CATEGORY_CLOTHES = 'clothes';
    public const CATEGORY_OTHER = 'other';

    protected $fillable = [
        'user_id',
        'category',
        'amount',
        'date',
        'description',
    ];

    protected $casts = [
        'date' => 'date',
        'amount' => 'decimal:2',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function scopeCategory(Builder $query, string $category): Builder
    {
        return $query->where('category', $category);
    }

    public function scopeThisMonth(Builder $query): Builder
    {
        return $query->whereMonth('date', now()->month)
            ->whereYear('date', now()->year);
    }
}

