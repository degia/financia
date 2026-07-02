<?php

namespace App\Http\Controllers;

use App\Models\Account;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SettingsController extends Controller
{
    public function index(): View
    {
        $accounts = request()->user()->accounts()->orderBy('name')->get(['id', 'name']);
        $menus = [
            'dashboard' => 'Dashboard',
            'accounts' => 'Accounts',
            'transactions' => 'Transactions',
            'transfers' => 'Transfer',
            'categories' => 'Categories',
            'budgets' => 'Budgets',
            'goals' => 'Goals',
            'loans' => 'Loans',
            'reports' => 'Reports',
        ];
        return view('settings.index', compact('accounts', 'menus'));
    }

    public function update(Request $request): RedirectResponse
    {
        $user = $request->user();

        $request->validate([
            'currency_preference' => ['required', 'string', 'size:3'],
            'menu_visibility' => ['nullable', 'array'],
            'menu_visibility.*' => ['string', 'in:dashboard,accounts,transactions,transfers,categories,budgets,goals,loans,reports'],
            'default_account_id' => ['nullable', 'exists:accounts,id', function ($attr, $val, $fail) use ($user) {
                if ($val && !$user->accounts()->where('id', $val)->exists()) {
                    $fail('The selected account is invalid.');
                }
            }],
            'default_expense_category_id' => ['nullable', 'exists:categories,id', function ($attr, $val, $fail) use ($user) {
                if ($val && !$user->categories()->where('id', $val)->exists()) {
                    $fail('The selected category is invalid.');
                }
            }],
            'default_income_category_id' => ['nullable', 'exists:categories,id', function ($attr, $val, $fail) use ($user) {
                if ($val && !$user->categories()->where('id', $val)->exists()) {
                    $fail('The selected category is invalid.');
                }
            }],
            'fonnte_token' => ['nullable', 'string', 'max:255'],
            'whatsapp_target' => ['nullable', 'string', 'max:20', 'regex:/^62\d{8,15}$/'],
        ]);

        $user->currency_preference = $request->currency_preference;

        $allMenus = ['dashboard', 'accounts', 'transactions', 'transfers', 'categories', 'budgets', 'goals', 'loans', 'reports'];
        $visible = $request->menu_visibility ?? [];
        $user->setPreference('menu_visibility', array_combine(
            $allMenus,
            array_map(fn($m) => in_array($m, $visible), $allMenus)
        ));
        $user->setPreference('default_account_id', $request->default_account_id);
        $user->setPreference('default_expense_category_id', $request->default_expense_category_id);
        $user->setPreference('default_income_category_id', $request->default_income_category_id);
        $user->setPreference('fonnte_token', $request->fonnte_token);
        $user->setPreference('whatsapp_target', $request->whatsapp_target);

        $user->save();

        return redirect()->route('settings.index')->with('success', 'Settings updated successfully.');
    }
}
