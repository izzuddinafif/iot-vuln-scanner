#!/bin/bash

log() {
    echo "$(date +"%Y-%m-%d %H:%M:%S") - $1" | tee -a deployment.log
}

# Ensure dependencies are installed
if [ ! -d "venv" ]; then
    python3 -m venv venv
fi
source venv/bin/activate
log "Installing Python dependencies..."
pip install -r python/requirements.txt

# Check if the user provided an IP range
if [ -z "$1" ]; then
  log "No IP range specified. Using default: 192.168.217.0/24"
  IP_RANGE="192.168.217.0/24"
else
  IP_RANGE=$1
fi

# Run the Python scanner service
log "Starting Python scanner service..."
nohup python3 python/scanner_service.py > python.log 2>&1 &
PYTHON_PID=$!
log "Python scanner service PID: $PYTHON_PID"

# Allow the Python server to start up
sleep 3

# Run the Go server with the specified IP range as the target
log "Starting Go server with target IP range: $IP_RANGE"
nohup go run go/app/server.go -target="$IP_RANGE" > go.log 2>&1 &
GO_PID=$!
log "Go server PID: $GO_PID"

# Set up Laravel
log "Setting up Laravel..."
cd laravel
if [ ! -f ".env" ]; then
    cp .env.example .env
    php artisan key:generate
fi

# Set database file permissions
sudo touch database/database.sqlite
sudo chmod 666 database/database.sqlite

# Install Laravel dependencies and run migrations
log "Installing Laravel dependencies..."
composer install
php artisan migrate --force

# Start Laravel server
log "Starting Laravel server on port 80..."
sudo nohup php artisan serve --host=0.0.0.0 --port=80 > laravel.log 2>&1 &
LARAVEL_PID=$!
log "Laravel server PID: $LARAVEL_PID"

# Summary of PIDs
log "All services started:"
log "Python scanner service PID: $PYTHON_PID"
log "Go server PID: $GO_PID"
log "Laravel server PID: $LARAVEL_PID"

cd ..
log "All services started:" > pid.txt
log "Python scanner service PID: $PYTHON_PID" >> pid.txt
log "Go server PID: $GO_PID" >> pid.txt
log "Laravel server PID: $LARAVEL_PID" >> pid.txt
