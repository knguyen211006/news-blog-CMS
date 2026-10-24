const { spawn, spawnSync } = require('node:child_process');
const path = require('node:path');
require('dotenv').config({ path: path.resolve(__dirname, '../.env'), quiet: true });
const root = path.resolve(__dirname, '../..');
const php = process.env.PHP_BINARY;
const target = new URL(process.env.BASE_URL);
const configPath = path.join(root, 'config/test.php');
if (!php || !['127.0.0.1', 'localhost'].includes(target.hostname) || target.protocol !== 'http:') {
  throw new Error('Configure PHP_BINARY and a local HTTP BASE_URL in e2e/.env.');
}
const check = spawnSync(php, ['-r', '$c = require $argv[1]; if (($c["db_name"] ?? "") !== "news_blog_cms_test" || ($c["base_path"] ?? "")."/" !== $argv[2]) { exit(1); }', configPath, target.pathname], {
  cwd: root, stdio: 'inherit', windowsHide: true,
});
if (check.status !== 0) throw new Error('E2E requires news_blog_cms_test and matching base_path.');
const server = spawn(php, ['-S', target.host, 'scripts/router.php'], {
  cwd: root, stdio: 'inherit', windowsHide: true,
  env: { ...process.env, CMS_CONFIG: configPath },
});
server.on('error', error => { console.error(error.message); process.exit(1); });
server.on('exit', code => process.exit(code ?? 1));
for (const signal of ['SIGINT', 'SIGTERM']) process.on(signal, () => server.kill());
