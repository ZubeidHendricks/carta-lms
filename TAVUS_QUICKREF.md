# Tavus Quick Reference Card

## Installation Checklist
- [x] Module created in `Modules/Tavus/`
- [x] 25 files created (Controllers, Models, Services, etc.)
- [x] 4 database migrations ready
- [x] 2 React components (TypeScript)
- [x] API routes configured
- [x] Module enabled in `modules_statuses.json`
- [x] Environment variables added to `.env.example`
- [x] Documentation complete

## To-Do Before Use
- [ ] Get Tavus API key from https://platform.tavus.io/
- [ ] Add `TAVUS_API_KEY=your_key` to `.env`
- [ ] Run `php artisan migrate`
- [ ] Run `php artisan config:clear`
- [ ] Test with first replica creation

## Quick API Reference

### Create Replica
```bash
curl -X POST https://yourapp.com/api/tavus/replicas \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "replica_name": "Dr. Smith",
    "train_video_url": "https://example.com/video.mp4"
  }'
```

### Generate Video
```bash
curl -X POST https://yourapp.com/api/tavus/videos \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "video_name": "Welcome",
    "replica_id": "r1234567890",
    "script": "Hi {{name}}!",
    "variables": {"name": "John"}
  }'
```

### Start Conversation
```bash
curl -X POST https://yourapp.com/api/tavus/conversations \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "replica_id": "r1234567890",
    "custom_greeting": "Hello!"
  }'
```

## React Component Usage

### Video Player
```tsx
import TavusVideoPlayer from '@/Modules/Tavus/Components/TavusVideoPlayer';

<TavusVideoPlayer 
  videoId={123}
  onReady={(url) => console.log(url)}
/>
```

### Conversation
```tsx
import TavusConversation from '@/Modules/Tavus/Components/TavusConversation';

<TavusConversation 
  replicaId="r1234567890"
  onEnd={() => console.log('Done')}
/>
```

## Environment Variables
```env
TAVUS_API_KEY=                    # Required - Your API key
TAVUS_API_URL=https://tavusapi.com
TAVUS_STORE_VIDEOS_LOCALLY=true
TAVUS_VIDEO_STORAGE_DISK=public
```

## File Locations
- **Config:** `config/tavus.php`
- **Routes:** `Modules/Tavus/routes/api.php`
- **Controllers:** `Modules/Tavus/app/Http/Controllers/`
- **Models:** `Modules/Tavus/app/Models/`
- **Services:** `Modules/Tavus/app/Services/`
- **Migrations:** `Modules/Tavus/database/migrations/`
- **Components:** `Modules/Tavus/resources/js/Components/`

## Documentation
- **Quick Start:** `TAVUS_INTEGRATION.md`
- **Full Docs:** `Modules/Tavus/README.md`
- **Integration:** `Modules/Tavus/INTEGRATION_GUIDE.md`
- **Summary:** `TAVUS_SUMMARY.md`

## Common Commands
```bash
# List modules
php artisan module:list

# Run migrations
php artisan migrate

# Clear cache
php artisan config:clear
php artisan cache:clear

# Check logs
tail -f storage/logs/laravel.log
```

## Support
- Tavus Docs: https://docs.tavus.io/
- API Reference: https://docs.tavus.io/api-reference/
- Platform: https://platform.tavus.io/
