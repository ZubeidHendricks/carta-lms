# CARTA LMS - Frontend Components Documentation

## 🎨 Enhanced Learning Features - Frontend

All frontend components have been built using React, TypeScript, and shadcn/ui components.

---

## 📁 File Structure

```
resources/js/
├── components/gamification/
│   ├── stats-card.tsx              # Reusable stats display card
│   ├── badge-card.tsx              # Badge display component
│   ├── leaderboard.tsx             # Leaderboard rankings
│   ├── course-progress-card.tsx    # Course progress display
│   ├── ai-chat-interface.tsx       # AI tutor chat UI
│   └── points-history.tsx          # Points transaction history
│
└── pages/student/
    ├── dashboard/
    │   └── index.tsx               # Main student dashboard
    ├── badges/
    │   └── index.tsx               # Badges & achievements page
    ├── progress/
    │   └── index.tsx               # Course progress tracking
    ├── leaderboard/
    │   └── index.tsx               # Leaderboard page
    └── ai-tutor/
        └── index.tsx               # AI tutor page
```

---

## 🧩 Components

### 1. **StatsCard**
Displays key metrics with icons.

**Props:**
```typescript
{
  title: string;
  value: string | number;
  icon: 'trophy' | 'target' | 'clock' | 'award';
  subtitle?: string;
  progress?: number;
}
```

**Usage:**
```tsx
<StatsCard
  title="Total Points"
  value={450}
  icon="trophy"
  subtitle="Keep learning!"
/>
```

---

### 2. **BadgeCard**
Shows badge details with earned/locked state.

**Props:**
```typescript
{
  badge: {
    id: number;
    name: string;
    description: string;
    icon: string;
    color: string;
    points_reward: number;
    earned_at?: string;
  };
  earned?: boolean;
}
```

**Features:**
- Visual distinction between earned/locked badges
- Shows earning date
- Displays points reward
- Color-coded by badge type

---

### 3. **Leaderboard**
Competitive rankings with special styling for top 3.

**Props:**
```typescript
{
  users: LeaderboardUser[];
  currentUserId?: number;
}
```

**Features:**
- 🏆 Gold medal for #1
- 🥈 Silver medal for #2
- 🥉 Bronze medal for #3
- Highlights current user
- Shows total points and badges

---

### 4. **CourseProgressCard**
Tracks individual course progress.

**Features:**
- Progress bar visualization
- Lessons completed counter
- Time spent tracking
- Last accessed timestamp
- Completion checkmark

---

### 5. **AIChatInterface**
Real-time chat with AI tutors.

**Features:**
- Message history
- Typing indicator
- Context-aware responses
- Multiple AI personas
- Course context integration

**Usage:**
```tsx
<AIChatInterface
  lecturer={professor}
  courseContext={{ course_id: 1, lesson_id: 5 }}
/>
```

---

### 6. **PointsHistory**
Transaction log for earned points.

**Features:**
- Chronological listing
- Color-coded by source
- Point value badges
- Source descriptions
- Timestamp display

---

## 📄 Pages

### 1. **Student Dashboard** (`/student/dashboard`)

**Features:**
- Overview stats (points, badges, courses, time)
- Recent course progress
- Points history timeline
- Recent badges earned
- Leaderboard preview

**Data Required:**
```typescript
{
  stats: UserStats;
  recentProgress: CourseProgress[];
  recentBadges: BadgeEarned[];
  leaderboard: LeaderboardUser[];
  pointsHistory: PointTransaction[];
}
```

---

### 2. **Badges Page** (`/student/badges`)

**Features:**
- Completion statistics
- Earned badges grid
- Available badges grid
- Badge categories
- Filtering tabs

**Sections:**
- Stats overview (earned, locked, points from badges)
- Tabbed view (Earned / Available / All)
- Badge cards with criteria

---

### 3. **Progress Page** (`/student/progress`)

**Features:**
- Overall statistics
- Completion rate visualization
- Course list with progress
- Filtering by status
- Time spent analytics

**Tabs:**
- All Courses
- In Progress
- Completed

---

### 4. **Leaderboard Page** (`/student/leaderboard`)

**Features:**
- User's current rank
- Top learners list
- Timeframe filters
- Competitive metrics

**Timeframes:**
- All Time
- This Month
- This Week

---

### 5. **AI Tutor Page** (`/student/ai-tutor`)

**Features:**
- Lecturer selection panel
- Chat interface
- Lecturer profiles
- Context-aware help
- Course integration

**AI Lecturers:**
- Professor Alex (Technology & Programming)
- Dr. Sarah Chen (Data Science & Analytics)

---

## 🎯 Usage Examples

### Tracking Progress
```typescript
// In a lesson component
import { router } from '@inertiajs/react';

const trackProgress = () => {
  router.post('/api/gamification/progress', {
    course_id: courseId,
    lessons_completed: 5,
    total_lessons: 10,
    time_spent: 45
  });
};
```

### Asking AI Tutor
```typescript
const askQuestion = async (question: string) => {
  const response = await fetch('/api/ai-lecturers/1/ask', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify({
      question,
      context: { course_id: 1 }
    })
  });
  return response.json();
};
```

---

## 🎨 Styling

All components use:
- **Tailwind CSS** for utility classes
- **shadcn/ui** for base components
- **Lucide React** for icons
- **Dark mode** support built-in

### Color Scheme
- Primary: `text-primary`, `bg-primary`
- Success: `text-green-600 dark:text-green-400`
- Warning: `text-yellow-600 dark:text-yellow-400`
- Muted: `text-muted-foreground`

---

## 📱 Responsive Design

All components are fully responsive:
- **Mobile**: Single column layout
- **Tablet** (md): 2-column grids
- **Desktop** (lg): 3-4 column grids

Example:
```tsx
<div className="grid gap-4 md:grid-cols-2 lg:grid-cols-4">
  {/* Stats cards */}
</div>
```

---

## 🔔 Real-time Updates

Components can be enhanced with real-time updates using:

```typescript
import { usePage } from '@inertiajs/react';
import Echo from 'laravel-echo';

// Listen for badge earned event
window.Echo.private(`user.${userId}`)
  .listen('BadgeEarned', (e) => {
    // Show notification
    toast.success(`You earned: ${e.badge.name}!`);
  });
```

---

## 🧪 Testing Components

Example test structure:
```typescript
import { render, screen } from '@testing-library/react';
import StatsCard from '@/components/gamification/stats-card';

test('renders stats card with correct value', () => {
  render(
    <StatsCard
      title="Total Points"
      value={450}
      icon="trophy"
    />
  );
  expect(screen.getByText('450')).toBeInTheDocument();
});
```

---

## 🚀 Next Steps

### To Complete Frontend:
1. **Add Routes** - Update `routes/web.php` with new pages
2. **Create Controllers** - Build Inertia controllers for each page
3. **Add Navigation** - Update menu with new links
4. **Integrate APIs** - Connect to backend endpoints
5. **Add Notifications** - Toast messages for badge earned, points awarded
6. **Build Admin Views** - Badge management, analytics dashboard

### Suggested Additions:
- Achievement notifications (toast/modal)
- Progress animations
- Badge unlock animations
- Confetti effect for milestones
- Social sharing for achievements
- Export progress reports

---

## 📊 Data Flow

```
User Action (Complete Lesson)
    ↓
Frontend Component (Course Player)
    ↓
API Call (/api/gamification/progress)
    ↓
GamificationService (Backend)
    ↓
Database Update
    ↓
Check Badges & Award Points
    ↓
Response to Frontend
    ↓
Update UI + Show Notification
```

---

## 🎓 Example Integration

### In a Course Player Component:
```typescript
import { useEffect } from 'react';
import { router } from '@inertiajs/react';

export default function LessonPlayer({ lesson, course }) {
  // Track when lesson is completed
  const handleLessonComplete = () => {
    router.post('/api/gamification/progress', {
      course_id: course.id,
      lessons_completed: course.lessons_completed + 1,
      total_lessons: course.total_lessons,
      time_spent: lesson.duration
    }, {
      onSuccess: (response) => {
        // Check if badge was earned
        if (response.badges_earned?.length > 0) {
          showBadgeNotification(response.badges_earned);
        }
      }
    });
  };

  return (
    // Lesson player UI
  );
}
```

---

## ✨ Summary

**Components Created: 6**
- StatsCard
- BadgeCard
- Leaderboard
- CourseProgressCard
- AIChatInterface
- PointsHistory

**Pages Created: 5**
- Student Dashboard
- Badges Page
- Progress Page
- Leaderboard Page
- AI Tutor Page

**Status: ✅ Ready for Integration**

All components are built, documented, and ready to connect to the backend APIs!
