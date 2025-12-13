#!/bin/bash

DROPLET_IP="165.227.113.197"
SSH_KEY="$HOME/.ssh/dunyawii_key"

echo "🚀 Deploying frontend assets to server..."

# Copy built assets
echo "📦 Copying build directory..."
rsync -avz -e "ssh -i $SSH_KEY" --delete public/build/ root@$DROPLET_IP:/opt/mentor-lms/public/build/

# Copy into Docker container
echo "📦 Copying into Docker container..."
ssh -i $SSH_KEY root@$DROPLET_IP << 'ENDSSH'
cd /opt/mentor-lms
docker cp public/build mentor-lms-app:/var/www/html/public/
docker-compose exec -T app chown -R www-data:www-data /var/www/html/public/build
docker-compose exec -T app php artisan view:clear
docker-compose exec -T app php artisan cache:clear
echo "✅ Frontend assets deployed"
ENDSSH

echo "✅ Deployment complete! Tavus lesson type should now be visible."
