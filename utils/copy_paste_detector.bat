@echo OFF
set PATH=%PATH%;%~pd0..\vendor\bin

cd ..
cmd /C phpcpd src
cmd /C phpcpd tests\DFe
cd %~pd0

pause
