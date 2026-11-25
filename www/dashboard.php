<?php
session_start();

if (!isset($_SESSION["id_usuario"])) {
    header("Location: index.php?pagina=login");
    exit;
}

require_once "conexao.php";

$id_usuario = (int) $_SESSION["id_usuario"];
$nome_usuario = $_SESSION["nome_usuario"] ?? "Usuário";

function formatar_valor_dashboard($valor) {
    return number_format((float) $valor, 2, ",", ".");
}

function escapar_dashboard($texto) {
    return htmlspecialchars((string) $texto, ENT_QUOTES, "UTF-8");
}

/* TOTAL DE RECEITAS */
$sql = "SELECT COALESCE(SUM(valor), 0) AS total FROM transacoes WHERE usuario_id = ? AND tipo = 'receita'";
$stmt = mysqli_prepare($conexao, $sql);
mysqli_stmt_bind_param($stmt, "i", $id_usuario);
mysqli_stmt_execute($stmt);
$resultado = mysqli_stmt_get_result($stmt);
$dados = mysqli_fetch_assoc($resultado);
$total_receitas = (float) $dados["total"];
mysqli_stmt_close($stmt);

/* TOTAL DE DESPESAS */
$sql = "SELECT COALESCE(SUM(valor), 0) AS total FROM transacoes WHERE usuario_id = ? AND tipo = 'despesa'";
$stmt = mysqli_prepare($conexao, $sql);
mysqli_stmt_bind_param($stmt, "i", $id_usuario);
mysqli_stmt_execute($stmt);
$resultado = mysqli_stmt_get_result($stmt);
$dados = mysqli_fetch_assoc($resultado);
$total_despesas = (float) $dados["total"];
mysqli_stmt_close($stmt);

/* SALDO ATUAL */
$saldo_atual = $total_receitas - $total_despesas;

/* EVOLUÇÃO DOS ÚLTIMOS SETE MESES */
$meses = [];
$receitas_mensais = [];
$despesas_mensais = [];

for ($i = 6; $i >= 0; $i--) {
    $mes = date("Y-m", strtotime("first day of this month -$i months"));
    $meses[] = date("m/Y", strtotime($mes . "-01"));
    $receitas_mensais[$mes] = 0;
    $despesas_mensais[$mes] = 0;
}

/* CONSULTAR RECEITAS E DESPESAS POR MÊS */
$sql = "SELECT DATE_FORMAT(data, '%Y-%m') AS mes, tipo, SUM(valor) AS total
        FROM transacoes
        WHERE usuario_id = ?
        AND data >= DATE_FORMAT(DATE_SUB(CURDATE(), INTERVAL 6 MONTH), '%Y-%m-01')
        AND data <= CURDATE()
        GROUP BY DATE_FORMAT(data, '%Y-%m'), tipo
        ORDER BY mes";

$stmt = mysqli_prepare($conexao, $sql);
mysqli_stmt_bind_param($stmt, "i", $id_usuario);
mysqli_stmt_execute($stmt);
$resultado = mysqli_stmt_get_result($stmt);

while ($linha = mysqli_fetch_assoc($resultado)) {
    $mes = $linha["mes"];

    if ($linha["tipo"] === "receita" && isset($receitas_mensais[$mes])) {
        $receitas_mensais[$mes] = (float) $linha["total"];
    }

    if ($linha["tipo"] === "despesa" && isset($despesas_mensais[$mes])) {
        $despesas_mensais[$mes] = (float) $linha["total"];
    }
}
mysqli_stmt_close($stmt);

/* ESCALA DO GRÁFICO */
$valores_receitas = array_values($receitas_mensais);
$valores_despesas = array_values($despesas_mensais);
$maior_valor = max(1, max($valores_receitas), max($valores_despesas));
$limite_grafico = ceil($maior_valor / 4) * 4;

/* CINCO MOVIMENTAÇÕES MAIS RECENTES */
$transacoes_recentes = [];

$sql = "SELECT descricao, valor, tipo, data FROM transacoes WHERE usuario_id = ? ORDER BY data DESC, id DESC LIMIT 5";
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
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link href="https://fonts.googleapis.com/css2?family=Lato:wght@400;700&family=Playfair+Display:wght@400;500;600;700&display=swap" rel="stylesheet">
<title>Meu Controle Financeiro - Dashboard</title>
<style>
* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

body {
    background: #1F0B0A;
    color: #F1E8E5;
    font-family: 'Lato', Arial, sans-serif;
}

/* MENU */
.navbar {
    background: #311B1D;
    color: #F1E8E5;
    padding: 20px 45px;
    border-bottom: 1px solid #563C3B;
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 20px;
}

.logo {
    font-family: 'Playfair Display', Georgia, serif;
    font-size: 22px;
    font-weight: 500;
}

.menu {
    display: flex;
    align-items: center;
    gap: 14px;
    flex-wrap: wrap;
}

.menu a {
    color: #D8C6C1;
    text-decoration: none;
    font-size: 13px;
    transition: 0.3s;
}

.menu a:hover,
.menu .ativo {
    color: #FFFFFF;
}

.menu .ativo {
    font-weight: bold;
}

/* CONTEÚDO */
.container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 35px 30px;
}

.titulo {
    margin-bottom: 28px;
}

.titulo h1 {
    font-family: 'Playfair Display', Georgia, serif;
    font-size: 32px;
    font-weight: 500;
    color: #E8D9D5;
    margin-bottom: 8px;
}

.titulo p {
    color: #B9A6A3;
    font-size: 14px;
}

/* CARDS */
.cards {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 18px;
    margin-bottom: 25px;
}

.card {
    background: #311B1D;
    border: 1px solid #563C3B;
    border-radius: 5px;
    padding: 24px;
}

.card.saldo {
    border-top: 3px solid #8F777B;
}

.card.receitas {
    border-top: 3px solid #4A4E57;
}

.card.despesas {
    border-top: 3px solid #76504D;
}

.card p {
    color: #B9A6A3;
    font-size: 13px;
    margin-bottom: 12px;
}

.valor {
    font-family: 'Playfair Display', Georgia, serif;
    font-size: 27px;
    font-weight: 500;
    overflow-wrap: anywhere;
}

.verde {
    color: #B5C5B4;
}

.azul {
    color: #A4AEC0;
}

.vermelho {
    color: #C58F91;
}

/* GRÁFICO */
.grafico {
    background: #311B1D;
    border: 1px solid #563C3B;
    border-radius: 5px;
    padding: 25px;
    margin-bottom: 25px;
}

.grafico h2,
.recentes h2 {
    font-family: 'Playfair Display', Georgia, serif;
    font-size: 21px;
    font-weight: 500;
    color: #E8D9D5;
    margin-bottom: 22px;
}

.area-grafico {
    display: flex;
    height: 310px;
    padding: 15px 12px 0 0;
    border: 1px solid #563C3B;
    border-radius: 4px;
    background: #281315;
}

.escala-grafico {
    width: 75px;
    flex-shrink: 0;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
    align-items: flex-end;
    padding: 0 10px 35px 0;
    color: #B9A6A3;
    font-size: 11px;
}

.barras-grafico {
    flex: 1;
    min-width: 0;
    display: grid;
    grid-template-columns: repeat(7, minmax(0, 1fr));
    gap: 8px;
    border-bottom: 1px solid #76504D;
    background: repeating-linear-gradient(
        to top,
        transparent 0,
        transparent calc(25% - 1px),
        #563C3B calc(25% - 1px),
        #563C3B 25%
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
    border-radius: 3px 3px 0 0;
}

.barra-receita {
    background: #8F9BAF;
}

.barra-despesa {
    background: #A36E70;
}

.mes-barra {
    height: 35px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #B9A6A3;
    font-size: 11px;
    white-space: nowrap;
}

.legenda {
    display: flex;
    justify-content: center;
    gap: 25px;
    margin-top: 20px;
    font-size: 12px;
    color: #B9A6A3;
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
    background: #8F9BAF;
}

.ponto-saida {
    background: #A36E70;
}

/* BOTÕES */
.acoes {
    display: flex;
    gap: 12px;
    margin-top: 20px;
    margin-bottom: 25px;
    flex-wrap: wrap;
}

.botao {
    display: inline-block;
    padding: 12px 19px;
    color: #F1E8E5;
    text-decoration: none;
    border-radius: 4px;
    font-size: 13px;
    font-weight: bold;
    transition: 0.3s;
}

.botao-verde {
    background: #563C3B;
    border: 1px solid #76504D;
}

.botao-azul {
    background: #4A4E57;
    border: 1px solid #626875;
}

.botao:hover {
    opacity: 0.85;
    transform: translateY(-1px);
}

/* MOVIMENTAÇÕES */
.recentes {
    background: #311B1D;
    border: 1px solid #563C3B;
    border-radius: 5px;
    padding: 25px;
}

.tabela-container {
    overflow-x: auto;
}

table {
    width: 100%;
    border-collapse: collapse;
    min-width: 500px;
}

th, td {
    text-align: left;
    padding: 14px 10px;
    border-bottom: 1px solid #563C3B;
    font-size: 13px;
}

th {
    color: #B9A6A3;
    background: #281315;
    font-weight: bold;
}

td {
    color: #E8D9D5;
}

.etiqueta {
    display: inline-block;
    padding: 5px 9px;
    border-radius: 3px;
    font-size: 11px;
    font-weight: bold;
}

.etiqueta-receita {
    background: #424B48;
    color: #C4D5C7;
}

.etiqueta-despesa {
    background: #563638;
    color: #E0B2B2;
}

.vazio {
    text-align: center;
    padding: 25px;
    color: #B9A6A3;
}

/* RODAPÉ */
footer {
    text-align: center;
    color: #8F777B;
    padding: 25px;
    font-size: 12px;
    border-top: 1px solid #563C3B;
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
    .navbar {
        padding: 20px;
    }

    .logo {
        font-size: 20px;
    }

    .container {
        padding: 20px 12px;
    }

    .titulo h1 {
        font-size: 27px;
    }

    .acoes {
        flex-direction: column;
    }

    .botao {
        text-align: center;
    }

    .grafico,
    .recentes {
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

<header class="navbar">
    <div class="logo">Meu Controle Financeiro</div>
    <nav class="menu">
        <a class="ativo" href="dashboard.php">Dashboard</a>
        <span>|</span>
        <a href="transacoes.php?pagina=lancamentos">Lançamentos</a>
        <span>|</span>
        <a href="transacoes.php?pagina=relatorios">Relatórios</a>
        <span>|</span>
        <a href="perfil.php">Meu Perfil</a>
        <span>|</span>
        <a href="logout.php">Sair</a>
    </nav>
</header>

<main class="container">

    <section class="titulo">
        <h1>Olá, <?php echo escapar_dashboard($nome_usuario); ?>!</h1>
        <p>Confira um resumo da sua situação financeira.</p>
    </section>

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

    <section class="grafico">
        <h2>Evolução Mensal — Últimos 7 Meses</h2>

        <div class="area-grafico">
            <div class="escala-grafico">
                <?php for ($i = 4; $i >= 0; $i--): ?>
                    <span>R$ <?php echo formatar_valor_dashboard($limite_grafico * $i / 4); ?></span>
                <?php endfor; ?>
            </div>

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
                            <div class="barra barra-receita"
                                style="height: <?php echo $altura_receita; ?>%;"
                                title="Receitas: R$ <?php echo formatar_valor_dashboard($receita); ?>">
                            </div>
                            <div class="barra barra-despesa"
                                style="height: <?php echo $altura_despesa; ?>%;"
                                title="Despesas: R$ <?php echo formatar_valor_dashboard($despesa); ?>">
                            </div>
                        </div>
                        <div class="mes-barra"><?php echo escapar_dashboard($nome_mes); ?></div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

        <div class="legenda">
            <span><span class="ponto-entrada"></span>Receitas</span>
            <span><span class="ponto-saida"></span>Despesas</span>
        </div>
    </section>

    <div class="acoes">
        <a class="botao botao-verde" href="transacoes.php?pagina=lancamentos">Adicionar lançamento</a>
        <a class="botao botao-azul" href="transacoes.php?pagina=relatorios">Ver relatórios</a>
    </div>

    <section class="recentes">
        <h2>Últimas movimentações</h2>
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
                            <td colspan="4" class="vazio">Você ainda não possui lançamentos cadastrados.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($transacoes_recentes as $transacao): ?>
                            <tr>
                                <td><?php echo escapar_dashboard($transacao["descricao"]); ?></td>
                                <td>
                                    <?php if ($transacao["tipo"] === "receita"): ?>
                                        <span class="etiqueta etiqueta-receita">Receita</span>
                                    <?php else: ?>
                                        <span class="etiqueta etiqueta-despesa">Despesa</span>
                                    <?php endif; ?>
                                </td>
                                <td><?php echo date("d/m/Y", strtotime($transacao["data"])); ?></td>
                                <td class="<?php echo $transacao["tipo"] === "receita" ? "verde" : "vermelho"; ?>">
                                    R$ <?php echo formatar_valor_dashboard($transacao["valor"]); ?>
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
