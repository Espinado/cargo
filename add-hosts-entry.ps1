# Добавляет cargo-trans.test в hosts. Запустите от имени администратора:
# ПКМ по файлу -> "Запуск с помощью PowerShell" (или: powershell -ExecutionPolicy Bypass -File add-hosts-entry.ps1)

$hostsPath = 'C:\Windows\System32\drivers\etc\hosts'
$line = "127.0.0.1`tcargo-trans.test`t#laragon magic!"

if (Get-Content $hostsPath -Raw | Select-String -Pattern 'cargo-trans\.test' -Quiet) {
    Write-Host 'cargo-trans.test уже есть в hosts.' -ForegroundColor Yellow
} else {
    Add-Content -Path $hostsPath -Value $line
    Write-Host 'Добавлено: 127.0.0.1 cargo-trans.test' -ForegroundColor Green
}

Read-Host 'Нажмите Enter для выхода'
