<?php
require_once 'untils.php';

function insert_feedback($conexao, $id_noticia, $nota){
    $noticias_id   = $id_noticia;
    $nota_feedback = $nota;
    $ip_user       = getClientIp();

    $sql2 = "CALL inserir_feedback(?, ?, ?)";
    $stmt2 = $conexao->prepare($sql2);

    if ( ! $stmt2 ) {
        die("Falha ao preparar inserir_feedback: ({$conexao->errno}) {$conexao->error}");
    }

    // Bind: 3 inteiros e 1 string (ip_user)
    $stmt2->bind_param(
        "iis",
        $noticias_id,
        $nota_feedback,
        $ip_user
    );

    if ( ! $stmt2->execute() ) {
        echo "Erro ao executar inserir_feedback: ({$stmt2->errno}) {$stmt2->error}";
    } else {
        echo "Feedback inserido com sucesso!";
    }

    $stmt2->close();

    // 8. Fecha a conexão
    $conexao->close();
}
?>
?>