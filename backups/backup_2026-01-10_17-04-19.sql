-- MySQL dump 10.13  Distrib 8.0.37, for Win64 (x86_64)
--
-- Host: localhost    Database: TDW
-- ------------------------------------------------------
-- Server version	9.1.0

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `actualites`
--

DROP TABLE IF EXISTS `actualites`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `actualites` (
  `id_actualite` int NOT NULL AUTO_INCREMENT,
  `titre` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `contenu` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `type_actualite_id` int DEFAULT NULL,
  `image` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `detail` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `date_publication` datetime DEFAULT CURRENT_TIMESTAMP,
  `afficher_diaporama` tinyint(1) DEFAULT '0',
  `ordre_diaporama` int DEFAULT '0',
  PRIMARY KEY (`id_actualite`),
  KEY `type_actualite_id` (`type_actualite_id`)
) ENGINE=MyISAM AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `actualites`
--

LOCK TABLES `actualites` WRITE;
/*!40000 ALTER TABLE `actualites` DISABLE KEYS */;
INSERT INTO `actualites` VALUES (1,'Lancement du projet AICARE : Intelligence Artificielle pour la Santé','Notre laboratoire annonce le lancement officiel du projet AICARE, financé par l\'ANR à hauteur de 1,2M€. Ce projet ambitieux vise à développer des algorithmes d\'apprentissage profond pour l\'aide au diagnostic médical. En collaboration avec le CHU local et l\'Université de Paris-Saclay, nous travaillerons sur l\'analyse d\'images médicales et la prédiction de pathologies. Le projet s\'étendra sur 4 ans et impliquera 3 doctorants, 2 post-doctorants et 5 chercheurs permanents.',1,'AI-Actu.jpg','Le projet AICARE (Artificial Intelligence for Computer-Aided medical caRE) représente une avancée significative dans l\'application de l\'intelligence artificielle au domaine médical. Doté d\'un financement de 1,2 million d\'euros sur 4 ans par l\'Agence Natio','2025-11-15 10:00:00',1,1),(2,'Publication majeure dans Nature Machine Intelligence','Le Dr. Sarah Martin et son équipe publient leurs travaux révolutionnaires sur l\'IA explicable dans la prestigieuse revue Nature Machine Intelligence. L\'article intitulé \"Transparent Deep Learning: A Novel Framework for Interpretable Neural Networks\" présente une nouvelle approche permettant de comprendre les décisions prises par les réseaux de neurones profonds. Cette avancée majeure pourrait avoir des implications importantes pour l\'utilisation de l\'IA dans des domaines critiques comme la santé et la justice.',2,'ML-Actu.jpg','L\'article publié dans Nature Machine Intelligence (Impact Factor: 25.898) marque une étape importante dans le domaine de l\'IA explicable (XAI). Les travaux du Dr. Sarah Martin et de son équipe de 8 chercheurs proposent un nouveau framework baptisé \"Transp','2025-11-28 14:30:00',1,2),(3,'Conférence Internationale sur la Robotique Mobile - Inscriptions ouvertes','Notre laboratoire a l\'honneur d\'accueillir la 8ème Conférence Internationale sur la Robotique Mobile (CIRM 2025) du 15 au 17 janvier 2026. Cet événement majeur réunira plus de 200 chercheurs du monde entier pour discuter des dernières avancées en robotique autonome, navigation intelligente et interaction homme-robot. Les inscriptions sont désormais ouvertes avec un tarif préférentiel jusqu\'au 20 décembre. Programme détaillé disponible sur le site de la conférence.',3,'Robotique-Actu.jpg','La Conférence Internationale sur la Robotique Mobile (CIRM) est l\'un des événements phares dans le domaine de la robotique autonome. Pour sa 8ème édition, notre laboratoire est fier d\'accueillir plus de 200 participants internationaux provenant de 35 pays','2025-12-01 09:00:00',1,3),(4,'Soutenance de thèse : Omar Benzaid - Deep Reinforcement Learning','Omar Benzaid soutiendra sa thèse intitulée \"Apprentissage par renforcement profond pour la navigation autonome de robots mobiles en environnements dynamiques\" le vendredi 20 décembre 2025 à 14h00 dans l\'amphithéâtre Pierre Curie. Sous la direction du Pr. Jean Dupont, cette thèse présente des contributions significatives à l\'application du deep reinforcement learning dans des contextes robotiques complexes. La soutenance sera suivie d\'un pot de thèse. Tous les membres du laboratoire sont invités à y assister.',4,'Soutenance-Actu.jpg','Omar Benzaid défendra sa thèse de doctorat en Informatique après 3 années de recherche intensive. Ses travaux portent sur l\'utilisation d\'algorithmes d\'apprentissage par renforcement profond (Deep Q-Learning, Proximal Policy Optimization, Soft Actor-Criti','2025-12-04 08:30:00',0,0),(5,'Nouveau partenariat stratégique avec IBM Research','Le laboratoire est fier d\'annoncer la signature d\'un accord de collaboration majeur avec IBM Research pour une durée de 5 ans. Ce partenariat porte sur le développement de solutions d\'IA quantique et de calcul haute performance. IBM mettra à disposition ses infrastructures quantum computing et ses experts pour accompagner nos projets de recherche. Trois bourses de thèse seront financées dès 2026 dans le cadre de ce partenariat. Une première réunion de lancement est prévue le 15 janvier avec la visite d\'une délégation d\'IBM comprenant des chercheurs seniors et des responsables R&D. Ce partenariat ouvre des perspectives passionnantes pour nos travaux sur l\'optimisation combinatoire et la cryptographie quantique.',5,'IBM-Actu.jpg','L\'accord de partenariat signé avec IBM Research représente une opportunité exceptionnelle pour notre laboratoire. D\'une durée de 5 ans renouvelable, ce partenariat stratégique permettra à nos chercheurs d\'accéder aux infrastructures de calcul quantique d\'','2025-12-05 11:00:00',1,4),(6,'Trois doctorants du laboratoire primés au Concours National de Thèses','Excellente nouvelle pour notre laboratoire ! Trois de nos doctorants ont été récompensés lors du 12ème Concours National de Thèses en Informatique organisé par l\'Association Française d\'Informatique (AFI). Leila Amrani a remporté le Prix d\'Excellence pour ses travaux sur les réseaux de neurones génératifs appliqués à la synthèse d\'images médicales. Karim Bencheikh a obtenu le 2ème prix dans la catégorie \"Systèmes Intelligents\" pour sa thèse sur les algorithmes d\'apprentissage fédéré. Enfin, Fatima Zohra Kaci a reçu une mention spéciale du jury pour ses recherches innovantes en traitement automatique du langage naturel pour les langues peu dotées. Ces distinctions témoignent de l\'excellence de la recherche menée au sein de notre équipe et du talent de nos jeunes chercheurs. Toutes nos félicitations à eux ainsi qu\'à leurs directeurs de thèse !',2,'Prix-Actu.jpg','Le 12ème Concours National de Thèses en Informatique, organisé par l\'Association Française d\'Informatique (AFI), a récompensé l\'excellence de trois doctorants de notre laboratoire parmi 250 candidatures. Leila Amrani a reçu le Prix d\'Excellence (dotation ','2025-12-03 16:45:00',0,0);
/*!40000 ALTER TABLE `actualites` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `backups`
--

DROP TABLE IF EXISTS `backups`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `backups` (
  `id` int NOT NULL AUTO_INCREMENT,
  `filename` varchar(255) NOT NULL,
  `filesize` bigint DEFAULT '0',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `backups`
--

LOCK TABLES `backups` WRITE;
/*!40000 ALTER TABLE `backups` DISABLE KEYS */;
/*!40000 ALTER TABLE `backups` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `documents_personnels`
--

DROP TABLE IF EXISTS `documents_personnels`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `documents_personnels` (
  `id_document` int NOT NULL AUTO_INCREMENT,
  `usr_id` int NOT NULL,
  `titre` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `type` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `fichier` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `taille_fichier` int DEFAULT NULL,
  `date_upload` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_document`),
  KEY `usr_id` (`usr_id`)
) ENGINE=MyISAM AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `documents_personnels`
--

LOCK TABLES `documents_personnels` WRITE;
/*!40000 ALTER TABLE `documents_personnels` DISABLE KEYS */;
INSERT INTO `documents_personnels` VALUES (6,2,'CV','CV','6940a51df2e82_1765844253.pdf',92570,'2025-12-15 23:17:33'),(8,2,'publication','Publication','6940a5720f70d_1765844338.ppt',776704,'2025-12-15 23:18:58');
/*!40000 ALTER TABLE `documents_personnels` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `equipements`
--

DROP TABLE IF EXISTS `equipements`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `equipements` (
  `id_equipement` int NOT NULL AUTO_INCREMENT,
  `nom` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `type_equipement_id` int DEFAULT NULL,
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  `numero_serie` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `localisation` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `etat` enum('libre','reserve','maintenance','hors_service') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT 'libre',
  `capacite` int DEFAULT NULL,
  `date_acquisition` date DEFAULT NULL,
  `date_creation` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `is_deleted` tinyint(1) DEFAULT '0',
  PRIMARY KEY (`id_equipement`),
  KEY `type_equipement_id` (`type_equipement_id`)
) ENGINE=MyISAM AUTO_INCREMENT=36 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `equipements`
--

LOCK TABLES `equipements` WRITE;
/*!40000 ALTER TABLE `equipements` DISABLE KEYS */;
INSERT INTO `equipements` VALUES (1,'Salle de conférence A',1,'Grande salle équipée pour les conférences et séminaires, avec projecteur HD et système audio','SALA-001','Bâtiment A - 1er étage','libre',50,'2020-01-15','2025-12-20 11:04:23',0),(2,'Salle de réunion B',1,'Salle de réunion pour petits groupes avec écran interactif','SALB-002','Bâtiment A - 2ème étage','reserve',12,'2021-03-20','2025-12-20 11:04:23',0),(3,'Salle de TP Informatique',1,'Salle équipée de 30 postes informatiques pour travaux pratiques','SALTP-003','Bâtiment B - RDC','reserve',30,'2019-09-10','2025-12-20 11:04:23',0),(4,'Amphithéâtre C',1,'Grand amphithéâtre pour cours magistraux et soutenances','AMPH-004','Bâtiment A - RDC','libre',100,'2018-06-05','2025-12-20 11:04:23',0),(5,'Salle de visioconférence',1,'Salle dédiée aux visioconférences avec équipement professionnel','SALV-005','Bâtiment A - 3ème étage','libre',8,'2022-01-10','2025-12-20 11:04:23',0),(6,'Serveur de calcul HPC',2,'Serveur haute performance avec 64 cœurs et 256GB RAM pour calcul intensif','SRV-HPC-001','Salle serveurs - Bâtiment C','libre',10,'2021-05-15','2025-12-20 11:04:23',0),(7,'Serveur de base de données',2,'Serveur dédié aux bases de données avec stockage SSD 4TB','SRV-DB-002','Salle serveurs - Bâtiment C','libre',15,'2020-11-20','2025-12-20 11:04:23',0),(8,'Serveur Web',2,'Serveur pour hébergement d\'applications web et sites du laboratoire','SRV-WEB-003','Salle serveurs - Bâtiment C','reserve',7,'2019-08-30','2025-12-20 11:04:23',0),(9,'Serveur de stockage NAS',2,'Système de stockage réseau 20TB pour données de recherche','SRV-NAS-004','Salle serveurs - Bâtiment C','libre',9,'2022-03-12','2025-12-20 11:04:23',0),(10,'Serveur de virtualisation',2,'Serveur VMware pour machines virtuelles et tests','SRV-VM-005','Salle serveurs - Bâtiment C','libre',12,'2021-09-25','2025-12-20 11:04:23',0),(11,'PC Workstation 1',3,'Poste de travail haute performance pour développement et simulations','PC-WS-001','Bureau 201 - Bâtiment B','libre',18,'2022-01-10','2025-12-20 11:04:23',0),(12,'PC Workstation 2',3,'Poste de travail pour traitement d\'image et IA','PC-WS-002','Bureau 202 - Bâtiment B','reserve',10,'2022-01-10','2025-12-20 11:04:23',0),(13,'PC Portable Dell XPS',3,'Ordinateur portable haute performance pour déplacements','PC-LAP-001','Stock - Bâtiment A','libre',20,'2021-11-05','2025-12-20 11:04:23',0),(14,'PC Gaming RTX 3080',3,'PC avec carte graphique haute performance pour IA et jeux','PC-GAM-001','Laboratoire IA - Bâtiment B','reserve',15,'2022-06-15','2025-12-20 11:04:23',0),(15,'iMac 27 pouces',3,'iMac pour développement iOS et design graphique','PC-MAC-001','Bureau 305 - Bâtiment A','maintenance',3,'2021-03-20','2025-12-20 11:04:23',0),(16,'Robot TurtleBot 3',4,'Robot mobile éducatif pour recherche en robotique et navigation','ROB-TB3-001','Laboratoire robotique - Bâtiment C','libre',5,'2021-04-12','2025-12-20 11:04:23',0),(17,'Bras robotique UR5',4,'Bras collaboratif Universal Robots pour manipulation d\'objets','ROB-UR5-001','Laboratoire robotique - Bâtiment C','libre',2,'2020-09-18','2025-12-20 11:04:23',0),(18,'Drone DJI Phantom 4',4,'Drone pour recherche en vision par ordinateur et cartographie','ROB-DRN-001','Stock équipement - Bâtiment C','reserve',6,'2021-06-22','2025-12-20 11:04:23',0),(19,'Robot humanoïde NAO',4,'Robot humanoïde programmable pour interaction homme-machine','ROB-NAO-001','Laboratoire IHM - Bâtiment B','libre',11,'2019-12-10','2025-12-20 11:04:23',0),(20,'Kit Arduino Robotique',4,'Ensemble de composants Arduino pour prototypage robotique','ROB-ARD-001','Salle TP - Bâtiment B','libre',3,'2022-02-05','2025-12-20 11:04:23',0),(21,'Imprimante 3D Prusa i3',5,'Imprimante 3D pour prototypage rapide et fabrication de pièces','IMP-3D-001','Atelier fabrication - Bâtiment C','libre',5,'2021-07-08','2025-12-20 11:04:23',0),(22,'Imprimante laser HP LaserJet',5,'Imprimante laser couleur pour documents administratifs','IMP-LAS-001','Secrétariat - Bâtiment A','libre',7,'2020-03-15','2025-12-20 11:04:23',0),(23,'Imprimante 3D Ultimaker S5',5,'Imprimante 3D professionnelle haute précision','IMP-3D-002','Atelier fabrication - Bâtiment C','maintenance',3,'2022-04-20','2025-12-20 11:04:23',0),(24,'Scanner 3D EinScan',5,'Scanner 3D pour numérisation d\'objets et rétro-ingénierie','IMP-SC3-001','Atelier fabrication - Bâtiment C','libre',10,'2021-10-12','2025-12-20 11:04:23',0),(25,'Traceur HP DesignJet',5,'Traceur grand format pour plans et posters scientifiques','IMP-TRA-001','Bureau impression - Bâtiment A','libre',19,'2019-11-28','2025-12-20 11:04:23',0),(26,'Caméra Intel RealSense',6,'Caméra de profondeur RGB-D pour vision 3D et reconnaissance','CAP-CAM-001','Laboratoire vision - Bâtiment B','libre',9,'2021-05-19','2025-12-20 11:04:23',0),(27,'Kit capteurs IoT',6,'Ensemble de capteurs (température, humidité, mouvement) pour IoT','CAP-IOT-001','Laboratoire IoT - Bâtiment C','libre',8,'2022-01-25','2025-12-20 11:04:23',0),(28,'LiDAR Velodyne VLP-16',6,'Capteur LiDAR 16 canaux pour cartographie et navigation','CAP-LID-001','Laboratoire robotique - Bâtiment C','reserve',12,'2020-12-08','2025-12-20 11:04:23',0),(29,'Capteur de force ATI',6,'Capteur 6 axes pour mesure de force et couple en robotique','CAP-FOR-001','Laboratoire robotique - Bâtiment C','libre',5,'2021-08-14','2025-12-20 11:04:23',0),(30,'Caméra thermique FLIR',6,'Caméra thermique pour détection de chaleur et analyse','CAP-THE-001','Stock équipement - Bâtiment C','libre',9,'2022-03-30','2025-12-20 11:04:23',0),(31,'Capteur ultrason HC-SR04',6,'Lot de 10 capteurs ultrasons pour mesure de distance','CAP-ULT-001','Salle TP - Bâtiment B','libre',8,'2021-09-05','2025-12-20 11:04:23',0),(32,'Accéléromètre 3 axes',6,'Capteur d\'accélération pour mesure de mouvement','CAP-ACC-001','Laboratoire IoT - Bâtiment C','libre',2,'2022-02-18','2025-12-20 11:04:23',0),(33,'GPS RTK',6,'Module GPS haute précision pour géolocalisation','CAP-GPS-001','Laboratoire robotique - Bâtiment C','maintenance',3,'2020-10-22','2025-12-20 11:04:23',0),(34,'Robot T30',4,'',NULL,'Bâtiment A - 3ème étage','libre',5,NULL,'2026-01-03 20:10:07',0),(35,'Imprimante 3D',5,'Imprimante 3D très sophistiquée',NULL,'Bâtiment A - 3ème étage','libre',4,NULL,'2026-01-10 08:36:06',0);
/*!40000 ALTER TABLE `equipements` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `evenements`
--

DROP TABLE IF EXISTS `evenements`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `evenements` (
  `id_evenement` int NOT NULL AUTO_INCREMENT,
  `titre` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `type_evenement_id` int DEFAULT NULL,
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  `lieu` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `date_debut` datetime NOT NULL,
  `date_fin` datetime NOT NULL,
  `organisateur_id` int DEFAULT NULL,
  `capacite_max` int DEFAULT NULL,
  `externe` tinyint(1) DEFAULT '0',
  `statut` enum('a_venir','en_cours','termine','annule') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT 'a_venir',
  `date_creation` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_evenement`),
  KEY `organisateur_id` (`organisateur_id`),
  KEY `type_evenement_id` (`type_evenement_id`)
) ENGINE=MyISAM AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `evenements`
--

LOCK TABLES `evenements` WRITE;
/*!40000 ALTER TABLE `evenements` DISABLE KEYS */;
INSERT INTO `evenements` VALUES (1,'Atelier Pratique : Introduction à TensorFlow 2.0',1,'Atelier de formation pratique destiné aux doctorants et étudiants en master. Au programme : installation et configuration de TensorFlow, construction de réseaux de neurones, entraînement de modèles et déploiement. Les participants travailleront sur des cas pratiques concrets avec des jeux de données réels. Prérequis : connaissances de base en Python et apprentissage automatique. Places limitées à 25 participants.','Salle de TP A-304','2025-12-16 09:00:00','2025-12-16 17:00:00',NULL,25,0,'a_venir','2025-12-06 11:32:05'),(2,'Séminaire : Éthique et IA - Enjeux et Perspectives',2,'Séminaire animé par le Pr. Marie Lefebvre, philosophe spécialisée en éthique des technologies. Discussion sur les implications éthiques de l\'intelligence artificielle : biais algorithmiques, transparence des décisions automatisées, protection de la vie privée et impact sociétal. Session de questions-réponses et débat ouvert avec le public.','Amphithéâtre Marie Curie','2025-12-10 14:00:00','2025-12-10 16:30:00',NULL,150,1,'a_venir','2025-12-06 11:32:05'),(3,'Conférence Internationale sur la Robotique Mobile (CIRM 2025)',3,'8ème édition de la conférence internationale dédiée à la robotique mobile. Thématiques : navigation autonome, SLAM, perception 3D, apprentissage par renforcement pour robots, systèmes multi-robots. 3 keynotes de chercheurs renommés, 40 présentations orales, 60 posters, ateliers techniques et démonstrations de robots. Soirée de gala le 16 janvier.','Centre de Congrès Universitaire','2026-01-15 08:30:00','2026-01-17 18:00:00',NULL,250,1,'a_venir','2025-12-06 11:32:05'),(4,'Soutenance de thèse : Omar Benzaid',4,'Thèse de doctorat en Informatique - Spécialité : Intelligence Artificielle et Robotique. Titre : \"Apprentissage par renforcement profond pour la navigation autonome de robots mobiles en environnements dynamiques\". Jury composé de 6 membres dont 2 rapporteurs internationaux. Direction de thèse : Pr. Jean Dupont. Co-encadrement : Dr. Sophie Bernard.','Amphithéâtre Pierre Curie','2025-12-20 14:00:00','2025-12-20 17:00:00',NULL,80,1,'a_venir','2025-12-06 11:32:05'),(5,'Journée Portes Ouvertes du Laboratoire',1,'Découvrez nos activités de recherche ! Visites guidées des plateformes expérimentales, démonstrations de robots autonomes, présentation des projets en cours, rencontres avec les chercheurs et doctorants. Événement ouvert au grand public, lycéens et étudiants. Ateliers ludiques pour découvrir l\'IA et la programmation. Restauration sur place.','Bâtiment du Laboratoire - Tous les espaces','2026-01-25 10:00:00','2026-01-25 18:00:00',NULL,200,0,'a_venir','2025-12-06 11:32:05'),(6,'Workshop : Computer Vision et Deep Learning',1,'Workshop intensif de 2 jours sur la vision par ordinateur avec deep learning. Jour 1 : CNNs, architectures modernes (ResNet, EfficientNet, Vision Transformers). Jour 2 : Détection d\'objets (YOLO, Faster R-CNN), segmentation sémantique, applications pratiques. Intervenants : Dr. Pierre Rousseau et Dr. Alice Chen. Certificat de participation délivré.','Salle de formation B-201','2026-02-10 09:00:00','2026-02-11 17:00:00',NULL,30,1,'a_venir','2025-12-06 11:32:05');
/*!40000 ALTER TABLE `evenements` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `historique_equipements`
--

DROP TABLE IF EXISTS `historique_equipements`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `historique_equipements` (
  `id_historique` int NOT NULL AUTO_INCREMENT,
  `equipement_id` int NOT NULL,
  `usr_id` int DEFAULT NULL,
  `action` enum('reservation','annulation','debut_utilisation','fin_utilisation','maintenance','etat_change') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `date_action` datetime DEFAULT CURRENT_TIMESTAMP,
  `reservation_id` int DEFAULT NULL,
  PRIMARY KEY (`id_historique`),
  KEY `equipement_id` (`equipement_id`),
  KEY `usr_id` (`usr_id`),
  KEY `fk_historique_equipements_reservations` (`reservation_id`)
) ENGINE=MyISAM AUTO_INCREMENT=24 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `historique_equipements`
--

LOCK TABLES `historique_equipements` WRITE;
/*!40000 ALTER TABLE `historique_equipements` DISABLE KEYS */;
INSERT INTO `historique_equipements` VALUES (1,3,2,'reservation','2025-12-18 12:04:24',1),(2,8,6,'reservation','2025-12-20 12:04:24',2),(3,32,3,'annulation','2025-12-21 13:44:46',NULL),(4,19,3,'annulation','2025-12-21 13:47:29',NULL),(5,2,3,'reservation','2025-12-21 14:04:30',NULL),(6,2,3,'reservation','2025-12-30 08:37:27',NULL),(7,22,3,'annulation','2025-12-30 08:59:58',NULL),(8,14,1,'reservation','2026-01-01 23:04:20',NULL),(9,14,1,'debut_utilisation','2026-01-01 23:05:15',NULL),(10,14,1,'reservation','2026-01-02 09:57:45',NULL),(11,14,3,'reservation','2026-01-02 10:00:51',NULL),(12,14,1,'annulation','2026-01-02 10:02:02',NULL),(13,8,6,'debut_utilisation','2026-01-02 10:02:31',NULL),(14,14,3,'debut_utilisation','2026-01-02 10:02:36',NULL),(15,14,1,'reservation','2026-01-02 18:19:16',NULL),(16,14,3,'reservation','2026-01-02 18:20:05',NULL),(17,14,2,'reservation','2026-01-02 18:20:38',NULL),(18,14,3,'debut_utilisation','2026-01-02 18:24:11',NULL),(19,14,1,'annulation','2026-01-02 18:24:11',NULL),(20,14,1,'annulation','2026-01-02 18:24:11',NULL),(21,14,1,'annulation','2026-01-02 18:24:11',NULL),(22,14,2,'debut_utilisation','2026-01-02 18:24:37',NULL),(23,31,1,'reservation','2026-01-04 20:25:02',NULL);
/*!40000 ALTER TABLE `historique_equipements` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `inscriptions_evenements`
--

DROP TABLE IF EXISTS `inscriptions_evenements`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `inscriptions_evenements` (
  `id` int NOT NULL AUTO_INCREMENT,
  `evenement_id` int NOT NULL,
  `usr_id` int DEFAULT NULL,
  `nom` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `email` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `date_inscription` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `statut` enum('en_attente','confirmee','annulee','demande_annulation') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `evenement_id` (`evenement_id`),
  KEY `usr_id` (`usr_id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `inscriptions_evenements`
--

LOCK TABLES `inscriptions_evenements` WRITE;
/*!40000 ALTER TABLE `inscriptions_evenements` DISABLE KEYS */;
INSERT INTO `inscriptions_evenements` VALUES (1,6,3,NULL,NULL,'2025-12-21 22:48:50','annulee');
/*!40000 ALTER TABLE `inscriptions_evenements` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `maintenances`
--

DROP TABLE IF EXISTS `maintenances`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `maintenances` (
  `id_maintenance` int NOT NULL AUTO_INCREMENT,
  `equipement_id` int NOT NULL,
  `type` enum('preventive','corrective','reparation') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  `date_debut` datetime NOT NULL,
  `date_fin` datetime DEFAULT NULL,
  `technicien_id` int DEFAULT NULL,
  `cout` decimal(10,2) DEFAULT NULL,
  `statut` enum('planifiee','en_cours','terminee') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT 'planifiee',
  PRIMARY KEY (`id_maintenance`),
  KEY `equipement_id` (`equipement_id`),
  KEY `technicien_id` (`technicien_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `maintenances`
--

LOCK TABLES `maintenances` WRITE;
/*!40000 ALTER TABLE `maintenances` DISABLE KEYS */;
/*!40000 ALTER TABLE `maintenances` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `messages_contact`
--

DROP TABLE IF EXISTS `messages_contact`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `messages_contact` (
  `id_message` int NOT NULL AUTO_INCREMENT,
  `nom` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `email` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `sujet` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `message` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `date_envoi` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `lu` tinyint(1) DEFAULT '0',
  `repondu` tinyint(1) DEFAULT '0',
  PRIMARY KEY (`id_message`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `messages_contact`
--

LOCK TABLES `messages_contact` WRITE;
/*!40000 ALTER TABLE `messages_contact` DISABLE KEYS */;
/*!40000 ALTER TABLE `messages_contact` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `offres`
--

DROP TABLE IF EXISTS `offres`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `offres` (
  `id_offre` int NOT NULL AUTO_INCREMENT,
  `titre` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `type` enum('stage','these','bourse','collaboration','emploi','autre') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `responsable_id` int DEFAULT NULL,
  `date_limite` date DEFAULT NULL,
  `statut` enum('ouverte','fermee','pourvue') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT 'ouverte',
  `fichier_pdf` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `date_creation` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `is_deleted` tinyint(1) DEFAULT '0',
  PRIMARY KEY (`id_offre`),
  KEY `responsable_id` (`responsable_id`)
) ENGINE=MyISAM AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `offres`
--

LOCK TABLES `offres` WRITE;
/*!40000 ALTER TABLE `offres` DISABLE KEYS */;
INSERT INTO `offres` VALUES (1,'Stage Master : Développement d\'un système de reconnaissance d\'images par Deep Learning','stage','Nous recherchons un étudiant en Master 2 pour un stage de 6 mois axé sur le développement d\'algorithmes de reconnaissance d\'images utilisant les réseaux de neurones profonds.\r\n\r\nMissions :\r\n- Étude bibliographique sur les techniques de deep learning\r\n- Conception et implémentation d\'architectures CNN\r\n- Entraînement et optimisation des modèles\r\n- Évaluation des performances et comparaison avec l\'état de l\'art\r\n\r\nProfil recherché :\r\n- Étudiant en Master 2 Informatique ou équivalent\r\n- Bonnes connaissances en Python et frameworks ML (TensorFlow, PyTorch)\r\n- Compétences en traitement d\'images\r\n- Autonomie et rigueur scientifique\r\n\r\nDurée : 6 mois\r\nGratification : Selon réglementation en vigueur',2,'2026-01-12','ouverte',NULL,'2025-12-13 22:52:27',0),(2,'Stage Ingénieur : Détection d\'intrusions par Machine Learning','stage','Le laboratoire LMCS propose un stage de fin d\'études focalisé sur la détection d\'intrusions réseau utilisant des techniques d\'apprentissage automatique.\r\n\r\nObjectifs :\r\n- Analyser les techniques existantes de détection d\'intrusions\r\n- Développer un système basé sur le machine learning\r\n- Tester le système sur des datasets réels\r\n- Rédiger un rapport scientifique\r\n\r\nCompétences requises :\r\n- Formation en Cybersécurité ou Informatique\r\n- Maîtrise de Python et scikit-learn\r\n- Connaissances en réseaux informatiques\r\n- Anglais technique\r\n\r\nDurée : 4-6 mois\r\nDate de début souhaitée : Flexible',2,'2026-01-27','ouverte',NULL,'2025-12-13 22:52:27',0),(3,'Sujet de Thèse : Architectures IoT pour les Smart Cities','these','Sujet de thèse de doctorat sur le développement d\'architectures IoT sécurisées et efficaces pour les villes intelligentes.\r\n\r\nContexte :\r\nL\'Internet des Objets (IoT) joue un rôle crucial dans le développement des smart cities. Cette thèse vise à concevoir des architectures innovantes répondant aux défis de scalabilité, sécurité et efficacité énergétique.\r\n\r\nAxes de recherche :\r\n- Architectures distribuées pour l\'IoT\r\n- Protocoles de communication optimisés\r\n- Sécurité et confidentialité des données\r\n- Gestion intelligente de l\'énergie\r\n- Applications pratiques (transport, énergie, santé)\r\n\r\nProfil du candidat :\r\n- Diplôme de Master en Informatique, Télécommunications ou équivalent\r\n- Solides bases en réseaux et systèmes distribués\r\n- Programmation (Python, C/C++, Java)\r\n- Capacités de recherche et rédaction scientifique\r\n- Anglais courant\r\n\r\nFinancement : Bourse PRFU disponible\r\nDurée : 3 ans\r\nLieu : ESI Alger',2,'2026-02-11','ouverte',NULL,'2025-12-13 22:52:27',0),(4,'Thèse : Analyse prédictive et Big Data dans l\'éducation','these','Proposition de thèse sur l\'application du Big Data Analytics pour améliorer la réussite étudiante dans l\'enseignement supérieur.\r\n\r\nDescription :\r\nCette thèse explore l\'utilisation de techniques avancées d\'analyse de données massives pour prédire et améliorer les performances académiques des étudiants.\r\n\r\nProblématiques :\r\n- Collecte et intégration de données hétérogènes\r\n- Modèles prédictifs de réussite/échec\r\n- Systèmes de recommandation personnalisés\r\n- Visualisation et aide à la décision\r\n- Aspects éthiques et protection des données\r\n\r\nCompétences attendues :\r\n- Master en Informatique, Data Science ou domaine connexe\r\n- Expertise en Machine Learning et Data Mining\r\n- Maîtrise des technologies Big Data (Hadoop, Spark)\r\n- Compétences en bases de données et programmation\r\n- Publication scientifique (souhaitée)\r\n\r\nFinancement : À discuter (plusieurs options disponibles)\r\nEncadrement : Co-direction possible',2,'2026-03-13','ouverte',NULL,'2025-12-13 22:52:27',0),(5,'Bourse Post-doctorale : Intelligence Artificielle Explicable','bourse','Le laboratoire LMCS offre une bourse post-doctorale d\'un an (renouvelable) sur l\'Intelligence Artificielle Explicable (XAI).\r\n\r\nProjet :\r\nDéveloppement de méthodes d\'IA explicable pour des systèmes critiques (santé, transport, finance) dans le cadre d\'un projet de recherche international.\r\n\r\nResponsabilités :\r\n- Recherche sur les méthodes XAI\r\n- Implémentation et expérimentation\r\n- Publication dans des conférences/journaux internationaux\r\n- Co-encadrement d\'étudiants en Master/Doctorat\r\n- Collaboration avec partenaires industriels\r\n\r\nExigences :\r\n- Doctorat en Informatique, IA ou domaine connexe\r\n- Publications dans des conférences/journaux de rang A\r\n- Expertise en Machine Learning et Deep Learning\r\n- Excellentes compétences en programmation\r\n- Maîtrise de l\'anglais scientifique\r\n\r\nConditions :\r\n- Contrat : 12 mois (renouvelable jusqu\'à 24 mois)\r\n- Salaire : Selon grille universitaire algérienne\r\n- Début : Septembre 2025',2,'2026-04-12','ouverte',NULL,'2025-12-13 22:52:27',0),(6,'Appel à Collaboration : Projet Horizon Europe sur la Cybersécurité','collaboration','Le laboratoire LMCS recherche des partenaires académiques et industriels pour un projet Horizon Europe focalisé sur la cybersécurité des infrastructures critiques.\r\n\r\nThématique :\r\nDéveloppement de solutions innovantes pour la protection des systèmes SCADA et infrastructures industrielles contre les cyberattaques.\r\n\r\nPartenaires recherchés :\r\n- Laboratoires de recherche en cybersécurité\r\n- Entreprises dans les secteurs de l\'énergie, transport, eau\r\n- PME spécialisées en sécurité informatique\r\n- Organismes de standardisation\r\n\r\nRôle du LMCS :\r\n- Coordination scientifique\r\n- Développement d\'algorithmes de détection\r\n- Formation et dissémination\r\n\r\nBudget estimé : 3M€\r\nDurée du projet : 36 mois\r\nDate de soumission : Mars 2025\r\n\r\nContact pour manifester votre intérêt.',2,'2026-02-11','ouverte',NULL,'2025-12-13 22:52:27',0),(7,'Recrutement Maître de Conférences : Systèmes Distribués','emploi','L\'ESI recrute un Maître de Conférences pour renforcer l\'équipe du laboratoire LMCS dans le domaine des systèmes distribués et cloud computing.\r\n\r\nMissions :\r\n- Enseignement (Licence, Master) : Systèmes distribués, Cloud, Architectures\r\n- Recherche au sein du LMCS\r\n- Encadrement d\'étudiants (Master, Doctorat)\r\n- Montage et participation à des projets de recherche\r\n- Rayonnement scientifique (publications, conférences)\r\n\r\nProfil :\r\n- Doctorat en Informatique\r\n- Spécialisation en systèmes distribués, cloud computing, ou domaine connexe\r\n- Publications de qualité (conférences/journaux internationaux)\r\n- Expérience d\'enseignement souhaitée\r\n- Capacités d\'encadrement et de travail en équipe\r\n\r\nConditions :\r\n- Poste statutaire (fonctionnaire)\r\n- Salaire : Selon grille universitaire\r\n- Prise de fonction : Septembre 2025\r\n\r\nDossier de candidature :\r\n- CV détaillé\r\n- Projet de recherche\r\n- Projet pédagogique\r\n- Lettres de recommandation (2)\r\n- Liste des publications',2,'2026-03-13','ouverte',NULL,'2025-12-13 22:52:27',0),(8,'Programme Visiting Scholar : Accueil de chercheurs internationaux','autre','Le laboratoire LMCS lance un programme d\'accueil de chercheurs visiteurs pour des séjours de recherche de courte à moyenne durée.\r\n\r\nObjectifs :\r\n- Favoriser la collaboration scientifique internationale\r\n- Transfert de connaissances et compétences\r\n- Développement de projets de recherche communs\r\n- Co-publications\r\n\r\nDomaines prioritaires :\r\n- Intelligence Artificielle\r\n- Cybersécurité\r\n- Systèmes distribués et Cloud\r\n- Internet des Objets\r\n- Big Data et Analytics\r\n\r\nConditions d\'accueil :\r\n- Durée : 1 à 6 mois\r\n- Mise à disposition de bureaux et équipements\r\n- Accès aux ressources du laboratoire\r\n- Possibilité de donner des séminaires\r\n- Hébergement : À la charge du visiteur (aide possible)\r\n\r\nCritères de sélection :\r\n- Doctorat en Informatique ou domaine connexe\r\n- Dossier scientifique de qualité\r\n- Projet de collaboration précis\r\n- Complémentarité avec les activités du LMCS\r\n\r\nCandidatures ouvertes toute l\'année.',2,'2026-12-13','ouverte',NULL,'2025-12-13 22:52:27',0);
/*!40000 ALTER TABLE `offres` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `organigramme`
--

DROP TABLE IF EXISTS `organigramme`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `organigramme` (
  `id` int NOT NULL AUTO_INCREMENT,
  `usr_id` int NOT NULL,
  `poste_hierarchique` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `niveau` int DEFAULT '1',
  `superieur_id` int DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `usr_id` (`usr_id`),
  KEY `superieur_id` (`superieur_id`)
) ENGINE=MyISAM AUTO_INCREMENT=15 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `organigramme`
--

LOCK TABLES `organigramme` WRITE;
/*!40000 ALTER TABLE `organigramme` DISABLE KEYS */;
INSERT INTO `organigramme` VALUES (1,2,'Directeur du laboratoire',1,NULL),(2,4,'Trésorier du laboratoire',2,NULL),(3,3,'Chef d\'équipe IA et Apprentissage Automatique',2,NULL),(4,8,'Chef d\'équipe Réseaux et Sécurité',2,NULL),(5,12,'Chef d\'équipe Systèmes Intelligents et Robotique',2,NULL),(6,5,'Chercheur senior - Équipe IA',3,3),(7,6,'Doctorante - Équipe IA',3,3),(8,7,'Doctorant - Équipe IA',3,3),(9,9,'Chercheur senior - Équipe Réseaux',3,8),(10,10,'Chercheur - Équipe Réseaux',3,8),(11,11,'Doctorante - Équipe Réseaux',3,8),(12,13,'Chercheur senior - Équipe Robotique',3,12),(13,14,'Doctorant - Équipe Robotique',3,12),(14,15,'Doctorante - Équipe Robotique',3,12);
/*!40000 ALTER TABLE `organigramme` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `parametres`
--

DROP TABLE IF EXISTS `parametres`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `parametres` (
  `id` int NOT NULL AUTO_INCREMENT,
  `param_key` varchar(100) NOT NULL,
  `param_value` text,
  `description` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `param_key` (`param_key`),
  KEY `idx_param_key` (`param_key`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `parametres`
--

LOCK TABLES `parametres` WRITE;
/*!40000 ALTER TABLE `parametres` DISABLE KEYS */;
INSERT INTO `parametres` VALUES (1,'site_name','Laboratoire LMCS','Nom du site web','2026-01-10 16:57:13','2026-01-10 16:57:13'),(2,'site_description','Laboratoire de Méthodes de Conception des Systèmes','Description du site','2026-01-10 16:57:13','2026-01-10 16:57:13'),(3,'contact_email','lmcs@esi.dz','Email de contact principal','2026-01-10 16:57:13','2026-01-10 16:57:13'),(4,'contact_phone','00 213 (0) 23-93-91-30','Téléphone de contact','2026-01-10 16:57:13','2026-01-10 16:57:13'),(5,'contact_address','LMCS, Ecole nationale Supérieure d\'Informatique, BP M68, Oued Smar, Alger 16309','Adresse physique','2026-01-10 16:57:13','2026-01-10 16:57:13'),(6,'theme_primary_color','#58ae1e','Couleur principale du thème','2026-01-10 16:57:13','2026-01-10 17:03:42'),(7,'theme_secondary_color','#3b82f6','Couleur secondaire du thème','2026-01-10 16:57:13','2026-01-10 16:57:13'),(8,'theme_accent_color','#60a5fa','Couleur d\'accent du thème','2026-01-10 16:57:13','2026-01-10 16:57:13'),(9,'theme_mode','dark','Mode d\'affichage (light/dark)','2026-01-10 16:57:13','2026-01-10 17:03:42'),(10,'logo_main','main_logo_1768064642.gif',NULL,'2026-01-10 17:04:02','2026-01-10 17:04:02');
/*!40000 ALTER TABLE `parametres` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `partenaires`
--

DROP TABLE IF EXISTS `partenaires`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `partenaires` (
  `id_partenaire` int NOT NULL AUTO_INCREMENT,
  `nom` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `type` enum('universite','entreprise','organisme') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `logo` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `site_web` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  `pays` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `date_partenariat` date DEFAULT NULL,
  `is_deleted` tinyint(1) DEFAULT '0',
  PRIMARY KEY (`id_partenaire`)
) ENGINE=MyISAM AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `partenaires`
--

LOCK TABLES `partenaires` WRITE;
/*!40000 ALTER TABLE `partenaires` DISABLE KEYS */;
INSERT INTO `partenaires` VALUES (1,'Université de Paris-Saclay','universite','Logo_Universite_Paris-Saclay.png','https://www.universite-paris-saclay.fr','Partenariat stratégique dans le domaine de l\'intelligence artificielle et du machine learning. Collaboration sur des projets de recherche conjoints et échanges d\'étudiants doctorants.','France','2022-03-15',0),(2,'Microsoft Research','entreprise','Microsoft_logo.png','https://www.microsoft.com/research','Partenariat technologique pour le développement d\'outils de cloud computing et d\'intelligence artificielle appliquée. Accès aux infrastructures Azure pour nos projets de recherche.','États-Unis','2023-01-10',0),(3,'Centre National de Recherche Scientifique (CNRS)','organisme','Logo_CNRS.png','https://www.cnrs.fr','Collaboration institutionnelle pour le financement de projets de recherche fondamentale en informatique, robotique et vision par ordinateur.','France','2020-09-20',0);
/*!40000 ALTER TABLE `partenaires` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `permissions`
--

DROP TABLE IF EXISTS `permissions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `permissions` (
  `id_permission` int NOT NULL AUTO_INCREMENT,
  `nom` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  `categorie` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `date_creation` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_permission`),
  UNIQUE KEY `nom` (`nom`)
) ENGINE=MyISAM AUTO_INCREMENT=41 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `permissions`
--

LOCK TABLES `permissions` WRITE;
/*!40000 ALTER TABLE `permissions` DISABLE KEYS */;
INSERT INTO `permissions` VALUES (1,'users.view','Consulter les utilisateurs','Utilisateurs','2025-12-22 10:47:36'),(2,'users.create','Créer des utilisateurs','Utilisateurs','2025-12-22 10:47:36'),(3,'users.edit','Modifier les utilisateurs','Utilisateurs','2025-12-22 10:47:36'),(4,'users.delete','Supprimer les utilisateurs','Utilisateurs','2025-12-22 10:47:36'),(5,'users.suspend','Suspendre des utilisateurs','Utilisateurs','2025-12-22 10:47:36'),(6,'users.manage_roles','Gérer les rôles des utilisateurs','Utilisateurs','2025-12-22 10:47:36'),(7,'projets.view','Consulter les projets','Projets','2025-12-22 10:47:36'),(8,'projets.create','Créer des projets','Projets','2025-12-22 10:47:36'),(9,'projets.edit','Modifier les projets','Projets','2025-12-22 10:47:36'),(10,'projets.delete','Supprimer les projets','Projets','2025-12-22 10:47:36'),(11,'projets.edit_own','Modifier ses propres projets','Projets','2025-12-22 10:47:36'),(12,'projets.delete_own','Supprimer ses propres projets (si chef)','Projets','2025-12-22 10:47:36'),(13,'projets.manage_members','Gérer les membres des projets','Projets','2025-12-22 10:47:36'),(14,'publications.view','Consulter les publications','Publications','2025-12-22 10:47:36'),(15,'publications.create','Créer des publications','Publications','2025-12-22 10:47:36'),(16,'publications.edit','Modifier les publications','Publications','2025-12-22 10:47:36'),(17,'publications.delete','Supprimer les publications','Publications','2025-12-22 10:47:36'),(18,'publications.publish','Publier des publications','Publications','2025-12-22 10:47:36'),(19,'publications.edit_own','Modifier ses propres publications','Publications','2025-12-22 10:47:36'),(20,'equipements.view','Consulter les équipements','Équipements','2025-12-22 10:47:36'),(21,'equipements.create','Créer des équipements','Équipements','2025-12-22 10:47:36'),(22,'equipements.edit','Modifier les équipements','Équipements','2025-12-22 10:47:36'),(23,'equipements.delete','Supprimer les équipements','Équipements','2025-12-22 10:47:36'),(24,'equipements.reserve','Réserver des équipements','Équipements','2025-12-22 10:47:36'),(25,'equipements.approve_reservation','Approuver les réservations','Équipements','2025-12-22 10:47:36'),(26,'equipements.cancel_reservation','Annuler les réservations','Équipements','2025-12-22 10:47:36'),(27,'evenements.view','Consulter les événements','Événements','2025-12-22 10:47:37'),(28,'evenements.create','Créer des événements','Événements','2025-12-22 10:47:37'),(29,'evenements.edit','Modifier les événements','Événements','2025-12-22 10:47:37'),(30,'evenements.delete','Supprimer les événements','Événements','2025-12-22 10:47:37'),(31,'evenements.approve_inscription','Approuver les inscriptions','Événements','2025-12-22 10:47:37'),(32,'evenements.cancel_inscription','Annuler les inscriptions','Événements','2025-12-22 10:47:37'),(33,'equipes.view','Consulter les équipes','Équipes','2025-12-22 10:47:37'),(34,'equipes.create','Créer des équipes','Équipes','2025-12-22 10:47:37'),(35,'equipes.edit','Modifier les équipes','Équipes','2025-12-22 10:47:37'),(36,'equipes.delete','Supprimer les équipes','Équipes','2025-12-22 10:47:37'),(37,'equipes.manage_members','Gérer les membres des équipes','Équipes','2025-12-22 10:47:37'),(38,'actualites.manage','Gérer les actualités','Contenu','2025-12-22 10:47:37'),(39,'offres.manage','Gérer les offres','Contenu','2025-12-22 10:47:37'),(40,'partenaires.manage','Gérer les partenaires','Contenu','2025-12-22 10:47:37');
/*!40000 ALTER TABLE `permissions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `projet_membres`
--

DROP TABLE IF EXISTS `projet_membres`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `projet_membres` (
  `id` int NOT NULL AUTO_INCREMENT,
  `projet_id` int NOT NULL,
  `usr_id` int NOT NULL,
  `role_projet` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `is_deleted` tinyint(1) DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_projet_membre` (`projet_id`,`usr_id`),
  KEY `usr_id` (`usr_id`)
) ENGINE=MyISAM AUTO_INCREMENT=16 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `projet_membres`
--

LOCK TABLES `projet_membres` WRITE;
/*!40000 ALTER TABLE `projet_membres` DISABLE KEYS */;
INSERT INTO `projet_membres` VALUES (1,1,2,'Chef de projet',0),(2,3,2,'Responsable scientifique',0),(3,5,2,'Coordinateur',0),(4,7,2,'Investigateur principal',0),(5,9,2,'Chef de projet',0),(6,3,3,'Chef de projet',0),(7,5,4,'Chef de projet',0),(8,7,5,'Chef de projet',0),(9,1,13,NULL,0),(10,1,5,NULL,0),(11,1,14,NULL,0),(12,21,4,'Responsable',0),(13,22,3,'Chef de projet',0),(14,22,1,'',1),(15,22,8,NULL,0);
/*!40000 ALTER TABLE `projet_membres` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `projet_partenaires`
--

DROP TABLE IF EXISTS `projet_partenaires`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `projet_partenaires` (
  `id` int NOT NULL AUTO_INCREMENT,
  `projet_id` int NOT NULL,
  `partenaire_id` int NOT NULL,
  `is_deleted` tinyint(1) DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_projet_partenaire` (`projet_id`,`partenaire_id`),
  KEY `partenaire_id` (`partenaire_id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `projet_partenaires`
--

LOCK TABLES `projet_partenaires` WRITE;
/*!40000 ALTER TABLE `projet_partenaires` DISABLE KEYS */;
INSERT INTO `projet_partenaires` VALUES (1,21,1,0);
/*!40000 ALTER TABLE `projet_partenaires` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `projets`
--

DROP TABLE IF EXISTS `projets`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `projets` (
  `id_projet` int NOT NULL AUTO_INCREMENT,
  `titre` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  `thematique` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `type_financement` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `statut` enum('en_cours','termine','soumis') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT 'en_cours',
  `responsable_id` int NOT NULL,
  `date_debut` date DEFAULT NULL,
  `date_fin` date DEFAULT NULL,
  `fiche_detaillee` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `budget` decimal(15,2) DEFAULT NULL,
  `date_creation` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `is_deleted` tinyint(1) DEFAULT '0',
  PRIMARY KEY (`id_projet`),
  KEY `responsable_id` (`responsable_id`)
) ENGINE=MyISAM AUTO_INCREMENT=23 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `projets`
--

LOCK TABLES `projets` WRITE;
/*!40000 ALTER TABLE `projets` DISABLE KEYS */;
INSERT INTO `projets` VALUES (1,'Système de Reconnaissance Faciale Avancé','Développement d\'un système de reconnaissance faciale utilisant des réseaux de neurones profonds pour améliorer la sécurité des campus universitaires.','Intelligence Artificielle','','termine',2,'2024-01-15','2025-12-31',NULL,150000.00,'2025-12-13 18:18:35',0),(3,'Détection d\'Intrusions par Machine Learning','Conception d\'un système de détection d\'intrusions réseau basé sur l\'apprentissage automatique pour identifier les menaces en temps réel.','Cybersécurité','Européen','en_cours',3,'2023-09-01','2025-06-30',NULL,200000.00,'2025-12-13 18:18:35',0),(5,'Smart Campus : Gestion Intelligente des Ressources','Mise en place d\'un réseau IoT pour optimiser la consommation énergétique et la gestion des espaces dans le campus universitaire.','Internet des Objets','PNR','soumis',4,'2025-02-01','2027-01-31',NULL,300000.00,'2025-12-13 18:18:35',0),(7,'Analyse Prédictive de la Réussite Étudiante','Utilisation du Big Data et de l\'analyse prédictive pour identifier les facteurs de réussite et d\'échec des étudiants.','Big Data & Analytics','Auto-financé','termine',5,'2022-03-01','2024-02-29',NULL,80000.00,'2025-12-13 18:18:35',0),(9,'Plateforme Cloud pour la Recherche Scientifique','Développement d\'une infrastructure cloud sécurisée pour faciliter la collaboration entre chercheurs et le partage de données.','Cloud Computing','PRFU','en_cours',2,'2024-06-01','2026-05-31',NULL,250000.00,'2025-12-13 18:18:35',0),(21,'Sécurité des systèmes d\'information d\'aide à la décision','Développement d\'une solution optimale de sécurisation des systèmes d\'information d\'aide à la décision','Sécurité des systèmes d\'information',NULL,'en_cours',4,NULL,NULL,NULL,1200000.00,'2025-12-23 23:00:00',1),(22,'Sécurité des systèmes d\'information d\'aide à la décision','Un projet visant à explorer les différentes méthodologies et techniques de sécurisation des systèmes d\'information d\'aide à la décision','Sécurité des systèmes d\'information','PRFU','en_cours',3,'2026-01-26','2028-11-29',NULL,50000000.00,'2026-01-09 20:16:14',0);
/*!40000 ALTER TABLE `projets` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `publication_auteurs`
--

DROP TABLE IF EXISTS `publication_auteurs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `publication_auteurs` (
  `id` int NOT NULL AUTO_INCREMENT,
  `publication_id` int NOT NULL,
  `usr_id` int NOT NULL,
  `ordre_auteur` int DEFAULT '1',
  `is_deleted` tinyint(1) DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_publication_auteur` (`publication_id`,`usr_id`),
  KEY `usr_id` (`usr_id`)
) ENGINE=MyISAM AUTO_INCREMENT=68 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `publication_auteurs`
--

LOCK TABLES `publication_auteurs` WRITE;
/*!40000 ALTER TABLE `publication_auteurs` DISABLE KEYS */;
INSERT INTO `publication_auteurs` VALUES (1,1,2,1,0),(2,2,2,1,0),(3,3,2,1,0),(4,4,2,1,0),(64,5,3,1,0),(6,7,2,1,0),(7,9,2,1,0),(8,11,2,1,0),(12,3,3,2,0),(13,3,4,3,0),(65,5,2,2,0),(66,5,5,3,0),(67,13,0,1,0);
/*!40000 ALTER TABLE `publication_auteurs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `publications`
--

DROP TABLE IF EXISTS `publications`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `publications` (
  `id_publication` int NOT NULL AUTO_INCREMENT,
  `titre` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `type_publication_id` int DEFAULT NULL,
  `resume` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  `annee` int NOT NULL,
  `doi` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `lien_telechargement` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `fichier_pdf` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `projet_id` int DEFAULT NULL,
  `domaine` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `statut` enum('publie','en_attente','rejete') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT 'en_attente',
  `date_publication` date DEFAULT NULL,
  `date_soumission` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `is_deleted` tinyint(1) DEFAULT '0',
  PRIMARY KEY (`id_publication`),
  KEY `projet_id` (`projet_id`),
  KEY `type_publication_id` (`type_publication_id`)
) ENGINE=MyISAM AUTO_INCREMENT=14 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `publications`
--

LOCK TABLES `publications` WRITE;
/*!40000 ALTER TABLE `publications` DISABLE KEYS */;
INSERT INTO `publications` VALUES (1,'Deep Learning Approach for Real-Time Facial Recognition in Surveillance Systems',1,'Cet article présente une approche innovante utilisant les réseaux de neurones convolutifs pour la reconnaissance faciale en temps réel. Les résultats montrent une amélioration de 15% par rapport aux méthodes traditionnelles.',2024,'10.1109/CVPR.2024.12345','http://localhost/Projet_TDW/public/assets/documents/6940a51df2e82_1765844253.pdf',NULL,1,'Computer Vision','publie','2024-06-15','2025-12-13 18:18:36',0),(2,'Machine Learning Based Network Intrusion Detection: A Comprehensive Survey',1,'Une revue exhaustive des techniques d\'apprentissage automatique appliquées à la détection d\'intrusions réseau, incluant une analyse comparative des performances.',2024,'10.1016/j.cose.2024.103456','http://localhost/Projet_TDW/public/assets/documents/6940a51df2e82_1765844253.pdf',NULL,3,'Network Security','publie','2024-03-20','2025-12-13 18:18:36',0),(3,'IoT Architecture for Smart University Campus Management',4,'Présentation d\'une architecture IoT complète pour la gestion intelligente des campus universitaires, incluant la gestion énergétique et la sécurité.',2024,'10.1016/j.cose.2024.103456','http://localhost/Projet_TDW/public/assets/documents/6940a51df2e82_1765844253.pdf',NULL,7,'Internet of Things','publie','2024-11-10','2025-12-13 18:18:36',0),(4,'Analyse Big Data pour la Prédiction de la Réussite Académique',1,'Application de techniques de Big Data Analytics pour prédire les performances académiques des étudiants et identifier les facteurs de risque d\'échec.',2023,'10.1016/j.cose.2024.103456','http://localhost/Projet_TDW/public/assets/documents/6940a51df2e82_1765844253.pdf',NULL,5,'Educational Data Mining','publie','2023-11-25','2025-12-13 18:18:36',0),(5,'Cloud Infrastructure Design for Scientific Computing',2,'Rapport technique détaillant la conception et l\'implémentation d\'une infrastructure cloud dédiée aux calculs scientifiques haute performance.',2024,'10.1016/j.cose.2024.103456','http://localhost/Projet_TDW/public/assets/documents/69617549c4af1_1767994697.pdf','69617549c4af1_1767994697.pdf',NULL,'Cloud Computing','publie','2026-01-06','2025-12-13 18:20:24',0),(7,'Blockchain pour la Sécurisation des Données de Santé',5,'Proposition d\'une solution blockchain pour garantir l\'intégrité et la confidentialité des dossiers médicaux électroniques.',2024,'10.1016/j.cose.2024.103456','http://localhost/Projet_TDW/public/assets/documents/6940a51df2e82_1765844253.pdf',NULL,NULL,'Blockchain & Healthcare','publie','2024-10-05','2025-12-13 18:20:24',0),(9,'Federated Learning for Privacy-Preserving Medical Image Analysis',1,'Une approche d\'apprentissage fédéré pour l\'analyse d\'images médicales tout en préservant la confidentialité des données des patients.',2024,'10.1016/j.cose.2024.103456','http://localhost/Projet_TDW/public/assets/documents/6940a51df2e82_1765844253.pdf',NULL,NULL,'Medical AI','publie',NULL,'2025-12-13 18:20:25',0),(11,'Optimisation des Algorithmes de Clustering pour le Big Data',1,'Amélioration des performances des algorithmes de clustering traditionnels pour traiter des volumes massifs de données en temps réel.',2023,'10.1007/s10618-023-00956-2','http://localhost/Projet_TDW/public/assets/documents/6940a51df2e82_1765844253.pdf',NULL,NULL,'Data Mining','publie','2023-08-15','2025-12-13 18:20:25',0),(13,'Optimisation des compilateurs',1,'',2026,'10.1016/j.cose.2024.103456','',NULL,NULL,'Compilation','publie','2026-01-11','2026-01-10 15:37:31',1);
/*!40000 ALTER TABLE `publications` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `reservations`
--

DROP TABLE IF EXISTS `reservations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `reservations` (
  `id_reservation` int NOT NULL AUTO_INCREMENT,
  `equipement_id` int NOT NULL,
  `usr_id` int NOT NULL,
  `date_debut` datetime NOT NULL,
  `date_fin` datetime NOT NULL,
  `motif` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  `statut` enum('en_attente','confirmee','annulee','terminee','demande_annulation') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT 'en_attente',
  `date_reservation` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `nb_instances` int DEFAULT '1',
  PRIMARY KEY (`id_reservation`),
  KEY `equipement_id` (`equipement_id`),
  KEY `usr_id` (`usr_id`)
) ENGINE=MyISAM AUTO_INCREMENT=17 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `reservations`
--

LOCK TABLES `reservations` WRITE;
/*!40000 ALTER TABLE `reservations` DISABLE KEYS */;
INSERT INTO `reservations` VALUES (1,3,2,'2025-12-22 12:04:23','2025-12-23 12:04:23','Développement d\'application de reconnaissance d\'images','confirmee','2025-12-20 11:04:23',1),(2,8,6,'2025-12-25 12:04:23','2025-12-26 12:04:23','Tests de cartographie aérienne pour projet de recherche','confirmee','2025-12-20 11:04:23',1),(3,18,3,'2025-12-20 12:04:23','2025-12-21 12:04:23','Collecte de données pour navigation autonome','confirmee','2025-12-19 11:04:23',1),(4,32,3,'2025-12-24 04:07:00','2025-12-24 12:07:00','Besoin d\'un capteur pour un projet IoT','demande_annulation','2025-12-21 10:11:33',1),(5,19,3,'2025-12-23 13:00:00','2025-12-23 15:00:00','Projet Iot avec IA','demande_annulation','2025-12-21 12:00:13',3),(6,22,3,'2025-12-29 14:05:00','2025-12-29 16:05:00',NULL,'annulee','2025-12-21 12:05:00',2),(7,2,3,'2025-12-30 16:05:00','2025-12-30 20:10:00',NULL,'confirmee','2025-12-21 13:04:30',1),(8,14,1,'2026-01-10 01:00:00','2026-01-18 02:00:00','Projet HPC','confirmee','2026-01-01 22:04:20',5),(9,14,1,'2026-01-13 10:57:00','2026-01-20 10:57:00',NULL,'annulee','2026-01-02 08:57:45',9),(10,14,3,'2026-01-18 11:00:00','2026-01-22 11:00:00',NULL,'confirmee','2026-01-02 09:00:51',3),(11,14,1,'2026-01-15 18:30:00','2026-01-18 18:30:00',NULL,'annulee','2026-01-02 16:30:35',5),(16,31,1,'2026-01-11 21:24:00','2026-01-17 21:24:00',NULL,'en_attente','2026-01-04 19:25:02',3),(14,14,3,'2026-01-14 19:19:00','2026-01-20 19:19:00',NULL,'confirmee','2026-01-02 17:20:05',3),(15,14,2,'2026-01-13 19:20:00','2026-01-19 19:20:00',NULL,'confirmee','2026-01-02 17:20:38',2);
/*!40000 ALTER TABLE `reservations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `role_permissions`
--

DROP TABLE IF EXISTS `role_permissions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `role_permissions` (
  `id` int NOT NULL AUTO_INCREMENT,
  `role` enum('admin','enseignant-chercheur','doctorant','etudiant','invite') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `permission_id` int NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_role_permission` (`role`,`permission_id`),
  KEY `permission_id` (`permission_id`)
) ENGINE=MyISAM AUTO_INCREMENT=102 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `role_permissions`
--

LOCK TABLES `role_permissions` WRITE;
/*!40000 ALTER TABLE `role_permissions` DISABLE KEYS */;
INSERT INTO `role_permissions` VALUES (1,'admin',1),(2,'admin',2),(3,'admin',3),(4,'admin',4),(5,'admin',5),(6,'admin',6),(7,'admin',7),(8,'admin',8),(9,'admin',9),(10,'admin',10),(11,'admin',11),(12,'admin',12),(13,'admin',13),(14,'admin',14),(15,'admin',15),(16,'admin',16),(17,'admin',17),(18,'admin',18),(19,'admin',19),(20,'admin',20),(21,'admin',21),(22,'admin',22),(23,'admin',23),(24,'admin',24),(25,'admin',25),(26,'admin',26),(27,'admin',27),(28,'admin',28),(29,'admin',29),(30,'admin',30),(31,'admin',31),(32,'admin',32),(33,'admin',33),(34,'admin',34),(35,'admin',35),(36,'admin',36),(37,'admin',37),(38,'admin',38),(39,'admin',39),(40,'admin',40),(41,'enseignant-chercheur',1),(42,'enseignant-chercheur',7),(43,'enseignant-chercheur',8),(44,'enseignant-chercheur',11),(45,'enseignant-chercheur',12),(46,'enseignant-chercheur',13),(47,'enseignant-chercheur',14),(48,'enseignant-chercheur',15),(49,'enseignant-chercheur',18),(50,'enseignant-chercheur',19),(51,'enseignant-chercheur',20),(52,'enseignant-chercheur',24),(53,'enseignant-chercheur',26),(54,'enseignant-chercheur',27),(55,'enseignant-chercheur',28),(56,'enseignant-chercheur',33),(57,'enseignant-chercheur',37),(58,'doctorant',1),(59,'doctorant',7),(60,'doctorant',8),(61,'doctorant',11),(62,'doctorant',14),(63,'doctorant',15),(64,'doctorant',19),(65,'doctorant',20),(66,'doctorant',24),(67,'doctorant',26),(68,'doctorant',27),(69,'doctorant',33),(96,'etudiant',20),(97,'etudiant',33),(98,'etudiant',27),(94,'etudiant',26),(95,'etudiant',24),(100,'etudiant',15),(99,'etudiant',7),(101,'etudiant',14),(85,'invite',27),(83,'invite',20),(84,'invite',33),(87,'invite',12),(86,'invite',8),(88,'invite',11),(89,'invite',7),(90,'invite',15),(91,'invite',19),(92,'invite',18),(93,'invite',14);
/*!40000 ALTER TABLE `role_permissions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `team_members`
--

DROP TABLE IF EXISTS `team_members`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `team_members` (
  `id` int NOT NULL AUTO_INCREMENT,
  `team_id` int NOT NULL,
  `usr_id` int NOT NULL,
  `role_dans_equipe` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `date_adhesion` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `is_deleted` tinyint(1) DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_team_member` (`team_id`,`usr_id`),
  KEY `usr_id` (`usr_id`)
) ENGINE=MyISAM AUTO_INCREMENT=18 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `team_members`
--

LOCK TABLES `team_members` WRITE;
/*!40000 ALTER TABLE `team_members` DISABLE KEYS */;
INSERT INTO `team_members` VALUES (1,1,3,'Chef d\'équipe','2025-12-16 09:50:14',0),(2,1,5,'Chercheur senior','2025-12-16 09:50:14',0),(3,1,6,'Doctorante','2025-12-16 09:50:14',0),(4,1,7,'Doctorant','2025-12-16 09:50:14',0),(5,2,8,'Chef d\'équipe','2025-12-16 09:50:14',0),(6,2,9,'Chercheur senior','2025-12-16 09:50:14',0),(7,2,10,'Chercheur','2025-12-16 09:50:14',0),(8,2,11,'Doctorante','2025-12-16 09:50:14',0),(9,3,12,'Chef d\'équipe','2025-12-16 09:50:14',0),(10,3,13,'Chercheur senior','2025-12-16 09:50:14',0),(11,3,14,'Doctorant','2025-12-16 09:50:14',0),(12,3,15,'Doctorante','2025-12-16 09:50:14',0),(13,1,4,'Doctorant','2025-12-23 09:43:46',0),(14,4,9,'Chef d\'équipe','2025-12-23 09:57:52',1),(15,4,10,'Chercheur Senior','2025-12-23 09:59:21',1),(16,4,11,'Doctorante','2025-12-23 09:59:39',1),(17,4,12,'Doctorant','2025-12-23 09:59:54',1);
/*!40000 ALTER TABLE `team_members` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `teams`
--

DROP TABLE IF EXISTS `teams`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `teams` (
  `id_team` int NOT NULL AUTO_INCREMENT,
  `nom` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  `thematique` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `chef_id` int DEFAULT NULL,
  `date_creation` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `is_deleted` tinyint(1) DEFAULT '0',
  PRIMARY KEY (`id_team`),
  UNIQUE KEY `nom` (`nom`),
  KEY `chef_id` (`chef_id`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `teams`
--

LOCK TABLES `teams` WRITE;
/*!40000 ALTER TABLE `teams` DISABLE KEYS */;
INSERT INTO `teams` VALUES (1,'Intelligence Artificielle et Apprentissage Automatique','Équipe de recherche dédiée aux techniques d\'intelligence artificielle, d\'apprentissage automatique, de deep learning et de traitement du langage naturel. L\'équipe développe des algorithmes innovants pour la reconnaissance, la classification et la prédiction dans divers domaines d\'application.','IA, Machine Learning, Deep Learning, NLP',3,'2025-12-16 09:50:14',0),(2,'Réseaux et Sécurité Informatique','Équipe spécialisée dans la recherche sur les réseaux informatiques, la cybersécurité, la cryptographie et les protocoles de sécurité. L\'équipe travaille sur la protection des systèmes d\'information et le développement de solutions de sécurité innovantes.','Réseaux, Cybersécurité, Cryptographie, IoT',8,'2025-12-16 09:50:14',0),(3,'Systèmes Intelligents et Robotique','Équipe de recherche axée sur la robotique mobile, les systèmes embarqués, la vision par ordinateur et la navigation autonome. L\'équipe conçoit et développe des systèmes robotiques intelligents pour diverses applications industrielles et scientifiques.','Robotique, Systèmes Embarqués, Vision, Navigation',9,'2025-12-22 23:00:00',0);
/*!40000 ALTER TABLE `teams` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `types_actualites`
--

DROP TABLE IF EXISTS `types_actualites`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `types_actualites` (
  `id_type` int NOT NULL AUTO_INCREMENT,
  `libelle` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  PRIMARY KEY (`id_type`),
  UNIQUE KEY `libelle` (`libelle`)
) ENGINE=MyISAM AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `types_actualites`
--

LOCK TABLES `types_actualites` WRITE;
/*!40000 ALTER TABLE `types_actualites` DISABLE KEYS */;
INSERT INTO `types_actualites` VALUES (1,'Projet'),(2,'Publication'),(3,'Événement'),(4,'Soutenance'),(5,'Collaboration');
/*!40000 ALTER TABLE `types_actualites` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `types_equipements`
--

DROP TABLE IF EXISTS `types_equipements`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `types_equipements` (
  `id_type` int NOT NULL AUTO_INCREMENT,
  `libelle` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  PRIMARY KEY (`id_type`),
  UNIQUE KEY `libelle` (`libelle`)
) ENGINE=MyISAM AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `types_equipements`
--

LOCK TABLES `types_equipements` WRITE;
/*!40000 ALTER TABLE `types_equipements` DISABLE KEYS */;
INSERT INTO `types_equipements` VALUES (1,'salles'),(2,'serveurs'),(3,'PC'),(4,'robots'),(5,'imprimantes'),(6,'capteurs');
/*!40000 ALTER TABLE `types_equipements` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `types_evenements`
--

DROP TABLE IF EXISTS `types_evenements`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `types_evenements` (
  `id_type` int NOT NULL AUTO_INCREMENT,
  `libelle` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  PRIMARY KEY (`id_type`),
  UNIQUE KEY `libelle` (`libelle`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `types_evenements`
--

LOCK TABLES `types_evenements` WRITE;
/*!40000 ALTER TABLE `types_evenements` DISABLE KEYS */;
INSERT INTO `types_evenements` VALUES (1,'Atelier'),(2,'Séminaire'),(3,'Conférence'),(4,'Soutenance');
/*!40000 ALTER TABLE `types_evenements` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `types_publications`
--

DROP TABLE IF EXISTS `types_publications`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `types_publications` (
  `id_type` int NOT NULL AUTO_INCREMENT,
  `libelle` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  PRIMARY KEY (`id_type`),
  UNIQUE KEY `libelle` (`libelle`)
) ENGINE=MyISAM AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `types_publications`
--

LOCK TABLES `types_publications` WRITE;
/*!40000 ALTER TABLE `types_publications` DISABLE KEYS */;
INSERT INTO `types_publications` VALUES (1,'article'),(2,'rapport'),(3,'these'),(4,'communication'),(5,'poster');
/*!40000 ALTER TABLE `types_publications` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `user_permissions`
--

DROP TABLE IF EXISTS `user_permissions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `user_permissions` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `permission_id` int NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_user_permission` (`user_id`,`permission_id`),
  KEY `permission_id` (`permission_id`)
) ENGINE=MyISAM AUTO_INCREMENT=64 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `user_permissions`
--

LOCK TABLES `user_permissions` WRITE;
/*!40000 ALTER TABLE `user_permissions` DISABLE KEYS */;
INSERT INTO `user_permissions` VALUES (1,16,26),(2,16,24),(3,16,20),(4,16,33),(5,16,27),(6,16,7),(7,16,14),(8,16,1),(9,1,38),(10,1,39),(11,1,40),(12,1,25),(13,1,26),(14,1,21),(15,1,23),(16,1,22),(17,1,24),(18,1,20),(19,1,34),(20,1,36),(21,1,35),(22,1,37),(23,1,33),(24,1,31),(25,1,32),(26,1,28),(27,1,30),(28,1,29),(29,1,27),(30,1,8),(31,1,10),(32,1,12),(33,1,9),(34,1,11),(35,1,13),(36,1,7),(37,1,15),(38,1,17),(39,1,16),(40,1,19),(41,1,18),(42,1,14),(43,1,2),(44,1,4),(45,1,3),(46,1,6),(47,1,5),(48,1,1),(49,3,24),(50,3,20),(51,3,37),(52,3,33),(53,3,27),(54,3,8),(55,3,12),(56,3,11),(57,3,13),(58,3,7),(59,3,15),(60,3,19),(61,3,18),(62,3,14),(63,3,1);
/*!40000 ALTER TABLE `user_permissions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `users` (
  `id_user` int NOT NULL AUTO_INCREMENT,
  `username` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `password` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `nom` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `prenom` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `email` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `photo` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `grade` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `poste` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `domaine_recherche` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `biographie` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  `role` enum('admin','enseignant-chercheur','doctorant','etudiant','invite') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT 'enseignant-chercheur',
  `statut` enum('actif','suspendu','inactif') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT 'actif',
  `is_deleted` tinyint(1) DEFAULT '0',
  `date_creation` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `derniere_connexion` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id_user`),
  UNIQUE KEY `username` (`username`),
  UNIQUE KEY `email` (`email`)
) ENGINE=MyISAM AUTO_INCREMENT=17 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (1,'admin','$2y$10$NVUEaI.q30hMyQEZZNMUwOME0CfFYAEEAj68G2nNPIDc5ugfk.n/u','Admin','System','admin@lmcs.dz','694927edecee0_1766402029.png','Administrateur','Administrateur système','','','admin','actif',0,'2025-12-04 20:59:39','2026-01-10 07:33:48'),(2,'user','$2y$10$PckCuItrjU6NlrYkq.uK0OF79RP8HCBQrC0De9BijDLaQmvH/aRMC','Benali','Ahmed','ba_benali@lmcs.dz','693d442e943db_1765622830.png','Professeur','Directeur du laboratoire','Intelligence Artificielle, Systèmes Distribués','Professeur et directeur du laboratoire LMCS depuis 2018. Spécialiste en IA et systèmes distribués avec plus de 20 ans d\'expérience.','enseignant-chercheur','actif',0,'2025-12-04 20:59:39','2026-01-06 08:38:54'),(3,'djabri_samira','$2y$10$Z4g.Jh6FMOv3dQRbb3gDw.5CePDDaaZgHZSbN1.T26HjvMYzHhUl2','Djabri','Samira','ds_djabrit@lmcs.dz','693d400e465a7_1765621774.png','MCA','Chef d\'équipe IA','Intelligence Artificielle, Machine Learning','Maître de conférences spécialisée en IA et apprentissage automatique. Chef de l\'équipe IA depuis 2020.','enseignant-chercheur','actif',0,'2025-12-04 20:59:39','2026-01-09 20:13:55'),(4,'meziani_karim','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','Meziani','Karim','meziani.karim@lmcs.dz','photos/meziani_karim.jpg','MCB','Trésorier du laboratoire','Gestion de projets, Finance','Maître de conférences et trésorier du laboratoire LMCS. Gère les budgets et financements des projets de recherche.','enseignant-chercheur','actif',0,'2025-12-16 09:50:13','2026-01-06 08:34:45'),(5,'boukhelf_rachid','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','Boukhelf','Rachid','boukhelf.rachid@lmcs.dz','photos/boukhelf_rachid.jpg','MCB','Chercheur en Deep Learning','Deep Learning, Computer Vision','Spécialiste en deep learning et vision par ordinateur. Travaille sur les réseaux de neurones convolutifs.','enseignant-chercheur','actif',0,'2025-12-16 09:50:13',NULL),(6,'amrani_fatima','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','Amrani','Fatima','amrani.fatima@lmcs.dz','photos/amrani_fatima.jpg','Doctorante','Doctorante en NLP','Traitement du Langage Naturel, NLP','Doctorante travaillant sur le traitement automatique du langage naturel en langue arabe.','doctorant','actif',0,'2025-12-16 09:50:13',NULL),(7,'benzerga_yacine','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','Benzerga','Yacine','benzerga.yacine@lmcs.dz','photos/benzerga_yacine.jpg','Doctorant','Doctorant en IA','Apprentissage par Renforcement','Doctorant spécialisé dans l\'apprentissage par renforcement et les systèmes multi-agents.','doctorant','actif',0,'2025-12-16 09:50:13',NULL),(8,'khelifi_mohammed','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','Khelifi','Mohammed','khelifi.mohammed@lmcs.dz','photos/khelifi_mohammed.jpg','Professeur','Chef d\'équipe Réseaux','Réseaux, Sécurité Informatique, Cryptographie','Professeur et chef de l\'équipe Réseaux et Sécurité. Expert en cryptographie et protocoles de sécurité.','enseignant-chercheur','actif',0,'2025-12-16 09:50:13',NULL),(9,'belgacem_nadia','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','Belgacem','Nadia','belgacem.nadia@lmcs.dz','photos/belgacem_nadia.jpg','MCA','Chercheuse en Cybersécurité','Cybersécurité, Détection d\'intrusions','Maître de conférences spécialisée en cybersécurité et systèmes de détection d\'intrusions.','enseignant-chercheur','actif',0,'2025-12-16 09:50:13',NULL),(10,'saidi_abdelkader','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','Saidi','Abdelkader','saidi.abdelkader@lmcs.dz','photos/saidi_abdelkader.jpg','MCB','Chercheur en Réseaux','Réseaux sans fil, IoT','Spécialiste des réseaux sans fil et de l\'Internet des objets (IoT).','enseignant-chercheur','actif',0,'2025-12-16 09:50:13',NULL),(11,'hamza_leila','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','Hamza','Leila','hamza.leila@lmcs.dz','photos/hamza_leila.jpg','Doctorante','Doctorante en Sécurité','Blockchain, Sécurité distribuée','Doctorante travaillant sur la sécurité des systèmes distribués et la technologie blockchain.','doctorant','actif',0,'2025-12-16 09:50:13',NULL),(12,'ziani_hakim','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','Ziani','Hakim','ziani.hakim@lmcs.dz','photos/ziani_hakim.jpg','MCA','Chef d\'équipe Robotique','Robotique, Systèmes Embarqués, Vision','Maître de conférences et chef de l\'équipe Systèmes Intelligents et Robotique. Expert en robotique mobile.','enseignant-chercheur','actif',0,'2025-12-16 09:50:13',NULL),(13,'mansouri_yasmine','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','Mansouri','Yasmine','mansouri.yasmine@lmcs.dz','photos/mansouri_yasmine.jpg','MCB','Chercheuse en Vision','Vision par ordinateur, Traitement d\'images','Spécialiste en vision par ordinateur et traitement d\'images appliqués à la robotique.','enseignant-chercheur','actif',0,'2025-12-16 09:50:13',NULL),(14,'touati_bilal','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','Touati','Bilal','touati.bilal@lmcs.dz','photos/touati_bilal.jpg','Doctorant','Doctorant en Robotique','Navigation autonome, SLAM','Doctorant travaillant sur la navigation autonome et les algorithmes SLAM pour robots mobiles.','doctorant','actif',0,'2025-12-16 09:50:13',NULL),(15,'mekki_sarah','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','Mekki','Sarah','mekki.sarah@lmcs.dz','photos/mekki_sarah.jpg','Doctorante','Doctorante en Systèmes Embarqués','Systèmes embarqués, IoT robotique','Doctorante spécialisée dans les systèmes embarqués pour la robotique et l\'IoT.','doctorant','actif',0,'2025-12-16 09:50:13',NULL),(16,'allouche_reda','$2y$10$uJ31CsNI8dHrIoQI9Ngaue02bv50Z/6iyQRe0CGrgKtG/BfbbUVxS','Allouche','Reda','mr_allouche@esi.dz',NULL,'Apprenti Chercheur','',NULL,NULL,'etudiant','actif',0,'2025-12-22 16:53:46',NULL);
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-01-10 18:04:21
