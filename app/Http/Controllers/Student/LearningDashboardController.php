<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\{User, LearnerProgress, LearnerPoint, LearnerBadge, GamificationBadge};
use App\Services\Gamification\GamificationService;
use Inertia\Inertia;
use Inertia\Response;

class LearningDashboardController extends Controller
{
    public function __construct(private GamificationService $gamificationService)
    {
    }

    public function progress(): Response
    {
        $user = auth()->user();
        
        // Get all progress
        $allProgress = LearnerProgress::where('user_id', $user->id)
            ->with('course')
            ->get()
            ->map(function ($progress) {
                return [
                    'course' => [
                        'id' => $progress->course->id,
                        'title' => $progress->course->title,
                        'thumbnail' => $progress->course->thumbnail ?? null,
                    ],
                    'progress' => [
                        'completion_percentage' => $progress->completion_percentage,
                        'lessons_completed' => $progress->lessons_completed,
                        'total_lessons' => $progress->total_lessons,
                        'time_spent_minutes' => $progress->time_spent_minutes,
                        'last_accessed_at' => $progress->last_accessed_at,
                    ],
                ];
            });

        // Filter by status
        $inProgress = $allProgress->filter(fn($item) => 
            $item['progress']['completion_percentage'] > 0 && 
            $item['progress']['completion_percentage'] < 100
        )->values();

        $completed = $allProgress->filter(fn($item) => 
            $item['progress']['completion_percentage'] >= 100
        )->values();

        // Calculate stats
        $stats = [
            'total_courses' => $allProgress->count(),
            'completed_courses' => $completed->count(),
            'in_progress_courses' => $inProgress->count(),
            'total_time_spent' => $allProgress->sum('progress.time_spent_minutes'),
            'average_completion' => $allProgress->count() > 0 
                ? round($allProgress->avg('progress.completion_percentage')) 
                : 0,
        ];

        return Inertia::render('student/progress/index', [
            'allProgress' => $allProgress,
            'inProgress' => $inProgress,
            'completed' => $completed,
            'stats' => $stats,
        ]);
    }

    public function badges(): Response
    {
        $user = auth()->user();
        
        // Get earned badges
        $earnedBadges = LearnerBadge::where('user_id', $user->id)
            ->with('badge')
            ->get()
            ->map(function ($learnerBadge) {
                $badge = $learnerBadge->badge;
                return [
                    'id' => $badge->id,
                    'name' => $badge->name,
                    'slug' => $badge->slug,
                    'description' => $badge->description,
                    'icon' => $badge->icon,
                    'color' => $badge->color,
                    'category' => $badge->category,
                    'criteria_type' => $badge->criteria_type,
                    'points_reward' => $badge->points_reward,
                    'earned_at' => $learnerBadge->earned_at,
                ];
            });

        // Get all badges
        $allBadges = GamificationBadge::where('is_active', true)->get();
        $earnedIds = $earnedBadges->pluck('id');
        
        $availableBadges = $allBadges->filter(function ($badge) use ($earnedIds) {
            return !$earnedIds->contains($badge->id);
        })->map(function ($badge) {
            return [
                'id' => $badge->id,
                'name' => $badge->name,
                'slug' => $badge->slug,
                'description' => $badge->description,
                'icon' => $badge->icon,
                'color' => $badge->color,
                'category' => $badge->category,
                'criteria_type' => $badge->criteria_type,
                'points_reward' => $badge->points_reward,
            ];
        })->values();

        // Calculate stats
        $pointsFromBadges = LearnerPoint::where('user_id', $user->id)
            ->where('source', 'badge_earned')
            ->sum('points');

        $stats = [
            'total_badges' => $allBadges->count(),
            'earned_badges' => $earnedBadges->count(),
            'points_from_badges' => $pointsFromBadges,
        ];

        return Inertia::render('student/badges/index', [
            'earnedBadges' => $earnedBadges,
            'availableBadges' => $availableBadges,
            'stats' => $stats,
        ]);
    }

    public function leaderboard(): Response
    {
        $user = auth()->user();
        
        // Get leaderboard data
        $leaderboard = $this->gamificationService->getLeaderboard(20);
        
        // Calculate user's rank
        $allUsers = User::where('role', 'student')->count();
        $userPoints = LearnerPoint::where('user_id', $user->id)->sum('points');
        $usersAhead = User::select('users.id')
            ->leftJoin('learner_points', 'users.id', '=', 'learner_points.user_id')
            ->where('users.role', 'student')
            ->groupBy('users.id')
            ->havingRaw('COALESCE(SUM(learner_points.points), 0) > ?', [$userPoints])
            ->count();

        $currentUserRank = [
            'position' => $usersAhead + 1,
            'total_users' => $allUsers,
            'points' => $userPoints,
        ];

        return Inertia::render('student/leaderboard/index', [
            'topLearners' => $leaderboard,
            'currentUserRank' => $currentUserRank,
            'timeframes' => [
                'all_time' => $leaderboard,
                'this_month' => $leaderboard, // TODO: Filter by date
                'this_week' => $leaderboard,  // TODO: Filter by date
            ],
        ]);
    }

    public function aiTutor(): Response
    {
        $lecturers = \App\Models\AILecturer::where('is_active', true)
            ->select('id', 'name', 'avatar', 'expertise_area', 'personality_traits', 'teaching_style')
            ->get();

        return Inertia::render('student/ai-tutor/index', [
            'lecturers' => $lecturers,
            'currentCourse' => null, // TODO: Get from session if in course context
        ]);
    }

    public function dashboard(): Response
    {
        $user = auth()->user();
        
        // Get stats
        $stats = $this->gamificationService->getUserStats($user);
        
        // Get recent progress
        $recentProgress = LearnerProgress::where('user_id', $user->id)
            ->with('course')
            ->orderByDesc('last_accessed_at')
            ->limit(5)
            ->get()
            ->map(function ($progress) {
                return [
                    'course' => [
                        'id' => $progress->course->id,
                        'title' => $progress->course->title,
                        'thumbnail' => $progress->course->thumbnail ?? null,
                    ],
                    'progress' => [
                        'completion_percentage' => $progress->completion_percentage,
                        'lessons_completed' => $progress->lessons_completed,
                        'total_lessons' => $progress->total_lessons,
                        'time_spent_minutes' => $progress->time_spent_minutes,
                        'last_accessed_at' => $progress->last_accessed_at,
                    ],
                ];
            });

        // Get recent badges
        $recentBadges = LearnerBadge::where('user_id', $user->id)
            ->with('badge')
            ->orderByDesc('earned_at')
            ->limit(4)
            ->get();

        // Get leaderboard
        $leaderboard = $this->gamificationService->getLeaderboard(10);

        // Get points history
        $pointsHistory = LearnerPoint::where('user_id', $user->id)
            ->orderByDesc('created_at')
            ->limit(20)
            ->get();

        return Inertia::render('student/dashboard/index', [
            'stats' => $stats,
            'recentProgress' => $recentProgress,
            'recentBadges' => $recentBadges,
            'leaderboard' => $leaderboard,
            'pointsHistory' => $pointsHistory,
        ]);
    }
}
