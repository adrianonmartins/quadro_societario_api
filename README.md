# Sistema de Gestão de Empresas - Quadro Societario

## Descrição
Este projeto é um sistema de gestão para empresas e seus quadros societários. Ele permite o cadastro e gerenciamento de empresas e sócios, além de suportar autenticação e autorização via API.

## Tecnologias Utilizadas
- **PHP**: 8.3.8
- **Symfony**: Framework PHP para desenvolvimento de aplicações web.
- **PostgreSQL**: Banco de dados relacional.
- **Doctrine ORM**: Mapeamento objeto-relacional para PHP.
- **Swagger**: Documentação e testes da API.

## Instalação

Para configurar e rodar o projeto localmente, siga os passos abaixo:

1. **Clone o repositório:**
   ```bash
   git clone https://gitlab.com/usuario/projeto.git

2. Navegue até o diretório do projeto:
  cd projeto

4. Instale as dependências:
   composer install
   
5.Configure o banco de dados:
  DATABASE_URL="postgresql://dev:66986703@127.0.0.1:5432/quadro_societario?serverVersion=15&charset=utf8"
  
6. Crie o banco de dados e execute as migrations:
   php bin/console doctrine:database:create
   php bin/console doctrine:migrations:migrate

## Uso
  Acesse a aplicação em http://localhost:8000

Para interagir com a API, utilize ferramentas como Postman ou cURL.

Endpoints da API
Criar Empresa
URL: /api/empresas
Método: POST
Corpo da Requisição:
  json
  {
    "razaoSocial": "Nome da Empresa",
    "cnpj": "12345678000199"
  }
  
Resposta:
  json
  Copiar código
  {
      "id": 1
  }

Obter Empresas
URL: /api/empresas
Método: GET
Resposta:
  json
  [
    {
      "id": 1,  
      "razaoSocial": "Nome da Empresa",
      "cnpj": "12345678000199"
    }
  ]

### Agora acesse a documentação API em seu navegador
[http://localhost:8000/api/doc](http://localhost:8000/api/doc)