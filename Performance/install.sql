-- MySQL dump 10.13  Distrib 5.5.37, for debian-linux-gnu (x86_64)
--
-- Host: localhost    Database: performance
-- ------------------------------------------------------
-- Server version	5.5.37-0+wheezy1

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
-- Table structure for table `measure_statistic_data`
--

DROP TABLE IF EXISTS `measure_statistic_data`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `measure_statistic_data` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `measureId` int(10) unsigned NOT NULL,
  `parentId` bigint(20) unsigned NOT NULL,
  `file` varchar(512) NOT NULL,
  `line` int(11) NOT NULL,
  `content` text NOT NULL,
  `lines` int(10) unsigned NOT NULL,
  `time` float unsigned NOT NULL,
  `timeSubStack` float unsigned NOT NULL,
  `immersion` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `attemptId` (`measureId`),
  CONSTRAINT `attempt_statistic_data_ibfk_1` FOREIGN KEY (`measureId`) REFERENCES `test_measure` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `request_filter`
--

DROP TABLE IF EXISTS `request_filter`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `request_filter` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `type` varchar(16) NOT NULL,
  `parameters` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `request_filter_set`
--

DROP TABLE IF EXISTS `request_filter_set`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `request_filter_set` (
  `requestId` int(10) unsigned NOT NULL,
  `filterId` int(10) unsigned NOT NULL,
  `setId` int(10) unsigned NOT NULL,
  KEY `fk_request_filter_set_scenario_request1_idx` (`requestId`),
  KEY `fk_request_filter_set_request_filter1_idx` (`filterId`),
  CONSTRAINT `fk_request_filter_set_request_filter1` FOREIGN KEY (`filterId`) REFERENCES `request_filter` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION,
  CONSTRAINT `fk_request_filter_set_scenario_request1` FOREIGN KEY (`requestId`) REFERENCES `scenario_request` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `request_parameter`
--

DROP TABLE IF EXISTS `request_parameter`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `request_parameter` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `requestId` int(10) unsigned NOT NULL,
  `method` varchar(16) NOT NULL,
  `name` varchar(64) NOT NULL,
  `value` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `requestId` (`requestId`),
  CONSTRAINT `request_parameter_ibfk_1` FOREIGN KEY (`requestId`) REFERENCES `scenario_request` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `scenario`
--

DROP TABLE IF EXISTS `scenario`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `scenario` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(64) DEFAULT NULL,
  `description` text,
  `edited` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `scenario_request`
--

DROP TABLE IF EXISTS `scenario_request`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `scenario_request` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `scenarioId` int(10) unsigned NOT NULL,
  `url` varchar(255) DEFAULT NULL,
  `method` varchar(16) DEFAULT NULL,
  `toMeasure` tinyint(1) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `measureId` (`scenarioId`),
  CONSTRAINT `measure_request_ibfk_1` FOREIGN KEY (`scenarioId`) REFERENCES `scenario` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `scenario_test`
--

DROP TABLE IF EXISTS `scenario_test`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `scenario_test` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `scenarioId` int(10) unsigned NOT NULL,
  `state` varchar(32) DEFAULT NULL,
  `started` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `measureId` (`scenarioId`),
  CONSTRAINT `measure_test_ibfk_1` FOREIGN KEY (`scenarioId`) REFERENCES `scenario` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `test_measure`
--

DROP TABLE IF EXISTS `test_measure`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `test_measure` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `testId` int(10) unsigned NOT NULL,
  `url` varchar(255) DEFAULT NULL,
  `method` varchar(16) NOT NULL,
  `parameters` text,
  `body` text,
  `state` varchar(32) DEFAULT NULL,
  `started` datetime DEFAULT NULL,
  `time` float NOT NULL,
  `calls` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `testId` (`testId`),
  CONSTRAINT `test_attempt_ibfk_1` FOREIGN KEY (`testId`) REFERENCES `scenario_test` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `translate`
--

DROP TABLE IF EXISTS `translate`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `translate` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `lang` varchar(8) NOT NULL,
  `module` varchar(32) NOT NULL,
  `key` varchar(255) NOT NULL,
  `text` text NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique` (`lang`,`module`,`key`)
) ENGINE=InnoDB AUTO_INCREMENT=437 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `translate`
--

LOCK TABLES `translate` WRITE;
/*!40000 ALTER TABLE `translate` DISABLE KEYS */;
INSERT INTO `translate` VALUES (1,'CS','main','menu.summary','Přehled'),(2,'EN','main','menu.summary','Summary'),(3,'CS','main','menu.search','Vyhledávání'),(4,'EN','main','menu.search','Search'),(5,'CS','main','menu.statistics','Statistiky'),(6,'EN','main','menu.statistics','Statistics'),(7,'CS','main','menu.optimalization','Optimalizace'),(8,'EN','main','menu.optimalization','Optimalization'),(9,'CS','main','menu.setup','Nastavení'),(10,'EN','main','menu.setup','Setup'),(11,'CS','main','menu.cron','Plánovač'),(12,'EN','main','menu.cron','Scheduler'),(13,'CS','main','menu.about','O aplikaci'),(14,'EN','main','menu.about','About application'),(15,'CS','main','language.cs','Česky'),(16,'CS','main','language.en','Anglicky'),(17,'EN','main','language.cs','Czech'),(18,'EN','main','language.en','English'),(19,'CS','main','yes','Ano'),(20,'CS','main','no','Ne'),(21,'EN','main','yes','Yes'),(22,'EN','main','no','No'),(23,'CS','main','list.search','Vyhledat'),(24,'EN','main','list.search','Search'),(25,'CS','profiler','scenario.list.name','Název'),(26,'EN','profiler','scenario.list.name','Name'),(27,'CS','profiler','scenario.list.description','Popis'),(28,'EN','profiler','scenario.list.description','Description'),(29,'CS','profiler','scenario.list.edited','Poslední změna'),(30,'EN','profiler','scenario.list.edited','Last change'),(33,'CS','profiler','scenario.list.delete.text','Opravdu si přejete smazat scénář?'),(34,'EN','profiler','scenario.list.delete.text','Do you really want to delete the scenario?'),(35,'CS','profiler','scenario.name','Název'),(36,'EN','profiler','scenario.name','Name'),(37,'CS','profiler','scenario.description','Popis'),(38,'EN','profiler','scenario.description','Description'),(39,'CS','main','add','Přidat'),(40,'EN','main','add','Add'),(41,'CS','profiler','scenario.request.url','Url'),(42,'EN','profiler','scenario.request.url','Url'),(43,'CS','profiler','scenario.request.list','Seznam testů'),(44,'EN','profiler','scenario.request.list','Test list'),(45,'CS','profiler','scenario.request.method','Metoda'),(46,'EN','profiler','scenario.request.method','Method'),(47,'CS','profiler','scenario.request.method.GET','Get'),(48,'EN','profiler','scenario.request.method.GET','Get'),(49,'CS','profiler','scenario.request.method.POST','Post'),(50,'EN','profiler','scenario.request.method.POST','Post'),(51,'CS','profiler','scenario.request.method.PUT','Put'),(52,'EN','profiler','scenario.request.method.PUT','Put'),(53,'CS','profiler','scenario.request.method.DELETE','Delete'),(54,'EN','profiler','scenario.request.method.DELETE','Delete'),(55,'CS','profiler','scenario.request.parameters.list','Seznam parametrů'),(56,'EN','profiler','scenario.request.parameters.list','Parameter list'),(57,'CS','profiler','scenario.request.toMeasure','Změřit'),(58,'EN','profiler','scenario.request.toMeasure','To measure'),(59,'CS','main','remove','Odebrat'),(60,'EN','main','remove','Remove'),(61,'CS','profiler','scenario.request.add','Přidat test'),(62,'EN','profiler','scenario.request.add','Add test'),(63,'CS','profiler','scenario.request.remove','Odebrat test'),(64,'EN','profiler','scenario.request.remove','Remove test'),(65,'CS','profiler','scenario.request.parameter.add','Přidat parametr'),(66,'EN','profiler','scenario.request.parameter.add','Add parameter'),(67,'CS','profiler','scenario.request.parameter.remove','Odebrat parameter'),(68,'EN','profiler','scenario.request.parameter.remove','Remove parameter'),(69,'CS','main','save','Uložit'),(70,'EN','main','save','Save'),(71,'CS','main','cancel','Zavřít'),(72,'EN','main','cancel','Cancel'),(73,'CS','profiler','scenario.request.parameter.name','Klíč'),(74,'EN','profiler','scenario.request.parameter.name','Key'),(75,'CS','profiler','scenario.request.parameter.value','Hodnota'),(76,'EN','profiler','scenario.request.parameter.value','Value'),(77,'CS','main','validate.required','Pole musí být vyplněno.'),(78,'EN','main','validate.required','Field is required.'),(81,'CS','profiler','scenario.test.started','Spuštěno'),(82,'EN','profiler','scenario.test.started','Started'),(83,'CS','profiler','scenario.test.showTest','Zobrazit test'),(84,'EN','profiler','scenario.test.showTest','Show test'),(99,'CS','profiler','scenario.edited','Editováno'),(100,'EN','profiler','scenario.edited','Edited'),(101,'CS','profiler','scenario.test.start','Spustit měření'),(102,'EN','profiler','scenario.test.start','Start measure'),(103,'CS','profiler','scenario.test.measure.detail.summary','Souhrn'),(104,'EN','profiler','scenario.test.measure.detail.summary','Summary'),(105,'CS','profiler','scenario.test.measure.detail.callStack','Strom volání'),(106,'EN','profiler','scenario.test.measure.detail.callStack','Call stack'),(107,'CS','profiler','scenario.test.measure.detail.functionStatistics','Statistiky volání'),(108,'EN','profiler','scenario.test.measure.detail.functionStatistics','Calls statistics'),(109,'CS','profiler','scenario.test.url','Url'),(110,'EN','profiler','scenario.test.url','Url'),(111,'CS','profiler','scenario.test.parameters','Parametry'),(112,'EN','profiler','scenario.test.parameters','Parameters'),(113,'CS','profiler','scenario.test.body','Tělo zprávy'),(114,'EN','profiler','scenario.test.body','Message body'),(115,'CS','profiler','scenario.test.time','Čas [ms]'),(116,'EN','profiler','scenario.test.time','Time [ms]'),(117,'CS','profiler','scenario.test.calls','Počet volání'),(118,'EN','profiler','scenario.test.calls','Count of calls'),(119,'CS','profiler','scenario.test.measure.summary.dateOfStart','Datum spuštění'),(120,'EN','profiler','scenario.test.measure.summary.dateOfStart','Date of start'),(121,'CS','profiler','scenario.test.measure.summary.maxImmersion','Nejvyšší úroveň zanoření'),(122,'EN','profiler','scenario.test.measure.summary.maxImmersion','Maximum immersion'),(123,'CS','profiler','scenario.test.measure.callStack.subStack','Podstrom volání'),(124,'EN','profiler','scenario.test.measure.callStack.subStack','Sub-stack'),(125,'CS','profiler','scenario.test.measure.callStack.file','Soubor'),(126,'EN','profiler','scenario.test.measure.callStack.file','File'),(127,'CS','profiler','scenario.test.measure.callStack.line','Řádek'),(128,'EN','profiler','scenario.test.measure.callStack.line','Line'),(129,'CS','profiler','scenario.test.measure.callStack.content','Obsah'),(130,'EN','profiler','scenario.test.measure.callStack.content','Content'),(131,'CS','profiler','scenario.test.measure.callStack.avgTime','Průměrný čas [ms]'),(132,'EN','profiler','scenario.test.measure.callStack.avgTime','Average time [ms]'),(133,'CS','profiler','scenario.test.measure.callStack.avgTimeSubStack','Průměrný čas (včetně podstromu) [ms]'),(134,'EN','profiler','scenario.test.measure.callStack.avgTimeSubStack','Average time (include sub-stack) [ms]'),(135,'CS','profiler','scenario.test.measure.callStack.timeSubStack','Celkový čas (včetně podstromu) [ms]'),(136,'EN','profiler','scenario.test.measure.callStack.timeSubStack','Total time (include sub-stack) [ms]'),(137,'CS','search','filter.menu.title','Přidat filtr'),(138,'EN','search','filter.menu.title','Add filter'),(139,'CS','search','filter.button.show','Zobrazit'),(140,'EN','search','filter.button.show','Show'),(141,'CS','search','filter.target.scenario','Scénář'),(142,'EN','search','filter.target.scenario','Scenario'),(143,'CS','search','filter.target.measure','Měření'),(144,'EN','search','filter.target.measure','Measure'),(145,'CS','search','filter.target.test','Test'),(146,'EN','search','filter.target.test','Test'),(147,'CS','search','filter.measure.fulltext','Fulltext'),(148,'EN','search','filter.measure.fulltext','Fulltext'),(149,'CS','search','filter.measure.url','Url'),(150,'EN','search','filter.measure.url','Url'),(151,'CS','search','filter.measure.state','Stav'),(152,'EN','search','filter.measure.state','State'),(153,'CS','search','filter.measure.started','Spuštěno'),(154,'EN','search','filter.measure.started','Started'),(155,'CS','search','filter.measure.method','Metoda'),(156,'EN','search','filter.measure.method','Method'),(157,'CS','search','filter.measure.time','Čas'),(158,'EN','search','filter.measure.time','Time'),(159,'CS','search','filter.measure.calls','Počet volání'),(160,'EN','search','filter.measure.calls','Count of calls'),(161,'CS','search','filter.measure.file','Soubor'),(162,'EN','search','filter.measure.file','File'),(163,'CS','search','filter.measure.line','Řádek souboru'),(164,'EN','search','filter.measure.line','Line in file'),(165,'CS','search','filter.measure.content','Obsah řádku'),(166,'EN','search','filter.measure.content','Content of line'),(167,'CS','search','filter.measure.immersion','Úroveň zanoření'),(168,'EN','search','filter.measure.immersion','Level of immersion'),(169,'CS','search','filter.scenario.fulltext','Fulltext'),(170,'EN','search','filter.scenario.fulltext','Fulltext'),(171,'CS','search','filter.scenario.name','Název'),(172,'EN','search','filter.scenario.name','Name'),(173,'CS','search','filter.scenario.edited','Poslední změna'),(174,'EN','search','filter.scenario.edited','Last change'),(177,'CS','search','filter.scenario.url','Url'),(178,'EN','search','filter.scenario.url','Url'),(179,'CS','search','filter.scenario.started','Spuštěno'),(180,'EN','search','filter.scenario.started','Started'),(181,'CS','search','filter.scenario.time','Čas'),(182,'EN','search','filter.scenario.time','Time'),(183,'CS','search','filter.scenario.calls','Počet volání'),(184,'EN','search','filter.scenario.calls','Count of calls'),(185,'CS','search','filter.test.fulltext','Fulltext'),(186,'EN','search','filter.test.fulltext','Fulltext'),(187,'CS','search','filter.test.url','Url'),(188,'EN','search','filter.test.url','Url'),(189,'CS','search','filter.test.state','Stav'),(190,'EN','search','filter.test.state','State'),(191,'CS','search','filter.test.started','Spuštěno'),(192,'EN','search','filter.test.started','Started'),(193,'CS','search','filter.test.time','Čas'),(194,'EN','search','filter.test.time','Time'),(195,'CS','search','filter.test.calls','Počet volání'),(196,'EN','search','filter.test.calls','Count of calls'),(197,'CS','search','filter.query.input.placeholder','Fulltextové vyhledávání'),(198,'EN','search','filter.query.input.placeholder','Fulltext search'),(199,'CS','search','filter.operator.equal','je stejné'),(200,'EN','search','filter.operator.equal','is equal to'),(201,'CS','search','filter.operator.not.equal','není stejné'),(202,'EN','search','filter.operator.not.equal','not equal to'),(203,'CS','search','filter.operator.greater.than','větší než'),(204,'EN','search','filter.operator.greater.than','greater than'),(205,'CS','search','filter.operator.less.than','menší než'),(206,'EN','search','filter.operator.less.than','less than'),(207,'CS','search','filter.operator.in','má nastaveno'),(208,'EN','search','filter.operator.in','is in'),(209,'CS','search','filter.operator.not.in','nemá nastaveno'),(210,'EN','search','filter.operator.not.in','not in'),(211,'CS','search','filter.operator.after','po'),(212,'EN','search','filter.operator.after','after'),(213,'CS','search','filter.operator.before','před'),(214,'EN','search','filter.operator.before','before'),(215,'CS','search','filter.operator.contains','obsahuje'),(216,'EN','search','filter.operator.contains','contains'),(217,'CS','search','filter.operator.does.not.contains','neobsahuje'),(218,'EN','search','filter.operator.does.not.contains','does not contains'),(219,'CS','search','filter.operator.empty','je prázdné'),(220,'EN','search','filter.operator.empty','is empty'),(221,'CS','search','filter.operator.set','je vyplněno'),(222,'EN','search','filter.operator.set','is set'),(223,'CS','search','filter.string.input.placeholder','Vyhledávání řetězce'),(224,'EN','search','filter.string.input.placeholder','String search'),(257,'CS','search','filter.float.input.placeholder','Hledání desetinného čísla'),(258,'EN','search','filter.float.input.placeholder','Search of float number'),(259,'CS','search','filter.int.input.placeholder','Hledání celého čísla'),(260,'EN','search','filter.int.input.placeholder','Search of integer'),(261,'CS','search','filter.measure.method.GET','GET'),(262,'EN','search','filter.measure.method.GET','GET'),(263,'CS','search','filter.measure.method.POST','POST'),(264,'EN','search','filter.measure.method.POST','POST'),(265,'CS','search','filter.measure.method.PUT','PUT'),(266,'EN','search','filter.measure.method.PUT','PUT'),(267,'CS','search','filter.measure.method.DELETE','DELETE'),(268,'EN','search','filter.measure.method.DELETE','DELETE'),(293,'CS','main','server.error','Nastala chyba. Kontaktujte správce systému.'),(294,'EN','main','server.error','An error has occurred. Contact your system administrator.'),(295,'CS','search','filter.measure.parameters','Parametry'),(296,'EN','search','filter.measure.parameters','Parameters'),(297,'CS','profiler','scenario.request.filter.list','Seznam filtrů'),(298,'EN','profiler','scenario.request.filter.list','Filter list'),(299,'CS','profiler','scenario.request.filter.add','Přidat filtr'),(300,'EN','profiler','scenario.request.filter.add','Add filter'),(301,'CS','profiler','scenario.request.filter.type','Typ filtru'),(302,'EN','profiler','scenario.request.filter.type','Filter type'),(303,'CS','profiler','scenario.request.filter.type.positive','positivní'),(304,'EN','profiler','scenario.request.filter.type.positive','positive'),(305,'CS','profiler','scenario.request.filter.type.negative','negativní'),(306,'EN','profiler','scenario.request.filter.type.negative','negative'),(307,'CS','profiler','scenario.request.filter.remove','Odebrat filtr'),(308,'EN','profiler','scenario.request.filter.remove','Remove filter'),(309,'CS','profiler','scenario.request.filter.parameter.add','Přidat pravidlo'),(310,'EN','profiler','scenario.request.filter.parameter.add','Add rule'),(311,'CS','profiler','scenario.request.filter.parameter.file','Soubor'),(312,'EN','profiler','scenario.request.filter.parameter.file','File'),(313,'CS','profiler','scenario.request.filter.parameter.line','Řádka'),(314,'EN','profiler','scenario.request.filter.parameter.line','Line'),(315,'CS','profiler','scenario.request.filter.parameter.immersion','Úroveň zanoření'),(316,'EN','profiler','scenario.request.filter.parameter.immersion','Level of immersion'),(317,'CS','profiler','scenario.request.filter.parameter.subStack','Pod-strom'),(318,'EN','profiler','scenario.request.filter.parameter.subStack','Sub-stack'),(319,'CS','profiler','scenario.request.filter.parameter.operator.regExp','(regulární výraz)'),(320,'EN','profiler','scenario.request.filter.parameter.operator.regExp','(regular expression)'),(321,'CS','profiler','scenario.request.filter.parameter.operator.lowerThan','menší než'),(322,'EN','profiler','scenario.request.filter.parameter.operator.lowerThan','lower than'),(323,'CS','profiler','scenario.request.filter.parameter.operator.higherThan','větší než'),(324,'EN','profiler','scenario.request.filter.parameter.operator.higherThan','higher than'),(327,'CS','main','validate.isNotNumber','Hodnota není číslo.'),(328,'EN','main','validate.isNotNumber','Value is not number.'),(337,'CS','profiler','scenario.test.state.idle','Nečinný'),(338,'EN','profiler','scenario.test.state.idle','Idle'),(339,'CS','profiler','scenario.test.state.measure_active','Probíhá měření'),(340,'EN','profiler','scenario.test.state.measure_active','Active measure'),(341,'CS','profiler','scenario.test.state.done','Změřeno'),(342,'EN','profiler','scenario.test.state.done','Done'),(343,'CS','profiler','scenario.test.state.error','Chyba'),(344,'EN','profiler','scenario.test.state.error','Error'),(345,'CS','profiler','scenario.test.measure.showStatistic','Zobrazit data'),(346,'EN','profiler','scenario.test.measure.showStatistic','Show data'),(347,'CS','main','menu.summary.mysql','Databáze'),(348,'EN','main','menu.summary.mysql','Database'),(349,'CS','main','menu.summary.file','Soubory'),(350,'EN','main','menu.summary.file','Files'),(351,'CS','profiler','scenario.test.state','Stav'),(352,'EN','profiler','scenario.test.state','State'),(353,'CS','profiler','scenario.list.create','Vytvořit scénář'),(354,'EN','profiler','scenario.list.create','Create scenario'),(355,'CS','profiler','scenario.list.edit','Upravit scénář'),(356,'EN','profiler','scenario.list.edit','Edit scenario'),(357,'CS','profiler','scenario.list.delete','Smazat scénář'),(358,'EN','profiler','scenario.list.delete','Delete scenario'),(359,'CS','profiler','scenario.request.filter.parameter.remove','Odebrat pravidlo'),(360,'EN','profiler','scenario.request.filter.parameter.remove','Remove rule'),(361,'CS','profiler','scenario.request.manual.start','Spustit ruční měření'),(362,'EN','profiler','scenario.request.manual.start','Run manual measure'),(363,'CS','main','back','Zpět'),(364,'EN','main','back','Back'),(365,'CS','profiler','scenario.edit','Upravit scénář'),(366,'EN','profiler','scenario.edit','Edit scenario'),(367,'CS','profiler','scenario.test.measure.state.empty','Prázdné'),(368,'EN','profiler','scenario.test.measure.state.empty','Empty'),(369,'CS','profiler','scenario.test.measure.state.ticking','Zachytávání'),(370,'EN','profiler','scenario.test.measure.state.ticking','Ticking'),(371,'CS','profiler','scenario.test.measure.state.ticked','Zachyceno'),(372,'EN','profiler','scenario.test.measure.state.ticked','Ticked'),(373,'CS','profiler','scenario.test.measure.state.analyzing','Analyzování'),(374,'EN','profiler','scenario.test.measure.state.analyzing','Analyzing'),(375,'CS','profiler','scenario.test.measure.state.analyzed','Analyzováno'),(376,'EN','profiler','scenario.test.measure.state.analyzed','Analyzed'),(377,'CS','profiler','scenario.test.measure.state.statistic_generating','Generování statistik'),(378,'EN','profiler','scenario.test.measure.state.statistic_generating','Statistic generating'),(379,'CS','profiler','scenario.test.measure.state.error','Chyba'),(380,'EN','profiler','scenario.test.measure.state.error','Error'),(381,'CS','profiler','scenario.test.measure.summary.time','Čas [ms]'),(382,'EN','profiler','scenario.test.measure.summary.time','Time [ms]'),(383,'CS','profiler','scenario.test.measure.summary.calls','Počet volání'),(384,'EN','profiler','scenario.test.measure.summary.calls','Count of calls'),(385,'CS','profiler','scenario.test.measure.callStack.time','Celkový čas [ms]'),(386,'EN','profiler','scenario.test.measure.callStack.time','Total time [ms]'),(387,'CS','profiler','scenario.test.measure.callStack.calls','Počet volání'),(388,'EN','profiler','scenario.test.measure.callStack.calls','Count of calls'),(389,'CS','profiler','file.list.id','Identifikátor'),(390,'EN','profiler','file.list.id','Identificator'),(391,'CS','profiler','file.list.created','Vytvořeno'),(392,'EN','profiler','file.list.created','Created'),(393,'CS','profiler','file.list.size','Velikost'),(394,'EN','profiler','file.list.size','Size'),(395,'CS','profiler','file.list.delete','Smazat měření'),(396,'EN','profiler','file.list.delete','Delete measure'),(397,'CS','profiler','file.list.delete.text','Opravdu si přejete smazat měření uložené v souboru?'),(398,'EN','profiler','file.list.delete.text','Do you really want to delte the measure saved in file?'),(399,'CS','search','filter.measure.state.empty','Prázdný'),(400,'EN','search','filter.measure.state.empty','Empty'),(401,'CS','search','filter.measure.state.ticking','Zachytávání'),(402,'EN','search','filter.measure.state.ticking','Ticking'),(403,'CS','search','filter.measure.state.ticked','Zachyceno'),(404,'EN','search','filter.measure.state.ticked','Ticked'),(405,'CS','search','filter.measure.state.analyzing','Analyzování'),(406,'EN','search','filter.measure.state.analyzing','Analyzing'),(407,'CS','search','filter.measure.state.analyzed','Analyzováno'),(408,'EN','search','filter.measure.state.analyzed','Analyzed'),(409,'CS','search','filter.measure.state.statistic_generating','Generování statistik'),(410,'EN','search','filter.measure.state.statistic_generating','Statistic generating'),(411,'CS','search','filter.measure.state.statistic_generated','Vygenerované statistiky'),(412,'EN','search','filter.measure.state.statistic_generated','Statistic generated'),(413,'CS','search','filter.measure.state.error','Chyba'),(414,'EN','search','filter.measure.state.error','Error'),(415,'CS','search','filter.test.state.idle','Nečinný'),(416,'EN','search','filter.test.state.idle','Idle'),(417,'CS','search','filter.test.state.measure_active','Probíhá měření'),(418,'EN','search','filter.test.state.measure_active','Active measure'),(419,'CS','search','filter.test.state.done','Dokončeno'),(420,'EN','search','filter.test.state.done','Done'),(421,'CS','search','filter.test.state.error','Chyba'),(422,'EN','search','filter.test.state.error','Error'),(423,'CS','search','filter.invalid','Nevalidní filtr'),(424,'EN','search','filter.invalid','Invalid filter'),(425,'CS','search','filter.measure.body','Tělo zprávy'),(426,'EN','search','filter.measure.body','Message body'),(427,'CS','profiler','scenario.test.measure.callStack.minTime','Minimální čas [ms]'),(428,'EN','profiler','scenario.test.measure.callStack.minTime','Minimal time [ms]'),(429,'CS','profiler','scenario.test.measure.callStack.maxTime','Maximální čas [ms]'),(430,'EN','profiler','scenario.test.measure.callStack.maxTime','Maximal time [ms]'),(431,'CS','profiler','scenario.test.measure.callStack.minTimeSubStack','Minimální čas (včetně podstromu) [ms]'),(432,'EN','profiler','scenario.test.measure.callStack.minTimeSubStack','Minimal time (include sub-stack) [ms]'),(433,'CS','profiler','scenario.test.measure.callStack.maxTimeSubStack','Maximální čas (včetně podstromu) [ms]'),(434,'EN','profiler','scenario.test.measure.callStack.maxTimeSubStack','Maximal time (include sub-stack) [ms]'),(435,'CS','profiler','scenario.test.measure.callStack.relativeCount','Relativní zastoupení [%]'),(436,'EN','profiler','scenario.test.measure.callStack.relativeCount','Relative count [%]');
/*!40000 ALTER TABLE `translate` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2014-06-09  8:53:17
