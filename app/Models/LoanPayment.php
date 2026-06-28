<?php

namespace App\Models;

use Database\Factories\LoanPaymentFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LoanPayment extends Model
{
    /** @use HasFactory<LoanPaymentFactory> */
    use HasFactory;

    protected $fillable = [
        'loan_id',
        'account_id',
        'transaction_id',
        'amount',
        'payment_date',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'payment_date' => 'date',
        ];
    }

    public function loan()
    {
        return $this->belongsTo(Loan::class);
    }

    public function account()
    {
        return $this->belongsTo(Account::class);
    }

    public function transaction()
    {
        return $this->belongsTo(Transaction::class);
    }
}
