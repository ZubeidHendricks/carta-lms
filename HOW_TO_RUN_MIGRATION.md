# How to Run Tavus Database Migration

MySQL is available on your system. Here are your options:

## Option 1: Using MySQL Command (Recommended)

If you know your database credentials, run:

```bash
mysql -u YOUR_USERNAME -p YOUR_DATABASE < tavus_setup.sql
```

You'll be prompted for the password.

## Option 2: Interactive MySQL

```bash
mysql -u YOUR_USERNAME -p
# Enter password when prompted
# Then run:
USE your_database_name;
SOURCE tavus_setup.sql;
EXIT;
```

## Option 3: Update .env and Use Laravel

1. Update your `.env` file with database credentials:
```env
DB_DATABASE=your_database_name
DB_USERNAME=your_username
DB_PASSWORD=your_password
```

2. Find PHP and run:
```bash
# If PHP is available
php artisan migrate

# Or if using specific PHP version
php8.2 artisan migrate
```

## Option 4: Using phpMyAdmin

1. Open phpMyAdmin in your browser
2. Select your database
3. Go to "Import" tab
4. Choose file: `tavus_setup.sql`
5. Click "Go"

## Option 5: Manual Table Creation

Copy the SQL from `tavus_setup.sql` and paste it directly into your MySQL client or phpMyAdmin SQL tab.

## Tables That Will Be Created

1. **tavus_replicas** - Instructor digital replicas
   - Stores replica ID, name, training status
   
2. **tavus_videos** - Generated AI videos
   - Stores video ID, URLs, metadata
   
3. **tavus_conversations** - Interactive conversations
   - Stores conversation sessions and duration
   
4. **tavus_webhooks** - Event tracking
   - Stores webhook events from Tavus

## After Migration

Once the migration is complete, run:

```bash
# Clear Laravel cache (if PHP available)
php artisan config:clear
php artisan cache:clear

# Or restart your web server
sudo systemctl restart apache2
# or
sudo systemctl restart nginx
```

## Verify Tables Were Created

```bash
mysql -u YOUR_USERNAME -p YOUR_DATABASE -e "SHOW TABLES LIKE 'tavus_%';"
```

You should see 4 tables:
- tavus_conversations
- tavus_replicas
- tavus_videos
- tavus_webhooks

## Need Help?

The SQL file is located at: `tavus_setup.sql`
You can open it in any text editor to see the table structure.
