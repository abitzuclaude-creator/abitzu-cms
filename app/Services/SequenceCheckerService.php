<?php

namespace App\Services;

use App\Models\Alert;
use App\Models\ProformaInvoice;

class SequenceCheckerService
{
    public function run(): int
    {
        $numbers = ProformaInvoice::orderBy('pi_number')->pluck('pi_number')->toArray();
        $created = 0;

        for ($i = 0; $i < count($numbers) - 1; $i++) {
            $current = $numbers[$i];
            $next = $numbers[$i + 1];

            for ($missing = $current + 1; $missing < $next; $missing++) {
                $exists = Alert::where('type', 'missing_pi_sequence')
                    ->where('description', 'like', "%#$missing%")
                    ->where('status', 'open')
                    ->exists();

                if (!$exists) {
                    Alert::create([
                        'type'        => 'missing_pi_sequence',
                        'title'       => "PI #{$missing} missing",
                        'description' => "PI #{$missing} missing between #{$current} and #{$next}",
                        'status'      => 'open',
                    ]);
                    $created++;
                }
            }
        }

        return $created;
    }
}
