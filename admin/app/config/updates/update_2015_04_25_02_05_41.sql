
START TRANSACTION;

INSERT INTO `fields` VALUES (NULL, 'lblAddCategoryText', 'backend', 'Label / No categories found. Add category here', 'script', NULL);

SET @id := (SELECT LAST_INSERT_ID());

INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '1', 'title', 'No categories found. Add category {STAG}here{ETAG}.', 'script');
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '2', 'title', 'No categories found. Add category {STAG}here{ETAG}.', 'script');
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '3', 'title', 'No categories found. Add category {STAG}here{ETAG}.', 'script');

INSERT INTO `fields` VALUES (NULL, 'infoProductsTitle', 'backend', 'Infobox / List of products', 'script', NULL);

SET @id := (SELECT LAST_INSERT_ID());

INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '1', 'title', 'List of products', 'script');
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '2', 'title', 'List of products', 'script');
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '3', 'title', 'List of products', 'script');

INSERT INTO `fields` VALUES (NULL, 'infoProductsDesc', 'backend', 'Infobox / List of products', 'script', NULL);

SET @id := (SELECT LAST_INSERT_ID());

INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '1', 'title', 'You can find below the list of products defined. Click on the pencil icon on the corresponding entry to view more details of a specific product. You can also click on the button "+ Add product" to add new product.', 'script');
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '2', 'title', 'You can find below the list of products defined. Click on the pencil icon on the corresponding entry to view more details of a specific product. You can also click on the button "+ Add product" to add new product.', 'script');
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '3', 'title', 'You can find below the list of products defined. Click on the pencil icon on the corresponding entry to view more details of a specific product. You can also click on the button "+ Add product" to add new product.', 'script');

SET @id := (SELECT `id` FROM `fields` WHERE `key` = "product_details_add_body");
UPDATE `multi_lang` SET `content` = 'Enter product details below. You can assign a product to one or more categories (change categories by clicking on Categories tab) and to make it featured.' WHERE `foreign_id` = @id AND `model` = "pjField" AND `field` = "title";

COMMIT;