# Sistema de Gestão de Tarefas (To-Do List)

![PHP](https://img.shields.io/badge/PHP-7.4%2B-777BB4?style=flat-square&logo=php&logoColor=white)
![MySQL](https://img.shields.io/badge/MySQL-5.7%2B-4479A1?style=flat-square&logo=mysql&logoColor=white)
![JavaScript](https://img.shields.io/badge/JavaScript-ES6%2B-F7DF1E?style=flat-square&logo=javascript&logoColor=black)
![HTML5](https://img.shields.io/badge/HTML5-E34F26?style=flat-square&logo=html5&logoColor=white)
![CSS3](https://img.shields.io/badge/CSS3-1572B6?style=flat-square&logo=css3&logoColor=white)
![PDO](https://img.shields.io/badge/PDO-Security-brightgreen?style=flat-square)
![REST API](https://img.shields.io/badge/REST_API-JSON-orange?style=flat-square)
![Flexbox](https://img.shields.io/badge/Flexbox-Layout-9cf?style=flat-square)
![Grid](https://img.shields.io/badge/Grid-Layout-9cf?style=flat-square)
![Responsive](https://img.shields.io/badge/Responsive-Design-blue?style=flat-square)
![MIT License](https://img.shields.io/badge/License-MIT-green.svg?style=flat-square)

Um sistema moderno e completo para gerenciamento de tarefas, desenvolvido com PHP PDO e MySQL, com interface responsiva e interativa.

<p align="center">
  <img src="https://ibb.co/r2D929Mm" alt="Sistema de Gestão de Tarefas Preview" width="800"/>
</p>

## ✨ Características

| Funcionalidade | Descrição |
|----------------|-----------|
| 🔐 **Autenticação** | Sistema completo de registro, login e logout de usuários |
| 📝 **CRUD de Tarefas** | Criar, listar, editar e excluir tarefas de forma intuitiva |
| 🔍 **Filtros** | Filtrar tarefas por status (pendente, em andamento, concluído) |
| 🔄 **Ordenação** | Ordenar tarefas por diversos critérios (data, prioridade, alfabética) |
| 🔎 **Pesquisa** | Busca instantânea em todo o conteúdo das tarefas |
| ⭐ **Priorização** | Atribuir prioridades às tarefas (baixa, média, alta) |
| 📱 **Responsividade** | Interface adaptável a qualquer dispositivo |
| 🔒 **Segurança** | PDO com prepared statements para prevenir injeção SQL |
| 🌐 **API REST** | Endpoints para integração com outros sistemas |

## 🛠️ Tecnologias Utilizadas

### Backend
- ![PHP](https://img.shields.io/badge/PHP-777BB4?style=flat-square&logo=php&logoColor=white) PHP 7.4+ com PDO
- ![MySQL](https://img.shields.io/badge/MySQL-4479A1?style=flat-square&logo=mysql&logoColor=white) MySQL 5.7+
- ![Apache](https://img.shields.io/badge/Apache-D22128?style=flat-square&logo=apache&logoColor=white) Apache com mod_rewrite

### Frontend
- ![HTML5](https://img.shields.io/badge/HTML5-E34F26?style=flat-square&logo=html5&logoColor=white) HTML5 Semântico
- ![CSS3](https://img.shields.io/badge/CSS3-1572B6?style=flat-square&logo=css3&logoColor=white) CSS3 (Flexbox/Grid)
- ![JavaScript](https://img.shields.io/badge/JavaScript-F7DF1E?style=flat-square&logo=javascript&logoColor=black) JavaScript ES6+
- ![Font Awesome](https://img.shields.io/badge/Font_Awesome-339AF0?style=flat-square&logo=font-awesome&logoColor=white) Font Awesome para ícones

## 🗂️ Estrutura de Diretórios

```
todo-list/
│
├── api/                # Endpoints da API REST
│   ├── auth.php        # Autenticação de usuários
│   ├── tasks.php       # Gerenciamento de tarefas
│   └── profile.php     # Gerenciamento de perfil
│
├── classes/            # Classes PHP
│   ├── Database.php    # Singleton para conexão e operações de BD
│   ├── Task.php        # Gestão de tarefas
│   └── User.php        # Gestão de usuários
│
├── config/             # Configurações
│   ├── config.php      # Configurações gerais
│   └── db.php          # Configurações do banco de dados
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
├── uploads/            # Diretório para uploads
├── temp/               # Diretório temporário
│
├── .htaccess           # Configurações do Apache
├── index.php           # Página inicial
├── login.php           # Página de login
├── login-simple.php    # Versão alternativa de login
├── register.php        # Página de registro
├── tasks.php           # Página principal de tarefas
├── profile.php         # Página de perfil do usuário
├── logout.php          # Script de logout
├── 404.php             # Página de erro 404
├── install.php         # Script de instalação
└── README.md           # Documentação
```

## 📋 Requisitos

- ![PHP](https://img.shields.io/badge/PHP-7.4%2B-777BB4?style=flat-square&logo=php&logoColor=white) PHP 7.4 ou superior
- ![MySQL](https://img.shields.io/badge/MySQL-5.7%2B-4479A1?style=flat-square&logo=mysql&logoColor=white) MySQL 5.7 ou superior
- ![Apache](https://img.shields.io/badge/Apache-D22128?style=flat-square&logo=apache&logoColor=white) Servidor web Apache com mod_rewrite habilitado
- Extensões PHP: PDO, PDO_MySQL, JSON

## 🚀 Instalação

1. **Clone o repositório**
   ```bash
   git clone https://github.com/evertonethan/Task-Management-System.git
   cd todo-list
   ```

2. **Crie o banco de dados**
   ```sql
   CREATE DATABASE todo_list_system CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
   ```

3. **Configure o ambiente**
   - Edite `config/db.php` com os dados de conexão:
     ```php
     define('DB_HOST', 'localhost');
     define('DB_NAME', 'todo_list_system');
     define('DB_USER', 'seu_usuario');
     define('DB_PASS', 'sua_senha');
     ```
   - Ajuste `config/config.php` com a URL base do sistema:
     ```php
     define('BASE_URL', 'http://seu-servidor/Task-Management-System/');
     ```

4. **Configure permissões**
   ```bash
   chmod 755 uploads/ temp/
   ```

5. **Execute o instalador**
   - Acesse `http://seu-servidor/Task-Management-System/install.php`
   - Siga o assistente para criar as tabelas e usuário administrador

6. **Finalize a instalação**
   - Após instalação bem-sucedida, remova o arquivo `install.php`
   - Recomendado: Configure seu servidor web para impedir acesso direto a diretórios sensíveis

## 📝 Uso

1. **Acesse o sistema**
   - URL: `http://seu-servidor/Task-Management-System/`

2. **Efetue login**
   - Usuário padrão: `admin`
   - Senha padrão: `admin123`

3. **Comece a gerenciar suas tarefas**
   - Crie novas tarefas
   - Organize por prioridade e status
   - Filtre e pesquise para encontrar rapidamente

## 🔒 Segurança

| Recurso | Implementação |
|---------|---------------|
| **Prepared Statements** | Todas as consultas SQL utilizam PDO com prepared statements |
| **Hashing de Senhas** | Senhas armazenadas com `password_hash()` e bcrypt |
| **Proteção CSRF** | Validação de tokens em formulários sensíveis |
| **Validação de Dados** | Validação rigorosa tanto no cliente quanto no servidor |
| **Sanitização** | Sanitização de entrada e saída para prevenir XSS |
| **Controle de Acesso** | Verificação de sessão em todas as páginas protegidas |
| **Segurança de Arquivos** | Restrição de acesso direto a diretórios via .htaccess |

## 💻 Desenvolvimento

### Configuração do Ambiente de Desenvolvimento

1. **Instale um servidor local**
   - Recomendado: XAMPP, WAMP, MAMP ou Docker

2. **Configure o ambiente**
   ```php
   // config/config.php
   define('IS_DEVELOPMENT', true);
   ```

3. **Habilite mensagens de erro durante o desenvolvimento**
   ```php
   ini_set('display_errors', 1);
   error_reporting(E_ALL);
   ```

### Boas Práticas de Codificação

- Siga o padrão PSR-4 para autoloading de classes
- Documente todas as funções e métodos com PHPDoc
- Mantenha separação clara entre lógica e apresentação
- Use constantes para valores fixos em todo o sistema

## 📄 Licença

Este projeto está licenciado sob a [Licença MIT](LICENSE) - veja o arquivo LICENSE para detalhes.

## 👤 Autor

**Everton "Ethan" de Jesus**
- Email: [ethan.emanuelmessias@gmail.com](mailto:seu-email@example.com)
- GitHub: [evertonethan](https://github.com/evertonethan)

---

<p align="center">
  <img src="https://img.shields.io/badge/Made_with-❤️-red?style=for-the-badge" alt="Made with love"/>
</p>