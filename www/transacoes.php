<?php

session_start();

if (!isset($_SESSION["id_usuario"])) {
    header("Location: index.php?pagina=login");
    exit;
}

require_once "conexao.php";

$id_usuario = (int) $_SESSION["id_usuario"];

$pagina = $_GET["pagina"] ?? "lancamentos";

if (!in_array($pagina, ["lancamentos", "relatorios"], true)) {
    $pagina = "lancamentos";
}

$mensagem = "";
$tipo_mensagem = "";
$edicao = null;

function escapar($texto)
{
    return htmlspecialchars((string) $texto, ENT_QUOTES, "UTF-8");
}

function formatar_valor($valor)
{
    return number_format((float) $valor, 2, ",", ".");
}

function formatar_data($data)
{
    return date("d/m/Y", strtotime($data));
}

function voltar_lancamentos($sucesso)
{
    header("Location: transacoes.php?pagina=lancamentos&sucesso=" . urlencode($sucesso));
    exit;
}

/* CADASTRAR E EDITAR LANÇAMENTOS */

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["salvar"])) {

    $id_transacao = filter_input(INPUT_POST, "id_transacao", FILTER_VALIDATE_INT);
    $descricao = trim($_POST["descricao"] ?? "");
    $valor_texto = trim($_POST["valor"] ?? "");
    $data = $_POST["data"] ?? "";
    $categoria_id = filter_input(INPUT_POST, "categoria_id", FILTER_VALIDATE_INT);
    $tipo = $_POST["tipo"] ?? "";

    if (strpos($valor_texto, ",") !== false) {
        $valor_texto = str_replace(".", "", $valor_texto);
        $valor_texto = str_replace(",", ".", $valor_texto);
    }

    $valor = is_numeric($valor_texto) ? (float) $valor_texto : 0;

    $data_objeto = DateTime::createFromFormat("!Y-m-d", $data);
    $data_valida = $data_objeto && $data_objeto->format("Y-m-d") === $data;

    if ($descricao === "") {
        $mensagem = "Informe a descrição.";
        $tipo_mensagem = "erro";
    } elseif (strlen($descricao) > 200) {
        $mensagem = "A descrição deve ter no máximo 200 caracteres.";
        $tipo_mensagem = "erro";
    } elseif (!is_numeric($valor_texto) || $valor <= 0 || $valor > 99999999.99) {
        $mensagem = "Informe um valor válido, maior que zero.";
        $tipo_mensagem = "erro";
    } elseif (!$data_valida) {
        $mensagem = "Informe uma data válida.";
        $tipo_mensagem = "erro";
    } elseif (!in_array($tipo, ["receita", "despesa"], true)) {
        $mensagem = "Selecione o tipo do lançamento.";
        $tipo_mensagem = "erro";
    } elseif (!$categoria_id) {
        $mensagem = "Selecione uma categoria.";
        $tipo_mensagem = "erro";
    } else {

        $sql_categoria = "SELECT id FROM categorias WHERE id = ? AND tipo = ?";
        $stmt_categoria = mysqli_prepare($conexao, $sql_categoria);

        mysqli_stmt_bind_param($stmt_categoria, "is", $categoria_id, $tipo);
        mysqli_stmt_execute($stmt_categoria);

        $resultado_categoria = mysqli_stmt_get_result($stmt_categoria);

        if (mysqli_num_rows($resultado_categoria) === 0) {
            $mensagem = "A categoria não corresponde ao tipo selecionado.";
            $tipo_mensagem = "erro";
        } else {

            if ($id_transacao) {

                $sql = "UPDATE transacoes
                        SET categoria_id = ?, descricao = ?, valor = ?, tipo = ?, data = ?
                        WHERE id = ? AND usuario_id = ?";

                $stmt = mysqli_prepare($conexao, $sql);

                mysqli_stmt_bind_param(
                    $stmt,
                    "isdssii",
                    $categoria_id,
                    $descricao,
                    $valor,
                    $tipo,
                    $data,
                    $id_transacao,
                    $id_usuario
                );

                if (mysqli_stmt_execute($stmt) && mysqli_stmt_affected_rows($stmt) >= 0) {
                    mysqli_stmt_close($stmt);
                    mysqli_stmt_close($stmt_categoria);
                    voltar_lancamentos("edicao");
                }

                mysqli_stmt_close($stmt);

                $mensagem = "Não foi possível atualizar o lançamento.";
                $tipo_mensagem = "erro";

            } else {

                $sql = "INSERT INTO transacoes
                        (usuario_id, categoria_id, descricao, valor, tipo, data)
                        VALUES (?, ?, ?, ?, ?, ?)";

                $stmt = mysqli_prepare($conexao, $sql);

                mysqli_stmt_bind_param(
                    $stmt,
                    "iisdss",
                    $id_usuario,
                    $categoria_id,
                    $descricao,
                    $valor,
                    $tipo,
                    $data
                );

                if (mysqli_stmt_execute($stmt)) {
                    mysqli_stmt_close($stmt);
                    mysqli_stmt_close($stmt_categoria);
                    voltar_lancamentos("cadastro");
                }

                mysqli_stmt_close($stmt);

                $mensagem = "Não foi possível cadastrar o lançamento.";
                $tipo_mensagem = "erro";
            }
        }

        mysqli_stmt_close($stmt_categoria);
    }
}

/* EXCLUIR LANÇAMENTO */

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["excluir"])) {

    $id_transacao = filter_input(INPUT_POST, "id_transacao", FILTER_VALIDATE_INT);

    if ($id_transacao) {

        $sql = "DELETE FROM transacoes WHERE id = ? AND usuario_id = ?";
        $stmt = mysqli_prepare($conexao, $sql);

        mysqli_stmt_bind_param($stmt, "ii", $id_transacao, $id_usuario);
        mysqli_stmt_execute($stmt);

        if (mysqli_stmt_affected_rows($stmt) > 0) {
            mysqli_stmt_close($stmt);
            voltar_lancamentos("exclusao");
        }

        mysqli_stmt_close($stmt);

        $mensagem = "Lançamento não encontrado ou sem permissão para excluí-lo.";
        $tipo_mensagem = "erro";

    } else {
        $mensagem = "Lançamento inválido.";
        $tipo_mensagem = "erro";
    }
}

/* MENSAGENS */

if (isset($_GET["sucesso"])) {

    $mensagens = [
        "cadastro" => "Lançamento cadastrado com sucesso!",
        "edicao" => "Lançamento atualizado com sucesso!",
        "exclusao" => "Lançamento excluído com sucesso!"
    ];

    if (isset($mensagens[$_GET["sucesso"]])) {
        $mensagem = $mensagens[$_GET["sucesso"]];
        $tipo_mensagem = "sucesso";
    }
}

/* CARREGAR LANÇAMENTO PARA EDIÇÃO */

if (isset($_GET["editar"])) {

    $id_editar = filter_input(INPUT_GET, "editar", FILTER_VALIDATE_INT);

    if ($id_editar) {

        $sql = "SELECT id, descricao, valor, data, tipo, categoria_id
                FROM transacoes
                WHERE id = ? AND usuario_id = ?";

        $stmt = mysqli_prepare($conexao, $sql);
        mysqli_stmt_bind_param($stmt, "ii", $id_editar, $id_usuario);
        mysqli_stmt_execute($stmt);

        $resultado_edicao = mysqli_stmt_get_result($stmt);
        $edicao = mysqli_fetch_assoc($resultado_edicao);

        mysqli_stmt_close($stmt);

        if (!$edicao) {
            $mensagem = "Lançamento não encontrado.";
            $tipo_mensagem = "erro";
        }
    }
}

/* FILTROS DOS RELATÓRIOS */

$filtro = $_GET["periodo"] ?? "mes";

if (!in_array($filtro, ["mes", "3meses", "6meses", "ano", "todos"], true)) {
    $filtro = "mes";
}

$data_inicio = null;
$data_fim = date("Y-m-d");

if ($filtro === "mes") {
    $data_inicio = date("Y-m-01");
} elseif ($filtro === "3meses") {
    $data_inicio = date("Y-m-d", strtotime("-3 months"));
} elseif ($filtro === "6meses") {
    $data_inicio = date("Y-m-d", strtotime("-6 months"));
} elseif ($filtro === "ano") {
    $data_inicio = date("Y-01-01");
}

/* CONSULTAR LANÇAMENTOS */

$sql = "SELECT t.id, t.descricao, t.valor, t.tipo, t.data, t.categoria_id,
               c.nome AS categoria
        FROM transacoes t
        INNER JOIN categorias c ON t.categoria_id = c.id
        WHERE t.usuario_id = ?";

if ($pagina === "relatorios" && $data_inicio !== null) {
    $sql .= " AND t.data >= ? AND t.data <= ?";
}

$sql .= " ORDER BY t.data DESC, t.id DESC";

$stmt = mysqli_prepare($conexao, $sql);

if ($pagina === "relatorios" && $data_inicio !== null) {
    mysqli_stmt_bind_param($stmt, "iss", $id_usuario, $data_inicio, $data_fim);
} else {
    mysqli_stmt_bind_param($stmt, "i", $id_usuario);
}

mysqli_stmt_execute($stmt);
$resultado = mysqli_stmt_get_result($stmt);

$transacoes = [];
$total_receitas = 0;
$total_despesas = 0;

while ($linha = mysqli_fetch_assoc($resultado)) {
    $transacoes[] = $linha;

    if ($linha["tipo"] === "receita") {
        $total_receitas += (float) $linha["valor"];
    } else {
        $total_despesas += (float) $linha["valor"];
    }
}

mysqli_stmt_close($stmt);

$saldo = $total_receitas - $total_despesas;

/* CARREGAR CATEGORIAS */

$categorias_receita = [];
$categorias_despesa = [];

$resultado_categorias = mysqli_query(
    $conexao,
    "SELECT id, nome, tipo FROM categorias ORDER BY nome"
);

while ($categoria = mysqli_fetch_assoc($resultado_categorias)) {
    if ($categoria["tipo"] === "receita") {
        $categorias_receita[] = $categoria;
    } else {
        $categorias_despesa[] = $categoria;
    }
}

?><!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lançamentos - Meu Controle Financeiro</title>

    <style>
        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            font-family: Arial, Helvetica, sans-serif;
            background: #f4f6f9;
            color: #333;
        }

        header {
            background: #243447;
            color: white;
            padding: 20px 5%;
        }

        header h1 {
            margin: 0;
            font-size: 24px;
        }

        nav {
            background: white;
            padding: 15px 5%;
            display: flex;
            gap: 25px;
            flex-wrap: wrap;
            box-shadow: 0 2px 5px #00000010;
        }

        nav a {
            text-decoration: none;
            color: #243447;
            font-weight: bold;
        }

        nav a:hover {
            color: #16875d;
        }

        .container {
            width: 90%;
            max-width: 1200px;
            margin: 30px auto;
        }

        .titulo {
            margin-bottom: 25px;
        }

        .titulo h2 {
            margin-bottom: 8px;
        }

        .titulo p {
            color: #777;
        }

        .mensagem {
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
        }

        .sucesso {
            background: #d4edda;
            color: #155724;
        }

        .erro {
            background: #f8d7da;
            color: #721c24;
        }

        .resumos {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 20px;
            margin-bottom: 25px;
        }

        .resumo {
            background: white;
            padding: 22px;
            border-radius: 10px;
            box-shadow: 0 2px 8px #00000008;
        }

        .resumo p {
            margin: 0 0 12px;
            color: #777;
        }

        .resumo h3 {
            margin: 0;
            font-size: 24px;
        }

        .receita {
            color: #16875d;
        }

        .despesa {
            color: #d64545;
        }

        .saldo {
            color: #243447;
        }

        .cartao {
            background: white;
            border-radius: 10px;
            padding: 25px;
            margin-bottom: 25px;
            box-shadow: 0 2px 8px #00000008;
        }

        .cartao h2 {
            margin-top: 0;
            margin-bottom: 20px;
        }

        .formulario {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 18px;
        }

        .campo {
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        .campo-completo {
            grid-column: 1 / -1;
        }

        label {
            font-weight: bold;
            font-size: 14px;
        }

        input,
        select {
            width: 100%;
            padding: 12px;
            border: 1px solid #d5dbe1;
            border-radius: 6px;
            font-size: 15px;
            background: white;
        }

        input:focus,
        select:focus {
            outline: 2px solid #16875d30;
            border-color: #16875d;
        }

        .opcoes-tipo {
            display: flex;
            gap: 20px;
            flex-wrap: wrap;
        }

        .opcoes-tipo label {
            display: flex;
            align-items: center;
            gap: 7px;
            font-weight: normal;
        }

        .opcoes-tipo input {
            width: auto;
        }

        .botoes {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
            margin-top: 5px;
        }

        button,
        .botao {
            display: inline-block;
            padding: 11px 16px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-size: 14px;
            font-weight: bold;
            text-decoration: none;
            text-align: center;
        }

        .botao-principal {
            background: #16875d;
            color: white;
        }

        .botao-principal:hover {
            background: #116b49;
        }

        .botao-secundario {
            background: #e5e9ee;
            color: #333;
        }

        .botao-editar {
            background: #e8f0fe;
            color: #2456a6;
        }

        .botao-excluir {
            background: #fde8e8;
            color: #b42323;
        }

        .tabela-container {
            overflow-x: auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            min-width: 750px;
        }

        th,
        td {
            text-align: left;
            padding: 14px 12px;
            border-bottom: 1px solid #eee;
        }

        th {
            background: #f8f9fb;
            color: #555;
            font-size: 13px;
        }

        td {
            font-size: 14px;
        }

        .etiqueta {
            display: inline-block;
            padding: 5px 9px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: bold;
        }

        .etiqueta-receita {
            background: #d4edda;
            color: #155724;
        }

        .etiqueta-despesa {
            background: #f8d7da;
            color: #721c24;
        }

        .acoes {
            display: flex;
            gap: 8px;
            align-items: center;
        }

        .acoes form {
            margin: 0;
        }

        .acoes button,
        .acoes a {
            white-space: nowrap;
        }

        .vazio {
            text-align: center;
            color: #777;
            padding: 30px;
        }

        .filtros {
            display: flex;
            gap: 12px;
            align-items: center;
            flex-wrap: wrap;
            margin-bottom: 20px;
        }

        .filtros select {
            width: auto;
            min-width: 180px;
        }

        .relatorio-linha {
            display: flex;
            justify-content: space-between;
            gap: 15px;
            padding: 15px 0;
            border-bottom: 1px solid #eee;
        }

        .barra-fundo {
            height: 10px;
            background: #edf0f3;
            border-radius: 10px;
            overflow: hidden;
            margin-top: 8px;
        }

        .barra {
            height: 100%;
            background: #d64545;
            border-radius: 10px;
        }

        footer {
            text-align: center;
            color: #888;
            padding: 25px;
            font-size: 13px;
        }

        @media (max-width: 750px) {
            .resumos {
                grid-template-columns: 1fr;
            }

            .formulario {
                grid-template-columns: 1fr;
            }

            .campo-completo {
                grid-column: auto;
            }

            .container {
                width: 94%;
            }

            .cartao {
                padding: 18px;
            }
        }
    </style>
</head>

<body>

<header>
    <h1>Meu Controle Financeiro</h1>
</header>

<nav>
    <a href="dashboard.php">Início</a>
    <a href="transacoes.php?pagina=lancamentos">Lançamentos</a>
    <a href="transacoes.php?pagina=relatorios">Relatórios</a>
    <a href="perfil.php">Meu Perfil</a>
    <a href="logout.php">Sair</a>
</nav>

<main class="container">

    <div class="titulo">
        <?php if ($pagina === "relatorios"): ?>
            <h2>Relatórios financeiros</h2>
            <p>Consulte suas receitas, despesas e seu saldo.</p>
        <?php else: ?>
            <h2>Meus lançamentos</h2>
            <p>Cadastre, consulte, edite e exclua suas movimentações financeiras.</p>
        <?php endif; ?>
    </div>

    <?php if ($mensagem !== ""): ?>
        <div class="mensagem <?php echo escapar($tipo_mensagem); ?>">
            <?php echo escapar($mensagem); ?>
        </div>
    <?php endif; ?>

    <section class="resumos">

        <div class="resumo">
            <p>Total de receitas</p>
            <h3 class="receita">
                R$ <?php echo formatar_valor($total_receitas); ?>
            </h3>
        </div>

        <div class="resumo">
            <p>Total de despesas</p>
            <h3 class="despesa">
                R$ <?php echo formatar_valor($total_despesas); ?>
            </h3>
        </div>

        <div class="resumo">
            <p>Saldo do período</p>
            <h3 class="saldo">
                R$ <?php echo formatar_valor($saldo); ?>
            </h3>
        </div>

    </section>

    <?php if ($pagina === "lancamentos"): ?>

        <section class="cartao" id="formulario-lancamento">

            <h2>
                <?php echo $edicao ? "Editar lançamento" : "Novo lançamento"; ?>
            </h2>

            <form method="POST" action="transacoes.php?pagina=lancamentos">

                <?php if ($edicao): ?>
                    <input
                        type="hidden"
                        name="id_transacao"
                        value="<?php echo (int) $edicao["id"]; ?>"
                    >
                <?php endif; ?>

                <div class="formulario">

                    <div class="campo campo-completo">
                        <label>Tipo de lançamento</label>

                        <div class="opcoes-tipo">

                            <label>
                                <input
                                    type="radio"
                                    name="tipo"
                                    value="receita"
                                    required
                                    <?php
                                    echo (($edicao["tipo"] ?? "") === "receita")
                                        ? "checked"
                                        : (!$edicao ? "checked" : "");
                                    ?>
                                >
                                Receita
                            </label>

                            <label>
                                <input
                                    type="radio"
                                    name="tipo"
                                    value="despesa"
                                    required
                                    <?php
                                    echo (($edicao["tipo"] ?? "") === "despesa")
                                        ? "checked"
                                        : "";
                                    ?>
                                >
                                Despesa
                            </label>

                        </div>
                    </div>

                    <div class="campo">
                        <label for="descricao">Descrição</label>

                        <input
                            type="text"
                            id="descricao"
                            name="descricao"
                            maxlength="200"
                            placeholder="Ex.: Compra no mercado"
                            value="<?php echo escapar($edicao["descricao"] ?? ""); ?>"
                            required
                        >
                    </div>

                    <div class="campo">
                        <label for="valor">Valor (R$)</label>

                        <input
                            type="text"
                            id="valor"
                            name="valor"
                            placeholder="Ex.: 150,00"
                            value="<?php
                            echo isset($edicao["valor"])
                                ? escapar(number_format((float) $edicao["valor"], 2, ",", "."))
                                : "";
                            ?>"
                            required
                        >
                    </div>

                    <div class="campo">
                        <label for="data">Data</label>

                        <input
                            type="date"
                            id="data"
                            name="data"
                            value="<?php
                            echo escapar($edicao["data"] ?? date("Y-m-d"));
                            ?>"
                            required
                        >
                    </div>

                    <div class="campo">
                        <label for="categoria_id">Categoria</label>

                        <select id="categoria_id" name="categoria_id" required>
                            <option value="">Selecione uma categoria</option>

                            <?php foreach ($categorias_receita as $categoria): ?>
                                <option
                                    value="<?php echo (int) $categoria["id"]; ?>"
                                    data-tipo="receita"
                                    <?php
                                    echo (
                                        (int) ($edicao["categoria_id"] ?? 0)
                                        === (int) $categoria["id"]
                                    ) ? "selected" : "";
                                    ?>
                                >
                                    <?php echo escapar($categoria["nome"]); ?>
                                </option>
                            <?php endforeach; ?>

                            <?php foreach ($categorias_despesa as $categoria): ?>
                                <option
                                    value="<?php echo (int) $categoria["id"]; ?>"
                                    data-tipo="despesa"
                                    <?php
                                    echo (
                                        (int) ($edicao["categoria_id"] ?? 0)
                                        === (int) $categoria["id"]
                                    ) ? "selected" : "";
                                    ?>
                                >
                                    <?php echo escapar($categoria["nome"]); ?>
                                </option>
                            <?php endforeach; ?>

                        </select>
                    </div>

                    <div class="campo campo-completo">
                        <div class="botoes">

                            <button
                                type="submit"
                                name="salvar"
                                value="1"
                                class="botao-principal"
                            >
                                <?php
                                echo $edicao
                                    ? "Salvar alterações"
                                    : "Cadastrar lançamento";
                                ?>
                            </button>

                            <?php if ($edicao): ?>
                                <a
                                    href="transacoes.php?pagina=lancamentos"
                                    class="botao botao-secundario"
                                >
                                    Cancelar edição
                                </a>
                            <?php endif; ?>

                        </div>
                    </div>

                </div>
            </form>

        </section>

        <section class="cartao">

            <h2>Histórico de lançamentos</h2>

            <div class="tabela-container">

                <table>
                    <thead>
                        <tr>
                            <th>Descrição</th>
                            <th>Categoria</th>
                            <th>Tipo</th>
                            <th>Data</th>
                            <th>Valor</th>
                            <th>Ações</th>
                        </tr>
                    </thead>

                    <tbody>

                    <?php if (count($transacoes) === 0): ?>

                        <tr>
                            <td colspan="6" class="vazio">
                                Nenhum lançamento cadastrado.
                            </td>
                        </tr>

                    <?php else: ?>

                        <?php foreach ($transacoes as $transacao): ?>

                            <tr>
                                <td><?php echo escapar($transacao["descricao"]); ?></td>

                                <td><?php echo escapar($transacao["categoria"]); ?></td>

                                <td>
                                    <?php if ($transacao["tipo"] === "receita"): ?>
                                        <span class="etiqueta etiqueta-receita">
                                            Receita
                                        </span>
                                    <?php else: ?>
                                        <span class="etiqueta etiqueta-despesa">
                                            Despesa
                                        </span>
                                    <?php endif; ?>
                                </td>

                                <td>
                                    <?php echo formatar_data($transacao["data"]); ?>
                                </td>

                                <td class="<?php
                                    echo $transacao["tipo"] === "receita"
                                        ? "receita"
                                        : "despesa";
                                ?>">
                                    R$ <?php echo formatar_valor($transacao["valor"]); ?>
                                </td>

                                <td>
                                    <div class="acoes">

                                        <a
                                            class="botao botao-editar"
                                            href="transacoes.php?pagina=lancamentos&amp;editar=<?php echo (int) $transacao["id"]; ?>#formulario-lancamento"
                                        >
                                            Editar
                                        </a>

                                        <form
                                            method="POST"
                                            action="transacoes.php?pagina=lancamentos"
                                            onsubmit="return confirm('Tem certeza de que deseja excluir este lançamento?');"
                                        >
                                            <input
                                                type="hidden"
                                                name="id_transacao"
                                                value="<?php echo (int) $transacao["id"]; ?>"
                                            >

                                            <button
                                                type="submit"
                                                name="excluir"
                                                value="1"
                                                class="botao-excluir"
                                            >
                                                Excluir
                                            </button>
                                        </form>

                                    </div>
                                </td>
                            </tr>

                        <?php endforeach; ?>

                    <?php endif; ?>

                    </tbody>
                </table>

            </div>

        </section>

    <?php else: ?>

        <section class="cartao">

            <h2>Consultar período</h2>

            <form method="GET" action="transacoes.php" class="filtros">

                <input type="hidden" name="pagina" value="relatorios">

                <label for="periodo">Período:</label>

                <select name="periodo" id="periodo">

                    <option value="mes" <?php echo $filtro === "mes" ? "selected" : ""; ?>>
                        Mês atual
                    </option>

                    <option value="3meses" <?php echo $filtro === "3meses" ? "selected" : ""; ?>>
                        Últimos 3 meses
                    </option>

                    <option value="6meses" <?php echo $filtro === "6meses" ? "selected" : ""; ?>>
                        Últimos 6 meses
                    </option>

                    <option value="ano" <?php echo $filtro === "ano" ? "selected" : ""; ?>>
                        Ano atual
                    </option>

                    <option value="todos" <?php echo $filtro === "todos" ? "selected" : ""; ?>>
                        Todo o histórico
                    </option>

                </select>

                <button type="submit" class="botao-principal">
                    Filtrar
                </button>

            </form>

            <p>
                Período consultado:
                <strong>
                    <?php
                    if ($filtro === "todos") {
                        echo "Todo o histórico";
                    } else {
                        echo formatar_data($data_inicio)
                            . " até "
                            . formatar_data($data_fim);
                    }
                    ?>
                </strong>
            </p>

        </section>

        <section class="cartao">

            <h2>Resumo financeiro</h2>

            <div class="relatorio-linha">
                <span>Total de receitas</span>
                <strong class="receita">
                    R$ <?php echo formatar_valor($total_receitas); ?>
                </strong>
            </div>

            <div class="relatorio-linha">
                <span>Total de despesas</span>
                <strong class="despesa">
                    R$ <?php echo formatar_valor($total_despesas); ?>
                </strong>
            </div>

            <div class="relatorio-linha">
                <span>Saldo do período</span>
                <strong>
                    R$ <?php echo formatar_valor($saldo); ?>
                </strong>
            </div>

        </section>

        <section class="cartao">

            <h2>Despesas por categoria</h2>

            <?php
            $despesas_categorias = [];

            foreach ($transacoes as $transacao) {
                if ($transacao["tipo"] === "despesa") {
                    $nome_categoria = $transacao["categoria"];

                    if (!isset($despesas_categorias[$nome_categoria])) {
                        $despesas_categorias[$nome_categoria] = 0;
                    }

                    $despesas_categorias[$nome_categoria] +=
                        (float) $transacao["valor"];
                }
            }

            arsort($despesas_categorias);
            ?>

            <?php if (count($despesas_categorias) === 0): ?>

                <p class="vazio">Não existem despesas nesse período.</p>

            <?php else: ?>

                <?php foreach ($despesas_categorias as $nome => $valor_categoria): ?>

                    <?php
                    $percentual = $total_despesas > 0
                        ? ($valor_categoria / $total_despesas) * 100
                        : 0;
                    ?>

                    <div style="margin-bottom: 22px;">

                        <div class="relatorio-linha">
                            <span><?php echo escapar($nome); ?></span>

                            <strong>
                                R$ <?php echo formatar_valor($valor_categoria); ?>
                            </strong>
                        </div>

                        <div class="barra-fundo">
                            <div
                                class="barra"
                                style="width: <?php echo min(100, max(0, $percentual)); ?>%;"
                            ></div>
                        </div>

                        <small>
                            <?php echo number_format($percentual, 1, ",", "."); ?>%
                            das despesas
                        </small>

                    </div>

                <?php endforeach; ?>

            <?php endif; ?>

        </section>

        <section class="cartao">

            <h2>Lançamentos do período</h2>

            <div class="tabela-container">

                <table>
                    <thead>
                        <tr>
                            <th>Descrição</th>
                            <th>Categoria</th>
                            <th>Tipo</th>
                            <th>Data</th>
                            <th>Valor</th>
                        </tr>
                    </thead>

                    <tbody>

                    <?php if (count($transacoes) === 0): ?>

                        <tr>
                            <td colspan="5" class="vazio">
                                Nenhum lançamento encontrado nesse período.
                            </td>
                        </tr>

                    <?php else: ?>

                        <?php foreach ($transacoes as $transacao): ?>

                            <tr>
                                <td><?php echo escapar($transacao["descricao"]); ?></td>

                                <td><?php echo escapar($transacao["categoria"]); ?></td>

                                <td>
                                    <?php echo $transacao["tipo"] === "receita"
                                        ? "Receita"
                                        : "Despesa"; ?>
                                </td>

                                <td><?php echo formatar_data($transacao["data"]); ?></td>

                                <td class="<?php
                                    echo $transacao["tipo"] === "receita"
                                        ? "receita"
                                        : "despesa";
                                ?>">
                                    R$ <?php echo formatar_valor($transacao["valor"]); ?>
                                </td>
                            </tr>

                        <?php endforeach; ?>

                    <?php endif; ?>

                    </tbody>
                </table>

            </div>

        </section>

    <?php endif; ?>

</main>

<footer>
    Meu Controle Financeiro &copy; <?php echo date("Y"); ?>
</footer>

<script>
    const radiosTipo = document.querySelectorAll('input[name="tipo"]');
    const campoCategoria = document.getElementById("categoria_id");

    function atualizarCategorias() {
        if (!campoCategoria) {
            return;
        }

        const selecionado = document.querySelector('input[name="tipo"]:checked');

        if (!selecionado) {
            return;
        }

        const tipo = selecionado.value;
        const opcaoAtual = campoCategoria.value;

        Array.from(campoCategoria.options).forEach(function(opcao) {
            if (!opcao.value) {
                opcao.hidden = false;
                return;
            }

            opcao.hidden = opcao.dataset.tipo !== tipo;
        });

        const opcaoSelecionada = campoCategoria.options[campoCategoria.selectedIndex];

        if (
            opcaoSelecionada &&
            opcaoSelecionada.value &&
            opcaoSelecionada.dataset.tipo !== tipo
        ) {
            campoCategoria.value = "";
        }
    }

    radiosTipo.forEach(function(radio) {
        radio.addEventListener("change", atualizarCategorias);
    });

    atualizarCategorias();
</script>

</body>
</html>