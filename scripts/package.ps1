param(
    [string]$OutputPath = (Join-Path (Split-Path $PSScriptRoot -Parent) '../news-blog-cms-submission.zip'),
    [switch]$IncludeLocalConfig
)
$ErrorActionPreference = 'Stop'
$projectRoot = [IO.Path]::GetFullPath((Split-Path $PSScriptRoot -Parent))
$zipPath = [IO.Path]::GetFullPath($OutputPath)
if (Test-Path -LiteralPath $zipPath) { throw "Archive already exists: $zipPath. Choose a new OutputPath." }
Add-Type -AssemblyName System.IO.Compression
Add-Type -AssemblyName System.IO.Compression.FileSystem
$stream = [IO.File]::Open($zipPath, [IO.FileMode]::CreateNew)
$archive = [IO.Compression.ZipArchive]::new($stream, [IO.Compression.ZipArchiveMode]::Create)
$count = 0
try {
    foreach ($file in Get-ChildItem -LiteralPath $projectRoot -File -Recurse -Force) {
        $relative = $file.FullName.Substring($projectRoot.Length + 1).Replace('\','/')
        if ($relative.StartsWith('.git/') -or $relative -eq 'config/test.php' -or $relative.StartsWith('tests/results/')) { continue }
        if ($relative -eq 'config/local.php' -and -not $IncludeLocalConfig) { continue }
        if ($relative.StartsWith('storage/') -and $relative -ne 'storage/.gitkeep') { continue }
        if ($relative.StartsWith('uploads/') -and $relative -notin @('uploads/.htaccess','uploads/.gitkeep')) { continue }
        if ($relative.EndsWith('.zip')) { continue }
        [IO.Compression.ZipFileExtensions]::CreateEntryFromFile($archive,$file.FullName,('news-blog-cms/'+$relative),[IO.Compression.CompressionLevel]::Optimal) | Out-Null
        $count++
    }
} finally {
    $archive.Dispose()
    $stream.Dispose()
}
[pscustomobject]@{ Archive=$zipPath; Files=$count; IncludesLocalConfig=$IncludeLocalConfig.IsPresent; Bytes=(Get-Item -LiteralPath $zipPath).Length }
