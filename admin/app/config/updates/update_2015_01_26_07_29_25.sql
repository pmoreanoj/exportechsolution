
START TRANSACTION;

INSERT INTO `fields` VALUES (NULL, 'system_133', 'frontend', 'System / Voucher not found', 'script', '2015-01-26 07:21:28');

SET @id := (SELECT LAST_INSERT_ID());

INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '1', 'title', 'Voucher not found', 'script');
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '2', 'title', 'Voucher not found', 'script');
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '3', 'title', 'Voucher not found', 'script');

COMMIT;