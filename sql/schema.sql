-- SQL Schema para o Sistema de Gestão de Tarefas

-- Criar database se não existir
CREATE DATABASE IF NOT EXISTS todo_list_system CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

USE todo_list_system;

-- Tabela de usuários
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabela de tarefas
CREATE TABLE IF NOT EXISTS tasks (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    title VARCHAR(100) NOT NULL,
    description TEXT,
    status ENUM('pendente', 'em_andamento', 'concluido') DEFAULT 'pendente' NOT NULL,
    priority ENUM('baixa', 'media', 'alta') DEFAULT 'media' NOT NULL,
    due_date DATE,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabela de categorias
CREATE TABLE IF NOT EXISTS categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(50) NOT NULL,
    color VARCHAR(7) DEFAULT '#777777'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabela de relacionamento entre tarefas e categorias
CREATE TABLE IF NOT EXISTS task_categories (
    task_id INT NOT NULL,
    category_id INT NOT NULL,
    PRIMARY KEY (task_id, category_id),
    FOREIGN KEY (task_id) REFERENCES tasks(id) ON DELETE CASCADE,
    FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Inserir categorias padrão
INSERT INTO categories (name, color) VALUES 
('Trabalho', '#4CAF50'),
('Pessoal', '#2196F3'),
('Urgente', '#F44336'),
('Estudo', '#9C27B0'),
('Projeto', '#FF9800')
ON DUPLICATE KEY UPDATE name = VALUES(name);

-- Criar um usuário de teste (senha: admin123)
INSERT INTO users (username, email, password) 
VALUES ('admin', 'admin@example.com', '$2y$10$B8T0.1xYgJQS4B1D5qgGhuuNr7RzMiOTtXZhXyvWGnxpzpLTgsYVW') 
ON DUPLICATE KEY UPDATE id = id;

-- Criar outro usuário de teste (senha: user123)
INSERT INTO users (username, email, password) 
VALUES ('usuario', 'usuario@example.com', '$2y$10$WXSBvzZ8yaBSFmY5rXmET.Ye8ZnKGraB1MQJ48u5n.zpgZ9sa7iZS')
ON DUPLICATE KEY UPDATE id = id;