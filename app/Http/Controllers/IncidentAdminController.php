<?php

namespace App\Http\Controllers;

use App\Models\Incident;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class IncidentAdminController extends Controller
{
    use AuthorizesRequests;

    /**
     * Display incidents management page
     */
    public function index(Request $request)
    {
        // Only show incidents from the same company
        $companyId = Auth::user()->company_id;
        $query = Incident::where('company_id', $companyId)->with('user')->latest();

        // Filter by status if provided
        if ($request->has('status') && $request->status !== '') {
            $query->where('status', $request->status);
        }

        // Filter by date range
        if ($request->has('date_from') && $request->date_from) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->has('date_to') && $request->date_to) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        // Filter by location
        if ($request->has('location') && $request->location) {
            $query->where('location', 'like', '%' . $request->location . '%');
        }

        $incidents = $query->paginate(10);

        return view('admin.incidents.index', compact('incidents'));
    }

    /**
     * Show the form for creating a new incident
     */
    public function create()
    {
        return view('admin.incidents.create');
    }

    /**
     * Display the specified incident
     */
    public function show(Incident $incident)
    {
        // Check if incident belongs to the same company
        if ($incident->company_id !== Auth::user()->company_id) {
            abort(403, 'Unauthorized action.');
        }

        return view('admin.incidents.show', compact('incident'));
    }

    /**
     * Show the form for editing the incident
     */
    public function edit(Incident $incident)
    {
        // Check if incident belongs to the same company
        if ($incident->company_id !== Auth::user()->company_id) {
            abort(403, 'Unauthorized action.');
        }

        return view('admin.incidents.edit', compact('incident'));
    }

    /**
     * Export incidents data to CSV
     */
    public function export(Request $request)
    {
        // Only export incidents from the same company
        $companyId = Auth::user()->company_id;
        $query = Incident::where('company_id', $companyId)->with('user');

        // Apply same filters as incidents page
        if ($request->has('date_from') && $request->date_from) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->has('date_to') && $request->date_to) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }
        if ($request->has('location') && $request->location) {
            $query->where('location', 'like', '%' . $request->location . '%');
        }

        $incidents = $query->get();

        $filename = 'incidents_export_' . date('Y-m-d_H-i-s') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($incidents) {
            $file = fopen('php://output', 'w');
            
            // Add BOM for UTF-8 encoding (Excel compatibility)
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));
            
            // CSV headers
            fputcsv($file, [
                'ID',
                'Title',
                'Content',
                'Location',
                'Occurred At',
                'User Name',
                'User Email',
                'Status',
                'Created At',
                'Updated At'
            ]);

            // CSV data
            foreach ($incidents as $incident) {
                fputcsv($file, [
                    $incident->id,
                    $incident->title,
                    $incident->content,
                    $incident->location,
                    $incident->occurred_at?->format('Y-m-d H:i:s'),
                    $incident->user->name,
                    $incident->user->email,
                    $incident->status ?? 'pending',
                    $incident->created_at->format('Y-m-d H:i:s'),
                    $incident->updated_at->format('Y-m-d H:i:s')
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
