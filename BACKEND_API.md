# CARTA LMS - Backend API Documentation

## Enhanced Learning Features API

Base URL: `http://165.227.113.197/api`

All endpoints require authentication via Bearer token or session.

---

## 🎮 Gamification Endpoints

### Get User Stats
```
GET /api/gamification/stats
```
**Response:**
```json
{
  "success": true,
  "data": {
    "total_points": 450,
    "badges_earned": 3,
    "courses_completed": 2,
    "courses_in_progress": 3,
    "total_time_spent": 1250
  }
}
```

### Get User Badges
```
GET /api/gamification/badges
```
**Response:**
```json
{
  "success": true,
  "data": [
    {
      "id": 1,
      "user_id": 5,
      "badge_id": 1,
      "earned_at": "2025-12-01T10:00:00Z",
      "badge": {
        "name": "First Steps",
        "icon": "🎯",
        "description": "Complete your first lesson",
        "points_reward": 10
      }
    }
  ]
}
```

### Get Points History
```
GET /api/gamification/points?per_page=20
```

### Get Leaderboard
```
GET /api/gamification/leaderboard?limit=10
```

### Update Course Progress
```
POST /api/gamification/progress
Content-Type: application/json

{
  "course_id": 1,
  "lessons_completed": 5,
  "total_lessons": 10,
  "time_spent": 60
}
```

### Get Course Progress
```
GET /api/gamification/progress/{courseId}
```

### Get All Progress
```
GET /api/gamification/progress
```

---

## 📝 Assessment Endpoints

### Start Assessment Attempt
```
POST /api/assessments/start
Content-Type: application/json

{
  "course_id": 1,
  "quiz_id": 5,
  "assessment_type": "quiz",
  "max_score": 100
}
```

### Complete Assessment
```
POST /api/assessments/{attemptId}/complete
Content-Type: application/json

{
  "score": 85
}
```

### Get User Attempts
```
GET /api/assessments/attempts?course_id=1
```

### Get Attempt Stats
```
GET /api/assessments/stats
```

---

## 🤖 AI Lecturer Endpoints

### Get Available Lecturers
```
GET /api/ai-lecturers
```
**Response:**
```json
{
  "success": true,
  "data": [
    {
      "id": 1,
      "name": "Professor Alex",
      "avatar": "/avatars/professor-alex.png",
      "expertise_area": "Technology & Programming",
      "personality_traits": "Friendly, encouraging, patient",
      "teaching_style": "Socratic method"
    }
  ]
}
```

### Ask Question
```
POST /api/ai-lecturers/{lecturerId}/ask
Content-Type: application/json

{
  "question": "Can you explain recursion?",
  "context": {
    "course_id": 1,
    "lesson_id": 5
  }
}
```

**Response:**
```json
{
  "success": true,
  "lecturer": {
    "id": 1,
    "name": "Professor Alex",
    "avatar": "/avatars/professor-alex.png"
  },
  "question": "Can you explain recursion?",
  "answer": "Great question! Recursion is...",
  "timestamp": "2025-12-10T11:00:00Z"
}
```

---

## 📱 WhatsApp Reminder Endpoints

### Schedule Reminder
```
POST /api/reminders/schedule
Content-Type: application/json

{
  "course_id": 1,
  "phone_number": "+1234567890",
  "reminder_type": "completion",
  "scheduled_at": "2025-12-15T10:00:00Z",
  "custom_message": "Don't forget to complete your course!"
}
```

### Get User Reminders
```
GET /api/reminders?status=pending
```

### Cancel Reminder
```
DELETE /api/reminders/{reminderId}
```

### Send Pending Reminders (Admin/Cron)
```
POST /api/reminders/send-pending
```

---

## 📊 Progress Tracking Models

### LearnerProgress Model
```php
{
  id: int,
  user_id: int,
  course_id: int,
  completion_percentage: int (0-100),
  lessons_completed: int,
  total_lessons: int,
  time_spent_minutes: int,
  last_accessed_at: datetime,
  completed_at: datetime|null,
  created_at: datetime,
  updated_at: datetime
}
```

### AssessmentAttempt Model
```php
{
  id: int,
  user_id: int,
  course_id: int,
  quiz_id: int|null,
  assessment_type: string (quiz|exam|assignment),
  attempt_number: int,
  score: int,
  max_score: int,
  percentage: decimal,
  time_taken_minutes: int,
  status: string (completed|in_progress|abandoned),
  started_at: datetime,
  completed_at: datetime|null
}
```

### GamificationBadge Model
```php
{
  id: int,
  name: string,
  slug: string,
  description: text,
  icon: string,
  color: string,
  category: string (achievement|milestone|special),
  criteria_type: string,
  criteria_data: json,
  points_reward: int,
  is_active: boolean
}
```

### LearnerPoint Model
```php
{
  id: int,
  user_id: int,
  points: int,
  source: string,
  source_id: int|null,
  source_type: string|null,
  description: text
}
```

---

## 🔧 Service Classes

### GamificationService
- `trackProgress(User $user, int $courseId, int $lessonsCompleted, int $totalLessons, int $timeSpent)`
- `awardPoints(User $user, int $points, string $source, ...)`
- `checkAndAwardBadges(User $user)`
- `getUserStats(User $user)`
- `getLeaderboard(int $limit)`

### AssessmentService
- `startAttempt(User $user, Course $course, ...)`
- `completeAttempt(AssessmentAttempt $attempt, int $score)`
- `getUserAttempts(User $user, ?int $courseId)`
- `getAttemptStats(User $user)`

### AILecturerService
- `askQuestion(int $lecturerId, string $question, array $context)`
- `getAvailableLecturers()`

### ReminderService
- `scheduleReminder(User $user, Course $course, string $phoneNumber, ...)`
- `sendPendingReminders()`
- `sendReminder(LearnerCourseReminder $reminder)`
- `cancelReminder(int $reminderId)`

---

## 🎯 Example Usage

### Track Lesson Completion
```javascript
// When user completes a lesson
await fetch('/api/gamification/progress', {
  method: 'POST',
  headers: {
    'Content-Type': 'application/json',
    'Authorization': 'Bearer ' + token
  },
  body: JSON.stringify({
    course_id: 1,
    lessons_completed: 5,
    total_lessons: 10,
    time_spent: 45
  })
});
```

### Ask AI Lecturer
```javascript
const response = await fetch('/api/ai-lecturers/1/ask', {
  method: 'POST',
  headers: {
    'Content-Type': 'application/json',
    'Authorization': 'Bearer ' + token
  },
  body: JSON.stringify({
    question: "How does async/await work in JavaScript?",
    context: {
      course_id: 1,
      lesson_id: 5
    }
  })
});

const data = await response.json();
console.log(data.answer);
```

---

## 🔐 Authentication

All API endpoints require authentication. Include one of:

**Option 1: Bearer Token**
```
Authorization: Bearer {your-api-token}
```

**Option 2: Session Cookie**
```
Cookie: carter_lms_session={session-id}
```

---

## 📝 Notes

- All timestamps are in ISO 8601 format
- Points are automatically awarded when:
  - Completing a course (100 points)
  - Passing a quiz (10-50 points based on score)
  - Earning a badge (varies by badge)
  - Daily login (10 points)
- Badges are automatically checked and awarded when criteria are met
- WhatsApp reminders require configuration of WHATSAPP_TOKEN in .env
- AI Lecturer requires OPENAI_API_KEY in .env

---

## 🚀 Implementation Status

✅ Models Created
✅ Migrations Run
✅ Services Implemented
✅ Sample Data Seeded
🔨 API Controllers (In Progress)
🔨 Frontend Integration (Pending)
