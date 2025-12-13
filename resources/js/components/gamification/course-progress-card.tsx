import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Progress } from '@/components/ui/progress';
import { Clock, BookOpen, CheckCircle2 } from 'lucide-react';
import { Link } from '@inertiajs/react';

interface CourseProgressCardProps {
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

export default function CourseProgressCard({ course, progress }: CourseProgressCardProps) {
    const hours = Math.floor(progress.time_spent_minutes / 60);
    const minutes = progress.time_spent_minutes % 60;

    return (
        <Card className="hover:shadow-lg transition-shadow">
            <CardHeader className="pb-3">
                <div className="flex items-start gap-4">
                    {course.thumbnail && (
                        <img
                            src={course.thumbnail}
                            alt={course.title}
                            className="w-20 h-20 rounded-lg object-cover"
                        />
                    )}
                    <div className="flex-1">
                        <Link href={`/courses/${course.id}`}>
                            <CardTitle className="text-lg hover:text-primary cursor-pointer">
                                {course.title}
                            </CardTitle>
                        </Link>
                        <div className="flex items-center gap-4 mt-2 text-sm text-muted-foreground">
                            <span className="flex items-center gap-1">
                                <BookOpen className="h-4 w-4" />
                                {progress.lessons_completed}/{progress.total_lessons} lessons
                            </span>
                            <span className="flex items-center gap-1">
                                <Clock className="h-4 w-4" />
                                {hours}h {minutes}m
                            </span>
                        </div>
                    </div>
                </div>
            </CardHeader>
            <CardContent>
                <div className="space-y-2">
                    <div className="flex items-center justify-between text-sm">
                        <span className="text-muted-foreground">Progress</span>
                        <span className="font-semibold">{progress.completion_percentage}%</span>
                    </div>
                    <Progress value={progress.completion_percentage} />
                    {progress.completion_percentage === 100 && (
                        <div className="flex items-center gap-2 text-sm text-green-600 dark:text-green-400">
                            <CheckCircle2 className="h-4 w-4" />
                            Course completed!
                        </div>
                    )}
                </div>
            </CardContent>
        </Card>
    );
}
