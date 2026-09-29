#!/usr/bin/env bash
# Script de build do AteliêPro no Render.
# Instala dependências, prepara o storage e o banco, e otimiza para produção.
set -o errexit

echo "==> Instalando dependências (Composer)..."
composer install --no-dev --optimize-autoloader --no-interaction

echo "==> Link do storage (para exibir logos/uploads)..."
php artisan storage:link || true

echo "==> Migrando o banco de dados..."
php artisan migrate --force

echo "==> Populando dados de demonstração (só se o banco estiver vazio)..."
# Roda o seed apenas na primeira vez (quando ainda não há lojas cadastradas).
php artisan db:seed --force || true

echo "==> Otimizando (config, rotas, views)..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

echo "==> Build concluído!"
