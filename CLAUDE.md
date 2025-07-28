# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

This is a Laravel 12 application with Vue 3, Inertia.js, and TypeScript. The project uses:
- **Backend**: Laravel 12, PHP 8.2+
- **Frontend**: Vue 3, TypeScript, Tailwind CSS v4
- **Bridge**: Inertia.js for SPA-like experience
- **Testing**: Pest for PHP, Vue Test Utils for frontend
- **Build**: Vite for asset bundling

## Common Development Commands

### Development Server
```bash
# Start all development services (server, queue, logs, vite)
composer dev

# Start with SSR enabled
composer dev:ssr

# Individual services
php artisan serve          # Laravel server
npm run dev               # Vite dev server
php artisan queue:listen  # Queue worker
php artisan pail         # Real-time logs
```

### Build Commands
```bash
# Frontend build
npm run build
npm run build:ssr  # SSR build

# Code Quality
npm run lint          # ESLint with auto-fix
npm run format        # Prettier formatting
npm run format:check  # Check formatting without fixing
php artisan pint      # PHP code style fixer
```

### Testing
```bash
# PHP tests
composer test           # Run all tests
php artisan test       # Run tests directly
php artisan test --filter TestName  # Run specific test
php artisan test tests/Feature/ExampleTest.php  # Run specific file

# Database
php artisan migrate
php artisan migrate:fresh --seed
```

## Architecture Overview

### Directory Structure
- `app/` - Laravel application code
  - `Http/Controllers/` - HTTP controllers
  - `Models/` - Eloquent models  
  - `Providers/` - Service providers
- `resources/js/` - Vue application
  - `pages/` - Inertia page components
  - `components/` - Reusable Vue components (using Reka UI)
  - `layouts/` - Page layouts
  - `composables/` - Vue composables
  - `lib/` - Utility functions
- `routes/` - Application routes
  - `web.php` - Web routes
  - `auth.php` - Authentication routes
  - `settings.php` - Settings routes

### Key Patterns

1. **Inertia.js Flow**: Controllers return Inertia responses that render Vue pages
   ```php
   return Inertia::render('Dashboard', ['data' => $data]);
   ```

2. **Component Library**: Uses Reka UI (headless components) with Tailwind CSS for styling

3. **TypeScript**: All frontend code uses TypeScript with strict typing

4. **Authentication**: Laravel's built-in auth with Inertia adaptations

5. **State Management**: Uses Vue's reactivity and composables, no Vuex/Pinia by default

### Frontend Routing
- Routes defined in Laravel (`routes/web.php`)
- Inertia handles client-side navigation
- Pages in `resources/js/pages/` correspond to Inertia page names

### API & Data Flow
- Controllers prepare data and pass to Inertia
- Inertia serializes data as page props
- Vue components receive props and handle reactivity
- Forms submit back to Laravel routes via Inertia

## Development Guidelines

1. Follow Laravel conventions and PSR standards
2. Use TypeScript for all frontend code
3. Components should be in `resources/js/components/ui/` for reusable UI
4. Page-specific components can live alongside pages
5. Use Tailwind CSS v4 utilities, avoid custom CSS
6. Leverage Reka UI components for consistency
7. Run linting and formatting before commits

## System Architecture

See `docs/ARCHITECTURE.md` for detailed system architecture, including:
- Data pipeline with Airbyte and dbt
- Multi-tenant architecture with Stancl/tenancy
- AI/NLQ integration approach
- Dashboard and visualization components
- Recommended project structure