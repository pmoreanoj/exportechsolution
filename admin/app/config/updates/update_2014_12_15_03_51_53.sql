
START TRANSACTION;

SET @id := (SELECT `id` FROM `fields` WHERE `key` = "front_booking_status_ARRAY_1");

UPDATE `multi_lang` SET `content` = 'Thank you. Your order has been made. [STAG]Go back[ETAG]' WHERE `foreign_id` = @id AND `model` = "pjField" AND `field` = "title";

COMMIT;