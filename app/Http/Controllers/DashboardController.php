<?php

namespace App\Http\Controllers;

use App\Models\Alert;
use App\Models\ProformaInvoice;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $query = ProformaInvoice::with('client');

        if ($user->isAgent()) {
            $query->where('assigned_agent_id', $user->id);
        }

        $invoices = $query->get();
        $open = $invoices->where('status', '!=', 'paid');
        $today = now()->toDateString();

        $stats = [
            'total_outstanding' => $open->sum('balance_due'),
            'overdue_amount'    => $open->filter(fn($p) => $p->due_date && $p->due_date->toDateString() < $today)->sum('balance_due'),
            'overdue_count'     => $open->filter(fn($p) => $p->due_date && $p->due_date->toDateString() < $today)->count(),
            'open_count'        => $open->count(),
            'collected_month'   => $invoices->filter(fn($p) => $p->status === 'paid')->sum(fn($p) => $p->grand_total - $p->balance_due),
            'promise_week'      => $invoices->filter(fn($p) => $p->promise_date && $p->promise_date->toDateString() >= $today && $p->promise_date->diffInDays($today) <= 7)->count(),
            'open_alerts'       => Alert::where('status', 'open')->count(),
        ];

        $agents = User::whereIn('role', ['agent'])->withCount(['assignedInvoices as open_pi' => fn($q) => $q->where('status', '!=', 'paid')])->get();

        if (request()->expectsJson()) {
            return response()->json(compact('stats', 'agents'));
        }

        return view('dashboard', compact('stats', 'invoices', 'agents'));
    }
}
