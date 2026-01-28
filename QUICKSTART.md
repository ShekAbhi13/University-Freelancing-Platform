# UniFreelance - Quick Start Guide

## 🚀 Getting Started in 5 Minutes

### 1. Start XAMPP
```bash
1. Open C:\xampp\xampp-control.exe
2. Click "Start" next to Apache
3. Click "Start" next to MySQL
```

### 2. Access the Application
```
http://localhost/unifreelance/
```

You should see the landing page with "Welcome to UniFreelance"

---

## 👥 Test Accounts (Pre-loaded in Database)

### Student Account
```
Username: john_student
Password: password123
Email: john@example.com
```

### Client Account
```
Username: tech_company
Password: password123
Email: tech@example.com
```

### Admin Account
```
Username: admin
Password: admin123
Email: admin@unifreelance.local
```

---

## 📍 Main Pages

### For Students
- **Dashboard**: `/frontend/student_pages/dashboard.php`
  - View earnings, active jobs, ratings
  - Quick links to browse jobs and applications
  
- **Browse Jobs**: `/frontend/student_pages/browse_jobs.php`
  - Search and filter jobs by category
  - Apply for jobs that match your skills
  
- **My Applications**: `/frontend/student_pages/my_applications.php`
  - Track all your job applications
  - See application status (pending/accepted/rejected)
  
- **Profile**: `/frontend/student_pages/profile.php`
  - Add your skills, university, bio
  - Update contact information

### For Clients
- **Dashboard**: `/frontend/client_pages/dashboard.php`
  - View open jobs, in-progress jobs, completed jobs
  - See pending applications
  
- **Create Job**: `/frontend/client_pages/create_job.php`
  - Post a new job
  - Set budget ($10-$5000), deadline, required skills
  
- **Manage Jobs**: `/frontend/client_pages/manage_jobs.php`
  - View all your job postings
  - Edit or delete jobs
  
- **View Applications**: `/frontend/client_pages/view_applications.php`
  - Review student applications for your jobs
  - Accept or reject candidates
  
- **Profile**: `/frontend/client_pages/profile.php`
  - Add company info, website, contact details

### For Admins
- **Dashboard**: `/frontend/admin_pages/dashboard.php`
  - Platform statistics
  - Management tools
  
- **Manage Users**: `/frontend/admin_pages/users.php`
  - Filter users by role and status
  - Approve or suspend accounts
  
- **Disputes**: `/frontend/admin_pages/disputes.php`
  - View open disputes
  - Resolve conflicts between users
  
- **Settings**: `/frontend/admin_pages/settings.php`
  - Configure platform parameters
  - Set fees, verification requirements, etc.

---

## 🔄 Workflow Examples

### Student Finding Work
1. Login as student
2. Go to "Browse Jobs"
3. Search for jobs matching your skills
4. Click "View Details"
5. Click "Apply Now"
6. Fill in proposal and bid amount
7. Check "My Applications" to track status

### Client Posting a Job
1. Login as client
2. Click "Post Job"
3. Fill in job details:
   - Title
   - Description
   - Category
   - Required skills
   - Budget amount
   - Deadline
4. Click "Post Job"
5. Go to "Manage Jobs" to view your posting
6. Check "View Applications" to see student responses

### Admin Managing Platform
1. Login as admin
2. View Dashboard for stats
3. Go to "Users" to manage accounts
4. Go to "Disputes" to resolve conflicts
5. Go to "Settings" to configure platform

---

## 🎨 Page Layout

All pages have:
- **Header** - Navigation bar with role-based menu
- **Main Content** - Role-specific information
- **Footer** - Copyright and links
- **Sidebar** - Quick stats or action cards
- **Forms** - For creating/editing data

---

## 🔑 Key Features

✅ **Role-Based Access** - Different views for students, clients, and admins  
✅ **Job Management** - Post, edit, and manage jobs  
✅ **Applications** - Apply for jobs and review applications  
✅ **Messaging** - Communicate with other users  
✅ **Admin Controls** - User management and dispute resolution  
✅ **Responsive Design** - Works on desktop, tablet, and mobile  
✅ **Data Validation** - Secure forms with error checking  

---

## 📊 Sample Data

The database comes with sample data:

### Jobs (already created)
- Website Redesign ($500) - In Progress
- Mobile App Development ($1500) - Open
- Logo Design ($200) - Completed
- Social Media Graphics ($25/hour) - Open

### Users
- 2 student accounts (verified)
- 2 client accounts (verified)
- 1 admin account

---

## 🐛 Troubleshooting

### Page shows "Please verify your ID"
→ This is normal for new accounts. Only verified users can post jobs or apply.

### Login not working
→ Check that MySQL is running. Try these test credentials:
  - Username: john_student
  - Password: password123

### Database connection error
→ Make sure MySQL is running in XAMPP. Check settings in `/backend/config/db.php`

### Page layout looks broken
→ Clear browser cache (Ctrl+Shift+Delete) and refresh page

---

## 📝 Database

### Location
```
C:\xampp\htdocs\unifreelance\database\unifreelance.sql
```

### Tables (10)
- users (authentication)
- students (student profiles)
- clients (client profiles)
- jobs (job postings)
- applications (student applications)
- payments (payment tracking)
- disputes (conflict resolution)
- messages (user communications)
- transactions (earnings history)
- logs (activity tracking)

### Settings Table
Contains configurable values:
- Platform fee: 10%
- Escrow days: 7
- Min job amount: $10
- Max job amount: $5000

---

## 🔐 Security

All pages require login (except landing page)
Admin pages require admin role
Form inputs are sanitized
Database uses prepared-like queries
Session-based authentication
ID verification required for main actions

---

## 📧 Support Files

Two documentation files are included:

1. **FRONTEND_BUILD_SUMMARY.md** - What was built
2. **FRONTEND_INTEGRATION_GUIDE.md** - Technical integration details

---

## ✨ Next Steps

1. ✅ **Basic Testing** - Navigate through different pages
2. 📝 **Create Content** - Post some jobs or applications
3. 💬 **Test Features** - Send messages, create disputes
4. 🔧 **Customize** - Edit CSS for your branding
5. 📱 **Mobile Test** - Check responsive design
6. 🚀 **Deploy** - Move to production server

---

## 💬 Common Questions

**Q: How do I reset the database?**
A: Re-run the SQL file: `mysql -u root < database\unifreelance.sql`

**Q: Can I add more users?**
A: Yes! Use the register page or database INSERT.

**Q: How do I change the platform fee?**
A: Go to Admin > Settings and update "platform_fee"

**Q: Where are uploaded files stored?**
A: The `/uploads/` folder is ready for files.

---

**Happy freelancing! 🎉**
