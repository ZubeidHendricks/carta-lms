import { useState } from 'react';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import { Textarea } from '@/components/ui/textarea';
import { Avatar, AvatarFallback, AvatarImage } from '@/components/ui/avatar';
import { Send, Loader2 } from 'lucide-react';
import { useForm } from '@inertiajs/react';

interface AILecturer {
    id: number;
    name: string;
    avatar: string;
    expertise_area: string;
}

interface Message {
    role: 'user' | 'assistant';
    content: string;
    timestamp: string;
}

interface AIChatInterfaceProps {
    lecturer: AILecturer;
    courseContext?: {
        course_id: number;
        lesson_id?: number;
    };
}

export default function AIChatInterface({ lecturer, courseContext }: AIChatInterfaceProps) {
    const [messages, setMessages] = useState<Message[]>([]);
    const { data, setData, post, processing } = useForm({
        question: '',
        lecturer_id: lecturer.id,
        context: courseContext,
    });

    const handleSubmit = (e: React.FormEvent) => {
        e.preventDefault();
        
        if (!data.question.trim()) return;

        // Add user message
        const userMessage: Message = {
            role: 'user',
            content: data.question,
            timestamp: new Date().toISOString(),
        };
        setMessages(prev => [...prev, userMessage]);

        // Send to API
        post('/api/ai-lecturers/ask', {
            preserveScroll: true,
            onSuccess: (response: any) => {
                const aiMessage: Message = {
                    role: 'assistant',
                    content: response.answer || 'I apologize, but I could not generate a response.',
                    timestamp: new Date().toISOString(),
                };
                setMessages(prev => [...prev, aiMessage]);
                setData('question', '');
            },
            onError: () => {
                const errorMessage: Message = {
                    role: 'assistant',
                    content: 'Sorry, I encountered an error. Please try again.',
                    timestamp: new Date().toISOString(),
                };
                setMessages(prev => [...prev, errorMessage]);
            },
        });
    };

    return (
        <Card className="flex flex-col h-[600px]">
            <CardHeader className="border-b">
                <div className="flex items-center gap-3">
                    <Avatar>
                        <AvatarImage src={lecturer.avatar} />
                        <AvatarFallback>{lecturer.name[0]}</AvatarFallback>
                    </Avatar>
                    <div>
                        <CardTitle className="text-lg">{lecturer.name}</CardTitle>
                        <CardDescription className="text-xs">
                            {lecturer.expertise_area}
                        </CardDescription>
                    </div>
                </div>
            </CardHeader>
            
            <CardContent className="flex-1 overflow-y-auto p-4 space-y-4">
                {messages.length === 0 && (
                    <div className="text-center text-muted-foreground py-8">
                        <p>Ask me anything about the course!</p>
                        <p className="text-sm mt-2">I'm here to help you learn.</p>
                    </div>
                )}
                
                {messages.map((message, index) => (
                    <div
                        key={index}
                        className={`flex gap-3 ${
                            message.role === 'user' ? 'justify-end' : 'justify-start'
                        }`}
                    >
                        {message.role === 'assistant' && (
                            <Avatar className="h-8 w-8">
                                <AvatarImage src={lecturer.avatar} />
                                <AvatarFallback>{lecturer.name[0]}</AvatarFallback>
                            </Avatar>
                        )}
                        <div
                            className={`rounded-lg px-4 py-2 max-w-[80%] ${
                                message.role === 'user'
                                    ? 'bg-primary text-primary-foreground'
                                    : 'bg-muted'
                            }`}
                        >
                            <p className="text-sm whitespace-pre-wrap">{message.content}</p>
                        </div>
                    </div>
                ))}
            </CardContent>

            <div className="border-t p-4">
                <form onSubmit={handleSubmit} className="flex gap-2">
                    <Textarea
                        value={data.question}
                        onChange={e => setData('question', e.target.value)}
                        placeholder="Ask a question..."
                        className="resize-none"
                        rows={2}
                        disabled={processing}
                    />
                    <Button type="submit" disabled={processing || !data.question.trim()}>
                        {processing ? (
                            <Loader2 className="h-4 w-4 animate-spin" />
                        ) : (
                            <Send className="h-4 w-4" />
                        )}
                    </Button>
                </form>
            </div>
        </Card>
    );
}
