<?php

session_start();

if (!isset($_SESSION["id_usuario"])) {
    header("Location: index.php?pagina=login");
    exit;
}

require_once "conexao.php";

$id_usuario = (int) $_SESSION["id_usuario"];

$nome_usuario = $_SESSION["nome_usuario"] ?? "Usuário";

/* FUNÇÃO PARA FORMATAR VALORES */

function formatar_valor_dashboard($valor)
{
    return number_format((float) $valor, 2, ",", ".");
}

/* FUNÇÃO PARA ESCAPAR TEXTOS */

function escapar_dashboard($texto)
{
    return htmlspecialchars((string) $texto, ENT_QUOTES, "UTF-8");
}

/* TOTAL DE RECEITAS */

$sql = "SELECT COALESCE(SUM(valor), 0) AS total
        FROM transacoes
        WHERE usuario_id = ? AND tipo = 'receita'";

$stmt = mysqli_prepare($conexao, $sql);

mysqli_stmt_bind_param($stmt, "i", $id_usuario);
mysqli_stmt_execute($stmt);

$resultado = mysqli_stmt_get_result($stmt);
$dados = mysqli_fetch_assoc($resultado);

$total_receitas = (float) $dados["total"];

mysqli_stmt_close($stmt);

/* TOTAL DE DESPESAS */

$sql = "SELECT COALESCE(SUM(valor), 0) AS total
        FROM transacoes
        WHERE usuario_id = ? AND tipo = 'despesa'";

$stmt = mysqli_prepare($conexao, $sql);

mysqli_stmt_bind_param($stmt, "i", $id_usuario);
mysqli_stmt_execute($stmt);

$resultado = mysqli_stmt_get_result($stmt);
$dados = mysqli_fetch_assoc($resultado);

$total_despesas = (float) $dados["total"];

mysqli_stmt_close($stmt);

/* SALDO ATUAL */

$saldo_atual = $total_receitas - $total_despesas;

/* EVOLUÇÃO MENSAL DOS ÚLTIMOS SETE MESES */

$meses = [];
$receitas_mensais = [];
$despesas_mensais = [];

for ($i = 6; $i >= 0; $i--) {

    $mes = date(
        "Y-m",
        strtotime("first day of this month -$i months")
    );

    $meses[] = date("m/Y", strtotime($mes . "-01"));

    $receitas_mensais[$mes] = 0;
    $despesas_mensais[$mes] = 0;
}

/* CONSULTAR RECEITAS E DESPESAS POR MÊS */

$sql = "SELECT
            DATE_FORMAT(data, '%Y-%m') AS mes,
            tipo,
            SUM(valor) AS total
        FROM transacoes
        WHERE usuario_id = ?
          AND data >= DATE_FORMAT(
              DATE_SUB(CURDATE(), INTERVAL 6 MONTH),
              '%Y-%m-01'
          )
          AND data <= CURDATE()
        GROUP BY DATE_FORMAT(data, '%Y-%m'), tipo
        ORDER BY mes";

$stmt = mysqli_prepare($conexao, $sql);

mysqli_stmt_bind_param($stmt, "i", $id_usuario);
mysqli_stmt_execute($stmt);

$resultado = mysqli_stmt_get_result($stmt);

while ($linha = mysqli_fetch_assoc($resultado)) {

    $mes = $linha["mes"];

    if (
        $linha["tipo"] === "receita"
        && isset($receitas_mensais[$mes])
    ) {
        $receitas_mensais[$mes] = (float) $linha["total"];
    }

    if (
        $linha["tipo"] === "despesa"
        && isset($despesas_mensais[$mes])
    ) {
        $despesas_mensais[$mes] = (float) $linha["total"];
    }
}

mysqli_stmt_close($stmt);

/* PREPARAR A ESCALA DO GRÁFICO DE BARRAS */

$valores_receitas = array_values($receitas_mensais);
$valores_despesas = array_values($despesas_mensais);

$maior_valor = max(
    1,
    max($valores_receitas),
    max($valores_despesas)
);

// Arredonda o limite superior para facilitar a leitura da escala.
$limite_grafico = ceil($maior_valor / 4) * 4;

/* CONSULTAR AS CINCO MOVIMENTAÇÕES MAIS RECENTES */

$transacoes_recentes = [];

$sql = "SELECT
            descricao,
            valor,
            tipo,
            data
        FROM transacoes
        WHERE usuario_id = ?
        ORDER BY data DESC, id DESC
        LIMIT 5";

$stmt = mysqli_prepare($conexao, $sql);

mysqli_stmt_bind_param($stmt, "i", $id_usuario);
mysqli_stmt_execute($stmt);

$resultado_recentes = mysqli_stmt_get_result($stmt);

while ($linha = mysqli_fetch_assoc($resultado_recentes)) {
    $transacoes_recentes[] = $linha;
}

mysqli_stmt_close($stmt);

?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Meu Controle Financeiro - Dashboard</title>

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

        /* NAVBAR */

        .navbar {
            background: #111827;
            color: white;
            padding: 15px 35px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 20px;
        }

        .logo {
            font-size: 18px;
            font-weight: bold;
        }

        .menu {
            display: flex;
            align-items: center;
            gap: 10px;
            flex-wrap: wrap;
        }

        .menu a {
            color: white;
            text-decoration: none;
            font-size: 14px;
        }

        .menu a:hover {
            text-decoration: underline;
        }

        .menu .ativo {
            font-weight: bold;
            color: #6ee7b7;
        }

        /* CONTAINER */

        .container {
            max-width: 1100px;
            margin: 0 auto;
            padding: 30px;
        }

        /* TITULO */

        .titulo {
            margin-bottom: 25px;
        }

        .titulo h1 {
            font-size: 28px;
            margin-bottom: 8px;
        }

        .titulo p {
            color: #64748b;
            font-size: 15px;
        }

        /* CARDS */

        .cards {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 18px;
            margin-bottom: 25px;
        }

        .card {
            background: white;
            border: 1px solid #e2e8f0;
            border-radius: 7px;
            padding: 22px;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.04);
        }

        .card.saldo {
            border-left: 4px solid #10b981;
        }

        .card.receitas {
            border-left: 4px solid #3b82f6;
        }

        .card.despesas {
            border-left: 4px solid #ef4444;
        }

        .card p {
            color: #64748b;
            font-size: 14px;
            margin-bottom: 7px;
        }

        .valor {
            font-size: 24px;
            font-weight: bold;
            overflow-wrap: anywhere;
        }

        .verde {
            color: #10b981;
        }

        .azul {
            color: #3b82f6;
        }

        .vermelho {
            color: #ef4444;
        }

        /* GRAFICO */

        .grafico {
            background: white;
            border: 1px solid #e2e8f0;
            border-radius: 7px;
            padding: 25px;
            margin-bottom: 25px;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.04);
        }

        .grafico h2 {
            font-size: 17px;
            margin-bottom: 20px;
        }

        .area-grafico {
            display: flex;
            height: 310px;
            padding: 15px 12px 0 0;
            border: 1px dashed #cbd5e1;
            border-radius: 5px;
            background: #fafbfc;
        }

        .escala-grafico {
            width: 75px;
            flex-shrink: 0;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            align-items: flex-end;
            padding: 0 10px 35px 0;
            color: #64748b;
            font-size: 11px;
        }

        .barras-grafico {
            flex: 1;
            min-width: 0;
            display: grid;
            grid-template-columns: repeat(7, minmax(0, 1fr));
            gap: 8px;
            border-bottom: 1px solid #cbd5e1;
            background: repeating-linear-gradient(
                to top,
                transparent 0,
                transparent calc(25% - 1px),
                #e2e8f0 calc(25% - 1px),
                #e2e8f0 25%
            );
        }

        .grupo-mes {
            min-width: 0;
            height: 100%;
            display: flex;
            flex-direction: column;
            justify-content: flex-end;
            align-items: center;
        }

        .colunas {
            width: 100%;
            height: calc(100% - 35px);
            display: flex;
            align-items: flex-end;
            justify-content: center;
            gap: 4px;
        }

        .barra {
            width: min(28px, 38%);
            min-height: 0;
            border-radius: 4px 4px 0 0;
            position: relative;
        }

        .barra-receita {
            background: #3b82f6;
        }

        .barra-despesa {
            background: #ef4444;
        }

        .mes-barra {
            height: 35px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #64748b;
            font-size: 11px;
            white-space: nowrap;
        }

        .legenda {
            display: flex;
            justify-content: center;
            gap: 25px;
            margin-top: 20px;
            font-size: 12px;
            color: #64748b;
        }

        .legenda span {
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .ponto-entrada,
        .ponto-saida {
            width: 10px;
            height: 10px;
            border-radius: 3px;
        }

        .ponto-entrada {
            background: #3b82f6;
        }

        .ponto-saida {
            background: #ef4444;
        }

        /* BOTOES */

        .acoes {
            display: flex;
            gap: 12px;
            margin-top: 20px;
            margin-bottom: 25px;
            flex-wrap: wrap;
        }

        .botao {
            display: inline-block;
            padding: 10px 18px;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            font-size: 14px;
            font-weight: bold;
            border: none;
            cursor: pointer;
        }

        .botao-verde {
            background: #10b981;
        }

        .botao-azul {
            background: #3b82f6;
        }

        .botao:hover {
            opacity: 0.9;
        }

        /* MOVIMENTAÇÕES RECENTES */

        .recentes {
            background: white;
            border: 1px solid #e2e8f0;
            border-radius: 7px;
            padding: 25px;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.04);
        }

        .recentes h2 {
            font-size: 18px;
            margin-bottom: 20px;
        }

        .tabela-container {
            overflow-x: auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            min-width: 500px;
        }

        th,
        td {
            text-align: left;
            padding: 13px 10px;
            border-bottom: 1px solid #e2e8f0;
            font-size: 14px;
        }

        th {
            color: #64748b;
            background: #f8fafc;
        }

        .etiqueta {
            display: inline-block;
            padding: 5px 9px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: bold;
        }

        .etiqueta-receita {
            background: #d1fae5;
            color: #065f46;
        }

        .etiqueta-despesa {
            background: #fee2e2;
            color: #991b1b;
        }

        .vazio {
            text-align: center;
            padding: 25px;
            color: #64748b;
        }

        /* RODAPE */

        footer {
            text-align: center;
            color: #94a3b8;
            padding: 25px;
            font-size: 13px;
        }

        /* RESPONSIVIDADE */

        @media (max-width: 800px) {

            .navbar {
                flex-direction: column;
                gap: 15px;
            }

            .menu {
                justify-content: center;
            }

            .container {
                padding: 25px 20px;
            }

            .cards {
                grid-template-columns: 1fr;
            }

        }

        @media (max-width: 500px) {

            .container {
                padding: 20px 12px;
            }

            .titulo h1 {
                font-size: 24px;
            }

            .acoes {
                flex-direction: column;
            }

            .botao {
                text-align: center;
            }

            .grafico {
                padding: 15px;
            }

            .area-grafico {
                height: 260px;
                padding-right: 5px;
            }

            .escala-grafico {
                width: 58px;
                font-size: 9px;
                padding-right: 6px;
            }

            .barras-grafico {
                gap: 3px;
            }

            .colunas {
                gap: 2px;
            }

            .mes-barra {
                font-size: 9px;
            }

            .legenda {
                gap: 15px;
            }

        }

    </style>

</head>

<body>

    <!-- MENU -->

    <header class="navbar">

        <div class="logo">
            Meu Controle Financeiro
        </div>

        <nav class="menu">

            <a class="ativo" href="dashboard.php">
                Dashboard
            </a>

            <span>|</span>

            <a href="transacoes.php?pagina=lancamentos">
                Lançamentos
            </a>

            <span>|</span>

            <a href="transacoes.php?pagina=relatorios">
                Relatórios
            </a>

            <span>|</span>

            <a href="perfil.php">
                Meu Perfil
            </a>

            <span>|</span>

            <a href="logout.php">
                Sair
            </a>

        </nav>

    </header>

    <main class="container">

        <!-- TITULO -->

        <section class="titulo">

            <h1>
                Olá, <?php echo escapar_dashboard($nome_usuario); ?>!
            </h1>

            <p>
                Confira um resumo da sua situação financeira.
            </p>

        </section>

        <!-- CARDS -->

        <section class="cards">

            <div class="card saldo">

                <p>Saldo Atual</p>

                <div class="valor <?php echo $saldo_atual >= 0 ? 'verde' : 'vermelho'; ?>">
                    R$ <?php echo formatar_valor_dashboard($saldo_atual); ?>
                </div>

            </div>

            <div class="card receitas">

                <p>Total de Receitas</p>

                <div class="valor azul">
                    R$ <?php echo formatar_valor_dashboard($total_receitas); ?>
                </div>

            </div>

            <div class="card despesas">

                <p>Total de Despesas</p>

                <div class="valor vermelho">
                    R$ <?php echo formatar_valor_dashboard($total_despesas); ?>
                </div>

            </div>

        </section>

        <!-- GRAFICO -->

        <section class="grafico">

            <h2>
                Evolução Mensal — Últimos 7 Meses
            </h2>

            <div class="area-grafico">

                <!-- Escala de valores em reais -->

                <div class="escala-grafico">

                    <?php for ($i = 4; $i >= 0; $i--): ?>

                        <span>
                            R$ <?php echo formatar_valor_dashboard($limite_grafico * $i / 4); ?>
                        </span>

                    <?php endfor; ?>

                </div>

                <!-- Barras de receitas e despesas de cada mês -->

                <div class="barras-grafico">

                    <?php

                    $abreviacoes_meses = [
                        "01" => "Jan", "02" => "Fev", "03" => "Mar",
                        "04" => "Abr", "05" => "Mai", "06" => "Jun",
                        "07" => "Jul", "08" => "Ago", "09" => "Set",
                        "10" => "Out", "11" => "Nov", "12" => "Dez"
                    ];

                    foreach ($receitas_mensais as $chave_mes => $receita):

                        $despesa = $despesas_mensais[$chave_mes];

                        $altura_receita = ($receita / $limite_grafico) * 100;
                        $altura_despesa = ($despesa / $limite_grafico) * 100;

                        $numero_mes = substr($chave_mes, 5, 2);
                        $ano_mes = substr($chave_mes, 2, 2);

                        $nome_mes = $abreviacoes_meses[$numero_mes] . "/" . $ano_mes;

                    ?>

                        <div class="grupo-mes">

                            <div class="colunas">

                                <div
                                    class="barra barra-receita"
                                    style="height: <?php echo $altura_receita; ?>%;"
                                    title="Receitas: R$ <?php echo formatar_valor_dashboard($receita); ?>"
                                ></div>

                                <div
                                    class="barra barra-despesa"
                                    style="height: <?php echo $altura_despesa; ?>%;"
                                    title="Despesas: R$ <?php echo formatar_valor_dashboard($despesa); ?>"
                                ></div>

                            </div>

                            <div class="mes-barra">
                                <?php echo escapar_dashboard($nome_mes); ?>
                            </div>

                        </div>

                    <?php endforeach; ?>

                </div>

            </div>

            <div class="legenda">

                <span>
                    <span class="ponto-entrada"></span>
                    Receitas
                </span>

                <span>
                    <span class="ponto-saida"></span>
                    Despesas
                </span>

            </div>

        </section>

        <!-- ACOES -->

        <div class="acoes">

            <a
                class="botao botao-verde"
                href="transacoes.php?pagina=lancamentos"
            >
                Adicionar lançamento
            </a>

            <a
                class="botao botao-azul"
                href="transacoes.php?pagina=relatorios"
            >
                Ver relatórios
            </a>

        </div>

        <!-- MOVIMENTAÇÕES RECENTES -->

        <section class="recentes">

            <h2>
                Últimas movimentações
            </h2>

            <div class="tabela-container">

                <table>

                    <thead>

                        <tr>
                            <th>Descrição</th>
                            <th>Tipo</th>
                            <th>Data</th>
                            <th>Valor</th>
                        </tr>

                    </thead>

                    <tbody>

                        <?php if (empty($transacoes_recentes)): ?>

                            <tr>
                                <td colspan="4" class="vazio">
                                    Você ainda não possui lançamentos cadastrados.
                                </td>
                            </tr>

                        <?php else: ?>

                            <?php foreach ($transacoes_recentes as $transacao): ?>

                                <tr>

                                    <td>
                                        <?php echo escapar_dashboard($transacao["descricao"]); ?>
                                    </td>

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
                                        <?php
                                        echo date(
                                            "d/m/Y",
                                            strtotime($transacao["data"])
                                        );
                                        ?>
                                    </td>

                                    <td class="<?php
                                        echo $transacao["tipo"] === "receita"
                                            ? "verde"
                                            : "vermelho";
                                    ?>">

                                        R$
                                        <?php
                                        echo formatar_valor_dashboard(
                                            $transacao["valor"]
                                        );
                                        ?>

                                    </td>

                                </tr>

                            <?php endforeach; ?>

                        <?php endif; ?>

                    </tbody>

                </table>

            </div>

        </section>

    </main>

    <footer>
        Meu Controle Financeiro &copy; <?php echo date("Y"); ?>
    </footer>

</body>

</html>