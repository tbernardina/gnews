/**
 * Portal de Notícias - Script Principal
 * 
 * Este arquivo contém todas as funcionalidades JavaScript do portal de notícias,
 * incluindo acessibilidade, interações de usuário e funcionalidades dinâmicas.
 */

document.addEventListener('DOMContentLoaded', function() {
    // ===== CONTROLES DE ACESSIBILIDADE =====
    initAccessibilityControls();
    
    // ===== FUNCIONALIDADE DE LEITURA DE TEXTO =====
    initTextToSpeech();
    
    // ===== FUNCIONALIDADE DE COMPARTILHAMENTO =====
    initShareButtons();
    
    // ===== DETECÇÃO DE NAVEGAÇÃO POR TECLADO =====
    initKeyboardNavigation();
    
    // ===== INICIALIZAÇÃO DE TOOLTIPS =====
    initTooltips();
    
    // ===== INICIALIZAÇÃO DE FAVORITOS =====
    initFavorites();
    
    // ===== ANIMAÇÕES DE ENTRADA =====
    initEntryAnimations();
});

/**
 * Inicializa os controles de acessibilidade
 */
function initAccessibilityControls() {
    // Controles de tamanho de fonte
    const decreaseFont = document.getElementById('decrease-font');
    const resetFont = document.getElementById('reset-font');
    const increaseFont = document.getElementById('increase-font');
    const toggleContrast = document.getElementById('toggle-contrast');
    
    if (decreaseFont) {
        decreaseFont.addEventListener('click', function() {
            changeFontSize('decrease');
        });
    }
    
    if (resetFont) {
        resetFont.addEventListener('click', function() {
            changeFontSize('reset');
        });
    }
    
    if (increaseFont) {
        increaseFont.addEventListener('click', function() {
            changeFontSize('increase');
        });
    }
    
    // Controle de contraste
    if (toggleContrast) {
        toggleContrast.addEventListener('click', function() {
            toggleHighContrast();
        });
    }
    
    // Restaurar preferências salvas
    restoreUserPreferences();
}

/**
 * Altera o tamanho da fonte do site
 * @param {string} action - 'increase', 'decrease' ou 'reset'
 */
function changeFontSize(action) {
    const body = document.body;
    const currentClass = Array.from(body.classList).find(cls => cls.startsWith('font-size-'));
    
    // Remove a classe atual de tamanho
    if (currentClass) {
        body.classList.remove(currentClass);
    }
    
    // Aplica a nova classe de tamanho
    switch (action) {
        case 'increase':
            if (!currentClass || currentClass === 'font-size-normal') {
                body.classList.add('font-size-medium');
                saveUserPreference('fontSize', 'medium');
            } else if (currentClass === 'font-size-medium') {
                body.classList.add('font-size-large');
                saveUserPreference('fontSize', 'large');
            } else if (currentClass === 'font-size-large') {
                body.classList.add('font-size-xlarge');
                saveUserPreference('fontSize', 'xlarge');
            } else {
                body.classList.add('font-size-xlarge');
                saveUserPreference('fontSize', 'xlarge');
            }
            break;
            
        case 'decrease':
            if (!currentClass || currentClass === 'font-size-normal') {
                body.classList.add('font-size-normal');
                saveUserPreference('fontSize', 'normal');
            } else if (currentClass === 'font-size-medium') {
                body.classList.add('font-size-normal');
                saveUserPreference('fontSize', 'normal');
            } else if (currentClass === 'font-size-large') {
                body.classList.add('font-size-medium');
                saveUserPreference('fontSize', 'medium');
            } else if (currentClass === 'font-size-xlarge') {
                body.classList.add('font-size-large');
                saveUserPreference('fontSize', 'large');
            }
            break;
            
        case 'reset':
        default:
            body.classList.add('font-size-normal');
            saveUserPreference('fontSize', 'normal');
            break;
    }
    
    // Anúncio para leitores de tela
    announceToScreenReader(`Tamanho de fonte alterado para ${action === 'increase' ? 'maior' : action === 'decrease' ? 'menor' : 'padrão'}`);
}

/**
 * Alterna o modo de alto contraste
 */
function toggleHighContrast() {
    const body = document.body;
    const toggleButton = document.getElementById('toggle-contrast');
    
    if (body.classList.contains('high-contrast-theme')) {
        body.classList.remove('high-contrast-theme');
        if (toggleButton) {
            toggleButton.setAttribute('aria-pressed', 'false');
        }
        saveUserPreference('highContrast', false);
        announceToScreenReader('Modo de alto contraste desativado');
    } else {
        body.classList.add('high-contrast-theme');
        if (toggleButton) {
            toggleButton.setAttribute('aria-pressed', 'true');
        }
        saveUserPreference('highContrast', true);
        announceToScreenReader('Modo de alto contraste ativado');
    }
}

/**
 * Salva preferências do usuário no localStorage
 * @param {string} key - Chave da preferência
 * @param {any} value - Valor da preferência
 */
function saveUserPreference(key, value) {
    try {
        localStorage.setItem(`portal_news_${key}`, JSON.stringify(value));
    } catch (e) {
        console.error('Erro ao salvar preferência:', e);
    }
}

/**
 * Restaura preferências do usuário do localStorage
 */
function restoreUserPreferences() {
    try {
        // Restaurar tamanho da fonte
        const fontSize = JSON.parse(localStorage.getItem('portal_news_fontSize'));
        if (fontSize) {
            document.body.classList.add(`font-size-${fontSize}`);
        }
        
        // Restaurar modo de alto contraste
        const highContrast = JSON.parse(localStorage.getItem('portal_news_highContrast'));
        if (highContrast) {
            document.body.classList.add('high-contrast-theme');
            const toggleButton = document.getElementById('toggle-contrast');
            if (toggleButton) {
                toggleButton.setAttribute('aria-pressed', 'true');
            }
        }
    } catch (e) {
        console.error('Erro ao restaurar preferências:', e);
    }
}

/**
 * Inicializa a funcionalidade de leitura de texto
 */
function initTextToSpeech() {
    const readButtons = document.querySelectorAll('.btn-read');
    
    readButtons.forEach(button => {
        button.addEventListener('click', function() {
            const text = this.getAttribute('data-text');
            if (!text) return;
            
            // Verifica se a API de síntese de voz está disponível
            if ('speechSynthesis' in window) {
                // Interrompe qualquer leitura em andamento
                window.speechSynthesis.cancel();
                
                // Cria uma nova instância de fala
                const speech = new SpeechSynthesisUtterance();
                speech.text = text;
                speech.lang = 'pt-BR';
                
                // Feedback visual
                const originalInnerHTML = this.innerHTML;
                this.innerHTML = '<span aria-hidden="true">⏸️</span>';
                this.classList.add('reading');
                
                // Evento de fim da leitura
                speech.onend = () => {
                    this.innerHTML = originalInnerHTML;
                    this.classList.remove('reading');
                };
                
                // Evento de erro
                speech.onerror = () => {
                    this.innerHTML = originalInnerHTML;
                    this.classList.remove('reading');
                    announceToScreenReader('Erro ao iniciar a leitura');
                };
                
                // Inicia a leitura
                window.speechSynthesis.speak(speech);
                
                // Anúncio para leitores de tela
                announceToScreenReader('Iniciando leitura da notícia');
            } else {
                alert('Seu navegador não suporta a funcionalidade de leitura de texto.');
            }
        });
    });
}

/**
 * Inicializa os botões de compartilhamento
 */
function initShareButtons() {
    const shareButtons = document.querySelectorAll('.btn-share');
    
    shareButtons.forEach(button => {
        button.addEventListener('click', function() {
            const url = this.getAttribute('data-url');
            const title = this.getAttribute('data-title');
            
            if (!url) return;
            
            // Verifica se a API Web Share está disponível
            if (navigator.share) {
                navigator.share({
                    title: title || 'Notícia do Portal',
                    url: url
                }).catch(error => {
                    console.error('Erro ao compartilhar:', error);
                });
            } else {
                // Fallback: copia o link para a área de transferência
                navigator.clipboard.writeText(url).then(() => {
                    // Feedback visual
                    const originalText = this.innerHTML;
                    this.innerHTML = '<span aria-hidden="true">✅</span> Copiado!';
                    
                    setTimeout(() => {
                        this.innerHTML = originalText;
                    }, 2000);
                    
                    // Anúncio para leitores de tela
                    announceToScreenReader('Link copiado para a área de transferência');
                }).catch(error => {
                    console.error('Erro ao copiar link:', error);
                    alert('Não foi possível copiar o link. Por favor, tente novamente.');
                });
            }
        });
    });
}

/**
 * Detecta navegação por teclado para aplicar estilos apropriados
 */
function initKeyboardNavigation() {
    // Adiciona classe quando o usuário navega por teclado
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Tab') {
            document.body.classList.add('keyboard-navigation');
        }
    });
    
    // Remove classe quando o usuário usa o mouse
    document.addEventListener('mousedown', function() {
        document.body.classList.remove('keyboard-navigation');
    });
}

/**
 * Inicializa tooltips personalizados
 */
function initTooltips() {
    const elementsWithTooltips = document.querySelectorAll('[title]');
    
    elementsWithTooltips.forEach(element => {
        const tooltipText = element.getAttribute('title');
        element.removeAttribute('title'); // Remove o atributo title para evitar tooltip nativo
        
        // Cria o tooltip personalizado
        element.addEventListener('mouseenter', function(e) {
            const tooltip = document.createElement('div');
            tooltip.className = 'custom-tooltip';
            tooltip.textContent = tooltipText;
            document.body.appendChild(tooltip);
            
            // Posiciona o tooltip
            const rect = element.getBoundingClientRect();
            tooltip.style.top = `${rect.bottom + window.scrollY + 5}px`;
            tooltip.style.left = `${rect.left + window.scrollX + (rect.width / 2) - (tooltip.offsetWidth / 2)}px`;
            
            // Armazena referência ao tooltip
            element.tooltip = tooltip;
        });
        
        // Remove o tooltip
        element.addEventListener('mouseleave', function() {
            if (element.tooltip) {
                document.body.removeChild(element.tooltip);
                element.tooltip = null;
            }
        });
    });
}

/**
 * Inicializa sistema de favoritos
 */
function initFavorites() {
    const favoriteButtons = document.querySelectorAll('.btn-favorite');
    
    favoriteButtons.forEach(button => {
        const newsId = button.getAttribute('data-id');
        
        // Verifica se a notícia já está nos favoritos
        if (isNewsFavorited(newsId)) {
            button.classList.add('favorited');
            button.setAttribute('aria-pressed', 'true');
        }
        
        button.addEventListener('click', function() {
            toggleFavorite(this, newsId);
        });
    });
}

/**
 * Verifica se uma notícia está nos favoritos
 * @param {string} newsId - ID da notícia
 * @returns {boolean} - true se estiver nos favoritos, false caso contrário
 */
function isNewsFavorited(newsId) {
    try {
        const favorites = JSON.parse(localStorage.getItem('portal_news_favorites')) || [];
        return favorites.includes(newsId);
    } catch (e) {
        console.error('Erro ao verificar favoritos:', e);
        return false;
    }
}

/**
 * Alterna o estado de favorito de uma notícia
 * @param {HTMLElement} button - Botão de favorito
 * @param {string} newsId - ID da notícia
 */
function toggleFavorite(button, newsId) {
    try {
        let favorites = JSON.parse(localStorage.getItem('portal_news_favorites')) || [];
        
        if (favorites.includes(newsId)) {
            // Remove dos favoritos
            favorites = favorites.filter(id => id !== newsId);
            button.classList.remove('favorited');
            button.setAttribute('aria-pressed', 'false');
            announceToScreenReader('Notícia removida dos favoritos');
        } else {
            // Adiciona aos favoritos
            favorites.push(newsId);
            button.classList.add('favorited');
            button.setAttribute('aria-pressed', 'true');
            announceToScreenReader('Notícia adicionada aos favoritos');
        }
        
        localStorage.setItem('portal_news_favorites', JSON.stringify(favorites));
    } catch (e) {
        console.error('Erro ao alternar favorito:', e);
    }
}

/**
 * Inicializa animações de entrada para elementos
 */
function initEntryAnimations() {
    const animatedElements = document.querySelectorAll('.news-item, .category-card, .popular-card');
    
    // Verifica se o usuário prefere animações reduzidas
    const prefersReducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
    
    if (!prefersReducedMotion) {
        animatedElements.forEach((element, index) => {
            element.style.opacity = '0';
            element.style.transform = 'translateY(20px)';
            element.style.transition = `opacity 0.5s ease, transform 0.5s ease`;
            element.style.transitionDelay = `${index * 0.1}s`;
            
            setTimeout(() => {
                element.style.opacity = '1';
                element.style.transform = 'translateY(0)';
            }, 100);
        });
    }
}

/**
 * Anuncia uma mensagem para leitores de tela
 * @param {string} message - Mensagem a ser anunciada
 */
function announceToScreenReader(message) {
    const announcement = document.createElement('div');
    announcement.setAttribute('aria-live', 'polite');
    announcement.className = 'visually-hidden';
    announcement.textContent = message;
    
    document.body.appendChild(announcement);
    
    // Remove o elemento após alguns segundos
    setTimeout(() => {
        document.body.removeChild(announcement);
    }, 3000);
}

