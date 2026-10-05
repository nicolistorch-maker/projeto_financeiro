<?php

session_start();

include("conexao.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $email = $_POST["email"];
    $senha = $_POST["senha"];

    if (empty($email) || empty($senha)) {

        echo "Preencha todos os campos.";

    } else {

        $sql = "SELECT * FROM usuarios
                WHERE email = '$email'";

        $resultado = mysqli_query($conexao, $sql);

        if (mysqli_num_rows($resultado) > 0) {

            $usuario = mysqli_fetch_array($resultado);

            if ($usuario["status"] == "bloqueado") {

                echo "Esta conta está bloqueada.";

            } elseif (password_verify($senha, $usuario["senha"])) {

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
    }
}

?>