-- Host: localhost    Database: evo
-- ------------------------------------------------------
-- Server version	5.5.41-0+wheezy1

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
-- Table structure for table `six_blocked`
--

DROP TABLE IF EXISTS `six_blocked`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `six_blocked` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `profile_id` int(10) unsigned NOT NULL,
  `blocked_profile_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `profile_id` (`profile_id`,`blocked_profile_id`),
  KEY `blocked_profile_id` (`blocked_profile_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `six_blocked`
--

LOCK TABLES `six_blocked` WRITE;
/*!40000 ALTER TABLE `six_blocked` DISABLE KEYS */;
/*!40000 ALTER TABLE `six_blocked` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `six_friends`
--

DROP TABLE IF EXISTS `six_friends`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `six_friends` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `profile_id` int(10) unsigned NOT NULL,
  `friend_profile_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `profile_id` (`profile_id`,`friend_profile_id`),
  KEY `friend_profile_id` (`friend_profile_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `six_friends`
--

LOCK TABLES `six_friends` WRITE;
/*!40000 ALTER TABLE `six_friends` DISABLE KEYS */;
/*!40000 ALTER TABLE `six_friends` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `six_history`
--

DROP TABLE IF EXISTS `six_history`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `six_history` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `playerId` mediumint(9) DEFAULT NULL,
  `profileId` mediumint(9) DEFAULT NULL,
  `season` int(2) DEFAULT NULL,
  `position` int(4) DEFAULT NULL,
  `points` int(4) DEFAULT NULL,
  `games` int(4) DEFAULT NULL,
  `wins` int(4) DEFAULT NULL,
  `losses` int(4) DEFAULT NULL,
  `draws` int(4) DEFAULT '0',
  `DC` smallint(6) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `six_history`
--

LOCK TABLES `six_history` WRITE;
/*!40000 ALTER TABLE `six_history` DISABLE KEYS */;
/*!40000 ALTER TABLE `six_history` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `six_matches`
--

DROP TABLE IF EXISTS `six_matches`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `six_matches` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `season` tinyint(4) NOT NULL DEFAULT '1',
  `reported` tinyint(1) DEFAULT '0',
  `edited` tinyint(4) NOT NULL DEFAULT '0',
  `score_home` int(10) unsigned NOT NULL DEFAULT '0',
  `score_away` int(10) unsigned NOT NULL DEFAULT '0',
  `score_home_reg` tinyint(4) NOT NULL DEFAULT '0',
  `score_away_reg` tinyint(11) NOT NULL DEFAULT '0',
  `team_id_home` int(11) DEFAULT '-1',
  `team_id_away` int(11) DEFAULT '-1',
  `hashHome` varchar(32) NOT NULL DEFAULT '',
  `hashAway` varchar(32) NOT NULL DEFAULT '',
  `lobbyName` varchar(50) NOT NULL DEFAULT '',
  `roomName` varchar(255) NOT NULL DEFAULT '',
  `minutes` int(3) NOT NULL,
  `numParticipants` tinyint(4) NOT NULL DEFAULT '0',
  `played_on` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `lobbyName` (`lobbyName`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `six_matches`
--

LOCK TABLES `six_matches` WRITE;
/*!40000 ALTER TABLE `six_matches` DISABLE KEYS */;
/*!40000 ALTER TABLE `six_matches` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `six_matches_info`
--

DROP TABLE IF EXISTS `six_matches_info`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `six_matches_info` (
  `matchId` int(11) NOT NULL,
  `type` varchar(1) NOT NULL,
  `matchStart` timestamp NULL DEFAULT NULL,
  `duration` int(11) NOT NULL,
  `matchTime` tinyint(4) NOT NULL,
  `timeLimit` tinyint(4) NOT NULL,
  `numberOfPauses` tinyint(4) NOT NULL,
  `conditionSetting` tinyint(4) NOT NULL,
  `injuries` tinyint(4) NOT NULL,
  `maxNoOfSubstitutions` tinyint(4) NOT NULL,
  `matchTypeEx` tinyint(4) NOT NULL,
  `matchTypePk` tinyint(4) NOT NULL,
  `timeSetting` tinyint(4) NOT NULL,
  `season` tinyint(4) NOT NULL,
  `weather` tinyint(4) NOT NULL,
  KEY `matchId` (`matchId`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `six_matches_info`
--

LOCK TABLES `six_matches_info` WRITE;
/*!40000 ALTER TABLE `six_matches_info` DISABLE KEYS */;
/*!40000 ALTER TABLE `six_matches_info` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `six_matches_played`
--

DROP TABLE IF EXISTS `six_matches_played`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `six_matches_played` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `match_id` bigint(20) unsigned NOT NULL,
  `profile_id` int(10) unsigned NOT NULL,
  `home` tinyint(1) NOT NULL DEFAULT '0',
  `points` int(11) DEFAULT '0',
  `pointsDiff` int(11) DEFAULT '0',
  `rating` int(11) DEFAULT '0',
  `ratingDiff` int(11) DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `match_id` (`match_id`,`profile_id`),
  KEY `profile_id` (`profile_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `six_matches_played`
--

LOCK TABLES `six_matches_played` WRITE;
/*!40000 ALTER TABLE `six_matches_played` DISABLE KEYS */;
/*!40000 ALTER TABLE `six_matches_played` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `six_matches_status`
--

DROP TABLE IF EXISTS `six_matches_status`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `six_matches_status` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `season` tinyint(4) NOT NULL DEFAULT '1',
  `reported` tinyint(4) DEFAULT '0',
  `minutes` int(2) unsigned NOT NULL DEFAULT '0',
  `state` varchar(32) DEFAULT NULL,
  `profileHome` int(5) DEFAULT NULL,
  `profileHome2` int(5) DEFAULT NULL,
  `profileHome3` int(5) NOT NULL DEFAULT '0',
  `profileAway` int(5) DEFAULT NULL,
  `profileAway2` int(5) DEFAULT NULL,
  `profileAway3` int(5) NOT NULL DEFAULT '0',
  `scoreHome` int(2) unsigned NOT NULL DEFAULT '0',
  `scoreAway` int(2) unsigned NOT NULL DEFAULT '0',
  `scoreHomeReg` tinyint(4) NOT NULL DEFAULT '0',
  `scoreAwayReg` tinyint(4) NOT NULL DEFAULT '0',
  `teamHome` int(11) DEFAULT NULL,
  `teamAway` int(11) DEFAULT NULL,
  `hashHome` varchar(32) NOT NULL,
  `hashAway` varchar(32) NOT NULL,
  `homeExit` timestamp NULL DEFAULT NULL,
  `awayExit` timestamp NULL DEFAULT NULL,
  `homeCancel` timestamp NULL DEFAULT NULL,
  `awayCancel` timestamp NULL DEFAULT NULL,
  `dc` tinyint(4) DEFAULT NULL,
  `lobbyName` varchar(50) NOT NULL,
  `updated` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `UpdateMatchStatus` (`profileHome`,`profileHome2`,`profileAway`,`profileAway2`,`updated`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `six_matches_status`
--

LOCK TABLES `six_matches_status` WRITE;
/*!40000 ALTER TABLE `six_matches_status` DISABLE KEYS */;
/*!40000 ALTER TABLE `six_matches_status` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `six_patches`
--

DROP TABLE IF EXISTS `six_patches`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `six_patches` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `hash` varchar(32) NOT NULL DEFAULT '',
  `name` varchar(64) NOT NULL DEFAULT '',
  `homepage` varchar(255) NOT NULL DEFAULT '',
  `autoReport` tinyint(4) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `hash` (`hash`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `six_patches`
--

LOCK TABLES `six_patches` WRITE;
/*!40000 ALTER TABLE `six_patches` DISABLE KEYS */;
/*!40000 ALTER TABLE `six_patches` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `six_patches_unknown`
--

DROP TABLE IF EXISTS `six_patches_unknown`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `six_patches_unknown` (
  `hash` varchar(32) NOT NULL,
  `userId` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `six_patches_unknown`
--

LOCK TABLES `six_patches_unknown` WRITE;
/*!40000 ALTER TABLE `six_patches_unknown` DISABLE KEYS */;
/*!40000 ALTER TABLE `six_patches_unknown` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `six_profiles`
--

DROP TABLE IF EXISTS `six_profiles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `six_profiles` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `deleted` tinyint(1) NOT NULL DEFAULT '0',
  `user_id` int(10) unsigned NOT NULL,
  `ordinal` tinyint(4) NOT NULL DEFAULT '-1',
  `name` varchar(32) NOT NULL,
  `rank` int(10) unsigned NOT NULL DEFAULT '0',
  `rating` int(10) unsigned NOT NULL DEFAULT '0',
  `points` int(10) unsigned NOT NULL DEFAULT '0',
  `disconnects` int(10) unsigned NOT NULL DEFAULT '0',
  `updated_on` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `seconds_played` bigint(20) unsigned NOT NULL DEFAULT '0',
  `comment` varchar(256) DEFAULT NULL,
  `points2` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`),
  KEY `user_id` (`user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `six_profiles`
--

LOCK TABLES `six_profiles` WRITE;
/*!40000 ALTER TABLE `six_profiles` DISABLE KEYS */;
/*!40000 ALTER TABLE `six_profiles` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `six_seasons`
--

DROP TABLE IF EXISTS `six_seasons`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `six_seasons` (
  `id` tinyint(4) NOT NULL AUTO_INCREMENT,
  `season` tinyint(4) NOT NULL,
  `begindate` varchar(10) NOT NULL DEFAULT '?',
  `enddate` varchar(10) NOT NULL DEFAULT '?',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `six_seasons`
--

LOCK TABLES `six_seasons` WRITE;
/*!40000 ALTER TABLE `six_seasons` DISABLE KEYS */;
INSERT INTO `six_seasons` VALUES (1,1,'01/01/2015','12/31/2015');
/*!40000 ALTER TABLE `six_seasons` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `six_settings`
--

DROP TABLE IF EXISTS `six_settings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `six_settings` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `profile_id` int(10) unsigned NOT NULL,
  `settings1` blob,
  `settings2` blob,
  PRIMARY KEY (`id`),
  UNIQUE KEY `profile_id` (`profile_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `six_settings`
--

LOCK TABLES `six_settings` WRITE;
/*!40000 ALTER TABLE `six_settings` DISABLE KEYS */;
/*!40000 ALTER TABLE `six_settings` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `six_stats`
--

DROP TABLE IF EXISTS `six_stats`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `six_stats` (
  `onlineUsers` int(11) NOT NULL DEFAULT '0',
  `maintenance` tinyint(4) NOT NULL,
  `season` tinyint(4) NOT NULL DEFAULT '1',
  `debugMode` int(11) NOT NULL DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `six_stats`
--

LOCK TABLES `six_stats` WRITE;
/*!40000 ALTER TABLE `six_stats` DISABLE KEYS */;
INSERT INTO `six_stats` VALUES (0,0,1,0);
/*!40000 ALTER TABLE `six_stats` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `six_streaks`
--

DROP TABLE IF EXISTS `six_streaks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `six_streaks` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `profile_id` int(10) unsigned NOT NULL,
  `wins` int(10) unsigned NOT NULL DEFAULT '0',
  `best` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `profile_id` (`profile_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `six_streaks`
--

LOCK TABLES `six_streaks` WRITE;
/*!40000 ALTER TABLE `six_streaks` DISABLE KEYS */;
/*!40000 ALTER TABLE `six_streaks` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `six_teams`
--

DROP TABLE IF EXISTS `six_teams`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `six_teams` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `patchId` int(1) NOT NULL,
  `sixTeamId` int(4) NOT NULL,
  `ladderTeamId` int(4) NOT NULL,
  `playerId` int(11) DEFAULT NULL,
  `insertDate` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `patchId` (`patchId`,`sixTeamId`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `six_teams`
--

LOCK TABLES `six_teams` WRITE;
/*!40000 ALTER TABLE `six_teams` DISABLE KEYS */;
/*!40000 ALTER TABLE `six_teams` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `weblm_admin`
--

DROP TABLE IF EXISTS `weblm_admin`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `weblm_admin` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `player_id` int(11) NOT NULL,
  `admin_full` char(3) NOT NULL DEFAULT '',
  `admin_ban` char(3) NOT NULL DEFAULT 'no',
  `mod_full` char(3) NOT NULL DEFAULT 'no',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=21 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `weblm_admin`
--

LOCK TABLES `weblm_admin` WRITE;
/*!40000 ALTER TABLE `weblm_admin` DISABLE KEYS */;
INSERT INTO `weblm_admin` VALUES (1,1,'yes','no','no');
/*!40000 ALTER TABLE `weblm_admin` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `weblm_awards`
--

DROP TABLE IF EXISTS `weblm_awards`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `weblm_awards` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `type` char(1) NOT NULL DEFAULT '',
  `playerId` int(11) NOT NULL DEFAULT '0',
  `leagueId` int(11) NOT NULL,
  `profileImage` varchar(30) NOT NULL DEFAULT '',
  `titleText` varchar(50) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=7903 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `weblm_awards`
--

LOCK TABLES `weblm_awards` WRITE;
/*!40000 ALTER TABLE `weblm_awards` DISABLE KEYS */;
INSERT INTO `weblm_awards` VALUES (1,'A',487,0,'gots.gif','Goal of the Season winner - Season 7'),(2,'A',903,0,'gots.gif','Goal of the Season winner - Season 9'),(3,'A',1111,0,'gots.gif','Goal of the Season winner - Season 10'),(4,'A',1386,0,'gots.gif','Goal of the Season winner - Season 11'),(5,'A',1111,0,'gots.gif','Goal of the Season winner - Season 12'),(6,'A',1355,0,'gots.gif','Goal of the Season winner - Season 13'),(8,'B',1040,1,'epl_league.gif','EPL winner'),(9,'B',1319,0,'epl_cup.gif','EPL - FA Cup winner'),(10,'B',1550,2,'laliga_league.gif','La Liga winner'),(11,'B',1624,3,'bundesliga_league.gif','Bundesliga winner'),(12,'A',1581,0,'gots.gif','Goal of the Season winner - Season 14'),(13,'B',1465,0,'laliga_cup.gif','Winner of La Copa del Rey'),(14,'B',1208,0,'bundesliga_league.gif','Winner of DFB-Pokal'),(15,'A',1111,0,'gots.gif','Goal of the Season winner - Season 15'),(16,'A',1227,0,'gots.gif','Goal of the Season winner - Season 16'),(17,'A',528,0,'gots.gif','Goal of the Season winner - Season 17'),(18,'C',2020,0,'newcomer.gif','Best Newcomer Award - 08/2006'),(19,'A',1011,0,'gots.gif','Goal of the Season winner - Season 18'),(20,'D',1812,0,'trophy_teamcup_profile.gif','Evo Team Cup \'06'),(21,'D',1536,0,'trophy_teamcup_profile.gif','Evo Team Cup \'06'),(22,'D',1624,0,'trophy_teamcup_profile.gif','Evo Team Cup \'06'),(23,'D',1551,0,'trophy_teamcup_profile.gif','Evo Team Cup \'06'),(24,'B',527,4,'ecl_league.png','European Club League Winner'),(25,'A',526,0,'gots.gif','Goal of the Season winner - Season 19'),(26,'C',2033,0,'newcomer.gif','Best Newcomer Award - 10/2006'),(27,'A',1079,0,'gots.gif','Goal of the Season winner - Season 20/21'),(28,'A',1227,0,'gots.gif','Goal of the Season winner - Season 22'),(29,'A',903,0,'gots.gif','Goal of the Season winner - Season 22'),(30,'A',1175,0,'gots.gif','Goal of the Season winner - Season 23'),(31,'C',2069,0,'newcomer.gif','Best Newcomer Award - 02/2007'),(32,'C',1,0,'newcomer.gif','Site Administrator');
/*!40000 ALTER TABLE `weblm_awards` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `weblm_countries`
--

DROP TABLE IF EXISTS `weblm_countries`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `weblm_countries` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `country` varchar(30) NOT NULL DEFAULT '',
  `continent` varchar(30) NOT NULL DEFAULT '',
  `region` varchar(30) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=88 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `weblm_countries`
--

LOCK TABLES `weblm_countries` WRITE;
/*!40000 ALTER TABLE `weblm_countries` DISABLE KEYS */;
INSERT INTO `weblm_countries` VALUES (1,'Argentina','South America',''),(2,'Australia','Australia/Oceania',''),(3,'Austria','Europe','Western Europe'),(4,'Belgium','Europe','Western Europe'),(5,'Bosnia','Europe','Eastern Europe'),(6,'Brazil','South America',''),(7,'Bulgaria','Europe','Eastern Europe'),(8,'Canada','North America',''),(9,'Chile','South America',''),(10,'China','Asia',''),(11,'Croatia','Europe','Eastern Europe'),(12,'Cyprus','Europe','Eastern Europe'),(13,'Czech Republic','Europe','Eastern Europe'),(14,'Denmark','Europe','Scandinavia'),(15,'England','Europe','British Isles'),(16,'Egypt','Africa',''),(17,'Estonia','Europe','Eastern Europe'),(18,'Finland','Europe','Scandinavia'),(19,'France','Europe','Western Europe'),(20,'Georgia','Europe','Eastern Europe'),(21,'Germany','Europe','Western Europe'),(22,'Netherlands','Europe','Western Europe'),(23,'Hong Kong','Asia',''),(24,'Hungary','Europe','Eastern Europe'),(25,'Iceland','Europe','Scandinavia'),(26,'India','Asia',''),(27,'Indonesia','Asia',''),(28,'Iran','Asia',''),(29,'Iraq','Asia',''),(30,'Ireland','Europe','British Isles'),(31,'Israel','Asia',''),(32,'Italy','Europe','Western Europe'),(33,'Japan','Asia',''),(34,'Latvia','Europe','Eastern Europe'),(35,'Liechtenstein','Europe','Western Europe'),(36,'Luxembourg','Europe','Western Europe'),(37,'FYR Macedonia','Europe','Eastern Europe'),(38,'Malaysia','Asia',''),(39,'Malta','Europe',''),(40,'Mexico','North America',''),(41,'Morocco','Africa',''),(42,'New Zealand','Australia/Oceania',''),(43,'North Vietnam','Asia',''),(44,'Norway','Europe','Scandinavia'),(45,'Poland','Europe','Eastern Europe'),(46,'Portugal','Europe','Western Europe'),(47,'Puerto Rico','North America',''),(48,'Qatar','Asia',''),(49,'Romania','Europe','Eastern Europe'),(50,'Russia','Asia',''),(51,'Scotland','Europe','British Isles'),(52,'Serbia','Europe','Eastern Europe'),(53,'Singapore','Asia',''),(54,'South Africa','Africa',''),(55,'Slovakia','Europe','Eastern Europe'),(56,'Spain','Europe','Western Europe'),(57,'Suriname','South America',''),(58,'Sweden','Europe','Scandinavia'),(59,'Switzerland','Europe','Western Europe'),(60,'Turkey','Asia',''),(61,'United Kingdom','Europe','British Isles'),(62,'United States','North America',''),(63,'Vietnam','Asia',''),(64,'Greece','Europe',''),(65,'Colombia','South America',''),(66,'Slovenia','Europe','Eastern Europe'),(67,'Algeria','Africa',''),(68,'Wales','Europe','British Isles'),(69,'Albania','Europe','Eastern Europe'),(70,'Dominican Republic','North America',''),(71,'Peru','South America',''),(72,'Lithuania','Europe','Eastern Europe'),(73,'Ukraine','Europe','Eastern Europe'),(74,'Kuwait','Asia',''),(75,'Tunisia','Africa',''),(76,'Montenegro','Europe','Eastern Europe'),(77,'Uruguay','South America',''),(78,'Palestine','Asia',''),(79,'Lebanon','Asia',''),(80,'Panama','South America',''),(81,'Thailand','Asia',''),(82,'Syria','Asia',''),(83,'Venezuela','South America',''),(84,'UAE','Asia',''),(85,'Jordan','Asia',''),(86,'Moldova','Europe','Eastern Europe'),(87,'Bahrain','Asia','');
/*!40000 ALTER TABLE `weblm_countries` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `weblm_donations`
--

DROP TABLE IF EXISTS `weblm_donations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `weblm_donations` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `name` varchar(40) DEFAULT NULL,
  `donationDate` varchar(10) DEFAULT NULL,
  `amount` int(4) DEFAULT NULL,
  `lastUpdated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `weblm_donations`
--

LOCK TABLES `weblm_donations` WRITE;
/*!40000 ALTER TABLE `weblm_donations` DISABLE KEYS */;
/*!40000 ALTER TABLE `weblm_donations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `weblm_games`
--

DROP TABLE IF EXISTS `weblm_games`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `weblm_games` (
  `game_id` int(10) NOT NULL AUTO_INCREMENT,
  `isDraw` tinyint(1) NOT NULL DEFAULT '0',
  `winner` varchar(40) DEFAULT NULL,
  `winner2` varchar(20) NOT NULL DEFAULT '',
  `loser` varchar(40) DEFAULT NULL,
  `loser2` varchar(20) NOT NULL DEFAULT '',
  `date` int(11) DEFAULT NULL,
  `winnerresult` varchar(30) DEFAULT NULL,
  `loserresult` varchar(30) DEFAULT NULL,
  `winnerteam` int(11) NOT NULL DEFAULT '0',
  `loserteam` int(11) NOT NULL DEFAULT '0',
  `comment` varchar(255) DEFAULT NULL,
  `dateday` varchar(12) NOT NULL DEFAULT '',
  `winpoints` tinyint(4) NOT NULL DEFAULT '0',
  `losepoints` tinyint(4) NOT NULL DEFAULT '0',
  `losepoints2` tinyint(4) NOT NULL DEFAULT '0',
  `ratingdiff` tinyint(4) NOT NULL DEFAULT '0',
  `ip` varchar(20) DEFAULT NULL,
  `deleted` char(3) NOT NULL DEFAULT '',
  `deletedBy` varchar(20) NOT NULL DEFAULT '',
  `deleteReason` varchar(100) NOT NULL DEFAULT '',
  `host` char(1) DEFAULT NULL,
  `season` int(2) NOT NULL DEFAULT '1',
  `fairness` tinyint(1) NOT NULL DEFAULT '0',
  `version` char(1) NOT NULL DEFAULT 'A',
  `teamBonus` tinyint(4) NOT NULL DEFAULT '0',
  `teamLadder` tinyint(4) NOT NULL DEFAULT '0',
  `sixGameId` int(7) DEFAULT NULL,
  `edited` int(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`game_id`),
  KEY `multicol` (`season`,`deleted`,`teamLadder`,`winner`,`winner2`,`loser`,`loser2`,`isDraw`),
  KEY `date-deleted` (`date`,`deleted`),
  KEY `version` (`version`),
  KEY `fairness-multicol` (`deleted`,`fairness`,`loser`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 PACK_KEYS=0;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `weblm_games`
--

LOCK TABLES `weblm_games` WRITE;
/*!40000 ALTER TABLE `weblm_games` DISABLE KEYS */;
/*!40000 ALTER TABLE `weblm_games` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `weblm_goals`
--

DROP TABLE IF EXISTS `weblm_goals`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `weblm_goals` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `type` char(1) NOT NULL DEFAULT 'A',
  `player_id` smallint(4) NOT NULL DEFAULT '0',
  `uploaded` varchar(10) NOT NULL DEFAULT '',
  `extension` varchar(5) NOT NULL DEFAULT '',
  `comment` varchar(100) NOT NULL DEFAULT '',
  `hasThumb` char(1) NOT NULL DEFAULT '',
  `rating` float NOT NULL DEFAULT '0',
  `votes` smallint(6) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `weblm_goals`
--

LOCK TABLES `weblm_goals` WRITE;
/*!40000 ALTER TABLE `weblm_goals` DISABLE KEYS */;
/*!40000 ALTER TABLE `weblm_goals` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `weblm_history`
--

DROP TABLE IF EXISTS `weblm_history`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `weblm_history` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `player_id` int(10) DEFAULT NULL,
  `player_name` varchar(40) DEFAULT NULL,
  `season` int(2) DEFAULT NULL,
  `ladder` varchar(30) NOT NULL DEFAULT '',
  `position` int(4) DEFAULT NULL,
  `points` int(4) DEFAULT NULL,
  `games` int(4) DEFAULT NULL,
  `aggregate` int(4) DEFAULT NULL,
  `wins` int(4) DEFAULT NULL,
  `losses` int(4) DEFAULT NULL,
  `draws` int(4) NOT NULL DEFAULT '0',
  `goals_for` int(5) DEFAULT NULL,
  `goals_against` int(5) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `weblm_history`
--

LOCK TABLES `weblm_history` WRITE;
/*!40000 ALTER TABLE `weblm_history` DISABLE KEYS */;
/*!40000 ALTER TABLE `weblm_history` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `weblm_leaguegames`
--

DROP TABLE IF EXISTS `weblm_leaguegames`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `weblm_leaguegames` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `winteam` int(5) NOT NULL DEFAULT '0',
  `loseteam` int(5) NOT NULL DEFAULT '0',
  `winresult` int(2) NOT NULL DEFAULT '0',
  `loseresult` int(2) NOT NULL DEFAULT '0',
  `reportDate` varchar(10) NOT NULL DEFAULT '',
  `reportUser` varchar(20) NOT NULL DEFAULT '',
  `league` smallint(6) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `weblm_leaguegames`
--

LOCK TABLES `weblm_leaguegames` WRITE;
/*!40000 ALTER TABLE `weblm_leaguegames` DISABLE KEYS */;
/*!40000 ALTER TABLE `weblm_leaguegames` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `weblm_leagues`
--

DROP TABLE IF EXISTS `weblm_leagues`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `weblm_leagues` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `team` int(5) NOT NULL DEFAULT '0',
  `player` varchar(20) NOT NULL DEFAULT '',
  `league` int(1) NOT NULL DEFAULT '12',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `weblm_leagues`
--

LOCK TABLES `weblm_leagues` WRITE;
/*!40000 ALTER TABLE `weblm_leagues` DISABLE KEYS */;
/*!40000 ALTER TABLE `weblm_leagues` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `weblm_leagues_meta`
--

DROP TABLE IF EXISTS `weblm_leagues_meta`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `weblm_leagues_meta` (
  `id` tinyint(2) NOT NULL AUTO_INCREMENT,
  `leagueId` tinyint(2) NOT NULL,
  `leagueName` varchar(128) NOT NULL,
  `forumId` int(11) NOT NULL,
  `year` int(11) NOT NULL,
  `isActive` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `weblm_leagues_meta`
--

LOCK TABLES `weblm_leagues_meta` WRITE;
/*!40000 ALTER TABLE `weblm_leagues_meta` DISABLE KEYS */;
/*!40000 ALTER TABLE `weblm_leagues_meta` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `weblm_log_access`
--

DROP TABLE IF EXISTS `weblm_log_access`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `weblm_log_access` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user` varchar(25) NOT NULL DEFAULT '',
  `ip` varchar(15) NOT NULL DEFAULT '',
  `accesstime` int(11) NOT NULL DEFAULT '0',
  `logType` char(2) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  KEY `user` (`user`)
) ENGINE=MyISAM AUTO_INCREMENT=7 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `weblm_log_access`
--

LOCK TABLES `weblm_log_access` WRITE;
/*!40000 ALTER TABLE `weblm_log_access` DISABLE KEYS */;
INSERT INTO `weblm_log_access` VALUES (1,'Admin','217.91.186.131',1427384911,'W'),(2,'Admin','217.91.186.131',1427384918,'W'),(3,'Admin','217.91.186.131',1427384924,'W'),(4,'Admin','217.91.186.131',1427384926,'W'),(5,'Admin','217.91.186.131',1427385138,'W'),(6,'Admin','217.91.186.131',1427385216,'W');
/*!40000 ALTER TABLE `weblm_log_access` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `weblm_log_deducted`
--

DROP TABLE IF EXISTS `weblm_log_deducted`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `weblm_log_deducted` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `user` varchar(20) DEFAULT NULL,
  `deductDate` int(11) DEFAULT '0',
  `deductPoints` tinyint(4) DEFAULT '0',
  `mailSent` char(3) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `weblm_log_deducted`
--

LOCK TABLES `weblm_log_deducted` WRITE;
/*!40000 ALTER TABLE `weblm_log_deducted` DISABLE KEYS */;
/*!40000 ALTER TABLE `weblm_log_deducted` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `weblm_log_mail`
--

DROP TABLE IF EXISTS `weblm_log_mail`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `weblm_log_mail` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user` varchar(25) DEFAULT NULL,
  `toAddress` varchar(40) DEFAULT NULL,
  `mailType` varchar(20) DEFAULT NULL,
  `logTime` varchar(10) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `weblm_log_mail`
--

LOCK TABLES `weblm_log_mail` WRITE;
/*!40000 ALTER TABLE `weblm_log_mail` DISABLE KEYS */;
/*!40000 ALTER TABLE `weblm_log_mail` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `weblm_news`
--

DROP TABLE IF EXISTS `weblm_news`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `weblm_news` (
  `news_id` int(10) NOT NULL AUTO_INCREMENT,
  `user` varchar(30) NOT NULL DEFAULT 'Ike',
  `title` varchar(100) DEFAULT NULL,
  `date` varchar(100) DEFAULT NULL,
  `news` text,
  PRIMARY KEY (`news_id`)
) ENGINE=MyISAM AUTO_INCREMENT=205 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `weblm_news`
--

LOCK TABLES `weblm_news` WRITE;
/*!40000 ALTER TABLE `weblm_news` DISABLE KEYS */;
INSERT INTO `weblm_news` VALUES (1,'Admin','Up and running!','01/01/2015','Welcome to our site!\r\n\r\nPlay fair and enjoy! ;)');
/*!40000 ALTER TABLE `weblm_news` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `weblm_players`
--

DROP TABLE IF EXISTS `weblm_players`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `weblm_players` (
  `player_id` int(10) NOT NULL AUTO_INCREMENT,
  `name` varchar(20) NOT NULL DEFAULT '',
  `alias` varchar(20) NOT NULL DEFAULT '',
  `pwd` binary(60) NOT NULL,
  `passworddb` varchar(10) DEFAULT NULL,
  `approved` varchar(10) DEFAULT 'no',
  `joindate` int(11) DEFAULT NULL,
  `activeDate` int(11) NOT NULL DEFAULT '0',
  `mail` varchar(50) DEFAULT NULL,
  `icq` varchar(15) DEFAULT NULL,
  `aim` varchar(40) DEFAULT NULL,
  `msn` varchar(100) DEFAULT NULL,
  `country` varchar(40) DEFAULT NULL,
  `nationality` varchar(40) NOT NULL DEFAULT '',
  `ra2pes4` int(11) NOT NULL DEFAULT '0',
  `ra2pes5` int(11) NOT NULL DEFAULT '0',
  `rating` int(10) DEFAULT '1500',
  `pes4wins` int(11) NOT NULL DEFAULT '0',
  `pes4losses` int(11) NOT NULL DEFAULT '0',
  `pes4games` int(11) NOT NULL DEFAULT '0',
  `pes5wins` int(11) NOT NULL DEFAULT '0',
  `pes5losses` int(11) NOT NULL DEFAULT '0',
  `pes5games` int(11) NOT NULL DEFAULT '0',
  `draws` int(11) NOT NULL DEFAULT '0',
  `teamPoints` smallint(6) DEFAULT '0',
  `teamWins` smallint(6) DEFAULT '0',
  `teamLosses` smallint(6) DEFAULT '0',
  `teamDraws` smallint(6) DEFAULT '0',
  `teamGames` smallint(6) DEFAULT '0',
  `totalwins` int(10) DEFAULT '0',
  `totallosses` int(10) DEFAULT '0',
  `totalgames` int(10) DEFAULT '0',
  `totaldraws` int(11) NOT NULL DEFAULT '0',
  `deductedPoints` tinyint(4) NOT NULL DEFAULT '0',
  `streakwins` int(10) DEFAULT '0',
  `streaklosses` int(10) DEFAULT '0',
  `ip` varchar(100) DEFAULT NULL,
  `forum` varchar(30) DEFAULT NULL,
  `sendDeductMail` char(3) DEFAULT 'yes',
  `sendGamesMail` char(3) DEFAULT 'yes',
  `sendNewsletter` char(3) NOT NULL DEFAULT 'yes',
  `uploadSpeed` varchar(5) DEFAULT NULL,
  `downloadSpeed` varchar(5) DEFAULT NULL,
  `message` varchar(40) NOT NULL DEFAULT '',
  `versions` varchar(15) NOT NULL DEFAULT '',
  `defaultversion` char(1) NOT NULL DEFAULT '',
  `favteam1` int(11) NOT NULL DEFAULT '0',
  `favteam2` int(11) NOT NULL DEFAULT '0',
  `serial5` varchar(20) NOT NULL DEFAULT '',
  `hash5` varchar(32) NOT NULL DEFAULT '',
  `serial6` varchar(20) NOT NULL DEFAULT '',
  `hash6` varchar(32) NOT NULL DEFAULT '',
  `invalidEmail` tinyint(4) NOT NULL DEFAULT '0',
  `signup` varchar(32) NOT NULL DEFAULT '',
  `signupSent` tinyint(4) NOT NULL DEFAULT '0',
  `rejected` tinyint(4) NOT NULL DEFAULT '0',
  `rejectReason` varchar(50) NOT NULL DEFAULT '',
  PRIMARY KEY (`player_id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `weblm_players`
--

LOCK TABLES `weblm_players` WRITE;
/*!40000 ALTER TABLE `weblm_players` DISABLE KEYS */;
INSERT INTO `weblm_players` VALUES (1,'Admin','Site admin','$2y$10$bbV5tWPt2Inm6drZbufzaufXgLv4GIe14/7bXY2wKsR7cw0k8hOAq','','yes',1100970582,1371829021,'admin@yoursite','n/a','n/a','n/a','No country','No country',0,0,1449,0,0,0,0,0,0,0,72,6,6,3,15,270,226,522,26,60,0,2,'62.158.169.167','','no','no','no','','','Play fair and enjoy','H','H',0,0,'','','','',0,'',0,0,'');
/*!40000 ALTER TABLE `weblm_players` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `weblm_playerstatus`
--

DROP TABLE IF EXISTS `weblm_playerstatus`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `weblm_playerstatus` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `userId` int(10) NOT NULL DEFAULT '0',
  `userName` varchar(20) NOT NULL DEFAULT '',
  `type` char(1) NOT NULL DEFAULT '',
  `active` char(1) NOT NULL DEFAULT '',
  `date` int(11) NOT NULL DEFAULT '0',
  `expireDate` int(11) NOT NULL DEFAULT '0',
  `forumLink` varchar(200) NOT NULL DEFAULT '',
  `reason` varchar(100) NOT NULL DEFAULT '',
  `strictness` varchar(20) NOT NULL DEFAULT '',
  `additionalInfo` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `multicol` (`userName`,`active`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `weblm_playerstatus`
--

LOCK TABLES `weblm_playerstatus` WRITE;
/*!40000 ALTER TABLE `weblm_playerstatus` DISABLE KEYS */;
/*!40000 ALTER TABLE `weblm_playerstatus` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `weblm_seasons`
--

DROP TABLE IF EXISTS `weblm_seasons`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `weblm_seasons` (
  `id` int(2) NOT NULL AUTO_INCREMENT,
  `season` int(2) DEFAULT NULL,
  `begindate` varchar(20) DEFAULT NULL,
  `enddate` varchar(20) DEFAULT NULL,
  `ladders` varchar(30) NOT NULL DEFAULT 'PES4',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=83 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `weblm_seasons`
--

LOCK TABLES `weblm_seasons` WRITE;
/*!40000 ALTER TABLE `weblm_seasons` DISABLE KEYS */;
INSERT INTO `weblm_seasons` VALUES (1,1,'01/01/2015','12/31/2015','PES6/PES2014');
/*!40000 ALTER TABLE `weblm_seasons` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `weblm_signup`
--

DROP TABLE IF EXISTS `weblm_signup`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `weblm_signup` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `sid` varchar(10) NOT NULL DEFAULT '',
  `expired` char(3) NOT NULL DEFAULT '',
  `used` char(3) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `weblm_signup`
--

LOCK TABLES `weblm_signup` WRITE;
/*!40000 ALTER TABLE `weblm_signup` DISABLE KEYS */;
/*!40000 ALTER TABLE `weblm_signup` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `weblm_teamladder`
--

DROP TABLE IF EXISTS `weblm_teamladder`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `weblm_teamladder` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `playerId` int(10) NOT NULL,
  `playerId2` int(10) NOT NULL,
  `type` varchar(6) NOT NULL COMMENT 'player or team',
  `timestamp` varchar(10) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `weblm_teamladder`
--

LOCK TABLES `weblm_teamladder` WRITE;
/*!40000 ALTER TABLE `weblm_teamladder` DISABLE KEYS */;
/*!40000 ALTER TABLE `weblm_teamladder` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `weblm_teams`
--

DROP TABLE IF EXISTS `weblm_teams`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `weblm_teams` (
  `ID` int(11) NOT NULL DEFAULT '0',
  `NAME` varchar(30) NOT NULL DEFAULT '',
  `SCOPE` char(1) NOT NULL DEFAULT '',
  `CATEGORY` tinyint(1) NOT NULL DEFAULT '0',
  `ABBREVIATION` char(3) NOT NULL DEFAULT '',
  `COUNTRY` varchar(32) NOT NULL DEFAULT '',
  PRIMARY KEY (`ID`),
  KEY `ABBREVIATION` (`ABBREVIATION`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `weblm_teams`
--

LOCK TABLES `weblm_teams` WRITE;
/*!40000 ALTER TABLE `weblm_teams` DISABLE KEYS */;
INSERT INTO `weblm_teams` VALUES (100,'Arsenal','A',2,'ARS','England'),(101,'Aston Villa','A',3,'ASV','England'),(102,'Birmingham City','A',5,'BIR','England'),(103,'Blackburn Rovers','A',4,'BLR','England'),(104,'Bolton Wanderers','A',4,'BOL','England'),(105,'Charlton Athletic','A',4,'CHA','England'),(106,'Chelsea','A',1,'CHE','England'),(107,'Everton','A',3,'EVE','England'),(108,'Fulham','A',3,'FUL','England'),(109,'Liverpool','A',2,'LIV','England'),(110,'Manchester City','A',1,'MAC','England'),(111,'Manchester United','A',1,'MAN','England'),(112,'Middlesbrough','A',4,'MID','England'),(113,'Newcastle United','A',3,'NEW','England'),(114,'Portsmouth','A',4,'PSM','England'),(115,'Sunderland AFC','A',4,'SUN','England'),(116,'Tottenham Hotspur','A',2,'TOT','England'),(117,'West Bromwich Albion','A',5,'WBA','England'),(118,'West Ham United','A',4,'WES','England'),(119,'Wigan Athletic','A',5,'WIG','England'),(200,'AC Milan','B',2,'MIL','Italy'),(201,'AS Roma','B',2,'RMA','Italy'),(202,'Ascoli Calsio','B',5,'ASC','Italy'),(203,'Cagliari Calsio','B',5,'CAG','Italy'),(204,'Chievo Verona','B',4,'CHO','Italy'),(205,'Empoli FC','B',5,'EMP','Italy'),(206,'ACF Fiorentina','B',3,'FIO','Italy'),(207,'FC Internazionale Milano','B',2,'INT','Italy'),(208,'Juventus FC','B',2,'JUV','Italy'),(209,'SS Lazio','B',2,'LAZ','Italy'),(210,'US Lecce','B',5,'LEC','Italy'),(211,'AS Livorno Calsio','B',4,'LNO','Italy'),(212,'FC Messina','B',5,'MES','Italy'),(213,'US Palermo','B',3,'PAL','Italy'),(214,'Parma FC','B',4,'PAR','Italy'),(215,'Reggina Calsio','B',5,'REG','Italy'),(216,'UC Sampdoria','B',4,'SAM','Italy'),(217,'AC Siena','B',4,'SIE','Italy'),(218,'Treviso FC','B',5,'TRE','Italy'),(219,'Udinese Calcio','B',4,'UDI','Italy'),(300,'Deportivo Alaves','C',5,'ALA','Spain'),(301,'CA Osasuna','C',5,'OSA','Spain'),(302,'Athletic Bilbao','C',3,'ABO','Spain'),(303,'Atlético Madrid','C',2,'AMD','Spain'),(304,'Cadiz CF','C',5,'CDZ','Spain'),(305,'RC Celta de Vigo','C',4,'CVO','Spain'),(306,'FC Barcelona','C',1,'BAR','Spain'),(307,'Getafe CF','C',5,'GET','Spain'),(308,'Malaga CF','C',3,'MLG','Spain'),(309,'Real Betis','C',3,'BTS','Spain'),(310,'Real Madrid','C',1,'MAD','Spain'),(311,'Racing Santander','C',5,'RAC','Spain'),(312,'Real Sociedad','C',4,'SOC','Spain'),(313,'Real Zaragosa','C',3,'ZAR','Spain'),(314,'RC Deportivo La Coruna','C',4,'DEP','Spain'),(315,'RCD Espanyol','C',4,'ESP','Spain'),(316,'RCD Mallorca','C',4,'MAL','Spain'),(317,'Sevilla FC','C',3,'SEV','Spain'),(318,'Valencia FC','C',2,'VAL','Spain'),(319,'Villarreal CF','C',3,'VIL','Spain'),(400,'1. FC Köln','D',5,'KOL','Germany'),(401,'Arminia Bielefeld','D',5,'ARM','Germany'),(402,'Bayer 04 Leverkusen','D',3,'BAL','Germany'),(403,'FC Bayern München','D',1,'BAY','Germany'),(404,'Borussia Dortmund','D',2,'DOR','Germany'),(405,'Borussia Mönchengladbach','D',4,'BMH','Germany'),(406,'MSV Duisburg','D',5,'DUI','Germany'),(407,'Eintracht Frankfurt','D',4,'FFT','Germany'),(408,'Hamburger SV','D',3,'HBG','Germany'),(409,'Hannover 96','D',4,'HAN','Germany'),(410,'Hertha BSC Berlin','D',4,'BER','Germany'),(411,'1. FC Kaiserslautern','D',5,'KAI','Germany'),(412,'FSV Mainz 05','D',4,'MNZ','Germany'),(413,'FC Nürnberg','D',4,'NUR','Germany'),(414,'FC Schalke 04','D',3,'SHK','Germany'),(415,'VfB Stuttgart','D',3,'STU','Germany'),(416,'VFL Wolfsburg','D',4,'WBG','Germany'),(417,'Werder Bremen','D',3,'BRE','Germany'),(500,'AC Ajaccio','E',5,'AJC','France'),(501,'AS Monaco','E',3,'MNO','France'),(502,'AJ Auxerre','E',5,'AUX','France'),(503,'Girondins Bordeaux','E',3,'BOR','France'),(504,'Le Mans','E',5,'LEM','France'),(505,'OSC Lille','E',3,'LIL','France'),(506,'Olympique Lyonnais','E',3,'LYO','France'),(507,'FC Metz','E',5,'MTZ','France'),(508,'AS Nancy','E',5,'NCY','France'),(509,'FC Nantes','E',5,'NAN','France'),(510,'OGC Nice','E',5,'NIC','France'),(511,'Olympique Marseille','E',3,'MAR','France'),(512,'Paris Saint-Germain FC','E',1,'PSG','France'),(513,'RC Lens','E',5,'LEN','France'),(514,'Stade Rennais FC','E',5,'REN','France'),(515,'AS Saint-Etienne','E',4,'ENE','France'),(516,'FC Sochaux','E',5,'SHX','France'),(517,'RC Strasbourg','E',5,'STR','France'),(518,'FC Toulouse','E',5,'TOU','France'),(519,'ES Troyes','E',5,'TRY','France'),(600,'ADO Den Haag','F',5,'HAG','Netherlands'),(601,'Ajax Amsterdam','F',3,'AJA','Netherlands'),(602,'AZ Alkmaar','F',5,'AZK','Netherlands'),(603,'FC Groningen','F',5,'GRO','Netherlands'),(604,'FC Twente','F',5,'TWE','Netherlands'),(605,'FC Utrecht','F',5,'UTR','Netherlands'),(606,'Feyenoord','F',3,'FEY','Netherlands'),(607,'Heracles Almelo','F',5,'ALM','Netherlands'),(608,'NAC Breda','F',5,'BDA','Netherlands'),(609,'NEC Nijmegen','F',5,'NEC','Netherlands'),(610,'PSV Eindhoven','F',3,'PSV','Netherlands'),(611,'RBC Roosendaal','F',5,'RBC','Netherlands'),(612,'RKC Waalwijk','F',5,'WAA','Netherlands'),(613,'Roda JC','F',5,'ROD','Netherlands'),(614,'SC Heerenveen','F',5,'HEE','Netherlands'),(615,'Sparta Rotterdam','F',4,'ROT','Netherlands'),(616,'Vitesse','F',5,'VIT','Netherlands'),(617,'Willem II','F',5,'WIL','Netherlands'),(1000,'Benfica','O',3,'BEN','Portugal'),(1001,'Beşiktaş JK','O',4,'BES','Turkey'),(1002,'Celtic FC','O',4,'CEL','Scotland'),(1003,'Djurgårdens IF','O',5,'DJU','Sweden'),(1004,'FC Dynamo Kyiv','O',4,'DYN','Ukraine'),(1005,'Club Brugge','O',5,'BRG','Belgium'),(1006,'FC Petržalka 1898','O',5,'PET','Slovakia'),(1007,'FC Porto','O',3,'POR','Portugal'),(1008,'Fenerbahçe SK','O',4,'FEN','Turkey'),(1009,'Galatasaray SK','O',4,'GAL','Turkey'),(1010,'CSKA Moscow','O',4,'CSK','Russia'),(1011,'Olympiacos CFP','O',4,'CFP','Greece'),(1012,'Panathinaikos FC','O',5,'PAN','Greece'),(1013,'Rangers FC','O',4,'RAN','Scotland'),(1014,'Rosenborg BK','O',5,'ROS','Norway'),(1015,'RSC Anderlecht','O',4,'AND','Belgium'),(1016,'FC Shakhtar Donetsk','O',3,'SHA','Ukraine'),(1017,'AC Sparta Praha','O',5,'PRA','Czech Republic'),(1018,'Sporting CP','O',4,'SPO','Portugal'),(1019,'São Paulo FC','O',4,'SAO','Brazil'),(1020,'FC Thun','O',5,'THU','Switzerland'),(1021,'SK Rapid Wien','O',5,'WIE','Austria'),(2000,'Argentina','X',1,'ARG',''),(2001,'Australia','X',4,'AUS',''),(2002,'Austria','X',5,'AUT',''),(2003,'Belgium','X',3,'BEL',''),(2004,'Brazil','X',1,'BZL',''),(2005,'Bulgaria','X',4,'BUL',''),(2006,'Cameroon','X',3,'CAM',''),(2007,'Chile','X',5,'CHL',''),(2008,'China','X',5,'CHN',''),(2009,'Colombia','X',4,'COL',''),(2010,'Costa Rica','X',5,'CCA',''),(2011,'Cote d\'Ivoire','X',3,'IVO',''),(2012,'Croatia','X',3,'CRO',''),(2013,'Czech Republic','X',2,'CZH',''),(2014,'Denmark','X',3,'DEN',''),(2015,'Ecuador','X',5,'ECU',''),(2016,'England','X',2,'ENG',''),(2017,'Finland','X',4,'FIN',''),(2018,'France','X',2,'FRA',''),(2019,'Germany','X',1,'GER',''),(2020,'Greece','X',4,'GRE',''),(2021,'Hungary','X',5,'HUN',''),(2022,'Iran','X',4,'IRN',''),(2023,'Ireland','X',4,'IRE',''),(2024,'Italy','X',1,'ITA',''),(2025,'Japan','X',4,'JAP',''),(2026,'Latvia','X',5,'LAT',''),(2027,'Mexico','X',4,'MEX',''),(2028,'Morocco','X',5,'MCO',''),(2029,'Netherlands','X',1,'NED',''),(2030,'Nigeria','X',3,'NIG',''),(2031,'Northern Ireland','X',5,'NIR',''),(2032,'Norway','X',4,'NOR',''),(2033,'Paraguay','X',4,'PGY',''),(2034,'Peru','X',4,'PER',''),(2035,'Poland','X',4,'POL',''),(2036,'Portugal','X',2,'PGL',''),(2037,'Romania','X',4,'ROM',''),(2038,'Russia','X',3,'RUS',''),(2039,'Saudi Arabia','X',5,'SAB',''),(2040,'Scotland','X',4,'SCO',''),(2041,'Senegal','X',4,'SEN',''),(2042,'Serbia','X',3,'SBA',''),(2043,'Slovakia','X',4,'SVK',''),(2044,'Slovenia','X',5,'SLO',''),(2045,'South Africa','X',4,'SAF',''),(2046,'South Korea','X',4,'SKA',''),(2047,'Spain','X',1,'SPA',''),(2048,'Sweden','X',2,'SWE',''),(2049,'Switzerland','X',4,'STZ',''),(2050,'Tunisia','X',5,'TUN',''),(2051,'Turkey','X',3,'TUR',''),(2052,'Ukraine','X',3,'UKR',''),(2053,'United States','X',4,'USA',''),(2054,'Uruguay','X',3,'URU',''),(2055,'Venezuela','X',5,'VEN',''),(2056,'Wales','X',4,'WAL',''),(1022,'Boca Juniors','O',4,'BOC','Argentina'),(2059,'Togo','X',5,'TGO',''),(2057,'Ghana','X',4,'GHA',''),(1024,'CA River Plate','O',4,'RIV','Argentina'),(2058,'Trinidad and Tobago','X',5,'TAT',''),(1025,'FC Copenhagen','O',4,'COP','Denmark'),(120,'Sheffield United','A',5,'SHU','England'),(121,'Watford','A',5,'WAT','England'),(122,'Reading','A',5,'REA','England'),(320,'Recreativo de Huelva','C',5,'RDH','Spain'),(220,'Atalanta FC','B',5,'ATA','Italy'),(221,'Torino FC','B',4,'TOR','Italy'),(520,'FC Lorient','E',5,'LOR','France'),(521,'Valenciennes FC','E',5,'VLS','France'),(321,'Levante UD','C',4,'LEV','Spain'),(2060,'Angola','X',5,'ANG',''),(522,'CS Sedan','E',5,'SED','France'),(1027,'SBV Excelsior Rotterdam','F',5,'EXC','Netherlands'),(322,'Gimnastic de Tarragona','C',5,'GIM','Spain'),(2061,'Israel','X',5,'ISR',''),(124,'Derby County FC','A',5,'DRB','England'),(1049,'Red Bull Salzburg','O',5,'SLZ','Austria'),(222,'Genoa CFC','B',4,'GEN','Italy'),(223,'SSC Napoli','B',2,'NAP','Italy'),(323,'UD Almeria','C',5,'UDA','Spain'),(324,'Real Valladolid','C',4,'RVA','Spain'),(325,'Real Murcia','C',4,'MUR','Spain'),(523,'SM Caen','E',5,'CAE','France'),(618,'De Graafschap','F',5,'GRA','Netherlands'),(619,'VVV-Vento','F',5,'VVV','Netherlands'),(1029,'Red Star Belgrade','O',5,'RSB','Serbia'),(1030,'AIK Fotboll','O',5,'AIK','Sweden'),(1031,'FC Spartak Moscow','O',4,'SMO','Russia'),(1032,'FC Lokomotiv Moscow','O',5,'LMO','Russia'),(1033,'Wisła Kraków','O',5,'WIS','Poland'),(1034,'HJK Helsinki','O',5,'HJK','Finland'),(1035,'GNK Dinamo Zagreb','O',5,'ZAG','Croatia'),(1036,'Hammarby Fotboll','O',5,'HAM','Sweden'),(1037,'Helsingborgs IF','O',5,'HEL','Sweden'),(1038,'IFK Göteborg','O',5,'GOT','Sweden'),(1039,'FC Basel','O',4,'BAS','Switzerland'),(1040,'SC Internacional','O',4,'SCI','Brazil'),(1041,'AEK Athens FC','O',4,'AEK','Greece'),(126,'Stoke City','A',5,'STO','England'),(127,'Hull City','A',5,'HUL','England'),(1042,'CFR Cluj','O',5,'CFR','Romania'),(1043,'Standard Liège','O',4,'SLI','Belgium'),(1044,'Zenit St. Petersburg','O',3,'ZEN','Russia'),(1045,'FC Volendam','F',5,'VOL','Netherlands'),(2062,'Thailand','X',5,'THA',''),(2063,'United Arab Emirates','X',5,'UAE',''),(2064,'Canada','X',5,'CAN',''),(2065,'Egypt','X',4,'EGY',''),(418,'TSG 1899 Hoffenheim','D',4,'HOF','Germany'),(419,'VfL Bochum','D',5,'BCH','Germany'),(420,'Karlsruher SC','D',5,'KSC','Germany'),(421,'FC Energie Cottbus','D',5,'COT','Germany'),(1046,'FC Steaua București','O',4,'BUC','Romania'),(224,'Bologna FC','B',4,'BLO','Italy'),(225,'Calcio Catania','B',5,'CAT','Italy'),(524,'Grenoble Foot 38','E',5,'GRN','France'),(525,'Le Havre AC','E',5,'LHA','France'),(326,'CD Numancia','C',5,'NUM','Spain'),(327,'Sporting de Gijon','C',5,'GIJ','Spain'),(226,'AS Bari','B',5,'BRI','Italy'),(328,'CD Tenerife','C',5,'TEN','Spain'),(1047,'APOEL FC','O',5,'APO','Cyprus'),(2066,'Bosnia-Herzegovina','X',4,'BHZ',''),(1048,'FC Rubin Kazan','O',4,'KAZ','Russia'),(1050,'Maccabi Haifa FC','O',5,'HAI','Israel'),(1051,'FC Unirea Urziceni','O',5,'UNI','Romania'),(329,'Xerez CD','C',5,'XER','Spain'),(2067,'Algeria','X',5,'ALG',''),(2068,'North Korea','X',6,'NKO',''),(2069,'Honduras','X',5,'HON',''),(2070,'New Zealand','X',5,'NZL',''),(1052,'JSD Partizan','O',5,'PTZ','Serbia'),(128,'Wolverhampton Wanderers','A',5,'WLV','England'),(1053,'FC Zürich','O',5,'ZUR','Switzerland'),(2071,'Montenegro','X',5,'MON',''),(1054,'SC Braga','O',5,'BRA','Portugal'),(2072,'Qatar','X',5,'QAT',''),(1055,'PAOK FC','O',5,'PAO','Greece'),(1056,'Bursaspor','O',4,'BUR','Turkey'),(1057,'Flamengo Rio de Janeiro','O',4,'FLA','Brazil'),(422,'FC St. Pauli','D',5,'PLI','Germany'),(227,'AC Cesena','B',5,'CES','Italy'),(1058,'Racing Club','O',4,'RCN','Argentina'),(2073,'Mali','X',5,'MLI',''),(330,'Hércules CF','C',5,'HRC','Spain'),(1059,'SC Corinthians Paulista','O',4,'COR','Brazil'),(129,'Queens Park Rangers','A',4,'QPR','England'),(1060,'KRC Genk','O',4,'GNK','Belgium'),(526,'Stade Brestois 29','E',5,'S29','France'),(527,'Montpellier HSC','E',4,'MPL','France'),(1061,'CS Marítimo','O',5,'MRT','Portugal'),(130,'Norwich City FC','A',5,'NWC','England'),(131,'Swansea City AFC','A',5,'SWC','England'),(1062,'CD Nacional da Madeira','O',5,'NAC','Portugal'),(2074,'Panama','X',5,'PNM',''),(1063,'Colo-Colo','O',5,'COC','Chile'),(1064,'Santos FC','O',5,'SNT','Brazil'),(1065,'San Lorenzo','O',5,'SNL','Argentina'),(1066,'Palmeiras','O',5,'PLM','Brazil'),(1067,'Club América','O',5,'CLA','Mexico'),(1068,'Granada CF','C',5,'GND','Spain'),(1069,'SC Beira-Mar','O',5,'BRM','Portugal'),(1070,'SC Olhanense','O',5,'OLH','Portugal'),(1071,'CA Peñarol','O',5,'PNR','Uruguay'),(1072,'Trabzonspor','O',5,'TRZ','Turkey'),(1073,'CD Guadalajara','O',5,'GDL','Mexico'),(1074,'Botafogo FR','O',4,'BTA','Brazil'),(1075,'Al-Ahli SC','O',5,'AHL','Qatar'),(1076,'Legia Warszawa','O',5,'LWZ','Poland'),(1077,'Polonia Warszawa','O',5,'PWZ','Poland'),(1078,'Atlético Mineiro','O',3,'AMI','Brazil'),(1079,'Fluminense FC','O',4,'FLU','Brazil'),(528,'Évian Thonon Gaillard','E',3,'EVI','France'),(228,'Pescara Calcio','B',4,'PES','Italy'),(132,'Southampton FC','A',5,'SHP','England'),(1080,'FC Anzhi Makhachkala','O',3,'ANZ','Russia'),(1081,'Estudiantes de La Plata','O',5,'ELP','Argentina'),(1082,'HNK Hajduk Split','O',5,'HNK','Croatia'),(1083,'Millonarios FC','O',4,'MLL','Colombia'),(1084,'Club Nacional de Football','O',4,'CNF','Uruguay'),(1085,'Ruch Chorzów','O',5,'CHZ','Poland'),(1086,'GKS Bełchatów','O',5,'BCT','Poland'),(1087,'Widzew Łódź','O',5,'LDZ','Poland'),(1088,'Korona Kielce','O',5,'KKL','Poland'),(1089,'CA Lanús','O',5,'LNS','Argentina'),(1090,'Carabobo FC','O',5,'CBB','Venezuela'),(1091,'Zamora FC','O',5,'ZMR','Venezuela'),(1092,'Zulia FC','O',5,'ZUL','Venezuela'),(1093,'Estudiantes de Mérida','O',5,'EDM','Venezuela'),(1094,'Monagas SC','O',5,'MNG','Argentina'),(1095,'Deportivo Táchira','O',5,'DTC','Venezuela'),(1096,'Atlético Venezuela','O',5,'DTC','Venezuela'),(1097,'CA Belgrano','O',5,'BGR','Argentina'),(1098,'Tucanes de Amazonas','O',5,'TDA','Venezuela'),(1099,'Universidad de Los Andes','O',5,'ULA','Venezuela'),(1100,'CS Emelec','O',5,'EML','Equador'),(2075,'Jamaica','X',5,'JMC',''),(2076,'Bolivia','X',5,'BLV',''),(1101,'Trujillanos FC','O',5,'TJI','Venezuela'),(1102,'Mineros de Guayana','O',5,'MDG','Venezuela'),(1103,'Deportivo Anzoátegui','O',5,'ANZ','Venezuela'),(1104,'Universidad Central FC','O',5,'UCV','Venezuela'),(1105,'Margarita FC','O',5,'MRG','Venezuela'),(1106,'CA Falcón','O',5,'FLC','Venezuela'),(1107,'UA Alto Apure','O',5,'AAP','Venezuela'),(1108,'JBL Zulia','O',5,'JBL','Venezuela'),(1109,'Yaracuyanos FC','O',5,'YRC','Venezuela'),(1110,'La Trinidad FC','O',5,'LTR','Venezuela'),(1111,'CD Lara','O',5,'CDL','Venezuela'),(1112,'Lotería del Táchira FC','O',5,'LDT','Venezuela'),(1113,'Ortiz FC','O',5,'ORT','Venezuela'),(1114,'Fundación Cesarger','O',5,'FCS','Venezuela'),(1115,'UD Guajira','O',5,'GJI','Venezuela'),(1116,'Atlético Sucre CF','O',5,'SCR','Venezuela'),(2077,'Vietnam','X',5,'VTN',''),(1117,'FC Dynamo Moscow','O',4,'DYM','Russia'),(423,'SC Freiburg','D',4,'FRB','Germany'),(1118,'Deportivo Petare FC','O',5,'PTR','Venezuela'),(1119,'Caracas FC','O',5,'CRC','Venezuela'),(1120,'Rosario Central','O',5,'ROS','Argentina'),(1121,'Quilmes Atlético Club','O',5,'ROS','Argentina'),(1122,'Grêmio FBPA','O',5,'GRM','Brazil'),(229,'U.S. Sassuolo Calcio','B',5,'SAS','Italy'),(133,'Crystal Palace FC','A',5,'CRY','England'),(1123,'Club Tijuana','O',5,'TIJ','Mexico'),(1124,'Cruzeiro EC','O',5,'CRZ','Brazil'),(1125,'Argentinos Juniors','O',5,'AJU','Argentina'),(1126,'FC Zorya Luhansk','O',5,'ZLH','Ukraine'),(1127,'FC Dnipro Dnipropetrovsk','O',5,'DNP','Ukraine'),(134,'Cardiff City FC','A',5,'CRD','Wales'),(1128,'Laos FC','O',5,'LAO','Philippines'),(1129,'Al Ahly SC','O',5,'ALY','Egypt'),(1130,'Zamalek SC','O',5,'ZAM','Egypt'),(1131,'Rayo Vallecano','O',5,'RYV','Spain'),(424,'FC Augsburg','D',4,'AUG','Germany'),(1132,'Deportivo Peñarol FC','O',5,'DPN','Venezuela'),(1133,'Llaneros de Guanare FC','O',5,'LDG','Venezuela'),(1134,'Espérance de Tunis','O',5,'EDT','Tunisia'),(1135,'New York Red Bulls','O',5,'NYR','United States'),(1136,'Atlético Nacional','O',4,'ANC','Colombia'),(1137,'Deportivo La Guaira','O',5,'DLG','Venezuela'),(1138,'Unión Atlético Maracaibo','O',5,'MRC','Venezuela'),(1139,'Arroceros de Calabozo FC','O',5,'CLB','Venezuela'),(1140,'Coritiba','O',5,'CRT','Brazil'),(1141,'Academia Emeritense FC','O',5,'AEM','Venezuela'),(1142,'Club Africain','O',5,'CAF','Tunisia'),(425,'Eintracht Braunschweig','D',5,'EBR','Germany'),(2078,'Zambia','X',5,'ZMB',''),(1143,'EC Bahia','O',5,'BHI','Brazil'),(1144,'Sturm Graz','O',5,'GRZ','Austria'),(2079,'Jordan','X',5,'JRD',''),(230,'CE Constantí','B',5,'CEC','Spain'),(231,'SS Virtus Lanciano','B',5,'VRT','Italy');
/*!40000 ALTER TABLE `weblm_teams` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `weblm_topics`
--

DROP TABLE IF EXISTS `weblm_topics`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `weblm_topics` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `topic` varchar(5) NOT NULL DEFAULT '',
  `active` char(1) NOT NULL DEFAULT 'Y',
  `prio` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `weblm_topics`
--

LOCK TABLES `weblm_topics` WRITE;
/*!40000 ALTER TABLE `weblm_topics` DISABLE KEYS */;
/*!40000 ALTER TABLE `weblm_topics` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `weblm_tournaments`
--

DROP TABLE IF EXISTS `weblm_tournaments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `weblm_tournaments` (
  `id` int(2) NOT NULL AUTO_INCREMENT,
  `cupName` varchar(30) NOT NULL DEFAULT '',
  `imageFilename` varchar(20) NOT NULL DEFAULT '',
  `imageMaxsize` varchar(10) NOT NULL DEFAULT '',
  `extension` varchar(5) NOT NULL DEFAULT '',
  `forumLink` varchar(50) NOT NULL DEFAULT '',
  `trophyImage` varchar(30) NOT NULL DEFAULT '',
  `profileImage` varchar(30) NOT NULL DEFAULT '',
  `getParam` varchar(20) NOT NULL DEFAULT '',
  `startDate` varchar(10) NOT NULL DEFAULT '',
  `endDate` varchar(10) NOT NULL DEFAULT '',
  `uploaders` varchar(50) NOT NULL DEFAULT '',
  `firstPlace` varchar(20) NOT NULL DEFAULT '',
  `secondPlace` varchar(20) NOT NULL DEFAULT '',
  `thirdPlace` varchar(20) NOT NULL DEFAULT '',
  `updateDate` varchar(15) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `weblm_tournaments`
--

LOCK TABLES `weblm_tournaments` WRITE;
/*!40000 ALTER TABLE `weblm_tournaments` DISABLE KEYS */;
/*!40000 ALTER TABLE `weblm_tournaments` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `weblm_tournaments_images`
--

DROP TABLE IF EXISTS `weblm_tournaments_images`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `weblm_tournaments_images` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `tid` int(11) NOT NULL DEFAULT '0',
  `slot` tinyint(4) NOT NULL DEFAULT '0',
  `ext` varchar(5) NOT NULL DEFAULT '',
  `description` varchar(255) NOT NULL DEFAULT '',
  `updateDate` varchar(15) NOT NULL DEFAULT '',
  `user` varchar(30) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `weblm_tournaments_images`
--

LOCK TABLES `weblm_tournaments_images` WRITE;
/*!40000 ALTER TABLE `weblm_tournaments_images` DISABLE KEYS */;
/*!40000 ALTER TABLE `weblm_tournaments_images` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `weblm_tournaments_mini`
--

DROP TABLE IF EXISTS `weblm_tournaments_mini`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `weblm_tournaments_mini` (
  `ID` smallint(4) NOT NULL AUTO_INCREMENT,
  `PLAYER_ID` smallint(4) NOT NULL DEFAULT '0',
  `LINK` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`ID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `weblm_tournaments_mini`
--

LOCK TABLES `weblm_tournaments_mini` WRITE;
/*!40000 ALTER TABLE `weblm_tournaments_mini` DISABLE KEYS */;
/*!40000 ALTER TABLE `weblm_tournaments_mini` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `weblm_vars`
--

DROP TABLE IF EXISTS `weblm_vars`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `weblm_vars` (
  `vars_id` int(10) NOT NULL AUTO_INCREMENT,
  `color1` varchar(20) DEFAULT NULL,
  `color2` varchar(20) DEFAULT NULL,
  `color3` varchar(20) DEFAULT NULL,
  `color4` varchar(20) DEFAULT NULL,
  `color5` varchar(20) DEFAULT NULL,
  `color6` varchar(20) DEFAULT NULL,
  `color7` varchar(20) DEFAULT NULL,
  `font` varchar(80) DEFAULT NULL,
  `fontweight` varchar(40) DEFAULT NULL,
  `fontsize` varchar(20) DEFAULT NULL,
  `numgamespage` int(10) DEFAULT NULL,
  `numplayerspage` int(10) DEFAULT NULL,
  `statsnum` int(10) DEFAULT NULL,
  `hotcoldnum` varchar(10) DEFAULT NULL,
  `gamesmaxdayplayer` int(10) DEFAULT NULL,
  `gamesmaxday` int(10) DEFAULT NULL,
  `approve` varchar(10) DEFAULT NULL,
  `approvegames` varchar(10) DEFAULT NULL,
  `system` varchar(40) DEFAULT NULL,
  `pointswin` int(10) DEFAULT NULL,
  `pointsloss` int(10) DEFAULT NULL,
  `report` varchar(20) DEFAULT NULL,
  `newsitems` int(10) DEFAULT NULL,
  `copyright` text,
  `ra2ladderneg` char(3) DEFAULT NULL,
  `uplfichierreport` char(3) DEFAULT NULL,
  `uplfichierreportforce` char(3) DEFAULT NULL,
  `maxsizereplayupl` int(11) DEFAULT NULL,
  `extvalable1` varchar(4) DEFAULT NULL,
  `extvalable2` varchar(4) DEFAULT NULL,
  `extvalable3` varchar(4) DEFAULT NULL,
  `idcontrol` varchar(10) DEFAULT NULL,
  `reportresult` char(3) DEFAULT NULL,
  `adminmail` varchar(50) DEFAULT NULL,
  `allowpswdmail` char(3) DEFAULT NULL,
  `maxplayers` int(11) DEFAULT NULL,
  `maxgameslinkpage` int(11) DEFAULT NULL,
  `maintenance` char(3) DEFAULT 'no',
  `season` int(2) NOT NULL DEFAULT '1',
  `autoApprove` tinyint(4) NOT NULL DEFAULT '1',
  PRIMARY KEY (`vars_id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `weblm_vars`
--

LOCK TABLES `weblm_vars` WRITE;
/*!40000 ALTER TABLE `weblm_vars` DISABLE KEYS */;
INSERT INTO `weblm_vars` VALUES (1,'#000000','#FFFFFF','#00097F','#0009d1','#EEEEEE','#000000','#FFFFFF','Verdana, Arial, Helvetica, sans-serif','normal','10',15,250,1000,'5',6,25,'yes','no','Ra2 system ladder',2,-1,'winner',4,'','no','no','no',1000000,'jpg','gif','rep','cookies','yes','admin@yoursite.com','yes',111,15,'no',1,1);
/*!40000 ALTER TABLE `weblm_vars` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `weblm_versions`
--

DROP TABLE IF EXISTS `weblm_versions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `weblm_versions` (
  `id` smallint(6) NOT NULL AUTO_INCREMENT,
  `version` char(1) NOT NULL DEFAULT '',
  `name` varchar(15) NOT NULL DEFAULT '',
  `image` varchar(16) NOT NULL,
  `grouping` varchar(30) NOT NULL,
  `color` varchar(6) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `version-grouping` (`version`,`grouping`)
) ENGINE=MyISAM AUTO_INCREMENT=38 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `weblm_versions`
--

LOCK TABLES `weblm_versions` WRITE;
/*!40000 ALTER TABLE `weblm_versions` DISABLE KEYS */;
INSERT INTO `weblm_versions` VALUES (1,'A','PES4 PC','pes4_pc.png','PES4/WE8I','eb271a'),(2,'B','PES4 XBox','pes4_xbox.png','PES4/WE8I','eb271a'),(3,'C','WE8I PC','we8i_pc.png','PES4/WE8I','eb271a'),(4,'D','PES5 PC','pes5_pc.png','PES5/WE9I/WE9LE','fef500'),(5,'E','PES5 PS2','pes5_ps2.png','PES5/WE9I/WE9LE','fef500'),(6,'F','PES5 XBox','pes5_xbox.png','PES5/WE9I/WE9LE','fef500'),(7,'G','WE9I PC','we9i_pc.png','PES5/WE9I/WE9LE','fef500'),(9,'H','PES6 PC','pes6_pc.png','PES6','0063b1'),(10,'I','PES6 PS2','pes6_ps2.png','PES6','0063b1'),(11,'J','PES6 X360','pes6_360.png','PES6','0063b1'),(12,'K','WE 2007 PS2','pes2k7_ps2.png','WE:PES2007','3c368e'),(13,'L','WE 2007 X360','pes2k7_x360.png','WE:PES2007','3c368e'),(14,'M','WE 2007 PC','pes2k7_pc.png','WE:PES2007','3c368e'),(15,'O','PES 2008 PC','pes2k8_pc.png','PES2008','04a841'),(16,'P','PES 2008 PS2','pes2k8_ps2.png','PES2008','04a841'),(17,'Q','PES 2008 PS3','pes2k8_ps3.png','PES2008','04a841'),(18,'R','PES 2008 X360','pes2k8_x360.png','PES2008','04a841'),(19,'S','PES 2009 PC','pes2k9_pc.png','PES2009','8c2c86'),(20,'T','PES 2009 PS3','pes2k9_ps3.png','PES2009','8c2c86'),(21,'U','PES 2009 X360','pes2k9_x360.png','PES2009','8c2c86'),(22,'V','PES 2010 PC','pes2010_pc.png','PES2010','f05620'),(23,'W','PES 2010 PS3','pes2010_ps3.png','PES2010','f05620'),(24,'X','PES 2010 X360','pes2010_x360.png','PES2010','f05620'),(8,'Y','WE9LE PC','we9le_pc.png','PES5/WE9I/WE9LE','fef500'),(25,'Z','PES 2011 PC','pes2011_pc.png','PES2011','73c03c'),(26,'1','PES 2011 PS3','pes2011_ps3.png','PES2011','73c03c'),(27,'2','PES 2011 X360','pes2011_x360.png','PES2011','73c03c'),(28,'3','PES 2012 PC','pes2012_pc.png','PES2012','f48322'),(29,'4','PES 2012 PS3','pes2012_ps3.png','PES2012','f48322'),(30,'5','PES 2012 X360','pes2012_x360.png','PES2012','f48322'),(31,'6','PES 2013 PC','pes2013_pc.png','PES2013','1bb7b2'),(32,'7','PES 2013 PS3','pes2013_ps3.png','PES2013','1bb7b2'),(33,'8','PES 2013 X360','pes2013_x360.png','PES2013','1bb7b2'),(34,'9','PES 2014 PC','pes2014_pc.png','PES2014','ff00a8'),(35,'0','PES 2014 PS3','pes2014_ps3.png','PES2014','ff00a8'),(36,'#','PES 2014 X360','pes2014_x360.png','PES2014','ff00a8');
/*!40000 ALTER TABLE `weblm_versions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `weblm_votes`
--

DROP TABLE IF EXISTS `weblm_votes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `weblm_votes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `goal_id` smallint(6) NOT NULL DEFAULT '0',
  `player_id` smallint(6) NOT NULL DEFAULT '0',
  `rating` tinyint(4) NOT NULL DEFAULT '0',
  `ratedate` varchar(10) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `weblm_votes`
--

LOCK TABLES `weblm_votes` WRITE;
/*!40000 ALTER TABLE `weblm_votes` DISABLE KEYS */;
/*!40000 ALTER TABLE `weblm_votes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `wtagshoutbox`
--

DROP TABLE IF EXISTS `wtagshoutbox`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `wtagshoutbox` (
  `messageid` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(30) NOT NULL,
  `url` varchar(100) NOT NULL,
  `message` text NOT NULL,
  `ip` int(11) NOT NULL,
  `date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`messageid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `wtagshoutbox`
--

LOCK TABLES `wtagshoutbox` WRITE;
/*!40000 ALTER TABLE `wtagshoutbox` DISABLE KEYS */;
/*!40000 ALTER TABLE `wtagshoutbox` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2015-03-26 16:55:10
