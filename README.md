# Sistema de Gestão de Tarefas (To-Do List)

Um sistema de gerenciamento de tarefas simples e moderno desenvolvido com PHP PDO e MySQL, com interface responsiva utilizando Flexbox e Grid.

## Características

- Sistema completo de autenticação de usuários (registro, login, logout)
- CRUD completo de tarefas (criar, listar, editar, excluir)
- Filtros por status (pendente, em andamento, concluído)
- Ordenação e pesquisa de tarefas
- Priorização de tarefas (baixa, média, alta)
- Interface responsiva usando Flexbox e Grid
- Segurança avançada com PDO e prepared statements
- API REST para operações CRUD

## Tecnologias Utilizadas

- Backend: PHP 7.4+ com PDO
- Banco de Dados: MySQL
- Frontend: HTML5, CSS3 (Flexbox/Grid), JavaScript (ES6+)
- Biblioteca de ícones: Font Awesome
- Responsividade: Design adaptativo em todos os dispositivos

## Estrutura de Diretórios

```
todo-list/
│
├── api/                # Endpoints da API
│   ├── auth.php        # Autenticação de usuários
│   └── tasks.php       # Gerenciamento de tarefas
│
├── classes/            # Classes PHP
│   ├── Database.php    # Classe para conexão e operações de BD
│   ├── Task.php        # Classe para gestão de tarefas
│   └── User.php        # Classe para gestão de usuários
│
├── config/             # Configurações
│   └── config.php      # Configurações gerais
│
├── css/                # Estilos CSS
│   ├── style.css       # Estilos principais
│   └── tasks.css       # Estilos específicos para tarefas
│
├── includes/           # Componentes reutilizáveis
│   ├── header.php      # Cabeçalho comum
│   └── footer.php      # Rodapé comum
│
├── js/                 # Scripts JavaScript
│   └── main.js         # JavaScript principal
│
├── uploads/            # Diretório para uploads (anexos)
│
├── temp/               # Diretório temporário
│
├── .htaccess           # Configurações do Apache
├── index.php           # Página inicial
├── login.php           # Página de login
├── login-simple.php    # Versão alternativa de login
├── register.php        # Página de registro
├── tasks.php           # Página principal de tarefas
├── logout.php          # Script de logout
├── install.php         # Script de instalação
└── README.md           # Documentação
```

## Requisitos

- PHP 7.4 ou superior
- MySQL 5.7 ou superior
- Servidor web Apache com mod_rewrite habilitado

## Instalação

1. Clone o repositório para o diretório do seu servidor web:
   ```
   git clone https://github.com/seu-usuario/todo-list.git
   ```

2. Crie um banco de dados MySQL:
   ```sql
   CREATE DATABASE todo_list_system;
   ```

3. Configure o arquivo `config/config.php` com as informações do seu ambiente:
   - Edite as constantes DB_HOST, DB_NAME, DB_USER e DB_PASS
   - Ajuste BASE_URL para corresponder ao seu ambiente

4. Certifique-se de que os diretórios `uploads/` e `temp/` tenham permissões de escrita:
   ```
   chmod 755 uploads/ temp/
   ```

5. Acesse a URL `http://seu-servidor/todo-list/install.php` para criar as tabelas e o usuário administrador inicial.

6. Após a instalação bem-sucedida, remova o arquivo `install.php` por segurança.

## Uso

1. Acesse o sistema através da URL: `http://seu-servidor/todo-list/`
2. Faça login com o usuário administrador criado durante a instalação (padrão: admin/admin123)
3. Comece a criar e gerenciar suas tarefas!

## Segurança

- Todas as consultas SQL utilizam prepared statements para prevenir injeção SQL
- Senhas armazenadas com hash usando password_hash() e bcrypt
- Proteção contra CSRF em formulários
- Validação de dados no lado do servidor
- Sanitização de entrada e saída
- Controle de acesso baseado em sessão

## Desenvolvimento

Para rodar o projeto em um ambiente de desenvolvimento:

1. Instale o XAMPP, WAMP ou similar
2. Configure a pasta do projeto no diretório web
3. Ajuste as configurações de conexão no arquivo config.php para o ambiente local

## Licença

Este projeto está licenciado sob a licença MIT - veja o arquivo LICENSE para detalhes.

## Autor

Seu Nome - [seu-email@example.com](mailto:seu-email@example.com)