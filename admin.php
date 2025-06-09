<?php
session_start();

// Verificar se o usuário está logado
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    // Processar login se foi enviado
    if ($_POST['username'] ?? '' === 'admin' && $_POST['password'] ?? '' === '123') {
        $_SESSION['admin_logged_in'] = true;
        header('Location: admin.php');
        exit;
    }
    
    // Exibir formulário de login
    ?>
    <!DOCTYPE html>
    <html lang="pt-BR">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Login - Painel Administrativo</title>
        <style>
            * {
                margin: 0;
                padding: 0;
                box-sizing: border-box;
            }
            
            body {
                font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
                background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                min-height: 100vh;
                display: flex;
                align-items: center;
                justify-content: center;
                padding: 1rem;
            }
            
            .login-container {
                background: white;
                padding: 2rem;
                border-radius: 12px;
                box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
                width: 100%;
                max-width: 400px;
                text-align: center;
            }
            
            .login-header {
                margin-bottom: 2rem;
            }
            
            .login-icon {
                font-size: 3rem;
                margin-bottom: 1rem;
            }
            
            .login-title {
                color: #333;
                font-size: 1.5rem;
                margin-bottom: 0.5rem;
            }
            
            .login-subtitle {
                color: #666;
                font-size: 0.9rem;
            }
            
            .login-form {
                display: flex;
                flex-direction: column;
                gap: 1rem;
            }
            
            .form-group {
                text-align: left;
            }
            
            .form-label {
                display: block;
                margin-bottom: 0.5rem;
                color: #333;
                font-weight: 500;
            }
            
            .form-input {
                width: 100%;
                padding: 0.75rem;
                border: 2px solid #e1e5e9;
                border-radius: 8px;
                font-size: 1rem;
                transition: border-color 0.2s ease;
            }
            
            .form-input:focus {
                outline: none;
                border-color: #667eea;
            }
            
            .login-button {
                background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                color: white;
                border: none;
                padding: 0.75rem 1.5rem;
                border-radius: 8px;
                font-size: 1rem;
                font-weight: 500;
                cursor: pointer;
                transition: transform 0.2s ease;
                margin-top: 1rem;
            }
            
            .login-button:hover {
                transform: translateY(-2px);
            }
            
            .login-button:active {
                transform: translateY(0);
            }
            
            .error-message {
                background: #fee;
                color: #c33;
                padding: 0.75rem;
                border-radius: 6px;
                margin-bottom: 1rem;
                border: 1px solid #fcc;
            }
            
            .login-footer {
                margin-top: 2rem;
                padding-top: 1rem;
                border-top: 1px solid #eee;
                color: #666;
                font-size: 0.8rem;
            }
            
            @media (max-width: 480px) {
                .login-container {
                    padding: 1.5rem;
                }
                
                .login-title {
                    font-size: 1.3rem;
                }
            }
        </style>
    </head>
    <body>
        <div class="login-container">
            <div class="login-header">
                <div class="login-icon" aria-hidden="true">🔐</div>
                <h1 class="login-title">Painel Administrativo</h1>
                <p class="login-subtitle">Faça login para acessar o sistema</p>
            </div>
            
            <?php if ($_SERVER['REQUEST_METHOD'] === 'POST'): ?>
                <div class="error-message">
                    <strong>Erro:</strong> Usuário ou senha incorretos.
                </div>
            <?php endif; ?>
            
            <form method="POST" class="login-form">
                <div class="form-group">
                    <label for="username" class="form-label">
                        <span aria-hidden="true">👤</span>
                        Usuário
                    </label>
                    <input 
                        type="text" 
                        id="username" 
                        name="username" 
                        class="form-input"
                        placeholder="Digite seu usuário"
                        required
                        autocomplete="username"
                        value="<?= htmlspecialchars($_POST['username'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                    >
                </div>
                
                <div class="form-group">
                    <label for="password" class="form-label">
                        <span aria-hidden="true">🔑</span>
                        Senha
                    </label>
                    <input 
                        type="password" 
                        id="password" 
                        name="password" 
                        class="form-input"
                        placeholder="Digite sua senha"
                        required
                        autocomplete="current-password"
                    >
                </div>
                
                <button type="submit" class="login-button">
                    <span aria-hidden="true">🚀</span>
                    Entrar
                </button>
            </form>
            
            <div class="login-footer">
                Sistema de Notícias - Acesso Restrito
            </div>
        </div>
        
        <script>
            // Focar no campo de usuário ao carregar a página
            document.addEventListener('DOMContentLoaded', function() {
                document.getElementById('username').focus();
            });
            
            // Adicionar efeito de loading no botão
            document.querySelector('.login-form').addEventListener('submit', function() {
                const button = document.querySelector('.login-button');
                button.innerHTML = '<span aria-hidden="true">⏳</span> Entrando...';
                button.disabled = true;
            });
        </script>
    </body>
    </html>
    <?php
    exit;
}

// Processar logout se solicitado
if (isset($_GET['logout'])) {
    session_destroy();
    header('Location: admin.php');
    exit;
}

require_once __DIR__ . '/db_config.php';
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/header.php';
?>

<div class="container">
    <!-- Cabeçalho da página com botão de logout -->
    <section class="page-header" aria-labelledby="page-heading">
        <div class="header-content">
            <div class="header-main">
                <h1 id="page-heading" class="page-title">
                    <span class="page-icon" aria-hidden="true">⚙️</span>
                    Painel Administrativo
                </h1>
                <p class="page-description">
                    Visualize as notícias ordenadas por média de avaliação dos usuários.
                </p>
            </div>
            <div class="header-actions">
                <a href="admin.php?logout=1" class="logout-btn" title="Sair do sistema">
                    <span aria-hidden="true">🚪</span>
                    Sair
                </a>
            </div>
        </div>
    </section>

    <!-- Seção de estatísticas -->
    <section class="stats-section" aria-labelledby="stats-heading">
        <h2 id="stats-heading" class="stats-title">
            <span class="stats-icon" aria-hidden="true">📊</span>
            Estatísticas Gerais
        </h2>
        
        <div class="stats-grid">
            <?php
            // Buscar estatísticas gerais
            $total_noticias_query = "SELECT COUNT(*) as total FROM noticias";
            $total_noticias_result = $conn->query($total_noticias_query);
            $total_noticias = $total_noticias_result->fetch_assoc()['total'];

            $total_feedbacks_query = "SELECT COUNT(*) as total FROM feedbacks";
            $total_feedbacks_result = $conn->query($total_feedbacks_query);
            $total_feedbacks = $total_feedbacks_result->fetch_assoc()['total'];

            $media_geral_query = "SELECT AVG(NOTA_FEEDBACK) as media FROM feedbacks";
            $media_geral_result = $conn->query($media_geral_query);
            $media_geral = $media_geral_result->fetch_assoc()['media'];
            $media_geral = $media_geral ? number_format($media_geral, 2) : '0.00';
            ?>
            
            <div class="stat-card">
                <div class="stat-icon" aria-hidden="true">📰</div>
                <div class="stat-content">
                    <div class="stat-number"><?= $total_noticias ?></div>
                    <div class="stat-label">Total de Notícias</div>
                </div>
            </div>
            
            <div class="stat-card">
                <div class="stat-icon" aria-hidden="true">💬</div>
                <div class="stat-content">
                    <div class="stat-number"><?= $total_feedbacks ?></div>
                    <div class="stat-label">Total de Avaliações</div>
                </div>
            </div>
            
            <div class="stat-card">
                <div class="stat-icon" aria-hidden="true">⭐</div>
                <div class="stat-content">
                    <div class="stat-number"><?= $media_geral ?></div>
                    <div class="stat-label">Média Geral</div>
                </div>
            </div>
        </div>
    </section>

    <!-- Seção de notícias ordenadas por média -->
    <section class="news-ranking-section" aria-labelledby="ranking-heading">
        <h2 id="ranking-heading" class="ranking-title">
            <span class="ranking-icon" aria-hidden="true">🏆</span>
            Ranking de Notícias por Avaliação
        </h2>
        
        <div class="ranking-controls">
            <form method="GET" class="filter-form">
                <div class="filter-group">
                    <label for="limit">Mostrar:</label>
                    <select name="limit" id="limit">
                        <option value="10" <?= (isset($_GET['limit']) && $_GET['limit'] == '10') ? 'selected' : '' ?>>10 notícias</option>
                        <option value="25" <?= (isset($_GET['limit']) && $_GET['limit'] == '25') ? 'selected' : '' ?>>25 notícias</option>
                        <option value="50" <?= (isset($_GET['limit']) && $_GET['limit'] == '50') ? 'selected' : '' ?>>50 notícias</option>
                        <option value="100" <?= (isset($_GET['limit']) && $_GET['limit'] == '100') ? 'selected' : '' ?>>100 notícias</option>
                    </select>
                </div>
                
                <div class="filter-group">
                    <label for="min_feedbacks">Mín. avaliações:</label>
                    <select name="min_feedbacks" id="min_feedbacks">
                        <option value="1" <?= (isset($_GET['min_feedbacks']) && $_GET['min_feedbacks'] == '1') ? 'selected' : '' ?>>1+</option>
                        <option value="3" <?= (isset($_GET['min_feedbacks']) && $_GET['min_feedbacks'] == '3') ? 'selected' : '' ?>>3+</option>
                        <option value="5" <?= (isset($_GET['min_feedbacks']) && $_GET['min_feedbacks'] == '5') ? 'selected' : '' ?>>5+</option>
                        <option value="10" <?= (isset($_GET['min_feedbacks']) && $_GET['min_feedbacks'] == '10') ? 'selected' : '' ?>>10+</option>
                    </select>
                </div>
                
                <button type="submit" class="filter-btn">
                    <span aria-hidden="true">🔍</span>
                    Filtrar
                </button>
            </form>
        </div>

        <div class="news-ranking">
            <?php
            // Parâmetros de filtro
            $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 25;
            $min_feedbacks = isset($_GET['min_feedbacks']) ? (int)$_GET['min_feedbacks'] : 1;
            
            // Usar a nova função para buscar o ranking
            $ranking = getNewsRanking($conn, $limit, $min_feedbacks);
            
            if (count($ranking) > 0):
                $position = 1;
                foreach ($ranking as $row):
                    $media_formatada = number_format($row['media_nota'], 2);
                    $title = htmlspecialchars($row['title'], ENT_QUOTES, 'UTF-8');
                    $description = htmlspecialchars($row['description'], ENT_QUOTES, 'UTF-8');
                    $url = htmlspecialchars($row['url'], ENT_QUOTES, 'UTF-8');
                    $image = htmlspecialchars($row['image_url'] ?? '', ENT_QUOTES, 'UTF-8');
                    $source = htmlspecialchars($row['source_name'], ENT_QUOTES, 'UTF-8');
                    $published_at = formatDate($row['published_at']);
                    
                    // Determinar classe da medalha
                    $medal_class = '';
                    $medal_icon = '';
                    if ($position == 1) {
                        $medal_class = 'gold';
                        $medal_icon = '🥇';
                    } elseif ($position == 2) {
                        $medal_class = 'silver';
                        $medal_icon = '🥈';
                    } elseif ($position == 3) {
                        $medal_class = 'bronze';
                        $medal_icon = '🥉';
                    } else {
                        $medal_icon = '#' . $position;
                    }
            ?>
            <article class="ranking-item <?= $medal_class ?>" aria-labelledby="ranking-title-<?= $row['id'] ?>">
                <div class="ranking-position">
                    <span class="position-number"><?= $medal_icon ?></span>
                </div>
                
                <div class="ranking-content">
                    <?php if (!empty($image)): ?>
                        <div class="ranking-image-container">
                            <img 
                                src="<?= $image ?>" 
                                alt="<?= generateAltText($title) ?>"
                                class="ranking-image"
                                loading="lazy"
                                onerror="this.parentElement.innerHTML='<div class=&quot;placeholder-image&quot; role=&quot;img&quot; aria-label=&quot;Imagem não disponível&quot;><span>Sem Imagem</span></div>'"
                            >
                        </div>
                    <?php else: ?>
                        <div class="placeholder-image" role="img" aria-label="Imagem não disponível">
                            <span>Sem Imagem</span>
                        </div>
                    <?php endif; ?>
                    
                    <div class="ranking-info">
                        <h3 id="ranking-title-<?= $row['id'] ?>" class="ranking-news-title">
                            <a href="<?= $url ?>" target="_blank" rel="noopener noreferrer">
                                <?= $title ?>
                            </a>
                        </h3>
                        
                        <div class="ranking-meta">
                            <span class="ranking-source">
                                <span class="source-icon" aria-hidden="true">📰</span>
                                <?= $source ?>
                            </span>
                            <time datetime="<?= $row['published_at'] ?>" class="ranking-time">
                                <span class="time-icon" aria-hidden="true">🕒</span>
                                <?= $published_at ?>
                            </time>
                        </div>
                        
                        <p class="ranking-description"><?= $description ?></p>
                        
                        <div class="ranking-stats">
                            <div class="stat-item">
                                <span class="stat-icon" aria-hidden="true">⭐</span>
                                <span class="stat-value"><?= $media_formatada ?></span>
                                <span class="stat-label">Média</span>
                            </div>
                            <div class="stat-item">
                                <span class="stat-icon" aria-hidden="true">💬</span>
                                <span class="stat-value"><?= $row['total_avaliacoes'] ?></span>
                                <span class="stat-label">Avaliações</span>
                            </div>
                        </div>
                        
                        <div class="ranking-actions">
                            <a href="<?= $url ?>" target="_blank" rel="noopener noreferrer" class="btn-view">
                                <span aria-hidden="true">📖</span>
                                Ver Notícia
                            </a>
                            <a href="view_feedbacks.php?url=<?= urlencode($url) ?>&title=<?= urlencode($title) ?>" class="btn-feedback">
                                <span aria-hidden="true">💬</span>
                                Ver Feedbacks
                            </a>
                        </div>
                    </div>
                </div>
            </article>
            <?php
                    $position++;
                endforeach;
            else:
            ?>
                <div class="no-results">
                    <div class="no-results-icon" aria-hidden="true">📊</div>
                    <h3>Nenhuma notícia encontrada</h3>
                    <p>Não há notícias com o número mínimo de avaliações especificado.</p>
                    <p>Tente diminuir o filtro de avaliações mínimas ou aguarde mais feedbacks dos usuários.</p>
                </div>
            <?php endif; ?>
        </div>
    </section>
</div>

<!-- Estilos específicos para a página admin -->
<style>
.header-content {
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 1rem;
}

.header-main {
    flex: 1;
}

.header-actions {
    display: flex;
    align-items: center;
    gap: 1rem;
}

.admin-info {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.5rem 1rem;
    background: #f8f9fa;
    border-radius: 20px;
    font-size: 0.9rem;
    color: #666;
}

.logout-btn {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.5rem 1rem;
    background: #dc3545;
    color: white;
    text-decoration: none;
    border-radius: 6px;
    font-size: 0.9rem;
    transition: background-color 0.2s ease;
}

.logout-btn:hover {
    background: #c82333;
    color: white;
}

.stats-section {
    margin: 2rem 0;
}

.stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 1rem;
    margin-top: 1rem;
}

.stat-card {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 1.5rem;
    border-radius: 12px;
    display: flex;
    align-items: center;
    gap: 1rem;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    transition: transform 0.2s ease;
}

.stat-card:hover {
    transform: translateY(-2px);
}

.stat-icon {
    font-size: 2rem;
}

.stat-number {
    font-size: 2rem;
    font-weight: bold;
    line-height: 1;
}

.stat-label {
    font-size: 0.9rem;
    opacity: 0.9;
}

.ranking-controls {
    margin: 1rem 0;
    padding: 1rem;
    background: #f8f9fa;
    border-radius: 8px;
}

.filter-form {
    display: flex;
    gap: 1rem;
    align-items: end;
    flex-wrap: wrap;
}

.filter-group {
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
}

.filter-group label {
    font-weight: 500;
    font-size: 0.9rem;
}

.filter-group select {
    padding: 0.5rem;
    border: 1px solid #ddd;
    border-radius: 4px;
    background: white;
}

.filter-btn {
    padding: 0.5rem 1rem;
    background: #007bff;
    color: white;
    border: none;
    border-radius: 4px;
    cursor: pointer;
    display: flex;
    align-items: center;
    gap: 0.5rem;
    transition: background-color 0.2s ease;
}

.filter-btn:hover {
    background: #0056b3;
}

.ranking-item {
    display: flex;
    gap: 1rem;
    padding: 1.5rem;
    margin-bottom: 1rem;
    background: white;
    border-radius: 12px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    transition: transform 0.2s ease, box-shadow 0.2s ease;
}

.ranking-item:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
}

.ranking-item.gold {
    border-left: 4px solid #ffd700;
}

.ranking-item.silver {
    border-left: 4px solid #c0c0c0;
}

.ranking-item.bronze {
    border-left: 4px solid #cd7f32;
}

.ranking-position {
    display: flex;
    align-items: center;
    justify-content: center;
    min-width: 60px;
}

.position-number {
    font-size: 1.5rem;
    font-weight: bold;
}

.ranking-content {
    display: flex;
    gap: 1rem;
    flex: 1;
}

.ranking-image-container {
    width: 120px;
    height: 80px;
    flex-shrink: 0;
}

.ranking-image {
    width: 100%;
    height: 100%;
    object-fit: cover;
    border-radius: 8px;
}

.placeholder-image {
    width: 120px;
    height: 80px;
    background: #f0f0f0;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 8px;
    color: #666;
    font-size: 0.8rem;
}

.ranking-info {
    flex: 1;
}

.ranking-news-title {
    margin: 0 0 0.5rem 0;
    font-size: 1.1rem;
    line-height: 1.3;
}

.ranking-news-title a {
    color: #333;
    text-decoration: none;
}

.ranking-news-title a:hover {
    color: #007bff;
}

.ranking-meta {
    display: flex;
    gap: 1rem;
    margin-bottom: 0.5rem;
    font-size: 0.9rem;
    color: #666;
}

.ranking-description {
    margin: 0.5rem 0;
    color: #555;
    font-size: 0.9rem;
    line-height: 1.4;
}

.ranking-stats {
    display: flex;
    gap: 1rem;
    margin: 1rem 0;
}

.stat-item {
    display: flex;
    align-items: center;
    gap: 0.3rem;
    padding: 0.3rem 0.8rem;
    background: #f8f9fa;
    border-radius: 20px;
    font-size: 0.9rem;
}

.stat-value {
    font-weight: bold;
    color: #007bff;
}

.ranking-actions {
    display: flex;
    gap: 0.5rem;
    margin-top: 1rem;
}

.btn-view, .btn-feedback {
    padding: 0.4rem 0.8rem;
    border-radius: 6px;
    text-decoration: none;
    font-size: 0.9rem;
    display: flex;
    align-items: center;
    gap: 0.3rem;
    transition: background-color 0.2s ease;
}

.btn-view {
    background: #007bff;
    color: white;
}

.btn-view:hover {
    background: #0056b3;
}

.btn-feedback {
    background: #6c757d;
    color: white;
}

.btn-feedback:hover {
    background: #545b62;
}

@media (max-width: 768px) {
    .header-content {
        flex-direction: column;
        align-items: stretch;
    }
    
    .header-actions {
        justify-content: space-between;
    }
    
    .ranking-content {
        flex-direction: column;
    }
    
    .ranking-image-container, .placeholder-image {
        width: 100%;
        height: 150px;
    }
    
    .filter-form {
        flex-direction: column;
        align-items: stretch;
    }
    
    .ranking-actions {
        flex-direction: column;
    }
}
</style>

<?php
require_once __DIR__ . '/includes/footer.php';
?>

