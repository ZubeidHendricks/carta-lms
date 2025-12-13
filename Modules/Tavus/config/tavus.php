<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Tavus API Configuration
    |--------------------------------------------------------------------------
    |
    | Configure your Tavus API credentials and settings here.
    | Get your API key from https://platform.tavus.io/
    |
    */

    'api_key' => env('TAVUS_API_KEY', ''),
    'api_url' => env('TAVUS_API_URL', 'https://tavusapi.com'),
    
    /*
    |--------------------------------------------------------------------------
    | Replica Settings
    |--------------------------------------------------------------------------
    */
    
    'default_replica_id' => env('TAVUS_DEFAULT_REPLICA_ID', null),
    
    /*
    |--------------------------------------------------------------------------
    | Video Settings
    |--------------------------------------------------------------------------
    */
    
    'default_video_name' => env('TAVUS_DEFAULT_VIDEO_NAME', 'LMS Video'),
    'default_background_source_url' => env('TAVUS_DEFAULT_BACKGROUND_URL', null),
    
    /*
    |--------------------------------------------------------------------------
    | Conversation Settings
    |--------------------------------------------------------------------------
    */
    
    'enable_conversation' => env('TAVUS_ENABLE_CONVERSATION', true),
    'conversation_timeout' => env('TAVUS_CONVERSATION_TIMEOUT', 300), // seconds
    
    /*
    |--------------------------------------------------------------------------
    | Storage Settings
    |--------------------------------------------------------------------------
    */
    
    'store_videos_locally' => env('TAVUS_STORE_VIDEOS_LOCALLY', true),
    'video_storage_disk' => env('TAVUS_VIDEO_STORAGE_DISK', 'public'),
    'video_storage_path' => env('TAVUS_VIDEO_STORAGE_PATH', 'tavus-videos'),
];
