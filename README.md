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