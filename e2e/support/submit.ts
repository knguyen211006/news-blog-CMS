import { expect, type Page, type Locator, type Route, type Request } from '@playwright/test';

// Hold an actual SSR navigation at the network boundary until assertions finish.
// No artificial delays and no mock success responses: continue to the real PHP app.
export async function holdSubmit(page: Page, action: string) {
  let release!: () => void;
  let seen!: (request: Request) => void;
  const gate = new Promise<void>(resolve => { release = resolve; });
  const arrived = new Promise<Request>(resolve => { seen = resolve; });
  const requests: Request[] = [];
  const pending: Promise<void>[] = [];
  const handler = async (route: Route) => {
    const request = route.request();
    if (request.method() === 'POST' && new URL(request.url()).searchParams.get('r') === action) {
      requests.push(request);
      seen(request);
      const continued = gate.then(() => route.continue());
      pending.push(continued);
      await continued;
      return;
    }
    await route.continue();
  };
  await page.route('**/index.php?**', handler);
  return {
    arrived, requests, release,
    async dispose() {
      release();
      await Promise.all(pending);
      await page.unroute('**/index.php?**', handler);
    },
  };
}

let observerId = 0;
type SubmitState = { disabled: boolean; label: string; busy: string | null; duplicateCancelled: boolean };

export async function observePending(button: Locator, form: Locator) {
  await expect(button).toBeEnabled();
  await expect(form).toBeVisible();
  let state: SubmitState | undefined;
  const binding = '__e2eSubmitState' + (++observerId);
  await button.page().exposeFunction(binding, (snapshot: SubmitState) => { state = snapshot; });
  // Capture the outgoing DOM synchronously, after the app's document listener.
  // Chromium suspends DOM queries while an SSR navigation is held by routing.
  // The binding passes only UI state; the real POST and redirect still proceed.
  await button.evaluate((element: HTMLButtonElement, name) => {
    window.addEventListener('submit', event => {
      const owner = element.form!;
      if (event.target !== owner) return;
      const duplicate = new SubmitEvent('submit', { bubbles: true, cancelable: true, submitter: element });
      owner.dispatchEvent(duplicate);
      const snapshot = {
        disabled: element.disabled,
        label: element.textContent!.trim(),
        busy: owner.getAttribute('aria-busy'),
        duplicateCancelled: duplicate.defaultPrevented,
      };
      const report = (window as unknown as Record<string, (value: typeof snapshot) => Promise<void>>)[name];
      void report(snapshot);
    }, { once: true });
  }, binding);
  return {
    async expectLocked() {
      await expect.poll(() => state).toEqual({
        disabled: true, label: 'Đang xử lý…', busy: 'true', duplicateCancelled: true,
      });
    },
    async dispose() { /* The one-shot listener is removed and the test context owns the binding. */ },
  };
}
