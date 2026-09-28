# Regra de Multi-Tenancy do AteliêPro

O AteliêPro é um SaaS **multi-loja**. O isolamento de dados por loja é uma
regra **inegociável**: uma loja NUNCA pode ver dados de outra.

## Como o isolamento funciona

- Toda tabela de negócio tem uma coluna `loja_id`.
- Models de negócio usam a trait `App\Models\Concerns\BelongsToLoja`, que:
  1. Aplica um **Global Scope** filtrando automaticamente toda query pela
     `loja_id` do usuário autenticado (isolamento no BACKEND).
  2. Preenche `loja_id` automaticamente ao criar registros.
- O `super_admin` (dono do SaaS) é **isento** do scope e enxerga todas as lojas.

## Regras para novo código

1. **Todo novo model de negócio** deve usar a trait `BelongsToLoja` e ter
   `loja_id` na migration.
2. **Nunca** confie em `loja_id` vindo de formulário/request. A trait injeta.
3. Validações `unique` devem ser **por loja**:
   `Rule::unique('tabela','coluna')->where('loja_id', $lojaId)`.
4. Validações `exists` que referenciam outra tabela de negócio devem ser
   **escopadas por loja**:
   `Rule::exists('tabela','id')->where('loja_id', $lojaId)`.
5. Use `Model::findOrFail($id)` normalmente — o scope garante 404 para IDs de
   outra loja. Não use `DB::table()` cru em dados de negócio (fura o scope).
6. Para o super_admin/relatórios globais, use os escopos explícitos
   `->todasAsLojas()` ou `->daLoja($id)`.

## Categorias financeiras

São **por loja** (opção B): cada loja tem sua própria lista. Ao criar uma loja,
popular com `CategoriaFinanceiraSeeder::popularParaLoja($lojaId)`.

## Como testar o isolamento

```bash
php artisan migrate:fresh
php artisan db:seed --class=DemoMultiTenantSeeder
```

Logins de teste (senha: `password`):
- `admin@ateliepro.com` — super_admin (vê tudo)
- `bella@ateliepro.com` — admin da Bella Noivas
- `elegance@ateliepro.com` — admin da Elegance Noivas

Teste: logue como `bella@` e tente abrir um cliente/peça da Elegance pela URL
(ex.: `/clientes/ID_DA_ELEGANCE`). Deve retornar **404** — nunca os dados.


## Models tenant-scoped (usam BelongsToLoja)

Cliente, Acervo, Locacao, Agendamento, Evento, Medida, CategoriaFinanceira,
LancamentoFinanceiro e **Assinatura**.

NÃO são tenant-scoped (globais do SaaS): User, Loja, Plano, Setting, AccessLog.
No webhook do Mercado Pago (sem usuário logado) usa-se
`Assinatura::withoutGlobalScope('loja')` para localizar a assinatura paga.
