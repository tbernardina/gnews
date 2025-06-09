<?php
require_once __DIR__ . '/db_config.php';
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/header.php';

// Verificar se URL e título foram fornecidos
$url = $_GET['url'] ?? '';
$title = $_GET['title'] ?? '';

if (empty($url)) {
    header('Location: admin.php');
    exit;
}

// Buscar feedbacks com comentários
$feedbacks = getFeedbacksWithComments($conn, $url);
?>

<div class="container">
    <!-- Cabeçalho da página -->
    <section class="page-header" aria-labelledby="page-heading">
        <h1 id="page-heading" class="page-title">
            <span class="page-icon" aria-hidden="true">💬</span>
            Feedbacks com Comentários
        </h1>
        <p class="page-description">
            Visualize todos os comentários deixados pelos usuários para esta notícia.
        </p>
    </section>

    <!-- Informações da notícia -->
    <section class="news-info-section">
        <div class="news-info-card">
            <h2 class="news-info-title">
                <span class="news-icon" aria-hidden="true">📰</span>
                <?= htmlspecialchars($title, ENT_QUOTES, 'UTF-8') ?>
            </h2>
            <div class="news-info-actions">
                <a href="<?= htmlspecialchars($url, ENT_QUOTES, 'UTF-8') ?>" target="_blank" rel="noopener noreferrer" class="btn-view-news">
                    <span aria-hidden="true">🔗</span>
                    Ver Notícia Original
                </a>
                <a href="admin.php" class="btn-back">
                    <span aria-hidden="true">⬅️</span>
                    Voltar ao Admin
                </a>
            </div>
        </div>
    </section>

    <!-- Seção de feedbacks -->
    <section class="feedbacks-section" aria-labelledby="feedbacks-heading">
        <h2 id="feedbacks-heading" class="feedbacks-title">
            <span class="feedbacks-icon" aria-hidden="true">📝</span>
            Comentários dos Usuários
            <span class="feedbacks-count">(<?= count($feedbacks) ?> comentários)</span>
        </h2>

        <?php if (count($feedbacks) > 0): ?>
            <div class="feedbacks-list">
                <?php foreach ($feedbacks as $index => $feedback): ?>
                    <article class="feedback-item" aria-labelledby="feedback-<?= $index ?>">
                        <div class="feedback-header">
                            <div class="feedback-rating">
                                <?php
                                $rating = (int)$feedback['NOTA_FEEDBACK'];
                                for ($i = 1; $i <= 5; $i++):
                                    if ($i <= $rating):
                                ?>
                                    <span class="star filled" aria-hidden="true">★</span>
                                <?php else: ?>
                                    <span class="star empty" aria-hidden="true">☆</span>
                                <?php 
                                    endif;
                                endfor; 
                                ?>
                                <span class="rating-text"><?= $rating ?>/5</span>
                            </div>
                            <div class="feedback-meta">
                                <span class="feedback-user">
                                    <span aria-hidden="true">👤</span>
                                    Usuário <?= substr(md5($feedback['IP_USER']), 0, 8) ?>
                                </span>
                            </div>
                        </div>
                        
                        <div class="feedback-content">
                            <blockquote class="feedback-comment">
                                "<?= htmlspecialchars($feedback['COMENTARIO'], ENT_QUOTES, 'UTF-8') ?>"
                            </blockquote>
                        </div>
                        
                        <div class="feedback-footer">
                            <div class="feedback-sentiment">
                                <?php
                                $sentiment_class = '';
                                $sentiment_icon = '';
                                $sentiment_text = '';
                                
                                if ($rating >= 4) {
                                    $sentiment_class = 'positive';
                                    $sentiment_icon = '😊';
                                    $sentiment_text = 'Positivo';
                                } elseif ($rating >= 3) {
                                    $sentiment_class = 'neutral';
                                    $sentiment_icon = '😐';
                                    $sentiment_text = 'Neutro';
                                } else {
                                    $sentiment_class = 'negative';
                                    $sentiment_icon = '😞';
                                    $sentiment_text = 'Negativo';
                                }
                                ?>
                                <span class="sentiment <?= $sentiment_class ?>">
                                    <span aria-hidden="true"><?= $sentiment_icon ?></span>
                                    <?= $sentiment_text ?>
                                </span>
                            </div>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>
            
            <!-- Estatísticas dos feedbacks -->
            <div class="feedback-stats">
                <h3 class="stats-title">
                    <span aria-hidden="true">📊</span>
                    Estatísticas dos Comentários
                </h3>
                
                <div class="stats-grid">
                    <?php
                    $total_comments = count($feedbacks);
                    $ratings = array_column($feedbacks, 'NOTA_FEEDBACK');
                    $average_rating = $total_comments > 0 ? array_sum($ratings) / $total_comments : 0;
                    
                    $positive = count(array_filter($ratings, function($r) { return $r >= 4; }));
                    $neutral = count(array_filter($ratings, function($r) { return $r == 3; }));
                    $negative = count(array_filter($ratings, function($r) { return $r <= 2; }));
                    ?>
                    
                    <div class="stat-item">
                        <div class="stat-value"><?= number_format($average_rating, 2) ?></div>
                        <div class="stat-label">Média Geral</div>
                    </div>
                    
                    <div class="stat-item positive">
                        <div class="stat-value"><?= $positive ?></div>
                        <div class="stat-label">Positivos</div>
                    </div>
                    
                    <div class="stat-item neutral">
                        <div class="stat-value"><?= $neutral ?></div>
                        <div class="stat-label">Neutros</div>
                    </div>
                    
                    <div class="stat-item negative">
                        <div class="stat-value"><?= $negative ?></div>
                        <div class="stat-label">Negativos</div>
                    </div>
                </div>
            </div>
            
        <?php else: ?>
            <div class="no-feedbacks">
                <div class="no-feedbacks-icon" aria-hidden="true">💭</div>
                <h3>Nenhum comentário encontrado</h3>
                <p>Esta notícia ainda não recebeu comentários dos usuários.</p>
                <p>Apenas avaliações com comentários são exibidas nesta página.</p>
                <div class="no-feedbacks-actions">
                    <a href="admin.php" class="btn-back-admin">
                        <span aria-hidden="true">⬅️</span>
                        Voltar ao Painel Admin
                    </a>
                </div>
            </div>
        <?php endif; ?>
    </section>
</div>

<!-- Estilos específicos para a página de feedbacks -->
<style>
.news-info-section {
    margin: 2rem 0;
}

.news-info-card {
    background: white;
    padding: 1.5rem;
    border-radius: 12px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    border-left: 4px solid #007bff;
}

.news-info-title {
    margin: 0 0 1rem 0;
    color: #333;
    font-size: 1.2rem;
    line-height: 1.4;
}

.news-info-actions {
    display: flex;
    gap: 1rem;
    flex-wrap: wrap;
}

.btn-view-news, .btn-back, .btn-back-admin {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.5rem 1rem;
    border-radius: 6px;
    text-decoration: none;
    font-size: 0.9rem;
    transition: background-color 0.2s ease;
}

.btn-view-news {
    background: #007bff;
    color: white;
}

.btn-view-news:hover {
    background: #0056b3;
}

.btn-back, .btn-back-admin {
    background: #6c757d;
    color: white;
}

.btn-back:hover, .btn-back-admin:hover {
    background: #545b62;
}

.feedbacks-title {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    margin-bottom: 1.5rem;
    color: #333;
}

.feedbacks-count {
    font-size: 0.9rem;
    color: #666;
    font-weight: normal;
}

.feedbacks-list {
    display: flex;
    flex-direction: column;
    gap: 1.5rem;
    margin-bottom: 2rem;
}

.feedback-item {
    background: white;
    padding: 1.5rem;
    border-radius: 12px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    transition: transform 0.2s ease, box-shadow 0.2s ease;
}

.feedback-item:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
}

.feedback-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 1rem;
    flex-wrap: wrap;
    gap: 1rem;
}

.feedback-rating {
    display: flex;
    align-items: center;
    gap: 0.3rem;
}

.star {
    font-size: 1.2rem;
}

.star.filled {
    color: #ffc107;
}

.star.empty {
    color: #e9ecef;
}

.rating-text {
    margin-left: 0.5rem;
    font-weight: bold;
    color: #333;
}

.feedback-meta {
    display: flex;
    align-items: center;
    gap: 1rem;
    color: #666;
    font-size: 0.9rem;
}

.feedback-content {
    margin: 1rem 0;
}

.feedback-comment {
    margin: 0;
    padding: 1rem;
    background: #f8f9fa;
    border-left: 4px solid #007bff;
    border-radius: 0 8px 8px 0;
    font-style: italic;
    line-height: 1.5;
    color: #333;
}

.feedback-footer {
    display: flex;
    justify-content: flex-end;
    align-items: center;
}

.sentiment {
    display: flex;
    align-items: center;
    gap: 0.3rem;
    padding: 0.3rem 0.8rem;
    border-radius: 20px;
    font-size: 0.8rem;
    font-weight: 500;
}

.sentiment.positive {
    background: #d4edda;
    color: #155724;
}

.sentiment.neutral {
    background: #fff3cd;
    color: #856404;
}

.sentiment.negative {
    background: #f8d7da;
    color: #721c24;
}

.feedback-stats {
    background: white;
    padding: 1.5rem;
    border-radius: 12px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
}

.stats-title {
    margin: 0 0 1rem 0;
    color: #333;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(120px, 1fr));
    gap: 1rem;
}

.stat-item {
    text-align: center;
    padding: 1rem;
    border-radius: 8px;
    background: #f8f9fa;
}

.stat-item.positive {
    background: #d4edda;
    color: #155724;
}

.stat-item.neutral {
    background: #fff3cd;
    color: #856404;
}

.stat-item.negative {
    background: #f8d7da;
    color: #721c24;
}

.stat-value {
    font-size: 1.5rem;
    font-weight: bold;
    margin-bottom: 0.3rem;
}

.stat-label {
    font-size: 0.9rem;
    opacity: 0.8;
}

.no-feedbacks {
    text-align: center;
    padding: 3rem 1rem;
    background: white;
    border-radius: 12px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
}

.no-feedbacks-icon {
    font-size: 3rem;
    margin-bottom: 1rem;
}

.no-feedbacks h3 {
    color: #333;
    margin-bottom: 1rem;
}

.no-feedbacks p {
    color: #666;
    margin-bottom: 0.5rem;
}

.no-feedbacks-actions {
    margin-top: 2rem;
}

@media (max-width: 768px) {
    .feedback-header {
        flex-direction: column;
        align-items: flex-start;
    }
    
    .news-info-actions {
        flex-direction: column;
    }
    
    .stats-grid {
        grid-template-columns: repeat(2, 1fr);
    }
}
</style>

<?php
require_once __DIR__ . '/includes/footer.php';
?>

