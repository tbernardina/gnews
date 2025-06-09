<?php
require_once __DIR__  . '\includes\functions.php';
require_once 'db_config.php';
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $url = $_POST['article_url'];
        $nota_feedback = $_POST['rating'];
        $comentario = $_POST['comment'];
    }

    $id = select_id_url($conn,$url);
    insert_feedback($conn, $id, $nota_feedback, $comentario);

