<?php

namespace Tests\Feature;

use App\Models\Account;
use App\Models\Loan;
use App\Models\LoanPayment;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LoanTest extends TestCase
{
    use RefreshDatabase;

    public function test_index_page_is_displayed(): void
    {
        $user = User::factory()->create();
        Loan::factory()->for($user)->create();

        $response = $this->actingAs($user)->get(route('loans.index'));

        $response->assertOk();
    }

    public function test_create_page_is_displayed(): void
    {
        $user = User::factory()->create();
        Account::factory()->for($user)->create();

        $response = $this->actingAs($user)->get(route('loans.create'));

        $response->assertOk();
    }

    public function test_can_store_loan(): void
    {
        $user = User::factory()->create();
        $account = Account::factory()->for($user)->create();

        $response = $this->actingAs($user)->post(route('loans.store'), [
            'name' => 'Test Loan',
            'type' => 'borrow',
            'lender_name' => 'Bank',
            'amount' => 10000.00,
            'interest_rate' => 5.5,
            'account_id' => $account->id,
            'start_date' => '2025-01-01',
            'due_date' => '2026-01-01',
            'notes' => 'Test notes',
        ]);

        $response->assertSessionHasNoErrors()->assertRedirect(route('loans.index'));

        $this->assertDatabaseHas('loans', [
            'user_id' => $user->id,
            'name' => 'Test Loan',
            'type' => 'borrow',
            'amount' => 10000.00,
            'remaining_amount' => 10000.00,
        ]);
    }

    public function test_store_validates_required_fields(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post(route('loans.store'), []);

        $response->assertSessionHasErrors(['name', 'type', 'amount', 'start_date']);
    }

    public function test_show_page_is_displayed(): void
    {
        $user = User::factory()->create();
        $loan = Loan::factory()->for($user)->create();

        $response = $this->actingAs($user)->get(route('loans.show', $loan));

        $response->assertOk();
    }

    public function test_show_page_returns_403_for_another_user(): void
    {
        $user = User::factory()->create();
        $other = User::factory()->create();
        $loan = Loan::factory()->for($other)->create();

        $response = $this->actingAs($user)->get(route('loans.show', $loan));

        $response->assertForbidden();
    }

    public function test_edit_page_is_displayed(): void
    {
        $user = User::factory()->create();
        Account::factory()->for($user)->create();
        $loan = Loan::factory()->for($user)->create();

        $response = $this->actingAs($user)->get(route('loans.edit', $loan));

        $response->assertOk();
    }

    public function test_edit_page_returns_403_for_another_user(): void
    {
        $user = User::factory()->create();
        $other = User::factory()->create();
        $loan = Loan::factory()->for($other)->create();

        $response = $this->actingAs($user)->get(route('loans.edit', $loan));

        $response->assertForbidden();
    }

    public function test_can_update_loan(): void
    {
        $user = User::factory()->create();
        $account = Account::factory()->for($user)->create();
        $loan = Loan::factory()->for($user)->for($account)->create(['paid_amount' => 0]);

        $response = $this->actingAs($user)->patch(route('loans.update', $loan), [
            'name' => 'Updated Loan',
            'type' => 'lend',
            'lender_name' => 'New Bank',
            'amount' => 20000.00,
            'interest_rate' => 3.0,
            'account_id' => $account->id,
            'start_date' => '2025-03-01',
            'due_date' => '2027-03-01',
            'notes' => 'Updated notes',
            'status' => 'active',
        ]);

        $response->assertSessionHasNoErrors()->assertRedirect(route('loans.index'));

        $this->assertDatabaseHas('loans', [
            'id' => $loan->id,
            'name' => 'Updated Loan',
            'type' => 'lend',
            'amount' => 20000.00,
        ]);
    }

    public function test_update_returns_403_for_another_user(): void
    {
        $user = User::factory()->create();
        $other = User::factory()->create();
        $loan = Loan::factory()->for($other)->create();

        $response = $this->actingAs($user)->patch(route('loans.update', $loan), [
            'name' => 'Hacked',
            'type' => 'borrow',
            'amount' => 100,
            'start_date' => '2025-01-01',
            'status' => 'active',
        ]);

        $response->assertForbidden();
    }

    public function test_update_validates_required_fields(): void
    {
        $user = User::factory()->create();
        $loan = Loan::factory()->for($user)->create();

        $response = $this->actingAs($user)->patch(route('loans.update', $loan), []);

        $response->assertSessionHasErrors(['name', 'type', 'amount', 'start_date', 'status']);
    }

    public function test_can_destroy_loan(): void
    {
        $user = User::factory()->create();
        $loan = Loan::factory()->for($user)->create();

        $response = $this->actingAs($user)->delete(route('loans.destroy', $loan));

        $response->assertSessionHasNoErrors()->assertRedirect(route('loans.index'));

        $this->assertDatabaseMissing('loans', ['id' => $loan->id]);
    }

    public function test_destroy_returns_403_for_another_user(): void
    {
        $user = User::factory()->create();
        $other = User::factory()->create();
        $loan = Loan::factory()->for($other)->create();

        $response = $this->actingAs($user)->delete(route('loans.destroy', $loan));

        $response->assertForbidden();
    }

    public function test_can_record_payment(): void
    {
        $user = User::factory()->create();
        $account = Account::factory()->for($user)->create();
        $loan = Loan::factory()->for($user)->for($account)->create([
            'amount' => 10000,
            'paid_amount' => 0,
            'remaining_amount' => 10000,
            'status' => 'active',
        ]);

        $response = $this->actingAs($user)->post(route('loans.payment', $loan), [
            'amount' => 3000,
            'account_id' => $account->id,
            'payment_date' => '2025-06-15',
            'notes' => 'First payment',
        ]);

        $response->assertSessionHasNoErrors()->assertRedirect(route('loans.show', $loan));

        $this->assertDatabaseHas('loan_payments', [
            'loan_id' => $loan->id,
            'amount' => 3000,
        ]);

        $loan->refresh();
        $this->assertEquals(3000, $loan->paid_amount);
        $this->assertEquals(7000, $loan->remaining_amount);
    }

    public function test_payment_completes_loan_when_fully_paid(): void
    {
        $user = User::factory()->create();
        $account = Account::factory()->for($user)->create();
        $loan = Loan::factory()->for($user)->for($account)->create([
            'amount' => 5000,
            'paid_amount' => 0,
            'remaining_amount' => 5000,
            'status' => 'active',
        ]);

        $response = $this->actingAs($user)->post(route('loans.payment', $loan), [
            'amount' => 5000,
            'account_id' => $account->id,
            'payment_date' => '2025-06-15',
        ]);

        $response->assertSessionHasNoErrors();

        $loan->refresh();
        $this->assertEquals('completed', $loan->status);
        $this->assertEquals(0, $loan->remaining_amount);
    }

    public function test_payment_returns_403_for_another_user(): void
    {
        $user = User::factory()->create();
        $other = User::factory()->create();
        $loan = Loan::factory()->for($other)->create(['remaining_amount' => 1000]);

        $response = $this->actingAs($user)->post(route('loans.payment', $loan), [
            'amount' => 100,
            'payment_date' => '2025-01-01',
        ]);

        $response->assertForbidden();
    }

    public function test_payment_validates_required_fields(): void
    {
        $user = User::factory()->create();
        $loan = Loan::factory()->for($user)->create();

        $response = $this->actingAs($user)->post(route('loans.payment', $loan), []);

        $response->assertSessionHasErrors(['amount', 'payment_date']);
    }

    public function test_can_destroy_payment(): void
    {
        $user = User::factory()->create();
        $account = Account::factory()->for($user)->create();
        $loan = Loan::factory()->for($user)->for($account)->create([
            'amount' => 10000,
            'paid_amount' => 3000,
            'remaining_amount' => 7000,
            'status' => 'active',
        ]);
        $payment = \App\Models\LoanPayment::create([
            'loan_id' => $loan->id,
            'amount' => 3000,
            'payment_date' => '2025-06-15',
        ]);

        $response = $this->actingAs($user)->delete(route('loans.payment.destroy', ['loan' => $loan, 'loanPayment' => $payment]));

        $response->assertStatus(302);
        $this->assertDatabaseMissing('loan_payments', ['id' => $payment->id]);
    }

    public function test_destroy_payment_returns_403_for_another_user(): void
    {
        $user = User::factory()->create();
        $other = User::factory()->create();
        $loan = Loan::factory()->for($other)->create();
        $payment = \App\Models\LoanPayment::create([
            'loan_id' => $loan->id,
            'amount' => 1000,
            'payment_date' => '2025-01-01',
        ]);

        $response = $this->actingAs($user)->delete(route('loans.payment.destroy', ['loan' => $loan->id, 'loanPayment' => $payment->id]));

        $response->assertForbidden();
    }
}
