<?php

$servidor = "mysql";
$usuario = "financeiro";
$senha = "financeiro";
$banco = "meu_controle_financeiro";

$conexao = mysqli_connect(
    $servidor,
    $usuario,
    $senha,
    $banco
);

if (!$conexao) {
    die("Erro ao conectar com o banco de dados: " . mysqli_connect_error());
}

mysqli_set_charset($conexao, "utf8mb4");

?>