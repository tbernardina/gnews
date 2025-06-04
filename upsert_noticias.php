<?php
// PÁGINA DE FUNÇÃO PARA INSERT DAS NOTICIAS DO GNEWS AO BANCO DE DADOS LOCAL

// Valores que você quer inserir/atualizar
function insert_noticias($conexao, $titulo, $descricao, $content, $url_db, $image_url_db, $published_at_db, $source_name_db, $source_url_db){
    $title        = $titulo;
    $description  = $descricao;
    $content      = $content;
    $url          = $url_db;
    $image_url    = $image_url_db;
    $published_at = $published_at_db;
    $source_name  = $source_name_db;
    $source_url   = $source_url_db;

    // 4. Prepara a chamada à stored procedure (9 parâmetros IN)
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