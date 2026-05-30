@php
    $isEdit = isset($chapter);
    $formAction = $isEdit
        ? route('admin.chapters.update', $chapter)
        : route('admin.chapters.store');
    $cancelUrl = $isEdit
        ? route('admin.courses.show', $chapter->course_id)
        : ($course_id ? route('admin.courses.show', $course_id) : route('admin.courses.index'));
    $primaryResources = $isEdit
        ? $chapter->resources->where('is_remedial', false)->sortBy('order')->values()
        : collect();
    $remedialResources = $isEdit
        ? $chapter->resources->where('is_remedial', true)->sortBy('order')->values()
        : collect();
    $hasPrimaryResources = $primaryResources->isNotEmpty();
@endphp

<div class="max-w-4xl mx-auto">
    @if(session('success'))
        <div class="mb-6 p-4 rounded-lg bg-green-50 border border-green-200 text-green-800 text-sm font-bold">
            {{ session('success') }}
        </div>
    @endif

    @if($errors->any())
        <div class="mb-6 p-4 rounded-lg bg-red-50 border border-red-200 text-red-800 text-sm">
            <p class="font-bold mb-2">Please fix the following:</p>
            <ul class="list-disc list-inside space-y-1">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ $formAction }}" method="POST" enctype="multipart/form-data" class="space-y-8" id="chapter-form">
        @csrf
        @if($isEdit)
            @method('PUT')
        @endif

        {{-- 1. Chapter Info --}}
        <section class="bg-white p-6 rounded-lg border border-gray-200 space-y-5">
            <h3 class="text-lg font-bold text-gray-900 border-b border-gray-100 pb-3">Informations du chapitre</h3>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Cours</label>
                <select name="course_id" class="w-full rounded-lg border-gray-200" required>
                    @foreach($courses as $course)
                        <option value="{{ $course->id }}"
                            @selected(old('course_id', $isEdit ? $chapter->course_id : $course_id) == $course->id)>
                            {{ $course->title }}
                        </option>
                    @endforeach
                </select>
                @error('course_id')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Titre du chapitre</label>
                    <input type="text" name="title" value="{{ old('title', $isEdit ? $chapter->title : '') }}"
                         class="w-full rounded-lg border-gray-200" required>
                    @error('title')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Ordre du chapitre</label>
                    <input type="number" name="order" min="1"
                        value="{{ old('order', $isEdit ? $chapter->order : 1) }}"
                        class="w-full rounded-lg border-gray-200" required>
                    @error('order')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Étiquette (Optionnel)</label>
                <input type="text" name="tag" value="{{ old('tag', $isEdit ? $chapter->tag : '') }}"
                    class="w-full rounded-lg border-gray-200" placeholder="Optionnel">
                @error('tag')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
            </div>
        </section>

        {{-- 2. Primary Resources --}}
        <section class="bg-white p-6 rounded-lg border border-gray-200 space-y-4" id="primary-resources-section">
            <div class="flex items-center justify-between border-b border-gray-100 pb-3">
                <div>
                    <h3 class="text-lg font-bold text-gray-900">Primary Resources</h3>
                    <p class="text-xs text-gray-500 mt-1">Content shown to students before remediation.</p>
                </div>
                <button type="button" id="add-primary-resource"
                    class="px-4 py-2 rounded-lg bg-indigo-50 text-indigo-700 text-sm font-bold hover:bg-indigo-100 transition">
                    + Add resource
                </button>
            </div>

            @if($isEdit && $primaryResources->isNotEmpty())
                <div class="space-y-2" id="existing-primary-resources">
                    <p class="text-xs font-bold text-gray-500 uppercase tracking-wider">Existing resources</p>
                    @foreach($primaryResources as $resource)
                        <div class="flex items-center justify-between p-3 rounded-lg border border-gray-200 bg-gray-50 existing-resource" data-counts-as-primary>
                            <div class="text-sm">
                                <span class="font-bold text-gray-800 uppercase">{{ $resource->type }}</span>
                                <span class="text-gray-500 ml-2">#{{ $resource->order }}</span>
                                @if($resource->type === 'text')
                                    <p class="text-gray-600 mt-1 line-clamp-2">{{ Str::limit($resource->value, 120) }}</p>
                                @else
                                    <p class="text-gray-500 mt-1 text-xs truncate max-w-md">{{ $resource->value }}</p>
                                @endif
                            </div>
                            <label class="flex items-center gap-2 text-sm text-red-600 font-medium cursor-pointer">
                                <input type="checkbox" name="remove_resource_ids[]" value="{{ $resource->id }}"
                                    class="rounded border-gray-300 text-red-600 focus:ring-red-500 remove-resource-checkbox">
                                Remove
                            </label>
                        </div>
                    @endforeach
                </div>
            @endif

            <div id="primary-resources-list" class="space-y-3" data-resource-list data-prefix="primary_resources"></div>
            @error('primary_resources')<p class="text-red-600 text-sm">{{ $message }}</p>@enderror
            @error('primary_resources.*')<p class="text-red-600 text-sm">{{ $message }}</p>@enderror
        </section>

        {{-- 3. Remediation Resources --}}
        <section id="remediation-resources-section"
            class="bg-white p-6 rounded-lg border border-gray-200 space-y-4 transition-opacity
                {{ $hasPrimaryResources ? '' : 'opacity-50' }}"
            data-remediation-section>
            <div class="flex items-center justify-between border-b border-gray-100 pb-3">
                <div>
                    <h3 class="text-lg font-bold text-gray-900">Remediation Resources</h3>
                    <p class="text-xs text-gray-500 mt-1" id="remediation-hint">
                        @if($hasPrimaryResources)
                            Shown after a failed quiz attempt.
                        @else
                            Add at least one primary resource first to enable remediation uploads.
                        @endif
                    </p>
                </div>
                <button type="button" id="add-remedial-resource"
                    class="px-4 py-2 rounded-lg bg-amber-50 text-amber-800 text-sm font-bold hover:bg-amber-100 transition disabled:opacity-50 disabled:cursor-not-allowed"
                    @disabled(!$hasPrimaryResources)>
                    + Add resource
                </button>
            </div>

            @if($isEdit && $remedialResources->isNotEmpty())
                <div class="space-y-2" id="existing-remedial-resources">
                    <p class="text-xs font-bold text-gray-500 uppercase tracking-wider">Existing resources</p>
                    @foreach($remedialResources as $resource)
                        <div class="flex items-center justify-between p-3 rounded-lg border border-amber-200 bg-amber-50/50 existing-resource">
                            <div class="text-sm">
                                <span class="font-bold text-amber-900 uppercase">{{ $resource->type }}</span>
                                <span class="text-amber-700/70 ml-2">#{{ $resource->order }}</span>
                                @if($resource->type === 'text')
                                    <p class="text-amber-900/80 mt-1 line-clamp-2">{{ Str::limit($resource->value, 120) }}</p>
                                @else
                                    <p class="text-amber-800/60 mt-1 text-xs truncate max-w-md">{{ $resource->value }}</p>
                                @endif
                            </div>
                            <label class="flex items-center gap-2 text-sm text-red-600 font-medium cursor-pointer">
                                <input type="checkbox" name="remove_resource_ids[]" value="{{ $resource->id }}"
                                    class="rounded border-gray-300 text-red-600 focus:ring-red-500 remove-resource-checkbox">
                                Remove
                            </label>
                        </div>
                    @endforeach
                </div>
            @endif

            <div id="remedial-resources-list" class="space-y-3" data-resource-list data-prefix="remedial_resources"
                @if(!$hasPrimaryResources) data-disabled="true" @endif></div>
            @error('remedial_resources')<p class="text-red-600 text-sm">{{ $message }}</p>@enderror
        </section>

        <div class="flex gap-3">
            <a href="{{ $cancelUrl }}"
                class="flex-1 py-3 text-center rounded-lg bg-gray-100 text-gray-600 font-bold hover:bg-gray-200 transition">
                Cancel
            </a>
            <button type="submit"
                class="flex-1 py-3 rounded-lg bg-indigo-600 text-white font-bold hover:bg-indigo-700 transition">
                {{ $isEdit ? 'Update chapter' : 'Create chapter' }}
            </button>
        </div>
    </form>
</div>

@include('admin.chapters.partials._resource-row-template')

<script>
(function () {
    const acceptByType = {
        pdf: '.pdf',
        video: 'video/mp4,video/quicktime,video/x-msvideo,video/x-ms-wmv,.mp4,.mov,.avi,.wmv',
        image: 'image/jpeg,image/png,image/gif,image/svg+xml,.jpg,.jpeg,.png,.gif,.svg',
        text: ''
    };

    const template = document.getElementById('resource-row-template');
    const remediationSection = document.querySelector('[data-remediation-section]');
    const remedialList = document.getElementById('remedial-resources-list');
    const addRemedialBtn = document.getElementById('add-remedial-resource');
    const remediationHint = document.getElementById('remediation-hint');
    const counters = { primary_resources: 0, remedial_resources: 0 };

    function hasPrimaryResources() {
        const existing = document.querySelectorAll('[data-counts-as-primary] .remove-resource-checkbox:not(:checked)').length;
        const newRows = document.querySelectorAll('#primary-resources-list [data-resource-row]').length;
        const newWithFile = Array.from(document.querySelectorAll('#primary-resources-list [data-resource-row]')).some(row => {
            const type = row.querySelector('[data-field="type"]')?.value;
            if (type === 'text') {
                return (row.querySelector('[data-field="value"]')?.value || '').trim() !== '';
            }
            return row.querySelector('[data-field="file"]')?.files?.length > 0;
        });
        return existing > 0 || newWithFile;
    }

    function updateRemediationAvailability() {
        const enabled = hasPrimaryResources();
        remediationSection.classList.toggle('opacity-50', !enabled);
        addRemedialBtn.disabled = !enabled;
        remedialList.dataset.disabled = enabled ? 'false' : 'true';
        remedialList.querySelectorAll('input, select, textarea, button[data-action="remove-row"]').forEach(el => {
            if (el.type === 'file' || el.tagName === 'SELECT' || el.tagName === 'TEXTAREA' || el.getAttribute('data-action') === 'remove-row') {
                el.disabled = !enabled;
            }
        });
        if (!enabled) {
            remediationHint.textContent = 'Add at least one primary resource first to enable remediation uploads.';
        } else {
            remediationHint.textContent = 'Shown after a failed quiz attempt.';
        }
    }

    const maxSizes = {
        pdf: 20 * 1024 * 1024,     // 20 MB
        video: 100 * 1024 * 1024,  // 100 MB
        image: 5 * 1024 * 1024     // 5 MB
    };
    const sizeLabels = {
        pdf: '20 Mo',
        video: '100 Mo',
        image: '5 Mo'
    };

    function validateFile(row) {
        const typeSelect = row.querySelector('[data-field="type"]');
        const fileInput = row.querySelector('[data-field="file"]');
        if (!fileInput) return;

        // Remove any existing error message
        const existingError = fileInput.parentNode.querySelector('.js-size-error');
        if (existingError) {
            existingError.remove();
        }
        fileInput.setCustomValidity('');

        if (fileInput.files && fileInput.files.length > 0) {
            const file = fileInput.files[0];
            const type = typeSelect.value;
            const maxSize = maxSizes[type];

            if (maxSize && file.size > maxSize) {
                fileInput.value = ''; // Clear file input
                const errorMsg = document.createElement('p');
                errorMsg.className = 'text-red-600 text-xs mt-1 js-size-error font-medium';
                errorMsg.textContent = `La taille du fichier (${(file.size / (1024 * 1024)).toFixed(2)} Mo) dépasse la limite autorisée pour ce type (${sizeLabels[type]}).`;
                fileInput.parentNode.appendChild(errorMsg);
                fileInput.setCustomValidity(`Le fichier dépasse la limite de ${sizeLabels[type]}.`);
            }
        }
    }

    function toggleResourceFields(row) {
        const type = row.querySelector('[data-field="type"]').value;
        const fileWrap = row.querySelector('[data-wrapper="file"]');
        const textWrap = row.querySelector('[data-wrapper="text"]');
        const fileInput = row.querySelector('[data-field="file"]');

        if (type === 'text') {
            fileWrap.classList.add('hidden');
            textWrap.classList.remove('hidden');
            fileInput.removeAttribute('required');
            fileInput.value = '';
        } else {
            fileWrap.classList.remove('hidden');
            textWrap.classList.add('hidden');
            fileInput.setAttribute('accept', acceptByType[type] || '');
        }
        validateFile(row);
        updateRemediationAvailability();
    }

    function wireRow(row, prefix, index) {
        const typeSelect = row.querySelector('[data-field="type"]');
        const fileInput = row.querySelector('[data-field="file"]');
        const valueInput = row.querySelector('[data-field="value"]');

        typeSelect.name = `${prefix}[${index}][type]`;
        fileInput.name = `${prefix}[${index}][file]`;
        valueInput.name = `${prefix}[${index}][value]`;

        typeSelect.addEventListener('change', () => {
            toggleResourceFields(row);
        });
        fileInput.addEventListener('change', () => {
            validateFile(row);
            updateRemediationAvailability();
        });
        valueInput.addEventListener('input', updateRemediationAvailability);

        row.querySelector('[data-action="remove-row"]').addEventListener('click', () => {
            row.remove();
            updateRemediationAvailability();
        });

        toggleResourceFields(row);
    }

    function addResourceRow(listId) {
        const list = document.getElementById(listId);
        const prefix = list.dataset.prefix;
        if (prefix === 'remedial_resources' && !hasPrimaryResources()) {
            return;
        }
        const index = counters[prefix]++;
        const clone = template.content.cloneNode(true);
        const row = clone.querySelector('[data-resource-row]');
        list.appendChild(row);
        wireRow(row, prefix, index);
        updateRemediationAvailability();
    }

    document.getElementById('add-primary-resource').addEventListener('click', () => {
        addResourceRow('primary-resources-list');
    });

    document.getElementById('add-remedial-resource').addEventListener('click', () => {
        if (!hasPrimaryResources()) return;
        remedialList.dataset.disabled = 'false';
        addResourceRow('remedial-resources-list');
    });

    document.querySelectorAll('.remove-resource-checkbox').forEach(cb => {
        cb.addEventListener('change', updateRemediationAvailability);
    });

    updateRemediationAvailability();
})();
</script>
