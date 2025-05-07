@echo off
:loop
php "D:\XAMPP\htdocs\CCS_SIT-IN_MONITORING_SYSTEM\src\admin\updateComputerStatus.php"
timeout /t 30 /nobreak
goto loop 