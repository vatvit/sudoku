import { test, expect } from '@playwright/test';

test('visits homepage', async ({ page }) => {
  await page.goto('/');
  await expect(page).toHaveTitle(/.+/); // проверка, что страница открылась и у неё есть title
});
