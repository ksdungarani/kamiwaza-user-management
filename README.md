Laravel Project Setup Guide

This guide outlines the steps to set up and run the Laravel project locally. The project is built with Laravel 12.0, PHP 8.2, Composer 2.7.2, and npm 11.3.0, using Vue with Inertia for the frontend.

## Prerequisites

- **XAMPP**: Installed as the local server for Apache and MySQL.
- **PHP**: Version 8.2.
- **Composer**: Version 2.7.2.
- **Node.js**: npm version 11.3.0.
- **MySQL**: Configured via XAMPP.
- **Git**: For cloning the project repository.

## Setup Instructions

1. **Clone the Project**

   - Clone the project repository to your local environment:

     ```bash
     git clone <repository-url>
     ```
   - Navigate to the project directory:

     ```bash
     cd <project-directory>
     ```

2. **Configure MySQL Database**

   - Start XAMPP and ensure Apache and MySQL services are running.
   - Open `localhost/phpmyadmin` in your browser.
   - Create a new database named `user_management`.
   - Update the `.env` file in the project root with the following database configuration:

     ```env
     DB_CONNECTION=mysql
     DB_HOST=127.0.0.1
     DB_PORT=3306
     DB_DATABASE=user_management
     DB_USERNAME=<your-mysql-username>
     DB_PASSWORD=<your-mysql-password>
     ```

3. **Install Composer Dependencies**

   - In the project directory, run the following command to install PHP dependencies:

     ```bash
     composer install
     ```

4. **Migrate and Seed the Database**

   - Run the following command to create database tables and seed initial data:

     ```bash
     php artisan migrate --seed
     ```

5. **Install Node.js Dependencies**

   - Install npm dependencies and start the Vite development server:

     ```bash
     npm install && npm run dev
     ```

6. **Serve the Project**

   - Start the Laravel development server:

     ```bash
     php artisan serve
     ```
   - Alternatively, use Composer to run the development server:

     ```bash
     composer run dev
     ```

7. **Frontend Setup**

   - The project uses Vue.js with Inertia.js, created via Vue UI.
   - Ensure the Vite development server is running for frontend assets:

     ```bash
     npm run dev
     ```

## Customizations

The project includes modifications to the default Laravel user registration system:

- **User Migration**:
  - The `name` field in the `users` table has been replaced with `first_name` and `last_name`.
- **User Model**:
  - Added `first_name` and `last_name` as fillable fields in the `User` model:

    ```php
    protected $fillable = ['first_name', 'last_name', 'email', 'password'];
    ```
- **Registration Form**:
  - Updated the Vue component for user registration to include `first_name` and `last_name` fields instead of `name`.
- **User Profile Section**:
  - Modified the profile route and related Vue components to display and update `first_name` and `last_name`.
- **Validation Rules**:
  - Updated validation rules to ensure `first_name` and `last_name` accept only alphabetic characters (`alpha` rule).

## Notes

- Ensure all services (Apache, MySQL) are running in XAMPP before starting the project.
- Verify the `.env` file is correctly configured with your MySQL credentials.
- The project uses Vite for frontend asset compilation, so keep `npm run dev` running during development.



## API Implementation

1. **Install Laravel Sanctum**

   - Install Laravel Sanctum to enable API authentication:

     ```bash
     composer require laravel/sanctum
     ```
   - Publish the Sanctum configuration file:

     ```bash
     php artisan vendor:publish --provider="Laravel\Sanctum\SanctumServiceProvider"
     ```
   - Run migrations to create Sanctum's personal access token table:

     ```bash
     php artisan migrate:fresh --seed
     ```
   - Add Sanctum's HasApiTokens trait to the User model (app/Models/User.php):

     ```bash
     use Laravel\Sanctum\HasApiTokens;

        class User extends Authenticatable
        {
            use HasApiTokens, HasFactory, Notifiable;

            protected $fillable = ['first_name', 'last_name', 'email', 'password'];
        }
     ```

2. **Define API Routes**

   - Create or update the `routes/api.php` file with the following routes:

     ```env
     DB_CONNECTION=mysql
     DB_HOST=127.0.0.1
     DB_PORT=3306
     DB_DATABASE=user_management
     DB_USERNAME=<your-mysql-username>
     DB_PASSWORD=<your-mysql-password>
     ```

3. **Install Composer Dependencies**

   - In the project directory, run the following command to install PHP dependencies:

     ```bash
        use App\Http\Controllers\Api\UserController;

        Route::post('/register', [UserController::class, 'store']);
        Route::post('/login', [UserController::class, 'loginAuth']);

        Route::middleware('auth:sanctum')->group(function () {
            Route::apiResource('users', UserController::class)->except(['store', 'loginAuth']);
            Route::post('/logout', [UserController::class, 'logout']);
        });
     ```

4. **Create API Controller**

   - Generate the `UserController` for API operations using the resource controller command:

     ```bash
     php artisan make:controller Api/UserController --api
     ```
  - The controller will be created at `app/Http/Controllers/Api/UserController.php`. Update it with the following code to handle registration, login, logout, and CRUD operations:   

5. **Set Up Postman Collection for API Testing**

   - I added the postman collection file on project repo.

6. **Vue.js Frontend Implementation**

   - Create a UsersList.vue component at `resources/js/Pages/UsersList.vue` to display and manage users via the API:

     ```bash
     npm install axios
     ```
   - Update `resources/js/app.ts` to configure Axios for API requests:

     ```bash
        import axios from 'axios';
        import '../css/app.css';

        axios.defaults.baseURL = '/api';
        axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

        const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
        if (token) {
            axios.defaults.headers.common['X-CSRF-TOKEN'] = token;
        }

        window.axios = axios;
     ```

## Notes

- When testing APIs via Postman, use the auth:sanctum middleware as defined in routes/api.php. For web interface testing without Sanctum tokens, comment out the auth:sanctum middleware in routes/api.php to bypass token authentication.
- Store the API token returned from /register or /login in Postman or the Vue frontend (e.g., localStorage) for authenticated requests.





