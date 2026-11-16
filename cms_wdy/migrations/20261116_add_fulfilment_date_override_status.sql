ALTER TABLE `blocked_fulfilment_dates`
    ADD COLUMN `availability_status` varchar(20) NOT NULL DEFAULT 'blocked' AFTER `fulfilment_type`;
