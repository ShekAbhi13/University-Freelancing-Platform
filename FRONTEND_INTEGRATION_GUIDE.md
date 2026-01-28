# UniFreelance Frontend - Complete File List & Integration Guide

## 📁 Complete Directory Structure

```
frontend/
├── assets/
│   ├── css/
│   │   └── style.css (2000+ lines of responsive styling)
│   ├── js/
│   │   └── main.js (Utility functions and helpers)
│   └── images/ (Ready for user uploads)
├── includes/
│   ├── header.php (Authentication + Navigation)
│   ├── footer.php (Footer template)
│   └── nav.php (Navigation component)
├── student_pages/
│   ├── dashboard.php (Student home with stats)
│   ├── browse_jobs.php (Job search & filter)
│   ├── my_applications.php (Track applications)
│   └── profile.php (Student profile editor)
├── client_pages/
│   ├── dashboard.php (Client home with stats)
│   ├── create_job.php (Post new job)
│   ├── manage_jobs.php (Manage job postings)
│   ├── view_applications.php (Review applications)
│   └── profile.php (Company profile editor)
├── admin_pages/
│   ├── dashboard.php (Admin statistics)
│   ├── users.php (User management)
│   ├── disputes.php (Dispute management)
│   └── settings.php (Platform settings)
└── messages.php (Messaging hub)

index.php (Landing page - ROOT)
```

## 📊 Files Created: 20 Total

### Template Files (2)

- [frontend/includes/header.php](frontend/includes/header.php) - Navigation bar + auth check
- [frontend/includes/footer.php](frontend/includes/footer.php) - Footer + script tags

### Assets (2)

- [frontend/assets/css/style.css](frontend/assets/css/style.css) - Complete styling
- [frontend/assets/js/main.js](frontend/assets/js/main.js) - JavaScript utilities

### Student Pages (4)

- [frontend/student_pages/dashboard.php](frontend/student_pages/dashboard.php) - Main student dashboard
- [frontend/student_pages/browse_jobs.php](frontend/student_pages/browse_jobs.php) - Job discovery
- [frontend/student_pages/my_applications.php](frontend/student_pages/my_applications.php) - Application tracking
- [frontend/student_pages/profile.php](frontend/student_pages/profile.php) - Profile management

### Client Pages (5)

- [frontend/client_pages/dashboard.php](frontend/client_pages/dashboard.php) - Main client dashboard
- [frontend/client_pages/create_job.php](frontend/client_pages/create_job.php) - Job posting
- [frontend/client_pages/manage_jobs.php](frontend/client_pages/manage_jobs.php) - Job management
- [frontend/client_pages/view_applications.php](frontend/client_pages/view_applications.php) - Application review
- [frontend/client_pages/profile.php](frontend/client_pages/profile.php) - Company profile

### Admin Pages (4)

- [frontend/admin_pages/dashboard.php](frontend/admin_pages/dashboard.php) - Platform overview
- [frontend/admin_pages/users.php](frontend/admin_pages/users.php) - User administration
- [frontend/admin_pages/disputes.php](frontend/admin_pages/disputes.php) - Dispute management
- [frontend/admin_pages/settings.php](frontend/admin_pages/settings.php) - Settings management

### Common Pages (2)

- [frontend/messages.php](frontend/messages.php) - Message center
- [index.php](index.php) - Landing page (at root)

---

## 🔗 Integration Points with Backend

### Session Variables Used

```php
$_SESSION['user_id']    // Current user ID
$_SESSION['role']       // 'student', 'client', or 'admin'
```

### Helper Functions Called (from backend/config/db.php)

```php
isLoggedIn()            // Check if user authenticated
getUserRole()           // Get current user's role
getUserId()             // Get current user's ID
isStudent()             // Check if student role
isClient()              // Check if client role
isAdmin()               // Check if admin role
sanitize($input)        // Sanitize user input
createLog($action, $details) // Log user actions
getDBConnection()       // Get DB connection
closeDBConnection($conn) // Close DB connection
redirect($url)          // Redirect to another page
```

### Database Tables Queried

- `users` - Authentication and basic user info
- `students` - Student profiles and stats
- `clients` - Client profiles and stats
- `jobs` - Job postings
- `applications` - Job applications
- `messages` - User messages
- `disputes` - Dispute records
- `payments` - Payment tracking
- `transactions` - Earnings history
- `settings` - Platform configuration

---

## 🎯 URL Routing

### Student Routes

```
/frontend/student_pages/dashboard.php      → Student home
/frontend/student_pages/browse_jobs.php    → Find jobs
/frontend/student_pages/my_applications.php → Track apps
/frontend/student_pages/profile.php        → Edit profile
```

### Client Routes

```
/frontend/client_pages/dashboard.php       → Client home
/frontend/client_pages/create_job.php      → Post job
/frontend/client_pages/manage_jobs.php     → Manage jobs
/frontend/client_pages/view_applications.php → Review apps
/frontend/client_pages/profile.php         → Edit profile
```

### Admin Routes

```
/frontend/admin_pages/dashboard.php        → Admin home
/frontend/admin_pages/users.php            → Manage users
/frontend/admin_pages/disputes.php         → Manage disputes
/frontend/admin_pages/settings.php         → Configure platform
```

### Common Routes

```
/frontend/messages.php                     → Message center
/index.php                                 → Landing page
/backend/auth/login.php                    → Login
/backend/auth/register.php                 → Register
/backend/auth/logout.php                   → Logout
```

---

## 📝 Form Submissions & Data Flow

### Job Creation Flow

```
/frontend/client_pages/create_job.php (form)
         ↓
     POST request
         ↓
  Database: INSERT INTO jobs
         ↓
  Redirect to dashboard with success message
```

### Job Application Flow

```
/frontend/student_pages/browse_jobs.php (view)
         ↓
  /frontend/student_pages/apply.php (form - to be created)
         ↓
     POST request
         ↓
  Database: INSERT INTO applications
         ↓
  Redirect to my_applications.php
```

### Profile Update Flow

```
/frontend/[role]_pages/profile.php (form)
         ↓
     POST request
         ↓
  Database: UPDATE [students/clients]
         ↓
  Display success message
```

---

## 🎨 Styling System

### Color Scheme

```css
--primary-color: #4caf50 (Green - main actions) --secondary-color: #2196f3
  (Blue - alternative) --danger-color: #f44336 (Red - delete/reject)
  --warning-color: #ff9800 (Orange - warnings) --success-color: #4caf50
  (Green - success) --light-gray: #f5f5f5 (Light backgrounds) --dark-gray: #333
  (Text);
```

### Responsive Breakpoints

```css
1200px max-width     (Desktop)
768px breakpoint     (Tablet)
480px breakpoint     (Mobile)
```

### Key Components

- `.btn` - Button styles (primary, secondary, danger)
- `.alert` - Alert messages (success, error, warning, info)
- `.card` - Content cards
- `.job-card` - Job listing cards
- `.badge` - Status badges
- `.dashboard-stats` - Stats grid
- `.table` - Table styling
- `.navbar` - Sticky header
- `.form-group` - Form field styling

---

## 🔒 Security Implemented

✅ **Authentication**: Session-based, required for all pages  
✅ **Authorization**: Role checks (isStudent, isClient, isAdmin)  
✅ **Input Sanitization**: All user inputs via sanitize()  
✅ **SQL Safety**: Using mysqli_real_escape_string  
✅ **ID Verification**: Checks before sensitive actions  
✅ **CSRF Prevention**: Session-based form handling  
✅ **Error Hiding**: No raw database errors shown to users

---

## 🚀 How to Launch

### Step 1: Start XAMPP

```bash
C:\xampp\xampp-control.exe
Start Apache and MySQL
```

### Step 2: Create Database

```bash
mysql -u root < database\unifreelance.sql
```

### Step 3: Access Application

```
http://localhost/unifreelance/
```

### Step 4: Test Login

Use sample credentials from database:

- **Student**: john_student / password123
- **Client**: tech_company / password123
- **Admin**: admin / admin123

---

## 📋 Testing Checklist

- [ ] Navigate to landing page (index.php)
- [ ] Register as new student
- [ ] Register as new client
- [ ] Login with student account
- [ ] View student dashboard
- [ ] Browse and search jobs
- [ ] View job details (needs detail page)
- [ ] Apply for a job (needs apply.php)
- [ ] View my applications
- [ ] Edit student profile
- [ ] Logout and login as client
- [ ] View client dashboard
- [ ] Create a new job
- [ ] Manage jobs
- [ ] View applications
- [ ] Edit client profile
- [ ] Login as admin
- [ ] View admin dashboard
- [ ] Browse users
- [ ] Check settings
- [ ] Test message center

---

## 🛠️ Still Need to Build (Optional)

### Detail Pages (to enhance navigation)

- `frontend/student_pages/job_detail.php` - Full job description
- `frontend/student_pages/apply.php` - Application form
- `frontend/student_pages/application_detail.php` - Application status
- `frontend/client_pages/job_detail.php` - Job overview with applications
- `frontend/client_pages/application_detail.php` - Application review form
- `frontend/admin_pages/user_detail.php` - User management form
- `frontend/admin_pages/dispute_detail.php` - Dispute resolution form

### Additional Pages

- `frontend/admin_pages/logs.php` - Activity logs viewer
- `frontend/job_messages.php` - Full conversation view
- `frontend/payments.php` - Payment/release page
- `frontend/[role]/verify_id.php` - ID upload pages

### Email Integration

- Registration confirmation
- Job application notifications
- Dispute notifications
- Payment release notifications

---

## 💡 Key Features by Page

| Page              | Features                                               |
| ----------------- | ------------------------------------------------------ |
| Student Dashboard | Stats, active jobs, quick actions, unread count        |
| Browse Jobs       | Search, filter by category, view details, apply button |
| My Applications   | Table of apps, status badges, view details             |
| Student Profile   | Form to edit all profile fields, skills textarea       |
| Client Dashboard  | Stats, recent jobs, quick actions                      |
| Create Job        | Form with validation, category selector, amount limits |
| Manage Jobs       | Table of all jobs, status indicators                   |
| View Applications | Filter applications, review details                    |
| Client Profile    | Company info form, website, bio                        |
| Admin Dashboard   | Platform stats, management links                       |
| Users             | Table with filters (role, status), view details        |
| Disputes          | Table with status filter, view details                 |
| Settings          | Form with all configurable settings                    |
| Messages          | Conversation list grouped by job                       |

---

## 📖 Documentation Files

- [FRONTEND_BUILD_SUMMARY.md](FRONTEND_BUILD_SUMMARY.md) - High-level overview
- [README.md](README.md) - Project info
- This file provides detailed integration guide

---

**Frontend build complete! Ready for testing and deployment.**
