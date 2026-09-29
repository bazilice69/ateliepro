#!/usr/bin/env sh
# Inicialização do AteliêPro no Render (executado a cada start do container).
set -e

echo "==> Preparando storage..."
php artisan storage:link || true

echo "==> Migrando o banco..."
php artisan migrate --force || true

echo "==> Populando dados de demonstração (idempotente)..."
php artisan db:seed --force || true

echo "==> Otimizando..."
php artisan config:cache || true
php artisan route:cache || true
php artisan view:cache || true

echo "==> Subindo o servidor na porta ${PORT:-10000}..."
php -S 0.0.0.0:${PORT:-10000} -t public public/index.php
