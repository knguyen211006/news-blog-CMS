param([string]$PhpPath='C:\xampp\php\php.exe')
$ErrorActionPreference='Stop'
$projectRoot=Split-Path $PSScriptRoot -Parent
$bad=@()
$files=@(Get-ChildItem -LiteralPath (Join-Path $projectRoot 'app'),(Join-Path $projectRoot 'config'),(Join-Path $projectRoot 'scripts'),(Join-Path $projectRoot 'tests') -Recurse -File -Filter '*.php')
$files+=Get-Item -LiteralPath (Join-Path $projectRoot 'index.php')
foreach($file in $files){
    $result=& $PhpPath -l $file.FullName
    if($LASTEXITCODE -ne 0){$bad+=$result}
}
if($bad.Count){$bad;exit 1}
Write-Output ("PHP syntax PASS: {0} files" -f $files.Count)
& $PhpPath (Join-Path $PSScriptRoot 'check.php')
exit $LASTEXITCODE
