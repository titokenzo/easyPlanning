CREATE DATABASE  IF NOT EXISTS `easyplanning` /*!40100 DEFAULT CHARACTER SET utf8 */;
USE `easyplanning`;
-- MySQL dump 10.13  Distrib 5.7.12, for Win64 (x86_64)
--
-- Host: localhost    Database: easyplanning
-- ------------------------------------------------------
-- Server version	5.7.16-log

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
-- Table structure for table `tb_persons`
--

DROP TABLE IF EXISTS `tb_persons`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tb_persons` (
  `person_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `person_name` varchar(100) NOT NULL,
  `person_email` varchar(200) NOT NULL,
  `person_dtcreation` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `person_phone` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`person_id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

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
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

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
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

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
  KEY `respondent_organization_idx` (`org_id`),
  CONSTRAINT `respondent_organization` FOREIGN KEY (`org_id`) REFERENCES `tb_organizations` (`org_id`) ON DELETE CASCADE ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `tb_strategic_planning`
--

DROP TABLE IF EXISTS `tb_strategic_planning`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tb_strategic_planning` (
  `plan_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `org_id` int(10) unsigned NOT NULL,
  `plan_dtcriation` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `plan_isopen` tinyint(1) NOT NULL DEFAULT '1',
  `plan_title` text NOT NULL,
  `plan_team` text NOT NULL,
  `plan_mission` text,
  `plan_vision` text,
  `plan_values` text,
  PRIMARY KEY (`plan_id`),
  KEY `tb_strategic_planning_FKIndex1` (`org_id`),
  CONSTRAINT `plan_from_org` FOREIGN KEY (`org_id`) REFERENCES `tb_organizations` (`org_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `tb_strategic_planning_ibfk_1` FOREIGN KEY (`org_id`) REFERENCES `tb_organizations` (`org_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

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
  `user_type` tinyint(2) unsigned NOT NULL DEFAULT '2' COMMENT '0 Admin, 1 Concultor Interno, 2 Colaborador',
  `user_phone` varchar(100) DEFAULT NULL,
  `user_position` varchar(100) DEFAULT NULL,
  `user_photo` varchar(255) DEFAULT NULL,
  `user_dtlastlogin` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`user_id`),
  UNIQUE KEY `user_login_UNIQUE` (`user_login`),
  UNIQUE KEY `user_cpf_UNIQUE` (`user_cpf`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `tb_users_organizations`
--

DROP TABLE IF EXISTS `tb_users_organizations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tb_users_organizations` (
  `user_id` int(10) unsigned NOT NULL,
  `org_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`user_id`,`org_id`),
  KEY `org_key_idx` (`org_id`),
  CONSTRAINT `org_key` FOREIGN KEY (`org_id`) REFERENCES `tb_organizations` (`org_id`) ON DELETE CASCADE ON UPDATE NO ACTION,
  CONSTRAINT `user_key` FOREIGN KEY (`user_id`) REFERENCES `tb_users` (`user_id`) ON DELETE CASCADE ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping routines for database 'easyplanning'
--
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

-- Dump completed on 2018-02-24  0:33:16
