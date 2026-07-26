@echo off
wt -d . powershell -NoExit -Command "php artisan serve" ; new-tab -d . powershell -NoExit -Command "npm run dev" ; new-tab -d . powershell