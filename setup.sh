#!/bin/bash

echo "🚀 Setting up TMS Application..."
echo ""

# Check if Docker is running
if ! docker info > /dev/null 2>&1; then
    echo "❌ Docker is not running. Please start Docker first."
    exit 1
fi

echo "📦 Starting Docker containers..."
docker-compose up -d --build

echo ""
echo "⏳ Waiting for containers to be ready..."
sleep 5

echo ""
echo "📝 Setting up Laravel API..."

# Copy .env file if it doesn't exist
echo "  - Copying .env.example to .env..."
docker-compose exec -T app cp .env.example .env 2>/dev/null || echo "    .env already exists"

# Install Composer dependencies
echo "  - Installing Composer dependencies..."
docker-compose exec -T app composer install --no-interaction

# Generate application key
echo "  - Generating application key..."
docker-compose exec -T app php artisan key:generate --force

# Run migrations and seeders
echo "  - Running database migrations and seeders..."
docker-compose exec -T app php artisan migrate --seed --force

echo ""
echo "📦 Setting up Frontend..."

# Install npm dependencies
echo "  - Installing npm dependencies..."
docker-compose exec -T frontend npm install

echo ""
echo "✅ Setup complete!"
echo ""
echo "🌐 Access the application:"
echo "   Frontend: http://localhost:3000"
echo "   API: http://localhost:8080/api"
echo ""
echo "📋 To check logs:"
echo "   docker-compose logs -f"
echo ""

