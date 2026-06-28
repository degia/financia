<?php

namespace App\Models;

use Database\Factories\TransactionFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    /** @use HasFactory<TransactionFactory> */
    use HasFactory;

    protected $fillable = [
        'user_id',
        'account_id',
        'category_id',
        'sub_category_id',
        'transfer_id',
        'loan_id',
        'is_savings',
        'amount',
        'type',
        'description',
        'date',
        'is_recurring',
        'recurring_interval',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'date' => 'date',
            'is_recurring' => 'boolean',
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

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function subCategory()
    {
        return $this->belongsTo(SubCategory::class);
    }

    public function transfer()
    {
        return $this->belongsTo(Transaction::class, 'transfer_id');
    }

    public function transfers()
    {
        return $this->hasMany(Transaction::class, 'transfer_id');
    }

    public function loan()
    {
        return $this->belongsTo(Loan::class);
    }

    public function loanPayment()
    {
        return $this->hasOne(LoanPayment::class, 'transaction_id');
    }
}
