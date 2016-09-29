
START TRANSACTION;

INSERT INTO `fields` VALUES (NULL, 'system_134', 'frontend', 'System / Voucher code cannot be empty.', 'script', '2015-08-20 08:58:42');

SET @id := (SELECT LAST_INSERT_ID());

INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '1', 'title', 'Voucher code cannot be empty.', 'script');
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '2', 'title', 'Voucher code cannot be empty.', 'script');
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '3', 'title', 'Voucher code cannot be empty.', 'script');

INSERT INTO `fields` VALUES (NULL, 'system_135', 'frontend', 'System / Date cannot be empty.', 'script', '2015-08-20 09:00:23');

SET @id := (SELECT LAST_INSERT_ID());

INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '1', 'title', 'Date cannot be empty.', 'script');
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '2', 'title', 'Date cannot be empty.', 'script');
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '3', 'title', 'Date cannot be empty.', 'script');

INSERT INTO `fields` VALUES (NULL, 'system_136', 'frontend', 'System / Voucher code is out of date.', 'script', '2015-08-20 09:01:01');

SET @id := (SELECT LAST_INSERT_ID());

INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '1', 'title', 'Voucher code is out of date.', 'script');
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '2', 'title', 'Voucher code is out of date.', 'script');
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '3', 'title', 'Voucher code is out of date.', 'script');

INSERT INTO `fields` VALUES (NULL, 'system_137', 'frontend', 'System / Voucher code has been applied.', 'script', '2015-08-20 09:01:41');

SET @id := (SELECT LAST_INSERT_ID());

INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '1', 'title', 'Voucher code has been applied.', 'script');
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '2', 'title', 'Voucher code has been applied.', 'script');
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '3', 'title', 'Voucher code has been applied.', 'script');

COMMIT;