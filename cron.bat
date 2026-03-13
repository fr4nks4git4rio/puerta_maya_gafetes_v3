@echo off

CD d: &&  CD \Job\PuertaMayaGafetes\proyecto\pm_credenciales && php artisan schedule:run

timeout 10

CD d: &&  CD \Job\PuertaMayaGafetes\proyecto\pm_credenciales && "cron.bat"

pause

@cls