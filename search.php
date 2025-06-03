<?php require_once 'includes/header.php'; ?>

<div class="container">
    <section class="search-results-section" aria-labelledby="search-results-heading">
        <h2 id="search-results-heading" class="page-title">Buscar Notícias</h2>

        <form action="search.php" method="get" class="search-page-form" role="search">
            <label for="search-input-page">Digite o termo que deseja buscar:</label>
            <input type="search" id="search-input-page" name="q"
                   value="<?php echo isset($_GET['q']) ? htmlspecialchars($_GET['q']) : ''; ?>"
                   placeholder="Ex: tecnologia, saúde, política..."
                   aria-describedby="search-help">
            <p id="search-help" class="visually-hidden">Pressione Enter para buscar após digitar o termo.</p>
            <button type="submit">Buscar Notícias</button>
        </form>

        <?php
        $searchTerm = null;
        if (isset($_GET['q']) && !empty(trim($_GET['q']))) {
            $searchTerm = trim($_GET['q']);
            echo "<h3>Resultados para: \"" . htmlspecialchars($searchTerm) . "\"</h3>";

            // Paginação simples (exemplo)
            $limit = 10; // Notícias por página
            $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
            $offset = ($page - 1) * $limit;

            $searched_news = getNewsFromDB($conn, $limit, $offset, $searchTerm, 'pt');

            if (!empty($searched_news)) {
                echo '<div class="news-grid">';
                foreach ($searched_news as $news_item) {
        ?>
                    <article class="news-item" aria-labelledby="news-title-search-<?php echo $news_item['id']; ?>">
                         <?php if (!empty($news_item['image_url'])): ?>
                            <img src="<?php echo htmlspecialchars($news_item['image_url']); ?>"
                                 alt="<?php echo generateAltText($news_item['title']); ?>" class="news-image">
                        <?php else: ?>
                            <div class="news-image placeholder-image" role="img" aria-label="Imagem não disponível">
                                <span>Sem Imagem</span>
                            </div>
                        <?php endif; ?>
                        <div class="news-content">
                            <h3 id="news-title-search-<?php echo $news_item['id']; ?>">
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
                echo '</div>';

                // Exemplo de Paginação (muito simples, precisaria de contagem total de itens para ser robusta)
                // Para uma paginação completa, você precisaria de uma função que conte o total de resultados para o termo.
                $prevPage = $page - 1;
                $nextPage = $page + 1;

                echo '<nav class="pagination" aria-label="Paginação de resultados da busca">';
                if ($page > 1) {
                    echo '<a href="search.php?q=' . urlencode($searchTerm) . '&page=' . $prevPage . '">Anterior</a>';
                } else {
                    echo '<span class="disabled">Anterior</span>';
                }
                echo '<span class="current-page">' . $page . '</span>';

                // Para saber se há próxima página, você precisaria buscar $limit + 1 itens
                // ou ter o total de itens. Assumindo que se veio $limit itens, pode haver mais.
                if (count($searched_news) == $limit) {
                     echo '<a href="search.php?q=' . urlencode($searchTerm) . '&page=' . $nextPage . '">Próxima</a>';
                } else {
                     echo '<span class="disabled">Próxima</span>';
                }
                echo '</nav>';

            } else {
                echo "<p class='no-results'>Nenhuma notícia encontrada para o termo \"" . htmlspecialchars($searchTerm) . "\". Tente buscar por outras palavras-chave.</p>";
            }
        } elseif (isset($_GET['q']) && empty(trim($_GET['q']))) {
             echo "<p>Por favor, digite um termo para buscar.</p>";
        }
        ?>
    </section>
</div>

<?php require_once 'includes/footer.php'; ?>