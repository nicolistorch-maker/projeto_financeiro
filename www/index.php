<?php

session_start();

$pagina = isset($_GET['pagina']) ? $_GET['pagina'] : 'home';

$logado = isset($_SESSION["id_usuario"]);
$administrador = $logado && isset($_SESSION["perfil_usuario"]) && $_SESSION["perfil_usuario"] == "admin";

?>

<!DOCTYPE html>
<html lang="pt-br">

<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link href="https://fonts.googleapis.com/css2?family=Lato:wght@400;700&family=Playfair+Display:wght@400;500;600;700&display=swap" rel="stylesheet">

    <title>Meu Controle Financeiro</title>

    <style>

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            min-height: 100vh;
            background: #1F0B0A;
            color: #F1E8E5;
            font-family: 'Lato', Arial, sans-serif;
        }

        /* MENU */

        .navbar {
            width: 100%;
            min-height: 85px;
            background: #311B1D;
            border-bottom: 1px solid #563C3B;
            padding: 18px 6%;
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 20px;
        }

        .logo {
            color: #E8D9D5;
            font-family: 'Playfair Display', Georgia, serif;
            font-size: 23px;
        }

        .menu {
            display: flex;
            align-items: center;
            gap: 12px;
            flex-wrap: wrap;
        }

        .menu a {
            color: #E8D9D5;
            text-decoration: none;
            font-size: 14px;
            padding: 10px 18px;
            border-radius: 4px;
            transition: 0.3s;
        }

        .menu .login {
            border: 1px solid #76797C;
        }

        .menu .login:hover {
            background: #4A4E57;
        }

        .menu .cadastro {
            background: #563C3B;
        }

        .menu .cadastro:hover {
            background: #76504D;
        }

        /* CONTEUDO */

        .container {
            min-height: calc(100vh - 85px);
            width: 100%;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 50px 25px;
        }

        /* PAGINA INICIAL */

        .hero {
            width: 100%;
            max-width: 900px;
            background: #311B1D;
            border: 1px solid #563C3B;
            border-radius: 6px;
            padding: 75px 45px;
            text-align: center;
            box-shadow: 0 15px 40px rgba(0, 0, 0, 0.25);
        }

        .destaque {
            display: inline-block;
            color: #B99B98;
            font-size: 11px;
            letter-spacing: 3px;
            text-transform: uppercase;
            margin-bottom: 25px;
        }

        .hero h1 {
            font-family: 'Playfair Display', Georgia, serif;
            font-size: 46px;
            font-weight: 500;
            line-height: 1.2;
            color: #E8D9D5;
            margin-bottom: 22px;
        }

        .hero p {
            max-width: 570px;
            margin: 0 auto 35px;
            color: #B9A6A3;
            font-size: 16px;
            line-height: 1.8;
        }

        .botoes-home {
            display: flex;
            justify-content: center;
            align-items: center;
            flex-wrap: wrap;
            gap: 14px;
        }

        .btn {
            display: inline-block;
            padding: 13px 23px;
            border-radius: 4px;
            text-decoration: none;
            color: #F1E8E5;
            font-size: 14px;
            transition: 0.3s;
        }

        .btn-principal {
            background: #563C3B;
            border: 1px solid #76504D;
        }

        .btn-principal:hover {
            background: #76504D;
        }

        .btn-secundario {
            background: #4A4E57;
            border: 1px solid #626875;
        }

        .btn-secundario:hover {
            background: #626875;
        }

        .btn-terciario {
            background: transparent;
            border: 1px solid #76797C;
        }

        .btn-terciario:hover {
            background: #4A4E57;
        }

        /* FORMULARIOS */

        .card {
            width: 100%;
            max-width: 420px;
            background: #311B1D;
            border: 1px solid #563C3B;
            border-radius: 6px;
            padding: 35px;
            box-shadow: 0 15px 40px rgba(0, 0, 0, 0.25);
        }

        .card h2 {
            font-family: 'Playfair Display', Georgia, serif;
            font-size: 28px;
            font-weight: 500;
            color: #E8D9D5;
            text-align: center;
            margin-bottom: 25px;
        }

        label {
            display: block;
            color: #D7C5C1;
            font-size: 13px;
            font-weight: bold;
            margin-bottom: 8px;
        }

        input {
            width: 100%;
            padding: 12px;
            margin-bottom: 19px;
            background: #1F0B0A;
            border: 1px solid #563C3B;
            border-radius: 4px;
            color: #F1E8E5;
            font-family: 'Lato', Arial, sans-serif;
            outline: none;
        }

        input:focus {
            border-color: #A17C78;
        }

        input::placeholder {
            color: #8C7774;
        }

        button {
            width: 100%;
            padding: 13px;
            background: #563C3B;
            border: 1px solid #76504D;
            border-radius: 4px;
            color: #F1E8E5;
            font-size: 14px;
            font-weight: bold;
            cursor: pointer;
            transition: 0.3s;
        }

        button:hover {
            background: #76504D;
        }

        .link {
            text-align: center;
            margin-top: 20px;
            color: #A99592;
            font-size: 13px;
            line-height: 2;
        }

        .link a {
            color: #C5A6A1;
            text-decoration: none;
        }

        .link a:hover {
            color: white;
            text-decoration: underline;
        }

        .texto {
            text-align: center;
            color: #B9A6A3;
            font-size: 14px;
            line-height: 1.6;
            margin-bottom: 20px;
        }

        /* RODAPE */

        .rodape {
            text-align: center;
            color: #806A67;
            font-size: 12px;
            padding: 20px;
        }

        /* RESPONSIVIDADE */

        @media(max-width: 650px) {

            .navbar {
                flex-direction: column;
                padding: 20px;
                text-align: center;
            }

            .menu {
                justify-content: center;
            }

            .container {
                padding: 30px 15px;
            }

            .hero {
                padding: 50px 22px;
            }

            .hero h1 {
                font-size: 34px;
            }

            .hero p {
                font-size: 14px;
            }

            .botoes-home {
                flex-direction: column;
            }

            .btn {
                width: 100%;
            }

            .card {
                padding: 25px;
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

            <?php if (!$logado) { ?>

                <a class="login" href="?pagina=login">
                    Login
                </a>

                <a class="cadastro" href="?pagina=cadastro">
                    Criar Conta
                </a>

            <?php } else { ?>

                <a href="dashboard.php">
                    Dashboard
                </a>

                <a href="perfil.php">
                    Meu Perfil
                </a>

                <?php if ($administrador) { ?>

                    <a href="admin.php">
                        Administração
                    </a>

                <?php } ?>

                <a href="logout.php">
                    Sair
                </a>

            <?php } ?>

        </nav>

    </header>


    <main class="container">

        <?php

        /* HOME */

        if ($pagina == "home") {

        ?>

            <section class="hero">

                <span class="destaque">
                    Planejamento e organização financeira
                </span>

                <h1>
                    Organize sua vida financeira com mais tranquilidade.
                </h1>

                <p>
                    Acompanhe suas receitas, controle suas despesas
                    e tenha uma visão mais clara do seu dinheiro.
                    Tudo em um só lugar.
                </p>

                <div class="botoes-home">

                    <?php if (!$logado) { ?>

                        <a class="btn btn-principal" href="?pagina=cadastro">
                            Começar Agora
                        </a>

                        <a class="btn btn-secundario" href="?pagina=login">
                            Entrar no Sistema
                        </a>

                    <?php } else { ?>

                        <a class="btn btn-principal" href="dashboard.php">
                            Acessar Dashboard
                        </a>

                        <a class="btn btn-secundario" href="transacoes.php">
                            Meus Lançamentos
                        </a>

                        <a class="btn btn-terciario" href="perfil.php">
                            Meu Perfil
                        </a>

                        <?php if ($administrador) { ?>

                            <a class="btn btn-terciario" href="admin.php">
                                Área Administrativa
                            </a>

                        <?php } ?>

                    <?php } ?>

                </div>

            </section>

        <?php

        }


        /* LOGIN */

        elseif ($pagina == "login") {

        ?>

            <section class="card">

                <h2>
                    Entrar no Sistema
                </h2>

                <form action="login.php" method="POST">

                    <label for="email">
                        E-mail
                    </label>

                    <input
                        type="email"
                        id="email"
                        name="email"
                        placeholder="seu@email.com"
                        required
                    >

                    <label for="senha">
                        Senha
                    </label>

                    <input
                        type="password"
                        id="senha"
                        name="senha"
                        placeholder="Digite sua senha"
                        required
                    >

                    <button type="submit">
                        Entrar
                    </button>

                </form>

                <div class="link">

                    <a href="?pagina=recuperar">
                        Esqueceu a senha?
                    </a>

                    |

                    <a href="?pagina=cadastro">
                        Criar conta
                    </a>

                </div>

            </section>

        <?php

        }


        /* CADASTRO */

        elseif ($pagina == "cadastro") {

        ?>

            <section class="card">

                <h2>
                    Criar Nova Conta
                </h2>

                <form action="cadastrar.php" method="POST">

                    <label for="nome">
                        Nome Completo
                    </label>

                    <input
                        type="text"
                        id="nome"
                        name="nome"
                        placeholder="Ex: João Silva"
                        required
                    >

                    <label for="email">
                        E-mail
                    </label>

                    <input
                        type="email"
                        id="email"
                        name="email"
                        placeholder="seu@email.com"
                        required
                    >

                    <label for="senha">
                        Senha
                    </label>

                    <input
                        type="password"
                        id="senha"
                        name="senha"
                        placeholder="Digite sua senha"
                        minlength="6"
                        required
                    >

                    <button type="submit">
                        Criar Minha Conta
                    </button>

                </form>

                <div class="link">

                    <a href="?pagina=login">
                        Já tem conta? Faça login
                    </a>

                </div>

            </section>

        <?php

        }


        /* RECUPERACAO */

        elseif ($pagina == "recuperar") {

        ?>

            <section class="card">

                <h2>
                    Recuperar Senha
                </h2>

                <p class="texto">
                    Informe seu e-mail para iniciar a recuperação da senha.
                </p>

                <form>

                    <label for="email">
                        E-mail
                    </label>

                    <input
                        type="email"
                        id="email"
                        placeholder="seu@email.com"
                    >

                    <button type="button">
                        Enviar Link de Recuperação
                    </button>

                </form>

                <div class="link">

                    <a href="?pagina=login">
                        Voltar para o Login
                    </a>

                </div>

            </section>

        <?php

        }

        ?>

    </main>

    <footer class="rodape">
        Meu Controle Financeiro
    </footer>

</body>

</html>
