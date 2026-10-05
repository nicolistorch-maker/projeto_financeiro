<?php

session_start();

include("conexao.php");

if (!isset($_SESSION["id_usuario"])) {

    header("Location: index.php?pagina=login");
    exit;

}

$id_usuario = $_SESSION["id_usuario"];


/* Primeiro exclui as transações do usuário */

$sql = "DELETE FROM transacoes WHERE usuario_id = ?";

$stmt = mysqli_prepare($conexao, $sql);

mysqli_stmt_bind_param(
    $stmt,
    "i",
    $id_usuario
);

mysqli_stmt_execute($stmt);


/* Depois exclui o usuário */

$sql = "DELETE FROM usuarios WHERE id = ?";

$stmt = mysqli_prepare($conexao, $sql);

mysqli_stmt_bind_param(
    $stmt,
    "i",
    $id_usuario
);

if (mysqli_stmt_execute($stmt)) {

    session_destroy();

    header("Location: index.php");
    exit;

} else {

    echo "Erro ao excluir a conta: " . mysqli_error($conexao);

}

?>