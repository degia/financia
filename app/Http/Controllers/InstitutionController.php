<?php

namespace App\Http\Controllers;

use App\Models\Institution;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class InstitutionController extends Controller
{
    public function index(): View
    {
        $institutions = Institution::orderBy('type')->orderBy('name')->get();
        return view('institutions.index', compact('institutions'));
    }

    public function create(): View
    {
        return view('institutions.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'type' => ['required', 'string', 'in:cash,bank,ewallet,credit_card,savings,other'],
            'color' => ['nullable', 'string', 'max:7'],
        ]);

        $slug = Str::slug($request->name);

        if (Institution::where('slug', $slug)->exists()) {
            $slug = $slug . '-' . uniqid();
        }

        $data = [
            'name' => $request->name,
            'type' => $request->type,
            'color' => $request->color ?? '#6366F1',
            'slug' => $slug,
            'is_active' => true,
        ];

        Institution::create($data);

        return redirect()->route('institutions.index')->with('success', 'Institution created successfully.');
    }

    public function edit(Institution $institution): View
    {
        return view('institutions.edit', compact('institution'));
    }

    public function update(Request $request, Institution $institution): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'type' => ['required', 'string', 'in:cash,bank,ewallet,credit_card,savings,other'],
            'color' => ['nullable', 'string', 'max:7'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $data = [
            'name' => $request->name,
            'type' => $request->type,
            'color' => $request->color ?? '#6366F1',
            'is_active' => $request->boolean('is_active', true),
        ];

        $institution->update($data);

        return redirect()->route('institutions.index')->with('success', 'Institution updated successfully.');
    }

    public function destroy(Institution $institution): RedirectResponse
    {
        if ($institution->accounts()->exists()) {
            return back()->with('error', 'Cannot delete institution with linked accounts. Remove the institution from accounts first.');
        }

        $institution->delete();

        return redirect()->route('institutions.index')->with('success', 'Institution deleted successfully.');
    }
}
