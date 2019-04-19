CREATE DATABASE IF NOT EXISTS `mysql_helper_test`;
USE `mysql_helper_test`;

CREATE TABLE IF NOT EXISTS `test_table` (
    `test_id` int(11) NOT NULL AUTO_INCREMENT,
    `message` varchar(140) NOT NULL,
    PRIMARY KEY (`test_id`)
);
