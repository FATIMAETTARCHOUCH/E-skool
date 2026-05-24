@extends('layouts.app')

@section('header')
    <div class="max-w-[95%] mx-auto flex items-center gap-6">
        <a href="{{ route('student.course', $course->id) }}" class="w-14 h-14 rounded-2xl glass flex items-center justify-center text-slate-500 hover:text-brand-600 transition-all border border-white/40 shadow-sm">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path d="M15 19l-7-7 7-7"></path></svg>
        </a>
        <div>
            <p class="text-[10px] font-black text-brand-600 uppercase tracking-widest">{{ $course->title }}</p>
            <h2 class="text-3xl font-black text-slate-900 tracking-tight">Partie {{ $lesson->order }} : {{ $lesson->title }}</h2>
        </div>
    </div>
@endsection

@section('content')
<div class="w-full max-w-[95%] mx-auto px-4 sm:px-6 lg:px-8 pb-24">
    
    @if(session('error'))
    <div class="mb-8 p-6 rounded-3xl bg-red-50 border border-red-100 text-red-600 font-bold flex items-center gap-4">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
        {{ session('error') }}
    </div>
    @endif

    @if(session('recommended_lesson_id'))
    <div class="mb-6 p-5 rounded-2xl bg-amber-50 border border-amber-100 text-amber-800 font-bold flex items-center justify-between">
        <div>
            Nous vous recommandons de consulter la version simplifiée suivante avant de réessayer le quiz :
            <div class="mt-2"> <strong>{{ session('recommended_lesson_title') }}</strong> </div>
        </div>
        <div>
            <a href="{{ route('student.lesson', session('recommended_lesson_id')) }}" class="px-4 py-3 rounded-xl bg-amber-600 text-white font-bold">Voir la version simplifiée</a>
        </div>
    </div>
    @endif

    <div class="glass p-6 md:p-8 rounded-[3rem] border border-white/60 shadow-glass relative overflow-hidden">
        
        <!-- Content -->
        <div class="prose prose-slate prose-lg max-w-none prose-headings:font-black prose-a:text-brand-600">
            {!! $lesson->content_text !!}
        </div>

        <!-- Attachments (PDFs, Videos, Images) -->
        @if($lesson->pdfs->count() > 0)
        <div class="mt-12 pt-8 border-t border-slate-200/50">
            <h4 class="text-xs font-black text-slate-400 uppercase tracking-widest mb-6">Fichiers joints</h4>

            <!-- PDF Viewer -->
            <div class="p-6 bg-gray-50">
                @foreach($lesson->pdfs as $pdf)
                <div class="pdf-viewer {{ $loop->first ? '' : 'hidden' }}" data-pdf-id="{{ $pdf->id }}" data-pdf-url="{{ asset('storage/' . $pdf->pdf_path) }}">
                    <div class="mb-4">
                        <h5 class="text-lg font-bold text-slate-800">{{ $pdf->title }}</h5>
                        <p class="text-sm text-slate-500">Lisez attentivement le document ci-dessous. Utilisez la navigation si nécessaire.</p>
                    </div>
                    <div id="pdf-container-{{ $pdf->id }}" class="w-full" style="height: 75vh; max-height:80vh; border: 1px solid #ddd; border-radius: 8px; position: relative; overflow: auto;">
                        <canvas id="pdf-canvas-{{ $pdf->id }}" style="display: block; margin: 0 auto; max-width: none;"></canvas>
                    </div>
                    <div id="pdf-controls-{{ $pdf->id }}" class="flex justify-between items-center mt-4 p-3 bg-white rounded-lg border border-gray-200" style="display:none">
                        <button onclick="prevPage({{ $pdf->id }})" id="pdf-prev-{{ $pdf->id }}" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 rounded-lg text-sm font-bold transition">← Précédent</button>
                        <div class="text-sm font-semibold text-gray-700">
                            <span id="page-count-{{ $pdf->id }}">1</span> / <span id="page-total-{{ $pdf->id }}">?</span>
                        </div>
                        <button onclick="nextPage({{ $pdf->id }})" id="pdf-next-{{ $pdf->id }}" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 rounded-lg text-sm font-bold transition">Suivant →</button>
                    </div>
                    @if($lesson->quizzes->count() > 0)
                    @php $quiz = $lesson->quizzes->first(); @endphp
                    <div class="mt-4 text-center">
                        <p class="text-sm text-slate-600 mb-2">Quand vous avez terminé la lecture, cliquez pour passer le quiz et valider cette partie.</p>
                        <a href="{{ route('student.quiz', $quiz->id) }}" class="inline-block px-8 py-3 rounded-2xl bg-brand-600 text-white font-black uppercase tracking-widest shadow-glow hover:bg-brand-500 transition-all">Passer le Quizz</a>
                    </div>
                    @endif
                </div>
                @endforeach

                @if($lesson->pdfs->count() > 1)
                <div class="mt-4 flex gap-2">
                    @foreach($lesson->pdfs as $pdf)
                    <button onclick="showPdfViewer({{ $pdf->id }})" class="px-3 py-2 rounded-lg bg-white border border-gray-200 text-sm">{{ $pdf->title }}</button>
                    @endforeach
                </div>
                @endif
            </div>
        </div>
        @elseif($lesson->video_path)
        <div class="mt-12 pt-8 border-t border-slate-200/50">
            <h4 class="text-xs font-black text-slate-400 uppercase tracking-widest mb-6">Fichiers joints</h4>
            <div class="p-6 bg-gray-50">
                <video width="100%" height="600" controls style="border-radius: 8px; border: 1px solid #ddd;">
                    <source src="{{ Storage::url($lesson->video_path) }}" type="video/mp4">
                    Your browser does not support the video tag.
                </video>
            </div>
        </div>
        @elseif($lesson->image_path)
        <div class="mt-12 pt-8 border-t border-slate-200/50">
            <h4 class="text-xs font-black text-slate-400 uppercase tracking-widest mb-6">Fichiers joints</h4>
            <div class="p-6 bg-gray-50 text-center">
                <img src="{{ Storage::url($lesson->image_path) }}" alt="{{ $lesson->title }}" style="max-width: 100%; max-height: 600px; border-radius: 8px; border: 1px solid #ddd;">
            </div>
        </div>
        @endif

        <!-- Action Area -->
        <div class="mt-12 pt-8 border-t border-slate-200/50 flex justify-center">
            @php
                $quiz = $lesson->quizzes->first();
            @endphp

            @if($quiz)
                <a href="{{ route('student.quiz', $quiz->id) }}" class="px-10 py-5 rounded-2xl bg-brand-600 text-white font-black uppercase tracking-widest shadow-glow hover:bg-brand-500 transition-all hover:-translate-y-1">
                    Passer le Quizz pour valider
                </a>
            @else
                <div class="px-8 py-4 rounded-2xl bg-slate-100 text-slate-400 font-bold uppercase text-xs italic">
                    Aucun quizz requis pour cette partie
                </div>
            @endif
        </div>

    </div>
</div>

<!-- PDF.js Library and viewer scripts -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.worker.min.js"></script>
<script>
    pdfjsLib.GlobalWorkerOptions.workerSrc = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.worker.min.js';
    let pdfStates = {};

    function showPdfViewer(pdfId) {
        document.querySelectorAll('.pdf-viewer').forEach(el => el.classList.add('hidden'));
        const viewer = document.querySelector(`.pdf-viewer[data-pdf-id='${pdfId}']`);
        if (viewer) viewer.classList.remove('hidden');
        loadPdf(pdfId);
    }

    async function loadPdf(pdfId) {
        const viewerEl = document.querySelector(`.pdf-viewer[data-pdf-id='${pdfId}']`);
        if (!viewerEl) return;
        const pdfUrl = viewerEl.dataset.pdfUrl;

        if (pdfStates[pdfId]) {
            renderPage(pdfId, pdfStates[pdfId].currentPage);
            return;
        }

        try {
            const pdf = await pdfjsLib.getDocument(pdfUrl).promise;
            pdfStates[pdfId] = {
                pdf: pdf,
                currentPage: 1,
                totalPages: pdf.numPages
            };

            document.getElementById(`page-total-${pdfId}`).textContent = pdf.numPages;

            // Show controls only if more than 1 page
            const controls = document.getElementById(`pdf-controls-${pdfId}`);
            if (controls) controls.style.display = (pdf.numPages > 1) ? 'flex' : 'none';

            renderPage(pdfId, 1);
        } catch (error) {
            console.error('Error loading PDF:', error);
            const container = document.getElementById(`pdf-container-${pdfId}`);
            if (container) container.innerHTML = '<p class="p-4 text-red-600">Erreur lors du chargement du PDF</p>';
        }
    }

    async function renderPage(pdfId, pageNum) {
        if (!pdfStates[pdfId]) return;
        const state = pdfStates[pdfId];
        if (pageNum < 1 || pageNum > state.totalPages) return;
        state.currentPage = pageNum;
        const page = await state.pdf.getPage(pageNum);

        const canvas = document.getElementById(`pdf-canvas-${pdfId}`);
        const context = canvas.getContext('2d');
        const container = document.getElementById(`pdf-container-${pdfId}`);

        // Compute scale to fit container width while respecting device pixel ratio
        const unscaledViewport = page.getViewport({ scale: 1 });
        const containerWidth = Math.max(container.clientWidth, 400);
        
        // If it's a wide/landscape PDF, use a minimum scale to maintain readability, triggering horizontal scroll
        const isLandscape = unscaledViewport.width > unscaledViewport.height;
        const fitScale = (containerWidth / unscaledViewport.width) * 0.98;
        
        // Use fitScale but ensure a minimum of 1.0 for landscape to allow scrolling, or 0.8 for portrait
        const minScale = isLandscape ? 1.1 : 0.8;
        const scale = Math.min(2.0, Math.max(minScale, fitScale));
        
        const viewport = page.getViewport({ scale: scale * (window.devicePixelRatio || 1) });

        // Set canvas size in device pixels
        canvas.width = Math.floor(viewport.width);
        canvas.height = Math.floor(viewport.height);

        // Set CSS size to match the viewport (scaled by scale factor, not devicePixelRatio)
        canvas.style.width = Math.floor(viewport.width / (window.devicePixelRatio || 1)) + 'px';
        canvas.style.height = 'auto';

        await page.render({ canvasContext: context, viewport: viewport }).promise;
        document.getElementById(`page-count-${pdfId}`).textContent = pageNum;
    }

    function nextPage(pdfId) {
        if (pdfStates[pdfId]) {
            const newPage = pdfStates[pdfId].currentPage + 1;
            if (newPage <= pdfStates[pdfId].totalPages) renderPage(pdfId, newPage);
        }
    }

    function prevPage(pdfId) {
        if (pdfStates[pdfId]) {
            const newPage = pdfStates[pdfId].currentPage - 1;
            if (newPage >= 1) renderPage(pdfId, newPage);
        }
    }

    // Auto-load first PDF if available
    window.addEventListener('DOMContentLoaded', () => {
        const first = document.querySelector('.pdf-viewer');
        if (first) {
            const id = first.dataset.pdfId;
            loadPdf(id);
        }
    });
</script>

@endsection
