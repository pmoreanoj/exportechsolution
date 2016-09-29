
START TRANSACTION;

ALTER TABLE `attributes` ADD `order_group` int(10) DEFAULT NULL AFTER `parent_id`;
ALTER TABLE `attributes` ADD `order_item` int(10) DEFAULT NULL AFTER `order_group`;

COMMIT;