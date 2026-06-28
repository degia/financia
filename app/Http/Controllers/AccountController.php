<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreAccountRequest;
use App\Http\Requests\UpdateAccountRequest;
use App\Models\Account;
use App\Services\AccountService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AccountController extends Controller
{
    public function __construct(
        protected AccountService $accountService
    ) {}

    public function index(Request $request): View
    {
        $accounts = $request->user()->accounts()->orderBy('created_at', 'desc')->get();
        return view('accounts.index', compact('accounts'));
    }

    public function create(): View
    {
        return view('accounts.create');
    }

    public function store(StoreAccountRequest $request): RedirectResponse
    {
        $this->accountService->createAccount([
            'user_id' => $request->user()->id,
            'name' => $request->name,
            'type' => $request->type,
            'initial_balance' => $request->initial_balance ?? 0,
            'currency' => $request->currency ?? $request->user()->currency_preference,
            'color' => $request->color ?? '#6366F1',
        ]);

        return redirect()->route('accounts.index')->with('success', 'Account created successfully.');
    }

    public function edit(Account $account): View
    {
        $this->authorize('update', $account);
        return view('accounts.edit', compact('account'));
    }

    public function update(UpdateAccountRequest $request, Account $account): RedirectResponse
    {
        $this->authorize('update', $account);
        $this->accountService->updateAccount($account, $request->validated());
        return redirect()->route('accounts.index')->with('success', 'Account updated successfully.');
    }

    public function destroy(Account $account): RedirectResponse
    {
        $this->authorize('delete', $account);
        $this->accountService->deleteAccount($account);
        return redirect()->route('accounts.index')->with('success', 'Account deleted successfully.');
    }
}
