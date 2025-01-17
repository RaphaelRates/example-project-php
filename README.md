# sistema-armazenamento-de-questoes-obs

Este repositório destina-se ao sistema de armazenamento de questões e alunos/jurados da OBS.

## Como contribuir

Siga estas etapas para contribuir para o projeto:

1. Clone o repositório, instale as dependências e execute no docker:
   ```shell
   git clone git@github.com:Calangio/sistema-armazenamento-de-questoes-obs.git sistema-armazenamento-de-questoes-obs
   cd sistema-armazenamento-de-questoes-obs
   composer install
   cd docker
   ```
2. Crie seu arquivo de variável de ambiente na raiz do projeto com as seguintes variáveis:
    ```shell
   DB_HOST= mariadb
   DB_NAME=
   DB_USER=
   DB_PASSWORD=
   DB_PORT=
   DB_ROOT_PASSWORD=
   ```
3. Eecutar o docker usando o arquivo ***docker-compose.ylm***
   ```shell
   docker-compose --env-file ../.env up -d
   ```
4. Verifique se está rodando localmente e sua maquina acessando o caminha a seguir:
OBS: caso esteja dando algum erro, é porque ele carrega as variáveis depois de 7 segundos, deve ser algum erro de tempo de conexão com o banco de dados mariadb
   ```shell
      http://localhost:8000
      ```
6. Crie uma issue com uma branch no repositório.
7. Faça checkout para a branch da issue criada:
   ```shell
   git checkout minha-branch-issue
   ```
8. Faça suas alterações locais.
9. Faça commit de suas alterações seguindo o [Conventional Commits](https://www.conventionalcommits.org/pt-br/v1.0.0/).
   ```shell
   git commit -m <type>[optional scope]: <description>
   ```
10. Envie suas alterações para a branch da issue.
   ```shell
   git push origin minha-branch-issue
   ```

Certifique-se de seguir o padrão de commits mencionado para garantir que os commits sejam registrados corretamente no repositório, uma vez que estamos usando `husky + commitlint` para impedir formatos indesejados. Veja alguns exemplos:


```shell
git commit -m "feat: permitir que o objeto de configuração fornecido estenda outras configurações"
```

```shell
git commit -m "chore!: remove suporte para Node 6"
```

```shell
git commit -m "docs: ortografia correta de CHANGELOG"
```

Segue os tipos de alterações possíveis: `[build, chore, ci, docs, feat, fix, perf, refactor, revert, style, test]`


## Estrutura do repositório

```shell
$ tree --dirsfirst --gitignore
.
├── app
│   └── controllers
│   └── models
│   |     └── Entity
│   |     └── Session
│   └── views
│   |     └── components
│── bootstrap
│   └── bootstrap.php
│── core
│   ├── helpers
│   │   └── Helper.php
│   ├── Http
│   │   ├── MiddleWare
│   │   │   └── Queue.php
│   │   │   └── Maintenance.php
│   │   └── Request.php
│   │   └── Response.php
│   │   └── Router.php
│   ├── Config.php
│   ├── Controller.php
│   ├── Database.php
│   ├── Model.php
│   ├── View.php
│── docker
│   ├── docker-compose.yml
│   ├── Dockerfile.php
│   └── Dockerfile.mariadb
│── public
│   ├── assets
│   ├── css
│   ├── js
│   ├── .htaccess
│   └── index.php
├── routes
│   ├── api (ainda a discutir)
│   │   └── v1 (ainda a discutir)
│   │   │   └── defalut.php (ainda a discutir)
│   ├── admin.php
│   ├── api.php (ainda a discutir)
│   └── routes.php
│── vendor
│   └── Todas as dependencias, aparecerá quando instalar as dependências
│── .env
│── .env.example
│── .gitignore
│── composer.json
│── compose.lock
└── README.md

```

É importante que siga o padrão mostrado a cima:

`app > controllers` são os controlladores do projeto...Responsáveis por executarem certas funções e retornar a view dependendo da sua rota e chamada.

`app > models` são as classes de modelos dos itens presentes no projeto.

`app > models > Entity` são as classes de modelos dos itens presentes no banco de dados.

`app > models > Session` são as classes de modelos de sessios de cada objeto do projeto.

`app > views ` cada página do projeto. Atualmente usamos apenas a ***Home***,***About***,***Contact*** e a ***SingleUser***. Entretanto elas estão como demonstração. Elas possuem uma rota raiz chamada ***page***. 

`app > views > compoenents` são os compoenentes mais específicos e reutilizáveis no projeto.

`app > bootsrtap` cada página do projeto. Atualmente usamos apenas a ***Home***,***About***,***Contact*** e a ***SingleUser***. Entretanto elas estão como demonstração. Elas possuem uma rota raiz chamada ***page***. 

`app > core` são as classes do núcleo da nossa aplicação, a base de todo o nosso projeto.

`app > core > Http` são as classes do núcleo da nossa aplicação responsáveis por monitorar e controlar as nossas requisições, respostas e MiddleWares http.

`app > core > Http > MiddleWare` são as classes de Middlewares presentes nosso projeto e a classe de fila de middlewares mapeaveis.

`app > docker` são os arquivos relacionados a configurações de Conteiners para rodar a aplicação, no caso o Php_Apache e o mariaDB.

`app > public` são os arquivos pricipais do peojto, como o ***index.php***, ***estilização css***, configuração de URLs Amigáveis, ***asstes*** e ***javascript***.

`app > routes` são os arquivos que armazenam as rotas da aplicação.

`app > routes` todas as dependências do nosso projeto.
