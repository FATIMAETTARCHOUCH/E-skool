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
            'total_lessons' => \App\Models\Lesson::count(),
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

        return view('admin.dashboard', compact('maintenance', 'stats', 'groupPerformance', 'recentResults'));
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
