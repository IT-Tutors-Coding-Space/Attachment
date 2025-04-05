@echo off
set DB_USER=root
set DB_PASS=Attachme@Admin
set DB_NAME=attachme
set BACKUP_DIR=C:\backups\database
set DATE=%date:~-4%%date:~-7,2%%date:~-10,2%

mkdir "%BACKUP_DIR%" 2>nul

"C:\wamp64\bin\mysql\mysql8.1.31\bin\mysqldump.exe" -u%DB_USER% -p%DB_PASS% %DB_NAME% > "%BACKUP_DIR%\backup_%DATE%.sql"

echo Backup completed: %BACKUP_DIR%\backup_%DATE%.sql
