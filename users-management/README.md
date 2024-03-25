# User management

## 🚀 Overview

User management is a web application to easy manage new users and permissions in the cloud.

## 💻 Technologies Used

-   **Backend:** Laravel
-   **Frontend:** Laravel with Filament
-   **DB:** Postgresql

## ✨ Features

-   **User Authentication:** Secure user authentication with azure authentication.
-   **Request CRUD:** Create, read, update and delete of request to a new user.
-   **Sending message:** Send message to admin about a new request, Send message for the requester about updating the request status.
-   **Create user:** Create a new user in the cloud.
-   **Responsive Design:** A seamless experience across various devices.

## 🔧 Installation

### Clone

```bash
# Install Dependencies
composer install

# Set up environment variables
cp .env.example .env

# Update the .env file with your configuration

# Generate APP_KEY variable
php artisan key:generate

# Run Migrations and Seed the Database
php artisan migrate --seed

```

### Every pull

```bash
# Install Dependencies
composer install

# Run Migrations and Seed the Database
php artisan migrate --seed

```

Your app will be accessible at http://localhost:8000.
