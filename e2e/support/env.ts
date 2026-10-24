import { config } from 'dotenv';
import path from 'node:path';

config({ path: path.resolve(__dirname, '../.env'), quiet: true });
export function required(name: string): string {
  const value = process.env[name]?.trim();
  if (!value) throw new Error('Missing ' + name + ': configure e2e/.env (see .env.example).');
  return value;
}
const configuredURL = new URL(required('BASE_URL'));
if (!['127.0.0.1', 'localhost'].includes(configuredURL.hostname) || configuredURL.protocol !== 'http:') {
  throw new Error('E2E requires a local HTTP server with config/test.php.');
}
configuredURL.pathname = configuredURL.pathname.replace(/\/$/, '') + '/';
export const baseURL = configuredURL.href;
export const adminState = path.resolve(__dirname, '../.auth/admin.json');
export const admin = { email: required('ADMIN_EMAIL'), password: required('ADMIN_PASSWORD') };
export const invalid = { email: required('INVALID_EMAIL'), password: required('INVALID_PASSWORD') };
export const route = (name = '', params: Record<string, string> = {}) =>
  './index.php?' + new URLSearchParams({ r: name, ...params });
