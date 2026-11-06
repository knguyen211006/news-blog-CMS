<?php
declare(strict_types=1);
// Read-only audit. The bundled baseline also works in submission ZIPs without Git.
$root = dirname(__DIR__);
require __DIR__.'/support/view-contract-functions.php';
$baselineFile = __DIR__.'/support/view-contracts.json';
if (!is_file($baselineFile)) { fwrite(STDERR,"Missing view contract baseline.\n"); exit(1); }
$baseline = json_decode(file_get_contents($baselineFile), true, 512, JSON_THROW_ON_ERROR);
$failed = 0;
foreach ($baseline as $name => $old) {
    $path = $root.'/'.$name;
    if (!is_file($path)) { echo "Missing view: $name\n"; ++$failed; continue; }
    $new = contracts(file_get_contents($path));
    foreach ($old as $kind => $counts) foreach ($counts as $value => $count) {
        if (($new[$kind][$value] ?? 0) < $count) {
            echo "$name missing $kind: $value\n"; ++$failed;
        }
    }
}
echo "Missing original contracts: $failed\n";
exit($failed ? 1 : 0);
