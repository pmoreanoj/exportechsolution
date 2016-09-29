
START TRANSACTION;

INSERT INTO `fields` VALUES (NULL, 'infoCreateClientTitle', 'backend', 'Infobox / Add new client', 'script', '2015-05-06 08:31:03');

SET @id := (SELECT LAST_INSERT_ID());

INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '1', 'title', 'Add new client', 'script');
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '2', 'title', 'Add new client', 'script');
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '3', 'title', 'Add new client', 'script');

INSERT INTO `fields` VALUES (NULL, 'infoCreateClientDesc', 'backend', 'Infobox / Add new client', 'script', '2015-05-06 08:32:55');

SET @id := (SELECT LAST_INSERT_ID());

INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '1', 'title', 'Please fill in the form below and click on "Save" button to add new client. There are two sections including "General info" and "Address book". For the "Address book", you can add as many as address books you want for each client.', 'script');
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '2', 'title', 'Please fill in the form below and click on "Save" button to add new client. There are two sections including "General info" and "Address book". For the "Address book", you can add as many as address books you want for each client.', 'script');
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '3', 'title', 'Please fill in the form below and click on "Save" button to add new client. There are two sections including "General info" and "Address book". For the "Address book", you can add as many as address books you want for each client.', 'script');

INSERT INTO `fields` VALUES (NULL, 'infoUpdateClientTitle', 'backend', 'Infobox / Update client', 'script', '2015-05-06 08:34:13');

SET @id := (SELECT LAST_INSERT_ID());

INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '1', 'title', 'Update client', 'script');
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '2', 'title', 'Update client', 'script');
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '3', 'title', 'Update client', 'script');

INSERT INTO `fields` VALUES (NULL, 'infoUpdateClientDesc', 'backend', 'Infobox / Update client', 'script', '2015-05-06 08:35:01');

SET @id := (SELECT LAST_INSERT_ID());

INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '1', 'title', 'You can make any change on the form below and click "Save" button to edit the information of client.', 'script');
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '2', 'title', 'You can make any change on the form below and click "Save" button to edit the information of client.', 'script');
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '3', 'title', 'You can make any change on the form below and click "Save" button to edit the information of client.', 'script');

INSERT INTO `fields` VALUES (NULL, 'infoVouchersTitle', 'backend', 'Infobox / List of vouchers', 'script', '2015-05-06 08:38:22');

SET @id := (SELECT LAST_INSERT_ID());

INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '1', 'title', 'List of vouchers', 'script');
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '2', 'title', 'List of vouchers', 'script');
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '3', 'title', 'List of vouchers', 'script');

INSERT INTO `fields` VALUES (NULL, 'infoVouchersDesc', 'backend', 'Infobox / List of vouchers', 'script', '2015-05-06 08:41:01');

SET @id := (SELECT LAST_INSERT_ID());

INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '1', 'title', 'You can find below the list of vouchers. In order to view more details of a specific voucher, click on the "Pencil" icon on the corresponding entry. If you want to add new voucher, click on the button "+ Add voucher".', 'script');
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '2', 'title', 'You can find below the list of vouchers. In order to view more details of a specific voucher, click on the "Pencil" icon on the corresponding entry. If you want to add new voucher, click on the button "+ Add voucher".', 'script');
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '3', 'title', 'You can find below the list of vouchers. In order to view more details of a specific voucher, click on the "Pencil" icon on the corresponding entry. If you want to add new voucher, click on the button "+ Add voucher".', 'script');

INSERT INTO `fields` VALUES (NULL, 'lblInstallSeo_4', 'backend', 'Label / Install SEO', 'script', '2015-05-06 09:17:11');

SET @id := (SELECT LAST_INSERT_ID());

INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '1', 'title', 'Step 4. (CROSS-DOMAIN INSTALL ONLY) Create .htaccess file (or update existing one) in the folder where your web page is and put the data below in it', 'script');
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '2', 'title', 'Step 4. (CROSS-DOMAIN INSTALL ONLY) Create .htaccess file (or update existing one) in the folder where your web page is and put the data below in it', 'script');
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '3', 'title', 'Step 4. (CROSS-DOMAIN INSTALL ONLY) Create .htaccess file (or update existing one) in the folder where your web page is and put the data below in it', 'script');

SET @id := (SELECT `id` FROM `fields` WHERE `key` = "lblInstallSeo_3");
UPDATE `multi_lang` SET `content` = 'Step 3. (SAME DOMAIN INSTALL ONLY) Create .htaccess file (or update existing one) in the folder where your web page is and put the data below in it' WHERE `foreign_id` = @id AND `model` = "pjField" AND `field` = "title";

COMMIT;