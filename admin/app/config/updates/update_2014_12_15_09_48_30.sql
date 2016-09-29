
START TRANSACTION;

INSERT INTO `fields` VALUES (NULL, 'sc_delete_confirmation', 'backend', 'Grid / Delete confirmation', 'script', '2014-12-15 08:39:26');

SET @id := (SELECT LAST_INSERT_ID());

INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '1', 'title', '[PRODUCT] has been purchased [X] times. Deleting the product will also remove it from all these orders. Are you sure you want to delete it or make it inactive?', 'script');
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '2', 'title', '[PRODUCT] has been purchased [X] times. Deleting the product will also remove it from all these orders. Are you sure you want to delete it or make it inactive?', 'script');
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '3', 'title', '[PRODUCT] has been purchased [X] times. Deleting the product will also remove it from all these orders. Are you sure you want to delete it or make it inactive?', 'script');

INSERT INTO `fields` VALUES (NULL, 'delete_product_confirmation', 'backend', 'Label / Are you sure you want to delete the product?', 'script', '2014-12-15 08:52:47');

SET @id := (SELECT LAST_INSERT_ID());

INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '1', 'title', 'Are you sure you want to delete the product?', 'script');
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '2', 'title', 'Are you sure you want to delete the product?', 'script');
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '3', 'title', 'Are you sure you want to delete the product?', 'script');

SET @id := (SELECT `id` FROM `fields` WHERE `key` = "delete_confirmation");

UPDATE `multi_lang` SET `content` = 'Are you sure that you want to delete selected record(s)?' WHERE `foreign_id` = @id AND `model` = "pjField" AND `field` = "title";

COMMIT;