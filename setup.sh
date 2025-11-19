#!/bin/bash

echo "🚀 Setting up TMS Application..."
echo ""

# Check if Docker is running
if ! docker info > /dev/null 2>&1; then
    echo "❌ Docker is not running. Please start Docker first."
    exit 1
fi

# Function to get PID using a port
get_port_pid() {
    local port=$1
    local pid=""
    
    # Try lsof first (common on macOS and Linux)
    if command -v lsof >/dev/null 2>&1; then
        pid=$(lsof -ti:$port 2>/dev/null)
    fi
    
    # Try fuser if lsof didn't work (common on Linux)
    if [ -z "$pid" ] && command -v fuser >/dev/null 2>&1; then
        pid=$(fuser $port/tcp 2>/dev/null | awk '{print $1}')
    fi
    
    # Try ss/netstat as fallback
    if [ -z "$pid" ] && command -v ss >/dev/null 2>&1; then
        pid=$(ss -lptn "sport = :$port" 2>/dev/null | grep -oP 'pid=\K\d+' | head -1)
    fi
    
    echo "$pid"
}

# Function to kill process using a port
kill_port() {
    local port=$1
    local pid=$(get_port_pid $port)
    
    if [ ! -z "$pid" ]; then
        echo "  ⚠️  Port $port is in use by PID $pid, killing process..."
        # Try normal kill first
        if kill -9 $pid 2>/dev/null; then
            sleep 1
        # Try with sudo if normal kill failed
        elif sudo kill -9 $pid 2>/dev/null; then
            sleep 1
        else
            echo "  ❌ Cannot kill process $pid. You may need to run with sudo or stop it manually."
            return 1
        fi
        
        # Verify port is free
        local new_pid=$(get_port_pid $port)
        if [ ! -z "$new_pid" ]; then
            echo "  ❌ Failed to free port $port. Process $new_pid is still using it."
            return 1
        else
            echo "  ✅ Port $port is now free"
        fi
    fi
    return 0
}

# Stop existing Docker containers that might be using these ports
echo "🛑 Stopping existing Docker containers..."
docker compose down 2>/dev/null || true

# Check and free required ports
echo "🔍 Checking required ports (3000, 8080, 3306)..."
PORTS=(3000 8080 3306)
PORT_NAMES=("Frontend" "API/Nginx" "MySQL")
PORT_FREE=true

for i in "${!PORTS[@]}"; do
    port=${PORTS[$i]}
    name=${PORT_NAMES[$i]}
    pid=$(get_port_pid $port)
    
    if [ ! -z "$pid" ]; then
        echo "  ⚠️  Port $port ($name) is in use by PID $pid"
        if ! kill_port $port; then
            PORT_FREE=false
        fi
    else
        echo "  ✅ Port $port ($name) is free"
    fi
done

if [ "$PORT_FREE" = false ]; then
    echo ""
    echo "❌ Some ports are still in use. Please stop them manually and try again."
    exit 1
fi

echo ""
echo "📦 Starting Docker containers..."
docker compose up -d --build

echo ""
echo "⏳ Waiting for containers to be ready..."
sleep 5

echo ""
echo "📝 Setting up Laravel API..."

# Copy .env file if it doesn't exist
echo "  - Copying .env.example to .env..."
docker compose exec -T app cp .env.example .env 2>/dev/null || echo "    .env already exists"

# Create necessary Laravel directories and set permissions (as root)
echo "  - Creating Laravel directories and setting permissions..."
docker compose exec -T -u root app mkdir -p /var/www/html/bootstrap/cache
docker compose exec -T -u root app mkdir -p /var/www/html/storage/framework/{sessions,views,cache}
docker compose exec -T -u root app mkdir -p /var/www/html/storage/logs
docker compose exec -T -u root app chown -R www-data:www-data /var/www/html
docker compose exec -T -u root app chmod -R 775 /var/www/html/bootstrap/cache
docker compose exec -T -u root app chmod -R 775 /var/www/html/storage

# Install Composer dependencies (as root to avoid permission issues)
echo "  - Installing Composer dependencies..."
docker compose exec -T -u root app composer install --no-interaction
docker compose exec -T -u root app chown -R www-data:www-data /var/www/html

# Generate application key
echo "  - Generating application key..."
docker compose exec -T app php artisan key:generate --force

# Run migrations and seeders
echo "  - Running database migrations and seeders..."
docker compose exec -T app php artisan migrate --seed --force

echo ""
echo "📦 Setting up Frontend..."

# Wait for frontend container to be running (not restarting)
echo "  - Waiting for frontend container to be ready..."
max_attempts=30
attempt=0
while [ $attempt -lt $max_attempts ]; do
    if docker compose ps frontend | grep -q "Up"; then
        sleep 2
        if docker compose exec -T frontend sh -c "test -f /app/package.json" 2>/dev/null; then
            break
        fi
    fi
    attempt=$((attempt + 1))
    sleep 1
done

# Install npm dependencies (remove node_modules and package-lock.json first to fix Rollup issue)
echo "  - Installing npm dependencies..."
# Stop the container to prevent restart loop during install
docker compose stop frontend 2>/dev/null || true
# Wait a moment for container to fully stop
sleep 2
# Clean install dependencies - use the service with proper volume mounts
docker compose run --rm --no-deps frontend sh -c "cd /app && rm -rf node_modules package-lock.json && npm install --force"
# Restart the container
echo "  - Starting frontend container..."
docker compose up -d frontend

echo ""
echo "✅ Setup complete!"
echo ""
echo "🌐 Access the application:"
echo "   Frontend: http://localhost:3000"
echo "   API: http://localhost:8080/api"
echo ""
echo "📋 To check logs:"
echo "   docker compose logs -f"
echo ""

