# Guest Appointment Booking Flow

This document describes the exact flow when a guest (not logged in) books a session on the public booking page.

## Flow Steps

### 1. Guest Submits Booking Form

**Location:** `client/src/pages/public/Book.jsx`

- User fills out: name, email, confirm email, doctor, date/time, problem description
- Form validates and calls: `POST /api/appointments/public`

### 2. Backend Creates User + Patient + Appointment

**Location:** `server/app/Http/Controllers/Api/Public/AppointmentController.php` → `store()`

#### Step 2a: User Creation

- Checks if user exists by email
- If NOT exists:
  - Generates random 10-character plaintext password
  - Creates `User` record with:
    - `name` = booked_by_name
    - `email` = booked_by_email
    - `password` = hashed (automatic via User model cast)
    - `role` = 'patient'
    - `is_active` = true ✅ (explicitly set)
  - Stores plaintext password in variable for email

#### Step 2b: Patient Record Creation

- Checks if patient exists for that user
- If NOT exists:
  - Creates `Patient` record with:
    - `user_id` = newly created user's ID
    - `doctor_id` = selected doctor's ID
    - `program_id` = null (assigned later)
    - `enrolled_at` = now()

#### Step 2c: Appointment Creation

- Creates `Appointment` record with:
  - `patient_id` = patient's ID (now linked!)
  - `doctor_id` = selected doctor
  - `slot_at` = selected time
  - `status` = 'pending'
  - `booked_by_name`, `booked_by_email` = form inputs
  - `problem_description` = form input

### 3. Send Credentials Email

**Location:** Same controller, after transaction

```php
if ($booking['plainPassword']) {
    Mail::to($booking['user']->email)->send(
        new PatientCredentialsMail($booking['user'], $booking['plainPassword'], $booking['appointment'])
    );
}
```

- Email template: `server/resources/views/emails/patient-credentials.blade.php`
- Sent to: user's email
- Contains:
  - Login URL (frontend)
  - Email address
  - **Plaintext password** (shown once!)

### 4. Response to Frontend

- Status: 201 (Created)
- Message: "Appointment booked. Your patient login credentials have been emailed to you."
- Returns: appointment details

### 5. Guest Receives Email

- User opens email and reads login credentials
- Email expires/password is temporary (user should change on first login)

### 6. User Logs In

**Location:** `client/src/pages/auth/` or login form

- User navigates to login page
- Enters: email + password (from email)
- Backend validates via Sanctum (checks `User` table)
- Returns JWT token if credentials match

### 7. Patient Sees Appointment in Dashboard

**Location:** `client/src/pages/patient/Dashboard.jsx`

- Frontend calls: `GET /api/patient/appointments` with JWT token
- Backend (`server/app/Http/Controllers/Api/Patient/AppointmentController.php` → `index()`):
  - Gets authenticated user's patient record
  - Queries `Appointment` where `patient_id` = patient's ID
  - Returns all appointments including the newly booked one with status='pending'

### 8. Doctor Sees Pending Appointment

**Location:** Doctor dashboard: `client/src/pages/doctor/Appointments.jsx`

- Doctor logs in (already has account)
- Calls: `GET /api/doctor/appointments`
- Backend returns all appointments for that doctor
- Shows in "Pending" tab (status filter)
- Doctor can Accept/Reject the appointment request

## Current Issues Addressed

✅ **Explicit is_active=true** - Ensures new patient can log in immediately  
✅ **Patient linked to appointment** - Appointment shows in patient's pending list after login  
✅ **Email sent synchronously** - Ensures credentials arrive before patient logs in  
✅ **Auto-attach legacy appointments** - Handles edge case where appointment is created before patient linkage

## Performance Notes

⚠️ **Mail is sent synchronously** (blocking request)

- This ensures email is sent before user logs in
- For production: migrate to queued mail (`->queue()`) but user must wait for worker to process
- Current: ~1-3 second delay per booking (acceptable for now)

## To Test This Flow

1. **Open booking page:** http://localhost:5173/book
2. **Book without logging in** using fake email: `test@example.com`
3. **Check email** (should arrive immediately or check Laravel logs)
4. **Log in** with that email + the password from email
5. **Go to patient dashboard:** should show "Pending" appointment
6. **Switch to doctor account** (if available): appointment shows in doctor's "Pending" list

## Files Involved

- Frontend:
  - `client/src/pages/public/Book.jsx` (booking form)
  - `client/src/pages/auth/` (login)
  - `client/src/pages/patient/Dashboard.jsx` (shows pending)
- Backend:
  - `server/app/Http/Controllers/Api/Public/AppointmentController.php` (booking)
  - `server/app/Http/Controllers/Api/Patient/AppointmentController.php` (patient list)
  - `server/app/Http/Controllers/Api/Doctor/AppointmentController.php` (doctor list)
  - `server/app/Mail/PatientCredentialsMail.php` (email template logic)
  - `server/resources/views/emails/patient-credentials.blade.php` (email HTML)

- Database:
  - `users` table (stores login credentials)
  - `patients` table (links user to doctor)
  - `appointments` table (stores booking)
