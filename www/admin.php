<?php
$pagina = isset($_GET['pagina']) ? $_GET['pagina'] : 'admin';
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Meu Controle Financeiro - Administração</title>

    <style>

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: Arial, Helvetica, sans-serif;
        }

        body {
            background: #f5f7fb;
            color: #111827;
        }

        .container {
            max-width: 1100px;
            margin: 0 auto;
            padding: 30px 20px;
        }

        .navbar {
            background: #1e293b;
            color: white;
            padding: 13px 16px;
            border-radius: 5px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 16px;
        }

        .logo {
            font-size: 16px;
            font-weight: bold;
        }

        .menu {
            display: flex;
            align-items: center;
            gap: 10px;
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

        .menu .sair {
            color: #f87171;
        }

        .estatisticas {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 16px;
            margin-bottom: 30px;
        }

        .estatistica {
            background: #f8fafc;
            border: 1px solid #dbe3ec;
            border-left: 4px solid #1e293b;
            border-radius: 6px;
            padding: 18px;
        }

        .estatistica p {
            color: #64748b;
            font-size: 13px;
            margin-bottom: 6px;
        }

        .estatistica strong {
            font-size: 20px;
        }

        .usuarios {
            background: white;
            border: 1px solid #dbe3ec;
            border-radius: 6px;
            padding: 15px;
        }

        .usuarios h2 {
            font-size: 15px;
            margin-bottom: 18px;
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
            background: #eef2f7;
            color: #334155;
            text-align: left;
            padding: 10px;
        }

        td {
            padding: 10px;
            border-bottom: 1px solid #dbe3ec;
            vertical-align: middle;
        }

        .perfil {
            display: inline-block;
            padding: 4px 7px;
            border-radius: 4px;
            color: white;
            font-size: 11px;
            font-weight: bold;
        }

        .comum {
            background: #64748b;
        }

        .admin {
            background: #a855f7;
        }

        .status {
            display: inline-block;
            padding: 4px 8px;
            border-radius: 4px;
            color: white;
            font-size: 11px;
            font-weight: bold;
        }

        .ativo {
            background: #10b981;
        }

        .bloqueado {
            background: #ef4444;
        }

        .acoes a {
            color: #2563eb;
            text-decoration: none;
            font-size: 12px;
            margin-right: 5px;
        }

        .acoes a:hover {
            text-decoration: underline;
        }

        .soberano {
            color: #94a3b8;
        }

        @media (max-width: 700px) {

            .navbar {
                flex-direction: column;
                gap: 12px;
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
                ⚙ Painel de Administração
            </div>

            <div class="menu">

                <a href="#">
                    Estatísticas
                </a>

                <span>|</span>

                <a href="#" class="ativo">
                    Usuários
                </a>

                <span>|</span>

                <a href="#">
                    Categorias
                </a>

                <span>|</span>

                <a href="index.php" class="sair">
                    Sair
                </a>

            </div>

        </nav>


        <section class="estatisticas">

            <div class="estatistica">

                <p>
                    Total de Usuários
                </p>

                <strong>
                    1.248 cadastros
                </strong>

            </div>


            <div class="estatistica">

                <p>
                    Registros Totais
                </p>

                <strong>
                    45.210 transações
                </strong>

            </div>

        </section>


        <section class="usuarios">

            <h2>
                Gerenciamento de Acessos
            </h2>

            <div class="tabela-container">

                <table>

                    <thead>

                        <tr>
                            <th>ID</th>
                            <th>Nome do Usuário</th>
                            <th>E-mail</th>
                            <th>Perfil</th>
                            <th>Status</th>
                            <th>Ações Permitidas</th>
                        </tr>

                    </thead>

                    <tbody>

                        <tr>

                            <td>001</td>

                            <td>
                                Carlos<br>
                                Eduardo<br>
                                Souza
                            </td>

                            <td>
                                carlos@email.com
                            </td>

                            <td>
                                <span class="perfil comum">
                                    Comum
                                </span>
                            </td>

                            <td>
                                <span class="status ativo">
                                    Ativo
                                </span>
                            </td>

                            <td class="acoes">
                                <a href="#">
                                    [Bloquear]
                                </a>
                                <a href="#">
                                    [Excluir]
                                </a>
                            </td>

                        </tr>


                        <tr>

                            <td>002</td>

                            <td>
                                Amanda<br>
                                Ribeiro
                            </td>

                            <td>
                                amanda@financeiro.com
                            </td>

                            <td>
                                <span class="perfil comum">
                                    Comum
                                </span>
                            </td>

                            <td>
                                <span class="status bloqueado">
                                    Bloqueado
                                </span>
                            </td>

                            <td class="acoes">
                                <a href="#">
                                    [Desbloquear]
                                </a>
                                <a href="#">
                                    [Excluir]
                                </a>
                            </td>

                        </tr>


                        <tr>

                            <td>003</td>

                            <td>
                                Administrador<br>
                                Master
                            </td>

                            <td>
                                admin@meucontrole.com
                            </td>

                            <td>
                                <span class="perfil admin">
                                    Admin
                                </span>
                            </td>

                            <td>
                                <span class="status ativo">
                                    Ativo
                                </span>
                            </td>

                            <td>
                                <span class="soberano">
                                    Soberano
                                </span>
                            </td>

                        </tr>

                    </tbody>

                </table>

            </div>

        </section>

    </main>

</body>

</html>
