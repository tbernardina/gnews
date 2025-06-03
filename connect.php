<?php
// BANCO LOCAL NOSSO(CUIDADO AO ALTERAR)
// credenciais de acesso ao banco
$host = 'localhost';
$database = 'gnews';
$user = 'root';
$password = '';

// credenciais de acesso ao banco
$conexao_db = new mysqli($host, $user, $password, $database);

if ($conexao_db->connect_error) {
    die("Falha na conexão com o Banco de Dados: " . $conexao_db->connect_error);
}
?>