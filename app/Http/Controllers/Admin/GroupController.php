<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

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
            'xlsx_file' => 'required|file|mimes:xlsx',
        ]);

        $group = \App\Models\Group::findOrFail($id);
        $file = $request->file('xlsx_file');
        
        // Read Excel file
        $rows = Excel::toArray([], $file);
        
        $imported = 0;
        // Process first sheet
        if (!empty($rows[0])) {
            foreach ($rows[0] as $index => $row) {
                // Skip header row (first row)
                if ($index === 0) {
                    continue;
                }
                
                // Excel format support: [first_name, last_name, massar_code] OR [first_name, last_name, age, massar_code]
                $massarCode = '';
                if (count($row) >= 4 && !empty($row[3])) {
                    $massarCode = trim($row[3]);
                } elseif (count($row) >= 3 && !empty($row[2])) {
                    $massarCode = trim($row[2]);
                }
                
                if (!empty($massarCode)) {
                    \App\Models\User::updateOrCreate(
                        ['massar_code' => $massarCode],
                        [
                            'first_name' => trim($row[0]),
                            'last_name'  => trim($row[1]),
                            'group_id'   => $group->id,
                            'role'       => 'student',
                            'username'   => $massarCode,
                            'password'   => \Illuminate\Support\Facades\Hash::make($massarCode),
                            'is_first_login' => true,
                        ]
                    );
                    $imported++;
                }
            }
        }

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
