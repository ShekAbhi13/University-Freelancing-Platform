# UniFreelance Frontend - Navigation Map

## 🗺️ User Journey by Role

### 👨‍🎓 STUDENT JOURNEY
```
Landing Page (index.php)
     ↓
Register (backend/auth/register.php)
     ↓
Login (backend/auth/login.php)
     ↓
Student Dashboard
├─→ Browse Jobs
│   ├─→ Search/Filter
│   └─→ Apply [TODO: apply.php]
├─→ My Applications
│   └─→ View Status [TODO: application_detail.php]
├─→ Profile
│   └─→ Edit Skills & Info
└─→ Messages
    └─→ Job Conversations

EARNINGS PATH:
Apply → Get Selected → Complete Job → Release Payment → Earnings Increase
```

### 💼 CLIENT JOURNEY
```
Landing Page (index.php)
     ↓
Register (backend/auth/register.php)
     ↓
Login (backend/auth/login.php)
     ↓
Client Dashboard
├─→ Create Job
│   └─→ Post Details
├─→ Manage Jobs
│   ├─→ View All Jobs
│   └─→ Edit/Delete [TODO: job_detail.php]
├─→ View Applications
│   ├─→ See All Applications
│   └─→ Accept/Reject [TODO: application_detail.php]
├─→ Profile
│   └─→ Edit Company Info
└─→ Messages
    └─→ Job Communications

JOB LIFECYCLE:
Post Job → Receive Applications → Select Winner → Monitor Progress → Release Payment
```

### 👨‍💼 ADMIN JOURNEY
```
Login (backend/auth/login.php)
     ↓
Admin Dashboard
├─→ Manage Users
│   ├─→ Filter by Role/Status
│   └─→ View Details [TODO: user_detail.php]
├─→ Manage Disputes
│   ├─→ Filter by Status
│   └─→ Resolve [TODO: dispute_detail.php]
├─→ Settings
│   ├─→ Platform Fees
│   ├─→ Job Limits
│   └─→ Verification Requirements
└─→ Activity Logs [TODO: logs.php]

ADMIN TASKS:
Verify Users → Monitor Platform → Resolve Disputes → Configure Settings
```

---

## 📍 Page Map by URL

### STUDENT PAGES
```
/frontend/student_pages/dashboard.php
  - Overview of student profile
  - Active jobs listing
  - Quick statistics (earnings, rating, applications)
  - Links to other sections

/frontend/student_pages/browse_jobs.php
  - All open jobs from clients
  - Search by keyword
  - Filter by category
  - View job details
  - Apply for jobs (one-click)

/frontend/student_pages/my_applications.php
  - All submitted applications
  - Shows job title, bid amount, status
  - Date applied
  - Status: pending/accepted/rejected

/frontend/student_pages/profile.php
  - Edit full name
  - Edit university & major
  - Edit year of study
  - Edit skills list
  - Write bio
  - Add phone number
  - View ID verification status
  - See total earnings
```

### CLIENT PAGES
```
/frontend/client_pages/dashboard.php
  - Overview of client profile
  - Quick statistics (open, in-progress, completed jobs)
  - Pending applications count
  - Recent jobs listing
  - Quick action links

/frontend/client_pages/create_job.php
  - Form to create new job
  - Title input
  - Description textarea
  - Category dropdown
  - Required skills input
  - Budget type (fixed/hourly)
  - Amount input ($10-$5000)
  - Deadline picker
  - ID verification check
  - Success/error messages

/frontend/client_pages/manage_jobs.php
  - Table of all posted jobs
  - Job title, category, budget
  - Status badge (open/in-progress/completed)
  - Application count
  - Deadline date
  - Link to view details

/frontend/client_pages/view_applications.php
  - Table of all applications
  - Student name
  - Job title
  - Bid amount
  - Days to complete
  - Application status
  - Date applied
  - Link to review details

/frontend/client_pages/profile.php
  - Edit company name
  - Edit contact person
  - Edit industry
  - Edit phone number
  - Edit website URL
  - Edit address
  - Write company bio
  - View ID verification status
  - See total spent on platform
```

### ADMIN PAGES
```
/frontend/admin_pages/dashboard.php
  - Statistics cards:
    * Total users
    * Total students
    * Total clients
    * Total jobs
    * Completed jobs
    * Open disputes
  - Quick links to all admin tools
  - Platform overview

/frontend/admin_pages/users.php
  - Table of all users
  - Filter by role (student/client)
  - Filter by status (active/pending/suspended)
  - Columns: username, name, email, role, status, joined date
  - Link to view user details
  - Sort by creation date

/frontend/admin_pages/disputes.php
  - Table of all disputes
  - Filter by status (open/resolved/closed)
  - Columns: job, client, student, amount, reason, status, created date
  - Link to resolve disputes
  - Sort by creation date

/frontend/admin_pages/settings.php
  - Form with all configurable settings:
    * platform_fee (default: 10)
    * escrow_days (default: 7)
    * require_id_verification (default: 1)
    * max_job_amount (default: 5000)
    * min_job_amount (default: 10)
    * admin_email
    * system_email
    * max_login_attempts
    * session_timeout
  - Update button
  - Success message on save
```

### COMMON PAGES
```
/frontend/messages.php
  - View all messages from all jobs
  - Grouped by job
  - Shows conversation partners
  - Date and time of messages
  - Dispute indicator if marked
  - Link to view full conversation

/index.php (Landing Page)
  - Welcome message
  - Call-to-action for students
  - Call-to-action for clients
  - Feature highlights
  - Login/Register buttons
  - No authentication required
```

---

## 🔑 Key Parameters

### URL Parameters
```
GET Parameters:
  ?role=student|client
  ?status=active|pending|suspended
  ?category=web_development|mobile_development|graphic_design|...
  ?search=keyword
  ?job_id=123
  ?id=123
  ?status=open|in_progress|completed|pending|accepted|rejected
```

### POST Parameters
```
Job Creation:
  title, description, category, skills, budget_type, amount, deadline

Job Application:
  proposal, bid_amount, estimated_days

Profile Update:
  full_name, university, major, year, skills, bio, phone (student)
  company_name, contact_person, industry, website, bio, phone, address (client)

Settings Update:
  platform_fee, escrow_days, require_id_verification, etc.
```

---

## 🔗 Internal Links

### Navigation Bar (All Pages)
```
Home:
  /frontend/[role]_pages/dashboard.php

Role-Specific:
  Student: Browse Jobs | My Applications | Profile
  Client:  Post Job | Manage Jobs | Profile
  Admin:   Users | Disputes | Settings

Common:
  Messages
  Logout → /backend/auth/logout.php
```

### Dashboard Quick Links
```
Student:
  Browse Jobs → /frontend/student_pages/browse_jobs.php
  My Applications → /frontend/student_pages/my_applications.php
  Profile → /frontend/student_pages/profile.php
  Messages → /frontend/messages.php

Client:
  Post Job → /frontend/client_pages/create_job.php
  Manage Jobs → /frontend/client_pages/manage_jobs.php
  View Applications → /frontend/client_pages/view_applications.php
  Profile → /frontend/client_pages/profile.php

Admin:
  Users → /frontend/admin_pages/users.php
  Disputes → /frontend/admin_pages/disputes.php
  Settings → /frontend/admin_pages/settings.php
```

---

## 📊 Data Flow Diagrams

### Job Application Flow
```
browse_jobs.php (List open jobs)
          ↓
    [Click Apply]
          ↓
apply.php (Form - TODO) ← Need to create
          ↓
  [Submit application]
          ↓
  INSERT INTO applications
          ↓
my_applications.php (Show confirmation)
```

### Job Creation Flow
```
client/dashboard.php (View stats)
          ↓
    [Post Job]
          ↓
create_job.php (Form + validation)
          ↓
  [Submit job details]
          ↓
  INSERT INTO jobs
          ↓
manage_jobs.php (Show new job)
```

### Dispute Resolution Flow
```
admin/dashboard.php (View statistics)
          ↓
admin/disputes.php (List open disputes)
          ↓
  [Click view]
          ↓
dispute_detail.php (TODO - Detail & resolution form)
          ↓
  [Select resolution]
          ↓
  UPDATE disputes
          ↓
admin/disputes.php (Show resolved)
```

---

## 🎯 Quick Links Reference

### For Students
| Action | URL | Method |
|--------|-----|--------|
| View Dashboard | `/frontend/student_pages/dashboard.php` | GET |
| Browse Jobs | `/frontend/student_pages/browse_jobs.php` | GET |
| Search Jobs | `/frontend/student_pages/browse_jobs.php?search=word&category=web_development` | GET |
| My Applications | `/frontend/student_pages/my_applications.php` | GET |
| Edit Profile | `/frontend/student_pages/profile.php` | GET/POST |
| View Messages | `/frontend/messages.php` | GET |

### For Clients
| Action | URL | Method |
|--------|-----|--------|
| View Dashboard | `/frontend/client_pages/dashboard.php` | GET |
| Post Job | `/frontend/client_pages/create_job.php` | GET/POST |
| Manage Jobs | `/frontend/client_pages/manage_jobs.php` | GET |
| View Applications | `/frontend/client_pages/view_applications.php` | GET |
| Edit Profile | `/frontend/client_pages/profile.php` | GET/POST |
| View Messages | `/frontend/messages.php` | GET |

### For Admins
| Action | URL | Method |
|--------|-----|--------|
| Dashboard | `/frontend/admin_pages/dashboard.php` | GET |
| Users | `/frontend/admin_pages/users.php` | GET |
| Filter Users | `/frontend/admin_pages/users.php?role=student&status=active` | GET |
| Disputes | `/frontend/admin_pages/disputes.php` | GET |
| Settings | `/frontend/admin_pages/settings.php` | GET/POST |

---

## 📱 Responsive Layout

All pages include:
```
┌─────────────────────────────────────┐
│  HEADER (Navigation Bar)            │
│  - Logo                             │
│  - Role-based menu items            │
│  - Logout button                    │
└─────────────────────────────────────┘

┌─────────────────────────────────────┐
│         MAIN CONTENT                │
│  (Desktop: 1200px, Tablet: 768px)   │
│  (Mobile: Full width)               │
└─────────────────────────────────────┘

┌─────────────────────────────────────┐
│  FOOTER (Copyright)                 │
└─────────────────────────────────────┘
```

Mobile Stacking:
```
Desktop (1200px):
[Sidebar] [Main Content]

Tablet (768px):
[Main Content - Full Width]

Mobile (480px):
[Main Content - Full Width]
[Stacked Elements]
```

---

**Navigation is fully mapped and ready to use!**
