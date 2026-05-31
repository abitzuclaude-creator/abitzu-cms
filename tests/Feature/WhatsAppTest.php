<?php

namespace Tests\Feature;

use App\Models\Client;
use App\Models\ProformaInvoice;
use App\Models\User;
use App\Models\WhatsappTemplate;
use App\Services\WhatsAppService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WhatsAppTest extends TestCase
{
    use RefreshDatabase;

    private function pi(string $dueDate): ProformaInvoice
    {
        $client = Client::create([
            'business_name' => 'Glow Bar', 'business_id' => 'BSN-9001',
            'invoice_type' => 'ho_level', 'invoice_name' => 'Glow Beauty LLP',
            'gstin_status' => 'valid', 'owner_name' => 'Rhea Kapadia',
            'owner_email' => 'rhea@glow.in', 'owner_phone1' => '9849001234',
        ]);
        return ProformaInvoice::create([
            'client_id' => $client->id, 'pi_number' => 901,
            'pi_date' => '2026-05-01', 'due_date' => $dueDate,
            'billing_cycle' => 'yearly', 'usage_period_start' => '2026-05-01',
            'usage_period_end' => '2027-04-30', 'invoice_name' => 'Glow Beauty LLP',
            'subtotal' => 30000, 'tax_type' => 'igst', 'tax_rate' => 18,
            'tax_amount' => 5400, 'grand_total' => 35400, 'balance_due' => 35400,
            'status' => 'unpaid', 'collection_stage' => 'new',
        ]);
    }

    public function test_pre_due_template_when_not_yet_due(): void
    {
        WhatsappTemplate::create(['type' => 'pre_due', 'template_body' => 'Reminder: {{pi_number}} due {{due_date}} amount {{amount_due}}']);
        $pi = $this->pi(now()->addDays(5)->toDateString());

        $out = app(WhatsAppService::class)->compose($pi->load('client', 'lineItems'));

        $this->assertEquals('pre_due', $out['template_type']);
        $this->assertStringContainsString('901', $out['message']);
    }

    public function test_post_due_template_when_overdue(): void
    {
        WhatsappTemplate::create(['type' => 'post_due', 'template_body' => 'Overdue: {{pi_number}} was due {{due_date}}']);
        $pi = $this->pi(now()->subDays(5)->toDateString());

        $out = app(WhatsAppService::class)->compose($pi->load('client', 'lineItems'));
        $this->assertEquals('post_due', $out['template_type']);
    }

    public function test_phone_number_formatted_with_country_code(): void
    {
        WhatsappTemplate::create(['type' => 'pre_due', 'template_body' => 'Hi']);
        $pi = $this->pi(now()->addDays(3)->toDateString());

        $out = app(WhatsAppService::class)->compose($pi->load('client', 'lineItems'));
        $this->assertStringContainsString('wa.me/919849001234', $out['wa_url']);
    }

    public function test_variables_are_replaced(): void
    {
        WhatsappTemplate::create(['type' => 'pre_due', 'template_body' => '{{business_name}} owes {{amount_due}}']);
        $pi = $this->pi(now()->addDays(3)->toDateString());

        $out = app(WhatsAppService::class)->compose($pi->load('client', 'lineItems'));
        $this->assertStringContainsString('Glow Bar', $out['message']);
        $this->assertStringNotContainsString('{{', $out['message']);
    }

    public function test_compose_endpoint_returns_message(): void
    {
        WhatsappTemplate::create(['type' => 'pre_due', 'template_body' => 'Hi {{business_name}}']);
        $user = User::factory()->create(['role' => 'owner']);
        $pi = $this->pi(now()->addDays(3)->toDateString());

        $this->actingAs($user)->getJson("/api/whatsapp/compose/{$pi->id}")
            ->assertOk()
            ->assertJsonStructure(['message', 'template_type', 'wa_url']);
    }

    public function test_logging_whatsapp_advances_new_to_called(): void
    {
        $user = User::factory()->create(['role' => 'owner']);
        $pi = $this->pi(now()->addDays(3)->toDateString());

        $this->actingAs($user)->postJson('/api/whatsapp/log', [
            'proforma_invoice_id' => $pi->id, 'template_type' => 'pre_due',
        ])->assertOk();

        $this->assertEquals('called', $pi->fresh()->collection_stage);
        $this->assertDatabaseHas('interactions', ['proforma_invoice_id' => $pi->id, 'type' => 'whatsapp']);
    }
}
