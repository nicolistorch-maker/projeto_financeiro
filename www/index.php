<?php

$pagina = isset($_GET['pagina']) ? $_GET['pagina'] : 'home';

?>

<!DOCTYPE html>
<html lang="pt-br">

<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Meu Controle Financeiro</title>

<style>

*{
    margin:0;
    padding:0;
    box-sizing:border-box;
    font-family:Arial, Helvetica, sans-serif;
}

body{
    background:#f5f7fb;
    color:#1f2937;
}


/* NAVBAR */

.navbar{
    background:#111827;
    color:white;
    padding:15px 35px;

    display:flex;
    justify-content:space-between;
    align-items:center;
}

.logo{
    font-size:18px;
    font-weight:bold;
}

.menu{
    display:flex;
    align-items:center;
    gap:10px;
}

.menu a{
    text-decoration:none;
    color:white;
    padding:8px 15px;
    border-radius:5px;
}

.login{
    border:1px solid white;
}

.cadastro{
    background:#3b82f6;
}


/* CONTAINER */

.container{
    min-height:85vh;

    display:flex;
    justify-content:center;
    align-items:center;

    padding:30px;
}


/* HOME */

.hero{
    background:white;

    width:100%;
    max-width:900px;

    text-align:center;

    padding:60px 30px;

    border-radius:8px;

    box-shadow:0 2px 10px #ddd;
}

.hero h1{
    font-size:32px;
    margin-bottom:15px;
}

.hero p{
    color:#64748b;
    margin-bottom:30px;
}


/* BOTOES DA HOME */

.botoes-home{
    display:flex;
    justify-content:center;
    flex-wrap:wrap;
    gap:12px;
}

.btn{
    display:inline-block;

    color:white;
    text-decoration:none;

    padding:12px 24px;

    border-radius:5px;

    font-weight:bold;
}

.btn-verde{
    background:#10b981;
}

.btn-azul{
    background:#3b82f6;
}

.btn-escuro{
    background:#111827;
}

.btn-cinza{
    background:#64748b;
}

.btn-roxo{
    background:#8b5cf6;
}


/* FORMULARIOS */

.card{
    background:white;

    width:100%;
    max-width:380px;

    padding:30px;

    border-radius:8px;

    box-shadow:0 2px 10px #ddd;
}

.card h2{
    text-align:center;
    margin-bottom:25px;
}

label{
    font-weight:bold;
    font-size:14px;
}

input{
    width:100%;

    padding:12px;

    margin:8px 0 18px;

    border:1px solid #cbd5e1;

    border-radius:5px;
}

button{
    width:100%;

    background:#3b82f6;

    color:white;

    border:none;

    padding:12px;

    border-radius:5px;

    cursor:pointer;

    font-weight:bold;
}

.verde{
    background:#10b981;
}

.link{
    text-align:center;
    margin-top:15px;
}

.link a{
    color:#2563eb;
    text-decoration:none;
    font-size:14px;
}

.texto{
    text-align:center;
    color:#64748b;
    font-size:14px;
    margin-bottom:20px;
}


/* RESPONSIVIDADE */

@media(max-width:600px){

    .navbar{
        flex-direction:column;
        gap:15px;
    }

    .menu{
        flex-wrap:wrap;
        justify-content:center;
    }

    .hero h1{
        font-size:25px;
    }

    .botoes-home{
        flex-direction:column;
    }

    .btn{
        width:100%;
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

    <div class="menu">

        <a class="login" href="?pagina=login">
            Login
        </a>

        <a class="cadastro" href="?pagina=cadastro">
            Cadastrar
        </a>

    </div>

</header>


<div class="container">


<?php


/* HOME */

if($pagina == "home"){

?>

<div class="hero">

    <h1>
        Organize sua vida financeira hoje
    </h1>

    <p>
        Controle receitas, despesas e alcance suas metas de forma simples.
    </p>


    <div class="botoes-home">

        <a class="btn btn-verde" href="?pagina=cadastro">
            Começar Agora Grátis
        </a>

        <a class="btn btn-azul" href="?pagina=login">
            Entrar no Sistema
        </a>

        <a class="btn btn-escuro" href="dashboard.php">
            Dashboard
        </a>

        <a class="btn btn-cinza" href="transacoes.php">
            Lançamentos
        </a>

        <a class="btn btn-roxo" href="transacoes.php?pagina=relatorios">
            Relatórios
        </a>

        <a class="btn btn-azul" href="perfil.php">
            Meu Perfil
        </a>

        <a class="btn btn-escuro" href="admin.php">
            Área Administrativa
        </a>

    </div>

</div>


<?php


}


/* LOGIN */

elseif($pagina=="login"){

?>

<div class="card">

    <h2>
        Entrar no Sistema
    </h2>

    <form>

        <label>
            E-mail
        </label>

        <input
            type="email"
            placeholder="seu@email.com"
        >

        <label>
            Senha
        </label>

        <input
            type="password"
            placeholder="••••••••"
        >

        <button
            type="button"
            onclick="window.location.href='dashboard.php'"
        >
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

</div>


<?php

}


/* CADASTRO */

elseif($pagina=="cadastro"){

?>

<div class="card">

    <h2>
        Criar Nova Conta
    </h2>

    <form>

        <label>
            Nome Completo
        </label>

        <input
            type="text"
            placeholder="Ex: João Silva"
        >

        <label>
            E-mail
        </label>

        <input
            type="email"
            placeholder="nome@provedor.com"
        >

        <label>
            Senha
        </label>

        <input
            type="password"
            placeholder="Mínimo 6 caracteres"
        >

        <button
            type="button"
            class="verde"
            onclick="window.location.href='dashboard.php'"
        >
            Criar Minha Conta
        </button>

    </form>


    <div class="link">

        <a href="?pagina=login">
            Já tem conta? Faça login
        </a>

    </div>

</div>


<?php

}


/* RECUPERAÇÃO */

elseif($pagina=="recuperar"){

?>

<div class="card">

    <h2>
        Recuperar Senha
    </h2>

    <p class="texto">
        Informe o e-mail cadastrado para receber um token de redefinição.
    </p>

    <form>

        <label>
            E-mail
        </label>

        <input
            type="email"
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

</div>


<?php

}


?>

</div>


</body>

</html>