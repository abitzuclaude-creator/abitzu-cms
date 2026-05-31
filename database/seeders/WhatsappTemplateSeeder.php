<?php

namespace Database\Seeders;

use App\Models\WhatsappTemplate;
use Illuminate\Database\Seeder;

class WhatsappTemplateSeeder extends Seeder
{
    public function run(): void
    {
        WhatsappTemplate::create([
            'type' => 'pre_due',
            'template_body' => "Dear Sir/Madam,\n\nThis is a reminder that Proforma Invoice #{{pi_number}} for {{business_name}} ({{location}}) amounting to {{amount_due}} is due on {{due_date}}.\n\nKindly arrange the payment at your earliest convenience.\n\nRegards,\nAbitzu Collection Team",
        ]);
        WhatsappTemplate::create([
            'type' => 'post_due',
            'template_body' => "Dear Sir/Madam,\n\nThis is to inform you that Proforma Invoice #{{pi_number}} for {{business_name}} ({{location}}) amounting to {{amount_due}} was due on {{due_date}} and remains unpaid.\n\nPlease make the payment immediately to avoid service disruption.\n\nRegards,\nAbitzu Collection Team",
        ]);
    }
}
