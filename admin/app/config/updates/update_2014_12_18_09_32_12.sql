
START TRANSACTION;

INSERT INTO `fields` VALUES (NULL, 'lblInstallJs2_1', 'backend', 'Label / Step 2.1 (Optional)', 'script', '2014-12-18 09:07:43');

SET @id := (SELECT LAST_INSERT_ID());

INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '1', 'title', 'Step 2.1 (Optional) Add the following code at the very top of your web page', 'script');
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '2', 'title', 'Step 2.1 (Optional) Add the following code at the very top of your web page', 'script');
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '3', 'title', 'Step 2.1 (Optional) Add the following code at the very top of your web page', 'script');

INSERT INTO `fields` VALUES (NULL, 'lblInstallJs2_2', 'backend', 'Label / Step 2.2 (Optional)', 'script', '2014-12-18 09:08:12');

SET @id := (SELECT LAST_INSERT_ID());

INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '1', 'title', 'Step 2.2 (Optional) Add the following code in the head tag of your web page for UTF and mobile view support', 'script');
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '2', 'title', 'Step 2.2 (Optional) Add the following code in the head tag of your web page for UTF and mobile view support', 'script');
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '3', 'title', 'Step 2.2 (Optional) Add the following code in the head tag of your web page for UTF and mobile view support', 'script');

COMMIT;