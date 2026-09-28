# 📖 AteliêPro — Estudo de Mercado & Roadmap de Funcionalidades

> Estudo do segmento de **ateliês, alfaiatarias e lojas de noivas/festa** (foco no público
> da região da Luz / centro de SP), cruzando o que o mercado precisa com o que o AteliêPro
> **já tem**, o que **pode melhorar** e o que **pode inovar**.
>
> **Premissa combinada:** manter a maior parte do que já foi construído. As sugestões abaixo
> são incrementais — nada aqui exige jogar fora o que existe.

---

## 1. Contexto do público-alvo

Lojas de noivas/festa e ateliês do centro de SP (Luz, 25 de Março, Santa Ifigênia) atendem um
público muito específico e, em boa parte, ainda operam com **papel, caderno e planilha**. Os
tipos de cliente e ocasião que o sistema precisa cobrir bem:

| Perfil de cliente | Ocasião típica | Necessidade principal |
|---|---|---|
| **Noiva** | Casamento | Vestido (venda sob medida ou aluguel), várias provas, ajustes |
| **Noivo / padrinhos** | Casamento | Terno/traje alugado ou sob medida, alfaiataria |
| **Madrinhas** | Casamento | Vestidos de festa coordenados (mesma cor/tecido) |
| **Debutante** | Festa de 15 anos | Vestido de valsa + troca (2º vestido), acessórios |
| **Daminha / pajem** | Casamento/festa | Traje infantil, medidas de criança |
| **Cliente de festa** | Formatura, madrinha, convidada | Aluguel ou venda de vestido de festa |

**Característica-chave do negócio:** quase tudo gira em torno de uma **DATA DE EVENTO fixa e
inadiável**. O casamento não pode atrasar porque o ajuste não ficou pronto. Isso muda a lógica
de todo o sistema: os prazos devem ser **contados de trás pra frente a partir da data do evento**.

---

## 2. Jornada real do cliente (o fluxo que o sistema precisa apoiar)

```
1. PRIMEIRO CONTATO / AGENDAMENTO
   Cliente liga/WhatsApp/passa na loja → agenda o primeiro atendimento

2. ATENDIMENTO INICIAL (consultoria)
   Prova de modelos, escolha da peça, orçamento → decisão

3. FECHAMENTO + SINAL
   Assina contrato, paga sinal/entrada → peça reservada para a data do evento

4. MEDIDAS / FICHA TÉCNICA
   Tira medidas, registra ficha → vai para a oficina (se sob medida/ajuste)

5. PROVAS E AJUSTES (2 a 3 rodadas)
   Prova 1 → ajusta → Prova 2 → ajusta → Prova final ("prova de tudo")

6. RETIRADA
   Cliente retira a peça pronta, quita o saldo

7. (ALUGUEL) DEVOLUÇÃO
   Devolve → vistoria → lavanderia → libera caução → peça volta ao acervo
```

> 💡 **Insight de mercado:** sistemas líderes (ex.: RingUps, CloudBridal) organizam TUDO em
> torno de manter esse fluxo "sem buracos" — nenhum ajuste esquecido, nenhum prazo estourado,
> nenhum sinal perdido. Esse é o verdadeiro valor percebido pela lojista.
> *Conteúdo baseado em material público desses fornecedores, reformulado para este estudo.*

---

## 3. O que o AteliêPro JÁ TEM (e está muito bom ✅)

O projeto está **mais maduro do que a maioria dos concorrentes de entrada**. Já existe:

| Módulo | O que já cobre | Avaliação |
|---|---|---|
| **Clientes** | Dados pessoais, CPF, telefone, endereço completo, tipo (Fem/Masc/Infantil), observações | ✅ Sólido |
| **Produtos/Acervo** | Código interno, categoria (Noiva/Festa/Debutante), tamanho, cor, valor de locação, estoque, foto | ✅ Bom |
| **Medidas (ficha técnica)** | Altura, peso, manequim, calçado, salto, busto/tórax, cintura, quadril, ombro, pescoço, braço, manga, entrepernas, comprimento, sob medida | ✅ **Excelente** — cobre fem/masc/infantil |
| **Agenda/Agendamentos** | Data/hora, duração, tipo (1º atendimento/Prova 1/Retirada/Devolução), **salas**, **checklist de atendimento** | ✅ **Muito bom** |
| **Eventos** | Nome do evento, data, tipo (Casamento/15 anos/Formatura), status | ✅ Bom — já centra no evento |
| **Locação** | Retirada, data do evento, devolução, **liberação prevista (+lavanderia)**, valor total, sinal pago, status | ✅ **Excelente** — já pensa em lavanderia |
| **Financeiro** | Categorias e lançamentos financeiros | ✅ Base sólida |
| **Multi-tenant (SaaS)** | Lojas, assinaturas, papéis (super_admin/admin_loja/funcionário), licença | 🟡 Estrutura pronta, falta ligar (ver checklist técnico) |

**Conclusão:** a fundação de negócio está muito bem pensada. O trabalho agora é **refinar**
alguns pontos e **adicionar os diferenciais** que fazem a lojista trocar o caderno pelo sistema.

---

## 4. O que pode MELHORAR (ajustes no que já existe)

Melhorias incrementais, sem reescrever nada:

### 4.1 Clientes
- ➕ Campo **e-mail** e **WhatsApp** (hoje só tem `telefone`) — WhatsApp é o canal nº 1 do público.
- ➕ Campo **"papel no evento"** (Noiva, Noivo, Madrinha, Daminha, Debutante, Convidada) — permite
  agrupar todo o "cortejo" de um mesmo casamento.
- ➕ Campo **"como conheceu a loja"** (Indicação, Instagram, Passou na frente...) — dado de ouro pra marketing.

### 4.2 Locação (o coração do aluguel)
- ➕ **Caução/depósito de segurança** (valor retido e devolvido após vistoria).
- ➕ **Vistoria na devolução** (estado da peça: OK / mancha / rasgo / falta acessório) + **multa** por dano/atraso.
- ➕ **Bloqueio de agenda da peça**: uma peça alugada para a data X não pode ser oferecida a outra cliente no mesmo período.

### 4.3 Medidas
- ➕ **Histórico de medidas por prova** (a cliente emagrece/engorda até o casamento — registrar a evolução dos ajustes).
- ➕ **Foto do croqui/modelo escolhido** anexada à ficha.

### 4.4 Produtos
- ➕ **Galeria de fotos** (hoje 1 foto só) — vestido bonito vende por foto.
- ➕ **Valor de venda** separado do valor de locação (peças que também são vendidas).

### 4.5 Financeiro
- ➕ **Sinal x saldo**: reconhecer que o **sinal só vira receita "de verdade" na entrega/retirada**
  (boa prática contábil dos sistemas líderes) — evita a lojista achar que "faturou" algo que
  ainda pode ser cancelado.
- ➕ **Contas a receber por evento** (parcelamento até a data do casamento).

---

## 5. O que pode INOVAR (diferenciais competitivos 🚀)

Estas são as funcionalidades que **transformam o AteliêPro num produto vendável** e o colocam
à frente do "sistema arcaico" e até de concorrentes:

### 5.1 ⭐ Lembretes automáticos por WhatsApp *(maior impacto)*
Estudos do setor apontam que lembretes por WhatsApp **reduzem faltas (no-show) entre 30% e 70%**,
tipicamente com uma sequência **24h antes + 2h antes** do compromisso.
*(Dados de mercado reformulados; fontes ao final.)*
- Lembrete automático de **prova**, **retirada** e **devolução**.
- Confirmação de presença ("responda SIM para confirmar").
- **Enorme dor resolvida:** cliente que esquece a prova = agenda furada = ajuste atrasado.

### 5.2 ⭐ Linha do tempo do evento (contagem regressiva)
Painel por evento mostrando, a partir da **data do casamento**, os prazos-limite:
"faltam 20 dias → 2ª prova pendente", "faltam 5 dias → peça ainda não liberada da lavanderia".
Inspirado no conceito de *special orders* dos líderes internacionais (contar de trás pra frente).

### 5.3 ⭐ Tela da Oficina / Workroom
Uma tela dedicada para a **costureira**, com a **fila de ajustes** (o que ajustar, em qual peça,
para qual prova, prazo). Poucos concorrentes têm isso — é um diferencial forte.

### 5.4 Painel do "cortejo" do casamento
Agrupar noiva + noivo + madrinhas + daminhas de um mesmo evento numa visão única (status de
cada traje, cor coordenada, prazos). Ninguém no segmento de entrada faz isso bem.

### 5.5 Contrato digital + assinatura
Gerar o contrato de locação/confecção em PDF já preenchido, enviar por WhatsApp/e-mail e
registrar aceite. Reduz papelada e dá segurança jurídica.

### 5.6 Catálogo/vitrine online (link público)
Página pública por loja (usando o multi-tenant já existente) com o acervo disponível e
**agendamento online** de prova — a cliente marca sozinha, cai direto na agenda.

### 5.7 Dashboard de aniversários e recompra
Avisar aniversários de clientes e "ex-debutantes" (que viram noivas anos depois) — reativação.

---

## 6. Comparativo rápido com o mercado

| Recurso | Sistemas "arcaicos" (papel/planilha) | Concorrentes comuns | **AteliêPro (atual + proposto)** |
|---|---|---|---|
| Cadastro de clientes/medidas | ❌ Caderno | ✅ | ✅ (ficha técnica rica) |
| Agenda de provas com salas | ❌ | 🟡 | ✅ |
| Controle de aluguel + lavanderia | ❌ | 🟡 | ✅ (+ caução/vistoria propostos) |
| Lembrete automático WhatsApp | ❌ | 🟡 raro | 🚀 **proposto** |
| Linha do tempo por evento | ❌ | ❌ | 🚀 **proposto** |
| Tela de oficina/workroom | ❌ | ❌ raro | 🚀 **proposto** |
| Multi-loja (SaaS revendável) | ❌ | 🟡 | ✅ estrutura pronta |

---

## 7. Priorização sugerida (o que fazer primeiro)

### 🥇 Fase 1 — Fechar o SaaS (técnico, já no checklist)
Terminar pagamento Mercado Pago, webhook, gerador de licença, middlewares e painel super admin.
**Sem isso não dá pra vender.** *(É o trabalho que já combinamos fazer no código.)*

### 🥈 Fase 2 — Diferenciais de alto impacto e baixo esforço
1. Campos extras em Clientes (e-mail, WhatsApp, papel no evento).
2. Caução + vistoria na devolução (Locação).
3. **Lembretes por WhatsApp** (o "uau" que vende o sistema).

### 🥉 Fase 3 — Diferenciais que encantam
4. Linha do tempo do evento (contagem regressiva).
5. Tela da oficina/workroom.
6. Contrato digital em PDF.

### 🏅 Fase 4 — Crescimento
7. Catálogo público + agendamento online.
8. Galeria de fotos, valor de venda, relatórios avançados.

---

## 8. Observação técnica encontrada durante o estudo ⚠️

Ao analisar o código, notei uma **migration de eventos duplicada**:
`2026_08_25_110057_create_eventos_table.php` **e** `2026_08_25_113437_create_eventos_table.php`
(as duas criam a tabela `eventos`). Isso pode causar erro ao rodar `migrate:fresh` do zero.
Vale revisar/remover a duplicada quando formos mexer no banco.

---

## 9. Fontes consultadas

- CloudBridal — funcionalidades para lojas de noivas (alterações, agendamentos): https://cloudbridal.com/features
- RingUps — sistema para boutiques de noivas (special orders, depósitos, oficina): https://ringups.com/
- Corsetta — software de alterações/oficina para noivas: https://www.corsetta.io/
- MyClosett — gestão de ateliê de aluguel (PT-BR): https://myclosett.app/
- SisRoupas — sistema para locação e venda de trajes: https://sisroupas.com.br/
- Dados sobre redução de no-show com lembretes WhatsApp: https://chatdaddy.tech/blog/whatsapp-appointment-reminder e https://pickyassist.com/blog/whatsapp-appointment-booking/

> *Conteúdo das fontes foi resumido e reformulado para fins deste estudo, respeitando as
> licenças de uso.*

---

*Documento gerado como estudo estratégico. Nada aqui altera o código — é material para
decisão. Quando você validar, transformamos os itens escolhidos em tarefas de implementação.*
