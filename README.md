# Goo-Taxi

A comprehensive taxi booking management system built with Laravel 12, designed to handle complex pricing calculations, driver management, and booking operations with a focus on type safety and code quality.

## Project Purpose

Goo-Taxi is a taxi booking platform that enables users to book taxi rides with sophisticated pricing mechanisms. The system handles:

- **Booking Management**: Users can create, view, and update taxi bookings with detailed location and passenger information
- **Dynamic Pricing**: Complex price calculation system that considers:
  - Base distance-based pricing
  - Time-based adjustments (peak hours, special time ranges)
  - Location-based pricing (nearest pricing zones)
  - Van surcharges for extra large bags
  - Promotional offers and coupon codes
- **Driver Management**: Assign and manage drivers for bookings
- **Offer System**: Create and manage promotional offers with coupon codes, discount rates, and usage limits
- **User Authentication**: Complete authentication system with email verification and password reset via OTP
- **Contact & Feedback**: System for users to submit inquiries and feedback

The application provides both public API endpoints for mobile/web clients and a dashboard interface for administrative management.

## Architectural Flow

The application follows a layered architecture pattern with clear separation of concerns:

### Request Flow Pattern

```
Route → Controller → FormRequest (Validation) → Service/Action → Data DTO → Resource → Response
```

### Key Components

#### 1. **Routes** (`routes/api.php`, `routes/dashboard.php`)
- Define API endpoints and middleware
- API routes: Public and authenticated user endpoints
- Dashboard routes: Admin-only endpoints with `auth:sanctum` and `admin-only` middleware

#### 2. **Controllers** (`app/Http/Controllers/`)
- **API Controllers** (`API/`): Handle public-facing API requests
- **Dashboard Controllers** (`Dashboard/`): Handle admin management operations
- Single-action controllers use `__invoke()` method
- Multi-action controllers follow RESTful conventions
- Controllers are thin - they delegate business logic to Services and Actions

#### 3. **Form Requests** (`app/Http/Requests/`)
- Handle validation logic
- Naming patterns:
  - `{Model}StoreRequest` - Create operations
  - `{Model}UpdateRequest` - Update operations
  - `{Model}FilterRequest` - Filtering/index operations
  - `User{Model}Request` - User-specific requests
- Properties use camelCase convention

#### 4. **Services** (`app/Services/`)
- Handle business logic for CRUD operations
- Use database transactions for data integrity
- Accept Data DTOs instead of raw arrays
- Examples: `BookingService`, `UserService`, `DriverService`

#### 5. **Actions** (`app/Actions/`)
- Single-purpose, reusable business logic operations
- Verb-based naming: `CalculateBookingPriceAction`, `ReturnNearestAdditionalPriceAction`, `GetOfferFromCouponCode`
- Used for complex operations that don't fit into service methods
- Can be injected into Services or Controllers

#### 6. **Data Transfer Objects (DTOs)** (`app/Data/`)
- Spatie Laravel Data classes for type-safe data handling
- Naming: `{Model}Data.php`
- Subdirectories for grouped DTOs: `app/Data/Auth/` (LoginData, RegisterData, etc.)
- Include `HasModelAttributes` trait for model attribute mapping
- Properties in camelCase

#### 7. **API Resources** (`app/Http/Resources/`)
- Format API responses consistently
- Naming: `{Model}Resource.php`, `{Model}SummaryResource.php`
- Properties in camelCase
- Handle media collections automatically

#### 8. **Models** (`app/Models/`)
- Eloquent models with strict typing
- Related files:
  - **Factories**: `database/factories/{Model}Factory.php`
  - **Policies**: `app/Policies/{Model}Policy.php`
  - **Filter Traits**: `app/Traits/FilterQueries/{Model}FilterQuery.php`
- Use PHP 8 constructor property promotion
- Casts defined in `casts()` method

#### 9. **Enums** (`app/Enums/`)
- Type-safe enumerations for status values
- Examples: `BookingStatus`, `OfferStatus`, `ResponseMessages`
- TitleCase naming convention

### Example: Booking Price Calculation Flow

1. **Route**: `POST /api/booking/price` → `BookingPriceController`
2. **Controller**: Validates request using `BookingPriceRequest`
3. **Action**: Calls `CalculateBookingPriceAction` which:
   - Retrieves base price configuration
   - Calculates base distance price
   - Applies van surcharge if needed
   - Gets time-based or location-based adjustments
   - Applies offer discount if valid
   - Returns `BookingPriceData` DTO
4. **Resource**: Formats `BookingPriceData` into `BookingPriceResource`
5. **Response**: Returns JSON response with price breakdown

### Database Layer

- Uses Eloquent ORM exclusively (no raw queries)
- Relationships defined with proper type hints
- Eager loading to prevent N+1 queries
- Database transactions in Services for data integrity

### Authentication & Authorization

- **Sanctum**: API token authentication
- **Policies**: Authorization logic per model
- **Middleware**: Route-level access control (`auth:sanctum`, `admin-only`)

### Testing

- **Pest**: Testing framework (v4)
- **Feature Tests**: `tests/Feature/` - Test HTTP endpoints
- **Unit Tests**: `tests/Unit/` - Test Services, Actions, Models
- **Factories**: Used for test data generation

---

## Laravel Starter Kit

This project is built on an ultra-strict, type-safe [Laravel](https://laravel.com) skeleton engineered for developers who refuse to compromise on code quality. This opinionated starter kit enforces rigorous development standards through meticulous tooling configuration and architectural decisions that prioritize type safety, immutability, and fail-fast principles.

## Why This Starter Kit?

Modern PHP has evolved into a mature, type-safe language, yet many Laravel projects still operate with loose conventions and optional typing. This starter kit changes that paradigm by enforcing:

- **100% Type Coverage**: Every method, property, and parameter is explicitly typed
- **Zero Tolerance for Code Smells**: Rector and PHPStan at maximum strictness catch issues before they become bugs
- **Immutable-First Architecture**: Data structures favor immutability to prevent unexpected mutations
- **Fail-Fast Philosophy**: Errors are caught at compile-time, not runtime
- **Automated Code Quality**: Pre-configured tools ensure consistent, pristine code across your entire team
- **Just Better Laravel Defaults**: Thanks to **[Essentials](https://github.com/nunomaduro/essentials)** / strict models, auto eager loading, immutable dates, and more...

This isn't just another Laravel boilerplate—it's a statement that PHP applications can and should be built with the same rigor as strongly-typed languages like Rust or TypeScript.

## Getting Started

> **Requires [PHP 8.4+](https://php.net/releases/)**.

Create your type-safe Laravel application using :

```bash
git clone https://github.com/Kawarem-Co/laravel-starter-kit
```

### Initial Setup

Navigate to your project and complete the setup:

```bash
cd example-app

# Setup project
composer setup

# Start the development server
composer dev
```

### Verify Installation

Run the test suite to ensure everything is configured correctly:

```bash
composer test
```

You should see 100% test coverage and all quality checks passing.

## Available Tooling

### Development
- `composer dev` - Starts Laravel server, queue worker, log monitoring, and Vite dev server concurrently

### Code Quality
- `composer lint` - Runs Rector (refactoring), Pint (PHP formatting), and Prettier (JS/TS formatting)
- `composer test:lint` - Dry-run mode for CI/CD pipelines

### Testing
- `composer test:type-coverage` - Ensures 100% type coverage with Pest
- `composer test:types` - Runs PHPStan at level 9 (maximum strictness)
- `composer test:unit` - Runs Pest tests with 100% code coverage requirement
- `composer test` - Runs the complete test suite (type coverage, unit tests, linting, static analysis)

### Maintenance
- `composer update:requirements` - Updates all PHP and NPM dependencies to latest versions

## Auto-CRUD Generation

This project includes an auto-CRUD generation system that can automatically create controllers, services, requests, resources, and filter builders for your models.

### Basic Usage

```bash
php artisan auto-crud:generate --model=User
```

### Available Options

- `--service` or `-S` - Generate with service layer (no repository pattern)
- `--filter` or `-F` - Generate filter builder and filter request for advanced filtering
- `--pattern=spatie-data` or `-P=spatie-data` - Use Spatie Laravel Data pattern (requires FormRequest + Data class)
- `--type=api` or `-T=api` - Generate API controller (default: api)
- `--type=web` or `-T=web` - Generate web controller
- `--type=both` or `-T=both` - Generate both API and web controllers
- `--overwrite` or `-O` - Overwrite existing files
- `--model=ModelName` or `-M=ModelName` - Specify model(s) to generate CRUD for
- `--model-path=path` or `-MP=path` - Set custom models path

### Examples

**Generate CRUD with service layer and Spatie Data pattern:**
```bash
php artisan auto-crud:generate --service --pattern=spatie-data --model=User
```

**Generate CRUD with filter builder:**
```bash
php artisan auto-crud:generate --service --pattern=spatie-data --filter --model=User
```

**Generate with all options:**
```bash
php artisan auto-crud:generate --service --pattern=spatie-data --filter --model=User --overwrite
```

### Generated Files

When using `--service --pattern=spatie-data --filter`, the command generates:

1. **UserService** (`app/Services/UserService.php`)
   - Contains `store()` and `update()` methods only
   - Uses `DB::transaction()` for data safety
   - Handles media uploads automatically if model uses `HasMediaConversions` trait
   - Accepts `UserData` objects instead of arrays

2. **UserRequest** (`app/Http/Requests/UserRequest.php`)
   - FormRequest for validation
   - Properties in camelCase
   - Includes media validation rules automatically

3. **UserFilterRequest** (`app/Http/Requests/UserFilterRequest.php`) - *when using `--filter`*
   - Validation for `search` and `perPage` parameters

4. **UserFilterBuilder** (`app/FilterBuilders/UserFilterBuilder.php`) - *when using `--filter`*
   - Extends `BaseFilterBuilder`
   - Includes `textSearch()` method if model has searchable properties
   - Used in controller's `index()` method

5. **UserData** (`app/Data/UserData.php`)
   - Spatie Laravel Data class
   - Includes `HasModelAttributes` trait
   - Automatically includes media properties if model uses media trait

6. **UserResource** (`app/Http/Resources/UserResource.php`)
   - API Resource for responses
   - Includes `@mixin` PHPDoc tag
   - Always includes `id`, `createdAt`, `updatedAt`
   - Automatically handles media collections
   - Properties in camelCase

7. **UserController** (`app/Http/Controllers/API/UserController.php`)
   - No base controller extension
   - No try-catch blocks (Laravel handles exceptions)
   - Uses Resources for responses
   - Index method uses FilterBuilder when `--filter` is enabled
   - Loads media relations automatically
   - Destroy returns `response()->noContent()` (204 status)

### Features

- **Automatic Media Detection**: Detects media fields from model traits and generates appropriate code
- **Type Safety**: Uses Data classes with proper type hints
- **Transaction Safety**: All store/update operations wrapped in DB transactions
- **CamelCase Properties**: All request and resource properties use camelCase
- **Media Support**: Automatically handles single and multiple file uploads
- **Filter Support**: Advanced filtering with search and pagination

## License
**Laravel Starter Kit** was created by **[Mustafa Fares](https://www.linkedin.com/in/mustafa-fares/)** Forking from **[Nuno Maduro](https://x.com/enunomaduro)** Package.
