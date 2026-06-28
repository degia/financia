<?php

namespace App\Models;

use Database\Factories\SubCategoryFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SubCategory extends Model
{
    /** @use HasFactory<SubCategoryFactory> */
    use HasFactory;

    protected $fillable = [
        'category_id',
        'name',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }
}
