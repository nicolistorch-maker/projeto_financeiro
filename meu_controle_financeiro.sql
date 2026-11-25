-- phpMyAdmin SQL Dump
-- version 5.2.3
-- https://www.phpmyadmin.net/
--
-- Host: mysql:3306
-- Tempo de geração: 25/11/2026 às 12:33
-- Versão do servidor: 8.3.0
-- Versão do PHP: 8.3.36

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Banco de dados: `meu_controle_financeiro`
--

-- --------------------------------------------------------

--
-- Estrutura para tabela `usuarios`
--

CREATE TABLE `usuarios` (
  `id` int NOT NULL,
  `nome` varchar(100) NOT NULL,
  `email` varchar(150) NOT NULL,
  `senha` varchar(255) NOT NULL,
  `perfil` enum('comum','admin') NOT NULL DEFAULT 'comum',
  `status` enum('ativo','bloqueado') NOT NULL DEFAULT 'ativo',
  `criado_em` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Despejando dados para a tabela `usuarios`
--

INSERT INTO `usuarios` (`id`, `nome`, `email`, `senha`, `perfil`, `status`, `criado_em`) VALUES
(1, 'nicoli storch', 'nicoli@gmail.com', '$2y$10$salh6H.B8wTNkLzLrxXI4.V6CzRIajzadYlQhxkWAEiFg9lLwADS2', 'comum', 'ativo', '2026-10-16 11:00:07'),
(3, 'Administrador', 'admin@meucontrole.com', '$2y$10$9UdINjAqtckikwi/8dzVgewbKbbAetPes7.efyQFH6MkqQla4FeLa', 'admin', 'ativo', '2026-10-16 11:59:35'),
(4, 'nica', 'nicaadimin@gmail.com', '$2y$10$kHh/lV0lajPSlOhnL1uMLOS0ytb1/Wn/pNtJDgNCEBjYQyt0jor52', 'admin', 'ativo', '2026-11-25 10:45:32'),
(6, 'camily', 'mily@gmail.com', '$2y$10$DV3yehRbHUBSD7eXs8ERteBSOiZIKR4S4llIo5nUXIAGoQ9YFmtr6', 'comum', 'ativo', '2026-11-25 11:24:26');

--
-- Índices para tabelas despejadas
--

--
-- Índices de tabela `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT para tabelas despejadas
--

--
-- AUTO_INCREMENT de tabela `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
