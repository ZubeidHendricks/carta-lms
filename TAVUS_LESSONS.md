# Tavus Video Lessons - Location & Usage

## ✅ Already Integrated!

Tavus video lessons are **fully integrated** into the course player system.

## Where to Find Tavus Videos

### 1. **Creating Tavus Lessons (Instructor Dashboard)**

When creating or editing a course lesson:

1. Go to **Instructor Dashboard** → **Courses** → Edit Course → **Lessons**
2. Create a new lesson or edit existing one
3. In the **Lesson Type** dropdown, select **"Tavus AI Video"**
4. You'll see three fields appear:
   - **Tavus Conversation ID** (optional)
   - **Tavus Replica ID** (optional)  
   - **Tavus API Key** (required)

**Note:** Either `conversation_id` OR `replica_id` is required (not both).

### 2. **Viewing Tavus Lessons (Student Course Player)**

When a student plays a course:

1. Navigate to any course enrolled in
2. Click on a lesson with type "Tavus AI Video"
3. The Tavus interactive video player will load automatically
4. Students can interact with the AI avatar in real-time

## Technical Implementation

### Database Fields (section_lessons table)
```sql
tavus_conversation_id  VARCHAR(191) NULL
tavus_replica_id       VARCHAR(191) NULL  
tavus_api_key          TEXT NULL
```

### Frontend Components

**Player Component:** `resources/js/components/tavus-player.tsx`
- Uses `@tavus/cvi-ui` package
- `<DailyVideo>` component for rendering

**Lesson Viewer:** `resources/js/pages/course-player/partials/lesson-viewer.tsx`
- Line 39-46: Checks if `lesson.lesson_type === 'tavus'`
- Renders `<TavusPlayer>` with conversation/replica data

**Lesson Form:** `resources/js/pages/dashboard/courses/partials/forms/lesson-form.tsx`
- Line 28: "Tavus AI Video" option in lesson type dropdown
- Lines 293-335: Form fields for Tavus configuration

### Migration
**File:** `database/migrations/2025_12_11_000001_add_tavus_fields_to_section_lessons_table.php`
- ✅ Deployed and run on server

## How It Works

```
┌─────────────────────┐
│  Instructor creates │
│  lesson with type   │──┐
│  "Tavus AI Video"   │  │
└─────────────────────┘  │
                         │
                         ▼
         ┌───────────────────────────────┐
         │ Stores in section_lessons:    │
         │ - tavus_conversation_id       │
         │ - tavus_replica_id            │
         │ - tavus_api_key               │
         └───────────────────────────────┘
                         │
                         ▼
         ┌───────────────────────────────┐
         │ Student views course          │
         │ → Lesson Viewer detects       │
         │   lesson_type === 'tavus'     │
         └───────────────────────────────┘
                         │
                         ▼
         ┌───────────────────────────────┐
         │ TavusPlayer component loads   │
         │ → DailyVideo renders          │
         │ → Interactive AI video plays  │
         └───────────────────────────────┘
```

## Deployment Status

✅ **Database Migration:** Run successfully on server
✅ **Frontend Components:** Already built and deployed
✅ **Package Dependencies:** `@tavus/cvi-ui` installed

## Testing Tavus Lessons

### Create a Test Lesson:

1. Login as instructor at http://165.227.113.197
2. Go to Courses → Select a course → Lessons
3. Click "Add Lesson"
4. Fill in:
   - Title: "Test Tavus Video"
   - Lesson Type: **Tavus AI Video**
   - Tavus Replica ID: `your_replica_id_from_tavus`
   - Tavus API Key: `your_tavus_api_key`
5. Save

### View as Student:

1. Login as student
2. Enroll in the course
3. Click on the Tavus lesson
4. Interactive AI video should load

## Getting Tavus Credentials

You need to obtain from Tavus dashboard (https://tavus.io):

1. **API Key** - From your Tavus account settings
2. **Replica ID** - Create a replica in Tavus dashboard
3. **Conversation ID** (optional) - For continuing existing conversations

## Related Files

**Backend:**
- Migration: `database/migrations/2025_12_11_000001_add_tavus_fields_to_section_lessons_table.php`
- Model: Handled by existing `SectionLesson` model

**Frontend:**
- Player: `resources/js/components/tavus-player.tsx`
- Viewer: `resources/js/pages/course-player/partials/lesson-viewer.tsx`
- Form: `resources/js/pages/dashboard/courses/partials/forms/lesson-form.tsx`

**Dependencies:**
- `@tavus/cvi-ui` - Tavus video player component
- `@tavus/cvi-ui/dist/style.css` - Tavus styles

## API Integration (Tavus Module)

For advanced Tavus features, check:
- `Modules/Tavus/` - Full Tavus module with API integration
- Routes in `Modules/Tavus/routes/api.php`
- Controllers for replicas, videos, conversations, personas

## Summary

Tavus videos are **already working** in your LMS! Instructors just need to:
1. Select "Tavus AI Video" as lesson type
2. Provide Tavus API credentials
3. Students will see interactive AI video lessons automatically

No additional setup required - it's ready to use! 🎉
