<?php

session_start();

include("conexao.php");


if (!isset($_SESSION["id_usuario"])) {

    header("Location: index.php?pagina=login");
    exit;

}

if ($_SERVER["REQUEST_METHOD"] != "POST") {

    header("Location: perfil.php");
    exit;

}


$id_usuario = $_SESSION["id_usuario"];

$nome = trim($_POST["nome"] ?? "");
$senha = $_POST["senha"] ?? "";
if (empty($nome)) {

    die("O nome não pode ficar vazio.");

}


if (!empty($senha)) {

    if (strlen($senha) < 6) {

        die("A senha deve ter pelo menos 6 caracteres.");

    }

    $senha_criptografada = password_hash($senha, PASSWORD_DEFAULT);

    $sql = "UPDATE usuarios
            SET nome = ?, senha = ?
            WHERE id = ?";

    $stmt = mysqli_prepare($conexao, $sql);

    mysqli_stmt_bind_param(
        $stmt,
        "ssi",
        $nome,
        $senha_criptografada,
        $id_usuario
    );

} else {

    $sql = "UPDATE usuarios
            SET nome = ?
            WHERE id = ?";

    $stmt = mysqli_prepare($conexao, $sql);

    mysqli_stmt_bind_param(
        $stmt,
        "si",
        $nome,
        $id_usuario
    );
}


if (mysqli_stmt_execute($stmt)) {

    $_SESSION["nome_usuario"] = $nome;

    header("Location: perfil.php");
    exit;

} else {

    echo "Erro ao atualizar os dados: " . mysqli_error($conexao);

}

?>
