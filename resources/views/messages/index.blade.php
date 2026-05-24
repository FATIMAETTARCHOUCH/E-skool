@extends(auth()->user()->role === 'admin' ? 'layouts.admin' : 'layouts.app')

@section('header')
    <div class="flex items-center justify-between gap-6">
        <div class="flex items-center gap-4">
            <div class="w-12 h-12 rounded-lg bg-indigo-600 text-white flex items-center justify-center">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"></path></svg>
            </div>
            <div>
                <h2 class="text-xl font-bold text-gray-900">Secure Messages</h2>
                <p class="text-sm text-gray-500 font-medium mt-0.5">Direct Communication Channel</p>
            </div>
        </div>
        @if(auth()->user()->role === 'admin')
        <button onclick="document.getElementById('new-chat-modal').classList.remove('hidden')" class="px-4 py-2.5 rounded-lg bg-indigo-600 text-white font-medium text-sm hover:bg-indigo-700 transition-colors">
            Start New Chat
        </button>
        @endif
    </div>
@endsection

@section('content')
<div class="max-w-5xl mx-auto space-y-6">
    
    <!-- New Chat Search Modal (Admin Only) -->
    @if(auth()->user()->role === 'admin')
    <div id="new-chat-modal" class="{{ request()->filled('search') || request()->filled('group_id') ? '' : 'hidden' }} fixed inset-0 z-50 items-center justify-center p-4 bg-black/50 {{ request()->filled('search') || request()->filled('group_id') ? 'flex' : '' }}">
        <div class="bg-white p-6 rounded-lg border border-gray-200 max-w-2xl w-full flex flex-col max-h-[90vh]">
            <div class="flex justify-between items-center mb-6">
                <h4 class="text-lg font-bold text-gray-900 border-l-4 border-indigo-600 pl-3">Initiate Conversation</h4>
                <a href="{{ route('messages.index') }}" class="text-gray-400 hover:text-gray-600 transition-colors">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path d="M6 18L18 6M6 6l12 12"></path></svg>
                </a>
            </div>

            <!-- Search Interface -->
            <form action="{{ route('messages.index') }}" method="GET" class="grid grid-cols-1 md:grid-cols-12 gap-4 mb-6">
                <div class="md:col-span-6">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by name or massar..." class="w-full bg-gray-50 border border-gray-200 rounded-lg py-2.5 px-4 text-gray-700 text-sm focus:outline-none focus:border-indigo-400 focus:ring-4 focus:ring-indigo-100 transition-all">
                </div>
                <div class="md:col-span-4">
                    <select name="group_id" class="w-full bg-gray-50 border border-gray-200 rounded-lg py-2.5 px-4 text-gray-700 text-sm outline-none focus:border-indigo-400 focus:ring-4 focus:ring-indigo-100 transition-all cursor-pointer">
                        <option value="">All Groups</option>
                        @foreach($groups as $group)
                            <option value="{{ $group->id }}" {{ request('group_id') == $group->id ? 'selected' : '' }}>{{ $group->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="md:col-span-2">
                    <button type="submit" class="w-full h-full bg-gray-900 text-white rounded-lg flex items-center justify-center hover:bg-gray-800 transition-all font-medium py-2.5">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                    </button>
                </div>
            </form>

            <!-- Search Results -->
            <div class="flex-1 overflow-y-auto space-y-3 pr-2 border-t border-gray-100 pt-4">
                @forelse($searchStudents as $student)
                <a href="{{ route('messages.show', $student->id) }}" class="flex items-center justify-between p-4 rounded-lg bg-white border border-gray-200 hover:bg-gray-50 hover:border-indigo-300 transition-all group">
                    <div class="flex items-center gap-4">
                        <div class="w-10 h-10 rounded-md bg-indigo-50 text-indigo-600 flex items-center justify-center font-bold text-sm border border-indigo-100 uppercase">
                            {{ substr($student->first_name, 0, 1) }}{{ substr($student->last_name, 0, 1) }}
                        </div>
                        <div>
                            <p class="font-bold text-gray-900 leading-tight group-hover:text-indigo-600 transition-colors">{{ $student->first_name }} {{ $student->last_name }}</p>
                            <p class="text-xs text-gray-500 font-medium tracking-wide mt-0.5">{{ $student->group->name ?? 'No Group' }} • {{ $student->massar_code }}</p>
                        </div>
                    </div>
                    <svg class="w-5 h-5 text-gray-300 group-hover:text-indigo-600 transition-all" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path></svg>
                </a>
                @empty
                    @if(request()->filled('search') || request()->filled('group_id'))
                    <div class="py-8 text-center italic text-gray-500 font-medium text-sm">No matching students found</div>
                    @else
                    <div class="py-8 text-center italic text-gray-400 font-medium text-sm opacity-70">Enter parameters to begin sync</div>
                    @endif
                @endforelse
            </div>
        </div>
    </div>
    @endif

    <div class="bg-white p-6 rounded-lg border border-gray-200">
        <h3 class="text-xs font-semibold text-gray-500 uppercase tracking-widest mb-6 border-b border-gray-100 pb-4">Recent Conversations</h3>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            @forelse($contacts as $contact)
            <a href="{{ route('messages.show', $contact->id) }}" class="group relative p-4 rounded-lg bg-white border border-gray-200 hover:border-indigo-300 transition-all duration-300 hover:shadow-sm flex items-center justify-between">
                
                <div class="flex items-center gap-4 relative z-10">
                    <div class="w-12 h-12 rounded-lg bg-indigo-50 text-indigo-600 flex items-center justify-center font-bold text-lg border border-indigo-100 group-hover:scale-105 transition-transform uppercase">
                        {{ substr($contact->first_name, 0, 1) }}{{ substr($contact->last_name, 0, 1) }}
                    </div>
                    <div>
                        <h4 class="text-base font-bold text-gray-900 group-hover:text-indigo-600 transition-colors leading-tight">{{ $contact->first_name }} {{ $contact->last_name }}</h4>
                        <div class="flex items-center gap-2 mt-1">
                            <span class="text-[10px] font-semibold uppercase tracking-wider {{ $contact->role === 'admin' ? 'text-amber-700 bg-amber-50 border border-amber-200' : 'text-indigo-700 bg-indigo-50 border border-indigo-200' }} px-2 py-0.5 rounded-md">
                                {{ $contact->role }}
                            </span>
                            @if($contact->massar_code)
                                <span class="text-[10px] font-medium text-gray-400 uppercase tracking-tighter italic">@ {{ $contact->massar_code }}</span>
                            @endif
                        </div>
                    </div>
                </div>
                
                <div class="w-8 h-8 rounded-full border border-gray-200 flex items-center justify-center text-gray-400 group-hover:text-indigo-600 group-hover:bg-indigo-50 group-hover:border-indigo-200 transition-all">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path d="M9 5l7 7-7 7"></path></svg>
                </div>
            </a>
            @empty
            <div class="col-span-full py-20 text-center bg-gray-50 rounded-lg border-2 border-dashed border-gray-200">
                <div class="w-16 h-16 bg-white border border-gray-100 rounded-full mx-auto flex items-center justify-center mb-4">
                    <svg class="w-8 h-8 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path></svg>
                </div>
                <h4 class="text-xl font-bold text-gray-400 italic">Inbox Depleted</h4>
                <p class="text-sm text-gray-500 mt-2">No active message threads found.</p>
            </div>
            @endforelse
        </div>
    </div>
</div>
@endsection
