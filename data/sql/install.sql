CREATE DATABASE IF NOT EXISTS `laminas`;

USE `laminas`;

DROP TABLE IF EXISTS `user_auth`;
CREATE TABLE IF NOT EXISTS `user_auth` (
  `auth_id`   INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `username`  VARCHAR(40)      NOT NULL,
  `email`     VARCHAR(128)     NOT NULL,
  `password`  VARCHAR(100)     NOT NULL,
  `picture`   VARCHAR(255)     NOT NULL,
  `birthday`  DATE             NOT NULL,
  `gender`    ENUM('Female', 'Male', 'Other') NOT NULL,
  `role_id`   TINYINT(1)       NOT NULL DEFAULT '2',
  `active`    TINYINT(1)       NOT NULL DEFAULT '1',
  `created`   DATETIME         NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated`   DATETIME         NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`auth_id`),
  UNIQUE KEY `username` (`username`),
  UNIQUE KEY `email` (`email`),
  KEY `active` (`active`),
  KEY `role_id` (`role_id`),
  KEY `created` (`created`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `user_forgot`;
CREATE TABLE IF NOT EXISTS `user_forgot` (
  `auth_id` INT(11) UNSIGNED NOT NULL,
  `token`   VARCHAR(40)      NOT NULL,
  `created` DATETIME         NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`auth_id`),
  KEY `token` (`token`),
  FOREIGN KEY (`auth_id`) REFERENCES `user_auth` (`auth_id`)
  ON DELETE CASCADE
  ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `user_resources`;
CREATE TABLE IF NOT EXISTS `user_resources` (
  `resource_id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `module`      VARCHAR(20) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL,
  `controller`  VARCHAR(20) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL,
  `action`      VARCHAR(20) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL,
  PRIMARY KEY (`resource_id`,`module`,`controller`,`action`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE utf8mb4_unicode_ci;

INSERT INTO `user_resources` (`resource_id`, `module`, `controller`, `action`) VALUES
(1,  'application', 'admin', 'index'),
(2,  'application', 'comment', 'create'),
(3,  'application', 'comment', 'delete'),
(4,  'application', 'comment', 'edit'),
(5,  'application', 'help', 'contact'),
(6,  'application', 'help', 'privacy'),
(7,  'application', 'help', 'terms'),
(8,  'application', 'index', 'index'),
(9,  'application', 'quiz', 'answer'),
(10, 'application', 'quiz', 'create'),
(11, 'application', 'quiz', 'delete'),
(12, 'application', 'quiz', 'index'),
(13, 'application', 'quiz', 'view'),
(14, 'application', 'search', 'index'),
(15, 'user', 'admin', 'index'),
(16, 'user', 'auth', 'create'),
(17, 'user', 'login', 'index'),
(18, 'user', 'logout', 'index'),
(19, 'user', 'member', 'index'),
(20, 'user', 'password', 'forgot'),
(21, 'user', 'password', 'reset'),
(22, 'user', 'profile', 'index'),
(23, 'user', 'setting', 'delete'),
(24, 'user', 'setting', 'email'),
(25, 'user', 'setting', 'index'),
(26, 'user', 'setting', 'password'),
(27, 'user', 'setting', 'upload'),
(28, 'user', 'setting', 'username');

DROP TABLE IF EXISTS `user_roles`;
CREATE TABLE IF NOT EXISTS `user_roles` (
  `role_id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `role`    VARCHAR(20)      NOT NULL,
  PRIMARY KEY (`role_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE utf8mb4_unicode_ci;

INSERT IGNORE INTO `user_roles` (`role_id`, `role`) VALUES
(1, 'admin'),
(2, 'member'),
(3, 'guest');

DROP TABLE IF EXISTS `user_privileges`;
CREATE TABLE IF NOT EXISTS `user_privileges` (
  `role_id`     INT(11) UNSIGNED NOT NULL,
  `resource_id` INT(11) UNSIGNED NOT NULL,
  PRIMARY KEY (`role_id`,`resource_id`),
  FOREIGN KEY (`role_id`) REFERENCES `user_roles` (`role_id`)
  ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE utf8mb4_unicode_ci;

INSERT IGNORE INTO `user_privileges` (`role_id`, `resource_id`) VALUES
(1,1),
(1,2),
(1,3),
(1,4),
(1,5),
(1,6),
(1,7),
(1,8),
(1,9),
(1,10),
(1,11),
(1,12),
(1,13),
(1,14),
(1,15),
(1,18),
(1,19),
(1,22),
(1,23),
(1,24),
(1,25),
(1,26),
(1,27),
(1,28),

(2,2),
(2,3),
(2,4),
(2,5),
(2,6),
(2,7),
(2,8),
(2,9),
(2,10),
(2,11),
(2,12),
(2,13),
(2,14),
(2,18),
(2,19),
(2,22),
(2,23),
(2,24),
(2,25),
(2,26),
(2,27),
(2,28),

(3,5),
(3,6),
(3,7),
(3,8),
(3,9),
(3,13),
(3,14),
(3,16),
(3,17),
(3,19),
(3,20),
(3,21),
(3,22);

DROP TABLE IF EXISTS `app_quizzes`;
CREATE TABLE IF NOT EXISTS `app_quizzes` (
  `quiz_id`     INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `auth_id`     INT(11) UNSIGNED NOT NULL,
  `category_id` INT(11) UNSIGNED NOT NULL DEFAULT '0',
  `title`       VARCHAR(100)     NOT NULL,
  `question`    TEXT             NOT NULL,
  `status`      TINYINT(1)       NOT NULL DEFAULT '1',
  `allow`       TINYINT(1)       NOT NULL DEFAULT '1',
  `total`       INT(11) UNSIGNED NOT NULL DEFAULT '0',
  `comments`    INT(11) UNSIGNED NOT NULL DEFAULT '0',
  `views`       INT(11) UNSIGNED NOT NULL DEFAULT '0',
  `timeout`     DATETIME         NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `created`     DATETIME         NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`quiz_id`),
  KEY `auth_id` (`auth_id`),
  KEY `category_id` (`category_id`),
  KEY `created` (`created`),
  FOREIGN KEY (`auth_id`) REFERENCES `user_auth` (`auth_id`)
  ON DELETE CASCADE
  ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `app_categories`;
CREATE TABLE `app_categories` (
  `category_id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `category`    VARCHAR(40)      NOT NULL,
  PRIMARY KEY (`category_id`),
  KEY `category_id` (`category_id`, `category`),
  KEY `category` (`category`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE utf8mb4_unicode_ci;

INSERT IGNORE INTO `app_categories` (`category_id`, `category`) VALUES
(1, 'Health'),
(2, 'Politics'),
(3, 'Science'),
(4, 'Sports'),
(5, 'Technology'),
(6, 'Travel');

DROP TABLE IF EXISTS `app_comments`;
CREATE TABLE IF NOT EXISTS `app_comments` (
  `comment_id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `quiz_id`    INT(11) UNSIGNED NOT NULL,
  `auth_id`    INT(11) UNSIGNED NOT NULL,
  `comment`    TEXT             NOT NULL,
  `created`    DATETIME         NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`comment_id`),
  KEY `quiz` (`quiz_id`),
  KEY `auth_id` (`auth_id`),
  FOREIGN KEY (`quiz_id`) REFERENCES `app_quizzes` (`quiz_id`)
  ON DELETE CASCADE
  ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `app_answers`;
CREATE TABLE IF NOT EXISTS `app_answers` (
  `answer_id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `quiz_id`   INT(11) UNSIGNED NOT NULL,
  `tally`     INT(11) UNSIGNED NOT NULL DEFAULT '0',
  `answer`    TEXT             NOT NULL,
  PRIMARY KEY (`answer_id`),
  KEY `quiz_id` (`quiz_id`),
  FOREIGN KEY (`quiz_id`) REFERENCES `app_quizzes` (`quiz_id`)
  ON DELETE CASCADE
  ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `app_tallies`;
CREATE TABLE IF NOT EXISTS `app_tallies` (
  `quiz_id`   INT(11) UNSIGNED NOT NULL,
  `answer_id` INT(11) UNSIGNED NOT NULL,
  `auth_id`   INT(11) UNSIGNED NOT NULL,
  `created`   DATETIME         NOT NULL,
  PRIMARY KEY (`quiz_id`,`auth_id`),
  KEY `answer_id` (`answer_id`),
  KEY `auth_id` (`auth_id`),
  FOREIGN KEY (`answer_id`) REFERENCES `app_answers` (`answer_id`)
  ON DELETE CASCADE
  ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE utf8mb4_unicode_ci;
