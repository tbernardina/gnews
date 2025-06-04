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
?>