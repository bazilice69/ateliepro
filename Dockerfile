# Imagem para rodar o AteliêPro (Laravel) no Render.
FROM php:8.3-cli

# Dependências de sistema + extensões PHP necessárias (PostgreSQL, zip, etc.).
RUN apt-get update && apt-get install -y \
        git unzip libpq-dev libzip-dev libonig-dev \
    && docker-php-ext-install pdo pdo_pgsql pgsql zip bcmath \
    && rm -rf /var/lib/apt/lists/*

# Composer (copiado da imagem oficial).
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /app

# Instala as dependências PHP primeiro (aproveita cache de camada).
COPY composer.json composer.lock ./
RUN composer install --no-dev --optimize-autoloader --no-interaction --no-scripts

# Copia o restante da aplicação.
COPY . .

# Finaliza o autoload e dá permissão às pastas de escrita do Laravel.
RUN composer dump-autoload --optimize \
    && chmod -R 775 storage bootstrap/cache

# O Render injeta a porta em $PORT; o start.sh cuida de migrar e subir o servidor.
CMD ["sh", "start.sh"]
