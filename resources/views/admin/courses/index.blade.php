@extends('layouts.admin')

@section('header', 'Cours')

@section('content')
<div class="glass p-10 rounded-[3rem] border border-white/60 shadow-glass">
    <div class="flex justify-between items-center mb-8">
        <h3 class="text-2xl font-black text-slate-800">Gestion des Cours</h3>
        <a href="{{ route('admin.courses.create') }}" class="px-6 py-3 rounded-2xl bg-brand-600 text-white font-bold shadow-glow hover:bg-brand-500 transition-colors">Créer un Cours</a>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-left">
            <thead>
                <tr class="text-slate-400 text-xs uppercase tracking-widest font-black border-b border-slate-200">
                    <th class="pb-4 pl-4">Titre</th>
                    <th class="pb-4">Groupes</th>
                    <th class="pb-4 text-right pr-4">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @foreach($courses as $course)
                <tr class="group hover:bg-white/40 transition-colors">
                    <td class="py-4 pl-4 font-bold text-slate-700">{{ $course->title }}</td>
                    <td class="py-4 text-sm text-slate-500">{{ $course->groups->pluck('name')->join(', ') }}</td>
                    <td class="py-4 pr-4 flex justify-end gap-3 opacity-0 group-hover:opacity-100 transition-opacity">
                        <a href="{{ route('admin.courses.show', $course) }}" class="text-brand-600 hover:text-brand-700 font-bold text-xs uppercase">Gérer les Parties</a>
                        <a href="{{ route('admin.courses.edit', $course) }}" class="text-indigo-600 hover:text-indigo-700 font-bold text-xs uppercase">Modifier</a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
