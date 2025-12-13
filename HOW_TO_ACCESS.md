# 🎯 How to Access Your Database and Run Migration

## Your Setup
- **Database**: DigitalOcean Managed PostgreSQL
- **Cluster ID**: 2052d40a-892f-41a7-a69a-4d6e4f39516c
- **Host**: aihr-postgres-do-user-23094081-0.g.db.ondigitalocean.com
- **Port**: 25060
- **Database Name**: defaultdb
- **User**: doadmin

## Option 1: Get Password from DigitalOcean Dashboard

1. Go to https://cloud.digitalocean.com/databases
2. Click on your database: **aihr-postgres**
3. Click "Connection Details"
4. Click "Show" next to the password field
5. Copy the password

## Option 2: Reset Database Password

```bash
doctl databases user reset 2052d40a-892f-41a7-a69a-4d6e4f39516c doadmin
```

This will show you a new password.

## Running the Migration

### Step 1: Get the password (use one of the options above)

### Step 2: Run the migration script

```bash
./tavus_migration_do.sh
```

When prompted, paste the password.

## Manual Method (if script doesn't work)

```bash
# Get password from DigitalOcean dashboard first, then:
psql -h aihr-postgres-do-user-23094081-0.g.db.ondigitalocean.com \
     -p 25060 \
     -U doadmin \
     -d defaultdb \
     < tavus_setup_postgres.sql
```

## What Gets Created

5 PostgreSQL tables:
1. **tavus_replicas** - Digital instructor replicas
2. **tavus_videos** - AI-generated videos
3. **tavus_conversations** - Interactive conversations
4. **tavus_personas** - AI personalities
5. **tavus_webhooks** - Event tracking

## Files Ready

- ✅ **tavus_migration_do.sh** - Automated script
- ✅ **tavus_setup_postgres.sql** - PostgreSQL migration
- ✅ **HOW_TO_ACCESS.md** (this file)

## Verification

After migration runs, check tables:

```bash
psql -h aihr-postgres-do-user-23094081-0.g.db.ondigitalocean.com \
     -p 25060 \
     -U doadmin \
     -d defaultdb \
     -c "\dt tavus_*"
```

You should see all 5 tables listed.

## Need Help?

If you can't find the password, reset it:
```bash
doctl databases user reset 2052d40a-892f-41a7-a69a-4d6e4f39516c doadmin
```

The output will show your new password.
