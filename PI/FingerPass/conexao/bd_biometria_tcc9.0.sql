-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Tempo de geração: 08/11/2025 às 18:46
-- Versão do servidor: 10.4.32-MariaDB
-- Versão do PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Banco de dados: `bd_biometria_tcc`
--

-- --------------------------------------------------------

--
-- Estrutura para tabela `aluno`
--

CREATE TABLE `aluno` (
  `id_aluno` int(11) NOT NULL,
  `nome` varchar(70) NOT NULL,
  `biometria` int(11) DEFAULT NULL COMMENT 'ID da digital no sensor biométrico (1-162)',
  `data_nascimento` date DEFAULT NULL,
  `sexo` char(1) DEFAULT NULL,
  `telefone` varchar(15) DEFAULT NULL,
  `matricula` varchar(15) DEFAULT NULL,
  `id_turma` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Despejando dados para a tabela `aluno`
--

INSERT INTO `aluno` (`id_aluno`, `nome`, `biometria`, `data_nascimento`, `sexo`, `telefone`, `matricula`, `id_turma`) VALUES
(9, 'Matheus Uggioni Possamai', 6, '2008-05-20', 'M', '48991151890', '4552102507', 4),
(10, 'Filipe Uggioni Possamai', 2, '2013-07-06', 'M', '48991151890', '4552102508', 4),
(11, 'Nathan', 3, '2008-09-01', 'M', '48999308352', '4552167706', 4),
(12, 'lucas constante serafim', 4, '2008-03-31', 'M', '222222222222222', '4540885137', 4),
(13, 'Luiz Fernando Cardoso Roberge', 5, '2007-04-12', 'M', '48991863228', '4552298053', 4),
(14, 'joaquim de bem marques de carvalho', 7, '2008-03-20', 'F', '48999971805', '4551856010', 4),
(15, 'Felson Lisbino', 8, '2008-06-23', 'M', '48998330688', '4540922806', 4),
(16, 'jessica', 9, '1991-04-21', 'F', '48996126151', '6669514', 4),
(17, 'leonardo delfino jose', 10, '2008-02-05', 'M', '48996040737', '4541496088', 4),
(18, 'Mugrilhos', 11, '2008-07-01', 'M', '48999264890', '4552234470', 4);

-- --------------------------------------------------------

--
-- Estrutura para tabela `curso`
--

CREATE TABLE `curso` (
  `id_curso` int(11) NOT NULL,
  `nome` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `curso`
--

INSERT INTO `curso` (`id_curso`, `nome`) VALUES
(1, 'Informática'),
(2, 'Administração'),
(3, 'Edificações'),
(4, 'Ciência de Dados'),
(5, 'Química'),
(6, 'Alimentos'),
(7, 'Comércio'),
(8, 'Marketing'),
(9, 'Informática - Not');

-- --------------------------------------------------------

--
-- Estrutura para tabela `horario_aula`
--

CREATE TABLE `horario_aula` (
  `id_horario` int(11) NOT NULL,
  `dia_semana` enum('Domingo','Segunda-feira','Terca-feira','Quarta-feira','Quinta-feira','Sexta-feira','Sabado') DEFAULT NULL,
  `hora_inicio` time DEFAULT NULL,
  `hora_fim` time DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Despejando dados para a tabela `horario_aula`
--

INSERT INTO `horario_aula` (`id_horario`, `dia_semana`, `hora_inicio`, `hora_fim`) VALUES
(5, 'Segunda-feira', '07:45:00', '17:15:00'),
(7, 'Terca-feira', '07:45:00', '11:45:00'),
(8, 'Sexta-feira', '08:30:00', '16:30:00'),
(9, 'Quarta-feira', '13:15:00', '16:30:00');

-- --------------------------------------------------------

--
-- Estrutura para tabela `hora_turma`
--

CREATE TABLE `hora_turma` (
  `id_hora_turma` int(11) NOT NULL,
  `id_turma` int(11) DEFAULT NULL,
  `id_horario` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Despejando dados para a tabela `hora_turma`
--

INSERT INTO `hora_turma` (`id_hora_turma`, `id_turma`, `id_horario`) VALUES
(1, 4, 5),
(4, 4, 7),
(7, 9, 5),
(8, 8, 5),
(9, 7, 7),
(10, 7, 5);

-- --------------------------------------------------------

--
-- Estrutura para tabela `registro_chamada`
--

CREATE TABLE `registro_chamada` (
  `id_registro` int(11) NOT NULL,
  `presenca` char(1) DEFAULT NULL,
  `data_biometria` date DEFAULT NULL,
  `hora_biometria` time DEFAULT NULL,
  `id_aluno` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Despejando dados para a tabela `registro_chamada`
--

INSERT INTO `registro_chamada` (`id_registro`, `presenca`, `data_biometria`, `hora_biometria`, `id_aluno`) VALUES
(5, 'S', '2025-11-05', '21:18:22', 10),
(6, 'S', '2025-11-05', '19:06:24', 9),
(7, 'S', '2025-11-05', '19:07:16', 13),
(8, 'S', '2025-11-05', '21:02:33', 15),
(9, 'S', '2025-11-05', '21:01:41', 14),
(10, 'S', '2025-11-06', '01:10:15', 10),
(11, 'S', '2025-11-06', '11:21:51', 11),
(12, 'S', '2025-11-06', '11:25:40', 14),
(13, 'S', '2025-11-06', '11:25:22', 17),
(14, 'S', '2025-11-07', '13:44:22', 10),
(15, 'S', '2025-11-07', '13:44:42', 18),
(16, 'S', '2025-11-07', '13:45:14', 11);

-- --------------------------------------------------------

--
-- Estrutura para tabela `turma`
--

CREATE TABLE `turma` (
  `id_turma` int(11) NOT NULL,
  `n_turma` varchar(4) DEFAULT NULL,
  `turno` enum('Matutino','Vespertino','Noturno') DEFAULT NULL,
  `contra_turno` enum('Matutino','Vespertino','Noturno') DEFAULT NULL,
  `id_curso` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Despejando dados para a tabela `turma`
--

INSERT INTO `turma` (`id_turma`, `n_turma`, `turno`, `contra_turno`, `id_curso`) VALUES
(4, '3-53', 'Matutino', 'Vespertino', 1),
(5, '3-51', 'Matutino', 'Vespertino', 1),
(6, '3-52', 'Matutino', 'Vespertino', 1),
(7, '3-54', 'Matutino', 'Vespertino', 1),
(8, '1-51', 'Vespertino', 'Matutino', 1),
(9, '1-52', 'Vespertino', 'Matutino', 1),
(10, '1-53', 'Vespertino', 'Matutino', 1),
(11, '2-51', 'Matutino', 'Vespertino', 1),
(12, '2-52', 'Matutino', 'Vespertino', 1),
(13, '2-53', 'Matutino', 'Vespertino', 1),
(14, '1-11', 'Vespertino', NULL, 2);

-- --------------------------------------------------------

--
-- Estrutura para tabela `usuario`
--

CREATE TABLE `usuario` (
  `id_usuario` int(11) NOT NULL,
  `email` varchar(100) NOT NULL,
  `senha` varchar(50) NOT NULL,
  `tipo` enum('Professor','Administrador') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Despejando dados para a tabela `usuario`
--

INSERT INTO `usuario` (`id_usuario`, `email`, `senha`, `tipo`) VALUES
(10, 'admin@gmail.com', '827ccb0eea8a706c4c34a16891f84e7b', 'Administrador'),
(11, 'teste01@gmail.com', '01cfcd4f6b8770febfb40cb906715822', 'Professor'),
(12, 'admin123@gmail.com', '827ccb0eea8a706c4c34a16891f84e7b', 'Administrador'),
(13, 'prof@gmail.com', '827ccb0eea8a706c4c34a16891f84e7b', 'Professor');

--
-- Índices para tabelas despejadas
--

--
-- Índices de tabela `aluno`
--
ALTER TABLE `aluno`
  ADD PRIMARY KEY (`id_aluno`),
  ADD UNIQUE KEY `biometria` (`biometria`),
  ADD KEY `id_turma` (`id_turma`);

--
-- Índices de tabela `curso`
--
ALTER TABLE `curso`
  ADD PRIMARY KEY (`id_curso`);

--
-- Índices de tabela `horario_aula`
--
ALTER TABLE `horario_aula`
  ADD PRIMARY KEY (`id_horario`);

--
-- Índices de tabela `hora_turma`
--
ALTER TABLE `hora_turma`
  ADD PRIMARY KEY (`id_hora_turma`),
  ADD KEY `fk_turma` (`id_turma`),
  ADD KEY `fk_horario` (`id_horario`);

--
-- Índices de tabela `registro_chamada`
--
ALTER TABLE `registro_chamada`
  ADD PRIMARY KEY (`id_registro`),
  ADD KEY `id_aluno` (`id_aluno`);

--
-- Índices de tabela `turma`
--
ALTER TABLE `turma`
  ADD PRIMARY KEY (`id_turma`),
  ADD KEY `id_curso` (`id_curso`);

--
-- Índices de tabela `usuario`
--
ALTER TABLE `usuario`
  ADD PRIMARY KEY (`id_usuario`),
  ADD UNIQUE KEY `unique_email` (`email`);

--
-- AUTO_INCREMENT para tabelas despejadas
--

--
-- AUTO_INCREMENT de tabela `aluno`
--
ALTER TABLE `aluno`
  MODIFY `id_aluno` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT de tabela `curso`
--
ALTER TABLE `curso`
  MODIFY `id_curso` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT de tabela `horario_aula`
--
ALTER TABLE `horario_aula`
  MODIFY `id_horario` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT de tabela `hora_turma`
--
ALTER TABLE `hora_turma`
  MODIFY `id_hora_turma` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT de tabela `registro_chamada`
--
ALTER TABLE `registro_chamada`
  MODIFY `id_registro` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT de tabela `turma`
--
ALTER TABLE `turma`
  MODIFY `id_turma` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT de tabela `usuario`
--
ALTER TABLE `usuario`
  MODIFY `id_usuario` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- Restrições para tabelas despejadas
--

--
-- Restrições para tabelas `aluno`
--
ALTER TABLE `aluno`
  ADD CONSTRAINT `aluno_ibfk_1` FOREIGN KEY (`id_turma`) REFERENCES `turma` (`id_turma`);

--
-- Restrições para tabelas `hora_turma`
--
ALTER TABLE `hora_turma`
  ADD CONSTRAINT `fk_horario` FOREIGN KEY (`id_horario`) REFERENCES `horario_aula` (`id_horario`),
  ADD CONSTRAINT `fk_turma` FOREIGN KEY (`id_turma`) REFERENCES `turma` (`id_turma`);

--
-- Restrições para tabelas `registro_chamada`
--
ALTER TABLE `registro_chamada`
  ADD CONSTRAINT `registro_chamada_ibfk_1` FOREIGN KEY (`id_aluno`) REFERENCES `aluno` (`id_aluno`);

--
-- Restrições para tabelas `turma`
--
ALTER TABLE `turma`
  ADD CONSTRAINT `turma_ibfk_1` FOREIGN KEY (`id_curso`) REFERENCES `curso` (`id_curso`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
