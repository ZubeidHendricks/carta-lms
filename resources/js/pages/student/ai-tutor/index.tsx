import { Head } from '@inertiajs/react';
import DashboardLayout from '@/layouts/dashboard/layout';
import AIChatInterface from '@/components/gamification/ai-chat-interface';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Avatar, AvatarFallback, AvatarImage } from '@/components/ui/avatar';
import { Badge } from '@/components/ui/badge';
import { Bot, Sparkles } from 'lucide-react';
import { useState } from 'react';

interface AILecturer {
    id: number;
    name: string;
    avatar: string;
    expertise_area: string;
    personality_traits: string;
    teaching_style: string;
}

interface Props {
    lecturers: AILecturer[];
    currentCourse?: {
        id: number;
        title: string;
    };
}

export default function AITutorPage({ lecturers, currentCourse }: Props) {
    const [selectedLecturer, setSelectedLecturer] = useState<AILecturer | null>(
        lecturers.length > 0 ? lecturers[0] : null
    );

    return (
        <DashboardLayout>
            <Head title="AI Tutor - Ask Questions" />

            <div className="container mx-auto p-6 space-y-6">
                {/* Header */}
                <div>
                    <h1 className="text-3xl font-bold flex items-center gap-2">
                        <Bot className="h-8 w-8 text-primary" />
                        AI Tutor
                    </h1>
                    <p className="text-muted-foreground mt-1">
                        Get instant help from our AI-powered instructors
                    </p>
                    {currentCourse && (
                        <Badge variant="secondary" className="mt-2">
                            Current Course: {currentCourse.title}
                        </Badge>
                    )}
                </div>

                <div className="grid gap-6 lg:grid-cols-4">
                    {/* Lecturer Selection */}
                    <div className="lg:col-span-1">
                        <Card>
                            <CardHeader>
                                <CardTitle className="text-lg flex items-center gap-2">
                                    <Sparkles className="h-5 w-5" />
                                    Select Tutor
                                </CardTitle>
                                <CardDescription>
                                    Choose your AI instructor
                                </CardDescription>
                            </CardHeader>
                            <CardContent className="space-y-3">
                                {lecturers.map(lecturer => (
                                    <button
                                        key={lecturer.id}
                                        onClick={() => setSelectedLecturer(lecturer)}
                                        className={`w-full text-left p-3 rounded-lg border transition-all ${
                                            selectedLecturer?.id === lecturer.id
                                                ? 'border-primary bg-primary/5'
                                                : 'hover:border-primary/50 hover:bg-muted'
                                        }`}
                                    >
                                        <div className="flex items-center gap-3">
                                            <Avatar>
                                                <AvatarImage src={lecturer.avatar} />
                                                <AvatarFallback>{lecturer.name[0]}</AvatarFallback>
                                            </Avatar>
                                            <div className="flex-1 min-w-0">
                                                <p className="font-medium text-sm truncate">
                                                    {lecturer.name}
                                                </p>
                                                <p className="text-xs text-muted-foreground truncate">
                                                    {lecturer.expertise_area}
                                                </p>
                                            </div>
                                        </div>
                                    </button>
                                ))}

                                {lecturers.length === 0 && (
                                    <div className="text-center text-muted-foreground py-8">
                                        <Bot className="h-12 w-12 mx-auto mb-2 opacity-20" />
                                        <p className="text-sm">No AI tutors available</p>
                                    </div>
                                )}
                            </CardContent>
                        </Card>

                        {/* Lecturer Info */}
                        {selectedLecturer && (
                            <Card className="mt-4">
                                <CardHeader>
                                    <CardTitle className="text-sm">About {selectedLecturer.name}</CardTitle>
                                </CardHeader>
                                <CardContent className="space-y-3 text-sm">
                                    <div>
                                        <p className="font-medium text-muted-foreground">Expertise</p>
                                        <p className="mt-1">{selectedLecturer.expertise_area}</p>
                                    </div>
                                    <div>
                                        <p className="font-medium text-muted-foreground">Teaching Style</p>
                                        <p className="mt-1">{selectedLecturer.teaching_style}</p>
                                    </div>
                                    <div>
                                        <p className="font-medium text-muted-foreground">Personality</p>
                                        <p className="mt-1">{selectedLecturer.personality_traits}</p>
                                    </div>
                                </CardContent>
                            </Card>
                        )}
                    </div>

                    {/* Chat Interface */}
                    <div className="lg:col-span-3">
                        {selectedLecturer ? (
                            <AIChatInterface
                                lecturer={selectedLecturer}
                                courseContext={currentCourse ? { course_id: currentCourse.id } : undefined}
                            />
                        ) : (
                            <Card className="h-[600px] flex items-center justify-center">
                                <div className="text-center text-muted-foreground">
                                    <Bot className="h-16 w-16 mx-auto mb-4 opacity-20" />
                                    <p className="text-lg">Select an AI tutor to start</p>
                                    <p className="text-sm mt-2">Choose from the available tutors on the left</p>
                                </div>
                            </Card>
                        )}
                    </div>
                </div>
            </div>
        </DashboardLayout>
    );
}
