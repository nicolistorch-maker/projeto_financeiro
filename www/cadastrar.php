<?php

include("conexao.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $nome = $_POST["nome"];
    $email = $_POST["email"];
    $senha = $_POST["senha"];

    if (empty($nome) || empty($email) || empty($senha)) {

        echo "Preencha todos os campos.";

    } else {

        $senha_criptografada = password_hash($senha, PASSWORD_DEFAULT);

        $sql = "INSERT INTO usuarios (nome, email, senha)
                VALUES ('$nome', '$email', '$senha_criptografada')";

        $resultado = mysqli_query($conexao, $sql);

        if ($resultado) {

            echo "Cadastro realizado com sucesso!";
            echo "<br><a href='index.php?pagina=login'>Ir para o login</a>";

        } else {

            echo "Erro ao realizar cadastro: " . mysqli_error($conexao);

        }
    }
}

?>