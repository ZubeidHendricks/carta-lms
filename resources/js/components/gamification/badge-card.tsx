import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Badge } from '@/components/ui/badge';
import { format } from 'date-fns';

interface BadgeCardProps {
    badge: {
        id: number;
        name: string;
        description: string;
        icon: string;
        color: string;
        points_reward: number;
        earned_at?: string;
    };
    earned?: boolean;
}

export default function BadgeCard({ badge, earned = false }: BadgeCardProps) {
    return (
        <Card className={earned ? 'border-primary' : 'opacity-50 grayscale'}>
            <CardHeader>
                <div className="flex items-start justify-between">
                    <div className="flex items-center space-x-3">
                        <div 
                            className="text-4xl p-3 rounded-lg"
                            style={{ backgroundColor: earned ? badge.color + '20' : '#f3f4f6' }}
                        >
                            {badge.icon}
                        </div>
                        <div>
                            <CardTitle className="text-lg">{badge.name}</CardTitle>
                            <CardDescription className="text-xs">
                                {badge.points_reward} points
                            </CardDescription>
                        </div>
                    </div>
                    {earned && (
                        <Badge variant="secondary">Earned</Badge>
                    )}
                </div>
            </CardHeader>
            <CardContent>
                <p className="text-sm text-muted-foreground">{badge.description}</p>
                {earned && badge.earned_at && (
                    <p className="text-xs text-muted-foreground mt-2">
                        Earned on {format(new Date(badge.earned_at), 'MMM dd, yyyy')}
                    </p>
                )}
            </CardContent>
        </Card>
    );
}
