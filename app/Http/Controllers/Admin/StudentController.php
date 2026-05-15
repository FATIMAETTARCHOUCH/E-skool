<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Group;
use Illuminate\Http\Request;

class StudentController extends Controller
{
    public function index()
    {
        $students = User::where('role', 'student')->with('group.branch.school')->orderBy('created_at', 'desc')->get();
        $groups = Group::all();
        return view('admin.students.index', compact('students', 'groups'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'massar_code' => 'required|string|unique:users,massar_code',
            'age' => 'required|integer',
            'group_id' => 'required|exists:groups,id',
        ]);

        \App\Models\User::create([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'massar_code' => $request->massar_code,
            'age' => $request->age,
            'group_id' => $request->group_id,
            'role' => 'student',
            'username' => $request->massar_code, // Default username
            'password' => \Illuminate\Support\Facades\Hash::make($request->massar_code), // Default password
            'is_first_login' => true,
        ]);

        return redirect()->back()->with('success', 'Student added manually.');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'massar_code' => 'required|string|unique:users,massar_code,'.$id,
            'age' => 'required|integer',
            'group_id' => 'required|exists:groups,id',
        ]);

        $student = \App\Models\User::where('role', 'student')->findOrFail($id);
        
        $student->update([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'massar_code' => $request->massar_code,
            'age' => $request->age,
            'group_id' => $request->group_id,
            'username' => $request->massar_code, // Sync username if massar_code changes
        ]);

        return redirect()->back()->with('success', 'Student details updated.');
    }

    public function destroy($id)
    {
        $user = User::where('role', 'student')->findOrFail($id);
        $user->delete();
        return redirect()->back()->with('success', 'Student deleted successfully.');
    }
}
