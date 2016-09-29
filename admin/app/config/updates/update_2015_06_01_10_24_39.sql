
START TRANSACTION;

INSERT INTO `fields` VALUES (NULL, 'front_maximum_items', 'frontend', 'Label / Maximum Items', 'script', '2015-06-01 10:17:07');

SET @id := (SELECT LAST_INSERT_ID());

INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '1', 'title', 'Maximum {MAX} items can be bought for this product.', 'script');
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '2', 'title', 'Maximum {MAX} items can be bought for this product.', 'script');
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '3', 'title', 'Maximum {MAX} items can be bought for this product.', 'script');

COMMIT;