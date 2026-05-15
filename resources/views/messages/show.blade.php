@extends(auth()->user()->role === 'admin' ? 'layouts.admin' : 'layouts.app')

@section('header')
    <div class="flex items-center gap-6">
        <a href="{{ route('messages.index') }}" class="w-12 h-12 rounded-2xl glass flex items-center justify-center text-slate-500 hover:text-brand-600 transition-all border border-white/40 shadow-sm">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path d="M15 19l-7-7 7-7"></path></svg>
        </a>
        <div class="flex items-center gap-4">
            <div class="w-14 h-14 rounded-2xl bg-brand-50 text-brand-600 flex items-center justify-center font-black text-lg border border-brand-100 uppercase">
                {{ substr($contact->first_name, 0, 1) }}{{ substr($contact->last_name, 0, 1) }}
            </div>
            <div>
                <h2 class="text-2xl font-black text-slate-900 tracking-tight">{{ $contact->first_name }} {{ $contact->last_name }}</h2>
                <div class="flex items-center gap-2 mt-0.5">
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                    <p class="text-[9px] text-slate-400 font-bold uppercase tracking-widest">{{ $contact->role }} Profile</p>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('content')
<div class="max-w-5xl mx-auto flex flex-col h-[calc(100vh-280px)] pb-10">
    
    <!-- Messages Scroll Area -->
    <div class="flex-1 overflow-y-auto p-10 rounded-[3.5rem] bg-white/30 backdrop-blur-md border border-white/60 shadow-inner space-y-8 custom-scrollbar" id="chat-container">
        @forelse($messages as $message)
        <div class="flex {{ $message->sender_id === auth()->id() ? 'justify-end' : 'justify-start' }} animate-in fade-in slide-in-from-bottom-4 duration-500">
            <div class="max-w-[75%] space-y-2">
                <div class="p-6 rounded-[2.5rem] {{ $message->sender_id === auth()->id() 
                    ? 'bg-brand-600 text-white shadow-glow rounded-tr-none' 
                    : 'glass text-slate-800 shadow-glass rounded-tl-none border border-white/80' }}">
                    <p class="text-base font-medium leading-relaxed leading-snug">{{ $message->content }}</p>
                </div>
                <p class="text-[8px] font-black uppercase tracking-widest {{ $message->sender_id === auth()->id() ? 'text-brand-400 text-right mr-4' : 'text-slate-400 ml-4' }}">
                    {{ $message->created_at->format('H:i') }} • {{ $message->created_at->diffForHumans() }}
                </p>
            </div>
        </div>
        @empty
        <div class="h-full flex flex-col items-center justify-center space-y-6">
            <div class="w-20 h-20 bg-slate-50 rounded-full flex items-center justify-center text-slate-200">
                <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z"></path></svg>
            </div>
            <p class="text-sm font-black text-slate-300 uppercase tracking-[0.3em] italic">Encrypted channel opened</p>
        </div>
        @endforelse
    </div>

    <!-- Interactive Input -->
    <div class="mt-8 glass p-4 rounded-[2.5rem] border border-white/60 shadow-glass">
        <form action="{{ route('messages.send', $contact->id) }}" method="POST" class="flex gap-4 items-center">
            @csrf
            <div class="flex-1 relative">
                <input type="text" name="content" autocomplete="off" required placeholder="Draft your message..." class="w-full bg-white/40 border border-slate-100 focus:border-brand-500 focus:ring-8 focus:ring-brand-500/5 rounded-3xl py-5 px-8 text-slate-700 font-bold placeholder-slate-300 transition-all outline-none">
            </div>
            <button type="submit" class="w-16 h-16 rounded-[1.5rem] bg-slate-900 text-white shadow-2xl flex items-center justify-center hover:bg-brand-600 transition-all group active:scale-90">
                <svg class="w-6 h-6 group-hover:translate-x-1 group-hover:-translate-y-1 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path></svg>
            </button>
        </form>
    </div>

</div>

<style>
    .custom-scrollbar::-webkit-scrollbar { width: 4px; }
    .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
    .custom-scrollbar::-webkit-scrollbar-thumb { background: rgba(99, 102, 241, 0.1); border-radius: 10px; }
</style>

<script>
    const container = document.getElementById('chat-container');
    container.scrollTop = container.scrollHeight;
</script>
@endsection
