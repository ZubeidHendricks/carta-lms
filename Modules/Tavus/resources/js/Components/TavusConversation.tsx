import React, { useEffect, useRef, useState } from 'react';
import axios from 'axios';

interface TavusConversationProps {
    replicaId: string;
    personaId?: string;
    conversationalContext?: string;
    customGreeting?: string;
    className?: string;
    onStart?: (conversationId: string) => void;
    onEnd?: () => void;
    onError?: (error: string) => void;
}

export default function TavusConversation({
    replicaId,
    personaId,
    conversationalContext = 'You are a helpful instructor.',
    customGreeting = 'Hello! How can I help you today?',
    className = '',
    onStart,
    onEnd,
    onError,
}: TavusConversationProps) {
    const [conversationId, setConversationId] = useState<string | null>(null);
    const [loading, setLoading] = useState(true);
    const [error, setError] = useState<string | null>(null);
    const containerRef = useRef<HTMLDivElement>(null);
    const cviRef = useRef<any>(null);

    useEffect(() => {
        startConversation();
        loadTavusCVI();

        return () => {
            if (cviRef.current) {
                cviRef.current.destroy();
            }
        };
    }, []);

    useEffect(() => {
        if (conversationId && window.TavusCVI && containerRef.current) {
            initializeCVI();
        }
    }, [conversationId]);

    const loadTavusCVI = () => {
        if (window.TavusCVI) {
            return;
        }

        const script = document.createElement('script');
        script.src = 'https://tavusapi.com/cvi-sdk.js';
        script.async = true;
        script.onload = () => {
            if (conversationId) {
                initializeCVI();
            }
        };
        script.onerror = () => {
            setError('Failed to load Tavus CVI SDK');
            setLoading(false);
            onError?.('Failed to load Tavus CVI SDK');
        };
        document.body.appendChild(script);
    };

    const startConversation = async () => {
        try {
            const response = await axios.post('/api/tavus/conversations', {
                replica_id: replicaId,
                persona_id: personaId,
                conversational_context: conversationalContext,
                custom_greeting: customGreeting,
            });

            const { conversation_id } = response.data.data;
            setConversationId(conversation_id);
            onStart?.(conversation_id);
        } catch (err: any) {
            const errorMessage = err.response?.data?.message || 'Failed to start conversation';
            setError(errorMessage);
            setLoading(false);
            onError?.(errorMessage);
        }
    };

    const initializeCVI = () => {
        if (!conversationId || !containerRef.current) return;

        try {
            cviRef.current = new window.TavusCVI({
                conversationId: conversationId,
                container: containerRef.current,
                onConversationEnd: handleConversationEnd,
                onError: (err: any) => {
                    setError(err.message || 'Conversation error');
                    onError?.(err.message);
                },
            });
            setLoading(false);
        } catch (err: any) {
            setError('Failed to initialize conversation');
            setLoading(false);
            onError?.('Failed to initialize conversation');
        }
    };

    const handleConversationEnd = async () => {
        if (!conversationId) return;

        try {
            await axios.post(`/api/tavus/conversations/${conversationId}/end`);
            onEnd?.();
        } catch (err) {
            console.error('Failed to end conversation', err);
        }
    };

    const endConversation = () => {
        if (cviRef.current) {
            cviRef.current.end();
        }
        handleConversationEnd();
    };

    if (loading) {
        return (
            <div className={`tavus-conversation-loading ${className}`}>
                <div className="flex flex-col items-center justify-center p-8 bg-gray-50 rounded-lg h-[600px]">
                    <div className="animate-spin rounded-full h-16 w-16 border-b-2 border-blue-500 mb-4"></div>
                    <p className="text-gray-700">Starting conversation...</p>
                </div>
            </div>
        );
    }

    if (error) {
        return (
            <div className={`tavus-conversation-error ${className}`}>
                <div className="p-6 bg-red-50 border border-red-200 rounded-lg">
                    <h3 className="text-lg font-semibold text-red-800 mb-2">Error</h3>
                    <p className="text-sm text-red-600 mb-4">{error}</p>
                    <button
                        onClick={() => window.location.reload()}
                        className="px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700 transition"
                    >
                        Reload
                    </button>
                </div>
            </div>
        );
    }

    return (
        <div className={`tavus-conversation ${className}`}>
            <div className="relative">
                <div
                    ref={containerRef}
                    className="w-full h-[600px] rounded-lg shadow-lg overflow-hidden"
                />
                <button
                    onClick={endConversation}
                    className="absolute top-4 right-4 px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700 transition z-10"
                >
                    End Conversation
                </button>
            </div>
        </div>
    );
}

declare global {
    interface Window {
        TavusCVI: any;
    }
}
