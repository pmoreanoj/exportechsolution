
START TRANSACTION;

SET @id := (SELECT `id` FROM `fields` WHERE `key` = "front_booking_status_ARRAY_1");
UPDATE `multi_lang` SET `content` = 'Thank you. Your order has been made.' WHERE `foreign_id` = @id AND `model` = "pjField" AND `field` = "title";

INSERT INTO `fields` VALUES (NULL, 'front_order_completed', 'frontend', 'Label / Order completed', 'script', '2015-05-06 02:19:33');

SET @id := (SELECT LAST_INSERT_ID());

INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '1', 'title', 'Order completed', 'script');
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '2', 'title', 'Order completed', 'script');
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '3', 'title', 'Order completed', 'script');

INSERT INTO `fields` VALUES (NULL, 'lblSummary', 'backend', 'Lable / Summary', 'script', '2015-05-06 02:46:11');

SET @id := (SELECT LAST_INSERT_ID());

INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '1', 'title', 'Summary', 'script');
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '2', 'title', 'Summary', 'script');
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '3', 'title', 'Summary', 'script');

INSERT INTO `fields` VALUES (NULL, 'lblNoExtrasFound', 'backend', 'Lable / No extras found.', 'script', '2015-05-06 02:58:50');

SET @id := (SELECT LAST_INSERT_ID());

INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '1', 'title', 'No extras found.', 'script');
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '2', 'title', 'No extras found.', 'script');
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '3', 'title', 'No extras found.', 'script');

INSERT INTO `fields` VALUES (NULL, 'lblNoAttributesFound', 'backend', 'Lable / No attributes found.', 'script', '2015-05-06 03:02:14');

SET @id := (SELECT LAST_INSERT_ID());

INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '1', 'title', 'No attributes found.', 'script');
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '2', 'title', 'No attributes found.', 'script');
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '3', 'title', 'No attributes found.', 'script');

INSERT INTO `fields` VALUES (NULL, 'lblProductsPrice', 'backend', 'Label / product(s) price', 'script', '2015-05-06 04:56:30');

SET @id := (SELECT LAST_INSERT_ID());

INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '1', 'title', 'product(s) price', 'script');
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '2', 'title', 'product(s) price', 'script');
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '3', 'title', 'product(s) price', 'script');

INSERT INTO `fields` VALUES (NULL, 'lblDashOrders', 'backend', 'Label / Order(s)', 'script', '2015-05-06 05:55:58');

SET @id := (SELECT LAST_INSERT_ID());

INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '1', 'title', 'Order(s)', 'script');
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '2', 'title', 'Order(s)', 'script');
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '3', 'title', 'Order(s)', 'script');

INSERT INTO `fields` VALUES (NULL, 'infoUsersTitle', 'backend', 'Infobox / List of users', 'script', '2015-05-06 06:06:29');

SET @id := (SELECT LAST_INSERT_ID());

INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '1', 'title', 'List of users', 'script');
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '2', 'title', 'List of users', 'script');
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '3', 'title', 'List of users', 'script');

INSERT INTO `fields` VALUES (NULL, 'infoUsersDesc', 'backend', 'Infobox / List of users', 'script', '2015-05-06 06:07:38');

SET @id := (SELECT LAST_INSERT_ID());

INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '1', 'title', 'You can find below the list of users. To view or edit user information, click on the Pencil icon on the corresponding entry.', 'script');
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '2', 'title', 'You can find below the list of users. To view or edit user information, click on the Pencil icon on the corresponding entry.', 'script');
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '3', 'title', 'You can find below the list of users. To view or edit user information, click on the Pencil icon on the corresponding entry.', 'script');

INSERT INTO `fields` VALUES (NULL, 'infoAddUserTitle', 'backend', 'Infobox / Add new user', 'script', '2015-05-06 06:09:04');

SET @id := (SELECT LAST_INSERT_ID());

INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '1', 'title', 'Add new user', 'script');
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '2', 'title', 'Add new user', 'script');
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '3', 'title', 'Add new user', 'script');

INSERT INTO `fields` VALUES (NULL, 'infoAddUserDesc', 'backend', 'Infobox / Add new user', 'script', '2015-05-06 06:09:57');

SET @id := (SELECT LAST_INSERT_ID());

INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '1', 'title', 'Fill in the form below and click "Save" button to add new user.', 'script');
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '2', 'title', 'Fill in the form below and click "Save" button to add new user.', 'script');
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '3', 'title', 'Fill in the form below and click "Save" button to add new user.', 'script');

INSERT INTO `fields` VALUES (NULL, 'infoUpdateUserTitle', 'backend', 'Infobox / Update user', 'script', '2015-05-06 06:12:39');

SET @id := (SELECT LAST_INSERT_ID());

INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '1', 'title', 'Update user', 'script');
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '2', 'title', 'Update user', 'script');
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '3', 'title', 'Update user', 'script');

INSERT INTO `fields` VALUES (NULL, 'infoUpdateUserDesc', 'backend', 'Infobox / Update user', 'script', '2015-05-06 06:13:19');

SET @id := (SELECT LAST_INSERT_ID());

INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '1', 'title', 'You can make any change on the form and click "Save" button to edit user information.', 'script');
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '2', 'title', 'You can make any change on the form and click "Save" button to edit user information.', 'script');
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '3', 'title', 'You can make any change on the form and click "Save" button to edit user information.', 'script');

COMMIT;