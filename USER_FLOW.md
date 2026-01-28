# UniFreelance User Flow Documentation

## Overview

UniFreelance is a three-tier platform connecting students with work opportunities through verified clients, with admin oversight for dispute resolution and platform management.

---

## User Types & Roles

1. **Student** - Seeks freelance work opportunities
2. **Client** - Posts jobs and hires students
3. **Admin** - Manages platform, users, disputes, and payments

---

## 🔐 Authentication Flow

### New User Registration

```
Homepage
  ↓
Click "Register" or "Get Started"
  ↓
Registration Page (/backend/auth/register.php)
  ├─ Select Role (Student or Client)
  ├─ Enter Username, Email, Password
  ├─ Confirm Password
  ↓
Create Account
  ├─ Validate inputs
  ├─ Hash password
  ├─ Create user record
  ├─ Create role-specific profile (students/clients table)
  ├─ Status: "pending" (requires ID verification)
  ↓
Success Message
  ↓
Redirect to Login Page
```

### User Login

```
Homepage or Direct Access to Login
  ↓
Login Page (/backend/auth/login.php)
  ├─ Enter Username/Email
  ├─ Enter Password
  ↓
Verify Credentials
  ├─ Check user exists
  ├─ Verify password hash
  ├─ Check account status (not suspended)
  ↓
Session Created
  ↓
Redirect Based on Role:
  ├─ Admin → /backend/admin/dashboard.php
  ├─ Client → /backend/client/dashboard.php
  ├─ Student → /backend/student/dashboard.php
```

---

## 👨‍🎓 Student User Flow

### 1. Complete Profile & Verification

```
Student Dashboard (/backend/student/dashboard.php)
  ↓
Click "Update Profile"
  ↓
Update Details Page (/backend/student/profile/update_details.php)
  ├─ Full Name (required)
  ├─ University
  ├─ Major
  ├─ Year of Study
  ├─ Skills (comma-separated)
  ├─ Bio
  ├─ Phone
  ↓
Save Profile
  ↓
Return to Dashboard
  ↓
Upload ID Verification
  ├─ Click "Upload ID"
  ├─ Upload Document (/backend/student/profile/upload_id.php)
  ├─ Accepted formats: JPG, PNG, PDF (max 5MB)
  ├─ Status: "pending" (awaiting admin verification)
```

### 2. Browse & Apply for Jobs

```
Student Dashboard
  ↓
Click "Browse Jobs"
  ↓
View Jobs Page (/backend/jobs/view_jobs.php)
  ├─ Display available jobs
  ├─ Filter by:
  │  ├─ Category
  │  ├─ Budget range
  │  ├─ Posted date
  ├─ Show job details:
  │  ├─ Job title & description
  │  ├─ Budget
  │  ├─ Deadline
  │  ├─ Client info
  ↓
Select Job
  ↓
Click "Apply"
  ↓
Apply Page (/backend/applications/apply.php)
  ├─ Submit proposal/cover letter
  ├─ Set expected completion date
  ├─ Optional: Ask clarifying questions
  ↓
Submit Application
  ├─ Create application record
  ├─ Status: "pending" (awaiting client review)
  ├─ Send notification to client
```

### 3. Manage Applications

```
Student Dashboard
  ↓
Click "My Applications"
  ↓
View Applications Page (/backend/applications/view_applications.php)
  ├─ List all applications
  ├─ Show status for each:
  │  ├─ Pending (waiting for client)
  │  ├─ Accepted (client hired you)
  │  ├─ Rejected (client declined)
  ├─ Show application details:
  │  ├─ Job title
  │  ├─ Company/Client name
  │  ├─ Applied date
  │  ├─ Status
  ↓
If Status = "Accepted"
  ↓
Work Begins
  ├─ Job moves to "in_progress"
  ├─ Payment held in escrow
  ├─ Access to client messages
```

### 4. Complete Work & Get Paid

```
During Job Execution
  ↓
Communicate with Client
  ├─ Access Messages (/backend/messages/inbox.php)
  ├─ Send/Receive messages
  ├─ Ask clarifications
  ├─ Share updates
  ↓
Complete Work
  ├─ Upload deliverables (if applicable)
  ├─ Notify client: "Work Complete"
  ↓
Client Reviews Work
  ├─ Client approves or requests revisions
  ├─ If approved → Payment released
  ├─ If revisions needed → Go back to work
  ↓
Payment Released
  ├─ Escrow released to student
  ├─ Funds available for withdrawal
  ├─ Job status: "completed"
```

### 5. Handle Disputes

```
If Conflict During Job
  ↓
Click "Create Dispute" (/backend/disputes/create_dispute.php)
  ├─ Select job
  ├─ Explain issue
  ├─ Submit evidence
  ↓
Admin Reviews Dispute
  ├─ Admin investigates
  ├─ Reviews both sides
  ├─ Makes decision
  ↓
Dispute Resolution (/backend/disputes/resolve_dispute.php)
  ├─ Money released (partial or full)
  ├─ Job status updated
  ├─ Notification sent to both parties
```

---

## 💼 Client User Flow

### 1. Complete Profile & Verification

```
Client Dashboard (/backend/client/dashboard.php)
  ↓
Click "Update Profile"
  ↓
Update Details Page (/backend/client/profile/update_details.php)
  ├─ Company Name
  ├─ Contact Person (required)
  ├─ Industry
  ├─ Website
  ├─ Bio/Description
  ├─ Phone (required)
  ├─ Address
  ↓
Save Profile
  ↓
Upload ID Verification
  ├─ Click "Upload ID"
  ├─ Upload Document (/backend/client/profile/upload_id.php)
  ├─ Accepted formats: JPG, PNG, PDF (max 5MB)
  ├─ Status: "pending" (awaiting admin verification)
```

### 2. Post a Job

```
Client Dashboard
  ↓
Click "Post Job"
  ↓
Create Job Page (/backend/jobs/create_job.php)
  ├─ Job Title (required)
  ├─ Description (required)
  ├─ Category (required)
  ├─ Budget (required)
  ├─ Deadline (required)
  ├─ Skills Required
  ├─ Attachment (optional)
  ↓
Submit Job
  ├─ Create job record
  ├─ Status: "open"
  ├─ Notify students
  ├─ Job appears in browse feed
```

### 3. Manage Jobs & Review Applications

```
Client Dashboard
  ↓
Click "Manage Jobs"
  ↓
View Jobs Page (/backend/jobs/view_jobs.php)
  ├─ List all client's jobs
  ├─ Show status for each:
  │  ├─ Open (accepting applications)
  │  ├─ In Progress
  │  ├─ Completed
  │  ├─ Closed
  ├─ Action buttons:
  │  ├─ View Applications
  │  ├─ Edit Job
  │  ├─ Delete Job
  ↓
Click "View Applications"
  ↓
View Applications Page (/backend/applications/view_applications.php)
  ├─ List all applications for job
  ├─ Show student details:
  │  ├─ Name & University
  │  ├─ Skills
  │  ├─ Student Bio
  │  ├─ Application date
  ├─ Show proposal/cover letter
  ↓
Review Application
  ├─ Option 1: Accept Application
  │  ├─ Hire the student
  │  ├─ Job status → "in_progress"
  │  ├─ Create escrow for payment
  │  ├─ Reject other applications
  │
  ├─ Option 2: Reject Application
  │  ├─ Send rejection notification
  │  ├─ Continue reviewing others
  │
  ├─ Option 3: Request More Info
  │  ├─ Send message to student
```

### 4. Pay for Completed Work

```
Job In Progress
  ↓
Student Completes Work
  ↓
Client Receives Notification
  ↓
Review Deliverables
  ├─ Check submitted work
  ├─ Verify quality
  ├─ Ask for revisions if needed
  ↓
If Work Approved
  ↓
Click "Release Payment" (/backend/payments/release.php)
  ├─ Confirm payment amount
  ├─ Confirm student details
  ├─ Authorize release from escrow
  ↓
Payment Released
  ├─ Money transferred from escrow to student
  ├─ Job status: "completed"
  ├─ Notification sent to student
```

### 5. Handle Disputes

```
If Issue with Completed Work
  ↓
Click "Create Dispute" (/backend/disputes/create_dispute.php)
  ├─ Select job
  ├─ Explain issue
  ├─ Submit evidence
  ↓
Admin Reviews Dispute
  ├─ Investigates claim
  ├─ Reviews work quality
  ├─ Contacts student if needed
  ↓
Dispute Resolution (/backend/disputes/resolve_dispute.php)
  ├─ Decision: Refund or Keep
  ├─ Money adjusted accordingly
  ├─ Notification sent to both parties
```

### 6. Edit/Delete Jobs

```
Client Dashboard → Manage Jobs
  ↓
Select Job
  ↓
If Status = "Open"
  ├─ Option: Edit Job (/backend/jobs/edit_job.php)
  │  ├─ Update any field
  │  ├─ Save changes
  │  ├─ Renotify students
  │
  ├─ Option: Delete Job (/backend/jobs/delete_job.php)
  │  ├─ Confirm deletion
  │  ├─ Job removed from listings
  │  ├─ Notify students who applied
  │
  ↓
If Status = "In Progress"
  ├─ Cannot edit or delete
```

---

## 🔧 Admin User Flow

### 1. Dashboard Overview

```
Admin Dashboard (/backend/admin/dashboard.php)
  ↓
View Statistics:
  ├─ Total Users (Students, Clients)
  ├─ Active Jobs
  ├─ Completed Jobs
  ├─ Total Revenue
  ├─ Pending Verifications
  ├─ Active Disputes
  ↓
Quick Actions:
  ├─ View Users
  ├─ View Disputes
  ├─ Create Admin
  ├─ View Logs
  ├─ Access Settings
```

### 2. Manage Users

```
Admin Dashboard
  ↓
Click "Users"
  ↓
Users Page (/backend/admin/users.php)
  ├─ List all users (Students & Clients)
  ├─ Filter by:
  │  ├─ Role (Student/Client)
  │  ├─ Status (Active/Suspended/Pending)
  ├─ Show user details:
  │  ├─ Username
  │  ├─ Email
  │  ├─ Role
  │  ├─ Registration date
  │  ├─ Status
  │  ├─ ID Verification status
  ↓
Click User → View Details
  ├─ Full profile information
  ├─ Verification documents
  ├─ Job/Application history
  ├─ Actions:
  │  ├─ Verify ID
  │  ├─ Suspend/Unsuspend
  │  ├─ Delete Account
```

### 3. Verify ID Documents

```
Admin Dashboard → Users
  ↓
Find User with Pending Verification
  ├─ Status: "pending"
  ↓
Click "Review ID"
  ├─ View uploaded document
  ├─ View user details
  ├─ Decision:
  │  ├─ Approve → Status: "verified"
  │  │  ├─ User can now apply/post jobs
  │  ├─ Reject → Status: "rejected"
  │  │  ├─ User must reupload
```

### 4. Manage Disputes

```
Admin Dashboard
  ↓
Click "Disputes"
  ↓
Disputes Page (/backend/admin/disputes.php)
  ├─ List all disputes
  ├─ Filter by status:
  │  ├─ Open (waiting for review)
  │  ├─ In Review (being investigated)
  │  ├─ Resolved
  ├─ Show dispute details:
  │  ├─ Job involved
  │  ├─ Student & Client names
  │  ├─ Description
  │  ├─ Evidence submitted
  ├─ Date created
  ↓
Click Dispute
  ↓
Review Dispute Details
  ├─ Read both sides
  ├─ Review evidence
  ├─ Contact history
  ↓
Make Decision
  ↓
Resolve Dispute (/backend/disputes/resolve_dispute.php)
  ├─ Decision: Release to Student, Refund to Client, or Split
  ├─ Add comment/explanation
  ├─ Submit
  ↓
Money Adjusted
  ├─ Escrow released per decision
  ├─ Notifications sent to both parties
  ├─ Dispute status: "resolved"
```

### 5. Manage Payments

```
Admin Dashboard
  ↓
Click "Payments"
  ↓
Payments Page (/backend/admin/payments.php)
  ├─ List all transactions
  ├─ Filter by:
  │  ├─ Date range
  │  ├─ Status (Pending, Released, Refunded)
  │  ├─ Amount
  ├─ Show payment details:
  │  ├─ Job involved
  │  ├─ Student & Client
  │  ├─ Amount
  │  ├─ Status
  │  ├─ Transaction date
  ↓
View Details
  ├─ Escrow status
  ├─ Job status
  ├─ Timeline
  ├─ Dispute status (if any)
```

### 6. View Activity Logs

```
Admin Dashboard
  ↓
Click "Logs"
  ↓
Activity Logs Page (/backend/admin/logs.php)
  ├─ List all system activities
  ├─ Filter by:
  │  ├─ Action type (login, upload, payment, etc.)
  │  ├─ User
  │  ├─ Date range
  ├─ Show log details:
  │  ├─ Timestamp
  │  ├─ User who performed action
  │  ├─ Action description
  │  ├─ Status (success/failure)
```

### 7. Platform Settings

```
Admin Dashboard
  ↓
Click "Settings"
  ↓
Settings Page (/backend/admin/settings.php)
  ├─ Configure:
  │  ├─ Platform name
  │  ├─ Platform email
  │  ├─ Commission percentage
  │  ├─ Min/Max job budget
  │  ├─ ID verification requirements
  │  ├─ Payment processing settings
  ├─ Save Changes
```

### 8. Create New Admin

```
Admin Dashboard
  ↓
Click "Create Admin"
  ↓
Create Admin Page (/backend/admin/create_admin.php)
  ├─ Enter Email
  ├─ Generate Temporary Password
  ├─ Create Account
  ↓
New admin receives credentials
  ├─ Can log in immediately
  ├─ Access full admin dashboard
```

---

## 📧 Messaging Flow (All Users)

```
Any User (Student/Client/Admin)
  ↓
Click "Messages"
  ↓
Inbox Page (/backend/messages/inbox.php)
  ├─ List all conversations
  ├─ Show:
  │  ├─ Conversation partner name
  │  ├─ Last message preview
  │  ├─ Unread status
  │  ├─ Last message date
  ↓
Click Conversation
  ↓
Read Message Thread (/backend/messages/read_message.php)
  ├─ Display full conversation
  ├─ All messages in chronological order
  ├─ Sender info for each message
  ↓
Reply to Message
  ↓
Send Message (/backend/messages/send_message.php)
  ├─ Type message
  ├─ Optional: Attach file
  ├─ Send
  ↓
Message delivered
  ├─ Status: unread for recipient
  ├─ Notification sent (if enabled)
  ├─ Recipient sees in their inbox
```

---

## 🔄 Common Workflows

### Job Lifecycle

```
1. Client creates job (status: open)
   ↓
2. Students browse and apply (applications created)
   ↓
3. Client reviews applications
   ↓
4. Client accepts one application (status: in_progress, escrow created)
   ↓
5. Other applicants rejected
   ↓
6. Student completes work
   ↓
7. Client reviews work
   ↓
8. Client releases payment (escrow transferred)
   ↓
9. Job status: completed
   ↓
10. (Optional) If dispute: admin resolves
```

### Payment Lifecycle

```
1. Client posts job with budget
2. Application accepted → Escrow created
3. Job in progress → Money held in escrow
4. Work completed
5. Client approves → Payment released to student
   OR
5. Dispute created → Admin investigates
   ↓
6. Admin resolves → Money distributed per decision
```

### Verification Lifecycle

```
1. New user registers (status: pending)
   ↓
2. User uploads ID document (status: pending)
   ↓
3. Admin reviews document
   ├─ Approved → Status: verified (can use full platform)
   └─ Rejected → Status: rejected (must reupload)
```

---

## 🚫 Error & Edge Cases

### User Cannot Access Page If:

- Not logged in → Redirect to login
- Insufficient permissions (wrong role) → Error message
- Account suspended → Access denied
- ID not verified → Limited access warning

### Job Cannot Be Posted If:

- User not verified → Show verification prompt
- Account suspended → Error
- Missing required fields → Form validation error

### Application Cannot Be Submitted If:

- Student not verified → Show verification prompt
- Job already closed → Error
- Student already applied → Prevent duplicate

### Payment Cannot Be Released If:

- Job not in_progress → Error
- Dispute pending → Payment blocked
- Insufficient balance → Error

---

## Summary Statistics

| User Type | Main Actions                      | Key Pages | Status Indicators            |
| --------- | --------------------------------- | --------- | ---------------------------- |
| Student   | Apply, Browse, Complete, Get Paid | 5+        | Verified, Pending, Suspended |
| Client    | Post, Hire, Manage, Pay           | 5+        | Verified, Pending, Suspended |
| Admin     | Review, Verify, Resolve, Manage   | 8+        | Active, Pending, Resolved    |

---

**Last Updated:** January 26, 2026
**Version:** 1.0
