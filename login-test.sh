#!/bin/bash

# Get CSRF token from login page
echo "Getting login page..."
CSRF=$(curl -s -c /tmp/cookies.txt "http://165.227.113.197/login" | grep -oP 'name="csrf-token" content="\K[^"]+')
echo "CSRF Token: $CSRF"

# Login
echo "Logging in..."
curl -s -b /tmp/cookies.txt -c /tmp/cookies.txt \
  -H "Content-Type: application/x-www-form-urlencoded" \
  -H "X-CSRF-TOKEN: $CSRF" \
  -d "email=sarah@carta.com&password=password&_token=$CSRF" \
  "http://165.227.113.197/login" -L -o /tmp/login-response.html

echo "Login response saved"

# Access AI Tutor page
echo "Accessing AI Tutor page..."
curl -s -b /tmp/cookies.txt "http://165.227.113.197/student/ai-tutor" -o /tmp/ai-tutor-authenticated.html

echo "Page size: $(wc -c < /tmp/ai-tutor-authenticated.html) bytes"
head -100 /tmp/ai-tutor-authenticated.html
