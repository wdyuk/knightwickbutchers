CREATE TABLE IF NOT EXISTS `blocked_fulfilment_dates` (
    `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
    `blocked_date` date NOT NULL,
    `fulfilment_type` varchar(20) NOT NULL,
    `created_at` datetime NOT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `blocked_fulfilment_dates_unique` (`blocked_date`, `fulfilment_type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
