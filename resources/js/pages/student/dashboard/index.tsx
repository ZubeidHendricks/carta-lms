import { Head } from '@inertiajs/react';
import DashboardLayout from '@/layouts/dashboard/layout';
import StatsCard from '@/components/gamification/stats-card';
import CourseProgressCard from '@/components/gamification/course-progress-card';
import Leaderboard from '@/components/gamification/leaderboard';
import BadgeCard from '@/components/gamification/badge-card';
import PointsHistory from '@/components/gamification/points-history';
import { Tabs, TabsContent, TabsList, TabsTrigger } from '@/components/ui/tabs';

interface Props {
    stats: {
        total_points: number;
        badges_earned: number;
        courses_completed: number;
        courses_in_progress: number;
        total_time_spent: number;
    };
    recentProgress: Array<{
        course: any;
        progress: any;
    }>;
    recentBadges: Array<any>;
    leaderboard: Array<any>;
    pointsHistory: Array<any>;
    auth: {
        user: any;
    };
}

export default function StudentDashboard({ stats, recentProgress, recentBadges, leaderboard, pointsHistory, auth }: Props) {
    const hoursSpent = Math.floor(stats.total_time_spent / 60);

    return (
        <DashboardLayout>
            <Head title="My Learning Dashboard" />

            <div className="container mx-auto p-6 space-y-6">
                {/* Header */}
                <div>
                    <h1 className="text-3xl font-bold">Welcome back, {auth.user.name}! 👋</h1>
                    <p className="text-muted-foreground mt-1">
                        Track your progress, earn badges, and climb the leaderboard
                    </p>
                </div>

                {/* Stats Grid */}
                <div className="grid gap-4 md:grid-cols-2 lg:grid-cols-4">
                    <StatsCard
                        title="Total Points"
                        value={stats.total_points.toLocaleString()}
                        icon="trophy"
                        subtitle="Keep learning to earn more!"
                    />
                    <StatsCard
                        title="Badges Earned"
                        value={stats.badges_earned}
                        icon="award"
                        subtitle="Unlock more achievements"
                    />
                    <StatsCard
                        title="Courses Completed"
                        value={stats.courses_completed}
                        icon="target"
                        subtitle={`${stats.courses_in_progress} in progress`}
                    />
                    <StatsCard
                        title="Time Spent"
                        value={`${hoursSpent}h`}
                        icon="clock"
                        subtitle="Total learning time"
                    />
                </div>

                {/* Main Content */}
                <div className="grid gap-6 lg:grid-cols-3">
                    {/* Left Column - Progress & Points */}
                    <div className="lg:col-span-2 space-y-6">
                        <Tabs defaultValue="progress" className="w-full">
                            <TabsList className="grid w-full grid-cols-2">
                                <TabsTrigger value="progress">My Progress</TabsTrigger>
                                <TabsTrigger value="points">Points History</TabsTrigger>
                            </TabsList>
                            
                            <TabsContent value="progress" className="space-y-4 mt-4">
                                {recentProgress.length > 0 ? (
                                    recentProgress.map((item, index) => (
                                        <CourseProgressCard
                                            key={index}
                                            course={item.course}
                                            progress={item.progress}
                                        />
                                    ))
                                ) : (
                                    <div className="text-center py-12 text-muted-foreground">
                                        <p>No courses in progress</p>
                                        <p className="text-sm mt-2">Enroll in a course to start learning!</p>
                                    </div>
                                )}
                            </TabsContent>
                            
                            <TabsContent value="points" className="mt-4">
                                <PointsHistory transactions={pointsHistory} />
                            </TabsContent>
                        </Tabs>

                        {/* Recent Badges */}
                        {recentBadges.length > 0 && (
                            <div>
                                <h2 className="text-xl font-semibold mb-4">Recent Achievements</h2>
                                <div className="grid gap-4 md:grid-cols-2">
                                    {recentBadges.slice(0, 4).map(badge => (
                                        <BadgeCard key={badge.id} badge={badge.badge} earned />
                                    ))}
                                </div>
                            </div>
                        )}
                    </div>

                    {/* Right Column - Leaderboard */}
                    <div>
                        <Leaderboard users={leaderboard} currentUserId={auth.user.id} />
                    </div>
                </div>
            </div>
        </DashboardLayout>
    );
}
