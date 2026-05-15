@extends('layouts.admin')

@section('header', 'Créer un Cours')

@section('content')
<div class="glass p-10 rounded-[3rem] border border-white/60 shadow-glass max-w-3xl">
    <form action="{{ route('admin.courses.store') }}" method="POST" class="space-y-6">
        @csrf
        <div>
            <x-input-label value="Titre du Cours" />
            <x-text-input name="title" class="w-full mt-1" required />
            @error('title')
                <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>
        
        <div>
            <x-input-label value="Description" />
            <textarea name="description" rows="4" class="w-full mt-1 bg-white/50 border border-slate-200 rounded-2xl py-3 px-4"></textarea>
            @error('description')
                <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <x-input-label value="Assigner aux Groupes" class="mb-2" />
            <div class="glass p-4 rounded-2xl border border-white/40 max-h-48 overflow-y-auto space-y-2">
                @foreach($groups as $group)
                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="checkbox" name="group_ids[]" value="{{ $group->id }}" class="rounded border-slate-300 text-brand-600 focus:ring-brand-500">
                    <span class="text-sm font-bold text-slate-700">{{ $group->name }}</span>
                </label>
                @endforeach
            </div>
            @error('group_ids')
                <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div class="flex gap-4 pt-4">
            <a href="{{ route('admin.courses.index') }}" class="flex-1 py-3 text-center rounded-2xl bg-slate-100 text-slate-600 font-bold hover:bg-slate-200">Annuler</a>
            <button type="submit" class="flex-1 py-3 rounded-2xl bg-brand-600 text-white font-bold shadow-glow hover:bg-brand-500">Créer le Cours</button>
        </div>
    </form>
</div>
@endsection
