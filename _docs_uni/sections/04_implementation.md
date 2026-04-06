# 4. IMPLEMENTATION

## 4.1. Implementation Overview

NutriBase is implemented as a decoupled two-tier system: a stateless RESTful API on the backend and a React TypeScript single-page application on the frontend. The backend is built with Laravel 12 [1] running on PHP 8.4 [2] and uses Laravel Sanctum [3] for token-based authentication. All business logic is encapsulated in a Service Layer, data access is abstracted through the Repository Pattern, and domain events are handled via the Observer Pattern. The frontend is a Vite-bundled [9] React application written in TypeScript [8], using TanStack Query [11] for server state management and shadcn/ui [12] for the component library. The two layers communicate exclusively over HTTPS, exchanging JSON payloads that conform to a consistent envelope structure: `{ "data": {...}, "message": "..." }` for success responses and `{ "message": "...", "errors": {...} }` for validation failures.

## 4.2. Backend Technologies

**PHP 8.4 [2]** is the server-side language underpinning the entire backend. PHP 8.4 introduces constructor property promotion, union types, named arguments, and fibers, which reduce boilerplate in Laravel service and model classes. It was chosen because Laravel 12 requires PHP 8.2 or higher, and 8.4 is the current stable release offering the most complete type system.

**Laravel 12 [1]** is the primary backend framework. It provides the MVC structure, Eloquent ORM, route model binding, form request validation, and policy-based authorization used throughout the application. Laravel's service container and dependency injection system make it straightforward to introduce the Service Layer and Repository patterns without framework friction. Its streamlined bootstrap structure (introduced in Laravel 11) reduces configuration overhead.

**Laravel Sanctum [3]** provides lightweight token-based authentication for the SPA. On login, Sanctum issues an opaque personal access token that the React client stores and attaches as a `Bearer` header on all subsequent requests. Sanctum was chosen over Passport because the application does not require OAuth2 flows — simple token issuance and revocation is sufficient for the clinical SPA use case.

**MySQL [4]** is the relational database. The schema consists of four core tables: `users`, `patients`, `patient_socioeconomic`, and `visits`. Foreign key constraints enforce referential integrity across the one-to-one and one-to-many relationships. SQLite is used in-memory during automated testing to provide fast, isolated test runs without requiring a running database server.

**Pest 4 [5]** is the testing framework. All API behaviour is verified through Pest feature tests that make real HTTP requests against the application using an in-memory SQLite database and the `RefreshDatabase` trait. Tests cover happy paths, validation failures, and all role-based authorization combinations. The `actingAs()` helper is used to authenticate requests as specific users without going through the login endpoint.

**Laravel Pint [6]** enforces PSR-12 code style across all PHP files. It is run with the `--dirty` flag as part of the development workflow to format only files that have changed, keeping the codebase consistent without full-suite overhead.

**Larastan [7]** provides static analysis at level 5 using PHPStan's engine extended with Laravel-specific rules. It detects type mismatches, undefined properties on Eloquent models, and incorrect return types before tests are run, catching a category of bugs that tests alone do not cover.

## 4.3. Frontend Technologies

**React 18 [3]** is the UI library. Its component model allows each page (login, patient list, patient profile, visit history) to be developed and tested independently. React's unidirectional data flow makes it straightforward to reason about which server state drives which UI elements.

**TypeScript [8]** adds static typing to the JavaScript frontend. API response shapes are defined as TypeScript interfaces, ensuring that components reference only fields that the API actually returns. This eliminates an entire class of runtime errors caused by field name mismatches between the frontend and backend contract.

**Vite [9]** is the build tool and development server. It provides near-instant hot module replacement during development and optimised production bundles via Rollup. Vite was chosen over Create React App because of its significantly faster cold start time and native ES module support.

**React Router v6 [10]** handles client-side routing. Routes are defined declaratively with nested layouts, allowing the authenticated shell (navigation bar, sidebar) to wrap all protected pages without duplicating layout code. Route-level guards redirect unauthenticated users to the login page.

**TanStack Query [11]** manages server state: fetching, caching, background refetching, and mutation handling. Each API resource (users, patients, visits) has a dedicated query key, and mutations automatically invalidate the relevant cache entries so that list views refresh immediately after a create or update operation without a full page reload.

**shadcn/ui [12]** provides the component library. Built on Radix UI primitives and Tailwind CSS, it offers accessible, unstyled-by-default components (buttons, forms, dialogs, tables) that can be customised to match the clinical UI requirements. Using shadcn/ui avoids writing low-level accessibility logic (focus trapping, ARIA attributes) from scratch.

**Axios [13]** is the HTTP client. A single Axios instance is configured with the API base URL and a request interceptor that attaches the Sanctum Bearer token from the auth store to every outgoing request. A response interceptor handles 401 responses by clearing the token and redirecting to the login page.

## 4.4. Integrations and Services

**Railway / Fly.io [14, 15]** is the deployment platform for the Laravel API backend. Both are PaaS providers that support PHP and MySQL, handle TLS termination, and allow environment variables to be managed through a web dashboard. The Laravel application is deployed as a containerised service; database migrations run automatically as a release command on each deployment.

**CORS (Laravel configuration).** The React SPA and the Laravel API are hosted on separate origins (different ports in development, different subdomains in production). Laravel's built-in CORS middleware is configured in `config/cors.php` to allow the SPA origin, the required HTTP methods (`GET`, `POST`, `PUT`, `PATCH`, `DELETE`), and the `Authorization` and `Content-Type` headers. In production, the allowed origin is restricted to the specific SPA domain to prevent cross-site request forgery.
