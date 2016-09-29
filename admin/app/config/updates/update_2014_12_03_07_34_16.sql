
START TRANSACTION;

INSERT INTO `fields` VALUES (NULL, 'front_send_content', 'frontend', 'Label / Send to friend content', 'script', '2014-12-03 07:34:01');

SET @id := (SELECT LAST_INSERT_ID());

INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '1', 'title', 'Dear %1$s,\r\n			\r\nYour friend %3$s thinks this may be interested you:\r\n%5$s', 'script');
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '2', 'title', 'Dear %1$s,\r\n			\r\nYour friend %3$s thinks this may be interested you:\r\n%5$s', 'script');
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '3', 'title', 'Dear %1$s,\r\n			\r\nYour friend %3$s thinks this may be interested you:\r\n%5$s', 'script');

COMMIT;