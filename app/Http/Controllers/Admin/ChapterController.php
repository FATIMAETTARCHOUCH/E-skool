<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Chapter;
use App\Models\ChapterResource;
use App\Models\Course;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ChapterController extends Controller
{
    public function index()
    {
        $chapters = Chapter::with('course')->orderBy('created_at', 'desc')->get();
        return view('admin.chapters.index', compact('chapters'));
    }

    public function create(Request $request)
    {
        $courses = Course::all();
        $course_id = $request->query('course_id');

        return view('admin.chapters.create', compact('courses', 'course_id'));
    }

    public function store(Request $request)
    {
        $this->validateChapterRequest($request);

        $chapter = Chapter::create($request->only(['course_id', 'title', 'order', 'tag']));

        $order = 0;
        $this->processResourceRows($chapter, $this->mergedResourceRows($request, 'primary_resources'), false, $order);

        if ($chapter->primaryResources()->exists()) {
            $remedialOrder = (int) $chapter->remedialResources()->max('order');
            $this->processResourceRows($chapter, $this->mergedResourceRows($request, 'remedial_resources'), true, $remedialOrder);
        }

        return redirect()->route('admin.courses.show', $request->course_id)
            ->with('success', 'Partie créée avec succès.');
    }

    public function edit($id)
    {
        $chapter = Chapter::with('resources')->findOrFail($id);
        $courses = Course::all();

        return view('admin.chapters.edit', compact('chapter', 'courses'));
    }

    public function update(Request $request, $id)
    {
        $chapter = Chapter::with('resources')->findOrFail($id);

        $this->validateChapterRequest($request, $chapter);

        $chapter->update($request->only(['course_id', 'title', 'order', 'tag']));

        $this->removeResources($chapter, $request->input('remove_resource_ids', []));

        $primaryOrder = (int) $chapter->primaryResources()->max('order');
        $this->processResourceRows($chapter, $this->mergedResourceRows($request, 'primary_resources'), false, $primaryOrder);

        if ($chapter->primaryResources()->exists()) {
            $remedialOrder = (int) $chapter->remedialResources()->max('order');
            $this->processResourceRows($chapter, $this->mergedResourceRows($request, 'remedial_resources'), true, $remedialOrder);
        }

        return redirect()->route('admin.chapters.edit', $chapter)
            ->with('success', 'Partie mise à jour avec succès.');
    }

    public function destroy($id)
    {
        $chapter = Chapter::with('resources')->findOrFail($id);
        $course_id = $chapter->course_id;

        $this->deleteStoredFiles($chapter->resources);
        $chapter->delete();

        return redirect()->route('admin.courses.show', $course_id)
            ->with('success', 'Partie supprimée.');
    }

    public function deletePdf($pdfId)
    {
        return redirect()->back()->with('error', 'La suppression de PDF individuel est désactivée avec le nouveau modèle de contenu.');
    }

    public function reorderPdfs(Request $request, $chapterId)
    {
        return response()->json(['success' => false, 'message' => 'PDF reorder is no longer supported.'], 410);
    }

    public function getChaptersForParent($courseId)
    {
        return response()->json([]);
    }

    private function validateChapterRequest(Request $request, ?Chapter $chapter = null): void
    {
        $rules = [
            'course_id' => 'required|exists:courses,id',
            'title' => 'required|string|max:255',
            'order' => 'required|integer|min:1',
            'tag' => 'nullable|string|max:255',
            'primary_resources' => 'nullable|array',
            'primary_resources.*.type' => 'nullable|in:text,pdf,video,image',
            'primary_resources.*.value' => 'nullable|string',
            'primary_resources.*.file' => 'nullable|file|max:102400',
            'remedial_resources' => 'nullable|array',
            'remedial_resources.*.type' => 'nullable|in:text,pdf,video,image',
            'remedial_resources.*.value' => 'nullable|string',
            'remedial_resources.*.file' => 'nullable|file|max:102400',
            'remove_resource_ids' => 'nullable|array',
            'remove_resource_ids.*' => 'integer|exists:chapter_resources,id',
        ];

        $request->validate($rules);

        $request->validate([
            'primary_resources' => function ($attribute, $value, $fail) use ($request) {
                foreach ($this->mergedResourceRows($request, 'primary_resources') as $index => $row) {
                    $this->validateResourceRow($row, "primary_resources.{$index}", $fail);
                }
            },
            'remedial_resources' => function ($attribute, $value, $fail) use ($request, $chapter) {
                $remedialRows = $this->mergedResourceRows($request, 'remedial_resources');
                if (! $this->willHavePrimaryResources($request, $chapter)) {
                    if ($this->hasResourceRowContent($remedialRows)) {
                        $fail('Remediation resources require at least one primary resource.');
                    }
                    return;
                }
                foreach ($remedialRows as $index => $row) {
                    $this->validateResourceRow($row, "remedial_resources.{$index}", $fail);
                }
            },
        ]);
    }

    /**
     * Merge text fields from input() with uploaded files from file().
     * input() alone never contains UploadedFile instances.
     */
    private function mergedResourceRows(Request $request, string $key): array
    {
        $input = $request->input($key, []);
        $files = $request->file($key, []);

        if (! is_array($input)) {
            $input = [];
        }
        if (! is_array($files)) {
            $files = [];
        }

        $indexes = array_unique(array_merge(array_keys($input), array_keys($files)));
        sort($indexes, SORT_NUMERIC);

        $rows = [];
        foreach ($indexes as $index) {
            $rows[] = [
                'type' => $input[$index]['type'] ?? null,
                'value' => $input[$index]['value'] ?? null,
                'file' => $files[$index]['file'] ?? null,
            ];
        }

        return $rows;
    }

    private function validateResourceRow(array $row, string $prefix, callable $fail): void
    {
        if (! $this->rowHasContent($row)) {
            return;
        }

        $type = $row['type'] ?? null;
        if (! in_array($type, ['text', 'pdf', 'video', 'image'], true)) {
            $fail("{$prefix}.type is invalid.");
            return;
        }

        if ($type === 'text') {
            if (trim($row['value'] ?? '') === '') {
                $fail("{$prefix}.value is required for text resources.");
            }
            return;
        }

        $file = $row['file'] ?? null;
        if (! $file instanceof \Illuminate\Http\UploadedFile || ! $file->isValid()) {
            $fail("{$prefix}.file is required for {$type} resources.");
            return;
        }

        $fileRules = match ($type) {
            'pdf' => 'mimes:pdf|max:20480',
            'video' => 'mimes:mp4,mov,avi,wmv|max:102400',
            'image' => 'image|mimes:jpeg,png,jpg,gif,svg|max:5120',
            default => '',
        };

        $validator = validator(['file' => $file], ['file' => $fileRules]);
        if ($validator->fails()) {
            $fail("{$prefix}.file: ".$validator->errors()->first('file'));
        }
    }

    private function willHavePrimaryResources(Request $request, ?Chapter $chapter): bool
    {
        $count = $chapter ? $chapter->primaryResources()->count() : 0;

        if ($chapter) {
            $removeIds = $request->input('remove_resource_ids', []);
            $count -= $chapter->primaryResources()->whereIn('id', $removeIds)->count();
        }

        foreach ($this->mergedResourceRows($request, 'primary_resources') as $row) {
            if ($this->rowHasContent($row)) {
                $count++;
            }
        }

        return $count > 0;
    }

    private function hasResourceRowContent(array $rows): bool
    {
        foreach ($rows as $row) {
            if ($this->rowHasContent($row)) {
                return true;
            }
        }
        return false;
    }

    private function rowHasContent(array $row): bool
    {
        $type = $row['type'] ?? null;
        if (! $type) {
            return false;
        }
        if ($type === 'text') {
            return trim($row['value'] ?? '') !== '';
        }

        $file = $row['file'] ?? null;

        return $file instanceof \Illuminate\Http\UploadedFile && $file->isValid();
    }

    private function processResourceRows(Chapter $chapter, array $rows, bool $isRemedial, int &$order): void
    {
        foreach ($rows as $row) {
            if (! $this->rowHasContent($row)) {
                continue;
            }

            $type = $row['type'];

            if ($type === 'text') {
                ChapterResource::create([
                    'chapter_id' => $chapter->id,
                    'is_remedial' => $isRemedial,
                    'type' => 'text',
                    'value' => $row['value'],
                    'order' => ++$order,
                ]);
                continue;
            }

            $path = match ($type) {
                'pdf' => $row['file']->store('chapters/pdfs', 'public'),
                'video' => $row['file']->store('chapters/videos', 'public'),
                'image' => $row['file']->store('chapters/images', 'public'),
                default => null,
            };

            if (! $path) {
                continue;
            }

            ChapterResource::create([
                'chapter_id' => $chapter->id,
                'is_remedial' => $isRemedial,
                'type' => $type,
                'value' => $path,
                'order' => ++$order,
            ]);
        }
    }

    private function removeResources(Chapter $chapter, array $resourceIds): void
    {
        if (empty($resourceIds)) {
            return;
        }

        $resources = ChapterResource::where('chapter_id', $chapter->id)
            ->whereIn('id', $resourceIds)
            ->get();

        $this->deleteStoredFiles($resources);
        ChapterResource::whereIn('id', $resources->pluck('id'))->delete();
    }

    private function deleteStoredFiles($resources): void
    {
        foreach ($resources as $resource) {
            if (in_array($resource->type, ['pdf', 'video', 'image'], true) && $resource->value) {
                Storage::disk('public')->delete($resource->value);
            }
        }
    }
}
