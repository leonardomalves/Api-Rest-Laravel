# Gerenciamento de Produtos - API Laravel

Este projeto é uma API desenvolvida em Laravel para gerenciar produtos. Ele fornece endpoints RESTful para criar, listar, atualizar e deletar produtos.

## 📌 Requisitos

Antes de iniciar, verifique se você tem os seguintes requisitos instalados:

- PHP 8.3 ou superior
- Composer
- MySQL
- Redis (opcional, para cache)
- Extensões PHP necessárias: `pdo`, `mbstring`, `openssl`, `tokenizer`, `xml`, `ctype`, `json`, `bcmath`, `fileinfo`

## 🚀 Instalação

Siga os passos abaixo para configurar e rodar a aplicação:

### 1️⃣ Instalar o PHP e o Composer

#### 📌 Ubuntu
```bash
sudo apt update && sudo apt upgrade -y
sudo apt install -y php-cli php-mbstring php-xml php-bcmath php-zip php-tokenizer php-pdo php-mysql unzip curl git
curl -sS https://getcomposer.org/installer | php
sudo mv composer.phar /usr/local/bin/composer
```

#### 📌 Windows
Baixe e instale:
- [PHP 8.3+](https://windows.php.net/download/)
- [Composer](https://getcomposer.org/download/)

Adicione o `php` e o `composer` ao PATH do sistema.

### 2️⃣ Clonar o repositório
```bash
git clone git@github.com:leonardomalves/Api-Rest-Laravel.git code-project
cd code-project
```

### 3️⃣ Instalar as dependências
```bash
composer install
```

### 4️⃣ Criar o arquivo de configuração
```bash
cp .env.example .env
```
Edite o arquivo `.env` e configure o banco de dados:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=seu_banco
DB_USERNAME=seu_usuario
DB_PASSWORD=sua_senha
```

### 5️⃣ Gerar a chave da aplicação
```bash
php artisan key:generate
```

### 6️⃣ Executar as migrations e seeds (opcional)
```bash
php artisan migrate --seed
```

### 7️⃣ Configurar permissões
```bash
chmod -R 777 storage bootstrap/cache
```

### 8️⃣ Inicializar o servidor Laravel
```bash
php artisan serve
```
A API estará disponível em: [http://127.0.0.1:8000](http://127.0.0.1:8000).

---

## 📌 Rotas da API

| Método HTTP  | Caminho                   | Método do Controller  | Finalidade                   |
|-------------|---------------------------|-----------------------|------------------------------|
| GET         | api/products               | index                 | Listar produtos              |
| POST        | api/products               | store                 | Criar um novo produto        |
| GET         | api/products/{product}     | show                  | Exibir um produto específico |
| PUT/PATCH   | api/products/{product}     | update                | Atualizar um produto         |
| DELETE      | api/products/{product}     | destroy               | Deletar um produto           |

---

## 🔥 Extras

- Para rodar o servidor de fila (caso necessário):
  ```bash
  php artisan queue:work
  ```

- Para limpar o cache:
  ```bash
  php artisan cache:clear
  php artisan config:clear
  php artisan route:clear
  php artisan view:clear
  ```


