
START TRANSACTION;

INSERT INTO `fields` VALUES (NULL, 'lblOutOfStock', 'backend', 'Label / Out of stock', 'script', '2015-05-15 05:26:46');

SET @id := (SELECT LAST_INSERT_ID());

INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '1', 'title', 'Out of stock', 'script');
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '2', 'title', 'Out of stock', 'script');
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '3', 'title', 'Out of stock', 'script');

COMMIT;