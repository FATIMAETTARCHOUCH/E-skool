@extends('layouts.admin')

@section('header', 'Course Management')

@section('content')
<div class="grid grid-cols-1 gap-8">
    
    <!-- Header Section with Search and Action -->
    <div class="flex justify-between items-center p-8 rounded-[2rem] shadow-neumorphic bg-neu-base">
        <div>
            <h3 class="text-xl font-bold text-gray-800">Total Chapters: {{ $chapters->count() }}</h3>
            <p class="text-sm text-gray-500">Manage your course content, uploads, and assignments.</p>
        </div>
        <a href="{{ route('admin.chapters.create') }}" class="px-8 py-3 rounded-2xl shadow-neumorphic-btn hover:shadow-neumorphic-btn-active font-bold text-primary transition duration-200">
            Create New Chapter
        </a>
    </div>

    <!-- Chapters Grid/Table -->
    <div class="p-8 rounded-[2rem] shadow-neumorphic bg-neu-base">
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead>
                    <tr class="text-gray-500 uppercase text-xs font-bold border-b border-gray-200">
                        <th class="px-6 py-4">Title & Group</th>
                        <th class="px-6 py-4">Attachments</th>
                        <th class="px-6 py-4">Type</th>
                        <th class="px-6 py-4">Tag</th>
                        <th class="px-6 py-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($chapters as $chapter)
                    <tr class="hover:bg-gray-50/50 transition duration-150">
                        <td class="px-6 py-5">
                            <div class="font-black text-slate-800 text-lg group-hover:text-brand-600 transition-colors leading-tight">{{ $chapter->title }}</div>
                        </td>
                        <td class="px-6 py-5">
                            <div class="flex gap-2">
                                @if($chapter->pdf_path)
                                <div class="w-8 h-8 rounded-lg shadow-neumorphic-inset flex items-center justify-center text-red-500" title="PDF Available">
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path d="M4 18h12V6h-4V2H4v16zm4-7h4v2H8v-2z"></path></svg>
                                </div>
                                @endif
                                @if($chapter->video_path)
                                <div class="w-8 h-8 rounded-lg shadow-neumorphic-inset flex items-center justify-center text-indigo-500" title="Video Available">
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path d="M10 18a8 8 0 100-16 8 8 0 000 16zM9.555 7.168A1 1 0 008 8v4a1 1 0 001.555.832l3-2a1 1 0 000-1.664l-3-2z"></path></svg>
                                </div>
                                @endif
                                @if($chapter->image_path)
                                <div class="w-8 h-8 rounded-lg shadow-neumorphic-inset flex items-center justify-center text-emerald-500" title="Image Available">
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path d="M4 3a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V5a2 2 0 00-2-2H4zm12 12H4l4-8 3 6 2-4 3 6z"></path></svg>
                                </div>
                                @endif
                                @if(!$chapter->pdf_path && !$chapter->video_path && !$chapter->image_path)
                                <span class="text-xs text-gray-400 italic">None</span>
                                @endif
                            </div>
                        </td>
                        <td class="px-6 py-5 text-sm">
                            <span class="px-3 py-1 rounded-full bg-slate-100 text-xs font-bold text-gray-700">
                                {{ ucfirst($chapter->type ?? 'normal') }}
                            </span>
                        </td>
                        <td class="px-6 py-5 text-sm">
                            <span class="px-3 py-1 rounded-full bg-neu-base shadow-neumorphic-inset text-xs font-bold text-gray-600">
                                {{ $chapter->tag ?: 'Untagged' }}
                            </span>
                        </td>
                        <td class="px-6 py-5 text-right flex justify-end gap-3">
                            <!-- Edit Button -->
                            <a href="{{ route('admin.chapters.edit', $chapter->id) }}" class="w-10 h-10 rounded-xl bg-brand-50 text-brand-600 hover:bg-brand-600 hover:text-white transition-all duration-300 flex items-center justify-center">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                            </a>
                            <form action="{{ route('admin.chapters.destroy', $chapter->id) }}" method="POST" onsubmit="return confirm('Are you sure?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="p-2 rounded-lg shadow-neumorphic-btn text-red-500 hover:shadow-neumorphic-btn-active transition duration-200">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                </button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="px-6 py-10 text-center text-gray-500 italic rounded-xl shadow-neumorphic-inset mt-4">
                            No chapters found. Click "Create New Chapter" to get started.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection
