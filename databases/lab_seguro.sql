-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 29, 2026 at 09:05 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `lab_seguro`
--

-- --------------------------------------------------------

--
-- Table structure for table `clientes_reparo`
--

CREATE TABLE `clientes_reparo` (
  `id` int(11) NOT NULL,
  `nome` varchar(100) NOT NULL,
  `email` varchar(100) DEFAULT NULL,
  `telefone` varchar(20) DEFAULT NULL,
  `modelo_aparelho` varchar(50) DEFAULT NULL,
  `problema_relatado` varchar(255) DEFAULT NULL,
  `status_servico` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `clientes_reparo`
--

INSERT INTO `clientes_reparo` (`id`, `nome`, `email`, `telefone`, `modelo_aparelho`, `problema_relatado`, `status_servico`) VALUES
(1, 'Ana Silva', 'ana.silva@email.com', '(11) 91234-5670', 'iPhone 13', 'Tela trincada', 'Em andamento'),
(2, 'Bruno Costa', 'bruno.costa@email.com', '(11) 91234-5671', 'Galaxy S22', 'Bateria não segura carga', 'Aguardando peça'),
(3, 'Carla Mendes', 'carla.mendes@email.com', '(11) 91234-5672', 'Moto G60', 'Não liga', 'Orçamento enviado'),
(4, 'Diego Rocha', 'diego.rocha@email.com', '(11) 91234-5673', 'iPhone 11', 'Conector de carga com mau contato', 'Concluído'),
(5, 'Elaine Gomes', 'elaine.gomes@email.com', '(11) 91234-5674', 'Xiaomi Redmi Note 11', 'Câmera traseira embaçada', 'Em andamento'),
(6, 'Fábio Martins', 'fabio.martins@email.com', '(11) 91234-5675', 'Galaxy A53', 'Caiu na água', 'Avaliação técnica'),
(7, 'Gabriela Nunes', 'gabriela.nunes@email.com', '(11) 91234-5676', 'iPhone 14 Pro', 'Face ID parou de funcionar', 'Aguardando aprovação'),
(8, 'Henrique Alves', 'henrique.alves@email.com', '(11) 91234-5677', 'Poco X3 Pro', 'Loop infinito na logo', 'Concluído'),
(9, 'Isabela Dias', 'isabela.dias@email.com', '(11) 91234-5678', 'Galaxy S23 Ultra', 'Tela verde', 'Em andamento'),
(10, 'João Pereira', 'joao.pereira@email.com', '(11) 91234-5679', 'iPhone 12', 'Bateria viciada', 'Concluído'),
(11, 'Karina Lima', 'karina.lima@email.com', '(11) 91234-5680', 'Moto Edge 30', 'Botão power não responde', 'Aguardando peça'),
(12, 'Leonardo Santos', 'leonardo.santos@email.com', '(11) 91234-5681', 'Galaxy Z Flip 4', 'Dobradiça danificada', 'Orçamento enviado'),
(13, 'Mariana Ferreira', 'mariana.ferreira@email.com', '(11) 91234-5682', 'iPhone XR', 'Vidro traseiro quebrado', 'Em andamento'),
(14, 'Nicolas Sousa', 'nicolas.sousa@email.com', '(11) 91234-5683', 'Xiaomi Mi 11', 'Esquentando muito', 'Avaliação técnica'),
(15, 'Olívia Ribeiro', 'olivia.ribeiro@email.com', '(11) 91234-5684', 'Galaxy S21 FE', 'Não reconhece chip', 'Aguardando peça'),
(16, 'Paulo Castro', 'paulo.castro@email.com', '(11) 91234-5685', 'iPhone 13 Pro Max', 'Microfone baixo', 'Concluído'),
(17, 'Quintino Moraes', 'quintino.moraes@email.com', '(11) 91234-5686', 'Moto G100', 'Touch screen falhando', 'Em andamento'),
(18, 'Rafaela Carvalho', 'rafaela.carvalho@email.com', '(11) 91234-5687', 'Galaxy S20', 'Mancha roxa na tela', 'Concluído'),
(19, 'Samuel Pinto', 'samuel.pinto@email.com', '(11) 91234-5688', 'iPhone 8 Plus', 'Bateria estufada', 'Aguardando aprovação'),
(20, 'Tatiana Nogueira', 'tatiana.nogueira@email.com', '(11) 91234-5689', 'Redmi Note 10', 'Alto-falante chiando', 'Em andamento'),
(21, 'Ubirajara Silva', 'ubirajara.silva@email.com', '(11) 91234-5690', 'Galaxy A73', 'Câmera frontal escura', 'Concluído'),
(22, 'Vanessa Borges', 'vanessa.borges@email.com', '(11) 91234-5691', 'iPhone 11 Pro', 'Caiu no mar', 'Sem conserto'),
(23, 'Wagner Freitas', 'wagner.freitas@email.com', '(11) 91234-5692', 'Poco F3', 'Travando em jogos', 'Avaliação técnica'),
(24, 'Xuxa Meneghel', 'xuxa.m@email.com', '(11) 91234-5693', 'Galaxy S22 Ultra', 'Caneta S Pen não conecta', 'Em andamento'),
(25, 'Yuri Teixeira', 'yuri.teixeira@email.com', '(11) 91234-5694', 'iPhone SE 2020', 'Botão home quebrado', 'Concluído'),
(26, 'Zélia Cardoso', 'zelia.cardoso@email.com', '(11) 91234-5695', 'Moto G20', 'Não sai som no fone', 'Aguardando peça'),
(27, 'André Almeida', 'andre.almeida@email.com', '(11) 91234-5696', 'Xiaomi 12', 'Traseira descascando', 'Orçamento enviado'),
(28, 'Beatriz Vieira', 'beatriz.vieira@email.com', '(11) 91234-5697', 'iPhone 14', 'Wifi não conecta', 'Em andamento'),
(29, 'Caio Batista', 'caio.batista@email.com', '(11) 91234-5698', 'Galaxy M53', 'Reiniciando sozinho', 'Avaliação técnica'),
(30, 'Daniela Ramos', 'daniela.ramos@email.com', '(11) 91234-5699', 'iPhone 12 Mini', 'Tela não responde ao toque', 'Aguardando aprovação'),
(31, 'Eduardo Machado', 'eduardo.machado@email.com', '(11) 91234-5700', 'Moto G82', 'Vibração parou', 'Concluído'),
(32, 'Fernanda Farias', 'fernanda.farias@email.com', '(11) 91234-5701', 'Galaxy S10', 'Tela listrada', 'Aguardando peça'),
(33, 'Gustavo Lopes', 'gustavo.lopes@email.com', '(11) 91234-5702', 'iPhone X', 'Câmera tremendo', 'Concluído'),
(34, 'Heloísa Monteiro', 'heloisa.monteiro@email.com', '(11) 91234-5703', 'Redmi Note 9', 'Não sai da tela de boot', 'Em andamento'),
(35, 'Igor Pires', 'igor.pires@email.com', '(11) 91234-5704', 'Galaxy A32', 'Conector USB-C frouxo', 'Orçamento enviado'),
(36, 'Júlia Moura', 'julia.moura@email.com', '(11) 91234-5705', 'iPhone 13 Mini', 'Sem sinal de rede', 'Avaliação técnica'),
(37, 'Kleber Duarte', 'kleber.duarte@email.com', '(11) 91234-5706', 'Poco M4 Pro', 'Lente da câmera quebrada', 'Concluído'),
(38, 'Lívia Barros', 'livia.barros@email.com', '(11) 91234-5707', 'Galaxy Note 20', 'Bateria dura pouco', 'Em andamento'),
(39, 'Marcelo Viana', 'marcelo.viana@email.com', '(11) 91234-5708', 'Moto E40', 'Tela fantasma (Burn-in)', 'Aguardando peça'),
(40, 'Natália Campos', 'natalia.campos@email.com', '(11) 91234-5709', 'iPhone 11', 'Som ambiente chiando', 'Concluído'),
(41, 'Otávio Leite', 'otavio.leite@email.com', '(11) 91234-5710', 'Xiaomi 13 Pro', 'Desliga a 30%', 'Em andamento'),
(42, 'Patrícia Neves', 'patricia.neves@email.com', '(11) 91234-5711', 'Galaxy S21 Ultra', 'Caiu e entortou a carcaça', 'Aguardando aprovação'),
(43, 'Renato Cordeiro', 'renato.cordeiro@email.com', '(11) 91234-5712', 'iPhone 7', 'Codec de áudio com defeito', 'Orçamento enviado'),
(44, 'Sofia Guimarães', 'sofia.guimaraes@email.com', '(11) 91234-5713', 'Moto G G52', 'Esqueceu a senha', 'Concluído'),
(45, 'Thiago Sales', 'thiago.sales@email.com', '(11) 91234-5714', 'Galaxy A13', 'Não lê cartão SD', 'Em andamento'),
(46, 'Úrsula Pacheco', 'ursula.pacheco@email.com', '(11) 91234-5715', 'iPhone 12 Pro', 'GPS não localiza', 'Avaliação técnica'),
(47, 'Vitor Novaes', 'vitor.novaes@email.com', '(11) 91234-5716', 'Redmi Note 12', 'Carregamento muito lento', 'Concluído'),
(48, 'Willian Pires', 'willian.pires@email.com', '(11) 91234-5717', 'Galaxy Z Fold 3', 'Película interna descolando', 'Aguardando peça'),
(49, 'Yasmin Franco', 'yasmin.franco@email.com', '(11) 91234-5718', 'iPhone 13', 'Lanterna não liga', 'Em andamento'),
(50, 'Zeca Martins', 'zeca.martins@email.com', '(11) 91234-5719', 'Moto G10', 'Travado na tela inicial', 'Concluído'),
(51, 'Felipe Luiz', 'felipe.luiz@email.com', '(11) 98999-8399', 'Galaxy S23 Ultra', 'Troca de película', 'Concluído');

-- --------------------------------------------------------

--
-- Table structure for table `logs_acesso`
--

CREATE TABLE `logs_acesso` (
  `id` int(11) NOT NULL,
  `email_tentado` varchar(100) DEFAULT NULL,
  `ip_origem` varchar(45) DEFAULT NULL,
  `status_login` varchar(20) DEFAULT NULL,
  `data_hora` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `logs_acesso`
--

INSERT INTO `logs_acesso` (`id`, `email_tentado`, `ip_origem`, `status_login`, `data_hora`) VALUES
(1, 'admin@mail.com', '::1', 'Falha', '2026-05-29 12:26:39'),
(2, 'admin@mail.com', '::1', 'Falha', '2026-05-29 12:27:04'),
(3, 'admin@mail.com', '::1', 'Sucesso', '2026-05-29 12:32:35'),
(4, 'Administrador', '::1', 'Logout', '2026-05-29 12:33:11'),
(5, 'felipe@mail.com', '::1', 'Sucesso', '2026-05-29 12:33:20'),
(6, 'Felipe', '::1', 'Logout', '2026-05-29 12:34:53'),
(7, 'felipe@mail.com', '::1', 'Sucesso', '2026-05-29 12:46:42'),
(8, 'Felipe', '::1', 'Logout', '2026-05-29 12:46:47'),
(9, 'admin@mail.com', '::1', 'Sucesso', '2026-05-29 12:46:55'),
(10, 'Administrador', '::1', 'Logout', '2026-05-29 12:54:33'),
(11, 'admin@mail.com', '::1', 'Sucesso', '2026-05-29 12:56:22'),
(12, 'Administrador', '::1', 'Logout', '2026-05-29 12:56:30'),
(13, 'admin@mail.com', '::1', 'Sucesso', '2026-05-29 12:56:52'),
(14, 'admin@mail.com', '::1', 'Sucesso', '2026-05-29 13:08:53'),
(15, 'Administrador', '::1', 'Logout', '2026-05-29 13:12:05'),
(16, 'admin@mail.com', '::1', 'Sucesso', '2026-05-29 13:13:40'),
(17, 'Administrador', '::1', 'Logout', '2026-05-29 13:14:14'),
(18, 'felipe@mail.com', '::1', 'Sucesso', '2026-05-29 13:19:37'),
(19, 'admin@mail.com', '::1', 'Sucesso', '2026-05-29 13:19:43'),
(20, 'felipe@mail.com', '::1', 'Senha Redefinida', '2026-05-29 13:21:13'),
(21, 'felipe@mail.com', '::1', 'Sucesso', '2026-05-29 13:21:39'),
(22, 'admin@mail.com', '::1', 'Sucesso', '2026-05-29 15:48:55'),
(23, 'Administrador', '::1', 'Logout', '2026-05-29 16:00:07'),
(24, 'admin@mail.com', '::1', 'Sucesso', '2026-05-29 16:03:47');

-- --------------------------------------------------------

--
-- Table structure for table `recuperacao_senha`
--

CREATE TABLE `recuperacao_senha` (
  `id` int(11) NOT NULL,
  `email` varchar(100) DEFAULT NULL,
  `token` varchar(255) DEFAULT NULL,
  `data_expiracao` datetime DEFAULT NULL,
  `usado` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `recuperacao_senha`
--

INSERT INTO `recuperacao_senha` (`id`, `email`, `token`, `data_expiracao`, `usado`) VALUES
(6, 'admin@mail.com', '1a184cd917ef8e12b4e66d6167dd302665c549a2df57be75e468d6144e22567e', '2026-05-29 16:22:42', 0);

-- --------------------------------------------------------

--
-- Table structure for table `usuarios`
--

CREATE TABLE `usuarios` (
  `id` int(11) NOT NULL,
  `nome` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `senha_hash` varchar(255) NOT NULL,
  `perfil` varchar(20) NOT NULL,
  `ativo` tinyint(1) DEFAULT 1,
  `data_criacao` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `usuarios`
--

INSERT INTO `usuarios` (`id`, `nome`, `email`, `senha_hash`, `perfil`, `ativo`, `data_criacao`) VALUES
(1, 'Administrador', 'admin@mail.com', '$2y$10$7d4yxliBSYt1sWJVEBMnVepiaXRU29SCZQmjnlGY7sMxzGE6PdQ0C', 'admin', 1, '2026-05-29 12:20:31'),
(2, 'Felipe', 'felipe@mail.com', '$2y$10$7YWJDNyL6ZdmmO7lp/MsBeNrhjA0zQRZA1kQeSy7ItrZuXuVK2ql.', 'user', 1, '2026-05-29 12:33:08'),
(4, 'Thaynara', 'thay@mail.com', '$2y$10$dqc.nMUmZeL1HqYecJGpQeZG6wz2hHumlDTj/Ec8ojqfZPqAnfuyK', 'admin', 1, '2026-05-29 16:04:07');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `clientes_reparo`
--
ALTER TABLE `clientes_reparo`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `logs_acesso`
--
ALTER TABLE `logs_acesso`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `recuperacao_senha`
--
ALTER TABLE `recuperacao_senha`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `clientes_reparo`
--
ALTER TABLE `clientes_reparo`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=52;

--
-- AUTO_INCREMENT for table `logs_acesso`
--
ALTER TABLE `logs_acesso`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT for table `recuperacao_senha`
--
ALTER TABLE `recuperacao_senha`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
