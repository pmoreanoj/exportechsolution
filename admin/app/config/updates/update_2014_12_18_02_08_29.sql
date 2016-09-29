
START TRANSACTION;

INSERT INTO `fields` VALUES (NULL, 'lblStoreName', 'frontend', 'Label / My Store', 'script', NULL);

SET @id := (SELECT LAST_INSERT_ID());

INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '1', 'title', 'My Store', 'script');
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '2', 'title', 'My Store', 'script');
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '3', 'title', 'My Store', 'script');

SET @id := (SELECT `id` FROM `fields` WHERE `key` = "error_bodies_ARRAY_AO25");

UPDATE `multi_lang` SET `content` = '<p>Email notifications will be sent to people who make an order after checkout form is completed or/and payment is made. Different messages are sent to the administrator. Using tokens you can customize all the messages.</p><br />\r\n<table width="100%" border="0" cellspacing="0" cellpadding="0">\r\n  <tr>\r\n    <td width="50%" valign="top"><p>{ClientName} - customer''s name<br />\r\n {ClientEmail} - customer''s e-mail<br />\r\n{ClientPassword} - customer''s password<br />\r\n{ClientPhone} -  customer''s phone number<br />\r\n{ClientURL} - customer''s website<br />\r\n{BillingName} - customer''s billing name<br />\r\n {BillingAddress1} - billing address 1<br />\r\n{BillingAddress2} - billing address 2<br />\r\n{BillingCity} - billing city<br />\r\n{BillingState} - billing state<br />\r\n{BillingZip} - billing zip code<br />\r\n{BillingCountry} - billing country<br />\r\n{ShippingName} - customer''s shipping name<br />\r\n{ShippingAddress1} - shipping address 1<br />\r\n{ShippingAddress2} - shipping address 2<br />\r\n{ShippingCity} - shipping city<br />\r\n{ShippingState} - shipping state<br />\r\n{ShippingZip} - shipping zip code; <br />\r\n    {ShippingCountry} - shipping country</p></td>\r\n    <td width="50%" valign="top"> {Notes} - additional notes<br />\r\n{CCType} - CC type<br />\r\n{CCNum} - CC number<br />\r\n{CCExpMonth} - CC exp.month<br />\r\n{CCExpYear} - CC exp.year<br />\r\n{CCSec} - CC sec. code<br />\r\n{PaymentMethod} - selected payment method<br />\r\n{Insurance} -  insurance fee<br />\r\n{Shipping} - shipping fee<br />\r\n{Tax} - tax fee<br />\r\n{Price} - price<br />\r\n{Total} - total amount<br />\r\n{Discount} - discount<br />\r\n{Voucher} - promo code<br />\r\n{Products} - list with purchased products<br />\r\n{OrderUUID} - Order number<br />\r\n{DigitalDownload} - Digital products download link<br />\r\n{StoreName} - Store name<br/>\r\n{FriendName} - your friend''s name<br/>\r\n{FriendEmail} - your friend''s email<br/>\r\n{YourName} - your name<br/>\r\n{YourEmail} - your email<br/>\r\n{URL} - url to product detail\r\n</td>\r\n  </tr>\r\n</table>\r\n<p> </p>' WHERE `foreign_id` = @id AND `model` = "pjField" AND `field` = "title";

UPDATE `multi_lang` SET `content` = 'Dear {ClientName},\r\n\r\nThank you for your order. Your order number is: {OrderUUID}\r\n\r\nPurchased products:\r\n{Products}\r\n\r\nPlease, complete your payment and another confirmation email will be sent.\r\n\r\nRegards,\r\n{StoreName}' WHERE `foreign_id` = 1 AND `model` = "pjOption" AND `field` = "confirm_tokens_client";

UPDATE `multi_lang` SET `content` = 'Dear {ClientName},\r\n\r\nThank you for your payment. \r\n\r\nAmount: {Price}\r\n\r\nWe will ship the products to you in next 24 - 48 hours.\r\n\r\nRegards,\r\n{StoreName}' WHERE `foreign_id` = 1 AND `model` = "pjOption" AND `field` = "payment_tokens_client";

UPDATE `multi_lang` SET `content` = 'Dear {Name},\r\n\r\nThank you for registering with us!\r\n\r\nRegards,\r\n{StoreName}' WHERE `foreign_id` = 1 AND `model` = "pjOption" AND `field` = "register_tokens";

UPDATE `multi_lang` SET `content` = 'Dear {Name},\r\n\r\nYour password is: {Password}\r\n\r\nRegards,\r\n{StoreName}' WHERE `foreign_id` = 1 AND `model` = "pjOption" AND `field` = "forgot_tokens";

COMMIT;