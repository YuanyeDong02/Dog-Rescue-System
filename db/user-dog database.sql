/*
SQLyog Community v13.2.0 (64 bit)
MySQL - 8.0.12 : Database - dog
*********************************************************************
*/

/*!40101 SET NAMES utf8 */;

/*!40101 SET SQL_MODE=''*/;

/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;
CREATE DATABASE /*!32312 IF NOT EXISTS*/`dog` /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci */;

USE `dog`;

/*Table structure for table `apply` */

DROP TABLE IF EXISTS `apply`;

CREATE TABLE `apply` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `Name` varchar(512) COLLATE utf8mb4_unicode_ci NOT NULL,
  `Address` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `CityRegion` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `PostalCode` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL,
  `Age` int(10) unsigned NOT NULL,
  `Occupation` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `FamilyMembers` int(10) unsigned NOT NULL,
  `FamilyMemberDetails` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `HousingType` enum('House','Townhouse','MobileHome','Apartment') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `AllowingPets` tinyint(1) NOT NULL,
  `ApartmentPetPolicy` tinyint(4) NOT NULL,
  `ApartmentPolicyDetails` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `PetExperience` tinyint(1) NOT NULL,
  `PetExperienceDetails` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `ReasonToAdopt` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `PetCareAndTrainingPlan` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `DailyScheduleImpact` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `DailyTimeWithDog` int(10) unsigned NOT NULL,
  `ExerciseAndPlay` tinyint(1) NOT NULL,
  `VeterinaryCare` tinyint(1) NOT NULL,
  `HomeVisit` tinyint(1) NOT NULL,
  `FamilyIncome` int(11) NOT NULL,
  `userid` int(11) NOT NULL,
  `active` tinyint(1) NOT NULL,
  `dogid` int(10) DEFAULT NULL,
  `statuses` tinyint(4) NOT NULL DEFAULT '0',
  `result` tinyint(4) NOT NULL DEFAULT '0',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  KEY `id` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `apply` */

insert  into `apply`(`id`,`Name`,`Address`,`CityRegion`,`PostalCode`,`Age`,`Occupation`,`FamilyMembers`,`FamilyMemberDetails`,`HousingType`,`AllowingPets`,`ApartmentPetPolicy`,`ApartmentPolicyDetails`,`PetExperience`,`PetExperienceDetails`,`ReasonToAdopt`,`PetCareAndTrainingPlan`,`DailyScheduleImpact`,`DailyTimeWithDog`,`ExerciseAndPlay`,`VeterinaryCare`,`HomeVisit`,`FamilyIncome`,`userid`,`active`,`dogid`,`statuses`,`result`,`created_at`) values 
(6,'test','test','test','test',12,'test',2,'test','House',1,1,'tets',1,'test','test','test','test',2,1,1,1,6000,1,0,7,1,1,'2024-02-10 15:43:29'),
(7,'test','test','test','test',12,'test',4,'test','Apartment',1,1,'yesy',1,'yeys','yeysu','e8is','e72uijj',2,1,1,1,3000,1,0,7,1,0,'2024-02-10 15:43:29'),
(8,'twst','tetstt','testtt','tetst',14,'test',3,'test','House',1,1,'test',1,'test','test','test','test',3,1,1,1,6000,1,0,7,1,0,'2024-02-10 15:43:29');

/*Table structure for table `newdog` */

DROP TABLE IF EXISTS `newdog`;

CREATE TABLE `newdog` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `Name` varchar(512) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `Breed` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `Age` int(11) NOT NULL,
  `Gender` enum('Male','Female','Unknown') COLLATE utf8mb4_unicode_ci NOT NULL,
  `Color` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `SpayedOrNeutered` tinyint(1) NOT NULL,
  `VaccinationStatus` tinyint(4) NOT NULL,
  `SpecialNeeds` tinyint(4) NOT NULL,
  `AdoptionRestrictions` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `AdoptionStatus` enum('Available','Adopted','On Hold','Foster Care') COLLATE utf8mb4_unicode_ci NOT NULL,
  `Weight` varchar(11) COLLATE utf8mb4_unicode_ci NOT NULL,
  `image` varchar(512) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  KEY `id` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `newdog` */

insert  into `newdog`(`id`,`Name`,`Breed`,`Age`,`Gender`,`Color`,`SpayedOrNeutered`,`VaccinationStatus`,`SpecialNeeds`,`AdoptionRestrictions`,`AdoptionStatus`,`Weight`,`image`) values 
(7,'Bob','golden retriever',1,'Male','Golden',1,1,1,'Since the dog is still young, it needs to be taken care of by the family.\r\nAt least 2h of playtime per ','Available','3kg','dogimg/20231121\\9e0ac392fc049aba83028df17d737af4.jpg'),
(8,'Jimmy',' Yorkshire dog',2,'Male','White',1,0,1,'1.No vaccinations yet, need adopter to take dog for vaccinations\r\n\r\n2.Dogs with gastrointestinal problems need regular checkups','On Hold','2kg','dogimg/20231121\\ddf9d74ad7848df2f3d1d51aa41d77d7.jpg'),
(9,'Coco','border collie',3,'Female','Black and white',1,1,0,'Patience is required.','Available','7kg','dogimg/20240220\\f8c957e31e4c39eafaa41a16be55abbd.jpg'),
(10,'Wednesday',' Samoyed',5,'Female','White',1,1,1,'I\'m looking for my forever home. Could you be my perfect match?','Available','10kg','dogimg/20240220\\72f0417da647954ed39f568c0f73d698.jpg'),
(11,'Buddy','Alaskan Malamute Cross',5,'Male','Black and white',1,1,1,'Buddy needs a home with no other pets or children.','Available','20kg','dogimg/20240220\\28dc33ec4239b1f3b7c9b6f92f72075e.jpg'),
(12,'Mojo','Retriever (Labrador) Cross',4,'Male','Brown',1,1,1,'Dogs and Secondary school age children.','Available','5kg','dogimg/20240220\\14c6f8426687d92dba61135b94a64bf3.jpg'),
(13,'Hugo','Dogue De Bordeaux Cross',2,'Male','Brown',1,1,1,'May live with Dogs and Secondary school age children.','Available','20kg','dogimg/20240220\\ce845559b7dd4a188c5bac138a57f0e3.webp'),
(14,'Lily','Collie (Border)',5,'Female','Black',1,1,1,'May live with Dogs and Secondary school age children.','Available','15kg','dogimg/20240220\\d87ea047d2076d0996d78daf3460381c.jpg'),
(15,'Nana','Spaniel (Cocker)',6,'Female','Brown',1,1,0,'May live with Secondary school age children.\r\n','Available','15kg','dogimg/20240220\\1eb25d21ab83c0eeff99026148882035.jpg'),
(16,'Nyx','Belgian Shepherd Dog (Malinois)',2,'Male','Black',1,1,0,'Nyx needs a home with no other pets or children.','Available','20kg','dogimg/20240220\\419d3cfc0ae536d7e7583eed86fa9a29.jpg');

/*Table structure for table `selecttime` */

DROP TABLE IF EXISTS `selecttime`;

CREATE TABLE `selecttime` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `userid` int(10) NOT NULL,
  `time` datetime NOT NULL,
  `link` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  KEY `id` (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=30 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `selecttime` */

insert  into `selecttime`(`id`,`userid`,`time`,`link`) values 
(1,1,'2024-02-23 21:05:00',NULL),
(2,1,'2024-02-16 17:20:00',NULL),
(3,1,'2024-02-16 17:20:00',NULL),
(4,1,'2024-02-16 17:20:00',NULL),
(5,1,'2024-02-16 17:20:00',NULL),
(6,1,'2024-02-16 17:20:00',NULL),
(7,1,'2024-02-16 17:20:00',NULL),
(8,1,'2024-02-16 17:20:00',NULL),
(9,1,'2024-02-15 17:22:00',NULL),
(10,1,'2024-02-10 22:26:00',NULL),
(11,1,'2024-02-10 22:28:00',NULL),
(12,1,'2024-02-10 22:28:00',NULL),
(13,1,'2024-02-10 22:28:00',NULL),
(14,1,'2024-02-10 22:28:00',NULL),
(15,1,'2024-02-10 21:32:00',NULL),
(16,1,'2024-02-10 21:32:00',NULL),
(17,1,'2024-02-10 21:32:00',NULL),
(18,1,'2024-02-10 21:32:00',NULL),
(19,1,'2024-02-10 21:32:00',NULL),
(20,1,'2024-02-23 22:08:00','chkQeCxdWIIltupqLXwy'),
(21,1,'2024-02-23 22:08:00','eHsHZwaXFIhRlQzOPZKY'),
(22,1,'2024-02-23 22:08:00','wGnVHXCdWToMbFiTApAO'),
(23,1,'2024-02-10 23:55:00','nQxaDGNHcnJfuIACheNg'),
(24,1,'2024-02-10 23:56:00','iHxtyqaZyTrBJruXbeMN'),
(25,1,'2024-02-11 00:56:00','NxtQHKSsxMCtDmXOYVji'),
(26,1,'2024-02-11 00:56:00','JgTleWuuxNQXgYdPttpM'),
(27,1,'2024-02-12 00:56:00','yqQYfYcULQmbBzaUXuHb'),
(28,1,'2024-02-13 00:56:00','BXzHCkiYNiocxmSykRmY'),
(29,1,'2024-02-10 00:20:00','FLIwPcgRBfIhJonuWKol');

/*Table structure for table `user` */

DROP TABLE IF EXISTS `user`;

CREATE TABLE `user` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `email` varchar(512) COLLATE utf8mb4_unicode_ci NOT NULL,
  `password` varchar(512) COLLATE utf8mb4_unicode_ci NOT NULL,
  `admin` tinyint(4) NOT NULL DEFAULT '0',
  `tel` varchar(60) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `user` */

insert  into `user`(`id`,`email`,`password`,`admin`,`tel`) values 
(1,'2601447509@qq.com','$argon2id$v=19$m=65536,t=4,p=1$Y2I5RGU0YkVRcG8ycXY1bQ$msn0ymZkpEwFbXxCtSZ9KN9Zh+shRL1bUqv+7bByQIU',1,'07421732313');

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
