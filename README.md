# TMS - Customer and Contact Management System

A full-stack application for managing customers and their contacts, built with Laravel 11 API and Vue.js frontend, running in Docker containers.

## Project Overview

This project demonstrates:
- Translation of project brief into functional demonstration
- Best practices in coding approach and comments
- Proficiency with Laravel, Vue.js, and MySQL
- Docker containerization for development environment

## Technology Stack

- **Backend**: Laravel 11 (PHP 8.2)
- **Frontend**: Vue.js 3 with Vite
- **Database**: MySQL 8.0
- **Web Server**: Nginx
- **Containerization**: Docker & Docker Compose

## Project Structure

```
TMS-test/
├── api/                    # Laravel API backend
│   ├── app/
│   │   ├── Http/
│   │   │   └── Controllers/Api/
│   │   └── Models/
│   ├── database/
│   │   ├── migrations/
│   │   └── seeders/
│   └── routes/
├── frontend/              # Vue.js frontend
│   ├── src/
│   │   ├── components/
│   │   ├── views/
│   │   ├── services/
│   │   └── router/
│   └── package.json
├── docker/                # Docker configuration files
│   └── nginx/
├── docker-compose.yml     # Docker Compose configuration
└── README.md
```

## Features

### Customers Management
- List all customers with search and filter functionality
- Create new customers
- Edit existing customers
- Delete customers (with confirmation)
- View customer details including associated contacts

### Contacts Management
- List contacts for each customer
- Create new contacts
- Edit existing contacts
- Delete contacts (with confirmation)
- Contacts are nested under customers

### Customer Categories
- Pre-seeded categories: Gold, Silver, Bronze
- Dropdown selection in customer forms

## Installation & Setup

### Prerequisites
- Docker and Docker Compose installed on your system
- Git (for cloning the repository)

### Step 1: Clone the Repository
```bash
git clone git@github.com:maicon0902/TMS-tenant.git
cd TMS-tenant
```

### Step 2: Run Setup Script (Recommended)

Từ thư mục gốc của project, chạy script setup tự động:

```bash
./setup.sh
```

Script này sẽ tự động:
- Khởi động Docker containers
- Copy file .env
- Cài đặt Composer dependencies
- Generate application key
- Chạy migrations và seeders
- Cài đặt npm dependencies

### Hoặc Setup Thủ Công:

#### Step 2a: Start Docker Containers

From the project root directory:

```bash
docker-compose up -d --build
```

This will:
- Build and start the Laravel API container
- Start the MySQL database container
- Start the Nginx web server
- Start the Vue.js frontend development server

#### Step 2b: Setup Laravel API inside Docker

1. Copy environment file:
```bash
docker-compose exec app cp .env.example .env
```

2. Install PHP dependencies (QUAN TRỌNG - phải chạy lệnh này):
```bash
docker-compose exec app composer install
```

3. Generate application key:
```bash
docker-compose exec app php artisan key:generate
```

#### Step 2c: Setup Frontend inside Docker

Install Node.js dependencies:
```bash
docker-compose exec frontend npm install
```

#### Step 2d: Run Database Migrations and Seeders

```bash
docker-compose exec app php artisan migrate --seed
```

This will:
- Create all necessary database tables
- Seed customer categories (Gold, Silver, Bronze)

### Step 3: Access the Application

- **Frontend**: http://localhost:3000
- **API**: http://localhost:8080/api
- **Database**: localhost:3306 (username: root, password: root, database: tms_db)

## Development

### Running Migrations
```bash
docker-compose exec app php artisan migrate
```

### Running Seeders
```bash
docker-compose exec app php artisan db:seed
```

### Viewing Logs
```bash
# All services
docker-compose logs -f

# Specific service
docker-compose logs -f app
docker-compose logs -f frontend
```

### Stopping Containers
```bash
docker-compose down
```

### Rebuilding Containers
```bash
docker-compose up -d --build
```

## API Endpoints

### Customer Categories
- `GET /api/customer-categories` - Get all categories

### Customers
- `GET /api/customers` - List all customers (supports `search` and `category` query parameters)
- `GET /api/customers/{id}` - Get customer details
- `POST /api/customers` - Create a new customer
- `PUT /api/customers/{id}` - Update a customer
- `DELETE /api/customers/{id}` - Delete a customer

### Contacts
- `GET /api/customers/{customerId}/contacts` - Get all contacts for a customer
- `POST /api/customers/{customerId}/contacts` - Create a new contact
- `PUT /api/contacts/{id}` - Update a contact
- `DELETE /api/contacts/{id}` - Delete a contact

## Database Schema

### customer_categories
- `id` (primary key)
- `name` (unique)
- `timestamps`

### customers
- `id` (primary key)
- `name`
- `reference` (unique)
- `customer_category_id` (foreign key, nullable)
- `start_date` (date, nullable)
- `description` (text, nullable)
- `timestamps`

### contacts
- `id` (primary key)
- `customer_id` (foreign key)
- `first_name`
- `last_name` (nullable)
- `timestamps`

## Notes

- Customer categories are pre-seeded and do not require full CRUD operations
- Each contact belongs to a customer (one-to-many relationship)
- First name is required for contacts
- The application uses modals for create/edit operations
- Delete operations require confirmation

## Troubleshooting

### Port Conflicts
If ports 3000, 3306, or 8080 are already in use, modify the port mappings in `docker-compose.yml`.

### Database Connection Issues
Ensure the database container is running and check the `.env` file in the `api` directory for correct database credentials.

### Permission Issues
If you encounter permission issues, you may need to adjust file permissions:
```bash
sudo chown -R $USER:$USER api/
```

## License

This project is created for demonstration purposes.

