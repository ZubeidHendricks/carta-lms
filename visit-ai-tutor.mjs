import { chromium } from 'playwright';

(async () => {
  const browser = await chromium.launch({ headless: true });
  const context = await browser.newContext({
    viewport: { width: 1920, height: 1080 }
  });
  const page = await context.newPage();
  
  console.log('Navigating to AI Tutor page...');
  await page.goto('http://165.227.113.197/student/ai-tutor', { 
    waitUntil: 'networkidle',
    timeout: 30000 
  });
  
  console.log('Page loaded. Title:', await page.title());
  
  // Take screenshot
  await page.screenshot({ 
    path: 'ai-tutor-screenshot.png',
    fullPage: true 
  });
  console.log('Screenshot saved to ai-tutor-screenshot.png');
  
  // Get some page info
  const url = page.url();
  console.log('Current URL:', url);
  
  // Get page content snippet
  const bodyText = await page.evaluate(() => {
    const body = document.body.innerText;
    return body.substring(0, 500);
  });
  console.log('\nPage content preview:\n', bodyText);
  
  await browser.close();
})();
