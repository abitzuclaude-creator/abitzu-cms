<?php

namespace Tests\Feature;

use App\Models\BankAccount;
use App\Models\Client;
use App\Models\ProformaInvoice;
use App\Models\User;
use App\Services\PaymentService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PaymentTest extends TestCase
{
    use RefreshDatabase;

    private function setup_pi(float $total, float $balance): ProformaInvoice
    {
        $client = Client::create([
            'business_name' => 'Pay Salon', 'business_id' => 'BSN-5001',
            'invoice_type' => 'ho_level', 'invoice_name' => 'Pay Salon Pvt Ltd',
            'gstin_status' => 'valid',
        ]);
        return ProformaInvoice::create([
            'client_id' => $client->id, 'pi_number' => 501,
            'pi_date' => '2026-05-01', 'due_date' => '2026-05-15',
            'billing_cycle' => 'yearly', 'usage_period_start' => '2026-05-01',
            'usage_period_end' => '2027-04-30', 'invoice_name' => 'Pay Salon Pvt Ltd',
            'subtotal' => $total, 'tax_type' => 'igst', 'tax_rate' => 0,
            'tax_amount' => 0, 'grand_total' => $total, 'balance_due' => $balance,
            'status' => 'unpaid', 'collection_stage' => 'new',
        ]);
    }

    public function test_partial_payment_sets_status_partially_paid(): void
    {
        $bank = BankAccount::create(['label' => 'Test', 'bank_name' => 'Test Bank', 'account_number_last4' => '1234']);
        $user = User::factory()->create(['role' => 'agent']);
        $pi = $this->setup_pi(10000, 10000);

        app(PaymentService::class)->record($pi, [
            'amount' => 4000, 'payment_date' => '2026-05-20',
            'mode' => 'neft', 'bank_account_id' => $bank->id,
        ], $user->id);

        $fresh = $pi->fresh();
        $this->assertEquals(6000, (float) $fresh->balance_due);
        $this->assertEquals('partially_paid', $fresh->status);
        $this->assertEquals('partial', $fresh->collection_stage);
    }

    public function test_full_payment_sets_status_paid(): void
    {
        $bank = BankAccount::create(['label' => 'Test', 'bank_name' => 'Test Bank', 'account_number_last4' => '1234']);
        $user = User::factory()->create(['role' => 'agent']);
        $pi = $this->setup_pi(10000, 10000);

        app(PaymentService::class)->record($pi, [
            'amount' => 10000, 'payment_date' => '2026-05-20',
            'mode' => 'neft', 'bank_account_id' => $bank->id,
        ], $user->id);

        $fresh = $pi->fresh();
        $this->assertEquals(0, (float) $fresh->balance_due);
        $this->assertEquals('paid', $fresh->status);
        $this->assertEquals('paid', $fresh->collection_stage);
    }

    public function test_overpayment_is_rejected(): void
    {
        $bank = BankAccount::create(['label' => 'Test', 'bank_name' => 'Test Bank', 'account_number_last4' => '1234']);
        $user = User::factory()->create(['role' => 'agent']);
        $pi = $this->setup_pi(10000, 5000);

        $this->expectException(\InvalidArgumentException::class);
        app(PaymentService::class)->record($pi, [
            'amount' => 6000, 'payment_date' => '2026-05-20',
            'mode' => 'neft', 'bank_account_id' => $bank->id,
        ], $user->id);
    }

    public function test_payment_api_endpoint_records_and_returns_balance(): void
    {
        $bank = BankAccount::create(['label' => 'Test', 'bank_name' => 'Test Bank', 'account_number_last4' => '1234']);
        $user = User::factory()->create(['role' => 'owner']);
        $pi = $this->setup_pi(10000, 10000);

        $this->actingAs($user)->postJson('/api/payments', [
            'proforma_invoice_id' => $pi->id, 'amount' => 3000,
            'payment_date' => '2026-05-20', 'mode' => 'upi', 'bank_account_id' => $bank->id,
        ])->assertOk()->assertJson(['ok' => true, 'new_status' => 'partially_paid']);

        $this->assertDatabaseHas('payments', ['proforma_invoice_id' => $pi->id, 'amount' => 3000]);
    }
}
