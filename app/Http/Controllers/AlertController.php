<?php

namespace App\Http\Controllers;

use App\Models\Alert;
use Illuminate\Http\Request;

class AlertController extends Controller
{
    public function index()
    {
        $alerts = Alert::latest()->get();
        if (request()->expectsJson()) return response()->json($alerts);
        return view('alerts.index', compact('alerts'));
    }

    public function count()
    {
        return response()->json(['count' => Alert::where('status', 'open')->count()]);
    }

    public function update(Request $request, Alert $alert)
    {
        $request->validate(['status' => 'required|in:open,investigating,resolved,ignored', 'resolved_notes' => 'nullable|string']);
        $alert->update([
            'status'         => $request->status,
            'resolved_notes' => $request->resolved_notes,
            'resolved_by'    => in_array($request->status, ['resolved', 'ignored']) ? auth()->id() : null,
            'resolved_at'    => in_array($request->status, ['resolved', 'ignored']) ? now() : null,
        ]);
        return response()->json(['ok' => true, 'alert' => $alert]);
    }
}
