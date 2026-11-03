# Learner Portal — Development Tickets

**Project:** iMET Learner Portal  
**Role:** Associate Full Stack Developer  
**Stack:** Laravel, MySQL, Next.js, TypeScript, Tailwind CSS

---

## Ticket 1: Laravel API Setup

**Estimate:** 0.5 hours  
**Priority:** High

### Tasks
- Set up Laravel 11+ project.
- Configure MySQL database connection.
- Import the provided starter kit.
- Run database migrations and seeders.

### Acceptance Criteria
- Laravel application runs successfully.
- Database connection works.
- Starter courses, modules, lessons, and demo learners are available.

**Dependencies:** None

---

## Ticket 2: Learner Authentication

**Estimate:** 0.5 hours  
**Priority:** High

### Tasks
- Implement login using Laravel Sanctum.
- Implement logout.
- Implement current learner endpoint.
- Protect authenticated API routes.

### Acceptance Criteria
- Learners can log in using valid credentials.
- Invalid credentials return an appropriate error.
- Authenticated learners can access their profile.
- Logout revokes the current token.

**Dependencies:** Ticket 1

---

## Ticket 3: Course Catalogue API

**Estimate:** 0.5 hours  
**Priority:** High

### Tasks
- Implement published courses endpoint.
- Add search by course title.
- Add pagination.
- Include module and lesson counts.
- Implement course details with ordered modules and lessons.

### Acceptance Criteria
- Only published courses are returned.
- Search and pagination work correctly.
- Course details include modules and lessons in order.
- Draft courses return 404.

**Dependencies:** Ticket 1

---

## Ticket 4: Enrolment and Progress Tracking

**Estimate:** 1 hour  
**Priority:** High

### Tasks
- Create enrolments database table.
- Create lesson completions database table.
- Implement course enrolment endpoint.
- Implement lesson completion endpoint.
- Implement learner enrolments and progress endpoint.

### Acceptance Criteria
- Learners can enrol in published courses.
- Duplicate enrolments are prevented.
- Only enrolled learners can complete lessons.
- Duplicate lesson completions are prevented.
- Course progress is calculated correctly.

**Dependencies:** Tickets 1, 2, and 3

---

## Ticket 5: Laravel API Testing

**Estimate:** 0.5 hours  
**Priority:** High

### Tasks
- Write at least six Laravel feature tests.
- Test authentication and course endpoints.
- Test duplicate enrolment.
- Test lesson completion authorization.
- Test draft course visibility.

### Acceptance Criteria
- At least six feature tests are implemented.
- All tests pass successfully.
- Important authorization and validation cases are covered.

**Dependencies:** Tickets 2, 3, and 4

---

## Ticket 6: Next.js Learner Portal

**Estimate:** 2 hours  
**Priority:** High

### Tasks
- Create Next.js App Router application.
- Build course catalogue with search and pagination.
- Build course detail page.
- Build learner login page.
- Build protected My Learning dashboard.
- Add enrolment and lesson completion actions.
- Add responsive layouts and loading/error states.

### Acceptance Criteria
- Learners can browse courses.
- Learners can view course details.
- Learners can log in securely.
- Learners can enrol and complete lessons.
- Learners can view their course progress.

**Dependencies:** Tickets 2, 3, and 4

---

## Ticket 7: Containerization, Cloud Deployment and Documentation

**Estimate:** 2.5 hours  
**Priority:** High

### Tasks
- Configure multi-stage production Dockerfile for Laravel API (PHP 8.4 Apache with prefork MPM).
- Configure `docker-compose.yml` pulling image `dvish2003/learner-api` with healthchecked MySQL 8.0.
- Set up GitHub Actions CI/CD workflow (`docker-publish.yml`) to run tests and push container images to Docker Hub.
- Deploy Laravel REST API and managed MySQL database on **Railway** with health checks (`/api/health`).
- Deploy Next.js frontend on **Vercel** connected via environment variables (`NEXT_PUBLIC_API_URL`).
- Configure production CORS and HTTPS security.
- Write comprehensive `README.md` covering local setup, hours log, architectural decisions & trade-offs, AI tools usage, and deployment steps.

### Acceptance Criteria
- Docker Compose spins up the entire backend and database with a single command.
- GitHub Actions pipeline runs automated tests and builds successfully on push.
- Backend API is live on Railway with `/api/health` returning HTTP 200 JSON confirming database connectivity.
- Frontend is live on Vercel with full catalogue, search, enrolment, and lesson completion functionality.
- Demo learners (`amara@example.com`, `nuwan@example.com`) can authenticate in production.
- Repository contains `.env.example` files without any hardcoded credentials or secrets.

**Dependencies:** Tickets 1–6

---

## Development Notes

- Keep the GitHub repository private.
- Use small, meaningful commits.
- Record actual development time.
- Explain technical decisions and trade-offs.
- Ensure all code can be explained during the interview.
