CREATE DATABASE  IF NOT EXISTS `easyplanning` /*!40100 DEFAULT CHARACTER SET utf8 */;
USE `easyplanning`;
-- MySQL dump 10.13  Distrib 5.7.17, for Win64 (x86_64)
--
-- Host: localhost    Database: easyplanning
-- ------------------------------------------------------
-- Server version	5.7.21-log

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `tb_legalnatures`
--

DROP TABLE IF EXISTS `tb_legalnatures`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tb_legalnatures` (
  `legalnature_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `legalnature_name` varchar(200) NOT NULL,
  PRIMARY KEY (`legalnature_id`)
) ENGINE=InnoDB AUTO_INCREMENT=71 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tb_legalnatures`
--

LOCK TABLES `tb_legalnatures` WRITE;
/*!40000 ALTER TABLE `tb_legalnatures` DISABLE KEYS */;
INSERT INTO `tb_legalnatures` VALUES (1,'Órgão público do poder executivo federal'),(2,'Órgão público do poder executivo estadual ou do DF'),(3,'Órgão público do poder executivo municipal'),(4,'Órgão público do poder legislativo federal'),(5,'Órgão público do poder legislativo estadual ou do DF'),(6,'Órgão público do poder legislativo municipal'),(7,'Órgão público do poder judiciário federal'),(8,'Órgão público do poder judiciário estadual'),(9,'Autarquia federal'),(10,'Autarquia estadual ou do DF'),(11,'Autarquia municipal'),(12,'Fundação federal'),(13,'Fundação estadual ou do DF'),(14,'Fundação municipal'),(15,'Órgão público autônomo da união'),(16,'Órgão público autônomo estadual ou do DF'),(17,'Órgão público autônomo municipal'),(18,'Empresa pública'),(19,'Sociedade de economia mista'),(20,'Sociedade anônima aberta'),(21,'Sociedade anônima fechada'),(22,'Sociedade empresária LTda'),(23,'Sociedade empresária em nome coletivo'),(24,'Sociedade empresária em comandita simples'),(25,'Sociedade empresária em comandita POR ações'),(26,'Sociedade mercantil de cap. e ind.(EXT.NCC/2002)'),(27,'Sociedade empresária em conta de participação'),(28,'Empresário (individual)'),(29,'Cooperativa'),(30,'Consórcios de sociedades'),(31,'Grupo de sociedades'),(32,'Estabelecimento, no Brasil, de sociedade estrangeira'),(33,'Estab. no Brasil empr. binacional argentino-brasileira'),(34,'Entidade binacional ITAIPU'),(35,'Empresa domiciliada no exterior'),(36,'Clube/fundo de investimento'),(37,'Sociedade simples pura'),(38,'Sociedade simples LTda'),(39,'Sociedade em nome coletivo'),(40,'Sociedade em comandita simples'),(41,'Sociedade simples em conta de participação'),(42,'Serviço notarial e registral (cartório)'),(43,'Organização social'),(44,'Org de sociedade civil de interesse público (OSCIP)'),(45,'Outras formas de fund. mantidas com recursos privados'),(46,'Serviço social autônomo'),(47,'Condomínio edifícios'),(48,'Unidade executora (programa dinheiro direto na escola)'),(49,'Comissão de conciliação prévia'),(50,'Entidade de mediação e arbitragem'),(51,'Partido político'),(52,'Entidade sindical'),(53,'Estab., no Brasil, de fund. ou assoc.estrangeiras'),(54,'Fundação ou associação domiciliada no exterior'),(55,'Outras formas de associação'),(56,'Empresa individual imobiliária'),(57,'Segurado especial'),(58,'Contribuinte individual'),(59,'Organizacao intern. e outras instit. extraterritoriais'),(60,'Soc. civil lib. com fins lucr./ demais soc. com fins lucrativos'),(61,'Demais soc. civis com fins lucrativos'),(62,'Sociedade civil sem fins lucrativos'),(63,'Outros'),(64,'Candidato a cargo politico eletivo'),(70,'Fundação mantida com recursos privados');
/*!40000 ALTER TABLE `tb_legalnatures` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tb_objective_objective`
--

DROP TABLE IF EXISTS `tb_objective_objective`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tb_objective_objective` (
  `obj_id` int(10) unsigned NOT NULL,
  `obj_dependson` int(10) unsigned NOT NULL,
  PRIMARY KEY (`obj_id`,`obj_dependson`),
  KEY `objobj_objectives_idx` (`obj_dependson`),
  CONSTRAINT `ob_dependson_objectives` FOREIGN KEY (`obj_dependson`) REFERENCES `tb_objectives` (`obj_id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `obj_objectives` FOREIGN KEY (`obj_id`) REFERENCES `tb_objectives` (`obj_id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tb_objective_objective`
--

LOCK TABLES `tb_objective_objective` WRITE;
/*!40000 ALTER TABLE `tb_objective_objective` DISABLE KEYS */;
/*!40000 ALTER TABLE `tb_objective_objective` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tb_objectives`
--

DROP TABLE IF EXISTS `tb_objectives`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tb_objectives` (
  `obj_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `plan_id` int(10) unsigned NOT NULL,
  `persp_id` int(10) unsigned NOT NULL,
  `obj_description` text NOT NULL,
  `obj_positionx` int(11) NOT NULL,
  `obj_positiony` int(11) NOT NULL,
  `obj_sizex` int(10) unsigned NOT NULL,
  `obj_sizey` int(10) unsigned NOT NULL,
  `user_id` int(10) unsigned NOT NULL,
  `obj_dtcreation` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`obj_id`),
  KEY `objective_plan_fk_idx` (`plan_id`),
  KEY `objective_perspective_fk_idx` (`persp_id`),
  KEY `objective_user_fk_idx` (`user_id`),
  CONSTRAINT `objective_perspective_fk` FOREIGN KEY (`persp_id`) REFERENCES `tb_perspectives` (`persp_id`) ON DELETE CASCADE ON UPDATE NO ACTION,
  CONSTRAINT `objective_plan_fk` FOREIGN KEY (`plan_id`) REFERENCES `tb_strategic_planning` (`plan_id`) ON DELETE CASCADE ON UPDATE NO ACTION,
  CONSTRAINT `objective_user_fk` FOREIGN KEY (`user_id`) REFERENCES `tb_users` (`user_id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tb_objectives`
--

LOCK TABLES `tb_objectives` WRITE;
/*!40000 ALTER TABLE `tb_objectives` DISABLE KEYS */;
/*!40000 ALTER TABLE `tb_objectives` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tb_organizations`
--

DROP TABLE IF EXISTS `tb_organizations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tb_organizations` (
  `org_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `org_companyname` varchar(255) NOT NULL,
  `org_tradingname` varchar(255) NOT NULL,
  `org_size` tinyint(3) unsigned NOT NULL,
  `legalnature_id` int(10) unsigned NOT NULL,
  `org_dtcreation` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `org_notification` tinyint(3) unsigned NOT NULL DEFAULT '1',
  `org_status` tinyint(3) unsigned NOT NULL DEFAULT '1',
  `org_cnpj` varchar(16) DEFAULT NULL,
  `org_logo` text,
  `org_addrbilling` text,
  `org_addrbilling_complement` text,
  `org_addrbilling_city` varchar(255) DEFAULT NULL,
  `org_addrbilling_state` varchar(2) DEFAULT NULL,
  `org_addrbilling_zip` varchar(10) DEFAULT NULL,
  `org_addrbilling_country` varchar(100) DEFAULT NULL,
  `org_address` text,
  `org_addr_complement` text,
  `org_addr_city` varchar(255) DEFAULT NULL,
  `org_addr_state` varchar(2) DEFAULT NULL,
  `org_addr_zip` varchar(10) DEFAULT NULL,
  `org_addr_country` varchar(100) DEFAULT NULL,
  `org_contact_name` text,
  `org_contact_email` text,
  `org_contact_phone` text,
  PRIMARY KEY (`org_id`),
  KEY `tb_organizations_legalnature_idx` (`legalnature_id`),
  CONSTRAINT `tb_organizations_legalnature` FOREIGN KEY (`legalnature_id`) REFERENCES `tb_legalnatures` (`legalnature_id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tb_organizations`
--

LOCK TABLES `tb_organizations` WRITE;
/*!40000 ALTER TABLE `tb_organizations` DISABLE KEYS */;
INSERT INTO `tb_organizations` VALUES (1,'Rogério Aguiar LTDA','Treina Recife',1,28,'2018-02-18 14:36:32',0,1,'123456','lolololo','av Boa Viagem','apto 102','Recife','PE','500001-200','Brasil','av Domingos Ferreira, 123 Pina','Empresarial Jaja, sala 56','Recife','PE','50000-100','Brasil','Rogério','rogerioaguiar@treinarecife.com.br','33557020'),(2,'Emprel - Empresa Muncipal de Informática','Emprel',1,28,'2018-03-08 03:47:41',0,1,'12345666','lololo',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL);
/*!40000 ALTER TABLE `tb_organizations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tb_perspectives`
--

DROP TABLE IF EXISTS `tb_perspectives`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tb_perspectives` (
  `persp_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `persp_name` varchar(100) NOT NULL,
  `persp_color` varchar(6) NOT NULL,
  `persp_description` text,
  PRIMARY KEY (`persp_id`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tb_perspectives`
--

LOCK TABLES `tb_perspectives` WRITE;
/*!40000 ALTER TABLE `tb_perspectives` DISABLE KEYS */;
INSERT INTO `tb_perspectives` VALUES (1,'Financeira','00FF00','Financeira'),(2,'Cliente','FFFF99','Cliente'),(3,'Processos Internos','00ccff','Processos Internos'),(4,'Aprendizado e Crescimento','FF6633','Aprendizado e Crescimento'),(5,'Ambiente Econômico','006666',''),(6,'Ambiente de Negócios','006666',''),(7,'Pesquisa e desenvolvimento','006666',''),(8,'Poder Executivo, Legislativo e Judiciário','006666',''),(9,'Educação e Cultura','006666',''),(10,'Meio Ambiente','006666',''),(11,'Legislação Municipal, Estadual e Federal ','006666',''),(12,'Comunicação','006666','');
/*!40000 ALTER TABLE `tb_perspectives` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tb_questions`
--

DROP TABLE IF EXISTS `tb_questions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tb_questions` (
  `quest_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `persp_id` int(10) unsigned NOT NULL,
  `qset_id` int(10) unsigned NOT NULL,
  `quest_text` text NOT NULL,
  `quest_environment` tinyint(2) NOT NULL DEFAULT '1' COMMENT '0 Interno, 1 Externo',
  `quest_status` tinyint(2) NOT NULL DEFAULT '1' COMMENT '0 inativo, 1 ativo',
  `quest_iscriticalkey` tinyint(2) NOT NULL DEFAULT '0' COMMENT 'o false, 1 true',
  `quest_positiveswot_full` text NOT NULL,
  `quest_negativeswot_full` text NOT NULL,
  `quest_positiveswot` text NOT NULL,
  `quest_negativeswot` text NOT NULL,
  `quest_variable` text NOT NULL,
  PRIMARY KEY (`quest_id`),
  KEY `pergunta_FKIndex1` (`qset_id`),
  KEY `pergunta_FKIndex2` (`persp_id`),
  CONSTRAINT `quest_has_persp` FOREIGN KEY (`persp_id`) REFERENCES `tb_perspectives` (`persp_id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `quest_in_qset` FOREIGN KEY (`qset_id`) REFERENCES `tb_questions_sets` (`qset_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=63 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tb_questions`
--

LOCK TABLES `tb_questions` WRITE;
/*!40000 ALTER TABLE `tb_questions` DISABLE KEYS */;
INSERT INTO `tb_questions` VALUES (1,1,2,'Volume de faturamento atual da empresa',0,1,0,'O volume de faturamento atual da empresa é compatível com o potencial do mercado e de acordo com a demanda dos clientes.','O volume de faturamento atual da empresa encontra-se muito abaixo do potencial do mercado e da demanda dos clientes.','Volume de faturamento satisfatório','Volume de faturamento insatisfatório','Volume de Faturamento'),(2,1,2,'Controle das contas a pagar e das contas a receber',0,1,0,'O controle das contas a pagar e das contas a receber  é muito bem trabalhado, consistente  e contempla relatório de posicionamento diário.','O controle das contas a pagar e das contas a receber apresenta-se inconsistente por falhas e omissões de dados e informações.','Controle contas a pagar satisfátório','Controle contas a pagar insatisfátório','Contas a Pagar'),(3,1,6,'Gestão diária do fluxo de caixa ou movimentação financeira\n',0,1,0,'A gestão diária do fluxo de caixa ou movimentação financeira é realizada de forma empírica sem controle por parte da direção da empresa. ','A gestão diária do fluxo de caixa ou movimentação financeira é realizada de forma profissional e controlada pela direção da empresa. ','Eficiência na gestão do Fluxo de Caixa','Gestão do Fluxo de Caixa ineficiente','Gestão do Fluxo de Caixa'),(4,1,2,'Apuração de resultados - lucro ou prejuízo - pela contabilidade',0,1,0,'A apuração de resultados - lucro ou prejuízo - pela contabilidade é realizada pontualmente até o quinto dia útil do mês seguinte ao do fechamento.','A apuração de resultados - lucro ou prejuízo - pela contabilidade apresenta-se comprometida por conta de freqüentes atrasos no fechamento mensal.','Eficiência na apuração de resultados','Ineficiência na apuração de resultados ','Apuração de Resultados'),(5,1,2,'Apuração, controle e corte de custos',0,1,0,'A apuração, controle e corte de custos é muito bem trabalhada por meio de um sistema de informação que alcança toda a empresa.','A apuração, controle e corte de custos não é trabalhada de forma sistemática no âmbito de toda a empresa por falta de cultura e prática efetiva.','Eficiência na apuração, controle e corte de custos. ','Ineficiência na apuração, controle e corte de custos. ','Cortes de Custos'),(6,1,2,'Combate a desperdícios de recursos financeiros, materiais e esforços humanos',0,1,0,'O combate a desperdícios de recursos financeiros, materiais e esforços humanos é bem trabalhado por meio de formação e orientação econômica.','O combate a desperdícios de recursos financeiros, materiais e esforços humanos não é trabalhado por falta de formação e orientação econômica.','Baixos desperdícios de recursos financeiros','Elevados desperdícios de recursos financeiros','Desperdícios de recursos financeiros'),(7,1,2,'Elaboração, execução e controle de orçamento empresarial',0,1,0,'A elaboração, execução e controle de orçamento empresarial  é uma realidade prática, consolidada e considerada estratégica.','A elaboração, execução e controle de orçamento empresarial não existe  implantada na empresa por falta de cultura e prática efetiva.','Eficiência na gestão do Orçamento Empresarial','Deficiência na gestão do Orçamento Empresarial','Orçamento Empresarial'),(8,1,6,'Auditoria interna para controle financeiro e administrativo',0,1,0,'Auditoria interna para controle financeiro e administrativo existe e  trabalha inspeções preventivas e corretivas no âmbito de toda a empresa.','Auditoria interna para controle financeiro e administrativo não existe na empresa por falta de cultura e implantação efetiva.','Eficiência na Auditoria Interna','Ineficiência na Auditoria Interna','Auditoria Interna '),(9,1,6,'Análise do lucro da empresa em comparação com a lucratividade do setor',0,1,0,'A análise do lucro da empresa em comparação com a lucratividade do setor é realizada com base em dados e informações confiáveis.','A análise do lucro da empresa em comparação com a lucratividade do setor não é realizada por falta de conhecimento e desinformação.','Razoável lucro empresarial','Insuficiente lucro empresarial','Lucro empresarial'),(10,1,2,'Política e critérios para destinação do lucro da empresa\n',0,1,0,'A política e critérios para destinação do lucro da empresa existe, é muito bem trabalhada e seguida rigorosamente em benefício da organização.','A política e critérios para destinação do lucro da empresa não existe, gera conflitos de interesses e prejudica o desenvolvimento da organização.','Destinação do Lucro Empresarial satisfatório ','Destinação do Lucro Empresarial insatisfatório ','Destinação do Lucro Empresarial'),(11,1,3,'Investimentos estratégicos para o desenvolvimento de todas as áreas da empresa',0,1,0,'Os investimentos estratégicos para desenvolvimento da empresa são planejados e realizados de forma inteligente com visão de futuro.','Os investimentos estratégicos para desenvolvimento da empresa são desenvolvida de forma tímida ou praticamente não existe.','Investimentos estratégicos satisfatório','Investimentos estratégicos insuficientes','Investimentos Estratégicos'),(12,1,4,'Combate à carga tributária por meio do planejamento tributário',0,1,0,'Combate à carga tributária por meio do planejamento tributário é  realizado sistematicamente com suporte jurídico. ','Combate à carga tributária por meio do planejamento tributário não é perseguido por falta de cultura e prática efetiva.','Combate à Carga Tributária satisfatório','Combate à Carga Tributária insatisfatório','Combate à Carga Tributária'),(13,1,5,'Relacionamento da empresa com os bancos',0,1,0,'O relacionamento da empresa com os bancos é bem administrado e incorpora marcas de negociações permanentes para bom entendimento.','O relacionamento da empresa com os bancos não é bem administrado o que gera conflitos de interesses e desentendimentos.','Relações com os bancos satisfatória','Relações com os bancos insatisfatória','Relacionamento com os Bancos'),(14,2,4,'Realização de pesquisa sobre o mercado',0,1,0,'A realização de pesquisa sobre o mercado explorado pela empresa é realizada de forma sistemática e é considerada estratégica.','A realização de pesquisa sobre o mercado explorado pela empresa, de forma sistemática, não existe por falta de investimentos neste sentido.','Eficiência nas pesquisas de mercado','Deficiência nas pesquisas de mercado','Pesquisa de Mercado'),(15,3,3,'Conhecimento sobre a realidade da concorrência',0,1,0,'O conhecimento sobre a realidade da concorrência é muito bem trabalhado pela área de mercado e clientes com dados e informações consistentes.','O conhecimento sobre a realidade da concorrência não é trabalhado de forma sistemática pela área responsável por mercado e clientes.','Eficiência no conhecimento sobre a realidade da concorrência.','Deficiencia no conhecimento sobre a realidade da concorrência.','Conhecimento da Concorrência'),(16,2,5,'Relacionamento entre a empresa e seus fornecedores',0,1,0,'O relacionamento  entre a empresa e seus fornecedores é marcado por um clima de parceria nas negociações de preço, qualidade e prazo de entrega.  ','O relacionamento  entre a empresa e seus fornecedores não é bem administrado  e convive com conflitos e preço, qualidade e prazo de entrega.  ','Relacionamento com os fornecedores satisfatório','Relacionamento com os fornecedores insatisfatório','Relacionamento com os Fornecedores'),(17,2,6,'Atualização do portfólio ou linha de produtos e serviços',0,1,0,'A atualização do portfólio ou linha de produtos e serviços da empresa é trabalhada por meio de estudos e pesquisas  junto ao mercado e aos clientes.','A atualização do portfólio ou linha de produtos e serviços oferecidos pela empresa aos clientes não é trabalhada de forma sistemática.','Atualização do portifólio satisfatório','Atualização do portifólio insatisfatório','Atualização do Portifólio'),(18,2,4,'Logística para distribuição ou entrega de produtos e serviços',0,1,0,'A logística para distribuição ou entrega de produtos e serviços junto aos clientes é muito bem administrada apresentado-se eficiente, eficaz, ágil e flexível.',' A logística para distribuição ou entrega de produtos e serviços junto aos clientes incorpora marcas de ineficiência e ineficácia provocando insatisfação.','A logística para distribuição eficiênte','A logística para distribuição ineficiênte','Logistica de Dristribuição de Produtos e Serviços'),(19,2,5,'Levantamento de desejos e necessidades dos clientes',0,1,0,'O levantamento de desejos e necessidades dos clientes para atender e superar suas expectativas é realizado de forma sistemática e considerado estratégico.','O levantamento de desejos e necessidades dos clientes na perspectiva de atender e superar suas expectativas não é realizado por falta de visão estratégica.','Excelência no levantamento de desejos e necessidades dos clientes','Falta de excelência no levantamento de desejos e necessidades dos clientes','Desejos e Necessidades dos Clientes'),(20,2,5,'Nível de satisfação dos clientes',0,1,0,'O nível de satisfação dos clientes para com os produtos e serviços ofertados pela empresa é levantado, auferido  e analisado continuamente.','O nível de satisfação dos clientes para com os produtos e serviços  ofertados pela empresa não é auferido nem analisado de forma sistemática.','Eficiência na medição do nível de satisfação dos clientes','Ineficiência na medição do nível de satisfação dos clientes','Satisfação dos Clientes'),(21,2,4,'Ações direcionadas para encantar e superar as expectativas do cliente',0,1,0,'As ações direcionadas para encantar e superar as expectativas do cliente é real, consolidada, produz resultados e diferencial competitivo.','As ações direcionadas para encantar e superar as expectativas do cliente não são trabalhadas por falta de conhecimento e definições estratégicas. ','Ações eficientes para encantar e superar as expectativas dos clientes','Ações ineficientes para encantar e superar as expectativas dos clientes','Encantamento dos Clientes'),(22,2,6,'Formação ou composição de preços produtos e serviços',0,1,0,'A formação ou composição de preços de produtos e serviços oferecidos pela empresa é muito bem trabalhada por meio de estudos e análises.','A  formação ou composição de preços de produtos e serviços oferecidos pela empresa não contempla estudos e análises mais aprofundadas','Composição de preços adequados','Inadequada a composição de preços.','Composição de preços'),(23,2,1,'Perfil, formação e desempenho da força de vendas',0,1,0,'O perfil, formação e desempenho da força de vendas é bom, compatível  com as necessidades e atende muito bem as exigências dos clientes.','O perfil, formação e desempenho da força de vendas é inadequado e deixa a desejar em função do elevado nível exigências por parte dos clientes.','Satisfatório desempenho da força de vendas','Insatisfatório desempenho da força de vendas','Desmpenho da Força de Vendas'),(24,12,9,'Publicidade para divulgação da marca, produtos e serviços',1,1,0,'A publicidade para divulgação da marca, produtos e serviços da empresa não é muito bem trabalhada e de forma sistemática.','A publicidade para divulgação da marca, produtos e serviços da empresa não é trabalhada de forma adequada e sistematicamente.','Excelência da divulgação da marca','Excelência da divulgação da marca','Divulgação da Marca'),(25,2,2,'Vendas de produtos e serviços pela internet',0,1,0,'As vendas de produtos e serviços pela internet para agilizar e dinamizar o atendimento a clientes é real, estratégico e consolidado.','As vendas de produtos e serviços pela internet para agilizar e dinamizar o atendimento a clientes foi apenas projetado, mas não existe.','Utilização de comércio eletrônico satisfatório','Utilização de comércio eletrônico insatisfatório','Vendas na Internet'),(26,6,4,'Preparação da empresa para a globalização e sua venda por fusão',1,1,0,'A preparação da empresa para a globalização e sua venda por fusão é trabalhada de forma sistemática e incorpora visão estratégica.','A preparação da empresa para a globalização e sua venda por fusão não é trabalhada por falta de conhecimento sobre a assunto e visão estratégica.','Preocupação com a globalização e sua venda por fusão.','Preocupação com a globalização e sua venda por fusão.','Globalização'),(27,3,3,'Direção estratégica com decisões para 10 anos a frente',0,1,0,'A direção estratégica para 10 anos a frente é uma realidade prática e objetiva e incorpora uma visão estratégica de futuro a longuíssimo prazo.','A direção estratégica para 10 anos a frente não existe por falta de cultura, conhecimentos estratégicos e implantação efetiva desta prática. ','Eficiência nas práticas de direção estratégicas','Ineficiência nas práticas de direção estratégica','Direção Estratégica'),(28,3,4,'Execução  e monitoramento de planos estratégico, tático e operacional\n',0,1,0,'A Execução  e monitoramento de planos estratégico, tático e operacional  existe e é muito bem trabalhada de forma sistemática  em toda a empresa.','A Execução  e monitoramento de planos estratégico, tático e operacional não existe por falta de implantação e consolidação do planejamento estratégico.','Boa cultura no Planejamento Estratégico','Baixa cultura no Planejamento Estratégico','Planejamento Estratégico'),(29,3,6,'Estrutura organizacional com definição de hierarquia  e atribuições de cada órgão\n',0,1,0,'A estrutura organizacional com definição de hierarquia e atribuições de cada órgão é trabalhada tecnicamente para torná-la ágil, flexível e documentada.','A estrutura organizacional com definição de hierarquia e atribuições de cada órgão é desatualizada ou nunca foi trabalhada de forma tecnica.','Estrutura organizacional adequada','Estrutura organizacional inadequada','Estrutura Organizacional'),(30,3,6,'Organização de recursos materiais, tecnológicos e equipe de trabalho\n',0,1,0,'A organização de recursos materiais, tecnológicos e equipe de trabalho é bem trabalhada  na aquisição e  alocação para funcionamento dos órgãos. ','A organização de recursos materiais, tecnológicos e equipe de trabalho não é bem trabalhada ou deixa muito a desejar em termos de aquisição e alocação. ','Boa organização dos Recursos Materiais e Tecnológicos','Baixa organização dos Recursos Materiais e Tecnológicos','Recursos Materiais e Tecnológicos'),(31,3,6,'Mapeamento e redesenho de processos, atividades e rotinas',0,1,0,'O mapeamento e redesenho de processos, atividades e rotinas é realizado  continuamente com simplificação e racionalização dos trabalhos na empresa.','O mapeamento e redesenho de processos, atividades e rotinas não contempla ações técnicas para simplificação e racionalização dos trabalhos na empresa.','Eficiência na gestão dos processos empresariais','Ineficiência na gestão dos processos empresariais','Gestão de Processos'),(32,3,7,'Informatização da empresa com sistemas, equipamentos e usuários finais',0,1,0,'A informatização da empresa com sistemas, equipamentos e usuários finais  é um processo muito dinâmico planejado, organizado e com critérios técnicos.','A informatização da empresa com sistemas, equipamentos e usuários finais não é trabalhada de forma planejada nem organizada e com critérios técnicos.','Bom nível de informatização','Baixo nível de informatização','Informatização'),(33,3,1,'Treinamento dos usuários finais em operação de sistemas e  equipamentos de informática',0,1,0,'O treinamento dos usuários finais em operação de sistemas e equipamentos de informática é contínuo e muito bem realizado.','O treinamento dos usuários finais em operação de sistemas e equipamentos de informática é superficial, inadequado ou não existe.','Satisfatória formação na operação de sistemas','Insatisfatória formação na operação de sistemas','Capacitação em TIC'),(34,3,7,'Definição de critérios e especificações técnicas para aquisição de sistemas\n',0,1,0,'A definição de critérios e especificações técnicas para aquisição de sistemas são trabalhadas para aquisições  adequadas e econômicas.','A definição de  critérios e especificações técnicas para aquisição de sistemas não é trabalhada. As aquisições são inadequadas, inconsistentes e caras.','Eficiência na aquisição de sistemas','Ineficiência na aquisição de sistemas','Aquisição de Sistemas'),(35,3,7,'Definição de critérios e especificações técnicas para aquisição de equipamentos de tecnologia da informação',0,1,0,'A definição de critérios e especificações técnicas para aquisição de equipamentos de tecnologia da informação é muito bem trabalhada.','A definição de critérios e especificações técnicas para aquisição de equipamentos de tecnologia da informação não é bem trabalhada.','Eficiência na aquisição de equipamentos de tecnologia da informação e comunicação','Ineficiência na aquisição de equipamentos de tecnologia da informação e comunicação ','Aquisição de equipamentos de tecnologia da informação e comunicação'),(36,3,3,'Desenvolvimento de trabalhos técnicos para inovação da empresa',0,1,0,'O desenvolvimento de trabalhos técnicos para inovação da empresa é trabalhado  de forma consistente e contínua gerando bons resultados.','O desenvolvimento de trabalhos técnicos para inovação da empresa não é trabalhado por falta de cultura, conhecimentos e prática efetiva.','Boas práticas da inovação da Empresa','Ausência das práticas da inovação na Empresa','Inovação Empresarial'),(37,3,6,'Implantação do sistema de gestão da qualidade - SGQ  com conquista e manutenção de certificação\n',0,1,0,'A implantação do sistema de gestão da qualidade - SGQ  com conquista e manutenção de certificação é um trabalho bem realizado e consistente.','A implantação do sistema de gestão da qualidade - SGQ  com conquista e manutenção de certificação nunca foi trabalhado ou foi superficialmente.','Eficiência na Gestão da Qualidade','Ineficiência na Gestão da Qualidade','Gestão da Qualidade'),(38,3,6,'Implantação de Pesquisa e Desenvolvimento - P&D para inovação de produtos e serviços\n',0,1,0,'A implantação de Pesquisa e Desenvolvimento - P&D para inovação de produtos e serviços existe, é bem trabalhada e considerada estratégica.','A implantação de  Pesquisa e Desenvolvimento - P&D para inovação de produtos e serviços é uma novidade nunca trabalhada na empresa.','Eficiência na pesquisa e desenvolvimento','Ineficiência na pesquisa e desenvolvimento','P&D - Pesquisa e Desenvolvimento'),(39,3,6,'Posicionamento hierárquico e o prestígio da área de gestão de pessoas e talentos\n',0,1,0,'O posicionamento hierárquico e o prestígio da área de gestão de pessoas e talentos é comprometido  pela falta de visão e valorização estratégica.','O posicionamento hierárquico e o prestígio da área de gestão de pessoas e talentos é comprometido  pela falta de visão e valorização estratégica.','Gestão de pessoas e talentos eficiente','Ineficiência na gestão de pessoas e talentos','Gestão de Pessoas e Talentos'),(40,4,6,'Política de atração, contratação e retenção de talentos',0,1,0,'A política de atração, contratação e retenção de talentos para suprir as unidades da estrutura organizacional é bem trabalhada e gera bons resultados.','A política de atração, contratação e retenção de talentos para suprir as unidades da estrutura organizacional da empresa é muito fraca ou não existe.','Eficiência na contratação e retenção de talentos','Ineficiência na contratação e retenção de talentos','Contratação de Pessoal e Retenção de Talentos'),(41,4,1,'Desenvolvimento, capacitação e atualização contínua dos executivos da empresa',0,1,0,'O desenvolvimento, capacitação e atualização  contínua do principal executivo da empresa é real, bem direcionado e considerado estratégico.','O desenvolvimento, capacitação e atualização contínua  do principal executivo da empresa não acontece de forma sistemática ou simplesmente não existe. ','Regularidade na capacitação dos executivos da empresa','Ausência de capacitação para os executivos da empresa','Capacitação dos Executivos'),(42,4,1,'Desenvolvimento, capacitação e atualização contínua de gestores operacionais como gerentes e supervisores',0,1,0,'O desenvolvimento, capacitação e atualização de gestores operacionais como gerentes e supervisores é real, bem executado e considerado estratégico.','O desenvolvimento, capacitação e atualização contínua  de gestores operacionais como gerentes e supervisores é precário ou não  existe.','Eficiência da política de capacitação dos colaboradores táticos e operacionais','Ineficiência da política de capacitação dos colaboradores táticos e operacionais','Capacitação dos colaboradore dos níveis táticos e operacional'),(43,4,2,'Política de cargos, carreiras, salários e benefícios',0,1,0,'A política de cargos, carreiras, salários e benefícios é muito bem trabalhada e direcionada para estimular e manter a motivação de todos para o trabalho.','A política de cargos, carreiras, salários e benefícios é muito fraca ou não existe o que provoca desmotivação nos profissionais para o trabalho.','Eficiência na Política de cargos e carreiras','Ineficiência da política de cargos e carreiras','Política de Cargos e Carreiras'),(44,4,6,'Programa de integração e treinamento inicial para recém-contratados\n\n',0,1,0,'O programa de integração e treinamento inicial para recém- contratados é uma realidade prática e objetiva e de importância estratégica.','O programa de integração e treinamento inicial para recém- contratados não existe por falta de conhecimento sobre sua importância estratégica.','Boas práticas na integração dos recém-contratados','Ausência de práticas de integração para os  recém-contratados','Política para os recém-contratados'),(45,4,3,'Medição do clima organizacional interno de toda a empresa',0,1,0,'A medição de clima organizacional interno de toda a empresa é muito bem  trabalhado incorporando técnicas e métodos que garantem seu monitoramento','A medição de clima organizacional interno de toda a empresa não é trabalhado a técnica e sistemática ou nunca foi trabalhado por falta visão da direção.','Eficiência na aferição do clima organizacional','Ineficiência na aferição do clima organizacional','Clima Organizacional'),(46,4,3,'Política de aferição de resultados por intermédio de indicadores',0,1,0,'A avaliação de desempenho, reconhecimento e premiações  faz parte da cultura e é muito bem praticado no âmbito de toda a empresa. ','A avaliação de desempenho, reconhecimento e premiações  não faz parte da cultura nem é praticada no âmbito de toda a empresa. Existem ações isoladas.','Eficiência na aferição dos resultados por indicadores','Ausência de instrumentos para mensurar resultados','Gestão por indicadores'),(47,4,6,'Humanização, espiritualização e conscientização socioambiental das pessoas que trabalham na empresa',0,1,0,'A humanização, espiritualização e conscientização socioambiental das pessoas que trabalham na empresa são valores muito bem trabalhados com resultados.','A humanização, espiritualização e conscientização socioambiental das pessoas que trabalham na empresa  são valores que não trabalhados adequadamente.','Eficiência na gestão da humanização, espiritualização e conscientização socioambiental','Ineficiência na gestão da humanização, espiritualização e conscientização socioambiental','Humanização, espiritualização e conscientização socioambiental'),(48,5,8,'Momento atual da economia',1,1,0,'O momento da economia é uma oportunidade porque pode impactar positivamente na expansão dos negócios da empresa.','O momento da economia é uma ameaça porque pode impactar negativamente os negócios da empresa por meio de crises.','Economia Mundial','Economia Mundial','Economia Mundial'),(49,5,8,'Desenvolvimento dos pólos regionais',1,1,0,'O desenvolvimento dos pólos regionais é uma oportunidade porque pode impactar positivamente na expansão dos negócios da empresa.','O desenvolvimento dos pólos regionais é uma ameaça porque pode impactar negativamente nos negócios da empresa pela concorrência.','Desenvolvimento dos pólos regionais ','Desenvolvimento dos pólos regionais ','Desenvolvimento dos pólos regionais '),(50,5,8,'Variação atual da taxa de inflação da economia',1,1,0,'A taxa de inflação da economia  quando baixa é uma oportunidade porque protege a lucratividade empresarial que trabalha com ganho real .','A taxa de inflação da economia  quando alta  é uma ameaça porque dificulta a lucratividade empresarial que trabalha com ganho real .','Taxa de inflação da economia','Taxa de inflação da economia','Taxa de inflação da economia'),(51,5,8,'Taxas de juros do mercado',1,1,0,'As taxas de juros do mercado se  baixa são oportunidade porque estimula   empréstimos para financiamento da expansão dos negócios da empresa.','As taxas de juros do mercado se elevadas são ameaças porque inibe a busca por empréstimo para financiar expansão dos negócios da empresa.','Taxas de juros do mercado ','Taxas de juros do mercado ','Taxas de juros do mercado '),(52,8,10,'Influência da carga tributária na composição de custos da empresa',1,1,0,'A carga tributária municipal quando baixa é uma oportunidade porque impacta positivamente diminuindo os custos da empresa. ','A carga tributária municipal quando elevada  é uma ameaça porque impacta negativamente na composição dos custos da empresa. ','Carga Tributária','Carga Tributária','Carga Tributária'),(53,8,10,'Legislações de pesquisa e desenvolvimento',1,1,0,'A legislação sobre incentivo a pesquisa e inovação é oportunidade porque impactar positivamente a empresa por sua simplicidade e agilidade.','A legislação sobre incentivo a pesquisa e inovação é  ameaça porque impactar negativamente a empresa por sua complexidade e burocracia.','Legislação de P&D','Legislação de P&D','Legislação sobre incentivo a pesquisa e inovação '),(54,8,10,'Realização de negócios com governo nas três esferas de poder',1,1,0,'Os negócios com governos nas três esferas de poder são oportunidades porque podem impactar positivamente na expansão do volume de negócios da empresa.','Os negócios com governos nas três esferas de poder  são ameaças porque impactam negativamente nos negócios da empresa pela ética e morosidade.','Negócios com governos','Negócios com governos','Negócios com governos'),(55,8,10,'Eleições majoritárias',1,1,0,'As eleições majoritárias são oportunidades porque promovem impactos positivos no poder, legislação e ações de governo.','As eleições majoritárias são ameaças porque promovem  impactos negativos no exercício do poder, legislação e ações no governo.','Eleições majoritárias','Eleições majoritárias','Eleições majoritárias'),(56,6,8,'Envolvimento da empresa em ações de responsabilidade social',1,1,0,'O envolvimento de responsabilidade social é oportunidade porque impactam positivamente na estratégia da empresa.','O envolvimento de responsabilidade social é ameaça por impactar negativamente na cultura e nos custos da empresa.','Responsabilidade social','Responsabilidade social','Responsabilidade social'),(57,12,9,'Mídias convencionais de comunicação nos negócios da empresa\n',1,1,0,'As mídias de comunicação representam uma oportunidade para o crescimento da empresa','As mídias de comunicação representam uma ameaça para o crescimento da empresa','Mídias de Comunicação','Mídias de Comunicação','Mídias de Comunicação'),(58,12,9,'As redes sociais de comunicação(twitter, facebook...) nos negócios da empresa\n',1,1,0,'As redes sociais representam uma oportunidade para o crescimento da empresa','As redes sociais representam uma ameaça para o crescimento da empresa','Redes Sociais','Redes Sociais','Redes Sociais'),(59,6,8,'Globalização da economia com chegada de concorrente internacionais',1,1,0,'A Chegada dos concorrentes intenacionais é um oportunidade por desafiar a empresa para se preparar para a concorrência','A Chegada dos concorrentes intenacionais é uma ameaçãopelo grande poder econômico da conrrencia','Globalização ','Globalização ','Globalização '),(60,6,8,'Acirramento da concorrência no seguimento de negócio da empresa',1,1,0,'Acirramento da concorrência é oportunidade porque exige uma estratégia de enfrentamento','Acirramento da concorrência é ameaça porque implica na possibilidade de perda de mercado.','Concorrência ','Concorrência ','Concorrência '),(61,6,8,'Práticas de acordos, alianças e parcerias estratégicas',1,1,0,'Práticas de acordos, alianças e parcerias é oportunidade porque fortalece a empresa para concorrência','Práticas de acordos, alianças e parcerias é ameaça pela falta de cultura e experiência real','Acordos, alianças e parcerias','Acordos, alianças e parcerias','Acordos, alianças e parcerias'),(62,6,8,'Envolvimento da empresa em ações de responsabilidade ambiental',1,1,0,'O envolvimento de responsabilidade ambiental é oportunidade porque impactam positivamente junto a sociedade','O envolvimento de responsabilidade ambiental é ameaça porque impactam negativamente junto a sociedade','Responsabilidade ambiental','Responsabilidade ambiental','Responsabilidade ambiental');
/*!40000 ALTER TABLE `tb_questions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tb_questions_sets`
--

DROP TABLE IF EXISTS `tb_questions_sets`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tb_questions_sets` (
  `qset_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `qset_name` varchar(100) NOT NULL,
  `qset_description` text,
  PRIMARY KEY (`qset_id`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tb_questions_sets`
--

LOCK TABLES `tb_questions_sets` WRITE;
/*!40000 ALTER TABLE `tb_questions_sets` DISABLE KEYS */;
INSERT INTO `tb_questions_sets` VALUES (1,'Formação',''),(2,'Eficiência',''),(3,'Inteligência',''),(4,'Planejamento',''),(5,'Relacionamento',''),(6,'Gestão',''),(7,'Tecnologia',''),(8,'Negócios',''),(9,'Comunicação',''),(10,'Governo','Governo');
/*!40000 ALTER TABLE `tb_questions_sets` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tb_respondents`
--

DROP TABLE IF EXISTS `tb_respondents`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tb_respondents` (
  `resp_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `org_id` int(10) unsigned NOT NULL,
  `resp_email` varchar(255) NOT NULL,
  `resp_dtcreation` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `resp_allowreturn` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `resp_allowpartial` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `resp_hasemailerror` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `resp_hascompleted` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `resp_orglevel` tinyint(2) unsigned NOT NULL,
  `resp_dtentered` datetime DEFAULT NULL,
  `resp_dtfinished` datetime DEFAULT NULL,
  `resp_lastip` varchar(20) DEFAULT NULL,
  PRIMARY KEY (`resp_id`),
  UNIQUE KEY `respondente_organization_UNIQUE` (`org_id`,`resp_email`),
  KEY `respondent_organization_idx` (`org_id`),
  CONSTRAINT `respondent_organization` FOREIGN KEY (`org_id`) REFERENCES `tb_organizations` (`org_id`) ON DELETE CASCADE ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tb_respondents`
--

LOCK TABLES `tb_respondents` WRITE;
/*!40000 ALTER TABLE `tb_respondents` DISABLE KEYS */;
INSERT INTO `tb_respondents` VALUES (1,1,'titolixao@gmail.com','2018-02-26 22:58:13',0,0,0,0,0,NULL,NULL,NULL),(3,1,'titopublico@gmail.com','2018-02-26 23:09:26',0,1,0,0,0,NULL,NULL,NULL),(13,1,'titolixao987654321@gmail.com','2018-02-27 23:13:47',0,1,0,0,1,NULL,NULL,NULL);
/*!40000 ALTER TABLE `tb_respondents` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tb_strategic_planning`
--

DROP TABLE IF EXISTS `tb_strategic_planning`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tb_strategic_planning` (
  `plan_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `org_id` int(10) unsigned NOT NULL,
  `plan_dtcreation` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `plan_status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '0-Fechado, 1-Em construção, 2-Em execução, 3-Suspenso\n',
  `plan_title` text NOT NULL,
  `plan_team` text NOT NULL,
  `plan_mission` text,
  `plan_vision` text,
  `plan_values` text,
  PRIMARY KEY (`plan_id`),
  KEY `tb_strategic_planning_FKIndex1` (`org_id`),
  CONSTRAINT `plan_from_org` FOREIGN KEY (`org_id`) REFERENCES `tb_organizations` (`org_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `tb_strategic_planning_ibfk_1` FOREIGN KEY (`org_id`) REFERENCES `tb_organizations` (`org_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tb_strategic_planning`
--

LOCK TABLES `tb_strategic_planning` WRITE;
/*!40000 ALTER TABLE `tb_strategic_planning` DISABLE KEYS */;
INSERT INTO `tb_strategic_planning` VALUES (1,1,'2018-03-13 02:19:48',1,'Planejamento 2018','Um time grande','Uma missão \r\nde duas linhas','Uma visão\r\nquebrada\r\nem três','valores\r\ne mais valores\r\ninfinitos valores\r\nmuitos mesmo');
/*!40000 ALTER TABLE `tb_strategic_planning` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tb_tmp`
--

DROP TABLE IF EXISTS `tb_tmp`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tb_tmp` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `data` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tb_tmp`
--

LOCK TABLES `tb_tmp` WRITE;
/*!40000 ALTER TABLE `tb_tmp` DISABLE KEYS */;
/*!40000 ALTER TABLE `tb_tmp` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tb_users`
--

DROP TABLE IF EXISTS `tb_users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tb_users` (
  `user_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_login` varchar(20) NOT NULL,
  `user_password` varchar(100) NOT NULL,
  `user_isadmin` tinyint(4) NOT NULL DEFAULT '0',
  `user_dtcriation` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `user_dtupdate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `user_cpf` varchar(11) NOT NULL,
  `user_name` varchar(200) NOT NULL,
  `user_email` varchar(255) NOT NULL,
  `user_islogged` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `user_phone` varchar(100) DEFAULT NULL,
  `user_position` varchar(100) DEFAULT NULL,
  `user_photo` varchar(255) DEFAULT NULL,
  `user_dtlastlogin` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`user_id`),
  UNIQUE KEY `user_login_UNIQUE` (`user_login`),
  UNIQUE KEY `user_cpf_UNIQUE` (`user_cpf`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tb_users`
--

LOCK TABLES `tb_users` WRITE;
/*!40000 ALTER TABLE `tb_users` DISABLE KEYS */;
INSERT INTO `tb_users` VALUES (1,'admin','$2y$12$7qHvGp5bJpnHQkoL/LYvj.MEE/z9rYkFOxf7dRYBb3cvky.AUSi6G',1,'2018-02-14 04:38:02','2018-03-03 00:57:47','11111111111','Administrador','titokenzo@gmail.com',0,'(81)99488-6132','Consultor','','2018-02-14 04:38:02'),(2,'titokenzo','$2y$12$ZfphMwK6E5ADmxtu1f1n/e7QL/gU0o4kSTDxPABJxuEdUZuEjCxAq',0,'2018-02-16 02:09:12','2018-02-16 02:09:12','03176865422','Tito Kenzo','titokenzo@gmail.com',0,'(81)988312611','Consultor',NULL,NULL);
/*!40000 ALTER TABLE `tb_users` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tb_users_organizations`
--

DROP TABLE IF EXISTS `tb_users_organizations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tb_users_organizations` (
  `user_id` int(10) unsigned NOT NULL,
  `org_id` int(10) unsigned NOT NULL,
  `userorg_type` tinyint(3) unsigned NOT NULL DEFAULT '2' COMMENT '1- Consultor Interno, 2-Colaborador',
  PRIMARY KEY (`user_id`,`org_id`),
  KEY `org_key_idx` (`org_id`),
  CONSTRAINT `org_key` FOREIGN KEY (`org_id`) REFERENCES `tb_organizations` (`org_id`) ON DELETE CASCADE ON UPDATE NO ACTION,
  CONSTRAINT `user_key` FOREIGN KEY (`user_id`) REFERENCES `tb_users` (`user_id`) ON DELETE CASCADE ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tb_users_organizations`
--

LOCK TABLES `tb_users_organizations` WRITE;
/*!40000 ALTER TABLE `tb_users_organizations` DISABLE KEYS */;
INSERT INTO `tb_users_organizations` VALUES (1,1,1),(1,2,1),(2,1,2);
/*!40000 ALTER TABLE `tb_users_organizations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tb_userspasswordsrecoveries`
--

DROP TABLE IF EXISTS `tb_userspasswordsrecoveries`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tb_userspasswordsrecoveries` (
  `recovery_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned NOT NULL,
  `recovery_ip` varchar(45) NOT NULL,
  `recovery_dtrecovery` datetime DEFAULT NULL,
  `recovery_dtregister` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`recovery_id`),
  KEY `fk_userspasswordsrecoveries_users_idx` (`user_id`),
  CONSTRAINT `fk_userspasswordsrecoveries_users` FOREIGN KEY (`user_id`) REFERENCES `tb_users` (`user_id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tb_userspasswordsrecoveries`
--

LOCK TABLES `tb_userspasswordsrecoveries` WRITE;
/*!40000 ALTER TABLE `tb_userspasswordsrecoveries` DISABLE KEYS */;
INSERT INTO `tb_userspasswordsrecoveries` VALUES (1,1,'127.0.0.1','2018-02-26 22:05:08','2018-02-27 01:02:19'),(2,1,'127.0.0.1','2018-02-26 22:16:23','2018-02-27 01:09:37'),(3,1,'127.0.0.1',NULL,'2018-02-27 01:17:30'),(4,1,'127.0.0.1',NULL,'2018-02-27 01:28:17'),(5,1,'127.0.0.1',NULL,'2018-02-27 01:28:56'),(6,1,'127.0.0.1',NULL,'2018-02-27 01:29:02'),(7,1,'127.0.0.1',NULL,'2018-03-03 00:48:59'),(8,1,'127.0.0.1','2018-03-02 21:57:46','2018-03-03 00:51:55'),(9,1,'127.0.0.1',NULL,'2018-03-03 00:52:27'),(10,1,'127.0.0.1',NULL,'2018-03-03 00:53:09'),(11,1,'127.0.0.1',NULL,'2018-03-03 00:56:42');
/*!40000 ALTER TABLE `tb_userspasswordsrecoveries` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping routines for database 'easyplanning'
--
/*!50003 DROP PROCEDURE IF EXISTS `sp_respondent_create` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'STRICT_TRANS_TABLES,NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_respondent_create`(
porg_id INT,
presp_email VARCHAR(200),
presp_orglevel tinyint,
presp_allowreturn tinyint,
presp_allowpartial tinyint
)
BEGIN
	
	INSERT INTO tb_respondents (org_id, resp_email, resp_orglevel, resp_allowreturn, resp_allowpartial)
    VALUES(porg_id, presp_email, presp_orglevel, presp_allowreturn, presp_allowpartial);
    
    SELECT * FROM tb_respondents WHERE resp_id = LAST_INSERT_ID();
    
END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `sp_userspasswordsrecoveries_create` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'STRICT_TRANS_TABLES,NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_userspasswordsrecoveries_create`(
piduser INT,
pip VARCHAR(45)
)
BEGIN
	
	INSERT INTO tb_userspasswordsrecoveries (user_id, recovery_ip)
    VALUES(piduser, pip);
    
    SELECT * FROM tb_userspasswordsrecoveries
    WHERE recovery_id = LAST_INSERT_ID();
    
END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `sp_usersupdate_save` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'STRICT_TRANS_TABLES,NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_usersupdate_save`(
piduser INT(10),
pname VARCHAR(100), 
plogin VARCHAR(20), 
ppass VARCHAR(100), 
pemail VARCHAR(200), 
pphone VARCHAR(100), 
pisadmin TINYINT
)
BEGIN
    DECLARE vidperson INT;
	
    SELECT person_id INTO vidperson FROM tb_users WHERE user_id = piduser;
    
	UPDATE tb_persons SET person_name=pname, person_email=pemail, person_phone=pphone WHERE person_id=vidperson;
    
    UPDATE tb_users SET user_login=plogin, user_password=ppass, user_isadmin=pisadmin, user_dtupdate=CURRENT_TIMESTAMP WHERE user_id=piduser;
    
    SELECT * FROM tb_users u INNER JOIN tb_persons p USING(person_id) WHERE u.user_id = piduser;
END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `sp_users_delete` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'STRICT_TRANS_TABLES,NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_users_delete`(
piduser INT
)
BEGIN
	
    DECLARE vidperson INT;
    
	SELECT person_id INTO vidperson
    FROM tb_users
    WHERE user_id = piduser;
    
    DELETE FROM tb_users WHERE user_id = piduser;
    DELETE FROM tb_persons WHERE person_id = vidperson;
END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `sp_users_save` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'STRICT_TRANS_TABLES,NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_users_save`(
pname VARCHAR(100), 
plogin VARCHAR(20), 
ppass VARCHAR(100), 
pemail VARCHAR(200), 
pphone VARCHAR(100), 
pisadmin TINYINT
)
BEGIN
    DECLARE vidperson INT;
    
	INSERT INTO tb_persons (person_name, person_email, person_phone)
    VALUES(pname, pemail, pphone);
    
    SET vidperson = LAST_INSERT_ID();
    
    INSERT INTO tb_users (person_id, user_login, user_password, user_isadmin)
    VALUES(vidperson, plogin, ppass, pisadmin);
    
    SELECT * FROM tb_users a INNER JOIN tb_persons b USING(person_id) WHERE a.user_id = LAST_INSERT_ID();
END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2018-03-15  0:51:03
