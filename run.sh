#!/bin/bash

# Ensure dependencies are installed
echo "Installing Python dependencies..."
pip install -r python/requirements.txt

# Check if the user provided an IP range
if [ -z "$1" ]; then
  echo "No IP range specified. Using default: 192.168.217.0/24"
  IP_RANGE="192.168.217.0/24"
else
  IP_RANGE=$1
fi

# Run the Python scanner service
echo "Starting Python scanner service..."
python3 python/scanner_service.py &

# Store the PID of the Python service so we can terminate it later
PYTHON_PID=$!

# Allow the Python server to start up
sleep 2

# Run the Go server with the specified IP range as the target
echo "Starting Go server with target IP range: $IP_RANGE"
go run go/app/server.go -target="$IP_RANGE"

# Kill the Python service after Go server stops
echo "Stopping Python scanner service..."
kill $PYTHON_PID
