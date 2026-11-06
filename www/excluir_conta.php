<?php

session_start();

include("conexao.php");

/* VERIFICAR SE O USUÁRIO ESTÁ LOGADO */

if (!isset($_SESSION["id_usuario"])) {

    header("Location: index.php?pagina=login");
    exit;

}

/* VERIFICAR SE A REQUISIÇÃO É POST */

if ($_SERVER["REQUEST_METHOD"] != "POST") {

    header("Location: perfil.php");
    exit;

}

$id_usuario = (int) $_SESSION["id_usuario"];

/* INICIAR TRANSAÇÃO NO BANCO DE DADOS */

mysqli_begin_transaction($conexao);

try {

    /* PRIMEIRO EXCLUI AS TRANSAÇÕES DO USUÁRIO */

    $sql = "DELETE FROM transacoes WHERE usuario_id = ?";

    $stmt = mysqli_prepare($conexao, $sql);

    mysqli_stmt_bind_param(
        $stmt,
        "i",
        $id_usuario
    );

    mysqli_stmt_execute($stmt);

    mysqli_stmt_close($stmt);


    /* DEPOIS EXCLUI O USUÁRIO */

    $sql = "DELETE FROM usuarios WHERE id = ?";

    $stmt = mysqli_prepare($conexao, $sql);

    mysqli_stmt_bind_param(
        $stmt,
        "i",
        $id_usuario
    );

    mysqli_stmt_execute($stmt);

    mysqli_stmt_close($stmt);


    /* CONFIRMAR AS EXCLUSÕES */

    mysqli_commit($conexao);

    /* ENCERRAR A SESSÃO */

    $_SESSION = [];

    session_destroy();

    header("Location: index.php");
    exit;

} catch (Throwable $erro) {

    /* DESFAZER AS ALTERAÇÕES EM CASO DE ERRO */

    mysqli_rollback($conexao);

    echo "Erro ao excluir a conta. Nenhum dado foi removido. Tente novamente.";

}

?>