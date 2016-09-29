
START TRANSACTION;

SET @id := (SELECT `id` FROM `fields` WHERE `key` = "multilangTooltip");
UPDATE `multi_lang` SET `content` = 'Click on the flag icon to choose which language version of the content you wish to edit.' WHERE `foreign_id` = @id AND `model` = "pjField" AND `field` = "title";

UPDATE `multi_lang` SET `content` = 'Dear {ClientName},<br /><br />thank you for your order. Your order number is: {OrderUUID}<br /><br />Purchased products:<br />{Products}<br /><br />Please, complete your payment and another confirmation email will be sent.<br /><br />Regards,<br />Shop Name' WHERE `foreign_id` = 1 AND `model` = "pjOption" AND `field` = "confirm_tokens_client";

UPDATE `multi_lang` SET `content` = 'Dear {ClientName},<br /><br />thank you for your payment. <br /><br />Amount: {Price}<br /><br />We will ship the products to you in next 24 - 48 hours.<br /><br />Regards,<br />Shop Name' WHERE `foreign_id` = 1 AND `model` = "pjOption" AND `field` = "payment_tokens_client";

UPDATE `multi_lang` SET `content` = 'New order has been made. <br /><br />Order number: {OrderUUID}<br /><br />Purchased products:<br />{Products}<br /><br />Order details<br /><br />{ClientName} - customer''s name<br />{ClientEmail} - customer''s e-mail<br />{ClientPassword} - customer''s password<br />{ClientPhone} - customer''s phone number<br />{ClientURL} - customer''s website<br />{BillingName} - customer''s billing name<br />{BillingAddress1} - billing address 1<br />{BillingAddress2} - billing address 2<br />{BillingCity} - billing city<br />{BillingState} - billing state<br />{BillingZip} - billing zip code<br />{BillingCountry} - billing country<br />{ShippingName} - customer''s shipping name<br />{ShippingAddress1} - shipping address 1<br />{ShippingAddress2} - shipping address 2<br />{ShippingCity} - shipping city<br />{ShippingState} - shipping state<br />{ShippingZip} - shipping zip code; <br />{ShippingCountry} - shipping country<br />{Notes} - additional notes<br />{CCType} - CC type<br />{CCNum} - CC number<br />{CCExpMonth} - CC exp.month<br />{CCExpYear} - CC exp.year<br />{CCSec} - CC sec. code<br />{PaymentMethod} - selected payment method<br />{Insurance} - insurance fee<br />{Shipping} - shipping fee<br />{Tax} - tax fee<br />{Total} - total amount<br />{Price} - price<br />{Discount} - discount<br />{Voucher} - promo code' WHERE `foreign_id` = 1 AND `model` = "pjOption" AND `field` = "confirm_tokens_admin";

UPDATE `multi_lang` SET `content` = 'Payment for Order {OrderUUID} has been made.<br /><br />You can ship the products.<br /><br />{Products}' WHERE `foreign_id` = 1 AND `model` = "pjOption" AND `field` = "payment_tokens_admin";

UPDATE `multi_lang` SET `content` = 'Dear {Name},<br /><br />Thank you for registering with us!<br /><br />Regards,<br />Shop Name' WHERE `foreign_id` = 1 AND `model` = "pjOption" AND `field` = "register_tokens";

UPDATE `multi_lang` SET `content` = 'Dear {Name},<br /><br />Your password is: {Password}<br /><br />Regards,<br />Shop Name' WHERE `foreign_id` = 1 AND `model` = "pjOption" AND `field` = "forgot_tokens";

UPDATE `multi_lang` SET `content` = 'Dear {FriendName},<br /><br />Your friend {YourName} thinks this may be interested you:<br />{URL}' WHERE `foreign_id` = 1 AND `model` = "pjOption" AND `field` = "send_to_tokens";

SET @id := (SELECT `id` FROM `fields` WHERE `key` = "product_is_digital");
UPDATE `multi_lang` SET `content` = 'This is a digital product' WHERE `foreign_id` = @id AND `model` = "pjField" AND `field` = "title";

INSERT INTO `fields` VALUES (NULL, 'btnPlusAddProduct', 'backend', 'Button / + Add product', 'script', NULL);

SET @id := (SELECT LAST_INSERT_ID());

INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '1', 'title', '+ Add product', 'script');
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '2', 'title', '+ Add product', 'script');
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '3', 'title', '+ Add product', 'script');

INSERT INTO `fields` VALUES (NULL, 'btnAddCategory', 'backend', 'Button / + Add category', 'script', NULL);

SET @id := (SELECT LAST_INSERT_ID());

INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '1', 'title', '+ Add category', 'script');
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '2', 'title', '+ Add category', 'script');
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '3', 'title', '+ Add category', 'script');

INSERT INTO `fields` VALUES (NULL, 'lblEmailNotifications', 'backend', 'Label / Email notifications', 'script', NULL);

SET @id := (SELECT LAST_INSERT_ID());

INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '1', 'title', 'Email notifications', 'script');
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '2', 'title', 'Email notifications', 'script');
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '3', 'title', 'Email notifications', 'script');

INSERT INTO `fields` VALUES (NULL, 'btnPlusAddClient', 'backend', 'Button / + Add client', 'script', NULL);

SET @id := (SELECT LAST_INSERT_ID());

INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '1', 'title', '+ Add client', 'script');
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '2', 'title', '+ Add client', 'script');
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '3', 'title', '+ Add client', 'script');

INSERT INTO `fields` VALUES (NULL, 'infoClientsTitle', 'backend', 'Infobox / List of clients', 'script', NULL);

SET @id := (SELECT LAST_INSERT_ID());

INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '1', 'title', 'List of clients', 'script');
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '2', 'title', 'List of clients', 'script');
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '3', 'title', 'List of clients', 'script');

INSERT INTO `fields` VALUES (NULL, 'infoClientsDesc', 'backend', 'Infobox / List of clients', 'script', NULL);

SET @id := (SELECT LAST_INSERT_ID());

INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '1', 'title', 'You can find below the list of clients. Click on the Pencil icon on the corresponding entry to view client details.', 'script');
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '2', 'title', 'You can find below the list of clients. Click on the Pencil icon on the corresponding entry to view client details.', 'script');
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '3', 'title', 'You can find below the list of clients. Click on the Pencil icon on the corresponding entry to view client details.', 'script');

INSERT INTO `fields` VALUES (NULL, 'order_tab_invoices', 'backend', 'Tab / Invoices', 'script', NULL);

SET @id := (SELECT LAST_INSERT_ID());

INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '1', 'title', 'Invoices', 'script');
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '2', 'title', 'Invoices', 'script');
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '3', 'title', 'Invoices', 'script');

INSERT INTO `fields` VALUES (NULL, 'infoOrdersTitle', 'backend', 'Infobox / List of orders', 'script', NULL);

SET @id := (SELECT LAST_INSERT_ID());

INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '1', 'title', 'List of orders', 'script');
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '2', 'title', 'List of orders', 'script');
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '3', 'title', 'List of orders', 'script');

INSERT INTO `fields` VALUES (NULL, 'infoOrdersDesc', 'backend', 'Infobox / List of orders', 'script', NULL);

SET @id := (SELECT LAST_INSERT_ID());

INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '1', 'title', 'You can find below the list of orders made. Click on the pencil icon on the corresponding entry to view more details of a specific order.', 'script');
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '2', 'title', 'You can find below the list of orders made. Click on the pencil icon on the corresponding entry to view more details of a specific order.', 'script');
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '3', 'title', 'You can find below the list of orders made. Click on the pencil icon on the corresponding entry to view more details of a specific order.', 'script');

COMMIT;