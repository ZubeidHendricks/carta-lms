import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { ScrollArea } from '@/components/ui/scroll-area';
import { Badge } from '@/components/ui/badge';
import { format } from 'date-fns';
import { TrendingUp, TrendingDown, Minus } from 'lucide-react';

interface PointTransaction {
    id: number;
    points: number;
    source: string;
    description: string;
    created_at: string;
}

interface PointsHistoryProps {
    transactions: PointTransaction[];
}

const sourceColors: Record<string, string> = {
    course_completion: 'bg-green-500',
    quiz_passed: 'bg-blue-500',
    badge_earned: 'bg-purple-500',
    daily_login: 'bg-yellow-500',
};

export default function PointsHistory({ transactions }: PointsHistoryProps) {
    return (
        <Card>
            <CardHeader>
                <CardTitle>Points History</CardTitle>
            </CardHeader>
            <CardContent>
                <ScrollArea className="h-[400px] pr-4">
                    <div className="space-y-4">
                        {transactions.map(transaction => {
                            const isPositive = transaction.points > 0;
                            const Icon = isPositive ? TrendingUp : transaction.points < 0 ? TrendingDown : Minus;
                            
                            return (
                                <div
                                    key={transaction.id}
                                    className="flex items-start gap-3 p-3 rounded-lg hover:bg-muted transition-colors"
                                >
                                    <div className={`p-2 rounded-full ${sourceColors[transaction.source] || 'bg-gray-500'}`}>
                                        <Icon className="h-4 w-4 text-white" />
                                    </div>
                                    <div className="flex-1 min-w-0">
                                        <div className="flex items-start justify-between gap-2">
                                            <div>
                                                <p className="font-medium text-sm">
                                                    {transaction.description}
                                                </p>
                                                <p className="text-xs text-muted-foreground mt-1">
                                                    {format(new Date(transaction.created_at), 'MMM dd, yyyy HH:mm')}
                                                </p>
                                            </div>
                                            <Badge
                                                variant={isPositive ? 'default' : 'destructive'}
                                                className="shrink-0"
                                            >
                                                {isPositive ? '+' : ''}{transaction.points}
                                            </Badge>
                                        </div>
                                    </div>
                                </div>
                            );
                        })}
                        
                        {transactions.length === 0 && (
                            <div className="text-center text-muted-foreground py-8">
                                <p>No point transactions yet</p>
                                <p className="text-sm mt-2">Start learning to earn points!</p>
                            </div>
                        )}
                    </div>
                </ScrollArea>
            </CardContent>
        </Card>
    );
}
