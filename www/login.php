<?php

session_start();

include("conexao.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $email = trim($_POST["email"] ?? "");
    $senha = $_POST["senha"] ?? "";

    if (empty($email) || empty($senha)) {

        echo "Preencha todos os campos.";

    } else {

        // BUSCAR O USUARIO PELO E-MAIL

        $sql = "SELECT * FROM usuarios WHERE email = ?";

        $stmt = mysqli_prepare($conexao, $sql);

        mysqli_stmt_bind_param($stmt, "s", $email);

        mysqli_stmt_execute($stmt);

        $resultado = mysqli_stmt_get_result($stmt);

        if (mysqli_num_rows($resultado) > 0) {

            $usuario = mysqli_fetch_assoc($resultado);

            // VERIFICAR SE A CONTA ESTA BLOQUEADA

            if ($usuario["status"] == "bloqueado") {

                echo "Esta conta está bloqueada.";

            } elseif (password_verify($senha, $usuario["senha"])) {

                // CRIAR UMA NOVA SESSAO

                session_regenerate_id(true);

                $_SESSION["id_usuario"] = $usuario["id"];
                $_SESSION["nome_usuario"] = $usuario["nome"];
                $_SESSION["perfil_usuario"] = $usuario["perfil"];

                header("Location: dashboard.php");
                exit;

            } else {

                echo "E-mail ou senha incorretos.";

            }

        } else {

            echo "E-mail ou senha incorretos.";

        }

        mysqli_stmt_close($stmt);

    }

}

?>
