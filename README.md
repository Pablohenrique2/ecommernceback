# Integração Trackcash
Projeto de Integração com os marketplaces.

## Arquitetura 

> Requisitos mínimos
- [PHP 8^](https://www.php.net)
- [Mysql](https://www.mysql.com)
- [Composer](https://getcomposer.org)

> Frameworks utilizados
- [Laravel 9](https://laravel.com/docs/7.x)

> Softwares recomendados
- [VSCode](https://code.visualstudio.com)
- [Git](https://git-scm.com)
***OBS: Não esqueça de configurar suas credenciais no git caso ainda não tenha***
 ```bash
  git config --global [user.name](http://user.name/) "Nome Sobrenome"
```
    
```bash
 git config --global user.email "seu-email@email.com"
```

## Instalação - WINDOWS

```sh
git git@github.com:Pablohenrique2/ecommernceback.git
```

```sh
cd ecommerceback
```

- Instalar as dependências

```sh
composer install --ignore-platform-reqs
```

- Duplicar o arquivo **.env.example** e renomear a copia para **.env**

```sh
  php -r "copy('.env.example', '.env');"
```
- Logo depois execute o comando abaixo para gerar uma nova chave
```PHP
php artisan key:generate
```
- Criar as tabelas no banco e popular com os dados default

```sh
php artisan migrate --seed
```

- Subir o servidor

```sh
php artisan serve
```

- Verificar se a Integração está online acessando [http://localhost:8000](http://localhost:8000)

## Complementos

Para poder saber de outras coisas que usamos dentro do laravel, de uma conferida nas outras documentações abaixo:


| Título  |  Links  |
|---|---|
| **Laravel Mix** | [Link](https://www.notion.so/Laravel-Mix-7cb7ebed452f48ba9dee326d3a2d93b0) |
| **Padrão de projetos Laravavel** | [Link](https://www.notion.so/Padr-o-de-projetos-laravel-9dbeafb26f7b4af29ba9d55998947715) |
| **Instalacao do MySQL** | [Link](https://www.notion.so/Instala-o-do-Mysql-PT-BR-5057da117fed457abedf018c35770d27) |

---
