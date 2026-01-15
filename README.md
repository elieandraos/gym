# LiftStation - Gym Management System

A comprehensive gym management system for trainers and members built with Laravel and Vue.js.

## Local Development Setup

### Prerequisites
- PHP 8.2+
- Composer
- Node.js & npm
- MySQL
- Laravel Valet or similar local server

### Installation

1. **Install dependencies:**
```bash
composer install
npm install
```

2. **Configure environment:**
```bash
cp .env.example .env
php artisan key:generate
```

3. **Setup database:**
```bash
php artisan migrate
php artisan db:seed
```

4. **Build assets:**
```bash
npm run dev
```

### Running the Application

You need **3 terminal windows** for full functionality:

**Terminal 1 - Queue Worker (Emails):**
```bash
php artisan queue:work
```

**Terminal 2 - Scheduler (Hourly Tasks):**
```bash
php artisan schedule:work
```

**Terminal 3 - Dev Server:**
```bash
php artisan serve
# or if using Valet
npm run dev
```

### Quick Commands

```bash
# Run tests
php artisan test

# Format code
./vendor/bin/pint
npm run eslint

# View scheduled tasks
php artisan schedule:list

# Process queued jobs immediately
php artisan queue:work --once
```

## Tech Stack
- Laravel 11 with Jetstream and Inertia Vue 3 starter kit
- Jetstream disabled features
  - API support
  - Account deletion
  - Email verification
  - Registration
  - Two-factor Authentication
- Coding style
  - [Pint](https://laravel.com/docs/11.x/pint) for php & Laravel.
  - [Es-lint](https://eslint.org/docs/latest/use/getting-started#quick-start) for Javascript and Vue.
 - Commands
 ```shell
    # run es lint
    npm run eslint

    # run pint
    ./vendor/bin/pint

    # run tests
    php artisan test
  ```

## UI blocks
- `AppLayout`
  - `Container`
    - Headings: `PageHeader` `PageBackButton`

---

## Queue & Scheduler Guide

For local development, run both queue worker and scheduler in separate terminals.

### Testing Without Waiting

```bash
php artisan queue:work --once     # Process one job
php artisan queue:failed          # View failed jobs
php artisan schedule:run          # Run due tasks now
php artisan schedule:list         # View scheduled tasks
```

### Troubleshooting

**Queue not processing:**
- Ensure `QUEUE_CONNECTION=database` in `.env`
- Check `jobs` table and `failed_jobs` table

**Scheduler not running:**
- Use `schedule:work` (not `schedule:run`)
- Check timezone: `APP_TIMEZONE=Asia/Beirut`

**Both not working:**
- Restart terminal processes
- `php artisan cache:clear`
- Check `storage/logs/laravel.log`

---

## Email System

**Configuration:**
- Mail driver: SMTP (Mailtrap for dev)
- Queue connection: database
- Owner emails: `OWNERS_EMAILS` env variable

**Email Types:**
1. **Member Welcome Email** - Sent on registration
2. **Booking Slot Reminder** - Sent 9pm night before session
3. **Owner Notification** - Sent when new member registers

**Testing Emails:**
```bash
php artisan test --filter=MemberWelcomeEmailTest
php artisan test --filter=BookingSlotReminderTest
php artisan test --filter=OwnerNewMemberEmailTest
```

**Email Previews (dev only):**
- `/preview-emails/member/welcome`
- `/preview-emails/member/booking-slot-reminder`
- `/preview-emails/owner`

**Branding:**
- Font: Inter | Logo: `public/logo.png`
- Layout: `resources/views/emails/layouts/branded.blade.php`
- Theme: `resources/views/vendor/mail/html/themes/liftstation.css`

---

## Scheduled Tasks

**Timezone:** Asia/Beirut

**Commands:**

1. **Mark Booking Slots Complete** (`lift-station:mark-booking-slots-complete`)
   - Runs hourly
   - Updates past slots to "Complete" (preserves Cancelled/Frozen)

2. **Send Booking Slot Reminders** (`lift-station:send-booking-slot-reminders`)
   - Runs daily at 9pm
   - Sends reminders for tomorrow's sessions

**Production Setup:**
```bash
* * * * * cd /path-to-project && php artisan schedule:run >> /dev/null 2>&1
```
