@extends('layouts.admin')

@section('header', 'Create New Group')

@section('content')
<div class="bg-white p-6 rounded-lg border border-gray-200 max-w-2xl">
    <form action="{{ route('admin.groups.store') }}" method="POST" class="space-y-8">
        @csrf
        
        <div>
            <x-input-label for="name" value="Group Name" />
            <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" required placeholder="e.g. Group A, 1ère Année..." />
        </div>

        <div>
            <x-input-label for="branch_id" value="Select Branch" />
            <select name="branch_id" id="branch_id" class="block w-full mt-1 bg-gray-50 border border-gray-200 rounded-lg py-2.5 px-4 text-gray-700 focus:border-indigo-600 focus:ring-0">
                @foreach($branches as $branch)
                    <option value="{{ $branch->id }}">{{ $branch->name }} ({{ $branch->school->name }})</option>
                @endforeach
            </select>
        </div>

        <div>
            <x-input-label for="academic_year_id" value="Academic Year" />
            <select name="academic_year_id" id="academic_year_id" class="block w-full mt-1 bg-gray-50 border border-gray-200 rounded-lg py-2.5 px-4 text-gray-700 focus:border-indigo-600 focus:ring-0">
                @foreach($academicYears as $year)
                    <option value="{{ $year->id }}">{{ $year->name }}</option>
                @endforeach
            </select>
        </div>

        <div class="flex gap-3 pt-4">
            <a href="{{ route('admin.groups.index') }}" class="flex-1 py-2.5 text-center rounded-lg bg-gray-100 text-gray-600 font-bold hover:bg-gray-200">Annuler</a>
            <x-primary-button class="flex-1 justify-center py-2.5">
                Create Group
            </x-primary-button>
        </div>
    </form>
</div>
@endsection
