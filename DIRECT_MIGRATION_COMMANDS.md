# 🚀 Direct Migration Commands - Choose Your Method

## Method 1: Copy & Paste SQL Directly

### Step 1: Connect to your database
```bash
mysql -u your_username -p
```

### Step 2: Select your database
```sql
USE your_database_name;
```

### Step 3: Copy and paste these SQL commands:

```sql
-- Table 1: Replicas
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

-- Table 2: Videos
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

-- Table 3: Conversations
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

-- Table 4: Webhooks
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

-- Table 5: Personas
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

-- Verify tables created
SHOW TABLES LIKE 'tavus_%';
```

### Step 4: Exit MySQL
```sql
EXIT;
```

---

## Method 2: One-Line Command

```bash
mysql -u YOUR_USERNAME -p YOUR_DATABASE < tavus_setup.sql
```

Replace:
- `YOUR_USERNAME` with your MySQL username
- `YOUR_DATABASE` with your database name

---

## Method 3: Interactive Script

```bash
./run_migration_simple.sh
```

Follow the prompts to enter your credentials.

---

## Method 4: phpMyAdmin

1. Open phpMyAdmin in your browser
2. Select your database
3. Click "SQL" tab
4. Open file: `tavus_setup.sql`
5. Copy all content
6. Paste into SQL query box
7. Click "Go"

---

## Method 5: Using Database Manager

If you use a database manager like:
- **TablePlus**
- **DBeaver**
- **MySQL Workbench**
- **HeidiSQL**

1. Connect to your database
2. Open SQL editor
3. Load file: `tavus_setup.sql`
4. Execute

---

## Verification

After running the migration, verify tables exist:

```sql
SHOW TABLES LIKE 'tavus_%';
```

You should see:
- tavus_conversations
- tavus_personas
- tavus_replicas
- tavus_videos
- tavus_webhooks

---

## If You Get Errors

### Error: "Table already exists"
- No problem! The migration uses `CREATE TABLE IF NOT EXISTS`
- Tables won't be recreated if they exist

### Error: "Foreign key constraint fails"
- Make sure `users` and `courses` tables exist first
- These are referenced by the foreign keys

### Error: "Access denied"
- Check your database credentials
- Make sure your user has CREATE TABLE permission

---

## Next Steps After Migration

1. **Clear Laravel cache:**
```bash
# If you can access PHP
php artisan config:clear
php artisan cache:clear
```

2. **Test the API:**
```bash
curl http://localhost:8000/api/tavus/replicas \
  -H "Authorization: Bearer YOUR_TOKEN"
```

3. **Check documentation:**
- `DEPLOYMENT_COMPLETE.md` - Full setup guide
- `PERSONA_ADDED.md` - Persona features
- `Modules/Tavus/PERSONA_GUIDE.md` - Complete guide

---

## Quick Test

After migration, create a test persona:

```sql
INSERT INTO tavus_personas (
  persona_id, 
  persona_name, 
  system_prompt,
  is_active,
  created_at,
  updated_at
) VALUES (
  'test_instructor',
  'Test Instructor',
  'You are a test instructor.',
  1,
  NOW(),
  NOW()
);

-- Verify
SELECT * FROM tavus_personas;
```

---

**Having trouble?** The SQL file is ready at: `tavus_setup.sql`
Just open it and copy the content into any SQL editor!
