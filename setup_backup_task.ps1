# Backup Task Configuration
$Action = New-ScheduledTaskAction -Execute "C:\wamp64\bin\mysql\mysql8.1.31\bin\mysqldump.exe" `
    -Argument "-u root -pAttachme@Admin attachme > C:\backups\database\backup_$(Get-Date -Format 'yyyyMMdd').sql"

$Trigger = New-ScheduledTaskTrigger -Daily -At 2am
$Settings = New-ScheduledTaskSettingsSet -StartWhenAvailable -DontStopOnIdleEnd

# Register the task
Register-ScheduledTask -TaskName "DatabaseBackup" `
    -Action $Action `
    -Trigger $Trigger `
    -Settings $Settings `
    -Description "Daily database backup at 2 AM" `
    -RunLevel Highest
