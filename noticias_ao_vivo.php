<?php 
require_once __DIR__ . '/includes/header.php';
?>

<div class="container">
    <!-- Cabeçalho da página -->
    <section class="page-header" aria-labelledby="page-heading">
        <h1 id="page-heading" class="page-title">
            <span class="page-icon" aria-hidden="true">📡</span>
            Notícias Ao Vivo
        </h1>
        <p class="page-description">
            Acompanhe as notícias mais recentes em tempo real. Use o formulário abaixo para buscar por temas específicos.
        </p>
    </section>

    <!-- Formulário de busca ao vivo -->
    <section class="search-section" aria-labelledby="search-heading">
        <h2 id="search-heading" class="visually-hidden">Buscar notícias ao vivo</h2>
        
        <form action="noticias_ao_vivo.php" method="get" class="search-form" role="search">
            <div class="search-form-container">
                <div class="search-input-group">
                    <label for="live-query-input" class="search-label">
                        <span class="search-icon" aria-hidden="true">🔍</span>
                        Buscar por tema
                    </label>
                    <input
                        type="search"
                        id="live-query-input"
                        name="query"
                        placeholder="Ex: política, economia, saúde..."
                        value="<?php echo isset($_GET['query']) ? htmlspecialchars($_GET['query'], ENT_QUOTES, 'UTF-8') : ''; ?>"
                        aria-label="Digite o termo para buscar notícias ao vivo"
                        autocomplete="off"
                        spellcheck="false"
                        maxlength="100"
                    >
                    <button type="submit" class="search-button" aria-label="Buscar notícias">
                        <span aria-hidden="true">🔍</span>
                        Buscar
                    </button>
                </div>
                
                <div class="search-suggestions">
                    <p class="suggestions-label">Sugestões populares:</p>
                    <div class="suggestions-tags">
                        <a href="noticias_ao_vivo.php?query=política" class="suggestion-tag">Política</a>
                        <a href="noticias_ao_vivo.php?query=economia" class="suggestion-tag">Economia</a>
                        <a href="noticias_ao_vivo.php?query=saúde" class="suggestion-tag">Saúde</a>
                        <a href="noticias_ao_vivo.php?query=tecnologia" class="suggestion-tag">Tecnologia</a>
                        <a href="noticias_ao_vivo.php?query=esporte" class="suggestion-tag">Esportes</a>
                    </div>
                </div>
            </div>
        </form>
    </section>

    <!-- Resultados das notícias -->
    <section class="results-section" aria-labelledby="results-heading">
        <?php
        $term = isset($_GET['query']) && trim($_GET['query']) !== '' ? trim($_GET['query']) : null;
        
        if ($term):
        ?>
            <h2 id="results-heading" class="results-title">
                <span class="results-icon" aria-hidden="true">📊</span>
                Resultados para: "<span class="search-term"><?php echo htmlspecialchars($term, ENT_QUOTES, 'UTF-8'); ?></span>"
            </h2>
        <?php else: ?>
            <h2 id="results-heading" class="results-title">
                <span class="results-icon" aria-hidden="true">🌐</span>
                Principais Notícias do Momento
            </h2>
        <?php endif; ?>

        <!-- Loading indicator -->
        <div id="news-loading" class="loading-container" style="display: none;" aria-hidden="true">
            <div class="loading-spinner"></div>
            <p>Carregando notícias...</p>
        </div>

        <div class="news-grid" id="news-results">
            <?php
            $articles = fetchArticlesFromGNews($term, 'pt', 12);
            
            if ($articles && count($articles) > 0):
                foreach ($articles as $index => $art):
                    // Salva no banco de dados
                    insert_noticias(
                        $conn,
                        $art['title'],
                        $art['description'] ?? '',
                        $art['content'] ?? '',
                        $art['url'],
                        $art['image'],
                        $art['publishedAt'],
                        $art['source']['name'] ?? '',
                        $art['source']['url'] ?? ''
                    );
                    
                    // Preparação de variáveis
                    $article_id = md5($art['url']);
                    $title = htmlspecialchars($art['title'], ENT_QUOTES, 'UTF-8');
                    $url = htmlspecialchars($art['url'], ENT_QUOTES, 'UTF-8');
                    $image = htmlspecialchars($art['image'] ?? '', ENT_QUOTES, 'UTF-8');
                    $source = htmlspecialchars($art['source']['name'] ?? 'Fonte desconhecida', ENT_QUOTES, 'UTF-8');
                    $publishedAt = htmlspecialchars($art['publishedAt'], ENT_QUOTES, 'UTF-8');
                    $formattedDate = formatDate($art['publishedAt']);
                    $description = htmlspecialchars($art['description'] ?? 'Descrição não disponível.', ENT_QUOTES, 'UTF-8');
                    $altText = generateAltText($art['title'] ?? '');
                    $readText = addslashes(strip_tags(($art['title'] ?? '') . ' — ' . ($art['description'] ?? '')));
            ?>
            <article class="news-item" aria-labelledby="live-title-<?= $article_id ?>" style="animation-delay: <?= $index * 0.1 ?>s;">
                <div class="news-item-inner">
                    <?php if (!empty($image)): ?>
                        <div class="news-image-container">
                            <img 
                                src="<?= $image ?>" 
                                alt="<?= $altText ?>"
                                class="news-image"
                                loading="lazy"
                                onerror="this.parentElement.innerHTML='<div class=&quot;placeholder-image&quot; role=&quot;img&quot; aria-label=&quot;Imagem não disponível&quot;><span>Sem Imagem</span></div>'"
                            >
                            <div class="news-badge live-badge" aria-label="Notícia ao vivo">
                                <span aria-hidden="true">🔴</span>
                                AO VIVO
                            </div>
                        </div>
                    <?php else: ?>
                        <div class="placeholder-image" role="img" aria-label="Imagem não disponível">
                            <span>Sem Imagem</span>
                        </div>
                    <?php endif; ?>
                    
                    <div class="news-content">
                        <h3 id="live-title-<?= $article_id ?>" class="news-title">
                            <a href="<?= $url ?>" target="_blank" rel="noopener noreferrer">
                                <?= $title ?>
                            </a>
                        </h3>
                        
                        <div class="news-meta">
                            <span class="source">
                                <span class="source-icon" aria-hidden="true">📰</span>
                                <?= $source ?>
                            </span>
                            <time datetime="<?= $publishedAt ?>" class="publish-time">
                                <span class="time-icon" aria-hidden="true">🕒</span>
                                <?= $formattedDate ?>
                            </time>
                        </div>
                        
                        <p class="news-description"><?= $description ?></p>
                        
                        <div class="card-actions">
                            <button
                                class="btn-read"
                                type="button"
                                data-text="<?= $readText ?>"
                                aria-label="Ouvir notícia"
                                title="Ouvir esta notícia"
                            >
                                <span aria-hidden="true">🔊</span>
                            </button>
                            
                            <form action="feedback.php" method="post">
                                <input type="hidden" name="url" value="<?= $url ?>">
                                <input type="hidden" name="title" value="<?= $title ?>">
                                <input type="submit" value="Avaliar">
                            </form>

                            <a
                                href="<?= $url ?>"
                                target="_blank"
                                rel="noopener noreferrer"
                                class="btn-link"
                                aria-label="Ler notícia completa em nova aba"
                            >
                                <span aria-hidden="true">📖</span>
                                Ler mais
                            </a>
                            
                            <button 
                                class="btn-share" 
                                type="button"
                                data-url="<?= $url ?>"
                                data-title="<?= $title ?>"
                                aria-label="Compartilhar notícia"
                                title="Compartilhar esta notícia"
                            >
                                <span aria-hidden="true">📤</span>
                            </button>
                        </div>
                    </div>
                </div>
            </article>
            <?php
                endforeach;
            else:
                if ($term):
            ?>
                <div class="no-results">
                    <div class="no-results-icon" aria-hidden="true">🔍</div>
                    <h3>Nenhuma notícia encontrada</h3>
                    <p>Não encontramos notícias ao vivo para "<strong><?= htmlspecialchars($term, ENT_QUOTES, 'UTF-8') ?></strong>".</p>
                    <div class="no-results-suggestions">
                        <p>Tente:</p>
                        <ul>
                            <li>Verificar a ortografia do termo</li>
                            <li>Usar palavras-chave mais gerais</li>
                            <li>Buscar por temas populares como política, economia ou saúde</li>
                        </ul>
                    </div>
                    <a href="noticias_ao_vivo.php" class="btn-try-again">Tentar novamente</a>
                </div>
            <?php else: ?>
                <div class="no-term">
                    <div class="no-term-icon" aria-hidden="true">📡</div>
                    <h3>Busque por notícias ao vivo</h3>
                    <p>Digite um termo no campo de busca acima para ver as notícias mais recentes sobre o assunto.</p>
                    <div class="popular-searches">
                        <p>Buscas populares:</p>
                        <div class="popular-tags">
                            <a href="noticias_ao_vivo.php?query=brasil" class="popular-tag">Brasil</a>
                            <a href="noticias_ao_vivo.php?query=mundo" class="popular-tag">Mundo</a>
                            <a href="noticias_ao_vivo.php?query=covid" class="popular-tag">COVID-19</a>
                            <a href="noticias_ao_vivo.php?query=eleições" class="popular-tag">Eleições</a>
                        </div>
                    </div>
                </div>
            <?php
                endif;
            endif;
            ?>
        </div>
        
        <?php if ($articles && count($articles) > 0): ?>
        <div class="load-more-container">
            <button id="load-more-btn" class="btn-load-more" type="button">
                <span class="load-more-icon" aria-hidden="true">⏬</span>
                <span class="load-more-text">Carregar mais notícias</span>
                <span class="load-more-spinner" style="display: none;" aria-hidden="true">⏳</span>
            </button>
        </div>
        <?php endif; ?>
    </section>

    <!-- Seção de filtros rápidos -->
    <aside class="quick-filters" aria-labelledby="filters-heading">
        <h2 id="filters-heading" class="filters-title">
            <span class="filters-icon" aria-hidden="true">🏷️</span>
            Filtros Rápidos
        </h2>
        
        <div class="filters-grid">
            <a href="noticias_ao_vivo.php?query=últimas+24+horas" class="filter-card">
                <span class="filter-icon" aria-hidden="true">⏰</span>
                <span class="filter-label">Últimas 24h</span>
            </a>
            
            <a href="noticias_ao_vivo.php?query=breaking+news" class="filter-card">
                <span class="filter-icon" aria-hidden="true">🚨</span>
                <span class="filter-label">Urgente</span>
            </a>
            
            <a href="noticias_ao_vivo.php?query=brasil" class="filter-card">
                <span class="filter-icon" aria-hidden="true">🇧🇷</span>
                <span class="filter-label">Brasil</span>
            </a>
            
            <a href="noticias_ao_vivo.php?query=internacional" class="filter-card">
                <span class="filter-icon" aria-hidden="true">🌍</span>
                <span class="filter-label">Internacional</span>
            </a>
        </div>
    </aside>
</div>

<!-- Script específico para notícias ao vivo -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Funcionalidade de compartilhamento
    document.querySelectorAll('.btn-share').forEach(button => {
        button.addEventListener('click', function() {
            const url = this.getAttribute('data-url');
            const title = this.getAttribute('data-title');
            
            if (navigator.share) {
                navigator.share({
                    title: title,
                    url: url
                }).catch(console.error);
            } else {
                // Feedback visual melhorado
                navigator.clipboard.writeText(url).then(() => {
                    // Feedback visual
                    const originalText = this.innerHTML;
                    this.innerHTML = '<span aria-hidden="true">✅</span><span class="share-text">Copiado!</span>';
                    
                    // Adiciona classe de animação
                    this.classList.add('share-success');
                    
                    setTimeout(() => {
                        this.innerHTML = originalText;
                        this.classList.remove('share-success');
                    }, 2000);
                    
                    // Anúncio para leitores de tela
                    const announcement = document.createElement('div');
                    announcement.setAttribute('aria-live', 'polite');
                    announcement.className = 'visually-hidden';
                    announcement.textContent = 'Link copiado para a área de transferência';
                    document.body.appendChild(announcement);
                    setTimeout(() => document.body.removeChild(announcement), 3000);
                });
            }
        });
    });
    
    // Melhorar o botão de carregar mais
    const loadMoreBtn = document.getElementById('load-more-btn');
    if (loadMoreBtn) {
        loadMoreBtn.addEventListener('click', function() {
            // Mostrar spinner
            this.querySelector('.load-more-text').textContent = 'Carregando...';
            this.querySelector('.load-more-spinner').style.display = 'inline-block';
            this.disabled = true;
            
            // Simular carregamento (aqui você implementaria a lógica real de carregamento)
            setTimeout(() => {
                // Restaurar estado do botão
                this.querySelector('.load-more-text').textContent = 'Carregar mais notícias';
                this.querySelector('.load-more-spinner').style.display = 'none';
                this.disabled = false;
                
                // Aqui você adicionaria as novas notícias ao DOM
                // Por enquanto, apenas exibimos uma mensagem
                const announcement = document.createElement('div');
                announcement.setAttribute('aria-live', 'polite');
                announcement.className = 'visually-hidden';
                announcement.textContent = 'Novas notícias carregadas';
                document.body.appendChild(announcement);
                setTimeout(() => document.body.removeChild(announcement), 3000);
            }, 1500);
        });
    }
});
</script>

<style>
/* Estilos adicionais para os botões melhorados */
.btn-share {
    display: flex;
    align-items: center;
    gap: 5px;
    background-color: #3b5998;
    color: white;
    border: none;
    border-radius: var(--border-radius-md);
    padding: var(--spacing-sm) var(--spacing-md);
    font-weight: 600;
    cursor: pointer;
    transition: all var(--transition-fast);
}

.btn-share:hover {
    background-color: #2d4373;
    transform: translateY(-2px);
}

.share-text {
    display: inline-block;
    font-size: var(--font-size-sm);
}

.share-success {
    background-color: #4CAF50 !important;
    animation: pulse 0.5s;
}

@keyframes pulse {
    0% { transform: scale(1); }
    50% { transform: scale(1.1); }
    100% { transform: scale(1); }
}

.btn-load-more {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-dark) 100%);
    color: white;
    border: none;
    border-radius: var(--border-radius-md);
    padding: var(--spacing-md) var(--spacing-xl);
    font-weight: 600;
    cursor: pointer;
    transition: all var(--transition-fast);
    margin: var(--spacing-xl) auto;
    min-width: 250px;
    box-shadow: var(--shadow-md);
}

.btn-load-more:hover {
    transform: translateY(-3px);
    box-shadow: var(--shadow-lg);
}

.btn-load-more:disabled {
    opacity: 0.7;
    cursor: not-allowed;
    transform: none;
}

.load-more-icon {
    font-size: 1.2em;
}

.load-more-spinner {
    animation: spin 1.5s linear infinite;
}

@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}
</style>

<?php require_once __DIR__ . '/includes/footer.php'; ?>

