import { Head } from '@inertiajs/react';
import DashboardLayout from '@/layouts/dashboard/layout';
import Leaderboard from '@/components/gamification/leaderboard';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Tabs, TabsContent, TabsList, TabsTrigger } from '@/components/ui/tabs';
import { Trophy, Users, TrendingUp } from 'lucide-react';

interface LeaderboardUser {
    id: number;
    name: string;
    email: string;
    total_points: number;
    badges_count: number;
    avatar?: string;
}

interface Props {
    topLearners: LeaderboardUser[];
    currentUserRank: {
        position: number;
        total_users: number;
        points: number;
    };
    auth: {
        user: any;
    };
    timeframes: {
        all_time: LeaderboardUser[];
        this_month: LeaderboardUser[];
        this_week: LeaderboardUser[];
    };
}

export default function LeaderboardPage({ topLearners, currentUserRank, auth, timeframes }: Props) {
    return (
        <DashboardLayout>
            <Head title="Leaderboard" />

            <div className="container mx-auto p-6 space-y-6">
                {/* Header */}
                <div>
                    <h1 className="text-3xl font-bold flex items-center gap-2">
                        <Trophy className="h-8 w-8 text-primary" />
                        Leaderboard
                    </h1>
                    <p className="text-muted-foreground mt-1">
                        See how you rank against other learners
                    </p>
                </div>

                {/* Current User Stats */}
                <div className="grid gap-4 md:grid-cols-3">
                    <Card>
                        <CardHeader className="flex flex-row items-center justify-between pb-2">
                            <CardTitle className="text-sm font-medium">Your Rank</CardTitle>
                            <Trophy className="h-4 w-4 text-muted-foreground" />
                        </CardHeader>
                        <CardContent>
                            <div className="text-2xl font-bold">
                                #{currentUserRank.position}
                            </div>
                            <p className="text-xs text-muted-foreground mt-1">
                                Out of {currentUserRank.total_users} learners
                            </p>
                        </CardContent>
                    </Card>

                    <Card>
                        <CardHeader className="flex flex-row items-center justify-between pb-2">
                            <CardTitle className="text-sm font-medium">Your Points</CardTitle>
                            <TrendingUp className="h-4 w-4 text-muted-foreground" />
                        </CardHeader>
                        <CardContent>
                            <div className="text-2xl font-bold">
                                {currentUserRank.points.toLocaleString()}
                            </div>
                            <p className="text-xs text-muted-foreground mt-1">
                                Keep learning to rank higher!
                            </p>
                        </CardContent>
                    </Card>

                    <Card>
                        <CardHeader className="flex flex-row items-center justify-between pb-2">
                            <CardTitle className="text-sm font-medium">Top Learner</CardTitle>
                            <Users className="h-4 w-4 text-muted-foreground" />
                        </CardHeader>
                        <CardContent>
                            <div className="text-2xl font-bold truncate">
                                {topLearners[0]?.name || 'N/A'}
                            </div>
                            <p className="text-xs text-muted-foreground mt-1">
                                {topLearners[0]?.total_points.toLocaleString() || 0} points
                            </p>
                        </CardContent>
                    </Card>
                </div>

                {/* Leaderboard Tabs */}
                <Tabs defaultValue="all" className="w-full">
                    <TabsList className="grid w-full grid-cols-3 lg:w-[400px]">
                        <TabsTrigger value="all">All Time</TabsTrigger>
                        <TabsTrigger value="month">This Month</TabsTrigger>
                        <TabsTrigger value="week">This Week</TabsTrigger>
                    </TabsList>

                    <TabsContent value="all" className="mt-6">
                        <Leaderboard users={timeframes.all_time} currentUserId={auth.user.id} />
                    </TabsContent>

                    <TabsContent value="month" className="mt-6">
                        <Leaderboard users={timeframes.this_month} currentUserId={auth.user.id} />
                    </TabsContent>

                    <TabsContent value="week" className="mt-6">
                        <Leaderboard users={timeframes.this_week} currentUserId={auth.user.id} />
                    </TabsContent>
                </Tabs>
            </div>
        </DashboardLayout>
    );
}
