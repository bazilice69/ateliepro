# 🚀 Deploy do AteliêPro no VPS (King Host / Ubuntu) — passo a passo

Guia para colocar o sistema **no ar de verdade**: rápido, com cadeado de
segurança (HTTPS) e **backup automático diário** — pronto para receber clientes
pagantes.

> Serve para qualquer VPS **Ubuntu** com acesso root (King Host, HostGator,
> Hostinger, etc.). Aqui usamos a **King Host VPS 4 GB Linux**.

---

## 📋 Antes de começar — o que você vai precisar

- Acesso ao painel da King Host (você já tem ✅)
- O **IP do servidor** e a **senha de root** (vêm quando o VPS é configurado)
- Um programa para acessar o servidor por SSH:
  - **Windows:** use o **PowerShell** (já vem no Windows) ou baixe o **PuTTY**
  - O comando de acesso é: `ssh root@SEU_IP_AQUI`

---

## PASSO 0 — Configurar o VPS no painel da King Host

1. No painel, clique em **Configurar** (no "Servidor VPS 4 GB LINUX").
2. **Sistema Operacional:** escolha **Ubuntu** (a versão LTS mais nova — ex.
   Ubuntu 24.04 ou 22.04). **SEM painel** (nada de cPanel/Plesk — queremos o
   Ubuntu "limpo").
3. **Senha de root:** crie uma senha FORTE e **guarde num lugar seguro**.
   Você vai usá-la para entrar no servidor.
4. Confirme e aguarde alguns minutos até o servidor ficar **pronto/ativo**.
5. Anote o **IP** que a King Host mostrar (algo como `123.45.67.89`).

---

## PASSO 1 — Entrar no servidor (SSH)

No PowerShell (Windows) ou terminal, rode (troque pelo seu IP):

```bash
ssh root@SEU_IP_AQUI
```

- Na primeira vez ele pergunta se confia — digite **yes**.
- Cole a **senha de root** (ao digitar/colar a senha não aparece nada na tela —
  é normal, é segurança). Enter.

Se apareceu algo como `root@servidor:~#`, **você está dentro!** 🎉

---

## PASSO 2 — Atualizar o Ubuntu e instalar o básico

Cole os comandos abaixo **um bloco por vez** (copiar e colar no terminal):

```bash
apt update && apt upgrade -y
```

```bash
apt install -y git unzip curl nginx software-properties-common
```

---

## PASSO 3 — Instalar o PHP 8.4 e extensões

O AteliêPro exige **PHP 8.4**. Adicionamos o repositório oficial e instalamos:

```bash
add-apt-repository ppa:ondrej/php -y && apt update
```

```bash
apt install -y php8.4-fpm php8.4-cli php8.4-mysql php8.4-pgsql \
  php8.4-mbstring php8.4-xml php8.4-bcmath php8.4-zip php8.4-curl \
  php8.4-gd php8.4-intl
```

Confira a versão:

```bash
php -v
```

Deve mostrar **PHP 8.4.x**.

---

## PASSO 4 — Instalar o Composer (gerenciador do PHP)

```bash
curl -sS https://getcomposer.org/installer | php
mv composer.phar /usr/local/bin/composer
composer --version
```

---

## PASSO 5 — Instalar o banco de dados (MySQL)

```bash
apt install -y mysql-server
```

Crie o banco e um usuário para o AteliêPro (troque `SENHA_FORTE_DO_BANCO` por
uma senha sua):

```bash
mysql -e "CREATE DATABASE ateliepro CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
mysql -e "CREATE USER 'ateliepro'@'localhost' IDENTIFIED BY 'SENHA_FORTE_DO_BANCO';"
mysql -e "GRANT ALL PRIVILEGES ON ateliepro.* TO 'ateliepro'@'localhost';"
mysql -e "FLUSH PRIVILEGES;"
```

> **Guarde** o nome do banco (`ateliepro`), o usuário (`ateliepro`) e a senha —
> vamos usar no Passo 7.

---

## PASSO 6 — Baixar o AteliêPro do GitHub

```bash
cd /var/www
git clone https://github.com/bazilice69/ateliepro.git
cd ateliepro
```

Instale as dependências do projeto (sem as de desenvolvimento):

```bash
composer install --no-dev --optimize-autoloader
```

---

## PASSO 7 — Configurar o ambiente (.env)

Crie o arquivo de configuração a partir do exemplo:

```bash
cp .env.example .env
```

Abra para editar:

```bash
nano .env
```

Ajuste estas linhas (as demais pode deixar como estão):

```env
APP_NAME=AteliêPro
APP_ENV=production
APP_DEBUG=false
APP_URL=https://SEU_DOMINIO_AQUI

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=ateliepro
DB_USERNAME=ateliepro
DB_PASSWORD=SENHA_FORTE_DO_BANCO
```

> ⚠️ **`APP_DEBUG=false`** e **`APP_ENV=production`** são essenciais em produção
> (escondem detalhes de erro que exporiam o sistema).

Para salvar no `nano`: **Ctrl+O** → Enter → **Ctrl+X**.

Gere a chave de segurança e prepare a aplicação:

```bash
php artisan key:generate
php artisan migrate --force
php artisan db:seed --class=DemoMultiTenantSeeder --force
php artisan storage:link
php artisan config:cache
```

Dê as permissões corretas às pastas de escrita:

```bash
chown -R www-data:www-data /var/www/ateliepro
chmod -R 775 /var/www/ateliepro/storage /var/www/ateliepro/bootstrap/cache
```

---

## PASSO 8 — Configurar o Nginx (o "porteiro" do site)

Crie a configuração do site:

```bash
nano /etc/nginx/sites-available/ateliepro
```

Cole (troque `SEU_DOMINIO_AQUI` pelo seu domínio ou pelo IP por enquanto):

```nginx
server {
    listen 80;
    server_name SEU_DOMINIO_AQUI;
    root /var/www/ateliepro/public;

    index index.php;
    charset utf-8;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        fastcgi_pass unix:/run/php/php8.4-fpm.sock;
        fastcgi_index index.php;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}
```

Salve (**Ctrl+O**, Enter, **Ctrl+X**) e ative o site:

```bash
ln -s /etc/nginx/sites-available/ateliepro /etc/nginx/sites-enabled/
rm -f /etc/nginx/sites-enabled/default
nginx -t && systemctl reload nginx
```

Se `nginx -t` disser **"syntax is ok"** e **"test is successful"**, deu certo. ✅

Acesse no navegador: `http://SEU_IP` — o AteliêPro deve aparecer. 🎉

---

## PASSO 9 — Apontar o domínio (bonacci...)

No painel de onde você registrou o domínio (Registro.br), aponte para o VPS:

- Crie um registro **A** com o nome `@` apontando para o **IP do VPS**.
- Crie um registro **A** com o nome `www` apontando para o mesmo **IP**.

A propagação pode levar de minutos a algumas horas.

---

## PASSO 10 — Cadeado de segurança (HTTPS grátis)

Depois que o domínio estiver apontando para o VPS:

```bash
apt install -y certbot python3-certbot-nginx
certbot --nginx -d SEU_DOMINIO_AQUI -d www.SEU_DOMINIO_AQUI
```

Siga as perguntas (informe um e-mail, aceite os termos). Ele instala o
certificado e ativa o **https://** automaticamente. 🔒

> Depois, edite o `.env` e ajuste `APP_URL=https://SEU_DOMINIO_AQUI` e rode
> `php artisan config:cache` de novo.

---

## PASSO 11 — Backup automático diário 🛡️ (para NUNCA perder dados)

Crie o script de backup:

```bash
nano /root/backup-ateliepro.sh
```

Cole:

```bash
#!/bin/bash
DATA=$(date +%Y%m%d_%H%M)
DEST=/root/backups
mkdir -p $DEST
# Backup do banco
mysqldump -u ateliepro -p'SENHA_FORTE_DO_BANCO' ateliepro > $DEST/db_$DATA.sql
# Backup dos arquivos enviados (fotos, croquis, logos)
tar -czf $DEST/storage_$DATA.tar.gz -C /var/www/ateliepro/storage/app/public .
# Mantém só os últimos 14 dias de backup
find $DEST -type f -mtime +14 -delete
```

Salve, dê permissão e agende para rodar todo dia às 3h da manhã:

```bash
chmod +x /root/backup-ateliepro.sh
(crontab -l 2>/dev/null; echo "0 3 * * * /root/backup-ateliepro.sh") | crontab -
```

> 💡 **Recomendado depois:** mandar esses backups para um lugar externo (Google
> Drive, S3, etc.) para o caso do servidor inteiro falhar. A gente configura
> isso quando o negócio crescer.

---

## PASSO 12 — Atualizar o sistema no futuro (novas versões)

Sempre que você (nós) fizer melhorias e der merge no GitHub, atualize o servidor:

```bash
cd /var/www/ateliepro
git pull origin main
composer install --no-dev --optimize-autoloader
php artisan migrate --force
php artisan config:cache
systemctl reload nginx
```

---

## 🔐 Logins de demonstração (senha: `password`)

- **Super Admin (você):** `admin@ateliepro.com`
- **Loja Bella Noivas:** `bella@ateliepro.com`
- **Loja Elegance:** `elegance@ateliepro.com`

> Para dados reais da Andreia, crie a loja dela pelo painel de Super Admin
> (ou peça que eu prepare um seeder só com a loja dela, sem os dados de demo).

---

## 🆘 Se der problema

- **Erro 500 na tela** → quase sempre falta `APP_KEY` (Passo 7) ou permissão de
  pasta. Rode de novo o `chown`/`chmod` do Passo 7 e veja o log:
  `tail -50 /var/www/ateliepro/storage/logs/laravel.log`
- **Página em branco / 502** → o PHP-FPM pode estar parado:
  `systemctl restart php8.4-fpm nginx`
- **Não abre pelo domínio** → o DNS ainda está propagando (aguarde) ou o
  registro A está com o IP errado.

Manda print do erro que eu te ajudo a resolver. 💪
