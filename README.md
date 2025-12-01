# Proporção Áurea – Inscrições de Cursos

Site simples em PHP/MySQL para gerir inscrições dos cursos de artesanato em resina, com geração de PDF de pré-inscrição, upload de comprovativos e área administrativa.

## Configuração
1. Crie a base de dados e rode o script `database.sql`.
2. Defina as credenciais em `config.php` (DB_HOST, DB_NAME, DB_USER, DB_PASS) e ajuste `PAYMENT_DETAILS`.
3. Crie um utilizador admin na tabela `admin_users` com `password_hash` gerado por `password_hash('senha', PASSWORD_DEFAULT)`.
4. Garanta permissão de escrita para a pasta `storage/` (já com subpastas de inscrições e comprovativos).

## Estrutura de páginas
- Público: `index.php`, `cursos.php`, `inscricao.php`, `obrigado.php`, `comprovativo.php`.
- Admin: `/admin/login.php`, `/admin/index.php`, `/admin/cursos.php`, `/admin/curso_form.php`, `/admin/inscricoes.php`, `/admin/inscricao_view.php`.

As pré-inscrições geram PDF com FPDF (incluído em `libs/`). O comprovativo muda o status para `em_analise`; o admin pode atualizar para `pago` ou `cancelado`.
