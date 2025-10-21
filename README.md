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
