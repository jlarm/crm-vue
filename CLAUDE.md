# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Tech Stack

This is a **Laravel 12 + Vue 3 + Inertia.js** application using:
- **Backend**: Laravel 12.0 (PHP 8.2+) with SQLite database
- **Frontend**: Vue 3 + TypeScript, Inertia.js for SPA experience
- **Styling**: Tailwind CSS 4.1.1 with Shadcn/ui components (Reka UI)
- **Build**: Vite 6.2.0 with SSR support
- **Testing**: Pest PHP

## Development Commands

**Primary development workflow:**
- `composer dev` - Starts full development stack (Laravel server + queue + logs + Vite)
- `composer dev:ssr` - Development with server-side rendering
- `composer test` - Run Pest PHP tests

**Frontend-specific commands:**
- `npm run dev` - Vite development server only
- `npm run build` - Production build
- `npm run build:ssr` - Production build with SSR
- `npm run lint` - ESLint with auto-fix
- `npm run format` - Prettier formatting

## Architecture

**Inertia.js Full-Stack SPA:**
- Pages in `/resources/js/pages/` map to Laravel routes
- Shared data injected via `HandleInertiaRequests.php` middleware
- Server-side routing through Laravel, client-side navigation via Inertia

**Component Architecture:**
- Shadcn/ui components in `/resources/js/components/ui/`
- Layouts in `/resources/js/layouts/`
- Vue 3 Composition API with TypeScript throughout
- Theme system with light/dark mode + system preference

**Key Files:**
- `/resources/js/app.ts` - Vue application bootstrap
- `/app/Http/Middleware/HandleInertiaRequests.php` - Global shared props
- `/resources/js/types/` - TypeScript type definitions
- `/components.json` - Shadcn/ui configuration

**Path Mapping:**
- `@/*` resolves to `resources/js/*` in TypeScript imports

## Development Patterns

**Laravel Backend:**
- Standard Laravel MVC with Inertia responses instead of Blade views
- Controllers return Inertia::render() with page components and props
- Authentication system in `/app/Http/Controllers/Auth/`

**Vue Frontend:**
- Page components receive props from Laravel controllers
- Use Composition API and TypeScript for all components
- Tailwind classes with Shadcn/ui design tokens via CSS variables
- Theme switching managed via appearance composable

**Styling:**
- Tailwind CSS 4.x with inline configuration
- Use existing UI components from `/resources/js/components/ui/`
- CSS variables for theme tokens (--background, --foreground, etc.)

## Testing

- Use Pest PHP for backend tests
- Test files in `/tests/Feature/` and `/tests/Unit/`
- Run tests with `composer test`

## Code Quality

- ESLint + Prettier configured for Vue 3 + TypeScript
- Run `npm run lint` before commits
- Use `npm run format` for consistent code formatting