CREATE TABLE `tree` (
    `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
    `user_id` INT(11) UNSIGNED NOT NULL DEFAULT '0',
    `width` SMALLINT NOT NULL DEFAULT '0' ,
    `height` SMALLINT NOT NULL DEFAULT '0' ,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ,
    PRIMARY KEY (`id`)
) ENGINE = InnoDB CHARSET=utf8 COLLATE utf8_general_ci;
