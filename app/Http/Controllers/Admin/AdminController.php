<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function index()
    {
        $maintenance = file_exists(storage_path('framework/maintenance_mode'));
        
        $stats = [
            'total_students' => \App\Models\User::where('role', 'student')->count(),
            'total_chapters' => \App\Models\Chapter::count(),
            'total_quizzes' => \App\Models\Quiz::count(),
            'average_score' => \App\Models\Result::avg('score') ?? 0,
            'total_groups' => \App\Models\Group::count(),
            'total_results' => \App\Models\Result::count(),
        ];

        // Group Performance
        $groupPerformance = \App\Models\Group::with(['users.results'])
            ->get()
            ->map(function($group) {
                $scores = $group->users ? $group->users->flatMap(function($user) {
                    return $user->results ? $user->results->pluck('score') : collect([]);
                }) : collect([]);
                
                return [
                    'name' => $group->name,
                    'avg' => $scores->count() > 0 ? round($scores->average(), 2) : 0,
                    'count' => $scores->count()
                ];
            })->sortByDesc('avg')->take(5);

        // Recent Activity
        $recentResults = \App\Models\Result::with(['user', 'quiz'])
            ->orderBy('created_at', 'desc')
            ->take(6)
            ->get();

        // Blocked Students by Group
        $blockedProgress = \App\Models\StudentProgress::where('status', \App\Enums\StudentProgressStatus::STUCK->value)
            ->orWhere(function($query) {
                $query->whereNotNull('quiz_blocked_until')
                      ->where('quiz_blocked_until', '>', now());
            })
            ->with(['user.group', 'chapter.quiz'])
            ->get();

        $blockedGroups = $blockedProgress->groupBy(function($p) {
            return $p->user && $p->user->group ? $p->user->group->id : 0;
        })->map(function($progresses, $groupId) {
            $groupName = $groupId == 0 ? 'Sans Groupe' : $progresses->first()->user->group->name;
            return [
                'id' => $groupId,
                'name' => $groupName,
                'count' => $progresses->count(),
                'progresses' => $progresses
            ];
        })->values();

        // Top Students (Passed on first attempt) grouped by Group
        $topResults = \App\Models\Result::where('attempt_number', 1)
            ->where('is_passed', true)
            ->with(['user.group', 'quiz.chapter.course'])
            ->orderBy('score', 'desc')
            ->orderBy('created_at', 'desc')
            ->get();

        $topGroups = $topResults->groupBy(function($r) {
            return $r->user && $r->user->group ? $r->user->group->id : 0;
        })->map(function($results, $groupId) {
            $groupName = $groupId == 0 ? 'Sans Groupe' : $results->first()->user->group->name;
            return [
                'id' => $groupId,
                'name' => $groupName,
                'count' => $results->unique('user_id')->count(),
                'results' => $results
            ];
        })->values();

        // Second Attempt Successes grouped by Group
        $secondAttemptResults = \App\Models\Result::where('attempt_number', 2)
            ->where('is_passed', true)
            ->with(['user.group', 'quiz.chapter.course'])
            ->orderBy('score', 'desc')
            ->orderBy('created_at', 'desc')
            ->get();

        $secondAttemptGroups = $secondAttemptResults->groupBy(function($r) {
            return $r->user && $r->user->group ? $r->user->group->id : 0;
        })->map(function($results, $groupId) {
            $groupName = $groupId == 0 ? 'Sans Groupe' : $results->first()->user->group->name;
            return [
                'id' => $groupId,
                'name' => $groupName,
                'count' => $results->unique('user_id')->count(),
                'results' => $results
            ];
        })->values();

        return view('admin.dashboard', compact('maintenance', 'stats', 'groupPerformance', 'recentResults', 'blockedGroups', 'topGroups', 'secondAttemptGroups'));
    }

    public function toggleMaintenance()
    {
        $path = storage_path('framework/maintenance_mode');
        if (file_exists($path)) {
            unlink($path);
            $msg = "Maintenance mode disabled.";
        } else {
            file_put_contents($path, '');
            $msg = "Maintenance mode enabled.";
        }

        return redirect()->back()->with('success', $msg);
    }
}
