<?php

namespace App\Http\Controllers;

use App\Models\Incident;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class IncidentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $query = Incident::with('user')->latest();

        if (request()->routeIs('incidents.index')) {
            // Employee view: show only incidents created by the current user
            if (Auth::check()) {
                $query->where('user_id', Auth::id());
            }
            $incidents = $query->paginate(10);
        } else {
            // Admin index: show all incidents
            $incidents = $query->paginate(20);
        }

        return view('incidents.index', compact('incidents'));
    }

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
            'content' => ['nullable', 'string'],
            'location' => ['nullable', 'string', 'max:255'],
            'occurred_at' => ['nullable', 'date'],
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
            'title' => $validated['title'],
            'content' => $validated['content'] ?? null,
            'location' => $validated['location'] ?? null,
            'occurred_at' => $validated['occurred_at'] ?? null,
            'images' => $imagePaths,
        ]);

        return redirect()->route('incidents.show', $incident)->with('status', 'created');
    }

    /**
     * Display the specified resource.
     */
    public function show(Incident $incident)
    {
        return view('incidents.show', compact('incident'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Incident $incident)
    {
        $this->authorize('update', $incident);
        return view('incidents.edit', compact('incident'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Incident $incident)
    {
        $this->authorize('update', $incident);

        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'content' => ['nullable', 'string'],
            'location' => ['nullable', 'string', 'max:255'],
            'occurred_at' => ['nullable', 'date'],
            'images.*' => ['nullable', 'image', 'max:5120'],
        ]);

        $imagePaths = $incident->images ?? [];
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
            'images' => $imagePaths,
        ]);

        return redirect()->route('incidents.show', $incident)->with('status', 'updated');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Incident $incident)
    {
        $this->authorize('delete', $incident);
        $incident->delete();
        return redirect()->route('incidents.index')->with('status', 'deleted');
    }
}
