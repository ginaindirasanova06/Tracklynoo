@echo off
title TRACKLY - Starting Services...
echo ==========================================
echo  TRACKLY - Menjalankan Apache dan MySQL
echo ==========================================
echo.

echo [1/2] Starting Apache...
start "" "c:\xampppp\apache_start.bat"
timeout /t 3 /nobreak >nul

echo [2/2] Starting MySQL...
start "" "c:\xampppp\mysql_start.bat"
timeout /t 5 /nobreak >nul

echo.
echo ==========================================
echo  Selesai! Buka browser dan akses:
echo  http://localhost/Trackly/
echo ==========================================
echo.
pause
