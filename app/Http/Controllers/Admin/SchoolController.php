<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class SchoolController extends Controller
{
    public function index()
    {
        $schools = \App\Models\School::all();
        return view('admin.schools.index', compact('schools'));
    }

    public function store(Request $request)
    {
        $request->validate(['name' => 'required|string|max:255']);
        \App\Models\School::create($request->all());
        return redirect()->back()->with('success', 'School created successfully.');
    }

    public function update(Request $request, $id)
    {
        $request->validate(['name' => 'required|string|max:255']);
        $school = \App\Models\School::findOrFail($id);
        $school->update($request->all());
        return redirect()->back()->with('success', 'School updated.');
    }

    public function destroy($id)
    {
        \App\Models\School::findOrFail($id)->delete();
        return redirect()->back()->with('success', 'School deleted.');
    }
}
