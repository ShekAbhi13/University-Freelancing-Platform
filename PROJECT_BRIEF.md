# UniFreelance - Project Brief

## 📋 Executive Summary

**UniFreelance** is a secure, university-focused freelancing platform that connects verified students with legitimate work opportunities provided by clients. The platform manages job postings, applications, secure payments through escrow, ID verification, and dispute resolution to create a safe, trustworthy ecosystem.

---

## 🎯 Project Objectives

1. **Connect University Students with Work** - Provide students with flexible, remote freelance opportunities
2. **Enable Clients to Hire Verified Talent** - Allow businesses to post jobs and hire qualified students
3. **Ensure Trust & Security** - Implement ID verification, escrow payments, and dispute resolution
4. **Manage Platform Integrity** - Provide admin tools for oversight, verification, and conflict resolution
5. **Facilitate Secure Payments** - Protect both parties through escrow-based payment system

---

## 👥 Target Users

### 1. **Students**

- University students seeking flexible work
- Ages 18+
- Need to earn money while studying
- May not have professional work experience
- Limited availability due to academic commitments

### 2. **Clients**

- Small to medium businesses (SMBs)
- Entrepreneurs and freelancers
- Need specific project work done quickly
- Looking for cost-effective talent
- May post regular/recurring jobs

### 3. **Administrators**

- Platform managers
- Support staff
- Conflict resolvers
- Platform oversight

---

## ✨ Core Features

### Authentication & Account Management

- User registration (Student/Client roles)
- Secure login with password hashing
- Session management
- Account suspension for violations
- Password recovery

### User Verification

- ID document upload (JPG, PNG, PDF)
- Admin verification workflow
- Status tracking (Pending/Verified/Rejected)
- Prevents unverified users from posting/applying
- Document storage in uploads folder

### Job Management

- **Create Jobs** - Post with title, description, category, budget, deadline
- **Browse Jobs** - Filtered search for students
- **Edit Jobs** - Modify open jobs
- **Delete Jobs** - Remove jobs before applications
- **View Applications** - Track who applied
- **Job Lifecycle** - Open → In Progress → Completed

### Application System

- Submit proposals/cover letters
- Track application status (Pending/Accepted/Rejected)
- Student portfolio visibility
- Multiple applications per job
- Client-student communication during applications

### Messaging System

- Direct messaging between users
- Conversation history
- Unread message notifications
- Accessible from any role
- For job clarifications and updates

### Payment Management

- **Escrow System** - Money held until work approved
- **Payment Release** - Client releases payment after work approval
- **Refunds** - Money returned to client if job cancelled/disputed
- **Transaction History** - Admin visibility into all payments
- **Secure Transactions** - No direct money transfer between users

### Dispute Resolution

- Students can file disputes for unpaid work
- Clients can file disputes for unsatisfactory work
- Admin investigation and mediation
- Evidence submission (screenshots, files)
- Decision implementation (Release/Refund/Split)
- Mandatory for payment release

### Admin Dashboard

- **User Management** - View, verify, suspend users
- **Dispute Management** - Review and resolve conflicts
- **Payment Tracking** - Monitor all transactions
- **Activity Logs** - System audit trail
- **Platform Settings** - Configure platform rules
- **Statistics** - Dashboard with key metrics

### User Profiles

- **Students**
  - Full name, university, major, year of study
  - Skills, bio, phone, ID verification
  - Application history
  - Completed jobs & earnings

- **Clients**
  - Company name, contact person, industry
  - Website, bio, phone, address, ID verification
  - Posted jobs, hiring history
  - Spending & transaction history

---

## 🔐 Security Features

### Authentication

- Password hashing (bcrypt)
- Session-based authentication
- Logout functionality
- Role-based access control (RBAC)

### Data Protection

- SQL injection prevention (sanitization)
- Input validation
- Secure file uploads (type checking, size limits)
- HTTPS ready (localhost currently)

### Account Security

- Account suspension for violations
- ID verification requirement
- Unverified users limited access
- Admin oversight of suspicious activity

### Payment Security

- Escrow system (no direct transfers)
- Payment holds until work approved
- Dispute mechanism before payment release
- Admin oversight of all transactions

---

## 🏗️ Technical Architecture

### Technology Stack

- **Backend:** PHP 7+
- **Database:** MySQL 5.7+
- **Frontend:** HTML5, CSS3, JavaScript ES6+
- **Server:** Apache (XAMPP)
- **Development:** Windows (localhost)

### Database Structure

- **users** - Authentication & basic info
- **students** - Student-specific profiles
- **clients** - Client-specific profiles
- **jobs** - Job postings
- **applications** - Student applications
- **payments** - Transaction records
- **disputes** - Conflict records
- **messages** - Communication
- **logs** - Activity tracking
- **admins** - Admin accounts

### File Organization

```
/unifreelance/
├── backend/              # Server-side logic
│   ├── auth/            # Login, register, logout
│   ├── admin/           # Admin panel (9 pages)
│   ├── client/          # Client dashboard & profile
│   ├── student/         # Student dashboard & profile
│   ├── jobs/            # Job management
│   ├── applications/    # Application handling
│   ├── messages/        # Messaging system
│   ├── payments/        # Payment processing
│   ├── disputes/        # Dispute handling
│   ├── logs/            # Activity logging
│   ├── config/          # Database connection
│   └── uploads/         # File storage
├── frontend/
│   ├── assets/css/      # Stylesheets (6 files)
│   ├── includes/        # Reusable components
│   └── index.php        # Landing page
├── database/            # SQL schema
└── documentation/       # Project docs
```

---

## 🎨 Design System

### Color Palette

- **Primary:** #273C50 (Dark Navy Blue)
- **Secondary:** #2590DC (Bright Blue)
- **Danger:** #f44336 (Red)
- **Success:** #4CAF50 (Green)
- **Warning:** #ff9800 (Orange)

### Typography

- **Font Family:** Segoe UI, Tahoma, Geneva, Verdana, sans-serif
- **H1:** 2.5rem | **H2:** 2rem | **H3:** 1.5rem | **Body:** 1rem

### Responsive Breakpoints

- Desktop: 1200px+
- Tablet: 768px - 1199px
- Mobile: 480px - 767px
- Small: <480px

---

## 📊 Key Pages & Functionality

| Page              | Role      | Purpose                  |
| ----------------- | --------- | ------------------------ |
| Index             | All       | Landing page, navigation |
| Login             | All       | Authentication           |
| Register          | New Users | Account creation         |
| Dashboard         | All       | Main hub & statistics    |
| Profile           | S/C       | Update personal info     |
| Upload ID         | S/C       | Identity verification    |
| Browse Jobs       | Student   | Job discovery            |
| Create Job        | Client    | Post new job             |
| View Applications | S/C       | Manage applications      |
| Inbox             | All       | Messaging hub            |
| Admin Dashboard   | Admin     | Platform overview        |
| Users Management  | Admin     | User administration      |
| Disputes          | Admin     | Conflict resolution      |
| Payments          | Admin     | Transaction tracking     |

---

## 🔄 User Workflows

### Student Workflow

```
Register → Verify ID → Complete Profile → Browse Jobs
→ Apply for Job → Wait for Acceptance → Work & Communicate
→ Submit Work → Get Paid
```

### Client Workflow

```
Register → Verify ID → Complete Profile → Post Job
→ Review Applications → Accept Student → Manage Job
→ Review Work → Release Payment
```

### Admin Workflow

```
Verify IDs → Monitor Platform → Resolve Disputes
→ Manage Users → Track Payments → View Logs
```

---

## 💰 Business Model

### Revenue Model

- **Commission-based:** Admin takes percentage from each completed job
- **Premium Features:** Future add-ons (verified badges, featured jobs, etc.)
- **Subscription:** Possible future client subscription tiers

### Transaction Flow

1. Client posts job with budget
2. Student hired → Money goes to escrow
3. Work completed
4. Client releases payment
5. Money transferred to student (minus platform commission)

---

## 🚀 Current Status

### Completed (✅)

- ✅ Core database structure
- ✅ Authentication system (login/register/logout)
- ✅ Admin panel (9 pages, fully styled)
- ✅ Client dashboard & profile pages
- ✅ Student dashboard & profile pages
- ✅ ID verification upload system
- ✅ Professional UI/UX design
- ✅ Navigation & header system
- ✅ Color palette implementation

### In Development (🔄)

- 🔄 Jobs management pages
- 🔄 Application system UI
- 🔄 Messaging interface
- 🔄 Payment processing UI
- 🔄 Dispute resolution pages

### Planned (📋)

- 📋 Payment gateway integration (Stripe/PayPal)
- 📋 Email notifications
- 📋 Advanced search & filtering
- 📋 User ratings & reviews
- 📋 Portfolio showcasing
- 📋 Analytics dashboard
- 📋 Mobile app

---

## 📈 Growth Roadmap

### Phase 1: MVP (Current)

- Core functionality operational
- Basic UI implemented
- ID verification working
- Escrow payments functional

### Phase 2: Enhancement

- Advanced job filtering
- User ratings system
- Email notifications
- Activity dashboard

### Phase 3: Scaling

- Payment gateway integration
- Mobile app launch
- API development
- Third-party integrations

### Phase 4: Expansion

- International support
- Advanced analytics
- Machine learning matching
- Enterprise features

---

## 🎓 University Integration (Future)

### Planned Features

- University email verification
- University directory integration
- Campus-specific job boards
- Student status verification
- Academic calendar awareness

---

## 📞 Support & Contact

### In-Platform Support

- Help documentation
- FAQ section
- Contact admin form
- Ticketing system (future)

### User Roles Support

- Student support: job finding, payment issues
- Client support: hiring, dispute processes
- Admin support: platform management

---

## ✅ Success Metrics

### Platform Metrics

- Total registered users
- Job posting rate
- Application acceptance rate
- Payment completion rate
- Dispute resolution time
- User retention rate
- Commission revenue

### User Satisfaction

- User ratings (1-5 stars)
- Job completion rate
- Payment dispute ratio
- Review sentiment

---

## 🔒 Compliance & Safety

### Data Protection

- Password encryption
- Secure session management
- GDPR compliance (future)
- Data backup procedures

### Fraud Prevention

- ID verification requirement
- Account suspension protocols
- Dispute investigation
- Activity logging

### Content Moderation

- Job description review
- Inappropriate content flagging
- User behavior monitoring
- Admin reporting tools

---

## 📝 Documentation

This project includes:

- **USER_FLOW.md** - Complete user journey documentation
- **PROJECT_BRIEF.md** - This document
- **DESIGN_TEMPLATE.md** - UI/UX design guidelines
- Database schema with comments
- Code comments throughout codebase

---

## 🎯 Project Goals (Next Steps)

1. ✅ Complete core platform functionality
2. ⏳ Build remaining page designs (Jobs, Applications, Payments, Disputes, Messaging)
3. ⏳ Implement payment processing
4. ⏳ Add email notification system
5. ⏳ Launch beta testing with universities
6. ⏳ Gather user feedback and iterate
7. ⏳ Scale to multiple universities

---

## 📚 Additional Resources

- Database: `/database/unifreelance.sql`
- Configuration: `/backend/config/db.php`
- Landing Page: `/index.php`
- User Documentation: `/USER_FLOW.md`

---

**Project Start Date:** January 2026
**Current Version:** 1.0 (MVP)
**Status:** In Active Development
**Last Updated:** January 26, 2026
