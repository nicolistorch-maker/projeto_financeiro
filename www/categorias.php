<?php

session_start();

if (!isset($_SESSION["id_usuario"])) {
    header("Location: index.php?pagina=login");
    exit;
}

if ($_SESSION["perfil_usuario"] != "admin") {
    echo "Acesso negado.";
    exit;
}

include("conexao.php");

$mensagem = "";

// CADASTRAR CATEGORIA

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $nome = trim($_POST["nome"] ?? "");
    $tipo = $_POST["tipo"] ?? "";

    if (empty($nome) || !in_array($tipo, ["receita", "despesa"])) {

        $mensagem = "Preencha todos os campos corretamente.";

    } else {

        // VERIFICAR SE JA EXISTE

        $sql = "SELECT id FROM categorias WHERE nome = ? AND tipo = ?";

        $stmt = mysqli_prepare($conexao, $sql);
        mysqli_stmt_bind_param($stmt, "ss", $nome, $tipo);
        mysqli_stmt_execute($stmt);

        $resultado = mysqli_stmt_get_result($stmt);

        if (mysqli_num_rows($resultado) > 0) {

            $mensagem = "Essa categoria ja existe.";

        } else {

            // INSERIR CATEGORIA

            $sql = "INSERT INTO categorias (nome, tipo) VALUES (?, ?)";

            $stmt2 = mysqli_prepare($conexao, $sql);
            mysqli_stmt_bind_param($stmt2, "ss", $nome, $tipo);

            if (mysqli_stmt_execute($stmt2)) {
                header("Location: categorias.php");
                exit;
            } else {
                $mensagem = "Erro ao cadastrar categoria.";
            }

            mysqli_stmt_close($stmt2);

        }

        mysqli_stmt_close($stmt);

    }

}

// LISTAR CATEGORIAS

$sql = "SELECT * FROM categorias ORDER BY tipo, nome";
$resultado = mysqli_query($conexao, $sql);

?>

<!DOCTYPE html>
<html lang="pt-br">

<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Gerenciar Categorias</title>

    <style>

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: Arial, Helvetica, sans-serif;
        }

        body {
            background: #090909;
            color: white;
        }

        .container {
            max-width: 850px;
            margin: auto;
            padding: 30px 20px;
        }

        .topo {
            background: #151515;
            border: 1px solid #292929;
            border-radius: 10px;
            padding: 18px;
            margin-bottom: 25px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .topo h2 {
            color: #c084fc;
            font-size: 18px;
        }

        .topo a {
            color: #c084fc;
            text-decoration: none;
            font-size: 13px;
        }

        .formulario, .lista {
            background: #151515;
            border: 1px solid #292929;
            border-radius: 10px;
            padding: 22px;
            margin-bottom: 20px;
        }

        h3 {
            margin-bottom: 18px;
        }

        label {
            display: block;
            margin-bottom: 7px;
            color: #cccccc;
            font-size: 13px;
        }

        input, select {
            width: 100%;
            padding: 12px;
            margin-bottom: 16px;
            background: #222222;
            border: 1px solid #444444;
            border-radius: 6px;
            color: white;
        }

        button {
            background: #9333ea;
            color: white;
            border: none;
            padding: 11px 18px;
            border-radius: 6px;
            cursor: pointer;
            font-weight: bold;
        }

        button:hover {
            background: #7e22ce;
        }

        .mensagem {
            color: #fca5a5;
            margin-bottom: 15px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th {
            text-align: left;
            color: #c084fc;
            background: #222222;
            padding: 12px;
        }

        td {
            padding: 12px;
            border-bottom: 1px solid #333333;
            color: #dddddd;
        }

        .tipo {
            color: #c084fc;
            font-size: 12px;
        }

        @media (max-width: 500px) {
            .container {
                padding: 20px 12px;
            }

            .topo {
                flex-direction: column;
                gap: 12px;
            }
        }

    </style>

</head>

<body>

    <main class="container">

        <div class="topo">

            <h2>⚙ Gerenciar Categorias</h2>

            <a href="admin.php">Voltar ao painel</a>

        </div>

        <section class="formulario">

            <h3>Cadastrar nova categoria</h3>

            <?php if (!empty($mensagem)) { ?>

                <p class="mensagem">
                    <?php echo htmlspecialchars($mensagem); ?>
                </p>

            <?php } ?>

            <form action="categorias.php" method="POST">

                <label>Nome da categoria</label>

                <input type="text" name="nome" maxlength="100"
                       placeholder="Ex: Saúde" required>

                <label>Tipo da categoria</label>

                <select name="tipo" required>

                    <option value="">Selecione</option>

                    <option value="receita">Receita</option>

                    <option value="despesa">Despesa</option>

                </select>

                <button type="submit">
                    Cadastrar categoria
                </button>

            </form>

        </section>

        <section class="lista">

            <h3>Categorias cadastradas</h3>

            <table>

                <thead>

                    <tr>
                        <th>ID</th>
                        <th>Nome</th>
                        <th>Tipo</th>
                    </tr>

                </thead>

                <tbody>

                    <?php while ($categoria = mysqli_fetch_assoc($resultado)) { ?>

                        <tr>

                            <td>
                                <?php echo $categoria["id"]; ?>
                            </td>

                            <td>
                                <?php echo htmlspecialchars($categoria["nome"]); ?>
                            </td>

                            <td class="tipo">

                                <?php
                                echo ucfirst($categoria["tipo"]);
                                ?>

                            </td>

                        </tr>

                    <?php } ?>

                </tbody>

            </table>

        </section>

    </main>

</body>

</html>
