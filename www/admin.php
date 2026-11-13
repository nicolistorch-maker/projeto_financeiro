<?php

session_start();

if (!isset($_SESSION["id_usuario"])) {
    header("Location: index.php?pagina=login");
    exit;
}

if ($_SESSION["perfil_usuario"] != "admin") {
    echo "Acesso negado. Esta área é exclusiva para administradores.";
    exit;
}

include("conexao.php");

// CONTAR USUARIOS

$sql_usuarios = "SELECT COUNT(*) AS total FROM usuarios";
$resultado_usuarios = mysqli_query($conexao, $sql_usuarios);
$total_usuarios = mysqli_fetch_assoc($resultado_usuarios);

// CONTAR TRANSACOES

$sql_transacoes = "SELECT COUNT(*) AS total FROM transacoes";
$resultado_transacoes = mysqli_query($conexao, $sql_transacoes);
$total_transacoes = mysqli_fetch_assoc($resultado_transacoes);

// LISTAR USUARIOS

$sql_lista = "SELECT * FROM usuarios ORDER BY id DESC";
$resultado_lista = mysqli_query($conexao, $sql_lista);

?>

<!DOCTYPE html>
<html lang="pt-br">

<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Administração - Meu Controle Financeiro</title>

    <style>

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: Arial, Helvetica, sans-serif;
        }

        body {
            background: #090909;
            color: #ffffff;
        }

        .container {
            max-width: 1100px;
            margin: 0 auto;
            padding: 30px 20px;
        }

        .navbar {
            background: #151515;
            border: 1px solid #292929;
            border-radius: 10px;
            padding: 18px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 25px;
        }

        .logo {
            font-size: 18px;
            font-weight: bold;
            color: #c084fc;
        }

        .menu {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .menu a {
            color: #cccccc;
            text-decoration: none;
            font-size: 13px;
        }

        .menu a:hover {
            color: #c084fc;
        }

        .menu .sair {
            color: #f87171;
        }

        .titulo {
            margin-bottom: 22px;
        }

        .titulo h1 {
            font-size: 24px;
            margin-bottom: 7px;
        }

        .titulo p {
            color: #999999;
            font-size: 14px;
        }

        .estatisticas {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 18px;
            margin-bottom: 25px;
        }

        .estatistica {
            background: #151515;
            border: 1px solid #292929;
            border-left: 4px solid #a855f7;
            border-radius: 10px;
            padding: 22px;
        }

        .estatistica p {
            color: #aaaaaa;
            font-size: 13px;
            margin-bottom: 10px;
        }

        .estatistica strong {
            font-size: 26px;
            color: #ffffff;
        }

        .usuarios {
            background: #151515;
            border: 1px solid #292929;
            border-radius: 10px;
            padding: 20px;
        }

        .usuarios h2 {
            font-size: 17px;
            margin-bottom: 20px;
        }

        .tabela-container {
            width: 100%;
            overflow-x: auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 13px;
        }

        th {
            background: #222222;
            color: #c084fc;
            text-align: left;
            padding: 13px 10px;
        }

        td {
            padding: 13px 10px;
            border-bottom: 1px solid #303030;
            color: #dddddd;
        }

        .perfil {
            display: inline-block;
            padding: 5px 9px;
            border-radius: 5px;
            font-size: 11px;
            font-weight: bold;
        }

        .comum {
            background: #374151;
            color: #ffffff;
        }

        .admin {
            background: #6b21a8;
            color: #ffffff;
        }

        .status {
            display: inline-block;
            padding: 5px 9px;
            border-radius: 5px;
            font-size: 11px;
            font-weight: bold;
        }

        .ativo {
            background: #14532d;
            color: #86efac;
        }

        .bloqueado {
            background: #7f1d1d;
            color: #fca5a5;
        }

        .vazio {
            text-align: center;
            color: #999999;
            padding: 25px;
        }

        @media (max-width: 700px) {

            .navbar {
                flex-direction: column;
                gap: 15px;
            }

            .menu {
                flex-wrap: wrap;
                justify-content: center;
            }

            .estatisticas {
                grid-template-columns: 1fr;
            }

        }

        @media (max-width: 450px) {

            .container {
                padding: 20px 12px;
            }

            .logo {
                text-align: center;
            }

        }

    </style>

</head>

<body>

    <main class="container">

        <nav class="navbar">

            <div class="logo">
                ⚙ Painel Administrativo
            </div>

            <div class="menu">

                <a href="admin.php">Painel</a>

                <a href="logout.php" class="sair">Sair</a>

            </div>

        </nav>

        <section class="titulo">

            <h1>Bem-vindo, Administrador!</h1>

            <p>Gerencie os usuários e acompanhe o sistema.</p>

        </section>

        <section class="estatisticas">

            <div class="estatistica">

                <p>Total de usuários cadastrados</p>

                <strong>
                    <?php echo $total_usuarios["total"]; ?>
                </strong>

            </div>

            <div class="estatistica">

                <p>Total de transações registradas</p>

                <strong>
                    <?php echo $total_transacoes["total"]; ?>
                </strong>

            </div>

        </section>

        <section class="usuarios">

            <h2>Gerenciamento de Usuários</h2>

            <div class="tabela-container">

                <table>

                    <thead>

                        <tr>
                            <th>ID</th>
                            <th>Nome</th>
                            <th>E-mail</th>
                            <th>Perfil</th>
                            <th>Status</th>
                        </tr>

                    </thead>

                    <tbody>

                        <?php if (mysqli_num_rows($resultado_lista) > 0) { ?>

                            <?php while ($usuario = mysqli_fetch_assoc($resultado_lista)) { ?>

                                <tr>

                                    <td>
                                        <?php echo $usuario["id"]; ?>
                                    </td>

                                    <td>
                                        <?php echo htmlspecialchars($usuario["nome"]); ?>
                                    </td>

                                    <td>
                                        <?php echo htmlspecialchars($usuario["email"]); ?>
                                    </td>

                                    <td>

                                        <?php if ($usuario["perfil"] == "admin") { ?>

                                            <span class="perfil admin">
                                                Administrador
                                            </span>

                                        <?php } else { ?>

                                            <span class="perfil comum">
                                                Usuário comum
                                            </span>

                                        <?php } ?>

                                    </td>

                                    <td>

                                        <?php if ($usuario["status"] == "ativo") { ?>

                                            <span class="status ativo">
                                                Ativo
                                            </span>

                                        <?php } else { ?>

                                            <span class="status bloqueado">
                                                Bloqueado
                                            </span>

                                        <?php } ?>

                                    </td>

                                </tr>

                            <?php } ?>

                        <?php } else { ?>

                            <tr>
                                <td colspan="5" class="vazio">
                                    Nenhum usuário cadastrado.
                                </td>
                            </tr>

                        <?php } ?>

                    </tbody>

                </table>

            </div>

        </section>

    </main>

</body>

</html>
