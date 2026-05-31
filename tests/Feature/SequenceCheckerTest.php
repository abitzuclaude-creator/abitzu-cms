<?php

namespace Tests\Feature;

use App\Models\Alert;
use App\Models\Client;
use App\Models\ProformaInvoice;
use App\Services\SequenceCheckerService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SequenceCheckerTest extends TestCase
{
    use RefreshDatabase;

    private function pi(int $number): void
    {
        $client = Client::create([
            'business_name' => "Salon $number", 'business_id' => "BSN-$number",
            'invoice_type' => 'ho_level', 'invoice_name' => "Salon $number Pvt Ltd",
            'gstin_status' => 'valid',
        ]);
        ProformaInvoice::create([
            'client_id' => $client->id, 'pi_number' => $number,
            'pi_date' => '2026-05-01', 'due_date' => '2026-05-15',
            'billing_cycle' => 'yearly', 'usage_period_start' => '2026-05-01',
            'usage_period_end' => '2027-04-30', 'invoice_name' => "Salon $number Pvt Ltd",
            'subtotal' => 1000, 'tax_type' => 'igst', 'tax_rate' => 0,
            'tax_amount' => 0, 'grand_total' => 1000, 'balance_due' => 1000,
            'status' => 'unpaid', 'collection_stage' => 'new',
        ]);
    }

    public function test_detects_single_gap(): void
    {
        $this->pi(146);
        $this->pi(148); // 147 missing

        $created = app(SequenceCheckerService::class)->run();

        $this->assertEquals(1, $created);
        $this->assertDatabaseHas('alerts', [
            'type' => 'missing_pi_sequence',
            'description' => 'PI #147 missing between #146 and #148',
        ]);
    }

    public function test_detects_multiple_missing_in_range(): void
    {
        $this->pi(100);
        $this->pi(104); // 101, 102, 103 missing

        $created = app(SequenceCheckerService::class)->run();
        $this->assertEquals(3, $created);
    }

    public function test_no_gap_creates_no_alert(): void
    {
        $this->pi(200);
        $this->pi(201);
        $this->pi(202);

        $created = app(SequenceCheckerService::class)->run();
        $this->assertEquals(0, $created);
        $this->assertEquals(0, Alert::count());
    }

    public function test_does_not_duplicate_existing_alert(): void
    {
        $this->pi(300);
        $this->pi(302); // 301 missing

        app(SequenceCheckerService::class)->run();
        app(SequenceCheckerService::class)->run(); // run twice

        $this->assertEquals(1, Alert::where('description', 'like', '%#301%')->count());
    }
}
