ALTER TABLE `user`
ADD `reset_token_hash` VARCHAR(64) NULL DEFAULT NULL AFTER `password`,
ADD `reset_token_expires_at` DATETIME NULL DEFAULT NULL AFTER `reset_token_hash`;
