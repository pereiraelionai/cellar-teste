## Descrição

Este projeto é uma aplicação web desenvolvida em Laravel que fornece um sistema capaz de criar e gerenciar categorias e produtos. Além disso, há dois tipos de usuários: Administrador e Usuário padrão.

Ao realizar um cadastro na página de cadastro, será criado um usuário Administrador, que após logado poderá criar outros usuários com perfil Usuário para acessarem o sistema. Para realizar o primeriro login, é necessário acessar o email informado para confirmaçao do cadastro.

O administrador tem permissão para gerenciar usuários, gerenciar permissões, criar, editar e excluir categorias e produtos. Por outro lado, o usuário padrão terá suas permissões conforme determinados pelo usuário Administrador.

# Recursos

- Autenticação de usuários e confirmação de cadastro via email;
- Dashboard com resumo das informações;
- Gerenciamento de categorias e produtos, o que inclui, criar, editar e excluir;
- Gerencimento de permissão dos usuários;
- Resetar e mudar a senha atráves do esqueci minha senha.

OBS. Quando o usuário Administrador cria um outro usuário na página de gerenciamento de usuário, esse usuário recebe a **senha padrão: Cellar@123**

## Instalação

Após clonar ou realizar o download do projeto atráves do repositório do [GitHub](https://github.com/pereiraelionai/cellar-teste), siga os passos abaixo:

1. Instale as dependências do composer - (composer install);
2. Instale as dependências do npm, esse passo é necessário pois o sistema utiliza os recursos de autenticação do laravel/ui - (npm install);
3. Copie as informações do arquivo .env.example para o arquivo .env - (apenas substitua o DB_USERNAME e o DB_PASSWORD com as informações de acesso do seu DB);
4. No banco de dados Mysql crie o database cellar - (CREATE DATABASE cellar);
5. Execute as migrações - (php artisan migrate);
6. Execute php artisan serve;
7. Execute npm run dev.

O sistema estará disponível em http://localhost:8000

## Recursos Necessários

- PHP ^8.1;
- Node ^18;
- Mysql ^10;
