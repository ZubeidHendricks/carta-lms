#!/bin/bash

echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
echo "  🎬 Tavus Migration for Mentor LMS (Carta)"
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
echo ""
echo "This will create 5 tables for Tavus AI Video integration."
echo ""

# Ask user for database type
echo "Which database are you using?"
echo "1) MySQL/MariaDB"
echo "2) PostgreSQL"
read -p "Enter choice (1 or 2): " DB_TYPE

if [ "$DB_TYPE" = "2" ]; then
    SQL_FILE="tavus_setup_postgres.sql"
    echo "Using PostgreSQL migration..."
else
    SQL_FILE="tavus_setup.sql"
    echo "Using MySQL migration..."
fi

echo ""
echo "Enter your database connection details:"
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
read -p "Database Host [localhost]: " DB_HOST
DB_HOST=${DB_HOST:-localhost}

read -p "Database Port [3306 for MySQL, 5432 for PostgreSQL]: " DB_PORT
if [ "$DB_TYPE" = "2" ]; then
    DB_PORT=${DB_PORT:-5432}
else
    DB_PORT=${DB_PORT:-3306}
fi

read -p "Database Name: " DB_NAME
read -p "Database Username: " DB_USER
read -sp "Database Password: " DB_PASS
echo ""
echo ""

echo "Testing connection..."

if [ "$DB_TYPE" = "2" ]; then
    # PostgreSQL
    PGPASSWORD="$DB_PASS" psql -h "$DB_HOST" -p "$DB_PORT" -U "$DB_USER" -d "$DB_NAME" -c "SELECT 1;" > /dev/null 2>&1
    if [ $? -eq 0 ]; then
        echo "✓ Connection successful!"
        echo ""
        echo "Running migration..."
        PGPASSWORD="$DB_PASS" psql -h "$DB_HOST" -p "$DB_PORT" -U "$DB_USER" -d "$DB_NAME" < "$SQL_FILE"
        
        if [ $? -eq 0 ]; then
            echo ""
            echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
            echo "✅ MIGRATION COMPLETED SUCCESSFULLY!"
            echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
            echo ""
            echo "Verifying tables..."
            PGPASSWORD="$DB_PASS" psql -h "$DB_HOST" -p "$DB_PORT" -U "$DB_USER" -d "$DB_NAME" -c "\dt tavus_*"
        else
            echo "✗ Migration failed. Check errors above."
        fi
    else
        echo "✗ Cannot connect to PostgreSQL database."
    fi
else
    # MySQL
    mysql -h "$DB_HOST" -P "$DB_PORT" -u "$DB_USER" -p"$DB_PASS" "$DB_NAME" -e "SELECT 1;" > /dev/null 2>&1
    if [ $? -eq 0 ]; then
        echo "✓ Connection successful!"
        echo ""
        echo "Running migration..."
        mysql -h "$DB_HOST" -P "$DB_PORT" -u "$DB_USER" -p"$DB_PASS" "$DB_NAME" < "$SQL_FILE"
        
        if [ $? -eq 0 ]; then
            echo ""
            echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
            echo "✅ MIGRATION COMPLETED SUCCESSFULLY!"
            echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
            echo ""
            echo "Verifying tables..."
            mysql -h "$DB_HOST" -P "$DB_PORT" -u "$DB_USER" -p"$DB_PASS" "$DB_NAME" -e "SHOW TABLES LIKE 'tavus_%';"
        else
            echo "✗ Migration failed. Check errors above."
        fi
    else
        echo "✗ Cannot connect to MySQL database."
    fi
fi

echo ""
echo "Tables created:"
echo "  • tavus_replicas"
echo "  • tavus_videos"
echo "  • tavus_conversations"
echo "  • tavus_personas"
echo "  • tavus_webhooks"
echo ""
echo "✅ Tavus is ready to use!"
