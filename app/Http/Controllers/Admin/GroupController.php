<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class GroupController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $groups = \App\Models\Group::with(['branch.school', 'academicYear'])->get();
        return view('admin.groups.index', compact('groups'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $branches = \App\Models\Branch::with('school')->get();
        $academicYears = \App\Models\AcademicYear::all();
        return view('admin.groups.create', compact('branches', 'academicYears'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'branch_id' => 'required|exists:branches,id',
            'academic_year_id' => 'required|exists:academic_years,id',
        ]);

        \App\Models\Group::create($request->all());

        return redirect()->route('admin.groups.index')->with('success', 'Group created successfully.');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string',
            'branch_id' => 'required|exists:branches,id',
            'academic_year_id' => 'required|exists:academic_years,id',
        ]);
        $group = \App\Models\Group::findOrFail($id);
        $group->update($request->all());
        return redirect()->route('admin.groups.index')->with('success', 'Group updated.');
    }

    public function importStudents(Request $request, $id)
    {
        $request->validate([
            'csv_file' => 'required|file|mimes:csv,txt',
        ]);

        $group = \App\Models\Group::findOrFail($id);
        $file = $request->file('csv_file');
        $handle = fopen($file->getRealPath(), 'r');
        
        // Skip header
        fgetcsv($handle);

        $imported = 0;
        while (($data = fgetcsv($handle)) !== FALSE) {
            // CSV format: first_name, last_name, age, massar_code
            if (count($data) >= 4) {
                \App\Models\User::updateOrCreate(
                    ['massar_code' => $data[3]],
                    [
                        'first_name' => $data[0],
                        'last_name'  => $data[1],
                        'age'        => $data[2],
                        'group_id'   => $group->id,
                        'role'       => 'student',
                        'username'   => $data[3], // Default to massar_code
                        'password'   => \Illuminate\Support\Facades\Hash::make($data[3]), // Default to massar_code
                        'is_first_login' => true,
                    ]
                );
                $imported++;
            }
        }

        fclose($handle);

        return redirect()->back()->with('success', "Successfully imported $imported students.");
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $group = \App\Models\Group::with(['branch.school', 'academicYear', 'users' => function($q) {
            $q->where('role', 'student');
        }])->findOrFail($id);
        
        return view('admin.groups.show', compact('group'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        \App\Models\Group::findOrFail($id)->delete();
        return redirect()->route('admin.groups.index')->with('success', 'Group deleted.');
    }
}
