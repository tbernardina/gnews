<?php
require_once 'db_config.php';
require_once 'includes/functions.php';

echo "<!DOCTYPE html><html lang='pt-BR'><head><meta charset='UTF-8'><title>Atualizar Notícias</title></head><body>";
echo "<h1>Atualizando Notícias da GNews...</h1>";
set_time_limit(300); // Aumenta o limite de tempo de execução para 5 minutos

$articles = fetchArticlesFromGNews(null, 'pt', 20); // Busca as 20 principais notícias em Português

if ($articles) {
    $savedCount = 0;
    $failedCount = 0;
    echo "<p>Encontrados " . count($articles) . " artigos.</p>";
    echo "<ul>";
    foreach ($articles as $article) {
        if (saveArticleToDB($conn, $article, 'pt')) {
            echo "<li>Notícia salva ou já existente: " . htmlspecialchars($article['title']) . "</li>";
            $savedCount++;
        } else {
            echo "<li style='color:red;'>Falha ao salvar: " . htmlspecialchars($article['title']) . "</li>";
            $failedCount++;
        }
        // Pequena pausa para não sobrecarregar o servidor ou a API
        usleep(200000); // 0.2 segundos
    }
    echo "</ul>";
    echo "<p><strong>Concluído. Artigos processados: {$savedCount}. Falhas: {$failedCount}.</strong></p>";
} else {
    echo "<p style='color:red;'>Nenhum artigo encontrado ou erro na API GNews.</p>";
}

$conn->close();
echo "</body></html>";
?>