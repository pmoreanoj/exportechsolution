
START TRANSACTION;

INSERT INTO `fields` VALUES (NULL, 'front_btn_remove_discount', 'frontend', 'Button / Remove discount', 'script', '2015-12-09 03:58:10');

SET @id := (SELECT LAST_INSERT_ID());

INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '1', 'title', 'Remove discount', 'script');
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '2', 'title', 'Remove discount', 'script');
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '3', 'title', 'Remove discount', 'script');

COMMIT;