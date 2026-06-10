<?php

namespace App\Console\Commands;

use App\Enums\UserType;
use App\Models\Instructor;
use App\Models\Page;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;

/**
 * Headless replacement for the web installer (Modules\Installer).
 *
 * The web installer writes its "installed" flag and admin user via an
 * interactive wizard whose state lives on local disk — which is ephemeral on
 * DigitalOcean App Platform. This command performs the same one-time setup
 * idempotently from environment variables so deploys are fully automated:
 *
 *   - runs database migrations (app + modules)
 *   - seeds base settings/pages/languages once (only on an empty database)
 *   - creates/updates the admin user + instructor profile
 *   - points the home_page setting at a real page
 *
 * Pair this with MENTOR_INSTALLED=true so the installer routes stay bypassed.
 */
class ProvisionApp extends Command
{
    protected $signature = 'app:provision
        {--fresh : Wipe and recreate the schema (migrate:fresh) before seeding}';

    protected $description = 'Headless first-run provisioning (migrate, seed, create admin) for automated deploys';

    public function handle(): int
    {
        $this->info('Provisioning application…');

        // 1. Schema -----------------------------------------------------------
        if ($this->option('fresh')) {
            $this->warn('Running migrate:fresh --seed (destructive)…');
            $this->call('migrate:fresh', ['--force' => true, '--seed' => true]);
        } else {
            $this->call('migrate', ['--force' => true]);

            // Seed base data only on a pristine database so re-runs stay safe.
            if (Schema::hasTable('settings') && Setting::count() === 0) {
                $this->info('Empty database detected — seeding base data…');
                $this->call('db:seed', ['--force' => true]);
            } else {
                $this->line('Base data already present — skipping seed.');
            }
        }

        // 2. Admin user -------------------------------------------------------
        $email = env('ADMIN_EMAIL');
        $password = env('ADMIN_PASSWORD');
        $name = env('ADMIN_NAME', 'Administrator');

        if ($email && $password) {
            $admin = User::updateOrCreate(
                ['email' => $email],
                [
                    'name' => $name,
                    'role' => UserType::ADMIN,
                    'status' => 1,
                    'password' => Hash::make($password),
                    'email_verified_at' => now(),
                ]
            );

            if (! $admin->instructor_id) {
                $instructor = Instructor::create([
                    'user_id' => $admin->id,
                    'status' => 'approved',
                    'designation' => 'Administrator',
                    'skills' => json_encode(['admin']),
                    'biography' => 'Platform administrator.',
                ]);
                $admin->instructor_id = $instructor->id;
                $admin->save();
            }

            $this->info("Admin user ready: {$email}");
        } else {
            $this->warn('ADMIN_EMAIL / ADMIN_PASSWORD not set — skipping admin creation.');
        }

        // 3. Settings (mirror installer's post-install wiring) ----------------
        $courseCreation = env('COURSE_CREATION', 'administrative'); // collaborative|administrative

        if (Schema::hasTable('settings')) {
            $page = Page::where('type', $courseCreation)->first()
                ?? Page::where('type', 'home_page')->first()
                ?? Page::first();

            if ($system = Setting::where('type', 'system')->first()) {
                $system->update(['sub_type' => $courseCreation]);
            }

            if ($page && $homePage = Setting::where('type', 'home_page')->first()) {
                $homePage->update([
                    'fields' => [
                        'page_id' => $page->id,
                        'page_name' => $page->name,
                        'page_slug' => $page->slug,
                    ],
                ]);
            }
        }

        $this->info('Provisioning complete.');

        return self::SUCCESS;
    }
}
