<?php

namespace Database\Seeders;

use App\Models\BankAccount;
use Illuminate\Database\Seeder;

class BankAccountSeeder extends Seeder
{
    public function run(): void
    {
        BankAccount::create([
            'label' => 'IndusInd Mumbai', 'bank_name' => 'IndusInd Bank Ltd',
            'account_number_last4' => '3083', 'branch' => 'Bandra West, Mumbai',
            'ifsc' => 'INDB0000003', 'is_active' => true,
        ]);
        BankAccount::create([
            'label' => 'HDFC Indore', 'bank_name' => 'HDFC Bank Ltd',
            'account_number_last4' => '7412', 'branch' => 'Vijay Nagar, Indore',
            'ifsc' => 'HDFC0001234', 'is_active' => true,
        ]);
    }
}
