<?php

namespace Tests\Feature;

use App\Models\Client;
use App\Models\ProformaInvoice;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CollectionsTest extends TestCase
{
    use RefreshDatabase;

    private function owner(): User
    {
        return User::factory()->create(['role' => 'owner', 'is_active' => true]);
    }

    private function makeInvoice(array $piOverrides = [], array $clientOverrides = []): ProformaInvoice
    {
        $client = Client::create(array_merge([
            'business_name' => 'Test Salon',
            'business_id'   => 'BSN-' . fake()->unique()->numberBetween(1000, 9999),
            'invoice_type'  => 'ho_level',
            'invoice_name'  => 'Test Salon Pvt Ltd',
            'gstin_status'  => 'valid',
            'owner_name'    => 'Test Owner',
            'owner_email'   => 'owner@test.in',
            'owner_phone1'  => '9876543210',
        ], $clientOverrides));

        return ProformaInvoice::create(array_merge([
            'client_id'          => $client->id,
            'pi_number'          => fake()->unique()->numberBetween(100, 999),
            'pi_date'            => '2026-05-01',
            'due_date'           => '2026-05-15',
            'billing_cycle'      => 'yearly',
            'usage_period_start' => '2026-05-01',
            'usage_period_end'   => '2027-04-30',
            'invoice_name'       => 'Test Salon Pvt Ltd',
            'subtotal'           => 10000,
            'tax_type'           => 'igst',
            'tax_rate'           => 18,
            'tax_amount'         => 1800,
            'grand_total'        => 11800,
            'balance_due'        => 11800,
            'status'             => 'unpaid',
            'collection_stage'   => 'new',
        ], $piOverrides));
    }

    public function test_collections_requires_auth(): void
    {
        $this->get('/collections')->assertRedirect('/login');
    }

    public function test_collections_returns_invoices_as_json(): void
    {
        $this->makeInvoice();
        $this->makeInvoice();

        $res = $this->actingAs($this->owner())
            ->getJson('/collections');

        $res->assertOk()
            ->assertJsonStructure(['invoices' => [['id', 'pi', 'brand', 'amount', 'stage']], 'agents']);
        $this->assertCount(2, $res->json('invoices'));
    }

    public function test_stage_can_be_updated(): void
    {
        $pi = $this->makeInvoice(['collection_stage' => 'new']);

        $this->actingAs($this->owner())
            ->patchJson("/api/collections/{$pi->id}/stage", ['stage' => 'promised'])
            ->assertOk()
            ->assertJson(['ok' => true]);

        $this->assertEquals('promised', $pi->fresh()->collection_stage);
    }

    public function test_invalid_stage_is_rejected(): void
    {
        $pi = $this->makeInvoice();

        $this->actingAs($this->owner())
            ->patchJson("/api/collections/{$pi->id}/stage", ['stage' => 'bogus'])
            ->assertStatus(422);
    }

    public function test_promise_date_advances_called_to_promised(): void
    {
        $pi = $this->makeInvoice(['collection_stage' => 'called']);

        $this->actingAs($this->owner())
            ->patchJson("/api/collections/{$pi->id}/promise", ['promise_date' => '2026-06-10'])
            ->assertOk();

        $fresh = $pi->fresh();
        $this->assertEquals('2026-06-10', $fresh->promise_date->toDateString());
        $this->assertEquals('promised', $fresh->collection_stage);
    }

    public function test_agent_only_sees_assigned_invoices(): void
    {
        $agentA = User::factory()->create(['role' => 'agent', 'is_active' => true]);
        $agentB = User::factory()->create(['role' => 'agent', 'is_active' => true]);

        $this->makeInvoice(['assigned_agent_id' => $agentA->id]);
        $this->makeInvoice(['assigned_agent_id' => $agentA->id]);
        $this->makeInvoice(['assigned_agent_id' => $agentB->id]);

        $res = $this->actingAs($agentA)->getJson('/collections');
        $this->assertCount(2, $res->json('invoices'));
    }

    public function test_search_filters_by_brand(): void
    {
        $this->makeInvoice([], ['business_name' => 'Truefitt & Hill']);
        $this->makeInvoice([], ['business_name' => 'Bloom Lounge']);

        $res = $this->actingAs($this->owner())->getJson('/collections?q=Truefitt');
        $this->assertCount(1, $res->json('invoices'));
        $this->assertEquals('Truefitt & Hill', $res->json('invoices.0.brand'));
    }
}
