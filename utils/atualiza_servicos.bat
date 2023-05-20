@echo OFF

mkdir -p "../storage/generated/src/DFe/Database/data"
php -f atualiza_servicos.php "../storage/generated/src/DFe/Database/data"
