# IT Maintenance Checklist

This system is a Laravel-based application designed to help IT professionals manage and track maintenance tasks efficiently. It provides a user-friendly interface for creating, updating, and deleting maintenance tasks, as well as viewing task history.

## Prerequisites

Before you begin, ensure you have the following prerequisites installed on your machine:
- PHP (version 8.1 or higher)
- Composer (dependency manager for PHP)
- MySQL or another supported database
- Valet (optional, for local development)

## Getting Started

To get started with the IT Maintenance Checklist application, follow these steps:

1. **Clone the Repository**: Clone this repository to your local machine using the following command:
   ```bash
   git clone git@github.com:apan17/it-maintenance-checklist.git
   ```
2. **Navigate to the Project Directory**: Change into the project directory:
   ```bash
   cd it-maintenance-checklist
   ```
3. **Install Dependencies**: Install the required PHP dependencies using Composer:
   ```bash
   composer install
   ```
4. **Set Up Environment Variables**: Copy the `.env.example` file to `.env` and configure your environment variables, including database connection settings:
   ```bash
   cp .env.example .env
   ```
5. **Generate Application Key**: Generate a new application key:
   ```bash
   php artisan key:generate
   ```
6. **Run Migrations**: Run the database migrations to set up the necessary tables:
   ```bash
   php artisan migrate
   ```
7. **Seed the Database** (optional): If you want to populate the database with sample data, you can run the seeder:
   ```bash
   php artisan db:seed
   ```
8. **Start the Development Server**: Start the Laravel development server:
   ```bash
   php artisan serve
   ```
