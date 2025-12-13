#!/bin/bash
set -e

DROPLET_IP="165.227.113.197"
APP_NAME="mentor-lms"
SSH_KEY="$HOME/.ssh/dunyawii_key"

echo "🚀 Deploying Carter LMS to Digital Ocean Droplet: $DROPLET_IP"

# Wait for droplet to be fully ready
echo "⏳ Waiting for droplet to be ready..."
sleep 30

# Copy application files to droplet
echo "📦 Copying application files..."
ssh -i $SSH_KEY -o StrictHostKeyChecking=no root@$DROPLET_IP "mkdir -p /opt/$APP_NAME"
rsync -avz -e "ssh -i $SSH_KEY" --exclude 'node_modules' --exclude 'vendor' --exclude '.git' \
  ./ root@$DROPLET_IP:/opt/$APP_NAME/

# Setup and run application on droplet
echo "🔧 Setting up application on droplet..."
ssh -i $SSH_KEY root@$DROPLET_IP << 'ENDSSH'
cd /opt/mentor-lms

# Install docker-compose if not present
if ! command -v docker-compose &> /dev/null; then
    curl -L "https://github.com/docker/compose/releases/download/v2.24.0/docker-compose-$(uname -s)-$(uname -m)" -o /usr/local/bin/docker-compose
    chmod +x /usr/local/bin/docker-compose
fi

# Create docker-compose.yml
cat > docker-compose.yml << 'EOF'
version: '3.8'

services:
  app:
    build: .
    container_name: mentor-lms-app
    ports:
      - "80:80"
    environment:
      - DB_HOST=db
      - DB_PORT=3306
      - DB_DATABASE=mentor_lms
      - DB_USERNAME=mentor_user
      - DB_PASSWORD=SecurePassword123!
      - REDIS_HOST=redis
      - REDIS_PORT=6379
      - APP_ENV=production
      - APP_DEBUG=false
    depends_on:
      - db
      - redis
    restart: unless-stopped
    volumes:
      - ./storage:/var/www/html/storage

  db:
    image: mysql:8.0
    container_name: mentor-lms-db
    environment:
      - MYSQL_ROOT_PASSWORD=RootPassword123!
      - MYSQL_DATABASE=mentor_lms
      - MYSQL_USER=mentor_user
      - MYSQL_PASSWORD=SecurePassword123!
    volumes:
      - db_data:/var/lib/mysql
    restart: unless-stopped

  redis:
    image: redis:7-alpine
    container_name: mentor-lms-redis
    restart: unless-stopped
    volumes:
      - redis_data:/data

volumes:
  db_data:
  redis_data:
EOF

# Build and start containers
echo "🏗️  Building Docker image..."
docker-compose build

echo "🚀 Starting containers..."
docker-compose up -d

# Wait for services to be ready
echo "⏳ Waiting for services to start..."
sleep 20

# Run Laravel setup commands
echo "⚙️  Running Laravel setup..."
docker-compose exec -T app php artisan migrate --force || echo "Migration may need manual setup"
docker-compose exec -T app php artisan config:cache
docker-compose exec -T app php artisan route:cache
docker-compose exec -T app php artisan view:cache

echo "✅ Deployment complete!"
echo "🌐 Your application is available at: http://165.227.113.197"
echo ""
echo "📝 Next steps:"
echo "   1. Access the installer at: http://165.227.113.197"
echo "   2. Configure your domain name (optional)"
echo "   3. Update DNS A record to point to: 165.227.113.197"
echo ""
echo "🔧 Useful commands:"
echo "   - View logs: docker-compose logs -f"
echo "   - Restart: docker-compose restart"
echo "   - Stop: docker-compose down"
ENDSSH

echo ""
echo "✨ Deployment completed successfully!"
echo "🌐 Access your application at: http://$DROPLET_IP"
