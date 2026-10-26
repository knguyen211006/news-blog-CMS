<?php
// Read-only audit against Git HEAD: PHP expressions and backend form contracts.
$root = dirname(__DIR__);
chdir($root);
function canon(string $code): string {
    $out = '';
    foreach (token_get_all($code) as $token) {
        if (is_array($token)) {
            if (in_array($token[0], [T_WHITESPACE,T_COMMENT,T_DOC_COMMENT,T_INLINE_HTML], true)) continue;
            // T_OPEN_TAG includes its trailing newline (CRLF in a Windows checkout).
            $out .= $token[0] === T_OPEN_TAG ? rtrim($token[1]) : $token[1];
        } else $out .= $token;
    }
    return trim($out);
}
function contracts(string $source): array {
    preg_match_all('~<\?(?:php|=).*?\?>~s', $source, $blocks);
    $php = array_map('canon', $blocks[0]);
    $masked = preg_replace_callback('~<\?(?:php|=).*?\?>~s', fn($m) => '__PHP_' . base64_encode(canon($m[0])) . '__', $source);
    preg_match_all('~\b(?:href|action|name)="([^"]*)"~', $masked, $attrs);
    return ['php'=>array_count_values($php), 'attributes'=>array_count_values($attrs[0])];
}
$failed = 0;
foreach (new RecursiveIteratorIterator(new RecursiveDirectoryIterator($root.'/app/Views')) as $file) {
    if ($file->getExtension() !== 'php') continue;
    $name = str_replace('\\','/',substr($file->getPathname(), strlen($root)+1));
    $before = shell_exec('git show '.escapeshellarg('HEAD:'.$name));
    $after = file_get_contents($file->getPathname());
    $old = contracts($before); $new = contracts($after);
    foreach ($old as $kind=>$counts) foreach ($counts as $value=>$count) {
        if (($new[$kind][$value]??0) < $count) {
            echo "$name missing $kind: $value\n"; ++$failed;
        }
    }
}
echo "Missing original contracts: $failed\n";
exit($failed ? 1 : 0);
