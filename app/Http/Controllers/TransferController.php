<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Services\TransferService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TransferController extends Controller
{
    public function __construct(
        protected TransferService $transferService
    ) {}

    public function create(Request $request): View
    {
        $accounts = Account::where('user_id', $request->user()->id)->orderBy('name')->get();
        return view('transfers.create', compact('accounts'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'from_account_id' => ['required', 'exists:accounts,id'],
            'to_account_id' => ['required', 'exists:accounts,id', 'different:from_account_id'],
            'amount' => ['required', 'numeric', 'min:0.01'],
            'description' => ['nullable', 'string', 'max:255'],
            'date' => ['nullable', 'date'],
            'is_savings' => ['nullable', 'boolean'],
        ]);

        $data['is_savings'] = $request->boolean('is_savings');

        $this->transferService->transfer($request->user(), $data);

        return redirect()->route('transactions.index')
            ->with('success', 'Transfer completed successfully.');
    }
}
