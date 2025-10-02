<?php

namespace App\Http\Controllers;

use App\Models\Incident;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class IncidentController extends Controller
{
    use AuthorizesRequests;

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('incidents.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'content' => ['required', 'string'],
            'location' => ['required', 'string', 'max:255'],
            'occurred_at' => ['required', 'date'],
            'severity' => ['nullable', 'in:low,medium,high,critical'],
            'immediate_action' => ['nullable', 'in:no,yes'],
            'images.*' => ['nullable', 'image', 'max:5120'],
        ]);

        $imagePaths = [];
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $imagePaths[] = $image->store('incidents', 'public');
            }
        }

        $incident = Incident::create([
            'user_id' => Auth::id(),
            'company_id' => Auth::user()->company_id,
            'title' => $validated['title'],
            'content' => $validated['content'] ?? null,
            'location' => $validated['location'] ?? null,
            'occurred_at' => $validated['occurred_at'] ?? null,
            'severity' => $validated['severity'] ?? 'low',
            'immediate_action' => $validated['immediate_action'] ?? 'no',
            'images' => !empty($imagePaths) ? $imagePaths : null,
        ]);

        // Redirect back to create page with success message
        return redirect()->route('incidents.create')->with('success', __('messages.Incident reported successfully. Thank you for your report!'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Incident $incident)
    {
        $this->authorize('update', $incident);

        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'content' => ['required', 'string'],
            'location' => ['required', 'string', 'max:255'],
            'occurred_at' => ['required', 'date'],
            'severity' => ['nullable', 'in:low,medium,high,critical'],
            'immediate_action' => ['nullable', 'in:no,yes'],
            'images.*' => ['nullable', 'image', 'max:5120'],
            'delete_images' => ['nullable', 'array'],
        ]);

        // Get current images or empty array
        $imagePaths = $incident->images ?? [];

        // Remove deleted images
        if ($request->has('delete_images')) {
            $imagePaths = array_values(array_diff($imagePaths, $request->delete_images));
        }

        // Add new images
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $imagePaths[] = $image->store('incidents', 'public');
            }
        }

        $incident->update([
            'title' => $validated['title'],
            'content' => $validated['content'] ?? null,
            'location' => $validated['location'] ?? null,
            'occurred_at' => $validated['occurred_at'] ?? null,
            'severity' => $validated['severity'] ?? 'low',
            'immediate_action' => $validated['immediate_action'] ?? 'no',
            'images' => !empty($imagePaths) ? $imagePaths : null,
        ]);

        return redirect()->route('admin.incidents.show', $incident)->with('status', __('admin.Incident Updated'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Incident $incident)
    {
        $this->authorize('delete', $incident);
        $incident->delete();
        return redirect()->route('admin.incidents.index')->with('status', __('admin.Incident Deleted'));
    }

    /**
     * Update the status of the incident.
     */
    public function updateStatus(Request $request, Incident $incident)
    {
        $validated = $request->validate([
            'status' => ['required', 'in:pending,investigating,resolved,closed'],
        ]);

        $incident->update([
            'status' => $validated['status'],
        ]);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => __('admin.Status Updated'),
                'status' => $incident->status,
            ]);
        }

        return redirect()->back()->with('status', __('admin.Status Updated'));
    }
}
