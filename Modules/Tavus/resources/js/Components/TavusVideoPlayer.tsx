import React, { useEffect, useState } from 'react';
import axios from 'axios';

interface TavusVideoPlayerProps {
    videoId: number;
    autoPlay?: boolean;
    className?: string;
    onReady?: (url: string) => void;
    onError?: (error: string) => void;
}

interface TavusVideoData {
    id: number;
    video_id: string;
    video_name: string;
    status: 'pending' | 'processing' | 'ready' | 'failed';
    download_url?: string;
    hosted_url?: string;
    local_path?: string;
    duration?: number;
    completed_at?: string;
}

export default function TavusVideoPlayer({
    videoId,
    autoPlay = false,
    className = '',
    onReady,
    onError,
}: TavusVideoPlayerProps) {
    const [video, setVideo] = useState<TavusVideoData | null>(null);
    const [loading, setLoading] = useState(true);
    const [error, setError] = useState<string | null>(null);
    const [progress, setProgress] = useState(0);

    useEffect(() => {
        loadVideo();
    }, [videoId]);

    useEffect(() => {
        if (!video) return;

        if (video.status === 'ready') {
            setLoading(false);
            const url = getVideoUrl();
            if (url) {
                onReady?.(url);
            }
        } else if (video.status === 'failed') {
            setError('Video generation failed');
            setLoading(false);
            onError?.('Video generation failed');
        } else {
            // Poll for status updates
            const interval = setInterval(() => {
                checkVideoStatus();
                // Simulate progress
                setProgress((prev) => Math.min(prev + 5, 95));
            }, 5000);

            return () => clearInterval(interval);
        }
    }, [video]);

    const loadVideo = async () => {
        try {
            const response = await axios.get(`/api/tavus/videos/${videoId}`);
            setVideo(response.data.data);
        } catch (err: any) {
            const errorMessage = err.response?.data?.message || 'Failed to load video';
            setError(errorMessage);
            setLoading(false);
            onError?.(errorMessage);
        }
    };

    const checkVideoStatus = async () => {
        try {
            const response = await axios.post(`/api/tavus/videos/${videoId}/sync`);
            setVideo(response.data.data);
        } catch (err) {
            console.error('Failed to sync video status', err);
        }
    };

    const getVideoUrl = (): string | null => {
        if (!video) return null;

        if (video.local_path) {
            return `/storage/${video.local_path}`;
        }
        return video.hosted_url || video.download_url || null;
    };

    const formatDuration = (seconds?: number): string => {
        if (!seconds) return '0:00';
        const mins = Math.floor(seconds / 60);
        const secs = seconds % 60;
        return `${mins}:${secs.toString().padStart(2, '0')}`;
    };

    if (loading) {
        return (
            <div className={`tavus-video-loading ${className}`}>
                <div className="flex flex-col items-center justify-center p-8 bg-gray-50 rounded-lg">
                    <div className="relative w-32 h-32 mb-4">
                        <div className="absolute inset-0 rounded-full border-4 border-gray-200"></div>
                        <div
                            className="absolute inset-0 rounded-full border-4 border-blue-500 border-t-transparent animate-spin"
                            style={{ borderTopColor: 'transparent' }}
                        ></div>
                    </div>
                    <h3 className="text-lg font-semibold text-gray-800 mb-2">
                        {video?.status === 'processing'
                            ? 'Generating Your Personalized Video'
                            : 'Loading Video'}
                    </h3>
                    <div className="w-full max-w-xs bg-gray-200 rounded-full h-2 mb-2">
                        <div
                            className="bg-blue-500 h-2 rounded-full transition-all duration-500"
                            style={{ width: `${progress}%` }}
                        ></div>
                    </div>
                    <p className="text-sm text-gray-600">
                        {progress}% Complete • This may take a few minutes
                    </p>
                </div>
            </div>
        );
    }

    if (error) {
        return (
            <div className={`tavus-video-error ${className}`}>
                <div className="p-6 bg-red-50 border border-red-200 rounded-lg">
                    <div className="flex items-center mb-2">
                        <svg
                            className="w-6 h-6 text-red-500 mr-2"
                            fill="none"
                            strokeLinecap="round"
                            strokeLinejoin="round"
                            strokeWidth="2"
                            viewBox="0 0 24 24"
                            stroke="currentColor"
                        >
                            <path d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <h3 className="text-lg font-semibold text-red-800">Error Loading Video</h3>
                    </div>
                    <p className="text-sm text-red-600">{error}</p>
                    <button
                        onClick={loadVideo}
                        className="mt-4 px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700 transition"
                    >
                        Retry
                    </button>
                </div>
            </div>
        );
    }

    if (!video) return null;

    const videoUrl = getVideoUrl();

    if (!videoUrl) {
        return (
            <div className={`tavus-video-unavailable ${className}`}>
                <div className="p-6 bg-yellow-50 border border-yellow-200 rounded-lg">
                    <p className="text-yellow-800">Video URL not available</p>
                </div>
            </div>
        );
    }

    return (
        <div className={`tavus-video-player ${className}`}>
            <div className="relative">
                <video
                    controls
                    autoPlay={autoPlay}
                    className="w-full rounded-lg shadow-lg"
                    src={videoUrl}
                >
                    Your browser does not support the video tag.
                </video>
                {video.duration && (
                    <div className="absolute bottom-4 right-4 bg-black bg-opacity-75 text-white px-3 py-1 rounded text-sm">
                        {formatDuration(video.duration)}
                    </div>
                )}
            </div>
            <div className="mt-4">
                <h4 className="text-lg font-semibold text-gray-800">{video.video_name}</h4>
                {video.completed_at && (
                    <p className="text-sm text-gray-600 mt-1">
                        Generated on {new Date(video.completed_at).toLocaleDateString()}
                    </p>
                )}
            </div>
        </div>
    );
}
