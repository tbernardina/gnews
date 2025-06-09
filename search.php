<?php 
require_once __DIR__ . '/includes/header.php';
?>

<div class="container">
    <!-- Cabeçalho da página -->
    <section class="page-header" aria-labelledby="page-heading">
        <h1 id="page-heading" class="page-title">
            <span class="page-icon" aria-hidden="true">🔍</span>
            Buscar Notícias
        </h1>
        <p class="page-description">
            Encontre notícias específicas usando nossa ferramenta de busca avançada. Digite palavras-chave para localizar as informações que você procura.
        </p>
    </section>

    <!-- Formulário de Busca Avançada -->
    <section class="search-section" aria-labelledby="search-heading">
        <h2 id="search-heading" class="visually-hidden">Formulário de busca</h2>
        
        <form action="search.php" method="get" class="search-form" role="search">
            <div class="search-form-container">
                <div class="search-input-group">
                    <label for="search-input" class="search-label">
                        <span class="search-icon" aria-hidden="true">🔍</span>
                        Termo de busca
                    </label>
                    <input
                        type="search"
                        id="search-input"
                        name="q"
                        value="<?php echo isset($_GET['q']) ? htmlspecialchars($_GET['q'], ENT_QUOTES, 'UTF-8') : ''; ?>"
                        placeholder="Ex: tecnologia, saúde, política..."
                        aria-label="Digite o termo para buscar notícias"
                        autocomplete="off"
                        spellcheck="false"
                        maxlength="100"
                        required
                    >
                    <button type="submit" class="search-button" aria-label="Executar busca">
                        <span aria-hidden="true">🔍</span>
                        Buscar
                    </button>
                </div>
                
                <div class="search-tips">
                    <details class="search-help">
                        <summary>Dicas de busca</summary>
                        <ul>
                            <li>Use palavras-chave específicas para resultados mais precisos</li>
                            <li>Combine termos relacionados (ex: "economia brasil")</li>
                            <li>Experimente sinônimos se não encontrar resultados</li>
                        </ul>
                    </details>
                </div>
            </div>
        </form>
    </section>

    <!-- Resultados da busca -->
    <?php
    $term = isset($_GET['q']) ? trim($_GET['q']) : '';
    $page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
    $limit = 12;
    $offset = ($page - 1) * $limit;
    
    if ($term !== ''):
    ?>
        <section class="results-section" aria-labelledby="results-heading">
            <h2 id="results-heading" class="results-title">
                <span class="results-icon" aria-hidden="true">📊</span>
                Resultados para: "<span class="search-term"><?= htmlspecialchars($term, ENT_QUOTES, 'UTF-8') ?></span>"
            </h2>

            <?php
            $results = getNewsFromDB($conn, $limit, $offset, $term, 'pt');
            
            if (!empty($results)):
            ?>
                <div class="results-meta">
                    <p class="results-count">
                        Mostrando página <?= $page ?> dos resultados encontrados
                    </p>
                </div>

                <div class="news-grid">
                    <?php foreach ($results as $index => $news): 
                        $id = $news['id'];
                        $title = htmlspecialchars($news['title'], ENT_QUOTES, 'UTF-8');
                        $url = htmlspecialchars($news['url'], ENT_QUOTES, 'UTF-8');
                        $img = htmlspecialchars($news['image_url'] ?? '', ENT_QUOTES, 'UTF-8');
                        $alt = generateAltText($news['title']);
                        $source = htmlspecialchars($news['source_name'] ?? 'Fonte desconhecida', ENT_QUOTES, 'UTF-8');
                        $pubIso = $news['published_at'];
                        $pub = $pubIso ? formatDate($pubIso) : 'Data indisponível';
                        $desc = htmlspecialchars(strip_tags($news['description']), ENT_QUOTES, 'UTF-8');
                        $readText = addslashes(strip_tags($news['title'] . ' — ' . $news['description']));
                    ?>
                    <article class="news-item" aria-labelledby="search-title-<?= $id ?>" style="animation-delay: <?= $index * 0.1 ?>s;">
                        <div class="news-item-inner">
                            <?php if ($img): ?>
                                <div class="news-image-container">
                                    <img 
                                        src="<?= $img ?>" 
                                        alt="<?= $alt ?>" 
                                        class="news-image"
                                        loading="lazy"
                                        onerror="this.parentElement.innerHTML='<div class=&quot;placeholder-image&quot; role=&quot;img&quot; aria-label=&quot;Imagem não disponível&quot;><span>Sem Imagem</span></div>'"
                                    >
                                </div>
                            <?php else: ?>
                                <div class="placeholder-image" role="img" aria-label="Imagem não disponível">
                                    <span>Sem Imagem</span>
                                </div>
                            <?php endif; ?>
                            
                            <div class="news-content">
                                <h3 id="search-title-<?= $id ?>" class="news-title">
                                    <a href="<?= $url ?>" target="_blank" rel="noopener noreferrer">
                                        <?= $title ?>
                                    </a>
                                </h3>
                                
                                <div class="news-meta">
                                    <span class="source">
                                        <span class="source-icon" aria-hidden="true">📰</span>
                                        <?= $source ?>
                                    </span>
                                    <time datetime="<?= htmlspecialchars($pubIso, ENT_QUOTES) ?>" class="publish-time">
                                        <span class="time-icon" aria-hidden="true">🕒</span>
                                        <?= $pub ?>
                                    </time>
                                </div>
                                
                                <p class="news-description"><?= $desc ?></p>
                                
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
                                    
                                    <form action="feedback.php" method="get">
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
                                </div>
                            </div>
                        </div>
                    </article>
                    <?php endforeach; ?>
                </div>

                <!-- Paginação -->
                <nav class="pagination" aria-label="Navegação de páginas dos resultados" style="margin: 20px; text-align: center;">
                    <?php
                    $prev = $page > 1 ? $page - 1 : null;
                    $next = count($results) === $limit ? $page + 1 : null;
                    ?>
                    
                    <?php if ($prev): ?>
                        <a href="search.php?q=<?= urlencode($term) ?>&page=<?= $prev ?>" 
                           class="pagination-btn pagination-prev"
                           aria-label="Ir para a página anterior">
                            <span aria-hidden="true">‹</span>
                            Anterior
                        </a>
                    <?php else: ?>
                        <span class="pagination-btn pagination-prev disabled" aria-disabled="true">
                            <span aria-hidden="true">‹</span>
                            Anterior
                        </span>
                    <?php endif; ?>
                    
                    <span class="pagination-current" aria-current="page">
                        Página <?= $page ?>
                    </span>
                    
                    <?php if ($next): ?>
                        <a href="search.php?q=<?= urlencode($term) ?>&page=<?= $next ?>" 
                           class="pagination-btn pagination-next"
                           aria-label="Ir para a próxima página">
                            Próxima
                            <span aria-hidden="true">›</span>
                        </a>
                    <?php else: ?>
                        <span class="pagination-btn pagination-next disabled" aria-disabled="true">
                            Próxima
                            <span aria-hidden="true">›</span>
                        </span>
                    <?php endif; ?>
                </nav>

            <?php else: ?>
                <div class="no-results">
                    <div class="no-results-icon" aria-hidden="true">🔍</div>
                    <h3>Nenhum resultado encontrado</h3>
                    <p>Não encontramos notícias para "<strong><?= htmlspecialchars($term, ENT_QUOTES, 'UTF-8') ?></strong>".</p>
                    <div class="no-results-suggestions">
                        <p>Sugestões:</p>
                        <ul>
                            <li>Verifique a ortografia das palavras</li>
                            <li>Tente usar termos mais gerais</li>
                            <li>Use sinônimos ou palavras relacionadas</li>
                            <li>Remova acentos ou caracteres especiais</li>
                        </ul>
                    </div>
                    <div class="alternative-searches">
                        <p>Ou tente buscar por:</p>
                        <div class="alternative-tags">
                            <a href="search.php?q=política" class="alternative-tag">Política</a>
                            <a href="search.php?q=economia" class="alternative-tag">Economia</a>
                            <a href="search.php?q=saúde" class="alternative-tag">Saúde</a>
                            <a href="search.php?q=tecnologia" class="alternative-tag">Tecnologia</a>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </section>

    <?php elseif (isset($_GET['q'])): ?>
        <section class="empty-search-section">
            <div class="empty-search">
                <div class="empty-search-icon" aria-hidden="true">⚠️</div>
                <h2>Termo de busca vazio</h2>
                <p>Por favor, digite um termo para buscar notícias.</p>
                <button type="button" onclick="document.getElementById('search-input').focus()" class="btn-focus-search">
                    Voltar ao campo de busca
                </button>
            </div>
        </section>

    <?php else: ?>
        <!-- Seção de buscas populares quando não há termo -->
        <section class="popular-searches-section" aria-labelledby="popular-heading">
            <h2 id="popular-heading" class="section-title">
                <span class="section-icon" aria-hidden="true">🔥</span>
                Buscas Populares
            </h2>
            
            <div class="popular-grid">
                <a href="search.php?q=política" class="popular-card">
                    <span class="popular-icon" aria-hidden="true">🏛️</span>
                    <h3>Política</h3>
                    <p>Últimas notícias políticas do Brasil e mundo</p>
                </a>
                
                <a href="search.php?q=economia" class="popular-card">
                    <span class="popular-icon" aria-hidden="true">💰</span>
                    <h3>Economia</h3>
                    <p>Mercado financeiro, inflação e indicadores</p>
                </a>
                
                <a href="search.php?q=saúde" class="popular-card">
                    <span class="popular-icon" aria-hidden="true">🏥</span>
                    <h3>Saúde</h3>
                    <p>Medicina, pesquisas e saúde pública</p>
                </a>
                
                <a href="search.php?q=tecnologia" class="popular-card">
                    <span class="popular-icon" aria-hidden="true">💻</span>
                    <h3>Tecnologia</h3>
                    <p>Inovações, gadgets e ciência</p>
                </a>
                
                <a href="search.php?q=esporte" class="popular-card">
                    <span class="popular-icon" aria-hidden="true">⚽</span>
                    <h3>Esportes</h3>
                    <p>Futebol, olimpíadas e competições</p>
                </a>
                
                <a href="search.php?q=entretenimento" class="popular-card">
                    <span class="popular-icon" aria-hidden="true">🎬</span>
                    <h3>Entretenimento</h3>
                    <p>Cinema, música e celebridades</p>
                </a>
            </div>
        </section>

        <!-- Seção de dicas de busca -->
        <section class="search-tips-section" aria-labelledby="tips-heading">
            <h2 id="tips-heading" class="section-title">
                <span class="section-icon" aria-hidden="true">💡</span>
                Dicas para uma Busca Eficiente
            </h2>
            
            <div class="tips-grid">
                <div class="tip-card">
                    <div class="tip-icon" aria-hidden="true">🎯</div>
                    <h3>Seja específico</h3>
                    <p>Use termos específicos para encontrar exatamente o que procura. Ex: "vacina covid brasil" em vez de apenas "vacina".</p>
                </div>
                
                <div class="tip-card">
                    <div class="tip-icon" aria-hidden="true">🔤</div>
                    <h3>Palavras-chave</h3>
                    <p>Utilize palavras-chave relevantes para o assunto que deseja encontrar. Evite artigos e preposições.</p>
                </div>
                
                <div class="tip-card">
                    <div class="tip-icon" aria-hidden="true">📅</div>
                    <h3>Considere a data</h3>
                    <p>Para notícias recentes, adicione termos como "últimas 24 horas" ou "semana passada" à sua busca.</p>
                </div>
                
                <div class="tip-card">
                    <div class="tip-icon" aria-hidden="true">🔄</div>
                    <h3>Tente sinônimos</h3>
                    <p>Se não encontrar resultados, experimente usar sinônimos ou termos relacionados ao seu tema de interesse.</p>
                </div>
            </div>
        </section>
    <?php endif; ?>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>

