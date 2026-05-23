# ⚙️ RecoverIQ — Laravel REST API Backend Server

This directory contains the robust, clinical-grade **Laravel 11 REST API** server for the **RecoverIQ** rehabilitation ecosystem. It handles stateful multi-role authentication, database persistence, relationship logic, and secure clinical transactions.

---

## 🛠️ Backend Stack & Integrations

*   **Framework:** Laravel 11 (powered by PHP 8.2+)
*   **Authentication:** Laravel Sanctum (Stateful HTTP-only cookie + Bearer API Token authorization)
*   **Roles & Permissions:** Spatie Laravel-Permission (Admin, Doctor, Patient)
*   **Mailers:** Symfony Mailgun Mailer SDK
*   **Database:** MySQL / SQLite (with SoftDeletes for data retention safety)

---

## 📊 Database Models & Relationships

The relational architecture ensures strict boundaries and references:

```text
User ──── HasOne ───> Doctor ──── HasMany ───> Patient (Assigned Case)
User ──── HasOne ───> Patient <── HasMany ─── AuthoredReviews (DoctorReview)
                               <── HasMany ─── Prescriptions (Prescription)
                               <── HasMany ─── DailyLogs (PatientDailyLog)
                               <── HasMany ─── ProgressRecords (PatientProgress)
```

### Key Models Mapped:
1.  **User:** Base profile holding login credentials, roles, and status flags.
2.  **Doctor:** Linked clinician profile managing specializations, reviews, and clinical cases.
3.  **Patient:** Linked patient profile referencing their current `RehabProgram` and assigned `Doctor`.
4.  **RehabProgram:** Rehabilitation program template (15, 30, 60, or 90 days duration).
5.  **ProgramMilestone:** Individual task/milestone in a program.
6.  **PatientProgress:** Real-time log of milestone check-ins (requires Doctor reviews).
7.  **PatientDailyLog:** Patient daily check-in (pain scale 1-10, mobility score, mood, exercise status).
8.  **Prescription:** Medical prescription containing diagnosis notes and schedules.
9.  **PrescriptionMedicine:** Specific drugs associated with a prescription (dose, duration).
10. **Appointment:** Unified booking slots linking patients, doctors, and slot times.

---

## 📁 Backend Folder Architecture

```text
server/
├── app/
│   ├── Http/Controllers/Api/   # API controller logic grouped by actor roles
│   │   ├── Admin/              # User management, program creations, appointment ledger
│   │   ├── Auth/               # Registration, Login, ForgotPassword, verification controllers
│   │   ├── Doctor/             # Note, Prescription, duplicate programs, caseload dashboards
│   │   └── Patient/            # Check-ins, milestones tracking, prescription viewing
│   │
│   ├── Models/                 # Eloquent entities (User, Patient, DailyLog, Note, etc.)
│   └── Mail/                   # PatientCredentialsMail & AppointmentConfirmationMail
│
├── database/
│   ├── migrations/             # Full relational database schemas
│   └── seeders/                # Seeds database with admin, doctors, patients, and standard programs
│
├── routes/
│   └── api.php                 # Core REST API endpoint registry
│
└── tests/                      # Automated API integration tests
```

---

## 🚀 Quick Start & Installation

Ensure you have **PHP 8.2+** and **Composer** installed.

1.  **Navigate into the server directory:**
    ```bash
    cd server
    ```
2.  **Install PHP packages:**
    ```bash
    composer install
    ```
3.  **Setup environment configuration:**
    ```bash
    cp .env.example .env
    ```
    *Configure database (`DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD`), mailer drivers (`MAIL_MAILER`), and frontend URLs inside the `.env` file.*

4.  **Generate application secure key:**
    ```bash
    php artisan key:generate
    ```
5.  **Rebuild database & seed default data:**
    ```bash
    php artisan migrate:fresh --seed
    ```
    This seeds the initial sandbox credentials (`admin@recoveriq.com`, `doctor@recoveriq.com`, `patient@recoveriq.com` - all with password `password`).
6.  **Start development server:**
    ```bash
    php artisan serve
    ```
    *The API will start and remain active at: `http://localhost:8000`*

---

## 📧 Mail Notifications Configuration

RecoverIQ handles automated outreach workflows:
*   **Patient Credentials Email:** Dispatched when a clinician registers a new patient. Auto-generates their temporary login credentials and password reset link.
*   **Appointment Confirmation Email:** Sent automatically upon guest booking confirmations or status overrides.
