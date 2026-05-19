SET @stmt = (
    SELECT IF(
        COUNT(*) = 0,
        'ALTER TABLE `orders` ADD COLUMN `delivery_collection_date` DATE DEFAULT NULL AFTER `shipping_postcode`',
        'SELECT 1'
    )
    FROM information_schema.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE()
      AND TABLE_NAME = 'orders'
      AND COLUMN_NAME = 'delivery_collection_date'
);
PREPARE stmt FROM @stmt;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SET @copy_stmt = (
    SELECT IF(
        COUNT(*) > 0,
        'UPDATE `orders` SET `delivery_collection_date` = `preferred_fulfilment_date` WHERE `delivery_collection_date` IS NULL AND `preferred_fulfilment_date` IS NOT NULL',
        'SELECT 1'
    )
    FROM information_schema.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE()
      AND TABLE_NAME = 'orders'
      AND COLUMN_NAME = 'preferred_fulfilment_date'
);
PREPARE copy_stmt FROM @copy_stmt;
EXECUTE copy_stmt;
DEALLOCATE PREPARE copy_stmt;
