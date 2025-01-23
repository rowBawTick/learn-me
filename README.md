# Learn Me Project

A modern web application built with Laravel, Vue.js, and TailwindCSS.

## Requirements

- PHP >= 8.3
- MySQL >= 9.0
- Node.js >= 23.0
- npm >= 10.0
- Composer >= 2.8

## Tech Stack

- Laravel 11
- Vue 3
- TailwindCSS 3
- MySQL
- Vite

## Setup Instructions

1. **Install dependencies**
   ```bash
   composer install
   npm i
   ```

2. **Environment Setup**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

3. **Configure Database**
   - Create a MySQL database
   - Update `.env` file with your database credentials:
     ```
     DB_CONNECTION=mysql
     DB_HOST=127.0.0.1
     DB_PORT=3306
     DB_DATABASE=your_database_name
     DB_USERNAME=your_username
     DB_PASSWORD=your_password
     ```

4. **Run Migrations**
   ```bash
   php artisan migrate
   ```

5. **Build Assets and start server**
   ```bash
   npm run build
   ```

## Development

To start the development server:

1. For Development:
   ```bash
   php artisan serve
   npm run dev
   ```

The application will be available at `http://localhost:8000`
