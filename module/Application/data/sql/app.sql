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
CREATE TABLE IF NOT EXISTS `app_categories` (
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
