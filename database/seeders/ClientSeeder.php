<?php

namespace Database\Seeders;

use App\Models\Alert;
use App\Models\Client;
use App\Models\Contact;
use App\Models\Interaction;
use App\Models\Location;
use App\Models\ProformaInvoice;
use App\Models\User;
use Illuminate\Database\Seeder;

class ClientSeeder extends Seeder
{
    public function run(): void
    {
        $priya  = User::where('email', 'priya@abitzu.com')->first();
        $rohan  = User::where('email', 'rohan@abitzu.com')->first();
        $aisha  = User::where('email', 'aisha@abitzu.com')->first();
        $vikram = User::where('email', 'vikram@abitzu.com')->first();

        $invoices = [
            [
                'client' => ['business_name'=>'Aura Skin Clinic','business_id'=>'BSN-2201','invoice_type'=>'ho_level','invoice_name'=>'Aura Dermatology LLP','address'=>'C-Scheme, Ashok Marg, Jaipur 302001','gstin'=>'08AAGFA2210P1ZK','owner_name'=>'Dr. Ishaan Verma','owner_email'=>'ishaan@auraskin.in','owner_phone1'=>'9414001234'],
                'pi' => ['pi_number'=>150,'pi_date'=>'2026-05-29','due_date'=>'2026-06-09','billing_cycle'=>'yearly','usage_period_start'=>'2026-06-01','usage_period_end'=>'2027-05-31','invoice_name'=>'Aura Dermatology LLP','gstin'=>'08AAGFA2210P1ZK','subtotal'=>18000.00,'tax_type'=>'igst','tax_rate'=>18.00,'tax_amount'=>3240.00,'grand_total'=>21240.00,'balance_due'=>21240.00,'status'=>'unpaid','collection_stage'=>'new'],
                'agent' => $priya,
            ],
            [
                'client' => ['business_name'=>'Urban Mane Co.','business_id'=>'BSN-2198','invoice_type'=>'ho_level','invoice_name'=>'Urban Mane Pvt Ltd','address'=>'Sector 29, Gurugram 122001','gstin'=>'06AAHCU1209J1ZF','owner_name'=>'Karan Bhatia','owner_email'=>'karan@urbanmane.in','owner_phone1'=>'9810005678'],
                'pi' => ['pi_number'=>149,'pi_date'=>'2026-05-28','due_date'=>'2026-06-08','billing_cycle'=>'yearly','usage_period_start'=>'2026-06-01','usage_period_end'=>'2027-05-31','invoice_name'=>'Urban Mane Pvt Ltd','gstin'=>'06AAHCU1209J1ZF','subtotal'=>16000.00,'tax_type'=>'igst','tax_rate'=>18.00,'tax_amount'=>2880.00,'grand_total'=>18880.00,'balance_due'=>18880.00,'status'=>'unpaid','collection_stage'=>'new'],
                'agent' => $priya,
            ],
            [
                'client' => ['business_name'=>'The Nail Atelier','business_id'=>'BSN-2195','invoice_type'=>'ho_level','invoice_name'=>'Atelier Beauty Pvt Ltd','address'=>'11 Indiranagar 100ft Rd, Bengaluru 560038','gstin'=>'29AANCA5521K1Z9','owner_name'=>'Sara DSouza','owner_email'=>'sara@nailatelier.in','owner_phone1'=>'9980012345'],
                'pi' => ['pi_number'=>148,'pi_date'=>'2026-05-27','due_date'=>'2026-06-11','billing_cycle'=>'yearly','usage_period_start'=>'2026-06-01','usage_period_end'=>'2027-05-31','invoice_name'=>'Atelier Beauty Pvt Ltd','gstin'=>'29AANCA5521K1Z9','subtotal'=>12000.00,'tax_type'=>'igst','tax_rate'=>18.00,'tax_amount'=>2160.00,'grand_total'=>14160.00,'balance_due'=>14160.00,'status'=>'unpaid','collection_stage'=>'new'],
                'agent' => $aisha,
            ],
            [
                'client' => ['business_name'=>'Lush Locks Salon','business_id'=>'BSN-2187','invoice_type'=>'ho_level','invoice_name'=>'Lush Locks Enterprises','address'=>'2nd Flr, Koramangala 5th Blk, Bengaluru 560095','gstin'=>'29AAEFL3344N1Z9','owner_name'=>'Nikhil Shetty','owner_email'=>'nikhil@lushlocks.in','owner_phone1'=>'9741001234'],
                'pi' => ['pi_number'=>146,'pi_date'=>'2026-05-25','due_date'=>'2026-06-07','billing_cycle'=>'yearly','usage_period_start'=>'2026-06-01','usage_period_end'=>'2027-05-31','invoice_name'=>'Lush Locks Enterprises','gstin'=>'29AAEFL3344N1Z9','subtotal'=>20000.00,'tax_type'=>'igst','tax_rate'=>18.00,'tax_amount'=>3600.00,'grand_total'=>23600.00,'balance_due'=>23600.00,'status'=>'unpaid','collection_stage'=>'called'],
                'agent' => $aisha,
                'calls' => [['date'=>'2026-05-29','note'=>'Left voicemail, no answer']],
            ],
            [
                'client' => ['business_name'=>'Kanvas Skin Studio','business_id'=>'BSN-2178','invoice_type'=>'ho_level','invoice_name'=>'Kanvas Aesthetics Pvt Ltd','address'=>'8 Lavelle Road, Bengaluru 560001','gstin'=>'29AAPCK7782L1Z3','owner_name'=>'Tanya Bhasin','owner_email'=>'tanya@kanvasskin.in','owner_phone1'=>'9886001234'],
                'pi' => ['pi_number'=>143,'pi_date'=>'2026-05-22','due_date'=>'2026-06-05','billing_cycle'=>'yearly','usage_period_start'=>'2026-06-01','usage_period_end'=>'2027-05-31','invoice_name'=>'Kanvas Aesthetics Pvt Ltd','gstin'=>'29AAPCK7782L1Z3','subtotal'=>24000.00,'tax_type'=>'igst','tax_rate'=>18.00,'tax_amount'=>4320.00,'grand_total'=>28320.00,'balance_due'=>28320.00,'status'=>'unpaid','collection_stage'=>'called'],
                'agent' => $rohan,
                'calls' => [['date'=>'2026-05-28','note'=>'Spoke to manager, owner travelling']],
            ],
            [
                'client' => ['business_name'=>'The Mane Studio','business_id'=>'BSN-2161','invoice_type'=>'ho_level','invoice_name'=>'Mane Studio (OPC) Pvt Ltd','address'=>'12 Park Street, Kolkata 700016','gstin'=>'19AALCM9087Q1ZT','owner_name'=>'Debolina Roy','owner_email'=>'debolina@manestudio.in','owner_phone1'=>'9830012345'],
                'pi' => ['pi_number'=>139,'pi_date'=>'2026-05-18','due_date'=>'2026-05-28','billing_cycle'=>'yearly','usage_period_start'=>'2026-06-01','usage_period_end'=>'2027-05-31','invoice_name'=>'Mane Studio (OPC) Pvt Ltd','gstin'=>'19AALCM9087Q1ZT','subtotal'=>24000.00,'tax_type'=>'igst','tax_rate'=>18.00,'tax_amount'=>4320.00,'grand_total'=>28320.00,'balance_due'=>28320.00,'status'=>'unpaid','collection_stage'=>'promised','promise_date'=>'2026-06-03'],
                'agent' => $priya,
                'calls' => [['date'=>'2026-05-22','note'=>'Discussed renewal'],['date'=>'2026-05-26','note'=>'Agreed to clear balance by 3 Jun']],
            ],
            [
                'client' => ['business_name'=>'Glow Bar','business_id'=>'BSN-2150','invoice_type'=>'ho_level','invoice_name'=>'Glow Beauty Lounge LLP','address'=>'21 Banjara Hills Rd 2, Hyderabad 500034','gstin'=>'36AAQCG4410M1Z2','owner_name'=>'Rhea Kapadia','owner_email'=>'rhea@glowbar.in','owner_phone1'=>'9849001234'],
                'pi' => ['pi_number'=>137,'pi_date'=>'2026-05-16','due_date'=>'2026-05-30','billing_cycle'=>'yearly','usage_period_start'=>'2026-06-01','usage_period_end'=>'2027-05-31','invoice_name'=>'Glow Beauty Lounge LLP','gstin'=>'36AAQCG4410M1Z2','subtotal'=>30000.00,'tax_type'=>'igst','tax_rate'=>18.00,'tax_amount'=>5400.00,'grand_total'=>35400.00,'balance_due'=>35400.00,'status'=>'unpaid','collection_stage'=>'promised','promise_date'=>'2026-06-02'],
                'agent' => $vikram,
                'calls' => [['date'=>'2026-05-27','note'=>'Confirmed NEFT first week of June']],
            ],
            [
                'client' => ['business_name'=>'Truefitt & Hill','business_id'=>'BSN-2120','invoice_type'=>'branch_level','invoice_name'=>'LPHC Lifestyle Pvt Ltd','address'=>'Phoenix Mktcity, Kurla West, Mumbai 400070','gstin'=>'27AABCL1234M1ZP','owner_name'=>'Meera Joshi','owner_email'=>'accounts@truefitthill.in','owner_phone1'=>'9022001234'],
                'pi' => ['pi_number'=>134,'pi_date'=>'2026-05-12','due_date'=>'2026-05-09','billing_cycle'=>'quarterly','usage_period_start'=>'2026-04-01','usage_period_end'=>'2026-06-30','invoice_name'=>'LPHC Lifestyle Pvt Ltd','gstin'=>'27AABCL1234M1ZP','subtotal'=>318000.00,'tax_type'=>'igst','tax_rate'=>18.00,'tax_amount'=>57240.00,'grand_total'=>375240.00,'balance_due'=>200000.00,'status'=>'partially_paid','collection_stage'=>'partial'],
                'agent' => $priya,
                'calls' => [['date'=>'2026-05-20','note'=>'HO released part payment, rest in approval queue']],
            ],
            [
                'client' => ['business_name'=>'Pearl Aesthetics','business_id'=>'BSN-2105','invoice_type'=>'ho_level','invoice_name'=>'Pearl Glow Pvt Ltd','address'=>'Banjara Hills Rd 12, Hyderabad 500034','gstin'=>'36AAKCP8890M1Z7','owner_name'=>'Ritu Agarwal','owner_email'=>'ritu@pearlaesthetics.in','owner_phone1'=>'9848001234'],
                'pi' => ['pi_number'=>130,'pi_date'=>'2026-05-08','due_date'=>'2026-05-18','billing_cycle'=>'yearly','usage_period_start'=>'2026-05-01','usage_period_end'=>'2027-04-30','invoice_name'=>'Pearl Glow Pvt Ltd','gstin'=>'36AAKCP8890M1Z7','subtotal'=>28000.00,'tax_type'=>'igst','tax_rate'=>18.00,'tax_amount'=>5040.00,'grand_total'=>33040.00,'balance_due'=>16520.00,'status'=>'partially_paid','collection_stage'=>'partial'],
                'agent' => $aisha,
                'calls' => [['date'=>'2026-05-24','note'=>'Part paid, balance after next billing']],
            ],
            [
                'client' => ['business_name'=>'Bloom Beauty Lounge','business_id'=>'BSN-2088','invoice_type'=>'ho_level','invoice_name'=>'Bloom Wellness LLP','address'=>'Sindhu Bhavan Road, Bodakdev, Ahmedabad 380059','gstin'=>'24AAFFB7788K1Z5','owner_name'=>'Kavya Desai','owner_email'=>'kavya@bloomlounge.in','owner_phone1'=>'9825001234'],
                'pi' => ['pi_number'=>126,'pi_date'=>'2026-05-02','due_date'=>'2026-05-15','billing_cycle'=>'yearly','usage_period_start'=>'2026-05-01','usage_period_end'=>'2027-04-30','invoice_name'=>'Bloom Wellness LLP','gstin'=>'24AAFFB7788K1Z5','subtotal'=>30000.00,'tax_type'=>'igst','tax_rate'=>18.00,'tax_amount'=>5400.00,'grand_total'=>35400.00,'balance_due'=>35400.00,'status'=>'unpaid','collection_stage'=>'overdue'],
                'agent' => $priya,
                'calls' => [['date'=>'2026-05-18','note'=>'No response'],['date'=>'2026-05-24','note'=>'Promised callback, never came'],['date'=>'2026-05-29','note'=>'Number not reachable']],
            ],
            [
                'client' => ['business_name'=>'Marigold Salon & Academy','business_id'=>'BSN-2060','invoice_type'=>'ho_level','invoice_name'=>'Marigold Beauty Edu Pvt Ltd','address'=>'FC Road, Shivajinagar, Pune 411005','gstin'=>'27AAJCM5567R1ZB','owner_name'=>'Sneha Kulkarni','owner_email'=>'sneha@marigold.academy','owner_phone1'=>'9890001234'],
                'pi' => ['pi_number'=>121,'pi_date'=>'2026-04-26','due_date'=>'2026-05-06','billing_cycle'=>'yearly','usage_period_start'=>'2026-05-01','usage_period_end'=>'2027-04-30','invoice_name'=>'Marigold Beauty Edu Pvt Ltd','gstin'=>'27AAJCM5567R1ZB','subtotal'=>80000.00,'tax_type'=>'igst','tax_rate'=>18.00,'tax_amount'=>14400.00,'grand_total'=>94400.00,'balance_due'=>94400.00,'status'=>'unpaid','collection_stage'=>'overdue'],
                'agent' => $rohan,
                'calls' => [['date'=>'2026-05-10','note'=>'Asked for more time'],['date'=>'2026-05-21','note'=>'Cited cash flow, requested extension']],
            ],
            [
                'client' => ['business_name'=>'Coiffure House','business_id'=>'BSN-2041','invoice_type'=>'ho_level','invoice_name'=>'Coiffure Salons Pvt Ltd','address'=>'5 Linking Road, Khar, Mumbai 400052','gstin'=>'27AARCC2210V1ZG','owner_name'=>'Farhan Qureshi','owner_email'=>'farhan@coiffurehouse.in','owner_phone1'=>'9820001234'],
                'pi' => ['pi_number'=>117,'pi_date'=>'2026-04-20','due_date'=>'2026-05-04','billing_cycle'=>'yearly','usage_period_start'=>'2026-05-01','usage_period_end'=>'2027-04-30','invoice_name'=>'Coiffure Salons Pvt Ltd','gstin'=>'27AARCC2210V1ZG','subtotal'=>40000.00,'tax_type'=>'igst','tax_rate'=>18.00,'tax_amount'=>7200.00,'grand_total'=>47200.00,'balance_due'=>47200.00,'status'=>'unpaid','collection_stage'=>'overdue'],
                'agent' => $vikram,
                'calls' => [['date'=>'2026-05-08','note'=>'Promised, did not pay'],['date'=>'2026-05-22','note'=>'Avoiding calls'],['date'=>'2026-05-28','note'=>'Sent final reminder email']],
            ],
            [
                'client' => ['business_name'=>'Serene Spa & Wellness','business_id'=>'BSN-2020','invoice_type'=>'branch_level','invoice_name'=>'Serene Hospitality Pvt Ltd','address'=>'Cyber Hub, DLF Ph II, Gurugram 122002','gstin'=>'06AAGCS5521H1Z0','owner_name'=>'Aditya Malhotra','owner_email'=>'aditya@serenespa.co','owner_phone1'=>'9810009876'],
                'pi' => ['pi_number'=>113,'pi_date'=>'2026-04-14','due_date'=>'2026-05-10','billing_cycle'=>'half_yearly','usage_period_start'=>'2026-04-01','usage_period_end'=>'2026-09-30','invoice_name'=>'Serene Hospitality Pvt Ltd','gstin'=>'06AAGCS5521H1Z0','subtotal'=>210000.00,'tax_type'=>'igst','tax_rate'=>18.00,'tax_amount'=>37800.00,'grand_total'=>247800.00,'balance_due'=>247800.00,'status'=>'disputed','collection_stage'=>'disputed'],
                'agent' => $rohan,
                'calls' => [['date'=>'2026-05-06','note'=>'Disputes 2 of 4 branch line items']],
            ],
            [
                'client' => ['business_name'=>'Opulence Med-Spa','business_id'=>'BSN-2001','invoice_type'=>'branch_level','invoice_name'=>'Opulence Aesthetics Pvt Ltd','address'=>'Jubilee Hills Rd 36, Hyderabad 500033','gstin'=>null,'owner_name'=>'Dr. Sanjana Reddy','owner_email'=>'sanjana@opulence.health','owner_phone1'=>'9849001111'],
                'pi' => ['pi_number'=>109,'pi_date'=>'2026-04-09','due_date'=>'2026-04-23','billing_cycle'=>'yearly','usage_period_start'=>'2026-04-01','usage_period_end'=>'2027-03-31','invoice_name'=>'Opulence Aesthetics Pvt Ltd','gstin'=>null,'subtotal'=>480000.00,'tax_type'=>'igst','tax_rate'=>18.00,'tax_amount'=>86400.00,'grand_total'=>566400.00,'balance_due'=>566400.00,'status'=>'disputed','collection_stage'=>'disputed'],
                'agent' => $aisha,
                'calls' => [['date'=>'2026-05-04','note'=>'Queries annual plan pricing vs quote']],
            ],
            [
                'client' => ['business_name'=>'Velvet Spa Retreat','business_id'=>'BSN-1988','invoice_type'=>'ho_level','invoice_name'=>'Velvet Wellness Pvt Ltd','address'=>'9 ECR, Injambakkam, Chennai 600115','gstin'=>'33AAVCV3367W1ZH','owner_name'=>'Leena Thomas','owner_email'=>'accounts@velvetspa.in','owner_phone1'=>'9444001234'],
                'pi' => ['pi_number'=>104,'pi_date'=>'2026-04-03','due_date'=>'2026-04-17','billing_cycle'=>'yearly','usage_period_start'=>'2026-04-01','usage_period_end'=>'2027-03-31','invoice_name'=>'Velvet Wellness Pvt Ltd','gstin'=>'33AAVCV3367W1ZH','subtotal'=>122000.00,'tax_type'=>'igst','tax_rate'=>18.00,'tax_amount'=>21960.00,'grand_total'=>143960.00,'balance_due'=>0.00,'status'=>'paid','collection_stage'=>'paid'],
                'agent' => $rohan,
            ],
            [
                'client' => ['business_name'=>'Vanité Beauty Bar','business_id'=>'BSN-1970','invoice_type'=>'ho_level','invoice_name'=>'Vanite Retail Pvt Ltd','address'=>'High Street Phoenix, Lower Parel, Mumbai 400013','gstin'=>'27AADCV9921L1Z2','owner_name'=>'Tara Kapoor','owner_email'=>'tara@vanite.in','owner_phone1'=>'9930001234'],
                'pi' => ['pi_number'=>96,'pi_date'=>'2026-03-27','due_date'=>'2026-04-10','billing_cycle'=>'quarterly','usage_period_start'=>'2026-04-01','usage_period_end'=>'2026-06-30','invoice_name'=>'Vanite Retail Pvt Ltd','gstin'=>'27AADCV9921L1Z2','subtotal'=>90000.00,'tax_type'=>'igst','tax_rate'=>18.00,'tax_amount'=>16200.00,'grand_total'=>106200.00,'balance_due'=>0.00,'status'=>'paid','collection_stage'=>'paid'],
                'agent' => $priya,
            ],
        ];

        foreach ($invoices as $data) {
            $clientData = array_merge($data['client'], [
                'assigned_agent_id' => $data['agent']->id,
                'gstin_status' => isset($data['client']['gstin']) && $data['client']['gstin'] ? 'valid' : 'awaiting_filing',
            ]);
            $client = Client::create($clientData);

            // Primary contact
            Contact::create([
                'client_id'    => $client->id,
                'contact_type' => 'owner',
                'name'         => $data['client']['owner_name'],
                'email'        => $data['client']['owner_email'],
                'phone1'       => $data['client']['owner_phone1'],
                'added_by'     => 1,
            ]);

            $piData = array_merge($data['pi'], [
                'client_id'        => $client->id,
                'assigned_agent_id'=> $data['agent']->id,
                'imported_by'      => 1,
                'imported_at'      => now(),
            ]);
            $pi = ProformaInvoice::create($piData);

            // Log call interactions
            foreach ($data['calls'] ?? [] as $call) {
                Interaction::create([
                    'client_id'          => $client->id,
                    'proforma_invoice_id'=> $pi->id,
                    'user_id'            => $data['agent']->id,
                    'type'               => 'phone_call',
                    'interaction_date'   => $call['date'] . ' 10:00:00',
                    'disposition'        => 'reached',
                    'notes'              => $call['note'],
                ]);
            }
        }

        // Create a PI-sequence gap alert (PI #147 is missing between #146 and #148)
        Alert::create([
            'type'        => 'missing_pi_sequence',
            'title'       => 'PI #147 missing',
            'description' => 'PI #147 missing between #146 and #148',
            'status'      => 'open',
        ]);
    }
}
