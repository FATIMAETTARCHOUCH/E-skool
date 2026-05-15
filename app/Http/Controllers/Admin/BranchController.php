<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class BranchController extends Controller
{
    public function index()
    {
        $branches = \App\Models\Branch::with('school')->get();
        $schools = \App\Models\School::all();
        return view('admin.branches.index', compact('branches', 'schools'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'school_id' => 'required|exists:schools,id'
        ]);
        \App\Models\Branch::create($request->all());
        return redirect()->back()->with('success', 'Branch created successfully.');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'school_id' => 'required|exists:schools,id'
        ]);
        $branch = \App\Models\Branch::findOrFail($id);
        $branch->update($request->all());
        return redirect()->back()->with('success', 'Branch updated.');
    }

    public function destroy($id)
    {
        \App\Models\Branch::findOrFail($id)->delete();
        return redirect()->back()->with('success', 'Branch deleted.');
    }
}
