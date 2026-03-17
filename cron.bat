@echo off

CD d: &&  CD \Job\PuertaMayaGafetes\proyecto\pm_credenciales && php artisan schedule:run

timeout 600

CD d: &&  CD \Job\PuertaMayaGafetes\proyecto\pm_credenciales && "cron.bat"

pause

@cls