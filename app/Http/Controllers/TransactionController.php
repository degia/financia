<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTransactionRequest;
use App\Http\Requests\UpdateTransactionRequest;
use App\Models\Transaction;
use App\Services\TransactionService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TransactionController extends Controller
{
    public function __construct(
        protected TransactionService $transactionService
    ) {}

    public function index(Request $request): View
    {
        $query = $request->user()->transactions()
            ->with(['account', 'category', 'subCategory'])
            ->orderBy('date', 'desc')
            ->orderBy('created_at', 'desc');

        if ($request->filled('start_date')) {
            $query->where('date', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $query->where('date', '<=', $request->end_date);
        }
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }
        if ($request->filled('account_id')) {
            $query->where('account_id', $request->account_id);
        }
        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }
        if ($request->filled('search')) {
            $query->where('description', 'like', '%' . $request->search . '%');
        }
        if ($request->filled('is_recurring')) {
            $query->where('is_recurring', true);
        }

        $transactions = $query->paginate(15);
        $accounts = $request->user()->accounts;
        $categories = $request->user()->categories()->with('subCategories')->get();

        return view('transactions.index', compact('transactions', 'accounts', 'categories'));
    }

    public function create(Request $request): View
    {
        $accounts = $request->user()->accounts;
        $categories = $request->user()->categories()->with('subCategories')->orderBy('type')->orderBy('name')->get();
        return view('transactions.create', compact('accounts', 'categories'));
    }

    public function store(StoreTransactionRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $data['user_id'] = $request->user()->id;
        $data['is_recurring'] = $request->boolean('is_recurring');
        if (!$data['is_recurring']) {
            $data['recurring_interval'] = null;
        }

        $this->transactionService->createTransaction($data);

        return redirect()->route('transactions.index')->with('success', 'Transaction created successfully.');
    }

    public function edit(Request $request, Transaction $transaction): View
    {
        $this->authorize('update', $transaction);
        $accounts = $request->user()->accounts;
        $categories = $request->user()->categories()->with('subCategories')->orderBy('type')->orderBy('name')->get();
        return view('transactions.edit', compact('transaction', 'accounts', 'categories'));
    }

    public function update(UpdateTransactionRequest $request, Transaction $transaction): RedirectResponse
    {
        $this->authorize('update', $transaction);
        $this->transactionService->updateTransaction($transaction, $request->validated());
        return redirect()->route('transactions.index')->with('success', 'Transaction updated successfully.');
    }

    public function destroy(Transaction $transaction): RedirectResponse
    {
        $this->authorize('delete', $transaction);
        $this->transactionService->deleteTransaction($transaction);
        return redirect()->route('transactions.index')->with('success', 'Transaction deleted successfully.');
    }
}
