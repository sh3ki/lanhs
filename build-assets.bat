@echo off
REM Build script for LANHS Laravel Mix assets
REM Fixes Node.js v22 compatibility issue with webpack

echo Building Laravel Mix assets...
set NODE_OPTIONS=--openssl-legacy-provider
call npm run dev

echo.
echo Build complete!
echo If you need production build, run: npm run production
pause
