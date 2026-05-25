@extends('layouts.admin')

@section('header', 'Gestion des Cours')

@section('content')
<div class="space-y-6">
    <div class="bg-white p-6 rounded-lg border border-gray-200 flex flex-col sm:flex-row sm:items-center gap-4">
        <input type="text" id="courseSearch" placeholder="Rechercher des cours..." class="flex-1 bg-gray-50 border border-gray-200 rounded-lg px-4 py-2.5 text-sm text-gray-700 placeholder-gray-400 focus:outline-none focus:border-indigo-400 focus:bg-white transition">
        <a href="{{ route('admin.courses.create') }}" class="px-4 py-2.5 rounded-lg bg-indigo-600 text-white font-bold text-sm hover:bg-indigo-700 transition-colors whitespace-nowrap">+ Créer un Cours</a>
    </div>

    <!-- Courses Table -->
    <div class="bg-white rounded-lg border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead>
                    <tr class="text-gray-500 text-xs uppercase tracking-wide font-medium border-b border-gray-100 bg-gray-50">
                        <th class="px-6 py-3">Titre</th>
                        <th class="px-6 py-3">Groupes</th>
                        <th class="px-6 py-3">Chapitres</th>
                        <th class="px-6 py-3 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($courses as $course)
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-6 py-4">
                            <div class="font-bold text-gray-900">{{ $course->title }}</div>
                            <div class="text-xs text-gray-500 mt-0.5">{{ Str::limit($course->description ?? '', 50) }}</div>
                        </td>
                        <td class="px-6 py-4 text-sm">
                            @if($course->groups->count())
                                <div class="flex gap-2 flex-wrap">
                                    @foreach($course->groups->take(3) as $group)
                                    <span class="px-2 py-1 rounded-md bg-blue-50 text-blue-700 text-xs font-medium border border-blue-200">{{ $group->name }}</span>
                                    @endforeach
                                    @if($course->groups->count() > 3)
                                    <span class="px-2 py-1 rounded-md bg-gray-50 text-gray-600 text-xs font-medium border border-gray-200">+{{ $course->groups->count() - 3 }}</span>
                                    @endif
                                </div>
                            @else
                                <span class="text-gray-400 italic">—</span>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            <span class="px-3 py-1 rounded-md bg-indigo-50 text-indigo-700 text-xs font-medium border border-indigo-200">{{ $course->chapters->count() }}</span>
                        </td>
                        <td class="px-6 py-4 text-right">
                            <div class="flex justify-end gap-2">
                                <a href="{{ route('admin.courses.show', $course) }}" class="px-3 py-1.5 rounded-md bg-indigo-50 text-indigo-600 hover:bg-indigo-100 text-xs font-bold uppercase border border-indigo-200 transition" title="Gérer les chapitres">
                                    Chapitres
                                </a>
                                <a href="{{ route('admin.courses.edit', $course) }}" class="px-3 py-1.5 rounded-md bg-indigo-50 text-indigo-600 hover:bg-indigo-100 text-xs font-bold uppercase border border-indigo-200 transition">
                                    Modifier
                                </a>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="px-6 py-12 text-center text-gray-400 italic">Aucun cours créé</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
