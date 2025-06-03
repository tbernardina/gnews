<?php
session_start(); // Se for usar sessões para preferências de usuário, por exemplo
require_once 'db_config.php'; // Inclui depois de iniciar a sessão se necessário
require_once 'includes/functions.php'; // Inclui as funções
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Site de notícias acessível para todos.">
    <title>Portal de Notícias Acessível</title>
    <link rel="stylesheet" href="style.css">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

</head>
<body>
<a href="#main-content" class="skip-link">Pular para o Conteúdo Principal</a>

    <a href="#main-content" class="skip-link">Pular para o conteúdo principal</a>

    <header class="site-header" role="banner">
        <div class="container">
            <div class="logo">
                <a href="index.php" aria-label="Página Inicial do Portal de Notícias Acessível">
                    <h1>Portal NotíciasJá</h1>
                </a>
            </div>
            <nav class="main-navigation" aria-label="Navegação principal">

            <nav class="main-navigation" aria-label="Navegação principal">
    <ul>
        <li><a href="index.php">Início</a></li>
        <li><a href="search.php">Buscar Notícias (BD)</a></li> <li><a href="noticias_ao_vivo.php">Notícias Ao Vivo (API)</a></li> </ul>
</nav>

            <div class="search-container-header">
                <form action="search.php" method="get" role="search">
                    <label for="search-input-header" class="visually-hidden">Buscar notícias</label>
                    <input type="search" id="search-input-header" name="q" placeholder="Buscar notícias..."
                           value="<?php echo isset($_GET['q']) ? htmlspecialchars($_GET['q']) : ''; ?>"
                           aria-label="Campo de busca de notícias">
                    <button type="submit">Buscar</button>
                </form>
            </div>
        </div>

<div class="accessibility-controls">
    <button id="decrease-font" aria-label="Diminuir tamanho da fonte" title="Diminuir tamanho da fonte">A-</button>
    <button id="reset-font" aria-label="Restaurar tamanho da fonte padrão" title="Restaurar tamanho da fonte padrão">A</button>
    <button id="increase-font" aria-label="Aumentar tamanho da fonte" title="Aumentar tamanho da fonte">A+</button>
    <button id="toggle-contrast" aria-label="Alternar esquema de contraste" title="Alternar esquema de contraste">Contraste</button> <?php // Novo botão de contraste ?>
</div>

    </header>

    <main id="main-content" role="main">