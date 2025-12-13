# Landing Page Customization Guide

## 1. Change Site Title & Favicon (Remove "Mentor")

### Via Admin Dashboard:
1. Login as **Admin** at http://165.227.113.197
2. Go to **Settings** → **General Settings**
3. Update:
   - **Site Name**: Change from "Mentor Learning Management System" to your name
   - **Site Title**: Change accordingly
   - **Favicon**: Upload your favicon (recommended: 32x32 or 64x64 PNG)
   - **Logo Light**: Upload your logo
   - **Logo Dark**: Upload your dark mode logo

### Via Database (Quick Fix):
```sql
UPDATE settings 
SET value = 'Your LMS Name' 
WHERE key = 'name';

UPDATE settings 
SET value = 'Your Site Description' 
WHERE key = 'description';
```

---

## 2. Customize Landing Page Sections

The landing page uses sections from the database. Here's how to customize:

### Remove Companies Section

**Option A: Via Admin Panel**
1. Go to **Pages** → **Home Page**
2. Find "Companies" or "Trusted By" section
3. Click **Delete** or **Disable**

**Option B: Edit the Home Page Component**

File: `resources/js/pages/intro/home-1.tsx` (or whatever home page is active)

Look for sections like:
- `<Companies />`
- `<TrustedBy />`
- `<Partners />`

Comment them out or remove them:
```tsx
{/* <Companies /> */}
```

### Remove Fake Numbers/Statistics

Look for sections like:
- `<Statistics />`
- `<SuccessStatistics />`
- `<StudentFeedback />` (if showing fake numbers)

Edit these files:
- `resources/js/pages/intro/partials/home-1/success-statistics.tsx`
- `resources/js/pages/intro/partials/home-1/statistics.tsx`

Either remove the components or update with real data.

---

## 3. Add Your Own Content - "What the System Can Do"

### Create a Features Section

Edit: `resources/js/pages/intro/home-1.tsx` (or your active home page)

Add a features section after the hero:

```tsx
{/* Features Section */}
<section className="py-20 bg-background">
  <div className="container mx-auto px-4">
    <h2 className="text-3xl font-bold text-center mb-12">What Our LMS Can Do</h2>
    
    <div className="grid md:grid-cols-3 gap-8">
      <div className="text-center p-6 rounded-lg border">
        <div className="text-4xl mb-4">🎓</div>
        <h3 className="text-xl font-semibold mb-2">Interactive Courses</h3>
        <p className="text-muted-foreground">
          Create engaging courses with video lessons, quizzes, and assignments
        </p>
      </div>
      
      <div className="text-center p-6 rounded-lg border">
        <div className="text-4xl mb-4">🤖</div>
        <h3 className="text-xl font-semibold mb-2">AI-Powered Tutoring</h3>
        <p className="text-muted-foreground">
          Get instant help from AI tutors with Tavus interactive video technology
        </p>
      </div>
      
      <div className="text-center p-6 rounded-lg border">
        <div className="text-4xl mb-4">📊</div>
        <h3 className="text-xl font-semibold mb-2">Progress Tracking</h3>
        <p className="text-muted-foreground">
          Monitor student progress with detailed analytics and gamification
        </p>
      </div>
      
      <div className="text-center p-6 rounded-lg border">
        <div className="text-4xl mb-4">🎥</div>
        <h3 className="text-xl font-semibold mb-2">Live Classes</h3>
        <p className="text-muted-foreground">
          Conduct live virtual classes with Zoom integration
        </p>
      </div>
      
      <div className="text-center p-6 rounded-lg border">
        <div className="text-4xl mb-4">📝</div>
        <h3 className="text-xl font-semibold mb-2">Exams & Certificates</h3>
        <p className="text-muted-foreground">
          Create exams and issue certificates upon completion
        </p>
      </div>
      
      <div className="text-center p-6 rounded-lg border">
        <div className="text-4xl mb-4">💰</div>
        <h3 className="text-xl font-semibold mb-2">Monetization</h3>
        <p className="text-muted-foreground">
          Sell courses with integrated payment gateways
        </p>
      </div>
    </div>
  </div>
</section>
```

---

## 4. Replace Images

### Hero Section Images
Look for `<Hero />` component in your home page.

Edit: `resources/js/pages/intro/partials/home-1/hero.tsx`

Replace image URLs:
```tsx
<img 
  src="/your-hero-image.jpg" 
  alt="Your LMS" 
/>
```

### Upload Your Images
1. Place images in: `public/images/`
2. Reference them as: `/images/your-image.jpg`

---

## 5. Quick Script to Remove Unwanted Sections

Create this script: `customize-landing.sh`

```bash
#!/bin/bash

# This will help you quickly customize the landing page

cd resources/js/pages/intro

# Find and list all sections
echo "Current sections in home pages:"
grep -r "<[A-Z].*\/>" home-1.tsx | grep -v "^//"

echo ""
echo "To remove a section, edit the file and comment it out with {/* */}"
```

---

## 6. Alternative: Use a Different Home Page Template

The system has 5 home page templates:
- `home-1` - Collaborative 1
- `home-2` - Collaborative 2  
- `home-3` - Collaborative 3
- `home-4` - Administrative 1
- `home-5` - Administrative 2

**To change the active home page:**

Via Database:
```sql
UPDATE settings 
SET value = 'home-2' 
WHERE key = 'intro_page';
```

Or via Admin Panel:
Settings → Appearance → Select Home Page Template

---

## 7. Step-by-Step Quick Customization

### Step 1: Change Title & Favicon
```bash
# SSH into server
ssh -i ~/.ssh/dunyawii_key root@165.227.113.197

# Update database
docker-compose exec -T db mysql -u mentor_user -pSecurePassword123! mentor_lms -e "
UPDATE settings SET value = 'YourLMS' WHERE key = 'name';
UPDATE settings SET value = 'Best Online Learning Platform' WHERE key = 'description';
"
```

### Step 2: Disable Sections You Don't Want

I'll create a cleaned-up version of home-1 for you without companies and fake stats.

---

## Files to Edit

1. **Main Home Page**: `resources/js/pages/intro/home-1.tsx`
2. **Hero Section**: `resources/js/pages/intro/partials/home-1/hero.tsx`
3. **Companies Section**: `resources/js/pages/intro/partials/home-1/companies.tsx` (remove)
4. **Statistics**: `resources/js/pages/intro/partials/home-1/success-statistics.tsx` (update or remove)

---

## Next Steps

Would you like me to:

1. ✅ Create a clean version of home-1 without companies/fake stats?
2. ✅ Create a new features section highlighting system capabilities?
3. ✅ Update the title/favicon in database now?
4. ⏳ Need actual images from you to replace placeholders

Let me know and I'll implement the changes!
