@extends(auth()->user()->role === 'admin' ? 'layouts.admin' : 'layouts.app')

@section('header')
    <div class="flex items-center justify-between gap-6">
        <div class="flex items-center gap-6">
            <div class="w-16 h-16 rounded-2xl bg-brand-600 text-white flex items-center justify-center shadow-glow">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"></path></svg>
            </div>
            <div>
                <h2 class="text-3xl font-black text-slate-900 tracking-tight italic">Secure <span class="text-brand-600">Messages</span></h2>
                <p class="text-xs text-slate-400 font-bold uppercase tracking-widest mt-1">Direct Communication Channel</p>
            </div>
        </div>
        @if(auth()->user()->role === 'admin')
        <button onclick="document.getElementById('new-chat-modal').classList.remove('hidden')" class="px-8 py-4 rounded-2xl bg-brand-600 text-white font-black text-xs shadow-glow hover:bg-brand-500 transition-all uppercase tracking-widest">
            START NEW CHAT
        </button>
        @endif
    </div>
@endsection

@section('content')
<div class="max-w-5xl mx-auto space-y-10 pb-24">
    
    <!-- New Chat Search Modal (Admin Only) -->
    @if(auth()->user()->role === 'admin')
    <div id="new-chat-modal" class="{{ request()->filled('search') || request()->filled('group_id') ? '' : 'hidden' }} fixed inset-0 z-50 flex items-center justify-center p-4 backdrop-blur-xl bg-slate-900/40">
        <div class="glass p-10 rounded-[3rem] shadow-2xl max-w-2xl w-full border border-white/60 flex flex-col max-h-[90vh]">
            <div class="flex justify-between items-center mb-8">
                <h4 class="text-2xl font-black text-slate-900 uppercase tracking-tighter italic">Initiate <span class="text-brand-600">Conversation</span></h4>
                <a href="{{ route('messages.index') }}" class="text-slate-400 hover:text-slate-600">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path d="M6 18L18 6M6 6l12 12"></path></svg>
                </a>
            </div>

            <!-- Search Interface -->
            <form action="{{ route('messages.index') }}" method="GET" class="grid grid-cols-1 md:grid-cols-12 gap-4 mb-8">
                <div class="md:col-span-7">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by name or massar..." class="w-full bg-white/40 border border-slate-200 rounded-2xl py-4 px-6 text-slate-700 font-bold outline-none focus:ring-4 focus:ring-brand-500/5">
                </div>
                <div class="md:col-span-3">
                    <select name="group_id" class="w-full bg-white/40 border border-slate-200 rounded-2xl py-4 px-4 text-slate-700 font-bold outline-none">
                        <option value="">All Groups</option>
                        @foreach($groups as $group)
                            <option value="{{ $group->id }}" {{ request('group_id') == $group->id ? 'selected' : '' }}>{{ $group->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="md:col-span-2">
                    <button type="submit" class="w-full h-full bg-slate-900 text-white rounded-2xl flex items-center justify-center hover:bg-brand-600 transition-all">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                    </button>
                </div>
            </form>

            <!-- Search Results -->
            <div class="flex-1 overflow-y-auto space-y-4 pr-2 custom-scrollbar">
                @forelse($searchStudents as $student)
                <a href="{{ route('messages.show', $student->id) }}" class="flex items-center justify-between p-5 rounded-[2rem] bg-white/40 border border-white/60 hover:bg-white/80 hover:border-brand-300 transition-all group">
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 rounded-xl bg-brand-50 text-brand-600 flex items-center justify-center font-black text-sm border border-brand-100 uppercase">
                            {{ substr($student->first_name, 0, 1) }}{{ substr($student->last_name, 0, 1) }}
                        </div>
                        <div>
                            <p class="font-black text-slate-800 leading-tight group-hover:text-brand-600 transition-colors">{{ $student->first_name }} {{ $student->last_name }}</p>
                            <p class="text-[9px] text-slate-400 font-bold uppercase tracking-widest">{{ $student->group->name ?? 'No Group' }} • {{ $student->massar_code }}</p>
                        </div>
                    </div>
                    <svg class="w-5 h-5 text-slate-300 group-hover:text-brand-600 transition-all" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 7l5 5m0 0l-5 5m5-5H6"></path></svg>
                </a>
                @empty
                    @if(request()->filled('search') || request()->filled('group_id'))
                    <div class="py-10 text-center italic text-slate-400 font-bold uppercase text-xs tracking-widest">No matching students found</div>
                    @else
                    <div class="py-10 text-center italic text-slate-400 font-bold uppercase text-xs tracking-widest italic opacity-50">Enter parameters to begin sync</div>
                    @endif
                @endforelse
            </div>
        </div>
    </div>
    @endif

    <div class="glass p-10 rounded-[3rem] border border-white/60 shadow-glass overflow-hidden relative">
        <div class="absolute top-0 right-0 w-64 h-64 bg-brand-600/5 rounded-full -mr-32 -mt-32 blur-3xl"></div>
        
        <h3 class="text-sm font-black text-slate-400 uppercase tracking-[0.3em] mb-10 border-b border-slate-100/50 pb-6">Recent Conversations</h3>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            @forelse($contacts as $contact)
            <a href="{{ route('messages.show', $contact->id) }}" class="group relative p-8 rounded-[2.5rem] bg-white/40 border border-white/60 hover:border-brand-300 transition-all duration-500 shadow-sm hover:shadow-glow flex items-center justify-between overflow-hidden">
                <div class="absolute inset-0 bg-brand-600 translate-y-full group-hover:translate-y-0 transition-transform duration-500 opacity-[0.02]"></div>
                
                <div class="flex items-center gap-6 relative z-10">
                    <div class="w-16 h-16 rounded-2xl bg-brand-50 text-brand-600 flex items-center justify-center font-black text-xl shadow-inner border border-brand-100 group-hover:scale-110 transition-transform uppercase">
                        {{ substr($contact->first_name, 0, 1) }}{{ substr($contact->last_name, 0, 1) }}
                    </div>
                    <div>
                        <h4 class="text-xl font-black text-slate-900 group-hover:text-brand-600 transition-colors leading-tight">{{ $contact->first_name }} {{ $contact->last_name }}</h4>
                        <div class="flex items-center gap-2 mt-1">
                            <span class="text-[9px] font-black uppercase tracking-widest {{ $contact->role === 'admin' ? 'text-indigo-600 bg-indigo-50' : 'text-brand-600 bg-brand-50' }} px-2 py-0.5 rounded-md">
                                {{ $contact->role }}
                            </span>
                            @if($contact->massar_code)
                                <span class="text-[9px] font-black text-slate-400 uppercase tracking-tighter italic">@ {{ $contact->massar_code }}</span>
                            @endif
                        </div>
                    </div>
                </div>
                
                <div class="w-10 h-10 rounded-full glass border border-white/60 flex items-center justify-center text-slate-300 group-hover:text-brand-600 group-hover:border-brand-200 transition-all relative z-10">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path d="M9 5l7 7-7 7"></path></svg>
                </div>
            </a>
            @empty
            <div class="col-span-full py-32 text-center glass rounded-[3rem] border border-dashed border-slate-300">
                <div class="w-20 h-20 bg-slate-50 rounded-full mx-auto flex items-center justify-center mb-6">
                    <svg class="w-10 h-10 text-slate-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path></svg>
                </div>
                <h4 class="text-2xl font-black text-slate-300 italic tracking-tighter uppercase">Inbox Depleted</h4>
                <p class="text-[10px] text-slate-400 font-bold uppercase tracking-widest mt-2">No active message threads found.</p>
            </div>
            @endforelse
        </div>
    </div>
</div>
@endsection
