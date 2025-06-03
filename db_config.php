<?php
define('DB_SERVER', 'localhost'); // Ou o host do seu BD
define('DB_USERNAME', 'root'); // Seu usuário do BD
define('DB_PASSWORD', ''); // Sua senha do BD
define('DB_NAME', 'gnews'); // Nome do seu BD

// Conexão com o banco de dados usando MySQLi
$conn = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);

// Checar conexão
if ($conn->connect_error) {
    die("Falha na conexão com o Banco de Dados: " . $conn->connect_error);
}

// Define o charset para UTF-8
$conn->set_charset("utf8mb4");

// Script para criar a tabela (execute uma vez ou verifique se já existe)
/*
CREATE TABLE IF NOT EXISTS noticias (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    description TEXT,
    content TEXT,
    url VARCHAR(255) UNIQUE,
    image_url VARCHAR(255),
    published_at DATETIME,
    source_name VARCHAR(100),
    source_url VARCHAR(255),
    fetched_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    lang VARCHAR(10) DEFAULT 'pt'
);
CREATE INDEX idx_published_at ON noticias (published_at);
CREATE INDEX idx_lang ON noticias (lang);
*/
?>