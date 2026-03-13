Set WshShell = CreateObject("WScript.Shell")
WshShell.Run chr(34) & "d:\\Job\\PuertaMayaGafetes\\proyecto\\pm_credenciales\\cron.bat" & Chr(34), 0
Set WshShell = Nothing