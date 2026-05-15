<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AcademicYearController extends Controller
{
    public function index()
    {
        $academicYears = \App\Models\AcademicYear::orderBy('created_at', 'desc')->get();
        return view('admin.academic_years.index', compact('academicYears'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:academic_years,name',
        ]);

        \App\Models\AcademicYear::create([
            'name' => $request->name,
            'is_active' => $request->has('is_active') ? true : false,
        ]);

        return redirect()->back()->with('success', 'Academic Year created successfully.');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:academic_years,name,'.$id,
        ]);
        $year = \App\Models\AcademicYear::findOrFail($id);
        $year->update($request->only('name'));
        return redirect()->back()->with('success', 'Academic Year updated.');
    }

    public function toggleActive($id)
    {
        $year = \App\Models\AcademicYear::findOrFail($id);
        
        // If we want only one active year at a time:
        if (!$year->is_active) {
            \App\Models\AcademicYear::where('id', '!=', $id)->update(['is_active' => false]);
        }
        
        $year->update(['is_active' => !$year->is_active]);

        return redirect()->back()->with('success', 'Status updated.');
    }

    public function destroy($id)
    {
        \App\Models\AcademicYear::findOrFail($id)->delete();
        return redirect()->back()->with('success', 'Academic Year deleted.');
    }
}
