<#
PowerShell script to add VirtualHost and hosts entry for SIGC.
Run this script as Administrator from the project root.
#>

function Test-IsAdmin {
    $current = [Security.Principal.WindowsIdentity]::GetCurrent()
    $principal = New-Object Security.Principal.WindowsPrincipal($current)
    return $principal.IsInRole([Security.Principal.WindowsBuiltInRole]::Administrator)
}

if (-not (Test-IsAdmin)) {
    Write-Host "Elevating to Administrator..."
    Start-Process -FilePath powershell.exe -ArgumentList "-NoProfile -ExecutionPolicy Bypass -File `"$PSCommandPath`"" -Verb RunAs
    exit
}

$vhostsPath = 'C:\xampp\apache\conf\extra\httpd-vhosts.conf'
$httpdConf = 'C:\xampp\apache\conf\httpd.conf'
$hostsPath = 'C:\Windows\System32\drivers\etc\hosts'
$backupDir = Join-Path $env:USERPROFILE 'sigc-setup-backups'

if (-not (Test-Path $backupDir)) { New-Item -Path $backupDir -ItemType Directory | Out-Null }

# Backup files
foreach ($p in @($vhostsPath, $httpdConf, $hostsPath)) {
    if (Test-Path $p) {
        $time = Get-Date -Format yyyyMMddHHmmss
        Copy-Item -Path $p -Destination (Join-Path $backupDir ([IO.Path]::GetFileName($p) + ".bak_$time")) -Force
    }
}

# Add VirtualHost if not present
$vhostsContent = ''
if (Test-Path $vhostsPath) { $vhostsContent = Get-Content $vhostsPath -Raw -ErrorAction SilentlyContinue }
if ($vhostsContent -notmatch 'ServerName\s+sigc\.local') {
    $vhostBlock = @'
<VirtualHost *:80>
    ServerName sigc.local
    DocumentRoot "C:/xampp/htdocs/SIGC"
    <Directory "C:/xampp/htdocs/SIGC">
        Options Indexes FollowSymLinks
        AllowOverride All
        Require all granted
    </Directory>
</VirtualHost>
'@

    Add-Content -Path $vhostsPath -Value "`r`n$vhostBlock" -Encoding UTF8
    Write-Host "VirtualHost añadido a $vhostsPath"
} else {
    Write-Host "VirtualHost ya existe en $vhostsPath"
}

# Ensure httpd.conf includes the vhosts file
if (Test-Path $httpdConf) {
    $httpdText = Get-Content $httpdConf -Raw -ErrorAction SilentlyContinue
    if ($httpdText -match '#\s*Include\s+conf/extra/httpd-vhosts.conf') {
        $newText = $httpdText -replace '#\s*Include\s+conf/extra/httpd-vhosts.conf','Include conf/extra/httpd-vhosts.conf'
        Set-Content -Path $httpdConf -Value $newText -Encoding UTF8
        Write-Host "Descomentada la línea Include en $httpdConf"
    } else {
        Write-Host "La línea Include en $httpdConf ya está activa o no encontrada"
    }
}

# Add hosts entry if missing
if (Test-Path $hostsPath) {
    $hostsText = Get-Content $hostsPath -Raw -ErrorAction SilentlyContinue
    if ($hostsText -notmatch 'sigc\.local') {
        Add-Content -Path $hostsPath -Value "`r`n127.0.0.1`t sigc.local" -Encoding UTF8
        Write-Host "Añadida entrada en hosts: 127.0.0.1 sigc.local"
    } else {
        Write-Host "Entrada sigc.local ya presente en hosts"
    }
}

# Restart Apache (try common service names, fallback to net commands)
$svc = Get-Service -Name 'Apache2.4' -ErrorAction SilentlyContinue
if (-not $svc) { $svc = Get-Service -Name 'httpd' -ErrorAction SilentlyContinue }

if ($svc) {
    Write-Host "Reiniciando servicio $($svc.Name)..."
    try { Restart-Service -Name $svc.Name -Force -ErrorAction Stop; Write-Host 'Servicio reiniciado.' } catch { Write-Host 'No se pudo reiniciar el servicio mediante Restart-Service.' }
} else {
    Write-Host "Servicios Apache no detectados; intentando usar net stop/start (requiere nombre de servicio Apache2.4)."
    Start-Process -FilePath net -ArgumentList 'stop','Apache2.4' -Wait -NoNewWindow -ErrorAction SilentlyContinue
    Start-Process -FilePath net -ArgumentList 'start','Apache2.4' -Wait -NoNewWindow -ErrorAction SilentlyContinue
}

Write-Host "Listo. Abre http://sigc.local/ en tu navegador. Si no funciona, verifica XAMPP Control Panel y permisos."
