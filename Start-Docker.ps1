$ErrorActionPreference = 'Stop'

Push-Location -LiteralPath $PSScriptRoot
try {
    if (-not (Get-Command docker -ErrorAction SilentlyContinue)) {
        throw 'Docker CLI not found. Install and start Docker Desktop, then reopen this terminal.'
    }
    docker info --format '{{.OSType}}' | ForEach-Object {
        if ($_ -ne 'linux') { throw 'Switch Docker Desktop to Linux containers.' }
    }
    if ($LASTEXITCODE -ne 0) { throw 'Docker Engine is not ready. Open Docker Desktop and wait for it to start.' }

    if (-not (Test-Path -LiteralPath '.env')) {
        $random = [System.Security.Cryptography.RandomNumberGenerator]::Create()
        try {
            $bytes = New-Object byte[] 32
            $random.GetBytes($bytes)
        } finally {
            $random.Dispose()
        }
        $password = [BitConverter]::ToString($bytes).Replace('-', '').ToLowerInvariant()
        [IO.File]::WriteAllLines((Join-Path $PSScriptRoot '.env'), @("CMS_DB_PASSWORD=$password", 'CMS_PORT=8088'))
    }

    docker compose up --build --detach --wait --wait-timeout 180
    if ($LASTEXITCODE -ne 0) { throw 'Compose could not start. Run docker compose logs to see the cause.' }
    docker compose ps
    docker compose port web 80
} finally {
    Pop-Location
}
