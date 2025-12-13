import { Head } from '@inertiajs/react';
import DashboardLayout from '@/layouts/dashboard/layout';
import BadgeCard from '@/components/gamification/badge-card';
import { Tabs, TabsContent, TabsList, TabsTrigger } from '@/components/ui/tabs';
import { Award, Lock, CheckCircle2 } from 'lucide-react';

interface Badge {
    id: number;
    name: string;
    slug: string;
    description: string;
    icon: string;
    color: string;
    category: string;
    criteria_type: string;
    points_reward: number;
    earned_at?: string;
}

interface Props {
    earnedBadges: Badge[];
    availableBadges: Badge[];
    stats: {
        total_badges: number;
        earned_badges: number;
        points_from_badges: number;
    };
}

export default function BadgesPage({ earnedBadges, availableBadges, stats }: Props) {
    const completionPercentage = stats.total_badges > 0 
        ? Math.round((stats.earned_badges / stats.total_badges) * 100) 
        : 0;

    return (
        <DashboardLayout>
            <Head title="My Badges & Achievements" />

            <div className="container mx-auto p-6 space-y-6">
                {/* Header */}
                <div>
                    <h1 className="text-3xl font-bold flex items-center gap-2">
                        <Award className="h-8 w-8 text-primary" />
                        My Badges & Achievements
                    </h1>
                    <p className="text-muted-foreground mt-1">
                        Unlock badges by completing courses, passing quizzes, and staying consistent
                    </p>
                </div>

                {/* Stats */}
                <div className="grid gap-4 md:grid-cols-3">
                    <div className="bg-gradient-to-br from-primary/10 to-primary/5 p-6 rounded-lg border">
                        <div className="flex items-center justify-between">
                            <div>
                                <p className="text-sm text-muted-foreground">Badges Earned</p>
                                <p className="text-3xl font-bold mt-1">
                                    {stats.earned_badges} / {stats.total_badges}
                                </p>
                            </div>
                            <CheckCircle2 className="h-12 w-12 text-primary opacity-20" />
                        </div>
                        <div className="mt-4">
                            <div className="flex items-center justify-between text-sm mb-1">
                                <span>Completion</span>
                                <span className="font-semibold">{completionPercentage}%</span>
                            </div>
                            <div className="w-full bg-muted rounded-full h-2">
                                <div
                                    className="bg-primary h-2 rounded-full transition-all"
                                    style={{ width: `${completionPercentage}%` }}
                                />
                            </div>
                        </div>
                    </div>

                    <div className="bg-gradient-to-br from-yellow-500/10 to-yellow-500/5 p-6 rounded-lg border">
                        <div className="flex items-center justify-between">
                            <div>
                                <p className="text-sm text-muted-foreground">Points from Badges</p>
                                <p className="text-3xl font-bold mt-1">
                                    {stats.points_from_badges.toLocaleString()}
                                </p>
                            </div>
                            <Award className="h-12 w-12 text-yellow-500 opacity-20" />
                        </div>
                    </div>

                    <div className="bg-gradient-to-br from-gray-500/10 to-gray-500/5 p-6 rounded-lg border">
                        <div className="flex items-center justify-between">
                            <div>
                                <p className="text-sm text-muted-foreground">Locked Badges</p>
                                <p className="text-3xl font-bold mt-1">
                                    {stats.total_badges - stats.earned_badges}
                                </p>
                            </div>
                            <Lock className="h-12 w-12 text-gray-500 opacity-20" />
                        </div>
                    </div>
                </div>

                {/* Badges Grid */}
                <Tabs defaultValue="earned" className="w-full">
                    <TabsList>
                        <TabsTrigger value="earned">
                            Earned ({stats.earned_badges})
                        </TabsTrigger>
                        <TabsTrigger value="available">
                            Available ({stats.total_badges - stats.earned_badges})
                        </TabsTrigger>
                        <TabsTrigger value="all">
                            All Badges ({stats.total_badges})
                        </TabsTrigger>
                    </TabsList>

                    <TabsContent value="earned" className="mt-6">
                        {earnedBadges.length > 0 ? (
                            <div className="grid gap-4 md:grid-cols-2 lg:grid-cols-3">
                                {earnedBadges.map(badge => (
                                    <BadgeCard key={badge.id} badge={badge} earned />
                                ))}
                            </div>
                        ) : (
                            <div className="text-center py-12 text-muted-foreground">
                                <Award className="h-16 w-16 mx-auto mb-4 opacity-20" />
                                <p className="text-lg">No badges earned yet</p>
                                <p className="text-sm mt-2">Complete courses and quizzes to unlock your first badge!</p>
                            </div>
                        )}
                    </TabsContent>

                    <TabsContent value="available" className="mt-6">
                        <div className="grid gap-4 md:grid-cols-2 lg:grid-cols-3">
                            {availableBadges.map(badge => (
                                <BadgeCard key={badge.id} badge={badge} />
                            ))}
                        </div>
                    </TabsContent>

                    <TabsContent value="all" className="mt-6">
                        <div className="grid gap-4 md:grid-cols-2 lg:grid-cols-3">
                            {[...earnedBadges, ...availableBadges].map(badge => (
                                <BadgeCard 
                                    key={badge.id} 
                                    badge={badge} 
                                    earned={!!badge.earned_at} 
                                />
                            ))}
                        </div>
                    </TabsContent>
                </Tabs>
            </div>
        </DashboardLayout>
    );
}
