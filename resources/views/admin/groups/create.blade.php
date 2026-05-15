@extends('layouts.admin')

@section('header', 'Create New Group')

@section('content')
<div class="max-w-2xl mx-auto p-10 rounded-[2.5rem] shadow-neumorphic bg-neu-base">
    <form action="{{ route('admin.groups.store') }}" method="POST" class="space-y-8">
        @csrf
        
        <div>
            <x-input-label for="name" value="Group Name" />
            <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" required placeholder="e.g. Group A, 1ère Année..." />
        </div>

        <div>
            <x-input-label for="branch_id" value="Select Branch" />
            <select name="branch_id" id="branch_id" class="block w-full mt-1 bg-neu-base border-none rounded-xl shadow-neumorphic-inset py-3 px-4 text-gray-700 focus:ring-0">
                @foreach($branches as $branch)
                    <option value="{{ $branch->id }}">{{ $branch->name }} ({{ $branch->school->name }})</option>
                @endforeach
            </select>
        </div>

        <div>
            <x-input-label for="academic_year_id" value="Academic Year" />
            <select name="academic_year_id" id="academic_year_id" class="block w-full mt-1 bg-neu-base border-none rounded-xl shadow-neumorphic-inset py-3 px-4 text-gray-700 focus:ring-0">
                @foreach($academicYears as $year)
                    <option value="{{ $year->id }}">{{ $year->name }}</option>
                @endforeach
            </select>
        </div>

        <div class="flex gap-4 pt-4">
            <a href="{{ route('admin.groups.index') }}" class="flex-1 py-4 text-center rounded-xl shadow-neumorphic-btn font-bold text-gray-500 transition hover:shadow-neumorphic-btn-active">Annuler</a>
            <x-primary-button class="flex-1 justify-center py-4">
                Create Group
            </x-primary-button>
        </div>
    </form>
</div>
@endsection
