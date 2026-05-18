ALTER TABLE `events`
ADD COLUMN `source` TINYINT(3) UNSIGNED NOT NULL DEFAULT 1 COMMENT '1=store,2=customer'
AFTER `sms_status`;

ALTER TABLE `stores`
ADD COLUMN `data_api_key` VARCHAR(128) NULL COMMENT 'Plain text key for external data API'
AFTER `api_key_last4`;