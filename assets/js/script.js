document.addEventListener('DOMContentLoaded', function() {
    console.log('DOM carregado. Iniciando script de acessibilidade.');

    // --- CONTROLES DE TAMANHO DE FONTE ---
    const btnIncreaseFont = document.getElementById('increase-font');
    const btnDecreaseFont = document.getElementById('decrease-font');
    const btnResetFont = document.getElementById('reset-font');
    const body = document.body;

    const fontLevels = ['font-size-normal', 'font-size-medium', 'font-size-large', 'font-size-xlarge'];
    let currentLevelIndex = 0; // Default para 'font-size-normal'

    if (!body) {
        console.error('Elemento <body> não encontrado.');
        return; // Interrompe se o body não existir
    }
    if (!btnIncreaseFont || !btnDecreaseFont || !btnResetFont) {
        console.warn('Um ou mais botões de controle de fonte não foram encontrados. Verifique os IDs no HTML: increase-font, decrease-font, reset-font.');
    }

    function applyFontSize(index) {
        if (index < 0 || index >= fontLevels.length) {
            console.error('Índice de nível de fonte inválido:', index);
            return;
        }
        currentLevelIndex = index; // Garante que currentLevelIndex está sempre atualizado
        console.log('Aplicando nível de fonte:', fontLevels[currentLevelIndex], '(índice:', currentLevelIndex, ')');

        fontLevels.forEach(level => body.classList.remove(level));
        body.classList.add(fontLevels[currentLevelIndex]);

        try {
            localStorage.setItem('fontLevelIndex', currentLevelIndex.toString());
            console.log('Preferência de fonte salva no localStorage:', currentLevelIndex);
        } catch (e) {
            console.warn('Não foi possível salvar preferência de fonte no localStorage:', e);
        }
        updateFontButtonStates();
    }

    function updateFontButtonStates() {
        if (btnDecreaseFont) {
            btnDecreaseFont.disabled = (currentLevelIndex === 0);
            // console.log('Botão Diminuir Fonte desabilitado:', btnDecreaseFont.disabled);
        }
        if (btnIncreaseFont) {
            btnIncreaseFont.disabled = (currentLevelIndex === fontLevels.length - 1);
            // console.log('Botão Aumentar Fonte desabilitado:', btnIncreaseFont.disabled);
        }
        if (btnResetFont) {
            btnResetFont.disabled = (currentLevelIndex === 0);
            // console.log('Botão Resetar Fonte desabilitado:', btnResetFont.disabled);
        }
    }

    function loadFontPreference() {
        let initialLevelIndex = 0;
        try {
            const savedLevelIndex = localStorage.getItem('fontLevelIndex');
            if (savedLevelIndex !== null) {
                const parsedIndex = parseInt(savedLevelIndex, 10);
                if (!isNaN(parsedIndex) && parsedIndex >= 0 && parsedIndex < fontLevels.length) {
                    initialLevelIndex = parsedIndex;
                    console.log('Preferência de fonte carregada do localStorage:', initialLevelIndex);
                } else {
                    console.log('Valor salvo de preferência de fonte inválido:', savedLevelIndex);
                    localStorage.removeItem('fontLevelIndex'); // Limpa valor inválido
                }
            } else {
                console.log('Nenhuma preferência de fonte encontrada no localStorage. Usando padrão.');
            }
        } catch (e) {
            console.warn('Erro ao carregar preferência de fonte do localStorage:', e);
        }
        // currentLevelIndex é atualizado dentro de applyFontSize
        applyFontSize(initialLevelIndex);
    }

    if (btnIncreaseFont) {
        btnIncreaseFont.addEventListener('click', () => {
            console.log('Botão Aumentar Fonte clicado. Nível atual:', currentLevelIndex);
            if (currentLevelIndex < fontLevels.length - 1) {
                applyFontSize(currentLevelIndex + 1);
            }
        });
    }

    if (btnDecreaseFont) {
        btnDecreaseFont.addEventListener('click', () => {
            console.log('Botão Diminuir Fonte clicado. Nível atual:', currentLevelIndex);
            if (currentLevelIndex > 0) {
                applyFontSize(currentLevelIndex - 1);
            }
        });
    }

    if (btnResetFont) {
        btnResetFont.addEventListener('click', () => {
            console.log('Botão Resetar Fonte clicado.');
            applyFontSize(0); // Reseta para font-size-normal
        });
    }

    loadFontPreference(); // Carrega e aplica a preferência de fonte ao iniciar

    // --- CONTROLE DE CONTRASTE ---
    const btnToggleContrast = document.getElementById('toggle-contrast');
    const contrastThemeClass = 'high-contrast-theme';

    if (!btnToggleContrast) {
        console.warn('Botão de alternar contraste não encontrado. Verifique o ID no HTML: toggle-contrast.');
    }

    function applyContrastPreference(isHighContrast) {
        if (isHighContrast) {
            body.classList.add(contrastThemeClass);
            console.log('Tema de alto contraste ATIVADO.');
        } else {
            body.classList.remove(contrastThemeClass);
            console.log('Tema de alto contraste DESATIVADO.');
        }
        try {
            localStorage.setItem('highContrastPref', isHighContrast ? 'true' : 'false');
            console.log('Preferência de contraste salva no localStorage:', isHighContrast);
        } catch (e) {
            console.warn('Não foi possível salvar preferência de contraste no localStorage:', e);
        }
    }

    function loadContrastPreference() {
        let prefersHighContrast = false;
        try {
            const savedContrastPref = localStorage.getItem('highContrastPref');
            if (savedContrastPref !== null) {
                prefersHighContrast = (savedContrastPref === 'true');
                console.log('Preferência de contraste carregada do localStorage:', prefersHighContrast);
            } else {
                console.log('Nenhuma preferência de contraste encontrada no localStorage.');
                // Opcional: verificar preferência do sistema operacional
                // if (window.matchMedia && window.matchMedia('(prefers-contrast: more)').matches) {
                //     prefersHighContrast = true;
                //     console.log('Detectada preferência do sistema por mais contraste.');
                // }
            }
        } catch (e) {
            console.warn('Erro ao carregar preferência de contraste do localStorage:', e);
        }
        applyContrastPreference(prefersHighContrast);
    }

    if (btnToggleContrast) {
        btnToggleContrast.addEventListener('click', () => {
            const isCurrentlyHighContrast = body.classList.contains(contrastThemeClass);
            console.log('Botão Alternar Contraste clicado. Estava em alto contraste?', isCurrentlyHighContrast);
            applyContrastPreference(!isCurrentlyHighContrast);
        });
    }

    loadContrastPreference(); // Carrega e aplica a preferência de contraste ao iniciar

    // SEU CÓDIGO EXISTENTE (se houver mais)
    const backToTopButton = document.querySelector('.back-to-top');
    if (backToTopButton) {
        backToTopButton.addEventListener('click', function(e) {
            e.preventDefault();
            window.scrollTo({ top: 0, behavior: 'smooth' });
        });
    }

    const carouselItems = document.querySelectorAll('.carousel-section .news-item');
    if (carouselItems.length > 0) {
        console.log(`Encontrados ${carouselItems.length} itens para o carrossel.`);
    }
});