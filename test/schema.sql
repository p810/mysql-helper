CREATE DATABASE IF NOT EXISTS `mysql_helper_test`;
USE `mysql_helper_test`;

CREATE TABLE IF NOT EXISTS `test_table` (
    `test_id` int(11) NOT NULL AUTO_INCREMENT,
    `message` varchar(140) NOT NULL,
    PRIMARY KEY (`test_id`)
);

INSERT INTO `test_table` (`test_id`, `message`) VALUES (1, 'I am the first row'), (2, 'I am the second row');
