# Deploy no cPanel

## Configuracao recomendada

- PHP 8.1 ou superior.
- Extensoes PHP: `pdo`, `pdo_mysql`, `mbstring`, `fileinfo`, `json`, `openssl`.
- Banco MySQL/MariaDB criado pelo cPanel.
- SSL ativo para o dominio.

## Arquivos para subir

Suba:

- `app/`
- `config/`
- `database/`
- `public/`
- `resources/`
- `storage/component_types.json`
- `storage/uploads/.gitkeep`
- `vendor/`
- `.env`
- `.htaccess`
- `composer.json`
- `composer.lock`
- `package.json`
- `package-lock.json`
- `tailwind.config.js`

Nao suba arquivos locais de banco/log:

- `storage/database.sqlite`
- `storage/server.log`
- `storage/server.err`
- arquivos reais dentro de `storage/uploads/`, exceto `.gitkeep`
- `node_modules/`
- `.git/`
- `tests/`

## .env de producao

```env
APP_ENV=production
APP_DEBUG=false
APP_URL=https://aazizrepresentacoes.com.br

DB_CONNECTION=mysql
DB_HOST=localhost
DB_PORT=3306
DB_DATABASE=SEU_BANCO
DB_USERNAME=SEU_USUARIO
DB_PASSWORD=SUA_SENHA

SESSION_NAME=meu_estoque_session
```

## Banco

Importe `database/schema.sql` no phpMyAdmin.

## CSS

Antes de enviar os arquivos, rode localmente:

```bash
npm install
npm run build:css
```

Suba o arquivo gerado em `public/assets/css/app.css`.

## Document root

Ideal: apontar o dominio para a pasta `public/`.

Se o cPanel nao permitir alterar o document root, suba o projeto inteiro fora de `public_html` e copie o conteudo de `public/` para `public_html/`, ajustando os caminhos de `index.php` conforme a estrutura usada.

## Permissoes

Garanta escrita em:

- `storage/`
- `storage/uploads/`

Permissoes comuns:

- Pastas: `755`
- Arquivos: `644`

Se uploads falharem, use `775` em `storage/uploads/`.
