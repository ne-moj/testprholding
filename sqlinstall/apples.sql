CREATE TABLE `apple` (
    `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT ,
    `tree_id` INT(11) UNSIGNED NOT NULL DEFAULT '0' ,
    `color` VARCHAR(7) NOT NULL DEFAULT '#00B454' ,
    `eaten` TINYINT UNSIGNED NOT NULL DEFAULT '0' ,
    `status` ENUM('hanging','lay','decayed','') NOT NULL DEFAULT 'hanging' ,
    `pos_x` SMALLINT NOT NULL DEFAULT '0' ,
    `pos_y` SMALLINT NOT NULL DEFAULT '0' ,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ,
    `fell_in` DATETIME,
    PRIMARY KEY (`id`)
) ENGINE = InnoDB CHARSET=utf8 COLLATE utf8_general_ci;
