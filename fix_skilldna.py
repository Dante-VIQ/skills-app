#!/usr/bin/env python3
"""Apply the reviewed SkillDNA/VumbiDNA repository-readiness fixes.

Run from the repository root:
    python fix_skilldna.py

The script validates all expected source snippets before writing any changes.
It does not commit, push, or merge anything.
"""
from pathlib import Path

ROOT = Path.cwd()

README = """# SkillDNA / VumbiDNA

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
"""

replacements = {
    "routes/web.php": [
        (
            """// Public routes
Route::get('/', function () {
    return view('welcome');
});

// Protected routes
""",
            """// Public routes
// The named landing-page route below is the canonical home route.

// Protected routes
""",
            "duplicate root route",
        ),
        (
            """    // ============= RESOURCE ROUTES =============
    Route::get('/resources/redirect/{id}', [App\\Http\\Controllers\\ExternalResourceController::class, 'redirect'])
        ->name('resources.redirect');

""",
            "",
            "duplicate resource redirect route",
        ),
        (
            """    // Interest selection for new users
    Route::get('/onboarding/interests', [OnboardingController::class, 'showInterests'])
        ->name('onboarding.interests');

    Route::post('/onboarding/interests', [OnboardingController::class, 'saveInterests'])
        ->name('onboarding.interests.save');
// Admin routes for YouTube integration
""",
            """// Admin routes for YouTube integration
""",
            "duplicate onboarding interest routes",
        ),
    ],
    "app/Http/Controllers/Child/ProgressController.php": [
        (
            """    private function getChildSkillsReport($child)
    {
        // This would query skills from completed modules
        // For now, return the child's skills array
        return collect($child->skills ?? [])->map(function ($skill) {
            return [
                'name' => $skill,
                'level' => 'beginner', // This would be calculated
                'progress' => rand(20, 100), // This would be calculated
                'last_practiced' => now()->subDays(rand(0, 30))->format('Y-m-d'),
            ];
        });
    }
""",
            """    private function getChildSkillsReport($child)
    {
        // Report observed activity only. Skill mastery should not be invented
        // until the application has a validated assessment model.
        return collect($child->skills ?? [])->map(function ($skill) use ($child) {
            $activities = $child->activities()
                ->whereHas('contentModule', function ($query) use ($skill) {
                    $query->whereHas('skills', function ($query) use ($skill) {
                        $query->where('name', $skill);
                    });
                });

            $activityCount = (clone $activities)->count();
            $lastPracticed = (clone $activities)->latest()->value('created_at');

            return [
                'name' => $skill,
                'level' => 'Not assessed',
                'progress' => null,
                'activities_count' => $activityCount,
                'last_practiced' => $lastPracticed ? Carbon::parse($lastPracticed)->format('Y-m-d') : null,
            ];
        });
    }
""",
            "random skill progress",
        ),
    ],
    "app/Models/User.php": [
        (
            """        'interests' => 'array',
        'skills' => 'array',
        'learning_goals' => 'array',
        'interests' => 'array',
""",
            """        'interests' => 'array',
        'skills' => 'array',
        'learning_goals' => 'array',
""",
            "duplicate interests cast",
        ),
        (
            """public function getInterestsAttribute()
{
    // Get user's interests from profile or derive from interactions
    return $this->profile['interests'] ?? [];
}
""",
            """    /**
     * Read interests from the dedicated column, falling back to legacy profile data.
     *
     * The explicit accessor must preserve the cast column value; otherwise writes to
     * the `interests` attribute can appear to succeed but read back as an empty array.
     */
    public function getInterestsAttribute($value): array
    {
        if (is_array($value)) {
            return $value;
        }

        if (is_string($value) && $value !== '') {
            $decoded = json_decode($value, true);
            if (is_array($decoded)) {
                return $decoded;
            }
        }

        $legacyInterests = data_get($this->profile, 'interests', []);

        return is_array($legacyInterests) ? $legacyInterests : [];
    }
""",
            "interests accessor overriding the stored column",
        ),
    ],
}

# Validate every target before making any changes, so a changed upstream file
# stops the script rather than causing a partial or unsafe edit.
pending = {}
errors = []
for relative_path, edits in replacements.items():
    path = ROOT / relative_path
    if not path.is_file():
        errors.append(f"Missing required file: {relative_path}")
        continue
    original = path.read_text(encoding="utf-8")
    updated = original
    for old, new, label in edits:
        count = updated.count(old)
        if count != 1:
            errors.append(f"{relative_path}: expected exactly one match for {label}; found {count}")
            break
        updated = updated.replace(old, new, 1)
    else:
        pending[relative_path] = updated

if errors:
    print("No files were changed. Resolve these precondition failures first:")
    print("\n".join(f"- {error}" for error in errors))
    raise SystemExit(1)

pending["README.md"] = README
for relative_path, content in pending.items():
    (ROOT / relative_path).write_text(content, encoding="utf-8")

removed = []
for relative_path in (",", "0"):
    path = ROOT / relative_path
    if path.is_file() and path.stat().st_size == 0:
        path.unlink()
        removed.append(relative_path)
    elif path.exists():
        print(f"Kept {relative_path!r}: it exists and is not empty.")

print("Applied changes:")
for relative_path in sorted(pending):
    print(f"  modified: {relative_path}")
for relative_path in removed:
    print(f"  deleted:  {relative_path}")
print("\nNo commit, push, or merge was performed.")
print("Next: inspect with `git diff --check` and `git diff`, then run `php artisan test`.")
