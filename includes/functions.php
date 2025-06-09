<?php
// ***** COLOQUE SUA CHAVE REAL E VÁLIDA DA GNEWS AQUI ABAIXO *****
define('GNEWS_API_KEY', '73f3f083fbbb3f59b35d65f37eb86444'); // <--- SUBSTITUA ISTO!
require_once 'untils.php';
/**
 * Busca notícias da API GNews.
 * @param string $query Termo de busca (opcional)
 * @param string $lang Idioma (padrão 'pt')
 * @param int $max Número máximo de artigos (padrão 10)
 * @return array|null Array de artigos ou null em caso de erro
 */
function fetchArticlesFromGNews($query = null, $lang = 'pt', $max = 10) {
    $token = GNEWS_API_KEY;

    if ($token === 'SUA_CHAVE_API_GNEWS_AQUI' || empty($token)) {
        error_log("ALERTA GNEWS: API Key não configurada. Verifique a constante GNEWS_API_KEY em functions.php.");
        return null;
    }

    $baseUrl = "https://gnews.io/api/v4/";
    $countryParam = ($lang === 'pt') ? "&country=br" : ""; // Foca no Brasil para idioma Português

    if ($query) {
        $endpoint = "search?q=" . urlencode($query) . "&lang={$lang}{$countryParam}&max={$max}&token={$token}";
    } else {
        $endpoint = "top-headlines?lang={$lang}{$countryParam}&max={$max}&token={$token}";
    }

    $url = $baseUrl . $endpoint;
    // Log para depuração da URL (verifique seus logs do PHP/Apache)
    error_log("DEBUG GNEWS: Chamando URL: " . $url);

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_TIMEOUT, 15); // Aumentado para 15 segundos
    curl_setopt($ch, CURLOPT_USERAGENT, 'PortalNoticiasApp/1.0 (PHP cURL)'); // User agent

    // Configurações SSL (podem ser necessárias em alguns ambientes XAMPP)
    // Se tiver problemas de SSL, pode tentar descomentar e ajustar o caminho do cacert.pem
    // curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
    // curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
    // $caCertPath = __DIR__ . '/cacert.pem'; // Exemplo: coloque cacert.pem na pasta 'includes'
    // if (file_exists($caCertPath)) {
    //     curl_setopt($ch, CURLOPT_CAINFO, $caCertPath);
    // } else {
    //     error_log("DEBUG GNEWS: cacert.pem não encontrado em " . $caCertPath . ". Se houver erros SSL, isso pode ser uma causa.");
    // }


    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $curlError = curl_error($ch);
    curl_close($ch);

    if ($curlError) {
        error_log("ERRO GNEWS cURL: " . $curlError . " (URL: " . $url . ")");
        return null;
    }

    if ($httpCode == 200) {
        $data = json_decode($response, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            error_log("ERRO GNEWS JSON Decode: " . json_last_error_msg() . ". Resposta recebida: " . $response);
            return null;
        }

        if (isset($data['errors'])) {
             error_log("ERRO GNEWS API: A API GNews retornou erros: " . print_r($data['errors'], true));
             return null;
        }
        
        // Verifica se 'articles' existe e é um array. Mesmo que seja vazio, retorna o array vazio.
        return isset($data['articles']) && is_array($data['articles']) ? $data['articles'] : null;

    } else {
        error_log("ERRO GNEWS HTTP: Código {$httpCode}. Resposta: {$response} (URL: " . $url . ")");
        return null;
    }
}

function select_id_url($conexao, $url){
    // 1) Prepara a chamada da procedure
    $sql = "CALL select_id(?)";
    if (! $stmt = $conexao->prepare($sql)) {
        // erro ao preparar
        return null;
    }

    // 2) Faz o bind do parâmetro e executa
    $stmt->bind_param('s', $url);
    if (! $stmt->execute()) {
        $stmt->close();
        return null;
    }

    // 3) Associa o resultado à variável $id
    $id = null;
    $stmt->bind_result($id);

    // 4) Busca o resultado
    if ($stmt->fetch()) {
        $stmt->close();

        // 5) Limpa quaisquer resultados adicionais gerados pela procedure
        while ($conexao->more_results() && $conexao->next_result()) {
            // apenas avançamos nos conjuntos de resultados
        }

        return $id;
    }

    // 6) Se não veio linha, fecha e retorna null
    $stmt->close();
    return null;
}

function insert_feedback($conexao, $id_noticia, $nota, $comment){
    header('Content-Type: application/json; charset=UTF-8');

    $noticias_id   = $id_noticia;
    $nota_feedback = $nota;
    $ip_user       = getClientIp();
    $comentario    = $comment;

    $check_ip = "SELECT IP_USER FROM feedbacks WHERE NOTICIAS_ID = ?";
    $stmt = $conexao->prepare($check_ip);
    $stmt->bind_param("i", $id_noticia);
    $stmt->execute();
    $stmt->store_result();
    if ($stmt->num_rows > 0) {  
        // já existe um registro com esse NOTICIAS_ID
        $stmt->close();
        $conexao->close();
        echo json_encode(['success' => true]);
    }
    else{
        $sql2 = "CALL inserir_feedback(?, ?, ?, ?)";
        $stmt2 = $conexao->prepare($sql2);

        if ( ! $stmt2 ) {
            die("Falha ao preparar inserir_feedback: ({$conexao->errno}) {$conexao->error}");
        }

        // Bind: 3 inteiros e 1 string (ip_user)
        $stmt2->bind_param(
            "iiss",
            $noticias_id,
            $nota_feedback,
            $ip_user,
            $comentario
        );

        if ( ! $stmt2->execute() ) {
            echo "Erro ao executar inserir_feedback: ({$stmt2->errno}) {$stmt2->error}";
        } else {
            $stmt2->close();
            $conexao->close();
            echo json_encode(['success' => true]);
        }
    }
}

/**
 * Busca notícias do banco de dados. (VERSÃO CORRIGIDA COM ...$params)
 * (Função como fornecida e corrigida anteriormente)
 */
function getNewsFromDB(mysqli $conn, int $limit = 10, int $offset = 0, string $searchTerm = null, string $lang = 'pt') {
    $noticias = [];
    $actual_params = [];
    $types_string = "";
    $sql = "SELECT id, title, description, url, image_url, published_at, source_name FROM noticias WHERE lang = ?";
    $types_string .= "s";
    $actual_params[] = $lang;
    if ($searchTerm !== null && trim($searchTerm) !== '') {
        $searchTermLike = "%" . trim($searchTerm) . "%";
        $sql .= " AND (title LIKE ? OR description LIKE ?)";
        $types_string .= "ss";
        $actual_params[] = $searchTermLike;
        $actual_params[] = $searchTermLike;
    }
    $sql .= " ORDER BY published_at DESC LIMIT ? OFFSET ?";
    $types_string .= "ii";
    $actual_params[] = $limit;
    $actual_params[] = $offset;
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        error_log("Erro ao preparar select de notícias: " . $conn->error);
        return $noticias;
    }
    if (!empty($types_string) && count($actual_params) > 0) {
        $stmt->bind_param($types_string, ...$actual_params);
    }
    if(!$stmt->execute()){
        error_log("Erro ao executar select de notícias: " . $stmt->error);
        $stmt->close();
        return $noticias;
    }
    $result = $stmt->get_result();
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $noticias[] = $row;
        }
    } else {
        error_log("Erro ao obter resultado do select de notícias: " . $stmt->error);
    }
    $stmt->close();
    return $noticias;
}

// Valores que você quer inserir/atualizar
function insert_noticias($conexao, $titulo, $descricao, $content_db, $url_db, $image_url_db, $published_at_db, $source_name_db, $source_url_db){
    $title        = $titulo;
    $description  = $descricao;
    $content      = $content_db;
    $url          = $url_db;
    $image_url    = $image_url_db;
    $published_at = $published_at_db;
    $source_name  = $source_name_db;
    $source_url   = $source_url_db;

    // Prepara a chamada à stored procedure (8 parâmetros IN)
    $sql = "CALL upsert_noticia(?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conexao->prepare($sql);

    if ( ! $stmt ) {
        die("Falha ao preparar a query: ({$conexao->errno}) {$conexao->error}");
    }

    // SETANDO TODOS OS PARAMETROS DA FUNÇÃO "s" = string, todas as variaveis são strings
    $stmt->bind_param(
        "ssssssss",
        $title,
        $description,
        $content,
        $url,
        $image_url,
        $published_at,
        $source_name,
        $source_url,
    );

    // Executa a procedure
    if ( ! $stmt->execute() ) {
        echo "Erro ao executar upsert_noticia: ({$stmt->errno}) {$stmt->error}";
    }

    // Fecha statement e libera resultados pendentes (caso a procedure retorne algo)
    $stmt->close();
}

/**
 * Formata a data para exibição.
 */
function formatDate(string $dateString): string {
    try {
        $date = new DateTime($dateString, new DateTimeZone('UTC'));
        $date->setTimezone(new DateTimeZone('America/Sao_Paulo'));
        return $date->format('d/m/Y H:i');
    } catch (Exception $e) {
        error_log("Erro ao formatar data: " . $e->getMessage() . " - Data original: " . $dateString);
        return $dateString;
    }
}

/**
 * Gera um texto alternativo simples para imagens.
 */
function generateAltText(string $title): string {
    return "Imagem relacionada à notícia: " . htmlspecialchars($title, ENT_QUOTES, 'UTF-8');
}

/**
 * Busca notícias ordenadas por média de avaliação
 * @param mysqli $conn Conexão com o banco de dados
 * @param int $limit Número máximo de notícias a retornar
 * @param int $min_feedbacks Número mínimo de avaliações que a notícia deve ter
 * @return array Array de notícias com suas estatísticas
 */
function getNewsRanking(mysqli $conn, int $limit = 25, int $min_feedbacks = 1) {
    $ranking_query = "
        SELECT 
            n.id,
            n.title,
            n.description,
            n.url,
            n.image_url,
            n.published_at,
            n.source_name,
            AVG(f.NOTA_FEEDBACK) as media_nota,
            COUNT(f.NOTA_FEEDBACK) as total_avaliacoes
        FROM noticias n
        INNER JOIN feedbacks f ON n.id = f.NOTICIAS_ID
        GROUP BY n.id, n.title, n.description, n.url, n.image_url, n.published_at, n.source_name
        HAVING COUNT(f.NOTA_FEEDBACK) >= ?
        ORDER BY media_nota DESC, total_avaliacoes DESC
        LIMIT ?
    ";
    
    $stmt = $conn->prepare($ranking_query);
    if (!$stmt) {
        error_log("Erro ao preparar consulta de ranking: " . $conn->error);
        return [];
    }
    
    $stmt->bind_param("ii", $min_feedbacks, $limit);
    
    if (!$stmt->execute()) {
        error_log("Erro ao executar consulta de ranking: " . $stmt->error);
        $stmt->close();
        return [];
    }
    
    $result = $stmt->get_result();
    $ranking = [];
    
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $ranking[] = $row;
        }
    } else {
        error_log("Erro ao obter resultado da consulta de ranking: " . $stmt->error);
    }
    
    $stmt->close();
    return $ranking;
}

/**
 * Busca feedbacks com comentários para uma notícia específica
 * @param mysqli $conn Conexão com o banco de dados
 * @param string $url URL da notícia
 * @return array Array de feedbacks com comentários
 */
function getFeedbacksWithComments(mysqli $conn, string $url) {
    // Primeiro, buscar o ID da notícia pela URL
    $id_noticia = select_id_url($conn, $url);
    
    if (!$id_noticia) {
        return [];
    }
    
    $feedback_query = "
        SELECT 
            f.NOTA_FEEDBACK,
            f.COMENTARIO,
            f.IP_USER,
            n.title,
            n.url,
            n.published_at
        FROM feedbacks f
        INNER JOIN noticias n ON f.NOTICIAS_ID = n.id
        WHERE f.NOTICIAS_ID = ? 
        AND f.COMENTARIO IS NOT NULL 
        AND TRIM(f.COMENTARIO) != ''
        ORDER BY f.NOTA_FEEDBACK DESC
    ";
    
    $stmt = $conn->prepare($feedback_query);
    if (!$stmt) {
        error_log("Erro ao preparar consulta de feedbacks: " . $conn->error);
        return [];
    }
    
    $stmt->bind_param("i", $id_noticia);
    
    if (!$stmt->execute()) {
        error_log("Erro ao executar consulta de feedbacks: " . $stmt->error);
        $stmt->close();
        return [];
    }
    
    $result = $stmt->get_result();
    $feedbacks = [];
    
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $feedbacks[] = $row;
        }
    } else {
        error_log("Erro ao obter resultado da consulta de feedbacks: " . $stmt->error);
    }
    
    $stmt->close();
    return $feedbacks;
}

