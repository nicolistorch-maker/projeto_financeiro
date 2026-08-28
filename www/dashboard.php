<?php

$pagina = isset($_GET['pagina']) ? $_GET['pagina'] : 'dashboard';

?>

<!DOCTYPE html>
<html lang="pt-br">

<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Meu Controle Financeiro - Dashboard</title>

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
    color:white;
    text-decoration:none;
    font-size:14px;
}

.menu a:hover{
    text-decoration:underline;
}

.menu .ativo{
    font-weight:bold;
}


/* CONTAINER */

.container{
    max-width:1100px;

    margin:0 auto;

    padding:30px;
}


/* TITULO */

.titulo{
    margin-bottom:25px;
}

.titulo h1{
    font-size:28px;
    margin-bottom:8px;
}

.titulo p{
    color:#64748b;
    font-size:15px;
}


/* CARDS */

.cards{
    display:grid;

    grid-template-columns:repeat(3, 1fr);

    gap:18px;

    margin-bottom:25px;
}

.card{
    background:white;

    border:1px solid #e2e8f0;

    border-radius:7px;

    padding:22px;

    box-shadow:0 2px 6px rgba(0,0,0,0.04);
}

.card.saldo{
    border-left:4px solid #10b981;
}

.card.receitas{
    border-left:4px solid #3b82f6;
}

.card.despesas{
    border-left:4px solid #ef4444;
}

.card p{
    color:#64748b;
    font-size:14px;
    margin-bottom:7px;
}

.valor{
    font-size:24px;
    font-weight:bold;
}

.verde{
    color:#10b981;
}

.azul{
    color:#3b82f6;
}

.vermelho{
    color:#ef4444;
}


/* GRAFICO */

.grafico{
    background:white;

    border:1px solid #e2e8f0;

    border-radius:7px;

    padding:25px;

    box-shadow:0 2px 6px rgba(0,0,0,0.04);
}

.grafico h2{
    font-size:17px;

    margin-bottom:20px;
}


/* AREA DO GRAFICO */

.area-grafico{
    height:300px;

    border:1px dashed #cbd5e1;

    border-radius:5px;

    background:#fafbfc;

    position:relative;

    overflow:hidden;
}


/* LINHAS DO GRAFICO */

.linha-horizontal{
    position:absolute;

    left:45px;
    right:20px;

    height:1px;

    background:#e2e8f0;
}

.linha1{
    top:60px;
}

.linha2{
    top:120px;
}

.linha3{
    top:180px;
}

.linha4{
    top:240px;
}


/* GRAFICO */

.grafico-svg{
    width:100%;
    height:100%;
}

.legenda{
    position:absolute;

    bottom:12px;
    left:50%;

    transform:translateX(-50%);

    display:flex;

    gap:25px;

    font-size:12px;

    color:#64748b;
}

.legenda span{
    display:flex;
    align-items:center;
    gap:5px;
}

.ponto-entrada{
    width:10px;
    height:10px;
    border-radius:50%;
    background:#3b82f6;
}

.ponto-saida{
    width:10px;
    height:10px;
    border-radius:50%;
    background:#ef4444;
}


/* BOTOES */

.acoes{
    display:flex;

    gap:12px;

    margin-top:20px;
}

.botao{
    display:inline-block;

    padding:10px 18px;

    color:white;

    text-decoration:none;

    border-radius:5px;

    font-size:14px;

    font-weight:bold;
}

.botao-verde{
    background:#10b981;
}

.botao-azul{
    background:#3b82f6;
}

.botao:hover{
    opacity:0.9;
}


/* RESPONSIVIDADE */

@media(max-width:800px){

    .navbar{
        flex-direction:column;
        gap:15px;
    }

    .menu{
        flex-wrap:wrap;
        justify-content:center;
    }

    .container{
        padding:25px 20px;
    }

    .cards{
        grid-template-columns:1fr;
    }

}


@media(max-width:500px){

    .container{
        padding:20px 12px;
    }

    .titulo h1{
        font-size:24px;
    }

    .acoes{
        flex-direction:column;
    }

    .botao{
        text-align:center;
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

        <a href="transacoes.php">
            Receitas
        </a>

        <span>|</span>

        <a href="transacoes.php">
            Despesas
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

        <a href="index.php">
            Sair
        </a>

    </nav>

</header>


<main class="container">


    <!-- TITULO -->

    <section class="titulo">

        <h1>
            Dashboard
        </h1>

        <p>
            Confira um resumo da sua situação financeira.
        </p>

    </section>


    <!-- CARDS -->

    <section class="cards">


        <div class="card saldo">

            <p>
                Saldo Atual
            </p>

            <div class="valor verde">
                R$ 3.500,00
            </div>

        </div>


        <div class="card receitas">

            <p>
                Total de Receitas
            </p>

            <div class="valor azul">
                R$ 5.000,00
            </div>

        </div>


        <div class="card despesas">

            <p>
                Total de Despesas
            </p>

            <div class="valor vermelho">
                R$ 1.500,00
            </div>

        </div>


    </section>


    <!-- GRAFICO -->

    <section class="grafico">

        <h2>
            Evolução Mensal (Diferencial)
        </h2>


        <div class="area-grafico">


            <div class="linha-horizontal linha1"></div>
            <div class="linha-horizontal linha2"></div>
            <div class="linha-horizontal linha3"></div>
            <div class="linha-horizontal linha4"></div>


            <svg
                class="grafico-svg"
                viewBox="0 0 900 300"
                preserveAspectRatio="none"
            >

                <!-- Linha de entrada -->

                <polyline
                    points="60,220 190,170 320,190 450,100 580,130 710,75 840,95"
                    fill="none"
                    stroke="#3b82f6"
                    stroke-width="4"
                />

                <!-- Linha de saída -->

                <polyline
                    points="60,250 190,225 320,235 450,185 580,205 710,150 840,165"
                    fill="none"
                    stroke="#ef4444"
                    stroke-width="4"
                />


                <!-- Pontos das entradas -->

                <circle cx="60" cy="220" r="5" fill="#3b82f6"/>
                <circle cx="190" cy="170" r="5" fill="#3b82f6"/>
                <circle cx="320" cy="190" r="5" fill="#3b82f6"/>
                <circle cx="450" cy="100" r="5" fill="#3b82f6"/>
                <circle cx="580" cy="130" r="5" fill="#3b82f6"/>
                <circle cx="710" cy="75" r="5" fill="#3b82f6"/>
                <circle cx="840" cy="95" r="5" fill="#3b82f6"/>


                <!-- Pontos das saídas -->

                <circle cx="60" cy="250" r="5" fill="#ef4444"/>
                <circle cx="190" cy="225" r="5" fill="#ef4444"/>
                <circle cx="320" cy="235" r="5" fill="#ef4444"/>
                <circle cx="450" cy="185" r="5" fill="#ef4444"/>
                <circle cx="580" cy="205" r="5" fill="#ef4444"/>
                <circle cx="710" cy="150" r="5" fill="#ef4444"/>
                <circle cx="840" cy="165" r="5" fill="#ef4444"/>

            </svg>


            <div class="legenda">

                <span>
                    <span class="ponto-entrada"></span>
                    Entradas
                </span>

                <span>
                    <span class="ponto-saida"></span>
                    Saídas
                </span>

            </div>


        </div>

    </section>


    <!-- ACOES -->

    <div class="acoes">

        <a
            class="botao botao-verde"
            href="transacoes.php"
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


</main>


</body>

</html>