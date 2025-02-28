# Sistema de Gestão de Tarefas (To-Do List)

![PHP](https://img.shields.io/badge/PHP-777BB4?style=for-the-badge&logo=php&logoColor=white)
![MySQL](https://img.shields.io/badge/MySQL-4479A1?style=for-the-badge&logo=mysql&logoColor=white)
![JavaScript](https://img.shields.io/badge/JavaScript-F7DF1E?style=for-the-badge&logo=javascript&logoColor=black)
![HTML5](https://img.shields.io/badge/HTML5-E34F26?style=for-the-badge&logo=html5&logoColor=white)
![CSS3](https://img.shields.io/badge/CSS3-1572B6?style=for-the-badge&logo=css3&logoColor=white)
![PDO](https://img.shields.io/badge/PDO-777BB4?style=for-the-badge&logo=php&logoColor=white)
![REST API](https://img.shields.io/badge/REST%20API-009688?style=for-the-badge&logo=fastapi&logoColor=white)
![FLEXBOX](https://img.shields.io/badge/FLEXBOX-38B2AC?style=for-the-badge&logo=css3&logoColor=white)
![GRID](https://img.shields.io/badge/GRID-663399?style=for-the-badge&logo=css3&logoColor=white)

Um sistema completo de gerenciamento de tarefas com autenticação de usuários, desenvolvido com PHP PDO e MySQL, seguindo boas práticas de desenvolvimento e com foco em segurança e experiência do usuário.

## 📋 Índice

- [Visão Geral](#-visão-geral)
- [Funcionalidades](#-funcionalidades)
- [Tecnologias](#-tecnologias)
- [Arquitetura](#-arquitetura)
- [Segurança](#-segurança)
- [Estrutura de Arquivos](#-estrutura-de-arquivos)
- [Instalação e Configuração](#-instalação-e-configuração)
- [Uso](#-uso)
- [API REST](#-api-rest)
- [Banco de Dados](#-banco-de-dados)
- [Responsividade e UX](#-responsividade-e-ux)
- [Possíveis Melhorias](#-possíveis-melhorias)
- [Licença](#-licença)

## 🔍 Visão Geral

O Sistema de Gestão de Tarefas é uma aplicação web desenvolvida para permitir que usuários organizem suas tarefas diárias de forma eficiente. Com uma interface moderna e responsiva, o sistema permite que os usuários criem, visualizem, atualizem e excluam tarefas, além de filtrá-las por status e prioridade.

![Screenshot do Sistema](https://via.placeholder.com/800x400?text=Screenshot+do+Sistema)

## ✨ Funcionalidades

### Autenticação de Usuários
- Registro de novos usuários
- Login seguro com senha hasheada
- Proteção de rotas (páginas que necessitam de autenticação)
- Logout

### Gerenciamento de Tarefas
- **Criar** novas tarefas com título, descrição e prioridade
- **Visualizar** lista de tarefas com estatísticas de status
- **Atualizar** informações e status das tarefas
- **Excluir** tarefas com confirmação
- **Filtrar** tarefas por:
  - Status (pendente, em andamento, concluído)
  - Prioridade (baixa, média, alta)
  - Busca por texto (título e descrição)
- **Estatísticas** em tempo real sobre as tarefas

### Interface
- Design responsivo (desktop, tablet e mobile)
- Feedback visual para ações do usuário
- Indicadores visuais de status e prioridade
- Modais para interações complexas
- Transições e animações suaves

## 🛠 Tecnologias

### Backend
- **PHP 7.0+**: Linguagem de programação server-side
- **PDO (PHP Data Objects)**: Interface para acesso a banco de dados
- **MySQL**: Sistema de gerenciamento de banco de dados relacional
- **Apache/Nginx**: Servidor web

### Frontend
- **HTML5**: Estruturação do conteúdo
- **CSS3**: Estilização com uso avançado de:
  - Flexbox: Posicionamento flexível de elementos
  - Grid: Layout em grade para organização do conteúdo
  - Variáveis CSS: Sistema de cores e estilos reutilizáveis
- **JavaScript**: Interatividade e comunicação com a API
- **Font Awesome**: Biblioteca de ícones

### Segurança
- **Prepared Statements**: Prevenção contra SQL Injection
- **Password Hashing**: Armazenamento seguro de senhas
- **CSRF Protection**: Proteção contra Cross-Site Request Forgery
- **Validação**: Nos dois lados (cliente e servidor)
- **Sanitização de Input**: Prevenção contra XSS

## 🏗 Arquitetura

O projeto segue uma arquitetura em camadas, com separação clara entre frontend, backend e acesso a dados:

### Camada de Apresentação (Frontend)
- Interface do usuário em HTML, CSS e JavaScript
- Comunicação com backend via chamadas AJAX para API REST

### Camada de Aplicação (Backend)
- API REST para manipulação de dados
- Controladores para autenticação e gerenciamento de tarefas
- Validação e processamento de dados

### Camada de Dados
- Classes PDO para acesso ao banco de dados
- Modelo de dados (Task e User)
- Consultas SQL otimizadas

## 🔒 Segurança

### Prevenção de Ataques Comuns
- **SQL Injection**: Uso de prepared statements em todas as consultas
- **XSS (Cross-Site Scripting)**: Sanitização de inputs e outputs
- **CSRF (Cross-Site Request Forgery)**: Tokens de proteção em formulários
- **Brute Force**: Validação de inputs e limitação de tentativas
- **Clickjacking**: Headers de segurança via .htaccess

### Autenticação Segura
- Senhas armazenadas com hash usando `password_hash()` e `PASSWORD_DEFAULT`
- Verificação segura com `password_verify()`
- Sessões protegidas com timeout configurável

### Acesso a Dados
- Permissões de usuário verificadas em todas as operações de acesso a dados
- Um usuário só pode ver e manipular suas próprias tarefas

## 📁 Estrutura de Arquivos

```
todo-list/
├── assets/
│   ├── css/
│   │   └── style.css
│   ├── js/
│   │   ├── main.js
│   │   └── tasks.js
│   └── img/
│       └── logo.png
├── config/
│   ├── config.php
│   └── db.php
├── includes/
│   ├── header.php
│   └── footer.php
├── classes/
│   ├── Database.php
│   ├── Task.php
│   └── User.php
├── api/
│   ├── tasks.php
│   └── auth.php
├── sql/
│   └── todo_db.sql
├── index.php
├── login.php
├── register.php
├── tasks.php
├── .htaccess
└── README.md
```

## 📥 Instalação e Configuração

### Requisitos do Sistema
- PHP 7.0 ou superior
- MySQL 5.7 ou superior
- Servidor web Apache/Nginx com mod_rewrite habilitado
- Extensões PHP: PDO, pdo_mysql, json, session

### Passos para Instalação

1. **Clone o repositório ou faça download**
   ```bash
   git clone https://github.com/seu-usuario/todo-list.git
   cd todo-list
   ```

2. **Crie o banco de dados**
   ```sql
   CREATE DATABASE todo_db;
   ```

3. **Importe o arquivo SQL**
   ```bash
   mysql -u seu_usuario -p todo_db < sql/todo_db.sql
   ```

4. **Configure o acesso ao banco de dados**
   - Abra o arquivo `config/db.php`
   - Edite as configurações de conexão:
     ```php
     // Desenvolvimento
     define('DB_HOST', 'localhost');
     define('DB_NAME', 'todo_db');
     define('DB_USER', 'seu_usuario');
     define('DB_PASS', 'sua_senha');
     ```

5. **Configure as URLs da aplicação**
   - Abra o arquivo `config/config.php`
   - Edite as configurações base:
     ```php
     // Ambiente (true para produção, false para desenvolvimento)
     define('IS_PRODUCTION', false);

     // URLs base
     if (IS_PRODUCTION) {
         define('BASE_URL', 'https://seu-dominio.com/');
         define('API_URL', 'https://seu-dominio.com/api/');
     } else {
         define('BASE_URL', 'http://localhost/todo-list/');
         define('API_URL', 'http://localhost/todo-list/api/');
     }
     ```

6. **Configure o servidor web**
   - Certifique-se de que o módulo rewrite está habilitado
   - Aponte o Document Root para a pasta do projeto
   - Certifique-se de que o arquivo .htaccess está sendo interpretado

7. **Verifique as permissões**
   - Certifique-se de que o servidor web tem permissões para ler/escrever no diretório

## 🚀 Uso

### Acesso Inicial
- Acesse a aplicação pelo navegador: `http://localhost/todo-list/`
- Você pode usar o usuário de teste:
  - Usuário: `usuario_teste`
  - Senha: `teste123`
- Ou registre um novo usuário na tela de cadastro

### Fluxo de Uso Básico
1. Faça login ou crie uma nova conta
2. Na página de tarefas, crie uma nova tarefa clicando no botão "Nova Tarefa"
3. Preencha as informações da tarefa e salve
4. Você verá sua tarefa na lista, com opções para editar, excluir ou alterar status
5. Use os filtros no topo para encontrar tarefas específicas

### Gerenciamento de Tarefas
- **Criar tarefa**: Clique no botão "Nova Tarefa" e preencha o formulário
- **Editar tarefa**: Clique no ícone de edição (lápis) de uma tarefa existente
- **Excluir tarefa**: Clique no ícone de exclusão (lixeira) e confirme a ação
- **Mudar status**: Use os botões de status conforme o estado atual da tarefa:
  - Pendente → Em Andamento → Concluído
  - Concluído → Pendente (reabrir)

## 📡 API REST

O sistema inclui uma API REST completa para manipulação de tarefas e autenticação.

### Endpoints de Autenticação

| Método | Endpoint | Descrição | Parâmetros |
|--------|----------|-----------|------------|
| POST | `/api/auth.php?action=login` | Login de usuário | username, password |
| POST | `/api/auth.php?action=register` | Registro de usuário | username, email, password |
| POST | `/api/auth.php?action=logout` | Logout de usuário | - |

### Endpoints de Tarefas

| Método | Endpoint | Descrição | Parâmetros |
|--------|----------|-----------|------------|
| GET | `/api/tasks.php` | Listar tarefas com filtros opcionais | status, priority, search, order_by, order_direction |
| GET | `/api/tasks.php?id={id}` | Obter tarefa específica | id |
| POST | `/api/tasks.php` | Criar nova tarefa | title, description, priority |
| PUT | `/api/tasks.php?id={id}` | Atualizar tarefa | title, description, priority, status |
| DELETE | `/api/tasks.php?id={id}` | Excluir tarefa | id |

### Exemplo de Uso da API

```javascript
// Criar uma nova tarefa
fetch('http://localhost/todo-list/api/tasks.php', {
    method: 'POST',
    headers: {
        'Content-Type': 'application/json'
    },
    body: JSON.stringify({
        title: 'Nova tarefa',
        description: 'Descrição da tarefa',
        priority: 'media'
    }),
    credentials: 'include'
})
.then(response => response.json())
.then(data => console.log(data));
```

## 💾 Banco de Dados

### Estrutura

O banco de dados consiste em duas tabelas principais:

#### Tabela `users`
| Campo | Tipo | Descrição |
|-------|------|-----------|
| id | INT | Chave primária, auto incremento |
| username | VARCHAR(50) | Nome de usuário (único) |
| email | VARCHAR(100) | Email do usuário (único) |
| password | VARCHAR(255) | Senha hasheada |
| created_at | TIMESTAMP | Data de criação |

#### Tabela `tasks`
| Campo | Tipo | Descrição |
|-------|------|-----------|
| id | INT | Chave primária, auto incremento |
| user_id | INT | Chave estrangeira para users.id |
| title | VARCHAR(100) | Título da tarefa |
| description | TEXT | Descrição da tarefa (opcional) |
| status | ENUM | Status: 'pendente', 'em_andamento', 'concluido' |
| priority | ENUM | Prioridade: 'baixa', 'media', 'alta' |
| created_at | TIMESTAMP | Data de criação |
| updated_at | TIMESTAMP | Data da última atualização |

### Relacionamentos
- Um usuário pode ter múltiplas tarefas (1:N)
- Cada tarefa pertence a exatamente um usuário

### Diagrama ER
```
users 1 --- * tasks
```

## 📱 Responsividade e UX

O sistema foi desenvolvido com foco em usabilidade e experiência do usuário em diferentes dispositivos:

### Desktop
- Layout completo com todas as funcionalidades
- Uso eficiente do espaço da tela
- Hover states para interações

### Tablet
- Layout adaptativo usando Flexbox
- Elementos redimensionados para telas médias

### Mobile
- Layout otimizado para telas pequenas
- Elementos empilhados quando necessário
- Touch-friendly com alvos de toque adequados
- Menus colapsáveis

### Aspectos de UX/UI
- Feedback visual para ações do usuário
- Mensagens de confirmação e erro
- Indicadores de carregamento
- Códigos de cores para status e prioridade
- Micro-interações para melhor engajamento

## 🚧 Possíveis Melhorias

Ideias para evolução futura do projeto:

1. **Funcionalidades Avançadas**
   - Subtarefas (tarefas aninhadas)
   - Tags/categorias para tarefas
   - Data de vencimento com lembretes
   - Compartilhamento de tarefas entre usuários

2. **Interface**
   - Tema escuro / modo noturno
   - Personalização de cores por usuário
   - Arrastar e soltar para reordenar tarefas
   - Visualização em calendário

3. **Técnicas**
   - Implementação de Docker para facilitar instalação
   - Testes automatizados (PHPUnit)
   - Refatoração para um framework PHP (Laravel, Symfony)
   - PWA (Progressive Web App) para uso offline
   - Integração com serviços externos (Google Calendar, etc)

## 📄 Licença

Este projeto está licenciado sob a [MIT License](https://opensource.org/licenses/MIT).

---

Desenvolvido com ❤️ como parte de um projeto demonstrativo de boas práticas de desenvolvimento PHP.