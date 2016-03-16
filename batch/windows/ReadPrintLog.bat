cd /d %~dp0
cd /d ../../user_config/
setlocal enabledelayedexpansion

for /f "tokens=*" %%a in ('type batch.config') do (
	set %%a
)
cd /d %~dp0
FOR /L %%G IN (1,1,%limit_run_batch%) DO (
	start /b %link_PHP_program%  ../php/BatchReadPrintLog.php 
	timeout /T 1
)