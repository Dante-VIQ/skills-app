# SkillDNA / VumbiDNA

SkillDNA (also referred to as VumbiDNA) is a Laravel-based learning and skills-discovery application. It is intended to help children explore interests and learning resources while giving parents, tutors, and administrators role-specific workflows.

> **Project status:** Development / review. This repository has not yet been verified as production-ready. Run the setup and test steps below, and complete the child-data privacy and security review before using it with real children.

## Current application areas

- Role-aware experiences for children, parents, tutors, and administrators.
- Child onboarding for avatars, interests, learning goals, and onboarding completion.
- Learning-resource discovery and recommendation services.
- Parent-child assignment and activity workflows.
- Child activity, watch-time, skill, and impact-progress reporting.
- Resource curation and YouTube content-management routes.
- Authentication, email-verification middleware, and permission/role foundations.

Some areas may be incomplete or dependent on external services/configuration. Treat the feature list as a guide to the codebase, not a guarantee that every workflow is fully implemented.

## Technology

- PHP 8.2+
- Laravel 12
- Livewire 3 and Livewire Volt
- Laravel Sanctum
- Spatie Laravel Permission
- MySQL/MariaDB or another database supported by Laravel
- Node.js and npm for frontend assets

## Local setup

1. Install PHP 8.2+, Composer, Node.js/npm, and a supported database.
2. Clone the repository and install dependencies:

   ```bash
   composer install
   npm install
   ```

3. Create the environment file and application key:

   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

4. Configure the database connection and any external-service credentials in `.env`.
5. Run database migrations:

   ```bash
   php artisan migrate
   ```

6. Build frontend assets and start the development server:

   ```bash
   npm run build
   php artisan serve
   ```

For active frontend development, use `npm run dev` in a separate terminal. Queue workers or scheduled tasks may be needed by configured discovery/integration features.

## Tests and code quality

Run the automated test suite before proposing or deploying changes:

```bash
php artisan test
```

Format PHP files with Laravel Pint when appropriate:

```bash
./vendor/bin/pint
```

The presence of a test command does not mean the suite has passed; record the actual results for each change.

## Child-data privacy and deployment checklist

Before a production deployment:

- Review authorization and family/guardian access on every child-related route and query.
- Confirm uploaded avatars and other child-related files are not publicly exposed unless explicitly intended.
- Review data minimization, retention/deletion, guardian consent, privacy notices, and applicable child-data laws.
- Check logs for unnecessary personal information and ensure production errors are not exposed to users.
- Configure HTTPS, secure cookies, rate limits, backups, least-privilege database credentials, and production-safe debug settings.
- Verify all integrations, queues, scheduled jobs, and failure handling with test credentials.
- Run migrations and the test suite against a clean test database.

Do not put real children's personal data, production credentials, `.env` files, or database dumps in this repository.

## License

`composer.json` currently declares the project as MIT-licensed, but this repository snapshot does not include a root `LICENSE` file. Confirm the intended copyright holder and license, then add the matching license text before redistribution.
