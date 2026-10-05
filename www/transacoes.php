<?php

session_start();

if (!isset($_SESSION["id_usuario"])) {

    header("Location: index.php?pagina=login");
    exit;

}

$pagina = isset($_GET['pagina']) ? $_GET['pagina'] : 'lancamentos';

?>

<!DOCTYPE html>
<html lang="pt-br">

<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Meu Controle Financeiro</title>

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

        .navbar {
            background: #111827;
            color: white;
            padding: 15px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-radius: 5px;
            margin-bottom: 18px;
        }

        .logo {
            font-size: 16px;
            font-weight: bold;
        }

        .menu {
            display: flex;
            gap: 12px;
            align-items: center;
        }

        .menu a {
            color: white;
            text-decoration: none;
            font-size: 13px;
        }

        .menu a:hover,
        .menu .ativo {
            font-weight: bold;
        }

        .container {
            max-width: 1100px;
            margin: 0 auto;
            padding: 30px 20px;
        }

        .box {
            background: white;
            border: 1px solid #dbe3ec;
            border-radius: 6px;
            padding: 16px;
            margin-bottom: 18px;
        }

        .box h2 {
            font-size: 15px;
            margin-bottom: 12px;
        }

        .formulario {
            background: #f8fafc;
            border: 1px solid #dbe3ec;
            border-radius: 6px;
            padding: 16px;
        }

        .formulario h2 {
            margin-bottom: 10px;
        }

        .campos {
            display: grid;
            grid-template-columns: 2fr 1fr 1fr 1fr;
            gap: 16px;
            align-items: start;
        }

        input,
        select {
            width: 100%;
            height: 32px;
            padding: 7px 10px;
            border: 1px solid #cbd5e1;
            border-radius: 4px;
            background: white;
            color: #1f2937;
        }

        .botao-container {
            display: flex;
            justify-content: flex-end;
            margin-top: 15px;
        }

        button {
            border: none;
            background: #10b981;
            color: white;
            font-weight: bold;
            padding: 10px 16px;
            border-radius: 4px;
            cursor: pointer;
        }

        button:hover {
            background: #059669;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 13px;
        }

        th {
            background: #eef2f7;
            color: #334155;
            text-align: left;
            padding: 10px;
        }

        td {
            padding: 10px;
            border-bottom: 1px solid #dbe3ec;
        }

        .entrada {
            color: #10b981;
            font-weight: bold;
        }

        .saida {
            color: #ef4444;
            font-weight: bold;
        }

        .acoes a {
            text-decoration: none;
            margin-right: 5px;
        }

        .filtro {
            background: #f1f5f9;
            border: 1px solid #dbe3ec;
            border-radius: 6px;
            padding: 10px;
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 15px;
        }

        .filtro label {
            font-weight: bold;
            font-size: 13px;
        }

        .filtro select {
            width: 150px;
        }

        .botao-filtrar {
            background: #1e293b;
            padding: 7px 14px;
        }

        .relatorios {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 16px;
        }

        .grafico {
            height: 220px;
            border: 1px dashed #cbd5e1;
            border-radius: 5px;
            display: flex;
            justify-content: center;
            align-items: center;
            color: #94a3b8;
            font-style: italic;
            background: #fafbfc;
            text-align: center;
        }

        .resumo p {
            display: flex;
            justify-content: space-between;
            padding: 10px 0;
            border-bottom: 1px solid #e2e8f0;
            font-size: 14px;
        }

        .resumo p:last-child {
            border-bottom: none;
        }

        .total-entrada {
            color: #10b981;
            font-weight: bold;
        }

        .total-saida {
            color: #ef4444;
            font-weight: bold;
        }

        .total-saldo {
            color: #3b82f6;
            font-weight: bold;
        }

        @media (max-width: 800px) {

            .navbar {
                flex-direction: column;
                gap: 12px;
            }

            .menu {
                flex-wrap: wrap;
                justify-content: center;
            }

            .campos {
                grid-template-columns: 1fr 1fr;
            }

            .relatorios {
                grid-template-columns: 1fr;
            }

        }

        @media (max-width: 550px) {

            .container {
                padding: 20px 12px;
            }

            .campos {
                grid-template-columns: 1fr;
            }

            .filtro {
                flex-direction: column;
                align-items: stretch;
            }

            .filtro select {
                width: 100%;
            }

            .filtro button {
                width: 100%;
            }

            .box {
                overflow-x: auto;
            }

            table {
                min-width: 650px;
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

                <a class="ativo" href="?pagina=lancamentos">
                    Lançamentos
                </a>

                <span>|</span>

                <a class="<?php echo ($pagina == 'relatorios') ? 'ativo' : ''; ?>" href="?pagina=relatorios">
                    Relatórios
                </a>

            </div>

        </nav>


        <?php if ($pagina == 'lancamentos') { ?>

            <section class="box formulario">

                <h2>
                    Adicionar Novo Registro
                </h2>

                <form>

                    <div class="campos">

                        <input
                            type="text"
                            placeholder="Descrição (Ex: Salário)"
                        >

                        <input
                            type="text"
                            placeholder="Valor R$"
                        >

                        <input
                            type="text"
                            placeholder="Data (DD/MM/AAAA)"
                        >

                        <select>

                            <option selected disabled>
                                Categoria ▼
                            </option>

                            <option>Trabalho</option>
                            <option>Alimentação</option>
                            <option>Moradia</option>
                            <option>Transporte</option>
                            <option>Lazer</option>
                            <option>Outros</option>

                        </select>

                    </div>

                    <div class="botao-container">

                        <button type="button">
                            Salvar Lançamento
                        </button>

                    </div>

                </form>

            </section>


            <section class="box">

                <h2>
                    Registros Cadastrados
                </h2>

                <table>

                    <thead>

                        <tr>
                            <th>Data</th>
                            <th>Descrição</th>
                            <th>Categoria</th>
                            <th>Valor</th>
                            <th>Ações</th>
                        </tr>

                    </thead>

                    <tbody>

                        <tr>

                            <td>05/06/2026</td>

                            <td>Salário Mensal</td>

                            <td>Trabalho</td>

                            <td class="entrada">
                                + R$ 5.000,00
                            </td>

                            <td class="acoes">
                                <a href="#">✏️</a>
                                <a href="#">🗑️</a>
                            </td>

                        </tr>

                        <tr>

                            <td>10/06/2026</td>

                            <td>Supermercado Central</td>

                            <td>Alimentação</td>

                            <td class="saida">
                                - R$ 1.500,00
                            </td>

                            <td class="acoes">
                                <a href="#">✏️</a>
                                <a href="#">🗑️</a>
                            </td>

                        </tr>

                    </tbody>

                </table>

            </section>


        <?php } else { ?>


            <section class="box">

                <div class="filtro">

                    <label>
                        Filtro por Período:
                    </label>

                    <select>

                        <option>
                            Mês Atual
                        </option>

                        <option>
                            Últimos 3 Meses
                        </option>

                        <option>
                            Últimos 6 Meses
                        </option>

                        <option>
                            Este Ano
                        </option>

                    </select>

                    <button
                        type="button"
                        class="botao-filtrar"
                    >
                        Filtrar Dados
                    </button>

                </div>


                <div class="relatorios">

                    <div class="box">

                        <h2>
                            Gastos por Categoria
                        </h2>

                        <div class="grafico">
                            [ Gráfico de Pizza / Distribuição ]
                        </div>

                    </div>


                    <div class="box resumo">

                        <h2>
                            Resumo Financeiro
                        </h2>

                        <p>
                            <span>Entradas Totais:</span>
                            <span class="total-entrada">
                                R$ 5.000,00
                            </span>
                        </p>

                        <p>
                            <span>Saídas Totais:</span>
                            <span class="total-saida">
                                R$ 1.500,00
                            </span>
                        </p>

                        <p>
                            <span>Balanço Líquido:</span>
                            <span class="total-saldo">
                                R$ 3.500,00
                            </span>
                        </p>

                    </div>

                </div>

            </section>


        <?php } ?>

    </main>

</body>

</html>
