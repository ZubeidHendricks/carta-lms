-- Tavus Integration PostgreSQL Migration
-- Creates 5 tables for Tavus AI Video Integration

-- Table 1: tavus_replicas
CREATE TABLE IF NOT EXISTS tavus_replicas (
  id BIGSERIAL PRIMARY KEY,
  user_id BIGINT REFERENCES users(id) ON DELETE CASCADE,
  replica_id VARCHAR(255) NOT NULL UNIQUE,
  replica_name VARCHAR(255) NOT NULL,
  training_video_url TEXT,
  status VARCHAR(255) NOT NULL DEFAULT 'pending',
  callback_url TEXT,
  metadata JSONB,
  trained_at TIMESTAMP,
  created_at TIMESTAMP,
  updated_at TIMESTAMP
);

CREATE INDEX IF NOT EXISTS tavus_replicas_user_id_idx ON tavus_replicas(user_id);
CREATE INDEX IF NOT EXISTS tavus_replicas_status_idx ON tavus_replicas(status);

-- Table 2: tavus_videos  
CREATE TABLE IF NOT EXISTS tavus_videos (
  id BIGSERIAL PRIMARY KEY,
  user_id BIGINT REFERENCES users(id) ON DELETE CASCADE,
  course_id BIGINT,
  video_id VARCHAR(255) NOT NULL UNIQUE,
  video_name VARCHAR(255) NOT NULL,
  replica_id VARCHAR(255) NOT NULL,
  script TEXT,
  background_source_url TEXT,
  status VARCHAR(255) NOT NULL DEFAULT 'pending',
  download_url TEXT,
  hosted_url TEXT,
  local_path VARCHAR(255),
  duration INTEGER,
  variables JSONB,
  metadata JSONB,
  completed_at TIMESTAMP,
  created_at TIMESTAMP,
  updated_at TIMESTAMP
);

CREATE INDEX IF NOT EXISTS tavus_videos_user_id_idx ON tavus_videos(user_id);
CREATE INDEX IF NOT EXISTS tavus_videos_course_id_idx ON tavus_videos(course_id);
CREATE INDEX IF NOT EXISTS tavus_videos_status_idx ON tavus_videos(status);
CREATE INDEX IF NOT EXISTS tavus_videos_replica_id_idx ON tavus_videos(replica_id);

-- Table 3: tavus_conversations
CREATE TABLE IF NOT EXISTS tavus_conversations (
  id BIGSERIAL PRIMARY KEY,
  user_id BIGINT REFERENCES users(id) ON DELETE CASCADE,
  conversation_id VARCHAR(255) NOT NULL UNIQUE,
  conversation_name VARCHAR(255),
  replica_id VARCHAR(255) NOT NULL,
  persona_id VARCHAR(255),
  conversational_context TEXT,
  custom_greeting TEXT,
  status VARCHAR(255) NOT NULL DEFAULT 'active',
  duration INTEGER,
  properties JSONB,
  started_at TIMESTAMP,
  ended_at TIMESTAMP,
  created_at TIMESTAMP,
  updated_at TIMESTAMP
);

CREATE INDEX IF NOT EXISTS tavus_conversations_user_id_idx ON tavus_conversations(user_id);
CREATE INDEX IF NOT EXISTS tavus_conversations_status_idx ON tavus_conversations(status);
CREATE INDEX IF NOT EXISTS tavus_conversations_replica_id_idx ON tavus_conversations(replica_id);

-- Table 4: tavus_webhooks
CREATE TABLE IF NOT EXISTS tavus_webhooks (
  id BIGSERIAL PRIMARY KEY,
  event_type VARCHAR(255) NOT NULL,
  resource_id VARCHAR(255),
  resource_type VARCHAR(255) NOT NULL,
  payload JSONB NOT NULL,
  processed BOOLEAN NOT NULL DEFAULT FALSE,
  processed_at TIMESTAMP,
  created_at TIMESTAMP,
  updated_at TIMESTAMP
);

CREATE INDEX IF NOT EXISTS tavus_webhooks_event_type_idx ON tavus_webhooks(event_type);
CREATE INDEX IF NOT EXISTS tavus_webhooks_resource_id_idx ON tavus_webhooks(resource_id);
CREATE INDEX IF NOT EXISTS tavus_webhooks_processed_idx ON tavus_webhooks(processed);

-- Table 5: tavus_personas
CREATE TABLE IF NOT EXISTS tavus_personas (
  id BIGSERIAL PRIMARY KEY,
  user_id BIGINT REFERENCES users(id) ON DELETE CASCADE,
  persona_id VARCHAR(255) NOT NULL UNIQUE,
  persona_name VARCHAR(255) NOT NULL,
  system_prompt TEXT,
  context TEXT,
  layers JSONB,
  default_replica_id VARCHAR(255),
  properties JSONB,
  metadata JSONB,
  is_active BOOLEAN NOT NULL DEFAULT TRUE,
  created_at TIMESTAMP,
  updated_at TIMESTAMP
);

CREATE INDEX IF NOT EXISTS tavus_personas_user_id_idx ON tavus_personas(user_id);
CREATE INDEX IF NOT EXISTS tavus_personas_persona_id_idx ON tavus_personas(persona_id);
CREATE INDEX IF NOT EXISTS tavus_personas_is_active_idx ON tavus_personas(is_active);
