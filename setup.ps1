param(
    [switch]$Reset,
    [switch]$NoServer,
    [switch]$SkipEnv
)

$database = 'ready_for_the_test'

$ErrorActionPreference = 'Stop'
$projectDir = $PSScriptRoot

$xamppPath = $env:XAMPP
if (-not $xamppPath) { $xamppPath = 'C:\xampp' }
$mysql = Join-Path $xamppPath 'mysql\bin\mysql.exe'
$php = Join-Path $xamppPath 'php\php.exe'

if (-not (Test-Path $mysql)) { throw "No se encuentra mysql.exe en: $mysql" }
if (-not (Test-Path $php)) { throw "No se encuentra php.exe en: $php" }

$schema = Join-Path $projectDir 'backend\database\schema.sql'
$seed = Join-Path $projectDir 'backend\database\seed.sql'
if (-not (Test-Path $schema)) { throw "Falta el esquema: $schema" }
if (-not (Test-Path $seed)) { throw "Faltan los datos semilla: $seed" }

Write-Host "Proyecto: $projectDir"

function Invoke-MysqlQuery([string]$Query) {
    & $mysql --default-character-set=utf8mb4 -u root --batch --skip-column-names --execute $Query
}

$tableCount = Invoke-MysqlQuery "SELECT COUNT(*) FROM information_schema.tables WHERE table_schema = '$database' AND table_name = 'users'"

if ($Reset -or [int]$tableCount -eq 0) {
    if ($Reset) {
        Write-Host "Restableciendo la base $database (DROP + CREATE)..."
        Invoke-MysqlQuery "DROP DATABASE IF EXISTS $database" | Out-Null
    }
    Write-Host "Creando esquema de la base de datos..."
    cmd /c "`"$mysql`" --default-character-set=utf8mb4 -u root < `"$schema`""
    if ($LASTEXITCODE -ne 0) { throw 'Fallo al importar backend/database/schema.sql' }
    Write-Host "Cargando datos semilla..."
    cmd /c "`"$mysql`" --default-character-set=utf8mb4 -u root < `"$seed`""
    if ($LASTEXITCODE -ne 0) { throw 'Fallo al importar backend/database/seed.sql' }
    Write-Host "Base de datos lista."
} else {
    Write-Host "La base '$database' ya existe; se omite la importacion."
}

if (-not $SkipEnv) {
    $envFile = Join-Path $projectDir 'backend\.env'
    if (-not (Test-Path $envFile)) {
        $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789'.ToCharArray()
        $secret = -join (1..48 | ForEach-Object { $chars | Get-Random })
        @("DB_HOST=localhost", "DB_NAME=$database", "DB_USER=root", "DB_PASSWORD=", "TOKEN_SECRET=$secret") |
            Set-Content -LiteralPath $envFile -Encoding ascii
        Write-Host "Creado backend\.env con un TOKEN_SECRET nuevo."
    } else {
        Write-Host "backend\.env ya existe; no se modifica."
    }
}

if (-not $NoServer) {
    $router = Join-Path $projectDir 'backend\router.php'
    Write-Host "Iniciando servidor en http://localhost:8090 (Ctrl+C para detener)..."
    & $php -S 127.0.0.1:8090 $router
}
