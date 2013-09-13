SET NAMES utf8;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
--  Table structure for `dashboards`
-- ----------------------------
ALTER TABLE `dashboards` ADD COLUMN `show_open_tickets` tinyint(1) NOT NULL DEFAULT '0';
ALTER TABLE `dashboards` ADD COLUMN `show_tickets_by_source` tinyint(1) NOT NULL DEFAULT '0';

-- ----------------------------
--  Table structure for `tickets`
-- ----------------------------
ALTER TABLE `tickets` ADD COLUMN `ticketsource_id` int(10) NOT NULL after `queue_id`;

-- ----------------------------
--  Table structure for `ticketsources`
-- ----------------------------
CREATE TABLE IF NOT EXISTS `ticketsources` (
  `id` int(10) NOT NULL,
  `name` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
  
-- ----------------------------
--  Table structure for `ticketsourcecounts`
-- ----------------------------
CREATE TABLE IF NOT EXISTS `ticketsourcecounts` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `created` date NOT NULL,
  `ticketsource_id` int(10) NOT NULL,
  `count` int(10) NOT NULL,
  PRIMARY KEY (`id`)) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=0 ;

-- ----------------------------
--  Records of `widgets`
-- ----------------------------
BEGIN;
INSERT INTO `widgets` VALUES ('11', 'Open', '1', '1', 'Widgets/opentickets'), ('12', 'Tickets by source', '3', '2', 'Widgets/tickets_by_source');
COMMIT;

SET FOREIGN_KEY_CHECKS = 1;