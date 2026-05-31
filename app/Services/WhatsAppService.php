<?php

namespace App\Services;

use App\Models\ProformaInvoice;
use App\Models\WhatsappTemplate;

class WhatsAppService
{
    public function compose(ProformaInvoice $pi): array
    {
        $today = now()->toDateString();
        $isPostDue = $pi->due_date->toDateString() < $today;
        $templateType = $isPostDue ? 'post_due' : 'pre_due';

        $template = WhatsappTemplate::where('type', $templateType)->first();
        $body = $template?->template_body ?? $this->defaultTemplate($isPostDue);

        $client = $pi->client;
        $locations = $pi->lineItems->pluck('location_name')->unique()->values();
        $locationStr = $locations->count() === 1
            ? $locations->first()
            : 'all ' . $locations->count() . ' locations';

        $msg = str_replace(
            ['{{business_name}}', '{{invoice_name}}', '{{location}}', '{{pi_number}}', '{{amount_due}}', '{{due_date}}'],
            [
                $client->business_name,
                $pi->invoice_name,
                $locationStr,
                'PI-' . $pi->pi_number,
                '₹' . number_format($pi->balance_due, 2),
                $pi->due_date->format('d M Y'),
            ],
            $body
        );

        $phone = $this->formatPhone($client->owner_phone1 ?? '');
        $waUrl = $phone ? 'https://wa.me/' . $phone . '?text=' . rawurlencode($msg) : null;

        return [
            'message'       => $msg,
            'template_type' => $templateType,
            'wa_url'        => $waUrl,
            'contact_name'  => $client->owner_name,
            'contact_phone' => $client->owner_phone1,
        ];
    }

    private function formatPhone(string $phone): string
    {
        $digits = preg_replace('/\D/', '', $phone);
        if (strlen($digits) === 10) {
            return '91' . $digits;
        }
        if (str_starts_with($digits, '91') && strlen($digits) === 12) {
            return $digits;
        }
        return $digits;
    }

    private function defaultTemplate(bool $isPostDue): string
    {
        if ($isPostDue) {
            return "Dear {{invoice_name}},\n\nThis is to inform you that Proforma Invoice #{{pi_number}} for {{business_name}} ({{location}}) amounting to {{amount_due}} was due on {{due_date}} and remains unpaid.\n\nPlease make the payment immediately to avoid service disruption.\n\nRegards,\nAbitzu Collection Team";
        }
        return "Dear {{invoice_name}},\n\nThis is a reminder that Proforma Invoice #{{pi_number}} for {{business_name}} ({{location}}) amounting to {{amount_due}} is due on {{due_date}}.\n\nKindly arrange the payment at your earliest convenience.\n\nRegards,\nAbitzu Collection Team";
    }
}
