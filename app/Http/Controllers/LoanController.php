<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\Loan;
use App\Models\LoanPayment;
use App\Models\Transaction;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class LoanController extends Controller
{
    public function index(Request $request): View
    {
        $loans = $request->user()->loans()->with('account')->orderBy('created_at', 'desc')->get();
        $totalBorrow = $loans->where('type', 'borrow')->sum('remaining_amount');
        $totalLend = $loans->where('type', 'lend')->sum('remaining_amount');
        $activeCount = $loans->where('status', 'active')->count();
        return view('loans.index', compact('loans', 'totalBorrow', 'totalLend', 'activeCount'));
    }

    public function create(): View
    {
        $accounts = request()->user()->accounts()->orderBy('name')->get(['id', 'name']);
        return view('loans.create', compact('accounts'));
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'type' => ['required', 'in:borrow,lend'],
            'lender_name' => ['nullable', 'string', 'max:255'],
            'amount' => ['required', 'numeric', 'min:0'],
            'interest_rate' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'account_id' => ['nullable', 'exists:accounts,id'],
            'start_date' => ['required', 'date'],
            'due_date' => ['nullable', 'date', 'after_or_equal:start_date'],
            'notes' => ['nullable', 'string'],
        ]);

        $data = $request->all();
        $data['user_id'] = $request->user()->id;
        $data['interest_rate'] = $request->interest_rate ?? 0;
        $data['paid_amount'] = 0;
        $data['remaining_amount'] = $request->amount;

        Loan::create($data);

        return redirect()->route('loans.index')->with('success', 'Loan created successfully.');
    }

    public function show(Loan $loan): View
    {
        $this->authorize('view', $loan);
        $loan->load('payments.account', 'account');
        return view('loans.show', compact('loan'));
    }

    public function edit(Loan $loan): View
    {
        $this->authorize('update', $loan);
        $accounts = request()->user()->accounts()->orderBy('name')->get(['id', 'name']);
        return view('loans.edit', compact('loan', 'accounts'));
    }

    public function update(Request $request, Loan $loan): RedirectResponse
    {
        $this->authorize('update', $loan);

        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'type' => ['required', 'in:borrow,lend'],
            'lender_name' => ['nullable', 'string', 'max:255'],
            'amount' => ['required', 'numeric', 'min:0'],
            'interest_rate' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'account_id' => ['nullable', 'exists:accounts,id'],
            'start_date' => ['required', 'date'],
            'due_date' => ['nullable', 'date', 'after_or_equal:start_date'],
            'notes' => ['nullable', 'string'],
            'status' => ['required', 'in:active,completed,defaulted'],
        ]);

        $data = $request->all();
        $data['interest_rate'] = $request->interest_rate ?? 0;
        $data['remaining_amount'] = $request->amount - $loan->paid_amount;

        $loan->update($data);

        return redirect()->route('loans.index')->with('success', 'Loan updated successfully.');
    }

    public function destroy(Loan $loan): RedirectResponse
    {
        $this->authorize('delete', $loan);
        $loan->delete();
        return redirect()->route('loans.index')->with('success', 'Loan deleted successfully.');
    }

    public function payment(Request $request, Loan $loan): RedirectResponse
    {
        $this->authorize('update', $loan);

        $request->validate([
            'amount' => ['required', 'numeric', 'min:0.01', 'max:' . $loan->remaining_amount],
            'account_id' => ['nullable', 'exists:accounts,id'],
            'payment_date' => ['required', 'date'],
            'notes' => ['nullable', 'string'],
        ]);

        $payment = LoanPayment::create([
            'loan_id' => $loan->id,
            'account_id' => $request->account_id,
            'amount' => $request->amount,
            'payment_date' => $request->payment_date,
            'notes' => $request->notes,
        ]);

        $loan->paid_amount += $request->amount;
        $loan->remaining_amount -= $request->amount;

        if ($loan->remaining_amount <= 0) {
            $loan->status = 'completed';
            $loan->remaining_amount = 0;
        }

        $loan->save();

        return redirect()->route('loans.show', $loan)->with('success', 'Payment recorded successfully.');
    }

    public function destroyPayment(Loan $loan, LoanPayment $loanPayment): RedirectResponse
    {
        $this->authorize('update', $loan);

        $loan->paid_amount -= $loanPayment->amount;
        $loan->remaining_amount += $loanPayment->amount;
        $loan->status = 'active';
        $loan->save();

        $loanPayment->delete();

        return redirect()->route('loans.show', $loan)->with('success', 'Payment deleted successfully.');
    }
}
