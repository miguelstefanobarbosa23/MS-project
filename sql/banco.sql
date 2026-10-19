
CREATE DATABASE IF NOT EXISTS ms_projects
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

USE ms_projects;


CREATE TABLE IF NOT EXISTS projetos (
    id            INT AUTO_INCREMENT PRIMARY KEY,
    titulo        VARCHAR(100)  NOT NULL,
    descricao     TEXT          NOT NULL,
    tecnologias   VARCHAR(150)  NOT NULL,       
    link_projeto  VARCHAR(255)  DEFAULT NULL,     
    imagem_url    VARCHAR(255)  DEFAULT NULL,     
    data_criacao  TIMESTAMP     NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS mensagens (
    id           INT AUTO_INCREMENT PRIMARY KEY,
    nome         VARCHAR(100)  NOT NULL,
    email        VARCHAR(150)  NOT NULL,
    assunto      VARCHAR(150)  NOT NULL,
    mensagem     TEXT          NOT NULL,
    data_envio   TIMESTAMP     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    lida         TINYINT(1)    NOT NULL DEFAULT 0  
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO projetos (titulo, descricao, tecnologias, link_projeto, imagem_url) VALUES
('Sistema de Biblioteca Escolar',
 'Aplicação para controle de empréstimos de livros, com cadastro de alunos e relatório de atrasos.',
 'HTML, CSS, PHP, PDO, MySQL',
 'https://github.com/seu-usuario/biblioteca-escolar',
 'https://images.unsplash.com/photo-1521587760476-6c12a4b040da?w=600&q=80'),

('Loja Virtual Simples',
 'E-commerce de pequeno porte com carrinho de compras em JavaScript e painel administrativo em PHP.',
 'HTML, CSS, JavaScript, PHP, PDO',
 'https://github.com/seu-usuario/loja-virtual',
 'https://images.unsplash.com/photo-1472851294608-062f824d29cc?w=600&q=80'),

('Dashboard de Tarefas',
 'Aplicação para organizar tarefas diárias por prioridade, com filtros dinâmicos em JavaScript puro.',
 'JavaScript, CSS Grid, LocalStorage',
 'https://github.com/seu-usuario/dashboard-tarefas',
 'https://images.unsplash.com/photo-1517245386807-bb43f82c33c4?w=600&q=80');
