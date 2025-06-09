<?php
require_once 'db_config.php';
require_once 'includes/functions.php';
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Portal de notícias acessível e moderno. Fique por dentro das últimas notícias com recursos de acessibilidade avançados.">
    <meta name="keywords" content="notícias, portal, acessibilidade, brasil, informação">
    <meta name="author" content="Portal Notícias Já">
    <meta name="robots" content="index, follow">
    
    <!-- Open Graph / Facebook -->
    <meta property="og:type" content="website">
    <meta property="og:url" content="<?php echo (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']; ?>">
    <meta property="og:title" content="GNEWS - Notícias Acessíveis">
    <meta property="og:description" content="Portal de notícias acessível e moderno. Fique por dentro das últimas notícias com recursos de acessibilidade avançados.">
    <meta property="og:image" content="/assets/images/logo.png">

    <!-- Twitter -->
    <meta property="twitter:card" content="summary_large_image">
    <meta property="twitter:url" content="<?php echo (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']; ?>">
    <meta property="twitter:title" content="GNEWS - Notícias Acessíveis">
    <meta property="twitter:description" content="Portal de notícias acessível e moderno. Fique por dentro das últimas notícias com recursos de acessibilidade avançados.">
    <meta property="twitter:image" content="/assets/images/logo.png">

    <title>GNEWS - Notícias Acessíveis</title>
    
    <!-- Preconnect para otimização de performance -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    
    <!-- Fontes otimizadas -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- CSS principal -->
    <link rel="stylesheet" href="style.css">
    
    <!-- Favicon -->
    <link rel="icon" type="image/png" href="assets/images/logo.png">
    <link rel="apple-touch-icon" sizes="180x180" href="assets/images/logo.png">
    
    <!-- Manifest para PWA -->
    <link rel="manifest" href="/site.webmanifest">
    
    <!-- Theme color -->
    <meta name="theme-color" content="#2563eb">
    
    <!-- Preload de recursos críticos -->
    <link rel="preload" href="style.css" as="style">
    <link rel="preload" href="assets/js/script.js" as="script">
</head>
<body class="keyboard-navigation">
    <!-- Skip links para acessibilidade -->
    <a href="#main-content" class="skip-link">Pular para o conteúdo principal</a>
    <a href="#main-navigation" class="skip-link">Pular para a navegação</a>
    <a href="#search-form" class="skip-link">Pular para a busca</a>
    <a href="#site-footer" class="skip-link">Pular para o rodapé</a>

    <!-- Cabeçalho principal -->
    <header class="site-header" role="banner">
        <div class="container">
            <!-- Logo/Marca -->
            <div class="logo">
                <a href="index.php" aria-label="Ir para a página inicial do GNEWS">
                    <img src="assets/images/logo.png" alt="Logo GNEWS" class="site-logo">
                </a>
            </div>

            <!-- Navegação principal -->
            <nav id="main-navigation" class="main-navigation" role="navigation" aria-label="Navegação principal">
                <ul>
                    <li>
                        <a href="index.php" 
                           <?php echo (basename($_SERVER['PHP_SELF']) == 'index.php') ? 'aria-current="page"' : ''; ?>>
                            <span aria-hidden="true">🏠</span>
                            Início
                        </a>
                    </li>
                    <li>
                        <a href="search.php" 
                           <?php echo (basename($_SERVER['PHP_SELF']) == 'search.php') ? 'aria-current="page"' : ''; ?>>
                            <span aria-hidden="true">🔍</span>
                            Buscar
                        </a>
                    </li>
                    <li>
                        <a href="noticias_ao_vivo.php" 
                           <?php echo (basename($_SERVER['PHP_SELF']) == 'noticias_ao_vivo.php') ? 'aria-current="page"' : ''; ?>>
                            <span aria-hidden="true">📡</span>
                            Ao Vivo
                        </a>
                    </li>
                </ul>
            </nav>

            <!-- Controles de acessibilidade -->
            <div class="accessibility-controls" role="group" aria-label="Controles de acessibilidade">
                <button 
                    id="decrease-font" 
                    type="button"
                    aria-label="Diminuir tamanho da fonte" 
                    title="Diminuir tamanho da fonte"
                    class="accessibility-btn"
                >
                    A-
                </button>
                <button 
                    id="reset-font" 
                    type="button"
                    aria-label="Restaurar tamanho padrão da fonte" 
                    title="Restaurar tamanho padrão da fonte"
                    class="accessibility-btn"
                >
                    A
                </button>
                <button 
                    id="increase-font" 
                    type="button"
                    aria-label="Aumentar tamanho da fonte" 
                    title="Aumentar tamanho da fonte"
                    class="accessibility-btn"
                >
                    A+
                </button>
                <button 
                    id="toggle-contrast" 
                    type="button"
                    aria-label="Alternar modo de alto contraste" 
                    title="Alternar modo de alto contraste"
                    aria-pressed="false"
                    class="accessibility-btn"
                >
                    <span aria-hidden="true">🌓</span>
                    <span class="visually-hidden">Contraste</span>
                </button>
            </div>

            <!-- Formulário de busca no cabeçalho -->
            <div class="search-container-header">
                <form id="search-form" action="search.php" method="get" role="search" aria-label="Busca de notícias">
                    <label for="search-input-header" class="visually-hidden">
                        Digite o termo para buscar notícias
                    </label>
                    <input 
                        type="search" 
                        id="search-input-header" 
                        name="q" 
                        placeholder="Buscar notícias..."
                        value="<?php echo isset($_GET['q']) ? htmlspecialchars($_GET['q'], ENT_QUOTES, 'UTF-8') : ''; ?>"
                        aria-label="Campo de busca de notícias"
                        autocomplete="off"
                        spellcheck="false"
                        maxlength="100"
                    >
                    <button 
                        type="submit" 
                        aria-label="Executar busca"
                        title="Executar busca"
                    >
                        <span aria-hidden="true">🔍</span>
                        <span class="visually-hidden">Buscar</span>
                    </button>
                </form>
            </div>
        </div>
    </header>

    <!-- Indicador de carregamento -->
    <div id="loading-indicator" class="loading-indicator" aria-hidden="true" role="status">
        <div class="loading-spinner"></div>
        <span class="visually-hidden">Carregando conteúdo...</span>
    </div>

    <!-- Conteúdo principal -->
    <main id="main-content" role="main" tabindex="-1">
        <!-- Breadcrumb (será adicionado dinamicamente se necessário) -->
        <nav aria-label="Você está aqui" class="breadcrumb-nav" style="display: none;">
            <ol class="breadcrumb">
                <li><a href="index.php">Início</a></li>
                <!-- Itens adicionais serão inseridos dinamicamente -->
            </ol>
        </nav>

