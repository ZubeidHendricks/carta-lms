import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Progress } from '@/components/ui/progress';
import { Trophy, Target, Clock, Award } from 'lucide-react';

interface StatsCardProps {
    title: string;
    value: string | number;
    icon: 'trophy' | 'target' | 'clock' | 'award';
    subtitle?: string;
    progress?: number;
}

const icons = {
    trophy: Trophy,
    target: Target,
    clock: Clock,
    award: Award,
};

export default function StatsCard({ title, value, icon, subtitle, progress }: StatsCardProps) {
    const Icon = icons[icon];

    return (
        <Card>
            <CardHeader className="flex flex-row items-center justify-between space-y-0 pb-2">
                <CardTitle className="text-sm font-medium">{title}</CardTitle>
                <Icon className="h-4 w-4 text-muted-foreground" />
            </CardHeader>
            <CardContent>
                <div className="text-2xl font-bold">{value}</div>
                {subtitle && (
                    <p className="text-xs text-muted-foreground mt-1">{subtitle}</p>
                )}
                {progress !== undefined && (
                    <Progress value={progress} className="mt-2" />
                )}
            </CardContent>
        </Card>
    );
}
