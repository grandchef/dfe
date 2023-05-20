@echo OFF
set PATH=%PATH%;%~pd0..\vendor\bin

cd ..
cmd /C phpdoc --progressbar --sourcecode -d src\DFe -t docs\docs
cd %~pd0
start %~pd0..\docs\docs\index.html
