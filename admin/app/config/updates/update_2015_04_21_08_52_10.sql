
START TRANSACTION;

INSERT INTO `fields` VALUES (NULL, 'titles_ARRAY_AP11', 'arrays', 'titles_ARRAY_AP11', 'script', '2015-04-21 08:29:52');

SET @id := (SELECT LAST_INSERT_ID());

INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '1', 'title', 'File not existing', 'script');
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '2', 'title', 'File not existing', 'script');
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '3', 'title', 'File not existing', 'script');

INSERT INTO `fields` VALUES (NULL, 'errors_ARRAY_AP11', 'arrays', 'errors_ARRAY_AP11', 'script', '2015-04-21 08:30:42');

SET @id := (SELECT LAST_INSERT_ID());

INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '1', 'title', 'The product has been updated, but file given from the file path does not exist.', 'script');
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '2', 'title', 'The product has been updated, but file given from the file path does not exist.', 'script');
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '3', 'title', 'The product has been updated, but file given from the file path does not exist.', 'script');

COMMIT;