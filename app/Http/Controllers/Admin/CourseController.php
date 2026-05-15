<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Group;
use Illuminate\Http\Request;

class CourseController extends Controller
{
    public function index()
    {
        $courses = Course::with('groups')->get();
        return view('admin.courses.index', compact('courses'));
    }

    public function create()
    {
        $groups = Group::all();
        return view('admin.courses.create', compact('groups'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'group_ids' => 'required|array',
            'group_ids.*' => 'exists:groups,id'
        ]);

        $course = Course::create($request->only('title', 'description'));
        $course->groups()->attach($request->group_ids);

        return redirect()->route('admin.courses.index')->with('success', 'Cours créé avec succès.');
    }

    public function show(Course $course)
    {
        $course->load('lessons', 'groups');
        return view('admin.courses.show', compact('course'));
    }

    public function edit(Course $course)
    {
        $groups = Group::all();
        return view('admin.courses.edit', compact('course', 'groups'));
    }

    public function update(Request $request, Course $course)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'group_ids' => 'required|array',
            'group_ids.*' => 'exists:groups,id'
        ]);

        $course->update($request->only('title', 'description'));
        $course->groups()->sync($request->group_ids);

        return redirect()->route('admin.courses.index')->with('success', 'Cours mis à jour.');
    }

    public function destroy(Course $course)
    {
        $course->delete();
        return redirect()->route('admin.courses.index')->with('success', 'Cours supprimé.');
    }
}
