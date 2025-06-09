<?php 
require_once __DIR__ . '/includes/header.php';
?>

<div class="container">
    <!-- Banner de destaque -->
    <section class="hero-section" aria-labelledby="hero-heading">
        <div class="hero-content">
            <h2 id="hero-heading" class="hero-title">Notícias que importam, acessíveis para todos</h2>
            <p class="hero-subtitle" style="margin: 10px">Fique por dentro dos acontecimentos mais recentes com nosso portal de notícias totalmente acessível</p>
        </div>
    </section>

    <!-- Seção de Destaques como Carrossel -->
    <section class="carousel-section" aria-labelledby="carousel-heading">
        <h2 id="carousel-heading">
            <span class="section-icon" aria-hidden="true">🔥</span>
            Notícias em Destaque
        </h2>
        <div class="carousel-wrapper">
            <div class="carousel-container">
                <?php
                $destaques_ao_vivo = fetchArticlesFromGNews(null, 'pt', 3);
                if ($destaques_ao_vivo && count($destaques_ao_vivo) > 0):
                    foreach ($destaques_ao_vivo as $news_item_api):
                        // Grava no banco (evita duplicatas)
                        insert_noticias(
                            $conn,
                            $news_item_api['title'],
                            $news_item_api['description'],
                            $news_item_api['content'],
                            $news_item_api['url'],
                            $news_item_api['image'],
                            $news_item_api['publishedAt'],
                            $news_item_api['source']['name'],
                            $news_item_api['source']['url']
                        );
                        
                        // Preparação de variáveis para exibição
                        $id_unico_destaque      = md5($news_item_api['url'] ?? rand());
                        $title_api              = htmlspecialchars($news_item_api['title'] ?? 'Título indisponível', ENT_QUOTES, 'UTF-8');
                        $url_api                = htmlspecialchars($news_item_api['url'] ?? '#', ENT_QUOTES, 'UTF-8');
                        $imageUrl_api           = htmlspecialchars($news_item_api['image'] ?? '', ENT_QUOTES, 'UTF-8');
                        $sourceName_api         = htmlspecialchars($news_item_api['source']['name'] ?? 'Fonte desconhecida', ENT_QUOTES, 'UTF-8');
                        $publishedAtOriginal_api= $news_item_api['publishedAt'] ?? '';
                        $publishedAt_api        = $publishedAtOriginal_api 
                                                   ? formatDate($publishedAtOriginal_api) 
                                                   : 'Data indisponível';
                        $altText_api            = generateAltText($news_item_api['title'] ?? '');
                        $description_api        = htmlspecialchars($news_item_api['description'] ?? 'Descrição indisponível.', ENT_QUOTES, 'UTF-8');
                        
                        if (mb_strlen($description_api, 'UTF-8') > 120) {
                            $description_api = mb_substr($description_api, 0, 117, 'UTF-8') . '...';
                        }
                        
                        $readText_api = addslashes(strip_tags(
                            ($news_item_api['title'] ?? '') . ' — ' . ($news_item_api['description'] ?? '')
                        ));
                ?>
                <article class="news-item" aria-labelledby="news-title-destaque-<?= $id_unico_destaque ?>">
                    <div class="news-item-inner">
                        <?php if ($imageUrl_api): ?>
                            <div class="news-image-container">
                                <img 
                                    src="<?= $imageUrl_api ?>" 
                                    alt="<?= $altText_api ?>" 
                                    class="news-image"
                                    loading="lazy"
                                >
                            </div>
                        <?php else: ?>
                            <div class="placeholder-image" role="img" aria-label="Imagem não disponível">
                                <span>Sem Imagem</span>
                            </div>
                        <?php endif; ?>
                        
                        <div class="news-content">
                            <h3 id="news-title-destaque-<?= $id_unico_destaque ?>" class="news-title">
                                <a href="<?= $url_api ?>" target="_blank" rel="noopener noreferrer">
                                    <?= $title_api ?>
                                </a>
                            </h3>
                            
                            <div class="news-meta">
                                <span class="source"><?= $sourceName_api ?></span>
                                <time datetime="<?= htmlspecialchars($publishedAtOriginal_api, ENT_QUOTES) ?>">
                                    <?= $publishedAt_api ?>
                                </time>
                            </div>
                            
                            <p class="news-description"><?= $description_api ?></p>
                            
                            <div class="card-actions">
                                <button
                                    class="btn-read"
                                    data-text="<?= $readText_api ?>"
                                    aria-label="Ouvir notícia"
                                    type="button"
                                >
                                    <span aria-hidden="true">🔊</span>
                                </button>
                                
                                <form action="feedback.php" method="post">
                                    <input type="hidden" name="image" value="<?= $imageUrl_api?>">
                                    <input type="hidden" name="url" value="<?= $url_api ?>">
                                    <input type="hidden" name="title" value="<?= $title_api ?>">
                                    <input type="submit" value="Avaliar">
                                </form>

                                <a
                                    href="<?= $url_api ?>"
                                    target="_blank"
                                    rel="noopener noreferrer"
                                    class="btn-link"
                                >
                                    Ler mais
                                </a>
                            </div>
                        </div>
                    </div>
                </article>
                <?php
                    endforeach;
                else:
                ?>
                <div class="no-results">
                    <p>Nenhuma notícia em destaque disponível no momento.</p>
                    <p>Tente novamente mais tarde ou explore outras seções do portal.</p>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </section>

    <!-- Seção de Categorias -->
    <section class="categories-section" aria-labelledby="categories-heading">
        <h2 id="categories-heading" class="section-title">
            <span class="section-icon" aria-hidden="true">📋</span>
            Categorias
        </h2>
        
        <div class="categories-grid">
            <a href="search.php?q=política" class="category-card">
                <span class="category-icon" aria-hidden="true">🏛️</span>
                <h3 class="category-title">Política</h3>
            </a>
            
            <a href="search.php?q=economia" class="category-card">
                <span class="category-icon" aria-hidden="true">💰</span>
                <h3 class="category-title">Economia</h3>
            </a>
            
            <a href="search.php?q=saúde" class="category-card">
                <span class="category-icon" aria-hidden="true">🏥</span>
                <h3 class="category-title">Saúde</h3>
            </a>
            
            <a href="search.php?q=tecnologia" class="category-card">
                <span class="category-icon" aria-hidden="true">💻</span>
                <h3 class="category-title">Tecnologia</h3>
            </a>
            
            <a href="search.php?q=esporte" class="category-card">
                <span class="category-icon" aria-hidden="true">⚽</span>
                <h3 class="category-title">Esportes</h3>
            </a>
            
            <a href="search.php?q=entretenimento" class="category-card">
                <span class="category-icon" aria-hidden="true">🎬</span>
                <h3 class="category-title">Entretenimento</h3>
            </a>
        </div>
    </section>

    <!-- Seção de Últimas Notícias -->
    <section class="latest-news-section" aria-labelledby="latest-news-heading">
        <h2 id="latest-news-heading" class="section-title">
            <span class="section-icon" aria-hidden="true">📰</span>
            Últimas Notícias
        </h2>
        
        <div class="news-grid">
            <?php
            $general_news = getNewsFromDB($conn, 9, 0, null, 'pt', 1);
            if (!empty($general_news)):
                foreach ($general_news as $news_item):
                    $id_db        = $news_item['id'];
                    $title_db     = htmlspecialchars($news_item['title'], ENT_QUOTES);
                    $url_db       = htmlspecialchars($news_item['url'], ENT_QUOTES);
                    $img_db       = htmlspecialchars($news_item['image_url'] ?? '', ENT_QUOTES);
                    $alt_db       = generateAltText($news_item['title']);
                    $source_db    = htmlspecialchars($news_item['source_name'] ?? 'Fonte desconhecida', ENT_QUOTES);
                    $pub_iso_db   = $news_item['published_at'];
                    $pub_db       = $pub_iso_db ? formatDate($pub_iso_db) : 'Data indisponível';
                    $desc_db      = htmlspecialchars(strip_tags($news_item['description']), ENT_QUOTES);
                    $readText_db  = addslashes(strip_tags($news_item['title'] . ' — ' . $news_item['description']));
            ?>
            <article class="news-item" aria-labelledby="news-title-db-<?= $id_db ?>">
                <div class="news-item-inner">
                    <?php if ($img_db): ?>
                        <div class="news-image-container">
                            <img 
                                src="<?= $img_db ?>" 
                                alt="<?= $alt_db ?>" 
                                class="news-image"
                                loading="lazy"
                            >
                        </div>
                    <?php else: ?>
                        <div class="placeholder-image" role="img" aria-label="Imagem não disponível">
                            <span>Sem Imagem</span>
                        </div>
                    <?php endif; ?>
                    
                    <div class="news-content">
                        <h3 id="news-title-db-<?= $id_db ?>" class="news-title">
                            <a href="<?= $url_db ?>" target="_blank" rel="noopener noreferrer">
                                <?= $title_db ?>
                            </a>
                        </h3>
                        
                        <div class="news-meta">
                            <span class="source"><?= $source_db ?></span>
                            <time datetime="<?= htmlspecialchars($pub_iso_db, ENT_QUOTES) ?>">
                                <?= $pub_db ?>
                            </time>
                        </div>
                        
                        <p class="news-description"><?= $desc_db ?></p>
                        
                        <div class="card-actions">
                            <button
                                class="btn-read"
                                data-text="<?= $readText_db ?>"
                                aria-label="Ouvir notícia"
                                type="button"
                            >
                                <span aria-hidden="true">🔊</span>
                            </button>
                            
                            <form action="feedback.php" method="get">
                                <input type="hidden" name="image" value="<?= $img_db?>">
                                <input type="hidden" name="url" value="<?= $url_db ?>">
                                <input type="hidden" name="title" value="<?= $title_db ?>">
                                <input type="submit" value="Avaliar">
                            </form>

                            <a
                                href="<?= $url_db ?>"
                                target="_blank"
                                rel="noopener noreferrer"
                                class="btn-link"
                            >
                                Ler mais
                            </a>
                        </div>
                    </div>
                </div>
            </article>
            <?php
                endforeach;
            else:
            ?>
            <div class="no-results">
                <p>Nenhuma notícia encontrada no banco de dados.</p>
                <p>Tente novamente mais tarde ou explore outras seções do portal.</p>
            </div>
            <?php endif; ?>
        </div>
    </section>
</div>

<?php
require_once __DIR__ . '/includes/footer.php';
?>

