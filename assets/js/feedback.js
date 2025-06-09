document.addEventListener('DOMContentLoaded', function() {
    console.log('feedback.js carregado e DOM pronto');

    const form = document.getElementById('feedback-form');
    const successMessage = document.querySelector('.feedback-success');

    form.addEventListener('submit', async function(e) {
        console.log('submit interceptado!');
        e.preventDefault();

        // 1) Verificar se uma avaliação foi selecionada
        const ratingInput = document.querySelector('input[name="rating"]:checked');
        if (!ratingInput) {
            alert('Por favor, selecione uma avaliação de 1 a 5 estrelas.');
            return;
        }

        // 2) Preparar dados para envio
        const articleUrl = form.querySelector('input[name="article_url"]')?.value || '';
        const rating    = ratingInput.value;
        const comment   = form.querySelector('textarea[name="comment"]').value;

        // 3) Feedback visual de "enviando"
        const submitButton     = form.querySelector('button[type="submit"]');
        const originalButtonHTML = submitButton.innerHTML;
        submitButton.innerHTML  = '<span aria-hidden="true">⏳</span> Enviando...';
        submitButton.disabled   = true;

        try {
            // 4) Envio via fetch
            const body = new URLSearchParams({ article_url: articleUrl, rating, comment });
            const response = await fetch(form.action, {
                method:  form.method.toUpperCase(),
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body:    body.toString()
            });

            if (!response.ok) {
                throw new Error(`Status ${response.status}`);
            }

            const json = await response.json();
            if (json.success) {
                // 5) Exibir sucesso
                form.style.display = 'none';
                successMessage.style.display = 'block';
                successMessage.setAttribute('aria-hidden', 'false');

                // Anúncio para leitores de tela
                const announcement = document.createElement('div');
                announcement.setAttribute('aria-live', 'assertive');
                announcement.className = 'visually-hidden';
                announcement.textContent = 'Feedback enviado com sucesso! Agradecemos por compartilhar sua opinião.';
                document.body.appendChild(announcement);
                setTimeout(() => document.body.removeChild(announcement), 3000);

            } else {
                throw new Error(json.error || 'Erro desconhecido');
            }
        } catch (err) {
            console.error(err);
            alert('Ocorreu um erro ao enviar o feedback. Tente novamente mais tarde.');
        } finally {
            // 6) Restaurar botão
            submitButton.innerHTML = originalButtonHTML;
            submitButton.disabled  = false;
        }
    });

    // Efeito visual ao selecionar estrelas
    const stars = document.querySelectorAll('.star-rating label');
    stars.forEach(star => {
        star.addEventListener('mouseover', () => star.classList.add('star-hover'));
        star.addEventListener('mouseout',  () => star.classList.remove('star-hover'));
        star.addEventListener('click',    () => {
            stars.forEach(s => s.classList.remove('star-selected'));
            star.classList.add('star-selected');
        });
    });
});
