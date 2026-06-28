<?php

namespace App\Models;

use Database\Factories\LoanFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Loan extends Model
{
    /** @use HasFactory<LoanFactory> */
    use HasFactory;

    protected $fillable = [
        'user_id',
        'account_id',
        'name',
        'type',
        'lender_name',
        'amount',
        'interest_rate',
        'paid_amount',
        'remaining_amount',
        'start_date',
        'due_date',
        'notes',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'interest_rate' => 'decimal:2',
            'paid_amount' => 'decimal:2',
            'remaining_amount' => 'decimal:2',
            'start_date' => 'date',
            'due_date' => 'date',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function account()
    {
        return $this->belongsTo(Account::class);
    }

    public function payments()
    {
        return $this->hasMany(LoanPayment::class);
    }
}
