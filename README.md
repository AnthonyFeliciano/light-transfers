# Light Transfers — Guia de Configuração & Execução

Este README explica **do zero** como preparar o ambiente, configurar dependências e rodar a aplicação (**Laravel + Livewire + Tailwind + filas**), além de como executar migrations, seeds, testes, build de assets e o worker de filas.

---

## Pré-requisitos

- **PHP 8.2+** com extensões: `mbstring`, `bcmath`, `intl`, `pdo_mysql`
- **Composer 2+**
- **Node 20+** e **npm 9+**
- **MySQL 8+** (local)
- Nos **testes/CI** usamos **SQLite**
- Extensões PHP para testes/CI: `pdo_sqlite`, `sqlite3`

---

## Primeira execução (dev local)

```bash
# 1) Clonar & instalar dependências
git clone <https://github.com/AnthonyFeliciano/light-transfers.git> light_transfers
cd light_transfers
composer install
npm ci

# 2) Copiar .env e gerar chave
cp .env.example .env
php artisan key:generate

# 3) Configurar .env (veja seção abaixo) e criar DB MySQL
#    Depois rode as migrations + seeds
php artisan migrate --seed

# 4) Rodar app + assets (em 2 terminais separados)
php artisan serve
npm run dev   # Vite (HMR) / Tailwind
```

---

## Configuração do `.env`

### App / Locale / Timezone
```env
APP_NAME=light_transfers
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost
APP_KEY=base64:...

APP_LOCALE=pt_BR
APP_FALLBACK_LOCALE=pt_BR
APP_FAKER_LOCALE=pt_BR
APP_TIMEZONE=America/Sao_Paulo
```

### Banco (desenvolvimento com MySQL)
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=light_transfers
DB_USERNAME=root
DB_PASSWORD=
```

> Testes/CI usam **SQLite**, então não conflita com o MySQL local.

### Fila / Cache / Sessão
```env
QUEUE_CONNECTION=database
CACHE_STORE=file
SESSION_DRIVER=file
```

Se ainda não criou as tabelas de fila:

```bash
php artisan queue:table
php artisan migrate
```

### E-mail / Log
```env
MAIL_MAILER=log
LOG_CHANNEL=stack
LOG_LEVEL=debug
```

---

## Tecnologias & Pacotes usados

- **Laravel** (API + Jobs + Eloquent + validação)
- **Livewire** (componentes reativos no Blade)
- **Tailwind CSS 4 + Vite**
- Pacotes: `tailwindcss`, `@tailwindcss/vite`, `vite`, `laravel-vite-plugin`
- **Queues** (driver database)
- **HTTP Client** (jobs/serviços externos)
- **Testes**: PHPUnit + Livewire Testing

---

## Front-end (Vite + Tailwind + Livewire)

### Estrutura básica
- **CSS de entrada**: `resources/css/app.css`
  ```css
  @import "tailwindcss";
  /* seus CSS utilitários/complementos aqui */
  ```

- **JS de entrada**: `resources/js/app.js` (opcional)

No layout Blade:
```blade
@vite(['resources/css/app.css', 'resources/js/app.js'])
@livewireStyles
...
@livewireScripts
```

### Dev & Build
```bash
npm run dev     # HMR (desenvolvimento)
npm run build   # gera /public/build (produção)
```

---

## Migrations

```bash
php artisan migrate
# ou
php artisan migrate:fresh --seed
```

### Modelos principais
- `users` (com role = user/merchant, documento como string)
- `wallets` (saldo do usuário)
- `transfers` (transferências)
- `ledger_entries` (lançamentos: debit/credit)
- `notifications` (notificação assíncrona)

> Se seus modelos usam UUID:
```php
protected $keyType = 'string';
public $incrementing = false;
```

---

## Seeds & Factories

### Seeds
- **UserWalletSeeder**: cria 10 usuários comuns e 10 lojistas com saldo inicial.

```bash
php artisan db:seed --class=UserWalletSeeder
# ou
php artisan db:seed
```

### Factories
- `UserFactory`, `WalletFactory`, `TransferFactory`, `NotificationFactory`

---

## Filas (Jobs)

Usamos driver `database`:

```bash
php artisan queue:table
php artisan migrate
php artisan queue:work       # inicia o worker
php artisan queue:work --tries=3 --backoff=10
```

### Job principal
- **SendTransferNotificationJob**
  - POST em `https://util.devi.tools/api/v1/notify`
  - Atualiza `notifications.status`, incrementa `attempts`, registra `last_error`
  - Em falha: `release(60)` (reagenda)
  - Em produção, rode via Supervisor/PM2 (Linux) ou serviço no Windows

---

## Services & Contratos

- **TransferServiceContract** + implementação `TransferService`
  - Regras de negócio (saldo, papéis, idempotência)
  - Persiste `transfers` e `ledger_entries`
  - Cria notificações pendentes

- **AuthorizationClientContract**
  - Cliente que chama `AUTHORIZER_URL` para aprovar/recusar

- **Livewire (Transfer\Wizard)**
  - `findPayee` → busca destinatário
  - `validateAmount` → garante saldo
  - `confirm` → chama autorização externa + executa serviço

---

## Testes

### Rodando testes
```bash
php artisan test
php artisan test -v
```

### Banco de dados (SQLite)
```bash
cp .env .env.testing
# Ajuste:
APP_ENV=testing
DB_CONNECTION=sqlite
DB_DATABASE=:memory:
```

### Cobertura dos testes
- **TransferService**: saldos, ledger, idempotência, exceções
- **Job de Notificação**: status, attempts, last_error
- **Livewire Wizard**: fluxos de confirmação, toasts, validações

---

## CI (GitHub Actions)

Workflow de CI:
- Instala PHP 8.2 + extensões
- Composer install (com cache)
- `.env.testing` com SQLite
- Migrations
- Node 20 + `npm ci`
- `npm run build`
- Roda testes

---

## Comandos úteis

```bash
# Limpezas
php artisan optimize:clear
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

# Tinker
php artisan tinker

# Gerar artefatos
php artisan make:model Wallet -mf
php artisan make:seeder UserWalletSeeder
php artisan make:migration add_xyz_to_table --table=table
```

---

## Dicas & Notas

- **CPF/CNPJ**: use string para preservar zeros à esquerda  
- **Moedas**: use `decimal(18,2)` e funções `bc*`  
- **Wizard UX**: mensagens claras, exceções geram toast genérico + report  

---

## Resumo rápido (como rodar)

```bash
composer install && npm ci
cp .env.example .env
php artisan key:generate
php artisan migrate --seed

# Terminais
php artisan serve
npm run dev
php artisan queue:work
```

Pronto! O sistema já estará no ar com **Livewire + Tailwind**, filas ativas e seeds criados para teste.
