/*
SQLyog Ultimate v13.1.1 (64 bit)
MySQL - 10.4.8-MariaDB : Database - amsalcolac
*********************************************************************
*/

/*!40101 SET NAMES utf8 */;

/*!40101 SET SQL_MODE=''*/;

/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;
CREATE DATABASE /*!32312 IF NOT EXISTS*/`amsalcolac` /*!40100 DEFAULT CHARACTER SET utf8mb4 */;

USE `amsalcolac`;

/*Table structure for table `menu_items` */

DROP TABLE IF EXISTS `menu_items`;

CREATE TABLE `menu_items` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `menu_id` bigint(20) unsigned NOT NULL,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `link` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `parent` int(11) NOT NULL,
  `access_level` int(11) NOT NULL,
  `order` int(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=22 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `menu_items` */

insert  into `menu_items`(`id`,`menu_id`,`name`,`link`,`parent`,`access_level`,`order`,`created_at`,`updated_at`,`deleted_at`) values 
(1,3,'Employees','employees',0,1,3,'2020-11-02 12:58:13',NULL,NULL),
(3,4,'Files','files',0,1,4,'2020-11-02 12:58:13',NULL,NULL),
(4,71,'Declarations','declarations',7,1,71,'2020-11-02 12:58:13',NULL,NULL),
(5,72,'SMS Templates ','sms-templates',7,1,72,'2021-02-04 12:08:05',NULL,NULL),
(6,5,'Messaging','messaging',0,2,5,NULL,NULL,NULL),
(9,73,'Role Manager','roles',7,1,73,NULL,NULL,NULL),
(10,7,'Settings','settings',0,2,7,NULL,NULL,NULL),
(11,1,'Dashboard','',0,1,1,'2021-03-17 03:06:07',NULL,NULL),
(12,2,'Admin Users','admin-users',0,1,2,NULL,NULL,NULL),
(13,74,'System Cron','system-crons',7,1,74,'2021-04-19 07:37:49',NULL,NULL),
(14,6,'QR Codes','qr-codes',0,1,6,NULL,NULL,NULL),
(16,70,'General Settings','settings',7,1,70,NULL,NULL,NULL),
(17,51,'Send Message','send-sms',5,1,51,NULL,NULL,NULL),
(18,52,'Message Status','sms-status',5,1,52,NULL,NULL,NULL),
(19,53,'Message Schedule','sms-schedule',5,1,53,NULL,NULL,NULL),
(20,75,'System Log','system-log',7,1,75,NULL,NULL,NULL),
(21,8,'RAT Result','rat-result',0,1,8,NULL,NULL,NULL);

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
