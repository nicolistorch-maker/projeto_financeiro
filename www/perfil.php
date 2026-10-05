<?php

session_start();

include("conexao.php");

if (!isset($_SESSION["id_usuario"])) {

    header("Location: index.php?pagina=login");
    exit;

}

$id_usuario = $_SESSION["id_usuario"];

$sql = "SELECT * FROM usuarios WHERE id = ?";

$stmt = mysqli_prepare($conexao, $sql);

mysqli_stmt_bind_param(
    $stmt,
    "i",
    $id_usuario
);

mysqli_stmt_execute($stmt);

$resultado = mysqli_stmt_get_result($stmt);

if (mysqli_num_rows($resultado) == 0) {

    session_destroy();

    header("Location: index.php?pagina=login");
    exit;

}

$usuario = mysqli_fetch_array($resultado);

?>

<!DOCTYPE html>
<html lang="pt-br">

<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Meu Controle Financeiro - Meu Perfil</title>

    <style>

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: Arial, Helvetica, sans-serif;
        }

        body {
            background: #f5f7fb;
            color: #1f2937;
        }

        .container {
            max-width: 1100px;
            margin: 0 auto;
            padding: 30px 20px;
        }

        .navbar {
            background: #111827;
            color: white;
            padding: 15px 20px;
            border-radius: 5px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
        }

        .logo {
            font-size: 16px;
            font-weight: bold;
        }

        .menu {
            display: flex;
            align-items: center;
            gap: 7px;
        }

        .menu a {
            color: white;
            text-decoration: none;
            font-size: 13px;
        }

        .menu a:hover {
            text-decoration: underline;
        }

        .conteudo {
            display: grid;
            grid-template-columns: 1.3fr 1fr;
            gap: 16px;
        }

        .card {
            background: white;
            border: 1px solid #dbe3ec;
            border-radius: 6px;
            padding: 20px;
        }

        .card h2 {
            font-size: 16px;
            margin-bottom: 18px;
        }

        label {
            display: block;
            font-size: 13px;
            font-weight: bold;
            margin-bottom: 7px;
        }

        input {
            width: 100%;
            height: 32px;
            padding: 7px 10px;
            border: 1px solid #cbd5e1;
            border-radius: 4px;
            margin-bottom: 16px;
            color: #1f2937;
        }

        button {
            border: none;
            border-radius: 4px;
            padding: 9px 13px;
            background: #3b82f6;
            color: white;
            font-weight: bold;
            cursor: pointer;
        }

        button:hover {
            background: #2563eb;
        }

        .excluir {
            border: 1px solid #fca5a5;
            background: #fff7f7;
            color: #991b1b;
        }

        .excluir h2 {
            color: #dc2626;
            margin-bottom: 8px;
        }

        .excluir p {
            color: #991b1b;
            font-size: 13px;
            line-height: 1.45;
            margin-bottom: 12px;
        }

        .botao-excluir {
            width: 100%;
            background: #ef4444;
            color: white;
            padding: 9px;
        }

        .botao-excluir:hover {
            background: #dc2626;
        }

        @media (max-width: 700px) {

            .navbar {
                flex-direction: column;
                gap: 12px;
            }

            .conteudo {
                grid-template-columns: 1fr;
            }

        }

        @media (max-width: 450px) {

            .container {
                padding: 20px 12px;
            }

            .menu {
                flex-wrap: wrap;
                justify-content: center;
            }

        }

    </style>

</head>

<body>

    <main class="container">

        <nav class="navbar">

            <div class="logo">
                Meu Controle Financeiro
            </div>

            <div class="menu">

                <a href="dashboard.php">
                    Dashboard
                </a>

                <span>|</span>

                <a href="perfil.php">
                    <strong>Meu Perfil</strong>
                </a>

            </div>

        </nav>


        <section class="conteudo">

            <div class="card">

                <h2>
                    Alterar Dados de Acesso
                </h2>

                <form action="atualizar_perfil.php" method="POST">

                    <label for="nome">
                        Nome
                    </label>

                    <input
                        type="text"
                        id="nome"
                        name="nome"
                        value="<?php echo htmlspecialchars($usuario["nome"]); ?>"
                        required
                    >

                    <label for="senha">
                        Nova Senha
                    </label>

                    <input
                        type="password"
                        id="senha"
                        name="senha"
                        placeholder="Digite uma nova senha"
                        minlength="6"
                    >

                    <button type="submit">
                        Atualizar Dados
                    </button>

                </form>

            </div>


            <div class="card excluir">

                <h2>
                    Encerrar Conta
                </h2>

                <p>
                    Esta ação é permanente e apagará todas as suas
                    receitas e despesas do banco de dados imediatamente.
                </p>

                <form
                    action="excluir_conta.php"
                    method="POST"
                    onsubmit="return confirm('Tem certeza que deseja excluir sua conta? Esta ação não pode ser desfeita.');"
                >

                    <button
                        type="submit"
                        class="botao-excluir"
                    >
                        Excluir Conta Permanentemente
                    </button>

                </form>

            </div>

        </section>

    </main>

</body>

</html>