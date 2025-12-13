import { chromium } from 'playwright';

(async () => {
  console.log('🚀 Launching Playwright with Chromium...');
  
  const browser = await chromium.launch({
    headless: false,
    args: ['--start-maximized']
  });
  
  const context = await browser.newContext({
    viewport: null,
    userAgent: 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36'
  });
  
  const page = await context.newPage();
  
  const credentials = {
    username: 'sarah@carta.com',
    password: 'password'
  };
  
  try {
    console.log('📱 Navigating to login page...');
    await page.goto('http://165.227.113.197/login', { timeout: 60000 });
    await page.waitForTimeout(2000);
    
    console.log('🔐 Logging in as sarah@carta.com...');
    await page.fill('input[name="email"]', credentials.username);
    await page.fill('input[name="password"]', credentials.password);
    
    await page.click('button[type="submit"]');
    await page.waitForNavigation({ timeout: 30000 });
    
    console.log('✅ Login successful!');
    await page.waitForTimeout(2000);
    
    console.log('📱 Navigating to AI Tutor...');
    await page.goto('http://165.227.113.197/student/ai-tutor', { timeout: 60000 });
    await page.waitForTimeout(3000);
    
    console.log('🎉 AI Tutor page loaded!');
    console.log('📸 Taking screenshot...');
    await page.screenshot({ path: 'ai-tutor-screenshot.png', fullPage: true });
    console.log('✅ Screenshot saved');
    
    console.log('🔍 Browser will stay open. Press Ctrl+C when done.');
    
    // Keep browser open
    await new Promise(() => {});
    
  } catch (error) {
    console.error('❌ Error:', error.message);
    try {
      await page.screenshot({ path: 'error-screenshot.png', fullPage: true });
      console.log('📸 Error screenshot saved');
    } catch (e) {}
    await browser.close();
  }
})();
