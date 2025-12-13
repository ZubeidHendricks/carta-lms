#!/bin/bash

echo "========================================="
echo "   Tavus Migration - Direct Execution"
echo "========================================="
echo ""

# Try to get database credentials from .env
if [ -f .env ]; then
    echo "Reading database credentials from .env..."
    DB_HOST=$(grep "^DB_HOST=" .env | cut -d '=' -f2)
    DB_PORT=$(grep "^DB_PORT=" .env | cut -d '=' -f2)
    DB_NAME=$(grep "^DB_DATABASE=" .env | cut -d '=' -f2)
    DB_USER=$(grep "^DB_USERNAME=" .env | cut -d '=' -f2)
    DB_PASS=$(grep "^DB_PASSWORD=" .env | cut -d '=' -f2)
else
    echo ".env file not found!"
fi

echo ""
echo "Please provide database credentials:"
echo "-----------------------------------"
read -p "Database Host [$DB_HOST]: " input_host
DB_HOST=${input_host:-$DB_HOST}

read -p "Database Port [$DB_PORT]: " input_port
DB_PORT=${input_port:-$DB_PORT}

read -p "Database Name [$DB_NAME]: " input_name
DB_NAME=${input_name:-$DB_NAME}

read -p "Database User [$DB_USER]: " input_user
DB_USER=${input_user:-$DB_USER}

read -sp "Database Password: " DB_PASS
echo ""
echo ""

# Test connection
echo "Testing database connection..."
mysql -h "$DB_HOST" -P "$DB_PORT" -u "$DB_USER" -p"$DB_PASS" "$DB_NAME" -e "SELECT 1;" 2>/dev/null

if [ $? -eq 0 ]; then
    echo "✓ Connection successful!"
    echo ""
    echo "Running Tavus migration..."
    mysql -h "$DB_HOST" -P "$DB_PORT" -u "$DB_USER" -p"$DB_PASS" "$DB_NAME" < tavus_setup.sql
    
    if [ $? -eq 0 ]; then
        echo ""
        echo "========================================="
        echo "✓ MIGRATION COMPLETED SUCCESSFULLY!"
        echo "========================================="
        echo ""
        echo "Tables created:"
        mysql -h "$DB_HOST" -P "$DB_PORT" -u "$DB_USER" -p"$DB_PASS" "$DB_NAME" -e "SHOW TABLES LIKE 'tavus_%';"
    else
        echo ""
        echo "✗ Migration failed! Check errors above."
    fi
else
    echo "✗ Cannot connect to database. Please check credentials."
    echo ""
    echo "Alternative: Run this command manually:"
    echo "mysql -h $DB_HOST -P $DB_PORT -u $DB_USER -p $DB_NAME < tavus_setup.sql"
fi
