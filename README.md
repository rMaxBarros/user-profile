
# Projeto de Edição de dados




## 📝 Visão Geral do Projeto

O Sync-360 é uma aplicação de perfil de usuário simples e eficiente, desenvolvida com React para o frontend e PHP com MySQL para o backend. Ele permite aos usuários visualizar, editar e gerenciar suas informações de perfil, incluindo nome, idade, biografia, endereço e uma foto de perfil. O projeto foi arquitetado para ser de fácil deploy e manutenção, utilizando variáveis de ambiente para configurações específicas de ambiente.

### Principais Funcionalidades:

* Visualização de Perfil: Exibe as informações detalhadas do usuário.

* Edição de Perfil: Permite a atualização de dados como nome, idade, biografia, endereço e foto de perfil.

* Gerenciamento de Imagem: Upload de novas fotos de perfil e opção de remover a foto existente.

* Armazenamento de Dados: Persistência de dados em um banco de dados MySQL.

* API RESTful: Backend em PHP para manipulação dos dados.
## 🚀 Tecnologias Utilizadas

**Frontend:**
* **React:** Biblioteca JavaScript para construção de interfaces de usuário.
* **Vite:** Ferramenta de build rápida para projetos web modernos.
* **Axios:** Cliente HTTP baseado em Promises para fazer requisições à API.

**Backend:**
* **PHP:** Linguagem de script para o lado do servidor.
* **Composer:** Gerenciador de dependências para PHP.
* **Dotenv:** Biblioteca para carregar variáveis de ambiente de um arquivo `.env`.
* **MySQL:** Sistema de gerenciamento de banco de dados relacional.
## ⚙️ Pré-requisitos

Antes de começar, certifique-se de ter os seguintes softwares instalados em sua máquina:

* **XAMPP (ou similar):** Para fornecer o servidor MySQL e PHP.
    * Certifique-se de que o **Apache** (para acesso ao PHPMyAdmin) e o **MySQL** estejam rodando no XAMPP Control Panel.
* **Node.js e npm (ou Yarn):** Para o frontend React.
    * Você pode baixar em [nodejs.org](https://nodejs.org/).
* **Composer:** Para gerenciar as dependências do PHP.
    * Você pode baixar em [getcomposer.org](https://getcomposer.org/download/).
## 🖥️ Configuração e Execução do Projeto

Siga os passos abaixo para configurar e rodar o Sync-360 em sua máquina local.

### 1. Configuração do Banco de Dados (MySQL)

1.  **Inicie o Apache e o MySQL** através do seu XAMPP Control Panel.
• **Atenção, eles devem estar com o fundo verde!**

2.  Acesse o **PHPMyAdmin** no seu navegador (geralmente em `http://localhost/phpmyadmin/`).
3.  Crie um novo banco de dados chamado `db_usuarios`.
4.  Execute as seguintes queries SQL para criar a tabela `users`:

    ```sql
    CREATE TABLE IF NOT EXISTS `users` (
        `id` INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
        `nome` VARCHAR(255) NOT NULL,
        `idade` INT(11) NOT NULL,
        `bio` TEXT,
        `rua` VARCHAR(255) NOT NULL,
        `numero` VARCHAR(50) NOT NULL,
        `bairro` VARCHAR(255) NOT NULL,
        `cidade` VARCHAR(255) NOT NULL,
        `url_foto` VARCHAR(255) NULL
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
    ```
    *(Opcional) Para preencher com um usuário inicial se a aplicação não criar automaticamente:*
    ```sql
    INSERT INTO `users` (`id`, `nome`, `idade`, `bio`, `rua`, `numero`, `bairro`, `cidade`, `url_foto`) VALUES (1, 'Usuário Exemplo', 30, 'Esta é uma biografia de exemplo para testar o sistema.', 'Rua da Amostra', '100', 'Centro', 'Cidade Teste', NULL);
    ```

### 2. Configuração do Backend (PHP)

1.  Navegue até o diretório `backend/` do projeto no seu terminal.
    ```bash
    cd C:\seu\caminho\de\pastas\backend # Ajuste para o seu caminho real e vá até a pasta backend do projeto.
    ```
2.  Instale as dependências do Composer:
    ```bash
    composer install
    ```
3.  Crie um arquivo `.env` na raiz do diretório `backend/` (se ainda não existir). Você pode copiar o `backend/.env.example` para `backend/.env`.
    ```bash
    copy backend\.env.example backend\.env  # Windows
    cp backend/.env.example backend/.env    # macOS/Linux
    ```
4.  Abra o arquivo `backend/.env` e configure as variáveis de ambiente. A configuração abaixo é típica para XAMPP:

    ```dotenv
    # Variáveis de Configuração do Banco de Dados
    DB_HOST=localhost
    DB_NAME=db_usuarios
    DB_USER=root
    DB_PASSWORD= # Deixe vazio se não tiver senha para o root do MySQL no XAMPP

    # Configurações de Upload de Arquivos
    # Caminho físico RELATIVO ao diretório `public/` do seu backend.
    UPLOAD_DIR_RELATIVE=../uploads/

    # URL base para acessar as imagens de perfil via HTTP.
    # Esta URL aponta para o servidor PHP embutido e o diretório 'uploads/'.
    UPLOAD_BASE_URL=http://localhost:8000/uploads/

    # URL da imagem de perfil padrão
    DEFAULT_PROFILE_IMAGE=[https://t3.ftcdn.net/jpg/05/16/27/58/360_F_516275801_f3Fsp17x6HQK0xQgDQEELoTuERO4SsWV.jpg](https://t3.ftcdn.net/jpg/05/16/27/58/360_F_516275801_f3Fsp17x6HQK0xQgDQEELoTuERO4SsWV.jpg)
    ```
5.  Crie o diretório `uploads` na raiz do seu projeto (`sync-360/uploads/`). Este diretório será usado para armazenar as imagens de perfil carregadas. Certifique-se de que ele tenha permissões de escrita para o PHP (em sistemas Linux/macOS, `chmod 777 uploads`).

### 3. Configuração do Frontend (React)

1.  Navegue até o diretório `frontend/` do projeto no seu terminal.
    ```bash
    cd C:\seu\caminho\de\pastas\frontend # Ajuste para o seu caminho real e vá até a pasta frontend do projeto.
    ```
2.  Instale as dependências do Node.js:
    ```bash
    npm install
    # ou
    yarn install
    ```
3.  Crie um arquivo `.env` na raiz do diretório `frontend/` (se ainda não existir). Você pode copiar o `frontend/.env.example` para `frontend/.env`.
    ```bash
    copy frontend\.env.example frontend\.env # Windows
    cp frontend/.env.example frontend/.env   # macOS/Linux
    ```
4.  Abra o arquivo `frontend/.env` e configure a URL da API do backend. É crucial que esta URL aponte para o servidor PHP embutido que vamos iniciar.

    ```dotenv
    VITE_API_BASE_URL=http://localhost:8000
    ```

### 4. Iniciando os Servidores

Agora que tudo está configurado, você pode iniciar o backend e o frontend.

1.  **Inicie o Backend (Servidor PHP Embutido):**
    * Abra um **novo terminal**.
    * Navegue até a **raiz da sua pasta `backend/`**:
        ```bash
        cd C:\seu\caminho\de\pastas\backend # Ajuste para o seu caminho real
        ```
    * Execute o seguinte comando para iniciar o servidor PHP embutido, servindo a pasta `public/`:
        ```bash
        php -S localhost:8000 -t public/
        ```
    * Deixe este terminal aberto. Ele exibirá os logs do seu backend.

2.  **Inicie o Frontend (Servidor de Desenvolvimento React):**
    * Abra um **segundo terminal** (deixe o terminal do backend rodando no primeiro).
    * Navegue até a **raiz da sua pasta `frontend/`**:
        ```bash
        cd C:\seu\caminho\de\pastas\frontend # Ajuste para o seu caminho real
        ```
    * Inicie o servidor de desenvolvimento React:
        ```bash
        npm run dev
        # ou
        yarn dev
        ```
    * Seu navegador deve abrir automaticamente (geralmente em `http://localhost:5173/`).

### 5. Acessando a Aplicação

* Acesse a aplicação no seu navegador: `http://localhost:5173/`

Você deve ver a interface de gerenciamento de perfil e poder interagir com ela.

### Clique abaixo para acessar o projeto no Vercel.
**ATENÇÃO:** O projeto só irá funcionar pelo link depois que você tiver realizado as configurações acima!!!!

[Clique Aqui](https://user-profile-woad-seven.vercel.app/)

---

Se tiver qualquer problema, verifique os logs nos terminais do backend e do frontend, e o console do navegador (F12).
