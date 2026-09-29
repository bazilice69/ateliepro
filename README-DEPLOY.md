# 🚀 Deploy do AteliêPro no Render (grátis) — passo a passo

Guia para colocar o sistema no ar de graça, para a sua amiga testar 24h sem
depender do seu PC ligado.

> ⚠️ **Importante:** o plano gratuito é ótimo para TESTE. O site "dorme" após
> ~15 min sem uso (o 1º acesso depois demora ~30s para "acordar") e o banco
> gratuito expira em ~90 dias. Quando ela decidir usar de verdade com clientes,
> migramos para uma hospedagem paga baratinha.

---

## Passo 1 — Criar conta no Render
1. Acesse **https://render.com** e clique em **Get Started**.
2. Escolha **Sign in with GitHub** (use a conta `bazilice69`).
3. Autorize o Render a acessar seus repositórios.

## Passo 2 — Criar o Blueprint (usa o render.yaml do projeto)
1. No painel do Render, clique em **New +** → **Blueprint**.
2. Selecione o repositório **`ateliepro`**.
3. O Render vai detectar o arquivo `render.yaml` e mostrar o que vai criar:
   - Um **Web Service** (o sistema)
   - Um **PostgreSQL** (o banco)
4. Clique em **Apply** / **Create**.

## Passo 3 — Gerar a APP_KEY
O Laravel precisa de uma chave de segurança. Gere uma:
- No seu PC, no terminal do projeto, rode:
  ```bash
  php artisan key:generate --show
  ```
- Vai aparecer algo como `base64:XXXXXXXX...`. **Copie tudo.**
- No Render: abra o serviço **ateliepro** → aba **Environment** → encontre
  `APP_KEY` → cole o valor → **Save Changes**.

## Passo 4 — Definir a APP_URL
- Ainda em **Environment**, encontre `APP_URL`.
- Coloque o endereço que o Render deu ao seu site, ex:
  `https://ateliepro.onrender.com` → **Save Changes**.

## Passo 5 — Deploy
- O Render faz o deploy automaticamente após salvar.
- Se precisar, clique em **Manual Deploy** → **Deploy latest commit**.
- Acompanhe os logs. Quando terminar, acesse a URL do site. 🎉

---

## Logins de demonstração (senha: `password`)
- **Super Admin (você):** `admin@ateliepro.com`
- **Loja Bella Noivas:** `bella@ateliepro.com`
- **Loja Elegance:** `elegance@ateliepro.com`

Mande o link `https://ateliepro.onrender.com` + o login `bella@ateliepro.com`
para sua amiga testar no celular dela. 📲

---

## Dúvidas comuns
- **"O site demorou pra abrir"** → é o plano grátis "acordando". Normal.
- **"Deu erro 500"** → geralmente falta a `APP_KEY` (Passo 3) ou a `APP_URL`.
  Confira e faça um novo deploy.
- **"Perdi os dados"** → o banco grátis expira em ~90 dias; para teste, é só
  recriar. Para dados reais, use hospedagem paga.
