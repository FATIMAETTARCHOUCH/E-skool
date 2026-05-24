@extends('layouts.admin')

@section('header', 'Modifier le Cours')

@section('content')
<div class="bg-white p-6 rounded-lg border border-gray-200 max-w-3xl">
    <form action="{{ route('admin.courses.update', $course) }}" method="POST" class="space-y-6">
        @csrf
        @method('PUT')
        <div>
            <x-input-label value="courses.title - Course Title" />
            <x-text-input name="title" :value="$course->title" class="w-full mt-1" required />
            @error('title')
                <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>
        
        <div>
            <x-input-label value="courses.description - Description" />
            <textarea name="description" rows="4" class="w-full mt-1 bg-gray-50 border border-gray-200 rounded-lg py-3 px-4">{{ $course->description }}</textarea>
            @error('description')
                <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <x-input-label value="course_group (Assign to Groups)" class="mb-2" />
            <div class="bg-white p-3 rounded-lg border border-gray-200 max-h-48 overflow-y-auto space-y-2">
                @foreach($groups as $group)
                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="checkbox" name="group_ids[]" value="{{ $group->id }}" 
                        {{ $course->groups->contains($group->id) ? 'checked' : '' }}
                        class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-600">
                    <span class="text-sm font-bold text-gray-700">{{ $group->name }}</span>
                </label>
                @endforeach
            </div>
            @error('group_ids')
                <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div class="flex gap-3 pt-4">
            <a href="{{ route('admin.courses.index') }}" class="flex-1 py-2.5 text-center rounded-lg bg-gray-100 text-gray-600 font-bold hover:bg-gray-200">Cancel</a>
            <button type="submit" class="flex-1 py-2.5 rounded-lg bg-indigo-600 text-white font-bold hover:bg-indigo-700">Update Course (courses)</button>
        </div>
    </form>
</div>
@endsection
