import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Avatar, AvatarFallback, AvatarImage } from '@/components/ui/avatar';
import { Badge } from '@/components/ui/badge';
import { Trophy, Medal, Award } from 'lucide-react';

interface LeaderboardUser {
    id: number;
    name: string;
    email: string;
    total_points: number;
    badges_count: number;
    avatar?: string;
}

interface LeaderboardProps {
    users: LeaderboardUser[];
    currentUserId?: number;
}

const positionIcons = {
    1: Trophy,
    2: Medal,
    3: Award,
};

const positionColors = {
    1: 'text-yellow-500',
    2: 'text-gray-400',
    3: 'text-amber-600',
};

export default function Leaderboard({ users, currentUserId }: LeaderboardProps) {
    return (
        <Card>
            <CardHeader>
                <CardTitle className="flex items-center gap-2">
                    <Trophy className="h-5 w-5" />
                    Leaderboard
                </CardTitle>
            </CardHeader>
            <CardContent>
                <div className="space-y-4">
                    {users.map((user, index) => {
                        const position = index + 1;
                        const PositionIcon = positionIcons[position as keyof typeof positionIcons];
                        const isCurrentUser = user.id === currentUserId;

                        return (
                            <div
                                key={user.id}
                                className={`flex items-center gap-4 p-3 rounded-lg ${
                                    isCurrentUser ? 'bg-primary/10 border border-primary' : 'hover:bg-muted'
                                }`}
                            >
                                <div className="flex items-center gap-3 flex-1">
                                    <div className="relative">
                                        {PositionIcon ? (
                                            <PositionIcon 
                                                className={`h-6 w-6 ${positionColors[position as keyof typeof positionColors]}`} 
                                            />
                                        ) : (
                                            <span className="text-sm font-semibold text-muted-foreground w-6 text-center">
                                                {position}
                                            </span>
                                        )}
                                    </div>
                                    <Avatar className="h-10 w-10">
                                        <AvatarImage src={user.avatar} />
                                        <AvatarFallback>
                                            {user.name.split(' ').map(n => n[0]).join('').toUpperCase()}
                                        </AvatarFallback>
                                    </Avatar>
                                    <div className="flex-1 min-w-0">
                                        <p className="font-medium truncate">
                                            {user.name}
                                            {isCurrentUser && (
                                                <Badge variant="secondary" className="ml-2 text-xs">You</Badge>
                                            )}
                                        </p>
                                        <p className="text-sm text-muted-foreground">
                                            {user.badges_count} badges
                                        </p>
                                    </div>
                                </div>
                                <div className="text-right">
                                    <p className="font-bold text-lg">{user.total_points.toLocaleString()}</p>
                                    <p className="text-xs text-muted-foreground">points</p>
                                </div>
                            </div>
                        );
                    })}
                </div>
            </CardContent>
        </Card>
    );
}
