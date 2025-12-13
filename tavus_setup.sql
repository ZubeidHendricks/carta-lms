-- Tavus Integration Database Setup
-- Run this SQL to create all required tables

-- Table: tavus_replicas
CREATE TABLE IF NOT EXISTS `tavus_replicas` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `replica_id` varchar(255) NOT NULL,
  `replica_name` varchar(255) NOT NULL,
  `training_video_url` text DEFAULT NULL,
  `status` varchar(255) NOT NULL DEFAULT 'pending',
  `callback_url` text DEFAULT NULL,
  `metadata` json DEFAULT NULL,
  `trained_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `tavus_replicas_replica_id_unique` (`replica_id`),
  KEY `tavus_replicas_user_id_index` (`user_id`),
  KEY `tavus_replicas_status_index` (`status`),
  CONSTRAINT `tavus_replicas_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: tavus_videos
CREATE TABLE IF NOT EXISTS `tavus_videos` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `course_id` bigint(20) UNSIGNED DEFAULT NULL,
  `video_id` varchar(255) NOT NULL,
  `video_name` varchar(255) NOT NULL,
  `replica_id` varchar(255) NOT NULL,
  `script` text DEFAULT NULL,
  `background_source_url` text DEFAULT NULL,
  `status` varchar(255) NOT NULL DEFAULT 'pending',
  `download_url` text DEFAULT NULL,
  `hosted_url` text DEFAULT NULL,
  `local_path` varchar(255) DEFAULT NULL,
  `duration` int(11) DEFAULT NULL,
  `variables` json DEFAULT NULL,
  `metadata` json DEFAULT NULL,
  `completed_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `tavus_videos_video_id_unique` (`video_id`),
  KEY `tavus_videos_user_id_index` (`user_id`),
  KEY `tavus_videos_course_id_index` (`course_id`),
  KEY `tavus_videos_status_index` (`status`),
  KEY `tavus_videos_replica_id_index` (`replica_id`),
  CONSTRAINT `tavus_videos_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `tavus_videos_course_id_foreign` FOREIGN KEY (`course_id`) REFERENCES `courses` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: tavus_conversations
CREATE TABLE IF NOT EXISTS `tavus_conversations` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `conversation_id` varchar(255) NOT NULL,
  `conversation_name` varchar(255) DEFAULT NULL,
  `replica_id` varchar(255) NOT NULL,
  `persona_id` varchar(255) DEFAULT NULL,
  `conversational_context` text DEFAULT NULL,
  `custom_greeting` text DEFAULT NULL,
  `status` varchar(255) NOT NULL DEFAULT 'active',
  `duration` int(11) DEFAULT NULL,
  `properties` json DEFAULT NULL,
  `started_at` timestamp NULL DEFAULT NULL,
  `ended_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `tavus_conversations_conversation_id_unique` (`conversation_id`),
  KEY `tavus_conversations_user_id_index` (`user_id`),
  KEY `tavus_conversations_status_index` (`status`),
  KEY `tavus_conversations_replica_id_index` (`replica_id`),
  CONSTRAINT `tavus_conversations_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: tavus_webhooks
CREATE TABLE IF NOT EXISTS `tavus_webhooks` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `event_type` varchar(255) NOT NULL,
  `resource_id` varchar(255) DEFAULT NULL,
  `resource_type` varchar(255) NOT NULL,
  `payload` json NOT NULL,
  `processed` tinyint(1) NOT NULL DEFAULT 0,
  `processed_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `tavus_webhooks_event_type_index` (`event_type`),
  KEY `tavus_webhooks_resource_id_index` (`resource_id`),
  KEY `tavus_webhooks_processed_index` (`processed`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: tavus_personas
CREATE TABLE IF NOT EXISTS `tavus_personas` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `persona_id` varchar(255) NOT NULL,
  `persona_name` varchar(255) NOT NULL,
  `system_prompt` text DEFAULT NULL,
  `context` text DEFAULT NULL,
  `layers` json DEFAULT NULL,
  `default_replica_id` varchar(255) DEFAULT NULL,
  `properties` json DEFAULT NULL,
  `metadata` json DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `tavus_personas_persona_id_unique` (`persona_id`),
  KEY `tavus_personas_user_id_index` (`user_id`),
  KEY `tavus_personas_persona_id_index` (`persona_id`),
  KEY `tavus_personas_is_active_index` (`is_active`),
  CONSTRAINT `tavus_personas_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
