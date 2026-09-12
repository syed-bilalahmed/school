# SaaS Roadmap for Multi-School School Management System

## Goal
Run one platform for many schools (tenants) with strict data isolation, role-based access, billing, and fully connected modules.

## Current State Snapshot
- App is single-tenant.
- Most tables and queries have no school_id filter.
- Roles exist (super_admin, admin, teacher, parent, student) but are global, not tenant-scoped.
- Settings/CMS appear single-row and global.

## Phase 1 - Core Multi-Tenancy Foundation

### 1. Add tenant data model
Create these tables:
- schools: id, code, name, domain, status, plan_id, created_at
- school_plans: id, name, limits_json, price
- school_subscriptions: id, school_id, plan_id, status, start_at, end_at, billing_provider, external_id

Add school_id to business tables (mandatory for tenant data):
- users
- students
- classes
- sections
- subjects
- fees tables
- exams tables
- attendance tables
- payroll tables
- expenses tables
- library tables
- inventory tables
- notifications
- front CMS tables
- settings tables

Index pattern:
- index on school_id for every tenant table
- composite unique per school where needed (example: unique(school_id, email), unique(school_id, admission_no))

### 2. Tenant context resolution
Resolve active school by:
- custom domain/subdomain, or
- explicit URL segment (/s/{school_code}/...)

Store active school in request context and session:
- session school_id
- session school_code

### 3. Enforce tenant scope in data access
Update all model queries to include school_id in WHERE or INSERT values.
No query should read or write tenant data without school_id.

## Phase 2 - Auth, Roles, and Permissions

### 1. Role model redesign
Use tenant-scoped RBAC:
- roles: id, school_id, name
- permissions: id, key
- role_permissions: role_id, permission_id
- user_roles: user_id, role_id

Suggested baseline roles per school:
- school_owner
- school_admin
- teacher
- finance_manager
- accountant
- librarian
- parent
- student

### 2. Guard middleware
Create centralized guards:
- requireAuth
- requireSchoolContext
- requirePermission(permission_key)

Controllers should call guard helpers instead of manual session checks.

## Phase 3 - Data Isolation and Security

### 1. Isolation policy
Every read/write must satisfy:
- user belongs to school_id
- target record belongs to same school_id

### 2. Hardening
- CSRF tokens for all POST forms
- audit log for critical actions (fees, marks, payroll, settings)
- password reset flow
- login throttling and account lock policy
- secure file upload validation and tenant file paths

### 3. Backups
Per-tenant backup/restore and disaster recovery plan.

## Phase 4 - Functional Completion by Module

### Priority order
1. Admissions and Students
2. Academics (Class, Section, Subject, Timetable)
3. Attendance
4. Exams and Results
5. Fees and Finance
6. Payroll
7. Inventory and Library
8. Communication and Notifications
9. Reports and Analytics
10. Front CMS per school

### Cross-module integration requirements
- Student is the core entity linked to attendance, exam, fees, library, and communication.
- Teacher linked to timetable, attendance, homework, and result entry.
- Finance data linked to student and class dimensions for reporting.

## Phase 5 - SaaS Operations

### 1. Super Admin portal
Build platform-level views:
- onboard new school
- activate/suspend school
- assign plan
- usage metrics by school
- billing status

### 2. School onboarding wizard
- school profile
- academic session setup
- classes/sections bootstrap
- admin account creation
- optional sample data seed

### 3. Billing
Integrate payment provider:
- subscription lifecycle
- renewal reminders
- grace periods
- invoice history

## Phase 6 - Reliability and Scale

### 1. Infrastructure
- move uploads to object storage
- queue worker for email/report generation
- cache hot lookups
- centralized logs and error monitoring

### 2. Testing
- tenant isolation tests (must-have)
- role authorization tests
- integration tests per core module

### 3. Release strategy
- feature flags
- migration rollback plan
- blue/green or rolling deployment

## Immediate Work Plan (Next 14 Days)

### Week 1
- Add schools and subscription tables
- Add school_id to users and students first
- Implement school context resolver and session binding
- Update login to enforce school-aware authentication

### Week 2
- Refactor student, class, section, and user queries to enforce school_id
- Add guard helper for permission checks
- Add super admin school management screens (basic CRUD)

## Definition of Done for SaaS Readiness
- Any user can access only their school data.
- All role checks are permission-based, not hardcoded strings in controllers.
- New school can be onboarded without code changes.
- Billing and school status can enable/disable access.
- Core modules (students, attendance, exams, fees) operate end-to-end with tenant isolation.

## Notes for This Codebase
- Start by introducing school_id in User, Student, SchoolClass, Section, Fee, Exam, Attendance models.
- Replace controller-level role if chains with a centralized authorization helper.
- Move one-off admin checks to permission gates.
- Keep super_admin as platform-level role only.
