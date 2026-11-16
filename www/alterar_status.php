<?php

session_start();

include("conexao.php");

// VERIFICAR SE O USUARIO ESTA LOGADO

if (!isset($_SESSION["id_usuario"])) {
    header("Location: index.php?pagina=login");
    exit;
}

// VERIFICAR SE E ADMINISTRADOR

if ($_SESSION["perfil_usuario"] != "admin") {
    echo "Acesso negado.";
    exit;
}

// VERIFICAR SE O ACESSO FOI FEITO PELO FORMULARIO

if ($_SERVER["REQUEST_METHOD"] != "POST") {
    header("Location: admin.php");
    exit;
}

// RECEBER OS DADOS

$id_usuario = intval($_POST["id_usuario"] ?? 0);
$novo_status = $_POST["status"] ?? "";

// VALIDAR OS DADOS

if ($id_usuario <= 0 || !in_array($novo_status, ["ativo", "bloqueado"])) {
    header("Location: admin.php");
    exit;
}

// IMPEDIR QUE O ADMIN BLOQUEIE A PROPRIA CONTA

if ($id_usuario == $_SESSION["id_usuario"]) {
    echo "Voce nao pode alterar o status da sua propria conta.";
    exit;
}

// BUSCAR O USUARIO

$sql = "SELECT perfil FROM usuarios WHERE id = ?";

$stmt = mysqli_prepare($conexao, $sql);

mysqli_stmt_bind_param($stmt, "i", $id_usuario);

mysqli_stmt_execute($stmt);

$resultado = mysqli_stmt_get_result($stmt);

if (mysqli_num_rows($resultado) == 0) {
    mysqli_stmt_close($stmt);
    echo "Usuario nao encontrado.";
    exit;
}

$usuario = mysqli_fetch_assoc($resultado);

mysqli_stmt_close($stmt);

// IMPEDIR ALTERACAO DE OUTRA CONTA ADMINISTRADORA

if ($usuario["perfil"] == "admin") {
    echo "Nao e permitido alterar o status de outro administrador.";
    exit;
}

// ATUALIZAR O STATUS

$sql = "UPDATE usuarios SET status = ? WHERE id = ?";

$stmt = mysqli_prepare($conexao, $sql);

mysqli_stmt_bind_param($stmt, "si", $novo_status, $id_usuario);

if (mysqli_stmt_execute($stmt)) {

    mysqli_stmt_close($stmt);

    header("Location: admin.php");
    exit;

} else {

    mysqli_stmt_close($stmt);
    echo "Erro ao atualizar o status do usuario.";

}

?>
