# Script de démarrage du serveur de développement PHP
# Usage : .\serve.ps1 [port]

param(
    [int]$Port = 8000
)

$phpPath = (Get-Command php -ErrorAction SilentlyContinue).Source
if (-not $phpPath) {
    Write-Error "PHP n'est pas trouvé dans le PATH. Vérifiez votre installation."
    exit 1
}

$host_addr = "localhost"
$docroot   = Join-Path $PSScriptRoot "public"
$router    = Join-Path $docroot "router.php"

Write-Host ""
Write-Host "=======================================" -ForegroundColor Cyan
Write-Host "  Serveur de développement Complianz   " -ForegroundColor Cyan
Write-Host "=======================================" -ForegroundColor Cyan
Write-Host ""
Write-Host "  URL      : http://$($host_addr):$Port" -ForegroundColor Green
Write-Host "  Racine   : $docroot"                  -ForegroundColor Gray
Write-Host "  PHP      : $phpPath"                  -ForegroundColor Gray
Write-Host ""
Write-Host "  Ctrl+C pour arrêter le serveur"       -ForegroundColor Yellow
Write-Host ""

& php -S "${host_addr}:${Port}" -t $docroot $router
