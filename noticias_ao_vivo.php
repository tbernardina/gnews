<?php
require_once 'includes/header.php'; // Garante que functions.php (com API Key e funções) seja carregado
?>

<div class="container">
    <main>
        <section class="live-news-section" aria-labelledby="live-news-heading">
            <h2 id="live-news-heading" class="page-title">Notícias Ao Vivo da GNews</h2>

            <form action="noticias_ao_vivo.php" method="GET" class="search-page-form" role="search">
                <label for="q_live">Buscar notícias ao vivo por termo:</label>
                <input type="search" id="q_live" name="q" value="<?php echo isset($_GET['q']) ? htmlspecialchars($_GET['q']) : ''; ?>" placeholder="Ex: tecnologia, Brasil, eleições...">
                <button type="submit">Buscar</button>
            </form>
            <hr style="margin: 1.5rem 0;">

            <?php
            $termo_busca_live = null;
            if (isset($_GET['q']) && !empty(trim($_GET['q']))) {
                $termo_busca_live = trim($_GET['q']);
                echo "<h3>Resultados para: \"" . htmlspecialchars($termo_busca_live) . "\"</h3><br>";
            } else {
                echo "<h3>Principais Notícias no momento:</h3><br>";
            }

            $idioma_live = 'pt';
            $maximo_artigos_live = 10; // Quantidade de artigos a serem buscados

            // Log para debug
            error_log("NOTICIAS_AO_VIVO: Buscando com query='{$termo_busca_live}', lang='{$idioma_live}', max='{$maximo_artigos_live}'");

            $live_articles = fetchArticlesFromGNews($termo_busca_live, $idioma_live, $maximo_artigos_live);

            if ($live_articles === null) {
                echo "<p style='color:red;'><strong>Falha ao buscar notícias da GNews.</strong> Verifique sua API Key em `includes/functions.php` e os logs de erro do PHP/Apache.</p>";
            } elseif (empty($live_articles)) {
                if ($termo_busca_live) {
                    echo "<p>Nenhuma notícia encontrada na API para o termo \"<strong>" . htmlspecialchars($termo_busca_live) . "</strong>\". Tente palavras-chave diferentes.</p>";
                } else {
                    echo "<p>Nenhuma notícia principal encontrada na API neste momento. Tente mais tarde.</p>";
                }
            } else {
                echo '<div class="news-grid">'; // Reutilizar a classe de grid

                foreach ($live_articles as $article) {
                    // Roda a função de insert no banco
                    insert_noticias($conn, $article['title'], $article['description'], $article['content'], $article['url'], $article['image'], $article['publishedAt'], $article['source']['name'], $article['source']['url']);
                    // Adaptação e exibição dos campos da API
                    $title = isset($article['title']) ? htmlspecialchars($article['title']) : 'Título indisponível';
                    $description = isset($article['description']) ? htmlspecialchars($article['description']) : 'Descrição indisponível.';
                    if (mb_strlen($description, 'UTF-8') > 150) {
                        $description = mb_substr($description, 0, 147, 'UTF-8') . "...";
                    }
                    $url = isset($article['url']) ? htmlspecialchars($article['url']) : '#';
                    $imageUrl = isset($article['image']) ? htmlspecialchars($article['image']) : '';
                    $sourceName = isset($article['source']['name']) ? htmlspecialchars($article['source']['name']) : 'Fonte desconhecida';
                    $publishedAtOriginal = $article['publishedAt'] ?? '';
                    $publishedAt = isset($article['publishedAt']) ? formatDate($article['publishedAt']) : 'Data indisponível';
                    $altText = generateAltText($article['title'] ?? 'Notícia');
                    $id_unico_artigo_live = md5($url . ($article['title'] ?? rand()));


                    echo '<article class="news-item" aria-labelledby="news-title-live-' . $id_unico_artigo_live . '">';
                    if (!empty($imageUrl)) {
                        echo '<img src="' . $imageUrl . '" alt="' . $altText . '" class="news-image">';
                    } else {
                        echo '<div class="news-image placeholder-image" role="img" aria-label="Imagem não disponível"><span>Sem Imagem</span></div>';
                    }
                    echo '<div class="news-content">';
                    echo '<h3 id="news-title-live-' . $id_unico_artigo_live . '"><a href="' . $url . '" target="_blank" rel="noopener noreferrer">' . $title . '</a></h3>';
                    echo '<p class="news-meta"><span class="source">' . $sourceName . '</span> - <time datetime="' . $publishedAtOriginal . '">' . $publishedAt . '</time></p>';
                    echo '<p class="news-description">' . $description . '</p>';
                    echo '<a href="' . $url . '" target="_blank" rel="noopener noreferrer" class="read-more">Ler na fonte</a>';
                    echo '</div>';
                    echo '</article>';
                }
                echo '</div>';
            }
            ?>
        </section>
    </main>
</div>

<?php require_once 'includes/footer.php'; ?>