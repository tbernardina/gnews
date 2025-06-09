<?php 
require_once __DIR__ . '/includes/header.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $url = $_POST['url'];
    $title = $_POST['title'];
}
?>
<link rel="stylesheet" href="feedback.css">
<div class="container">
    <!-- Cabeçalho da página -->
    <section class="page-header" aria-labelledby="page-heading">
        <h1 id="page-heading" class="page-title">
            <span class="page-icon" aria-hidden="true">⭐</span>
            Feedback
        </h1>
        <p style="text-align: center; margin: 5px;">
            Sua opinião é muito importante para nós. Avalie nosso conteúdo e ajude-nos a melhorar.
        </p>
    </section>

    <!-- Formulário de Feedback -->
    <section class="feedback-container">
        <div class="feedback-header">
            <h2 class="feedback-title">Avalie nossa notícia</h2>
            <?php if (!empty($title)): ?>
                <p class="feedback-subtitle">"<?php echo $title; ?>"</p>
            <?php else: ?>
                <p class="feedback-subtitle">Compartilhe sua experiência com nosso portal</p>
            <?php endif; ?>
        </div>

        <form action="insert_feedback.php" method="post" class="feedback-form" id="feedback-form">
            <?php if (!empty($url)): ?>
                <input type="hidden" name="article_url" value="<?php echo htmlspecialchars($url, ENT_QUOTES); ?>">
            <?php endif; ?>
            
            <!-- Sistema de avaliação por estrelas -->
            <div class="star-rating-container">
                <h3>Como você avalia esta notícia?</h3>
                <div class="star-rating">
                    <input type="radio" id="star1" name="rating" value="5" />
                    <label for="star1" title="1 estrela - Ruim">★</label>
                    
                    <input type="radio" id="star2" name="rating" value="4" />
                    <label for="star2" title="2 estrelas - Regular">★</label>
                    
                    <input type="radio" id="star3" name="rating" value="3" />
                    <label for="star3" title="3 estrelas - Bom">★</label>
                    
                    <input type="radio" id="star4" name="rating" value="2" />
                    <label for="star4" title="4 estrelas - Muito bom">★</label>
                    
                    <input type="radio" id="star5" name="rating" value="1" />
                    <label for="star5" title="5 estrelas - Excelente">★</label>
                </div>
            </div>
            
            <!-- Campo de comentário -->
            <div class="comment-container">
                <h3>Deixe seu comentário (opcional)</h3>
                <textarea 
                    name="comment" 
                    id="feedback-comment" 
                    placeholder="Compartilhe sua opinião sobre a notícia ou sugestões para melhorarmos nosso conteúdo..."
                    aria-label="Seu comentário ou sugestão"
                ></textarea>
            </div>
            
            <!-- Botão de envio -->
            <button type="submit" class="submit-feedback">
                <span aria-hidden="true">📤</span>
                Enviar Feedback
            </button>
        </form>

        <!-- Mensagem de sucesso (inicialmente oculta) -->
        <div class="feedback-success" style="display: none;" aria-hidden="true">
            <div class="success-icon">✅</div>
            <h3>Feedback enviado com sucesso!</h3>
            <p>Agradecemos por compartilhar sua opinião. Sua avaliação é muito importante para continuarmos melhorando.</p>
            <a href="index.php" class="btn-back-home">Voltar para a página inicial</a>
        </div>
    </section>
</div>
<script src="assets/js/feedback.js"></script>
<?php require_once __DIR__ . '/includes/footer.php'; ?>

