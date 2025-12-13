import { chromium } from '@playwright/test';

(async () => {
  const browser = await chromium.launch({ headless: true });
  const context = await browser.newContext({
    viewport: { width: 1920, height: 1080 }
  });
  const page = await context.newPage();
  
  console.log('Navigating to login page...');
  await page.goto('http://165.227.113.197/login', { 
    waitUntil: 'networkidle',
    timeout: 30000 
  });
  
  console.log('Filling login form...');
  await page.fill('input[type="email"], input[name="email"]', 'sarah@carta.com');
  await page.fill('input[type="password"], input[name="password"]', 'password');
  
  console.log('Submitting login...');
  await page.click('button[type="submit"]');
  await page.waitForLoadState('networkidle');
  
  console.log('Current URL after login:', page.url());
  
  console.log('\nNavigating to AI Tutor page...');
  await page.goto('http://165.227.113.197/student/ai-tutor', { 
    waitUntil: 'networkidle',
    timeout: 30000 
  });
  
  console.log('Page loaded. Title:', await page.title());
  console.log('Current URL:', page.url());
  
  // Wait a bit for any dynamic content
  await page.waitForTimeout(3000);
  
  // Take screenshot
  await page.screenshot({ 
    path: 'ai-tutor-logged-in.png',
    fullPage: true 
  });
  console.log('\nScreenshot saved to ai-tutor-logged-in.png');
  
  // Get page content
  const bodyText = await page.evaluate(() => {
    return document.body.innerText;
  });
  console.log('\n=== Page Content ===\n', bodyText.substring(0, 1000));
  
  // Check for Tavus elements
  const tavusElements = await page.evaluate(() => {
    const elements = [];
    document.querySelectorAll('[id*="tavus"], [class*="tavus"], iframe').forEach(el => {
      elements.push({
        tag: el.tagName,
        id: el.id,
        class: el.className,
        src: el.src || null
      });
    });
    return elements;
  });
  
  if (tavusElements.length > 0) {
    console.log('\n=== Tavus/Video Elements Found ===');
    console.log(JSON.stringify(tavusElements, null, 2));
  }
  
  await browser.close();
})();
