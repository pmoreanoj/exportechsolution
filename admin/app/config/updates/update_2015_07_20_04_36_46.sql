
START TRANSACTION;

INSERT INTO `fields` VALUES (NULL, 'front_field_required', 'frontend', 'Field ', 'script', '2015-07-20 04:36:29');

SET @id := (SELECT LAST_INSERT_ID());

INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '1', 'title', 'Field is required.', 'script');
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '2', 'title', 'Field is required.', 'script');
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '3', 'title', 'Field is required.', 'script');

COMMIT;