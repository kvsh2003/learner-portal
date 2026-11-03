# iMET Learner Portal — Full Stack Assessment

A full-stack learning management portal built with **Laravel 11 REST API** (Backend) and **Next.js 16 App Router** (Frontend), styled with Tailwind CSS, containerized via **Docker**, and deployed to the cloud (**Railway** and **Vercel**).

---

## 1. Project Overview

Learners can:
- Authenticate securely via token-based Sanctum authentication with 1-click demo accounts (`amara@example.com` and `nuwan@example.com`).
- Browse published course catalogues with search and pagination (drafts remain strictly hidden).
- View course details with ordered curriculum modules and lessons.
- Enrol in courses with duplicate prevention.
- Complete lessons and track progress dynamically via calculation formula: `completed lessons / total lessons`.

---

## 2. Live Deployment URLs

- **API Endpoint:** `https://learner-portal-production.up.railway.app` (or your deployed Railway domain)
- **API Health Check:** `https://learner-portal-production.up.railway.app/api/health` (Returns HTTP 200 JSON with DB confirmation)
- **Frontend App:** `https://learner-portal-frontend.vercel.app/` (or your deployed Vercel domain)

> Both URLs will remain active for at least 14 days for review.

---

## 3. Local Setup Instructions (Clean Clone)

### Repositories
- **Backend (Laravel API):** `https://github.com/kvsh2003/learner-portal.git`
- **Frontend (Next.js):** `https://github.com/kvsh2003/learner-portal-frontend.git`

### Prerequisites
- PHP 8.2 or higher
- Composer
- Node.js 20+ and npm
- Docker & Docker Compose (or local MySQL 8.0+)

---

### Option A: Running with Docker Compose (Recommended - Fastest)

Docker Compose starts the pre-built containerized Laravel API (pulled automatically from Docker Hub: `dvish2003/learner-api:latest`) and a dedicated MySQL 8.0 database service with a single command.

```bash
# 1. Clone the backend repository
git clone https://github.com/kvsh2003/learner-portal.git
cd learner-portal

# 2. Boot containers (Docker will pull the prebuilt image and configure MySQL)
docker compose up -d

# 3. Run database migrations and sample course seeds
docker compose exec api php artisan migrate:fresh --seed --force
```

- API is live at: `http://localhost:8000`
- API Health Check: `http://localhost:8000/api/health`
- MySQL is running at: `localhost:3306` (Database: `learner_portal`, User: `root`, Password: `secret`)

Now clone and start the frontend:
```bash
cd ..
git clone https://github.com/kvsh2003/learner-portal-frontend.git
cd learner-portal-frontend
cp .env.example .env.local
npm install
npm run dev
```
Open `http://localhost:3000` in your browser.

---

### Option B: Manual Local Setup (Step-by-Step)

#### 1. Backend Setup (Laravel 11 API)

```bash
# 1. Clone & enter repository
git clone https://github.com/kvsh2003/learner-portal.git
cd learner-portal

# 2. Create your environment file from example
cp .env.example .env

# 3. Install PHP dependencies
composer install

# 4. Generate unique application encryption key
php artisan key:generate
```

**Configure `.env` for Database Connection:**
Open your `.env` file and set your MySQL credentials (ensure your local MySQL server is running and a database named `learner_portal` exists):
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=learner_portal
DB_USERNAME=root
DB_PASSWORD=your_mysql_password
```
*(Alternatively, if you prefer SQLite for quick local inspection, set `DB_CONNECTION=sqlite` and create `database/database.sqlite`).*

**Run Migrations & Seeders:**
```bash
php artisan migrate:fresh --seed
```
This populates the database with:
- 6 courses, modules, and 54 lessons (including 1 unpublished draft course)
- Demo learners:
  - `amara@example.com` / `password`
  - `nuwan@example.com` / `password`

**Run the Server:**
```bash
php artisan serve
```
The REST API is running at `http://localhost:8000`. Test health: `http://localhost:8000/api/health`.

---

#### 2. Frontend Setup (Next.js 16 App Router)

Open a new terminal window:
```bash
# 1. Clone & enter repository
git clone https://github.com/kvsh2003/learner-portal-frontend.git
cd learner-portal-frontend

# 2. Setup environment file
cp .env.example .env.local

# 3. Verify .env.local points to the Laravel API:
# NEXT_PUBLIC_API_URL=http://localhost:8000/api

# 4. Install dependencies
npm install

# 5. Start development server
npm run dev
```
Open `http://localhost:3000` to browse courses, log in using 1-click demo accounts, enrol, and track lesson progress.

---

#### 3. Running Automated Tests

To run the full suite of feature tests locally:
```bash
cd learner-portal
php artisan test
```
All 8 tests (46 assertions) verify authentication, draft protection, duplicate prevention, and progress calculation.

---

## 4. Automated Tests

All assessment criteria are verified via comprehensive PHPUnit feature tests:
```bash
php artisan test
```

### Test Suite (`tests/Feature/LearnerPortalApiTest.php`):
- `health check returns 200 and confirms database`
- `learner can login and access profile`
- `draft courses stay hidden from catalogue and detail` (Returns 404 for drafts)
- `learner can enrol in course and duplicate enrolment is handled` (Handled gracefully with HTTP 200)
- `completing lesson without being enrolled is forbidden` (Returns HTTP 403)
- `enrolled learner can complete lesson and progress updates` (Calculates correct ratio)

---

## 5. Development Hours Log

| Phase | Description | Estimated | Actual |
| :--- | :--- | :--- | :--- |
| **A. Plan** | `TICKETS.md` definition, breakdown, and dependencies | 0.5 h | 0.5 h |
| **B. API Foundations** | Sanctum authentication, health check, course catalogue & draft filter | 1.0 h | 1.0 h |
| **C. Enrolment & Progress** | Enrolment & lesson completion tables, controller logic, N+1 query elimination | 1.0 h | 1.0 h |
| **D. API Tests** | 6+ comprehensive feature tests covering edge cases | 0.5 h | 0.5 h |
| **E. Next.js Catalogue & Detail** | Course listing, live search, pagination, syllabus display | 1.0 h | 1.0 h |
| **F. Next.js Auth & My Learning** | Login page with 1-click demo buttons, protected dashboard, mark complete | 1.0 h | 1.0 h |
| **G. Packaging** | Multi-stage Dockerfile, docker-compose, GitHub Actions workflow | 0.5 h | 0.8 h |
| **H. Cloud Deployment** | Railway (API + MySQL), Vercel (Frontend), CORS & domain wiring | 1.5 h | 1.2 h |
| **I. Documentation** | README, decisions log, architecture notes | 0.5 h | 0.5 h |
| **Total** | | **7.5 h** | **7.5 h** |

---

## 6. Technical Decisions & Trade-Offs

1. **Elimination of N+1 Queries in Progress Calculation:**
   - In `EnrolmentController@index`, rather than querying each course's completions individually, we eager load courses with nested lessons and retrieve all learner completions in a single query, indexing them into an $O(1)$ lookup hash map in PHP.
2. **Idempotent Enrolments & Completions:**
   - Enrolment and lesson completion use `firstOrCreate()` with unique composite database keys to avoid race conditions and duplicate records.
3. **Draft Course Isolation:**
   - An Eloquent global scope or explicit `where('is_published', true)` query constraint is enforced on all public catalogue and detail endpoints, returning `404 Not Found` for unpublished materials.
4. **Token Storage & Frontend State:**
   - Authenticated tokens are stored in `localStorage` for responsive client-side SPA navigation while adhering to clean minimalist UI aesthetics without third-party heavy dependencies.
5. **Modern PHP 8.4 Apache MPM:**
   - Prefork MPM was explicitly enforced inside Docker container to ensure thread-safety for `mod_php`.

---

## 7. AI Tool Usage Note

- **Tool Used:** Antigravity (Google DeepMind)
- **Scope of Use:**
  - Used for rapid scaffolding of boilerplate CRUD operations, test suite structure, and Docker configuration troubleshooting.
  - All database designs, N+1 optimization techniques, security validations, and architecture choices were planned and verified manually.

---

## 8. Cloud Deployment Architecture

- **Backend API & Database:** Hosted on **Railway**. The Laravel API runs in a containerized environment connected to a managed MySQL 8.4 service with automatic restart policies.
- **Frontend Portal:** Hosted on **Vercel** connected to GitHub for automatic continuous deployments on push.
- **Redeployment Strategy:**
  - Pushing to `main` branch triggers GitHub Actions to run tests, build the Docker container, and push to Docker Hub (`dvish2003/learner-api`).
  - Railway automatically triggers re-deployments of the latest image.
- **Production Enhancements (What I would add for scale):**
  - Redis cache layer for course catalogue and session storage.
  - AWS S3 for media assets (lesson videos and attachments).
  - Horizon queue workers with asynchronous notifications when learners reach 100% completion.
