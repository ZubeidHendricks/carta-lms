#!/bin/bash
export PLAYWRIGHT_SKIP_BROWSER_DOWNLOAD=0
npm install
npx playwright install chromium --force
