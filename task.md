# Task Checklist: Advanced Quiz & Learning Management Platform

## Phase 1: Initial Setup
- [x] Initialize Laravel 11 project.
- [x] Configure Environment for MySQL.
- [x] Create initial Database (`laravel_lms`).
- [x] Install Laravel Breeze (or set up custom auth for Admin/Student).
- [x] Set up TailwindCSS with Neumorphism configurations.

## Phase 2: Database & Models (Admin Flow)
- [x] Create `School`, `Branch`, `AcademicYear`, `Group` models & migrations.
- [x] Update `User` model & migration (Admin vs. Student fields like Massar Code).
- [x] Configure Relationships between Models.
- [x] Create initial seeder for testing Admin user fallback or install route.

## Phase 3: Core LMS Features
- [x] Create `Lesson`, `Quiz`, `Question`, `Option`, `Result`, `QuizRetake` models & migrations.
- [x] Build Admin Controllers and Views for managing entities (CRUD).
- [x] Implement File Uploads for PDFs/Videos (Laravel Storage).
- [x] Set up `maatwebsite/excel` for Student CSV Imports.

## Phase 4: Student Flow
- [x] Create Student Auth Pages.
- [x] Implement Dashboard to fetch valid Groups & Lessons.
- [x] Build Dynamic Quiz Engine (Blade UI Scaffolded).
- [x] Implement Results calculation and Auto-Remediation logic.

## Phase 5: Additional Features
- [x] Direct Messaging (Students <-> Admin).
- [x] Maintenance Mode Toggle & Middleware.
- [x] Analytics & Progress Tracking views.
- [x] Refine Neumorphism CSS variables/tokens globally.

## Phase 6: Polish & Verification
- [x] Ensure all Views apply Neumorphism Design (Cards, Inputs, Buttons).
- [x] Write missing unit tests for critical paths.
- [x] Manual end-to-end testing.
