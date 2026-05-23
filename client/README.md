# 💻 RecoverIQ — React Frontend Application

This directory contains the premium React 18 client application for the **RecoverIQ** rehabilitation ecosystem. It features a modern, high-performance **Spotify-inspired dark mode UI** tailored for doctors, patients, and administrators.

---

## 🛠️ Tech Stack & Key Libraries

*   **Core:** React 18 & Vite
*   **Styling:** TailwindCSS (with customized Spotify HSL palette) & Vanilla CSS variables
*   **State Management & Caching:** TanStack React Query (v5) & Axios
*   **Routing:** React Router DOM (v6)
*   **Icons & Assets:** Lucide React
*   **Date Formatting:** Date-fns
*   **Visual Charts:** Recharts (caseload distribution, milestone progression charts)
*   **Feedback & Notifications:** React Hot Toast

---

## 📁 Client Folder Architecture

```text
client/
├── src/
│   ├── components/            # Reusable UI & Feature components
│   │   ├── doctor/            # Doctor components (AssignProgramModal, PatientDetailDrawer, etc.)
│   │   ├── landing/           # Landing page landing components (Hero, Features, Footer)
│   │   ├── layout/            # Sidebar, Navbar, and general wrapper structures
│   │   ├── shared/            # Shared widgets (MetricCard, RecoveryCharts, check-in dialogs)
│   │   └── ui/                # Core elements (Buttons, Alerts, ProgressBars, Select, Modal)
│   │
│   ├── pages/                 # Full feature views
│   │   ├── auth/              # LoginPage, ResetPasswordPage
│   │   ├── doctor/            # Caseload, Analytics, Appointments, Programs & Milestones
│   │   ├── patient/           # Dashboard, Milestones, Daily Timeline, Settings
│   │   └── public/            # LandingPage, Doctor Discovery, Public Booking
│   │
│   ├── lib/                   # API clients and utilities
│   │   └── api.js             # Unified Axios service mapping `patientApi` and `doctorApi`
│   │
│   ├── context/
│   │   └── AuthContext.jsx    # Global context managing user authorization tokens
│   │
│   ├── App.jsx                # Layout mapping and router configurations
│   └── main.jsx               # Entry-point rendering the React DOM
```

---

## 🎨 Theme Customization & Design System

The application relies on customized color systems defined under `src/index.css` and configured within `tailwind.config.js`:
*   `--primary`: `#1DB954` (Spotify green primary indicator)
*   `--background`: `#121212` (Immersive slate black)
*   `--bg-card`: `#181818`, `#282828` (Spotify grey card tiles)
*   `--text`: `#FFFFFF` (Clear headings)
*   `--text-soft`: `#B3B3B3` (Subtle metadata descriptions)

---

## 🚀 Quick Start & Installation

Ensure you have **Node.js v18+** installed before proceeding.

1.  **Navigate into the client directory:**
    ```bash
    cd client
    ```
2.  **Install node packages:**
    ```bash
    npm install
    ```
3.  **Setup environment variables:**
    ```bash
    cp .env.example .env
    ```
    Ensure that `VITE_API_URL` correctly targets your active Laravel server api instance:
    ```env
    VITE_API_URL=http://localhost:8000/api
    ```
4.  **Start Vite dev server:**
    ```bash
    npm run dev
    ```
    *The frontend will boot up and remain accessible at: `http://localhost:5173`*

---

## 🔒 State Sync & API Interceptors

Axios is customized inside [api.js](file:///e:/Laravel%20project/recover/client/src/lib/api.js) to:
*   Automatically attach the active `Bearer <token>` authentication token from `localStorage` to all request headers.
*   Intercept `401 Unauthorized` response errors to automatically clear stale session tokens and redirect users back to the secure login gateway.
*   Utilize **React Query** key caching to verify that dashboard counts, checklist reviews, and notification indicators update immediately on action without redundant page refreshes.