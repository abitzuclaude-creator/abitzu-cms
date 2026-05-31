// ---- Abitzu Collections : data layer (salon / spa / wellness clients) ----

const STAGES = [
  { id: 'new',      label: 'New / Unpaid',           short: 'New',      color: '#64748b', tint: '#f1f5f9' },
  { id: 'called',   label: 'Called \u2013 awaiting reply', short: 'Called',   color: '#1f6feb', tint: '#eaf2fe' },
  { id: 'promised', label: 'Promised to pay',         short: 'Promised', color: '#7c3aed', tint: '#f1ecfd' },
  { id: 'partial',  label: 'Partially paid',          short: 'Partial',  color: '#b45309', tint: '#fdf3e7' },
  { id: 'overdue',  label: 'Overdue / Escalated',     short: 'Overdue',  color: '#dc2626', tint: '#fdecec' },
  { id: 'disputed', label: 'Disputed',                short: 'Disputed', color: '#db2777', tint: '#fdebf4' },
  { id: 'paid',     label: 'Paid',                    short: 'Paid',     color: '#16a34a', tint: '#e9f7ef' },
];
const STAGE = Object.fromEntries(STAGES.map(s => [s.id, s]));

const TEAM = [
  { id: 'priya',  name: 'Priya Nair',  initials: 'PN', color: '#1f6feb' },
  { id: 'rohan',  name: 'Rohan Mehta', initials: 'RM', color: '#7c3aed' },
  { id: 'aisha',  name: 'Aisha Khan',  initials: 'AK', color: '#0e7490' },
  { id: 'vikram', name: 'Vikram Rao',  initials: 'VR', color: '#b45309' },
];
const MEMBER = Object.fromEntries(TEAM.map(m => [m.id, m]));

// ---- helpers ----------------------------------------------------------
const TODAY = new Date('2026-05-31');

function fmtINR(n) {
  if (n == null) return '\u2014';
  return '\u20b9' + new Intl.NumberFormat('en-IN', { maximumFractionDigits: 0 }).format(n);
}
function fmtDate(iso) {
  if (!iso) return '\u2014';
  return new Date(iso).toLocaleDateString('en-IN', { day: '2-digit', month: 'short', year: 'numeric' });
}
function fmtShortDate(iso) {
  if (!iso) return '\u2014';
  return new Date(iso).toLocaleDateString('en-IN', { day: '2-digit', month: 'short' });
}
function daysFromToday(iso) {
  if (!iso) return null;
  return Math.round((new Date(iso) - TODAY) / 86400000);
}
function dueLabel(iso) {
  const d = daysFromToday(iso);
  if (d == null) return { text: '\u2014', tone: 'muted' };
  if (d < 0)  return { text: Math.abs(d) + 'd overdue', tone: 'danger' };
  if (d === 0) return { text: 'Due today', tone: 'warn' };
  if (d <= 5) return { text: 'Due in ' + d + 'd', tone: 'warn' };
  return { text: 'Due in ' + d + 'd', tone: 'muted' };
}

let _id = 100;
const uid = () => 'A' + (++_id);
function mk(o) {
  return Object.assign({ calls: [], activity: [], reply: '', promiseDate: null, paidAmount: 0, paidDate: null, notes: '' }, o);
}

const INVOICES = [
  mk({ id: uid(), pi: 'PI-2026-0150', piDate: '2026-05-29',
    brand: 'Aura Skin Clinic', business: 'Aura Dermatology LLP', address: 'C-Scheme, Ashok Marg, Jaipur 302001',
    gst: '08AAGFA2210P1ZK', subPeriod: 'Jun 2026 \u2013 May 2027', dueDate: '2026-06-09',
    contact: 'Dr. Ishaan Verma', ownerEmail: 'ishaan@auraskin.in', amount: 21240, stage: 'new', assignee: 'priya',
    activity: [{ t: 'created', by: 'system', at: '2026-05-29', text: 'Proforma parsed from support@abitzu.com' }] }),
  mk({ id: uid(), pi: 'PI-2026-0149', piDate: '2026-05-28',
    brand: 'Urban Mane Co.', business: 'Urban Mane Pvt Ltd', address: 'Sector 29, Gurugram 122001',
    gst: '06AAHCU1209J1ZF', subPeriod: 'Jun 2026 \u2013 May 2027', dueDate: '2026-06-08',
    contact: 'Karan Bhatia', ownerEmail: 'karan@urbanmane.in', amount: 18880, stage: 'new', assignee: 'priya',
    activity: [{ t: 'created', by: 'system', at: '2026-05-28', text: 'Proforma parsed from support@abitzu.com' }] }),
  mk({ id: uid(), pi: 'PI-2026-0148', piDate: '2026-05-27',
    brand: 'The Nail Atelier', business: 'Atelier Beauty Pvt Ltd', address: '11 Indiranagar 100ft Rd, Bengaluru 560038',
    gst: '29AANCA5521K1Z9', subPeriod: 'Jun 2026 \u2013 May 2027', dueDate: '2026-06-11',
    contact: 'Sara DSouza', ownerEmail: 'sara@nailatelier.in', amount: 14160, stage: 'new', assignee: 'aisha',
    activity: [{ t: 'created', by: 'system', at: '2026-05-27', text: 'Proforma parsed from support@abitzu.com' }] }),

  mk({ id: uid(), pi: 'PI-2026-0146', piDate: '2026-05-25',
    brand: 'Lush Locks Salon', business: 'Lush Locks Enterprises', address: '2nd Flr, Koramangala 5th Blk, Bengaluru 560095',
    gst: '29AAEFL3344N1Z9', subPeriod: 'Jun 2026 \u2013 May 2027', dueDate: '2026-06-07',
    contact: 'Nikhil Shetty', ownerEmail: 'nikhil@lushlocks.in', amount: 23600, stage: 'called', assignee: 'aisha',
    calls: [{ date: '2026-05-29', note: 'Left voicemail, no answer' }],
    activity: [
      { t: 'created', by: 'system', at: '2026-05-25', text: 'Proforma parsed from support@abitzu.com' },
      { t: 'call', by: 'aisha', at: '2026-05-29', text: 'Called \u2014 left voicemail, no answer' },
      { t: 'stage', by: 'aisha', at: '2026-05-29', text: 'Moved to Called \u2013 awaiting reply' } ] }),
  mk({ id: uid(), pi: 'PI-2026-0143', piDate: '2026-05-22',
    brand: 'Kanvas Skin Studio', business: 'Kanvas Aesthetics Pvt Ltd', address: '8 Lavelle Road, Bengaluru 560001',
    gst: '29AAPCK7782L1Z3', subPeriod: 'Jun 2026 \u2013 May 2027', dueDate: '2026-06-05',
    contact: 'Tanya Bhasin', ownerEmail: 'tanya@kanvasskin.in', amount: 28320, stage: 'called', assignee: 'rohan',
    calls: [{ date: '2026-05-28', note: 'Spoke to manager, owner travelling' }],
    reply: 'Owner back on 3 Jun, will confirm payment then.',
    activity: [
      { t: 'created', by: 'system', at: '2026-05-22', text: 'Proforma parsed from support@abitzu.com' },
      { t: 'call', by: 'rohan', at: '2026-05-28', text: 'Spoke to manager \u2014 owner travelling till 3 Jun' } ] }),

  mk({ id: uid(), pi: 'PI-2026-0139', piDate: '2026-05-18',
    brand: 'The Mane Studio', business: 'Mane Studio (OPC) Pvt Ltd', address: '12 Park Street, Kolkata 700016',
    gst: '19AALCM9087Q1ZT', subPeriod: 'Jun 2026 \u2013 May 2027', dueDate: '2026-05-28',
    contact: 'Debolina Roy', ownerEmail: 'debolina@manestudio.in', amount: 28320, stage: 'promised', assignee: 'priya',
    promiseDate: '2026-06-03',
    calls: [{ date: '2026-05-26', note: 'Agreed to clear balance by 3 Jun' }],
    reply: 'Will process the full payment by 3rd June.',
    activity: [
      { t: 'created', by: 'system', at: '2026-05-18', text: 'Proforma parsed from support@abitzu.com' },
      { t: 'call', by: 'priya', at: '2026-05-22', text: 'Discussed renewal' },
      { t: 'promise', by: 'priya', at: '2026-05-26', text: 'Promise to pay set for 03 Jun 2026' } ] }),
  mk({ id: uid(), pi: 'PI-2026-0137', piDate: '2026-05-16',
    brand: 'Glow Bar', business: 'Glow Beauty Lounge LLP', address: '21 Banjara Hills Rd 2, Hyderabad 500034',
    gst: '36AAQCG4410M1Z2', subPeriod: 'Jun 2026 \u2013 May 2027', dueDate: '2026-05-30',
    contact: 'Rhea Kapadia', ownerEmail: 'rhea@glowbar.in', amount: 35400, stage: 'promised', assignee: 'vikram',
    promiseDate: '2026-06-02',
    calls: [{ date: '2026-05-27', note: 'Confirmed NEFT first week of June' }],
    reply: 'NEFT will be done first week of June.',
    activity: [
      { t: 'created', by: 'system', at: '2026-05-16', text: 'Proforma parsed from support@abitzu.com' },
      { t: 'promise', by: 'vikram', at: '2026-05-27', text: 'Promise to pay set for 02 Jun 2026' } ] }),

  mk({ id: uid(), pi: 'PI-2026-0134', piDate: '2026-05-12',
    brand: 'Truefitt & Hill', business: 'LPHC Lifestyle Pvt Ltd', address: 'Phoenix Mktcity, Kurla West, Mumbai 400070',
    gst: '27AABCL1234M1ZP', subPeriod: 'Apr 2026 \u2013 Jun 2026 (Q)', dueDate: '2026-05-09',
    contact: 'Meera Joshi', ownerEmail: 'accounts@truefitthill.in', amount: 375240, stage: 'partial', assignee: 'priya',
    paidAmount: 175240, paidDate: '2026-05-23',
    calls: [{ date: '2026-05-20', note: 'HO released part payment, rest in approval queue' }],
    reply: 'Paid first tranche, balance after board approval (~7 Jun).',
    activity: [
      { t: 'created', by: 'system', at: '2026-05-12', text: 'Proforma parsed from support@abitzu.com' },
      { t: 'payment', by: 'priya', at: '2026-05-23', text: 'Part payment received \u20b91,75,240' } ] }),
  mk({ id: uid(), pi: 'PI-2026-0130', piDate: '2026-05-08',
    brand: 'Pearl Aesthetics', business: 'Pearl Glow Pvt Ltd', address: 'Banjara Hills Rd 12, Hyderabad 500034',
    gst: '36AAKCP8890M1Z7', subPeriod: 'May 2026 \u2013 Apr 2027', dueDate: '2026-05-18',
    contact: 'Ritu Agarwal', ownerEmail: 'ritu@pearlaesthetics.in', amount: 33040, stage: 'partial', assignee: 'aisha',
    paidAmount: 16520, paidDate: '2026-05-26',
    calls: [{ date: '2026-05-24', note: 'Part paid, balance after next billing' }],
    activity: [
      { t: 'created', by: 'system', at: '2026-05-08', text: 'Proforma parsed from support@abitzu.com' },
      { t: 'payment', by: 'aisha', at: '2026-05-26', text: 'Part payment received \u20b916,520' } ] }),

  mk({ id: uid(), pi: 'PI-2026-0126', piDate: '2026-05-02',
    brand: 'Bloom Beauty Lounge', business: 'Bloom Wellness LLP', address: 'Sindhu Bhavan Road, Bodakdev, Ahmedabad 380059',
    gst: '24AAFFB7788K1Z5', subPeriod: 'May 2026 \u2013 Apr 2027', dueDate: '2026-05-15',
    contact: 'Kavya Desai', ownerEmail: 'kavya@bloomlounge.in', amount: 35400, stage: 'overdue', assignee: 'priya',
    calls: [
      { date: '2026-05-18', note: 'No response' },
      { date: '2026-05-24', note: 'Promised callback, never came' },
      { date: '2026-05-29', note: 'Number not reachable' } ],
    activity: [
      { t: 'created', by: 'system', at: '2026-05-02', text: 'Proforma parsed from support@abitzu.com' },
      { t: 'stage', by: 'priya', at: '2026-05-24', text: 'Escalated \u2014 overdue 15+ days' } ] }),
  mk({ id: uid(), pi: 'PI-2026-0121', piDate: '2026-04-26',
    brand: 'Marigold Salon & Academy', business: 'Marigold Beauty Edu Pvt Ltd', address: 'FC Road, Shivajinagar, Pune 411005',
    gst: '27AAJCM5567R1ZB', subPeriod: 'May 2026 \u2013 Apr 2027', dueDate: '2026-05-06',
    contact: 'Sneha Kulkarni', ownerEmail: 'sneha@marigold.academy', amount: 94400, stage: 'overdue', assignee: 'rohan',
    calls: [
      { date: '2026-05-10', note: 'Asked for more time' },
      { date: '2026-05-21', note: 'Cited cash flow, requested extension' } ],
    reply: 'Facing a cash crunch, requesting a 2-week extension.',
    activity: [
      { t: 'created', by: 'system', at: '2026-04-26', text: 'Proforma parsed from support@abitzu.com' },
      { t: 'stage', by: 'rohan', at: '2026-05-21', text: 'Escalated \u2014 overdue' } ] }),
  mk({ id: uid(), pi: 'PI-2026-0117', piDate: '2026-04-20',
    brand: 'Coiffure House', business: 'Coiffure Salons Pvt Ltd', address: '5 Linking Road, Khar, Mumbai 400052',
    gst: '27AARCC2210V1ZG', subPeriod: 'May 2026 \u2013 Apr 2027', dueDate: '2026-05-04',
    contact: 'Farhan Qureshi', ownerEmail: 'farhan@coiffurehouse.in', amount: 47200, stage: 'overdue', assignee: 'vikram',
    calls: [
      { date: '2026-05-08', note: 'Promised, did not pay' },
      { date: '2026-05-22', note: 'Avoiding calls' },
      { date: '2026-05-28', note: 'Sent final reminder email' } ],
    activity: [
      { t: 'created', by: 'system', at: '2026-04-20', text: 'Proforma parsed from support@abitzu.com' },
      { t: 'stage', by: 'vikram', at: '2026-05-22', text: 'Escalated \u2014 overdue 18+ days' } ] }),

  mk({ id: uid(), pi: 'PI-2026-0113', piDate: '2026-04-14',
    brand: 'Serene Spa & Wellness', business: 'Serene Hospitality Pvt Ltd', address: 'Cyber Hub, DLF Ph II, Gurugram 122002',
    gst: '06AAGCS5521H1Z0', subPeriod: 'Apr 2026 \u2013 Sep 2026 (H)', dueDate: '2026-05-10',
    contact: 'Aditya Malhotra', ownerEmail: 'aditya@serenespa.co', amount: 247800, stage: 'disputed', assignee: 'rohan',
    calls: [{ date: '2026-05-06', note: 'Disputes 2 of 4 branch line items' }],
    reply: 'Two branches were billed twice. Please revise before we pay.',
    activity: [
      { t: 'created', by: 'system', at: '2026-04-14', text: 'Proforma parsed from support@abitzu.com' },
      { t: 'stage', by: 'rohan', at: '2026-05-06', text: 'Marked Disputed \u2014 branch line-item mismatch' } ] }),
  mk({ id: uid(), pi: 'PI-2026-0109', piDate: '2026-04-09',
    brand: 'Opulence Med-Spa', business: 'Opulence Aesthetics Pvt Ltd', address: 'Jubilee Hills Rd 36, Hyderabad 500033',
    gst: 'A/f', subPeriod: 'Apr 2026 \u2013 Mar 2027 (Y)', dueDate: '2026-04-23',
    contact: 'Dr. Sanjana Reddy', ownerEmail: 'sanjana@opulence.health', amount: 566400, stage: 'disputed', assignee: 'aisha',
    calls: [{ date: '2026-05-04', note: 'Queries annual plan pricing vs quote' }],
    reply: 'Annual figure differs from the quote we signed. Awaiting clarification.',
    activity: [
      { t: 'created', by: 'system', at: '2026-04-09', text: 'Proforma parsed from support@abitzu.com' },
      { t: 'stage', by: 'aisha', at: '2026-05-04', text: 'Marked Disputed \u2014 pricing query (GSTIN awaiting filing)' } ] }),

  mk({ id: uid(), pi: 'PI-2026-0104', piDate: '2026-04-03',
    brand: 'Velvet Spa Retreat', business: 'Velvet Wellness Pvt Ltd', address: '9 ECR, Injambakkam, Chennai 600115',
    gst: '33AAVCV3367W1ZH', subPeriod: 'Apr 2026 \u2013 Mar 2027', dueDate: '2026-04-17',
    contact: 'Leena Thomas', ownerEmail: 'accounts@velvetspa.in', amount: 144000, stage: 'paid', assignee: 'rohan',
    paidAmount: 144000, paidDate: '2026-05-10',
    calls: [{ date: '2026-04-30', note: 'Confirmed payment scheduled' }],
    activity: [
      { t: 'created', by: 'system', at: '2026-04-03', text: 'Proforma parsed from support@abitzu.com' },
      { t: 'payment', by: 'rohan', at: '2026-05-10', text: 'Full payment received \u20b91,44,000' },
      { t: 'stage', by: 'rohan', at: '2026-05-10', text: 'Marked Paid' } ] }),
  mk({ id: uid(), pi: 'PI-2026-0096', piDate: '2026-03-27',
    brand: 'Vanit\u00e9 Beauty Bar', business: 'Vanite Retail Pvt Ltd', address: 'High Street Phoenix, Lower Parel, Mumbai 400013',
    gst: '27AADCV9921L1Z2', subPeriod: 'Apr 2026 \u2013 Jun 2026 (Q)', dueDate: '2026-04-10',
    contact: 'Tara Kapoor', ownerEmail: 'tara@vanite.in', amount: 106200, stage: 'paid', assignee: 'priya',
    paidAmount: 106200, paidDate: '2026-05-02',
    activity: [
      { t: 'created', by: 'system', at: '2026-03-27', text: 'Proforma parsed from support@abitzu.com' },
      { t: 'payment', by: 'priya', at: '2026-05-02', text: 'Full payment received \u20b91,06,200' } ] }),
];

Object.assign(window, { STAGES, STAGE, TEAM, MEMBER, INVOICES, fmtINR, fmtDate, fmtShortDate, daysFromToday, dueLabel, uid, TODAY });
