<?php

include("conexao.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $nome = trim($_POST["nome"] ?? "");
    $email = trim($_POST["email"] ?? "");
    $senha = $_POST["senha"] ?? "";

    /* VALIDAR OS CAMPOS */

    if (empty($nome) || empty($email) || empty($senha)) {

        echo "Preencha todos os campos.";

    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {

        echo "Informe um e-mail válido.";

    } elseif (strlen($senha) < 6) {

        echo "A senha deve ter pelo menos 6 caracteres.";

    } else {

        /* VERIFICAR SE O E-MAIL JÁ ESTÁ CADASTRADO */

        $sql = "SELECT id FROM usuarios WHERE email = ?";

        $stmt = mysqli_prepare($conexao, $sql);

        mysqli_stmt_bind_param(
            $stmt,
            "s",
            $email
        );

        mysqli_stmt_execute($stmt);

        $resultado = mysqli_stmt_get_result($stmt);

        if (mysqli_num_rows($resultado) > 0) {

            echo "Este e-mail já está cadastrado.";

        } else {

            /* CRIPTOGRAFAR A SENHA */

            $senha_criptografada = password_hash($senha, PASSWORD_DEFAULT);

            /* CADASTRAR O USUÁRIO */

            $sql = "INSERT INTO usuarios (nome, email, senha)
                    VALUES (?, ?, ?)";

            $stmt = mysqli_prepare($conexao, $sql);

            mysqli_stmt_bind_param(
                $stmt,
                "sss",
                $nome,
                $email,
                $senha_criptografada
            );

            if (mysqli_stmt_execute($stmt)) {

                echo "Cadastro realizado com sucesso!";
                echo "<br><a href='index.php?pagina=login'>Ir para o login</a>";

            } else {

                echo "Erro ao realizar cadastro.";

            }

        }

        mysqli_stmt_close($stmt);

    }

}

?>