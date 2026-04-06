# Academy Dashboard Page

## Overview

The Academy Dashboard is a specialized server-side rendered page component designed for learning management and educational analytics. It provides students and instructors with insights into course progress, assignments, instructors, and academic performance metrics.

**Key Path:** `src/app/[lang]/(dashboard)/(private)/apps/academy/dashboard/page.tsx`

**Type:** Async Server Component with Course Data Integration

**Data Source:** Server-side fetched via `getAcademyData()` server action

---

## Key Features

### Dashboard Composition

1. **Welcome Card** - Personalized greeting and quick actions
2. **Interested Topics** - Topics/subjects of interest tracking
3. **Popular Instructors** - Featured instructors with ratings
4. **Top Courses** - Most popular or recommended courses
5. **Upcoming Webinar** - Schedule and details of upcoming events
6. **Assignment Progress** - Student assignment completion tracking
7. **Course Table** - Detailed course listing with progress

### Educational Analytics

- **Personalization** - User-specific welcome and preferences
- **Course Discovery** - Popular courses and topics
- **Instructor Showcase** - Faculty highlights and ratings
- **Event Management** - Upcoming sessions and webinars
- **Progress Tracking** - Assignment and course completion
- **Course Management** - Detailed course information

---

## Page Layout

The academy dashboard uses a student-centric responsive layout:

**Desktop Layout (MD+):**
- Row 1: Full-width welcome card
- Row 2: Large topics section (8 cols) + Instructors (4 cols)
- Row 3: 3x Cards (4 cols each)
- Row 4: Full-width course table

**Tablet Layout (MD):**
- Row 1: Full-width welcome
- Row 2: Topics (8 cols), Instructors wrap
- Row 3: 2x Charts (6 cols each), remainder on next row
- Row 4: Full-width table

**Mobile Layout (XS/SM):**
- All components 12 cols (stacked)

---

## Components Used

### Hero/Welcome
- `WelcomeCard` - Personalized user greeting

### Primary Analytics
- `InterestedTopics` - Topic selection and preferences
- `PopularInstructors` - Instructor listing and ratings
- `TopCourses` - Course recommendations
- `UpcomingWebinar` - Event schedule
- `AssignmentProgress` - Progress tracking

### Table Component
- `CourseTable` - Detailed course listing with server data

---

## Type Definitions

```typescript
// Academy Dashboard - Main Page Component
const AcademyDashboard = async (): Promise<React.ReactNode>

// Academy data structure
interface AcademyData {
  courses: Course[]
  instructors?: Instructor[]
  topics?: Topic[]
}

interface Course {
  id: string
  title: string
  instructor: string
  progress?: number
  dueDate?: string
  status: 'active' | 'completed' | 'pending'
  students?: number
}

interface Instructor {
  id: string
  name: string
  rating?: number
  specialty?: string
  avatar?: string
}

interface Topic {
  id: string
  name: string
  interest?: boolean
  courseCount?: number
}

// Component props
interface CourseTableProps {
  courseData: Course[]
}

// Grid sizing
type GridBreakpoint = {
  xs?: number | 'auto'
  sm?: number | 'auto'
  md?: number | 'auto'
  lg?: number | 'auto'
}
```

---

## Component Breakdown

### Row 1: Welcome Section
- **WelcomeCard** (12 cols) - Full-width personalized greeting

### Row 2: Primary Analytics
- **InterestedTopics** (md: 8) - Topics of interest visualization
- **PopularInstructors** (sm: 6, md: 4) - Instructor listing

### Row 3: Learning Resources
- **TopCourses** (sm: 6, md: 4) - Course recommendations
- **UpcomingWebinar** (sm: 6, md: 4) - Event information
- **AssignmentProgress** (sm: 6, md: 4) - Progress tracking

### Row 4: Course Details
- **CourseTable** (12 cols) - Comprehensive course listing with server data

---

## Usage Example

```typescript
import Grid from '@mui/material/Grid'
import { getAcademyData } from '@/app/server/actions'
import WelcomeCard from '@views/apps/academy/dashboard/WelcomeCard'
import InterestedTopics from '@views/apps/academy/dashboard/InterestedTopics'
import PopularInstructors from '@views/apps/academy/dashboard/PopularInstructors'
import TopCourses from '@views/apps/academy/dashboard/TopCourses'
import UpcomingWebinar from '@views/apps/academy/dashboard/UpcomingWebinar'
import AssignmentProgress from '@views/apps/academy/dashboard/AssignmentProgress'
import CourseTable from '@views/apps/academy/dashboard/CourseTable'

const AcademyDashboard = async () => {
  // Fetch academy data server-side
  const data = await getAcademyData()

  return (
    <Grid container spacing={6}>
      {/* Welcome section */}
      <Grid size={{ xs: 12 }}>
        <WelcomeCard />
      </Grid>

      {/* Topics and instructors */}
      <Grid size={{ xs: 12, md: 8 }}>
        <InterestedTopics />
      </Grid>
      <Grid size={{ xs: 12, sm: 6, md: 4 }}>
        <PopularInstructors />
      </Grid>

      {/* Learning resources */}
      <Grid size={{ xs: 12, sm: 6, md: 4 }}>
        <TopCourses />
      </Grid>
      <Grid size={{ xs: 12, sm: 6, md: 4 }}>
        <UpcomingWebinar />
      </Grid>
      <Grid size={{ xs: 12, sm: 6, md: 4 }}>
        <AssignmentProgress />
      </Grid>

      {/* Course listing */}
      <Grid size={{ xs: 12 }}>
        <CourseTable courseData={data?.courses} />
      </Grid>
    </Grid>
  )
}

export default AcademyDashboard
```

---

## Key Implementation Notes

1. **Async Server Component** - Uses getAcademyData() server action
2. **Server Data Integration** - CourseTable receives course data prop
3. **Welcome Personalization** - Greeting card tailored to user
4. **Wide Topics Section** - InterestedTopics takes 8 cols on desktop
5. **3-Column Card Grid** - Resource cards (TopCourses, Webinar, Progress)
6. **Full-Width Table** - Course listing spans entire width
7. **Education-Focused Layout** - Designed for learning platforms
8. **Mobile-First Responsive** - Stacks cleanly on small screens

---

## File Structure

```
src/
├── app/[lang]/(dashboard)/(private)/apps/
│   └── academy/
│       └── dashboard/
│           └── page.tsx (THIS FILE)
├── views/apps/academy/dashboard/
│   ├── WelcomeCard.tsx
│   ├── InterestedTopics.tsx
│   ├── PopularInstructors.tsx
│   ├── TopCourses.tsx
│   ├── UpcomingWebinar.tsx
│   ├── AssignmentProgress.tsx
│   └── CourseTable.tsx
├── app/server/
│   └── actions.ts (getAcademyData function)
└── fake-db/
    └── academy/...
```

---

## Data Flow

1. Page component is async
2. Calls `getAcademyData()` server action
3. Server fetches course data from fake-db
4. Data includes courses array with progress metadata
5. Courses data passed to CourseTable component
6. Other components are self-contained
7. No client-side data fetching

---

## Responsive Behavior

### Welcome Card
- XS-LG: 12 cols (always full width)

### Topics & Instructors Row
- XS: InterestedTopics 12 cols, PopularInstructors wraps below
- SM: InterestedTopics 12 cols (wraps), PopularInstructors 6 cols
- MD: InterestedTopics 8 cols, PopularInstructors 4 cols

### Resource Cards
- XS: All 12 cols (stacked)
- SM: 6 cols (2 per row, 3 rows for 3 items)
- MD: 4 cols (3 per row)
- LG: 4 cols (continues 3 per row)

### Course Table
- XS-LG: 12 cols (always full width)
- Horizontal scrolling on small screens

---

## Academy Components

### WelcomeCard
- Personalized greeting
- User name/avatar
- Quick action buttons
- Motivational messaging

### InterestedTopics
- Topic selection interface
- User interest visualization
- Category browsing
- Visual hierarchy

### PopularInstructors
- Instructor profiles/cards
- Rating display
- Specialty information
- Profile links

### TopCourses
- Course cards or list
- Rating and level info
- Enrollment status
- Quick enrollment buttons

### UpcomingWebinar
- Event details
- Date and time
- Speaker information
- Registration button

### AssignmentProgress
- Progress indicator
- Completion percentage
- Due dates
- Status badges

### CourseTable
- Course listing
- Progress bars
- Status indicators
- Detailed course info
- Server-fetched data

---

## Performance Optimizations

1. **Server-Side Data Fetching** - All data loaded on server
2. **No Client-Side API Calls** - Faster initial render
3. **Code Splitting** - Components loaded dynamically
4. **Memoization** - Components use React.memo
5. **Table Virtualization** - Large course lists handled efficiently

---

## Student-Centric Design

1. **Personalization** - Welcome with user name/preferences
2. **Clear Progress Tracking** - Assignment and course progress visible
3. **Course Discovery** - Popular courses and topics highlighted
4. **Instructor Access** - Popular/featured instructors visible
5. **Event Awareness** - Upcoming webinars and sessions
6. **Comprehensive View** - Complete course listing available

---

## Accessibility

- Semantic HTML structure
- Card-based layout hierarchy
- Course table with proper headers
- Progress indicators with ARIA
- Color contrast compliance
- Keyboard navigation support
- Screen reader compatibility
- Mobile-responsive design

---

## Use Cases

- Students reviewing course progress
- Instructors monitoring class metrics
- Administrators viewing learning analytics
- Parents checking student progress
- Platform managers analyzing engagement

