<?php 
require_once 'includes/header.php';
?>

<div class="container">

    <section class="carousel-section" aria-labelledby="carousel-heading">
        <h2 id="carousel-heading">Notícias em Destaque (Ao Vivo)</h2>
        <div class="carousel-container">
            <?php
            // ANTES: $carousel_news = getNewsFromDB($conn, 3, 0, null, 'pt');
            // AGORA: Buscar 3 notícias de destaque diretamente da API GNews
            $destaques_ao_vivo = fetchArticlesFromGNews(null, 'pt', 3); // (termo=null, lang='pt', max=3)

            if ($destaques_ao_vivo && count($destaques_ao_vivo) > 0) {
                foreach ($destaques_ao_vivo as $news_item_api) {
                    // Roda a função de insert no banco
                    insert_noticias($conn, $news_item_api['title'], $news_item_api['description'], $news_item_api['content'], $news_item_api['url'], $news_item_api['image'], $news_item_api['publishedAt'], $news_item_api['source']['name'], $news_item_api['source']['url']);
                    // Adaptação dos campos para o formato da API GNews
                    $id_unico_destaque = md5($news_item_api['url'] ?? rand());
                    $title_api = isset($news_item_api['title']) ? htmlspecialchars($news_item_api['title']) : 'Título indisponível';
                    $url_api = isset($news_item_api['url']) ? htmlspecialchars($news_item_api['url']) : '#';
                    $imageUrl_api = isset($news_item_api['image']) ? htmlspecialchars($news_item_api['image']) : '';
                    $sourceName_api = isset($news_item_api['source']['name']) ? htmlspecialchars($news_item_api['source']['name']) : 'Fonte desconhecida';
                    $publishedAtOriginal_api = $news_item_api['publishedAt'] ?? '';
                    $publishedAt_api = isset($news_item_api['publishedAt']) ? formatDate($news_item_api['publishedAt']) : 'Data indisponível';
                    $altText_api = generateAltText($news_item_api['title'] ?? 'Notícia Destaque');
                    $description_api = isset($news_item_api['description']) ? htmlspecialchars($news_item_api['description']) : 'Descrição indisponível.';
                    if (mb_strlen($description_api, 'UTF-8') > 120) { // Limitar descrição
                        $description_api = mb_substr($description_api, 0, 117, 'UTF-8') . "...";
                    }
            ?>
                    <article class="news-item" aria-labelledby="news-title-destaque-<?php echo $id_unico_destaque; ?>">
                        <?php if (!empty($imageUrl_api)): ?>
                            <img src="<?php echo $imageUrl_api; ?>"
                                 alt="<?php echo $altText_api; ?>" class="news-image">
                        <?php else: ?>
                            <div class="news-image placeholder-image" role="img" aria-label="Imagem não disponível">
                                <span>Sem Imagem</span>
                            </div>
                        <?php endif; ?>
                        <div class="news-content">
                            <h3 id="news-title-destaque-<?php echo $id_unico_destaque; ?>">
                                <a href="<?php echo $url_api; ?>" target="_blank" rel="noopener noreferrer">
                                    <?php echo $title_api; ?>
                                </a>
                            </h3>
                            <p class="news-meta">
                                <span class="source"><?php echo $sourceName_api; ?></span> -
                                <time datetime="<?php echo $publishedAtOriginal_api; ?>">
                                    <?php echo $publishedAt_api; ?>
                                </time>
                            </p>
                            <p class="news-description">
                                <?php echo $description_api; ?>
                            </p>
                            <a href="<?php echo $url_api; ?>" target="_blank" rel="noopener noreferrer" class="read-more">
                                Ler mais <span class="visually-hidden"> sobre <?php echo $title_api; ?></span>
                            </a>
                        </div>
                    </article>
            <?php
                }
            } else {
                echo "<p>Nenhuma notícia em destaque disponível no momento (API). Verifique sua chave API ou tente mais tarde.</p>";
            }
            ?>
        </div>
    </section>

    <hr style="margin: 2rem 0;">

    <section class="general-news" aria-labelledby="general-news-heading">
        <h2 id="general-news-heading" class="page-title">Últimas Notícias</h2>
        <div class="news-grid">
            <?php
            // Esta seção continua buscando do banco de dados como antes
            $general_news = getNewsFromDB($conn, 9, 0, null, 'pt'); // Pega 9 notícias do BD (ajuste o offset se os destaques eram do BD antes)
            if (!empty($general_news)) {
                foreach ($general_news as $news_item) { // Usando $news_item (original) para não confundir
            ?>
                    <article class="news-item" aria-labelledby="news-title-db-<?php echo $news_item['id']; ?>">
                        <?php if (!empty($news_item['image_url'])): ?>
                            <img src="<?php echo htmlspecialchars($news_item['image_url']); ?>"
                                 alt="<?php echo generateAltText($news_item['title']); ?>" class="news-image">
                        <?php else: ?>
                            <div class="news-image placeholder-image" role="img" aria-label="Imagem não disponível">
                                <span>Sem Imagem</span>
                            </div>
                        <?php endif; ?>
                        <div class="news-content">
                             <h3 id="news-title-db-<?php echo $news_item['id']; ?>">
                                <a href="<?php echo htmlspecialchars($news_item['url']); ?>" target="_blank" rel="noopener noreferrer">
                                    <?php echo htmlspecialchars($news_item['title']); ?>
                                </a>
                            </h3>
                            <p class="news-meta">
                                <span class="source"><?php echo htmlspecialchars($news_item['source_name']); ?></span> -
                                <time datetime="<?php echo $news_item['published_at']; ?>">
                                    <?php echo formatDate($news_item['published_at']); ?>
                                </time>
                            </p>
                            <p class="news-description">
                                <?php echo htmlspecialchars(strip_tags($news_item['description'])); ?>
                            </p>
                            <a href="<?php echo htmlspecialchars($news_item['url']); ?>" target="_blank" rel="noopener noreferrer" class="read-more">
                                Ler mais <span class="visually-hidden"> sobre <?php echo htmlspecialchars($news_item['title']); ?></span>
                            </a>
                        </div>
                    </article>
            <?php
                }
            } else {
                 echo "<p>Nenhuma notícia encontrada no banco de dados. Tente atualizar as notícias através do script apropriado.</p>";
            }
            insert_feedback($conn, 64, 5);
            ?>
        </div>
        </section>

</div>

<?php require_once 'includes/footer.php'; ?>