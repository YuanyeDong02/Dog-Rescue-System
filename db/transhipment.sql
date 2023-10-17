/*
SQLyog Community v13.2.0 (64 bit)
MySQL - 8.0.12 : Database - transhipment
*********************************************************************
*/

/*!40101 SET NAMES utf8 */;

/*!40101 SET SQL_MODE=''*/;

/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;
CREATE DATABASE /*!32312 IF NOT EXISTS*/`transhipment` /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci */;

USE `transhipment`;

/*Table structure for table `address` */

DROP TABLE IF EXISTS `address`;

CREATE TABLE `address` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `userID` int(11) unsigned NOT NULL,
  `receiver` varchar(128) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '收件人姓名',
  `line1` varchar(512) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `line2` varchar(512) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `line3` varchar(512) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL COMMENT '（供国内仓库地址使用）',
  `city` varchar(128) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `postcode` varchar(128) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `country` varchar(128) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'United Kingdom',
  `tel` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

/*Data for the table `address` */

insert  into `address`(`id`,`userID`,`receiver`,`line1`,`line2`,`line3`,`city`,`postcode`,`country`,`tel`) values 
(1,1,'Ziyi','Flat A2304','9 Owen Street','','Manchester','M15 4TQ','英国','01312562197');

/*Table structure for table `channel` */

DROP TABLE IF EXISTS `channel`;

CREATE TABLE `channel` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `effectiveness` int(11) NOT NULL COMMENT '时效（天）',
  `firstPrice` float NOT NULL COMMENT '首重',
  `laterPrice` float NOT NULL COMMENT '续重',
  `name` varchar(128) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '渠道名称',
  `comment` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

/*Data for the table `channel` */

insert  into `channel`(`id`,`effectiveness`,`firstPrice`,`laterPrice`,`name`,`comment`) values 
(1,8,80,46,'空运普货','非液体，可发食品、固态化妆品'),
(2,8,80,54,'空运液体','');

/*Table structure for table `coupon` */

DROP TABLE IF EXISTS `coupon`;

CREATE TABLE `coupon` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `type` enum('FIXED','RATIO') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '固定/比例优惠',
  `value` float NOT NULL COMMENT '价值',
  `expire` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '过期时间',
  `status` tinyint(4) NOT NULL DEFAULT '1' COMMENT '是否可用',
  `code` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '优惠码',
  `recurrent` tinyint(4) NOT NULL DEFAULT '0' COMMENT '长期有效',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `coupon` */

/*Table structure for table `order_address` */

DROP TABLE IF EXISTS `order_address`;

CREATE TABLE `order_address` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `line1` varchar(256) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `line2` varchar(256) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `line3` varchar(256) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `city` varchar(64) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `receiver` varchar(64) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `country` varchar(64) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT 'United Kingdom',
  `postcode` varchar(64) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `tel` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

/*Data for the table `order_address` */

insert  into `order_address`(`id`,`line1`,`line2`,`line3`,`city`,`receiver`,`country`,`postcode`,`tel`) values 
(1,'Flat A2304','9 Owen Street','','Manchester','Ziyi','英国','M15 4TQ','01312562197');

/*Table structure for table `order_images` */

DROP TABLE IF EXISTS `order_images`;

CREATE TABLE `order_images` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `orderID` int(10) unsigned NOT NULL,
  `md5` varchar(64) COLLATE utf8mb4_general_ci NOT NULL,
  `ext` varchar(5) COLLATE utf8mb4_general_ci NOT NULL,
  `date` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

/*Data for the table `order_images` */

insert  into `order_images`(`id`,`orderID`,`md5`,`ext`,`date`) values 
(16,1,'e7aa1a51073fab65c974c99040e8fa89','png','2023-07-28 13:16:36');

/*Table structure for table `orders` */

DROP TABLE IF EXISTS `orders`;

CREATE TABLE `orders` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `uid` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `userID` int(11) unsigned NOT NULL,
  `orderAddressID` int(11) unsigned NOT NULL,
  `weight` float DEFAULT NULL,
  `volume` float DEFAULT NULL,
  `price` float DEFAULT NULL,
  `channelID` int(11) NOT NULL COMMENT '转运渠道',
  `trackNum` varchar(128) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `createAt` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
  `comment` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci COMMENT '备注',
  `dispatchStatus` tinyint(1) NOT NULL DEFAULT '0' COMMENT '发货状态',
  `dispatchTime` datetime DEFAULT NULL COMMENT '发货时间',
  `paymentStatus` tinyint(1) NOT NULL DEFAULT '0' COMMENT '付款状态',
  `processStatus` tinyint(1) NOT NULL DEFAULT '0',
  `pointGain` int(11) DEFAULT NULL,
  `payWithPoint` tinyint(4) NOT NULL,
  PRIMARY KEY (`id`,`uid`)
) ENGINE=InnoDB AUTO_INCREMENT=2510284626 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

/*Data for the table `orders` */

insert  into `orders`(`id`,`uid`,`userID`,`orderAddressID`,`weight`,`volume`,`price`,`channelID`,`trackNum`,`createAt`,`comment`,`dispatchStatus`,`dispatchTime`,`paymentStatus`,`processStatus`,`pointGain`,`payWithPoint`) values 
(1,'0000',1,1,200,NULL,5,1,'1212121','2023-07-25 12:44:41','关注永雏塔菲喵，关注永雏塔菲谢谢喵',1,'2023-07-28 13:57:40',1,1,NULL,1);

/*Table structure for table `parcel` */

DROP TABLE IF EXISTS `parcel`;

CREATE TABLE `parcel` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `orderID` int(11) unsigned DEFAULT NULL COMMENT '对应的转运ID，创建后再更新',
  `userID` int(11) unsigned DEFAULT NULL COMMENT '用户ID',
  `trackNum` varchar(128) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '快递单号',
  `status` enum('NOTRECEIVED','RECEIVED','PROCESSED') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'NOTRECEIVED' COMMENT '状态',
  `comment` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci COMMENT '备注',
  `createdAt` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '添加日期',
  `receivedAt` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

/*Data for the table `parcel` */

insert  into `parcel`(`id`,`orderID`,`userID`,`trackNum`,`status`,`comment`,`createdAt`,`receivedAt`) values 
(1,1,1,'111','PROCESSED','1','2023-07-23 21:26:59','2023-07-23 21:27:06'),
(2,1,1,'222','PROCESSED','2','2023-07-23 21:27:00','2023-07-23 21:27:06'),
(3,1,1,'333','PROCESSED','','2023-07-23 21:27:00','2023-07-23 21:27:06'),
(4,2,1,'114514','PROCESSED','','2023-07-28 11:46:28','2023-07-28 11:46:40'),
(5,NULL,1,'114515','RECEIVED','','2023-07-28 11:46:28','2023-07-28 11:46:40');

/*Table structure for table `setting` */

DROP TABLE IF EXISTS `setting`;

CREATE TABLE `setting` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `key` varchar(256) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `comment` varchar(256) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `setting` */

insert  into `setting`(`id`,`key`,`value`,`comment`) values 
(1,'pointRate','0.15','积分比例'),
(2,'warehouseAddress','11111\n22222\n33333','仓库地址'),
(3,'notice','关注永雏塔菲喵，关注永雏塔菲谢谢喵','公告'),
(4,'minUsablePoint','500','最低可消费积分');

/*Table structure for table `user` */

DROP TABLE IF EXISTS `user`;

CREATE TABLE `user` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `username` varchar(128) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `password` varchar(128) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `email` varchar(128) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `points` float DEFAULT '0',
  `tel` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '电话号码',
  `admin` tinyint(4) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

/*Data for the table `user` */

insert  into `user`(`id`,`username`,`password`,`email`,`points`,`tel`,`admin`) values 
(1,'admin','$argon2id$v=19$m=65536,t=4,p=1$b0xOMGFua2ljMjQ4bDJ0Mg$x5IOaeC5RqmIql/Wd7uPn0kmouNcqjqX4xEDUs8yOuA','admin@admin.com',30,'123456',1),
(3,'monesy','$argon2id$v=19$m=65536,t=4,p=1$QS5EN0NlNEtNUXNyZGJ2UA$ehZBne45iQmnM1lHFgfEpZ7GSo1gwRj+bvAp4dxuQZM','test@monesy.com',0,'7355608',0);

/*Table structure for table `user_token` */

DROP TABLE IF EXISTS `user_token`;

CREATE TABLE `user_token` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `userID` int(10) unsigned NOT NULL,
  `token` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `expire` datetime NOT NULL,
  `type` enum('cookie','reset') COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `user_token` */

insert  into `user_token`(`id`,`userID`,`token`,`expire`,`type`) values 
(1,1,'FhGAlEsdueNWUAmF5fM428YrZhA95NrN','2023-08-04 10:24:27','cookie');

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
