#!/bin/bash

# SpotSeeker Backend Server Startup Script
# This script starts the Laravel development server using PHP 8.3

echo "🚀 Starting SpotSeeker Backend API Server..."
echo "📍 Server will be available at: http://localhost:8000"
echo "📍 API Base URL: http://localhost:8000/api"
echo "🔍 Health Check: http://localhost:8000/api/healthcheck"
echo ""
echo "📝 Use api_test.http file with REST Client extension to test the API endpoints"
echo ""
echo "⚠️  Press Ctrl+C to stop the server"
echo ""

# Start the Laravel server using PHP 8.3
/usr/bin/php8.3 artisan serve --host=0.0.0.0 --port=8000
