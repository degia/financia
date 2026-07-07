<?php

namespace App\Models;

use Database\Factories\InstitutionFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Institution extends Model
{
    /** @use HasFactory<InstitutionFactory> */
    use HasFactory;

    protected $fillable = [
        'name',
        'type',
        'color',
        'slug',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    public function accounts()
    {
        return $this->hasMany(Account::class, 'institution_id');
    }

    public function getLogoUrlAttribute(): ?string
    {
        return null;
    }
}
