
START TRANSACTION;

UPDATE `options` SET `value` = '1|2|3::1' WHERE `key` = 'o_layout';
UPDATE `options` SET `label` = 'Layout 1|Layout 2|Bootstrap template' WHERE `key` = 'o_layout';

COMMIT;