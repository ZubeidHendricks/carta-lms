import { Head } from '@inertiajs/react';
import DashboardLayout from '@/layouts/dashboard/layout';
import CourseProgressCard from '@/components/gamification/course-progress-card';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Tabs, TabsContent, TabsList, TabsTrigger } from '@/components/ui/tabs';
import { Progress } from '@/components/ui/progress';
import { BookOpen, CheckCircle2, Clock, TrendingUp } from 'lucide-react';

interface CourseProgress {
    course: {
        id: number;
        title: string;
        thumbnail?: string;
    };
    progress: {
        completion_percentage: number;
        lessons_completed: number;
        total_lessons: number;
        time_spent_minutes: number;
        last_accessed_at: string;
    };
}

interface Props {
    allProgress: CourseProgress[];
    inProgress: CourseProgress[];
    completed: CourseProgress[];
    stats: {
        total_courses: number;
        completed_courses: number;
        in_progress_courses: number;
        total_time_spent: number;
        average_completion: number;
    };
}

export default function ProgressPage({ allProgress, inProgress, completed, stats }: Props) {
    const hoursSpent = Math.floor(stats.total_time_spent / 60);
    const minutesSpent = stats.total_time_spent % 60;

    return (
        <DashboardLayout>
            <Head title="My Progress" />

            <div className="container mx-auto p-6 space-y-6">
                {/* Header */}
                <div>
                    <h1 className="text-3xl font-bold flex items-center gap-2">
                        <TrendingUp className="h-8 w-8 text-primary" />
                        My Learning Progress
                    </h1>
                    <p className="text-muted-foreground mt-1">
                        Track your course completion and time spent learning
                    </p>
                </div>

                {/* Stats Grid */}
                <div className="grid gap-4 md:grid-cols-2 lg:grid-cols-4">
                    <Card>
                        <CardHeader className="flex flex-row items-center justify-between pb-2">
                            <CardTitle className="text-sm font-medium">Total Courses</CardTitle>
                            <BookOpen className="h-4 w-4 text-muted-foreground" />
                        </CardHeader>
                        <CardContent>
                            <div className="text-2xl font-bold">{stats.total_courses}</div>
                            <p className="text-xs text-muted-foreground mt-1">
                                Enrolled courses
                            </p>
                        </CardContent>
                    </Card>

                    <Card>
                        <CardHeader className="flex flex-row items-center justify-between pb-2">
                            <CardTitle className="text-sm font-medium">Completed</CardTitle>
                            <CheckCircle2 className="h-4 w-4 text-muted-foreground" />
                        </CardHeader>
                        <CardContent>
                            <div className="text-2xl font-bold text-green-600 dark:text-green-400">
                                {stats.completed_courses}
                            </div>
                            <p className="text-xs text-muted-foreground mt-1">
                                Courses finished
                            </p>
                        </CardContent>
                    </Card>

                    <Card>
                        <CardHeader className="flex flex-row items-center justify-between pb-2">
                            <CardTitle className="text-sm font-medium">In Progress</CardTitle>
                            <TrendingUp className="h-4 w-4 text-muted-foreground" />
                        </CardHeader>
                        <CardContent>
                            <div className="text-2xl font-bold text-blue-600 dark:text-blue-400">
                                {stats.in_progress_courses}
                            </div>
                            <p className="text-xs text-muted-foreground mt-1">
                                Active courses
                            </p>
                        </CardContent>
                    </Card>

                    <Card>
                        <CardHeader className="flex flex-row items-center justify-between pb-2">
                            <CardTitle className="text-sm font-medium">Time Spent</CardTitle>
                            <Clock className="h-4 w-4 text-muted-foreground" />
                        </CardHeader>
                        <CardContent>
                            <div className="text-2xl font-bold">
                                {hoursSpent}h {minutesSpent}m
                            </div>
                            <p className="text-xs text-muted-foreground mt-1">
                                Total learning time
                            </p>
                        </CardContent>
                    </Card>
                </div>

                {/* Overall Progress */}
                <Card>
                    <CardHeader>
                        <CardTitle>Overall Completion Rate</CardTitle>
                    </CardHeader>
                    <CardContent>
                        <div className="space-y-2">
                            <div className="flex items-center justify-between text-sm">
                                <span className="text-muted-foreground">Average Progress</span>
                                <span className="font-semibold">{stats.average_completion}%</span>
                            </div>
                            <Progress value={stats.average_completion} className="h-3" />
                            <p className="text-xs text-muted-foreground">
                                You've completed an average of {stats.average_completion}% across all courses
                            </p>
                        </div>
                    </CardContent>
                </Card>

                {/* Course Progress Tabs */}
                <Tabs defaultValue="all" className="w-full">
                    <TabsList>
                        <TabsTrigger value="all">
                            All Courses ({allProgress.length})
                        </TabsTrigger>
                        <TabsTrigger value="in-progress">
                            In Progress ({inProgress.length})
                        </TabsTrigger>
                        <TabsTrigger value="completed">
                            Completed ({completed.length})
                        </TabsTrigger>
                    </TabsList>

                    <TabsContent value="all" className="mt-6 space-y-4">
                        {allProgress.length > 0 ? (
                            allProgress.map((item, index) => (
                                <CourseProgressCard
                                    key={index}
                                    course={item.course}
                                    progress={item.progress}
                                />
                            ))
                        ) : (
                            <EmptyState message="No courses yet. Enroll in a course to start learning!" />
                        )}
                    </TabsContent>

                    <TabsContent value="in-progress" className="mt-6 space-y-4">
                        {inProgress.length > 0 ? (
                            inProgress.map((item, index) => (
                                <CourseProgressCard
                                    key={index}
                                    course={item.course}
                                    progress={item.progress}
                                />
                            ))
                        ) : (
                            <EmptyState message="No courses in progress" />
                        )}
                    </TabsContent>

                    <TabsContent value="completed" className="mt-6 space-y-4">
                        {completed.length > 0 ? (
                            completed.map((item, index) => (
                                <CourseProgressCard
                                    key={index}
                                    course={item.course}
                                    progress={item.progress}
                                />
                            ))
                        ) : (
                            <EmptyState message="No completed courses yet. Keep learning!" />
                        )}
                    </TabsContent>
                </Tabs>
            </div>
        </DashboardLayout>
    );
}

function EmptyState({ message }: { message: string }) {
    return (
        <div className="text-center py-12 text-muted-foreground">
            <BookOpen className="h-16 w-16 mx-auto mb-4 opacity-20" />
            <p>{message}</p>
        </div>
    );
}
