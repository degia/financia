<?php

namespace App\Providers;

use App\Models\Account;
use App\Models\Budget;
use App\Models\Category;
use App\Models\Goal;
use App\Models\Loan;
use App\Models\Transaction;
use App\Policies\AccountPolicy;
use App\Policies\BudgetPolicy;
use App\Policies\CategoryPolicy;
use App\Policies\GoalPolicy;
use App\Policies\LoanPolicy;
use App\Policies\TransactionPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    protected $policies = [
        Account::class => AccountPolicy::class,
        Category::class => CategoryPolicy::class,
        Budget::class => BudgetPolicy::class,
        Transaction::class => TransactionPolicy::class,
        Goal::class => GoalPolicy::class,
        Loan::class => LoanPolicy::class,
    ];

    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        $this->registerPolicies();
    }
}
