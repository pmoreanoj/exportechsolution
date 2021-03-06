
START TRANSACTION;

INSERT INTO `fields` VALUES (NULL, 'plugin_backup_datetime', 'backend', 'Label / Date/time', 'plugin', '2015-11-17 09:38:32');

SET @id := (SELECT LAST_INSERT_ID());

INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '1', 'title', 'Date/time', 'plugin');
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '2', 'title', 'Date/time', 'plugin');
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '3', 'title', 'Date/time', 'plugin');

INSERT INTO `fields` VALUES (NULL, 'plugin_backup_type', 'backend', 'Label / Type', 'plugin', '2015-11-17 09:38:57');

SET @id := (SELECT LAST_INSERT_ID());

INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '1', 'title', 'Type', 'plugin');
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '2', 'title', 'Type', 'plugin');
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '3', 'title', 'Type', 'plugin');

INSERT INTO `fields` VALUES (NULL, 'plugin_backup_file', 'backend', 'Label / File', 'plugin', '2015-11-17 09:39:15');

SET @id := (SELECT LAST_INSERT_ID());

INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '1', 'title', 'File', 'plugin');
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '2', 'title', 'File', 'plugin');
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '3', 'title', 'File', 'plugin');

INSERT INTO `fields` VALUES (NULL, 'plugin_backup_delete_confirmation', 'backend', 'Backup plugin / Delete confirmation', 'plugin', '2015-11-17 09:40:22');

SET @id := (SELECT LAST_INSERT_ID());

INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '1', 'title', 'Are you sure you want to delete selected file?', 'plugin');
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '2', 'title', 'Are you sure you want to delete selected file?', 'plugin');
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '3', 'title', 'Are you sure you want to delete selected file?', 'plugin');

INSERT INTO `fields` VALUES (NULL, 'plugin_backup_delete_selected', 'backend', 'Backup plugin / Delete selected', 'plugin', '2015-11-17 09:41:00');

SET @id := (SELECT LAST_INSERT_ID());

INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '1', 'title', 'Delete selected', 'plugin');
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '2', 'title', 'Delete selected', 'plugin');
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '3', 'title', 'Delete selected', 'plugin');

COMMIT;