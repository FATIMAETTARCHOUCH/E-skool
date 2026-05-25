<template id="resource-row-template">
    <div class="resource-row p-4 rounded-lg border border-gray-200 bg-gray-50 space-y-3" data-resource-row>
        <div class="flex items-start justify-between gap-3">
            <div class="flex-1 grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-1">Type</label>
                    <select data-field="type" class="w-full rounded-lg border-gray-200 text-sm" required>
                        <option value="pdf">PDF</option>
                        <option value="video">Video</option>
                        <option value="image">Image</option>
                        <option value="text">Text</option>
                    </select>
                </div>
                <div data-wrapper="file">
                    <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-1">File</label>
                    <input type="file" data-field="file" class="w-full text-sm text-gray-600">
                </div>
                <div data-wrapper="text" class="hidden sm:col-span-2">
                    <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-1">Text content</label>
                    <textarea data-field="value" rows="3" class="w-full rounded-lg border-gray-200 text-sm"></textarea>
                </div>
            </div>
            <button type="button" data-action="remove-row" class="shrink-0 px-3 py-2 text-xs font-bold text-red-600 hover:bg-red-50 rounded-lg transition">
                Remove
            </button>
        </div>
    </div>
</template>
