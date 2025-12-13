import puppeteer from 'puppeteer';

(async () => {
  console.log('🚀 Launching browser...');
  
  const browser = await puppeteer.launch({
    headless: false,
    args: [
      '--start-maximized',
      '--no-sandbox',
      '--disable-setuid-sandbox',
      '--disable-blink-features=AutomationControlled',
      '--disable-extensions'
    ],
    ignoreDefaultArgs: ['--enable-automation'],
    defaultViewport: null
  });
  
  const page = await browser.newPage();
  
  await page.evaluateOnNewDocument(() => {
    Object.defineProperty(navigator, 'webdriver', { get: () => false });
  });
  
  await page.setUserAgent('Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36');
  
  const credentials = {
    username: 'sarah@carta.com',
    password: 'password'
  };
  
  const targetUrl = 'http://165.227.113.197/student/ai-tutor';
  
  try {
    console.log('📱 Navigating to login page...');
    await page.goto('http://165.227.113.197/login', { waitUntil: 'domcontentloaded', timeout: 60000 });
    await page.waitForTimeout(2000);
    
    console.log('🔐 Logging in as sarah@carta.com...');
    
    // Fill in login form
    await page.type('input[name="email"]', credentials.username, { delay: 50 });
    await page.type('input[name="password"]', credentials.password, { delay: 50 });
    
    // Submit form
    await Promise.all([
      page.waitForNavigation({ waitUntil: 'domcontentloaded', timeout: 30000 }),
      page.click('button[type="submit"]')
    ]);
    
    console.log('✅ Login successful!');
    await page.waitForTimeout(2000);
    
    console.log(`📱 Navigating to AI Tutor: ${targetUrl}`);
    await page.goto(targetUrl, { waitUntil: 'domcontentloaded', timeout: 60000 });
    await page.waitForTimeout(3000);
    
    const currentUrl = page.url();
    console.log(`✅ Current URL: ${currentUrl}`);
    
    if (currentUrl.includes('ai-tutor')) {
      console.log('🎉 AI Tutor page loaded successfully!');
    }
    
    console.log('📸 Taking screenshot...');
    await page.screenshot({ path: 'ai-tutor-screenshot.png', fullPage: true });
    console.log('✅ Screenshot saved as ai-tutor-screenshot.png');
    
    console.log('🔍 Browser will stay open for interaction. Press Ctrl+C when done.');
    
  } catch (error) {
    console.error('❌ Error:', error.message);
    await page.screenshot({ path: 'error-screenshot.png', fullPage: true });
    console.log('📸 Error screenshot saved');
    console.log('🔍 Browser will stay open for debugging.');
  }
})();
