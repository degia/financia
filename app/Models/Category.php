<?php

namespace App\Models;

use Database\Factories\CategoryFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    /** @use HasFactory<CategoryFactory> */
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'type',
        'icon',
        'color',
        'is_system',
    ];

    public function scopeForUser($query, $userId)
    {
        return $query->whereNull('user_id')->orWhere('user_id', $userId);
    }

    protected function casts(): array
    {
        return [
            'is_system' => 'boolean',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }

    public function budgets()
    {
        return $this->hasMany(Budget::class);
    }

    public function subCategories()
    {
        return $this->hasMany(SubCategory::class)->orderBy('name');
    }
}
