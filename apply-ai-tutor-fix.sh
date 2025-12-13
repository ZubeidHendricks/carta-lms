#!/bin/bash

DROPLET_IP="165.227.113.197"
SSH_KEY="$HOME/.ssh/dunyawii_key"

echo "🔧 Applying AI Tutor fix to server..."

# Create directory and copy the new controller
echo "📦 Creating directory and copying AILecturerController..."
ssh -i $SSH_KEY root@$DROPLET_IP "mkdir -p /opt/mentor-lms/app/Http/Controllers/AI"
scp -i $SSH_KEY app/Http/Controllers/AI/AILecturerController.php root@$DROPLET_IP:/opt/mentor-lms/app/Http/Controllers/AI/

# Copy updated routes
echo "📦 Copying routes/web.php..."
scp -i $SSH_KEY routes/web.php root@$DROPLET_IP:/opt/mentor-lms/routes/

# Clear caches on server
echo "🧹 Clearing caches on server..."
ssh -i $SSH_KEY root@$DROPLET_IP << 'ENDSSH'
cd /opt/mentor-lms
docker-compose exec -T app php artisan route:clear
docker-compose exec -T app php artisan config:clear
docker-compose exec -T app php artisan cache:clear
docker-compose exec -T app php artisan optimize:clear
echo "✅ Caches cleared"
ENDSSH

echo ""
echo "✅ AI Tutor fix applied successfully!"
echo "🌐 Test at: http://$DROPLET_IP/student/ai-tutor"
echo ""
echo "📝 Note: Login required. The chat will now work without 404 errors."
