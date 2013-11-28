SET NAMES utf8;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
--  Table structure for `dashboards`
-- ----------------------------
ALTER TABLE `dashboards` MODIFY `show_kill_rate` tinyint(2) NOT NULL DEFAULT '1';
ALTER TABLE `dashboards` MODIFY `show_accounts` tinyint(2) NOT NULL DEFAULT '1';
ALTER TABLE `dashboards` MODIFY `show_queues` tinyint(2) NOT NULL DEFAULT '1';
ALTER TABLE `dashboards` MODIFY `show_resources` tinyint(2) NOT NULL DEFAULT '1';
ALTER TABLE `dashboards` MODIFY `show_unassigned` tinyint(2) NOT NULL DEFAULT '1';
ALTER TABLE `dashboards` MODIFY `show_missing_issue_type` tinyint(2) NOT NULL DEFAULT '1';
ALTER TABLE `dashboards` MODIFY `show_rolling_week` tinyint(2) NOT NULL DEFAULT '1';
ALTER TABLE `dashboards` MODIFY `show_rolling_week_bars` tinyint(2) NOT NULL DEFAULT '0';
ALTER TABLE `dashboards` MODIFY `show_queue_health` tinyint(2) NOT NULL DEFAULT '0';
ALTER TABLE `dashboards` MODIFY `show_sla_violations` tinyint(2) NOT NULL DEFAULT '0';
ALTER TABLE `dashboards` MODIFY `show_tickets_top_x` tinyint(2) NOT NULL DEFAULT '0';
ALTER TABLE `dashboards` MODIFY `show_clock` tinyint(2) NOT NULL DEFAULT '0';
ALTER TABLE `dashboards` ADD COLUMN `show_open_tickets` tinyint(2) NOT NULL DEFAULT '0';
ALTER TABLE `dashboards` ADD COLUMN `show_tickets_by_source` tinyint(2) NOT NULL DEFAULT '0';

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
  `queue_id` int(10) NOT NULL,
  `count` int(10) NOT NULL,
  PRIMARY KEY (`id`)) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=0 ;

-- ----------------------------
--  Table structure for `ticketstatuscounts`
-- ----------------------------
ALTER TABLE `ticketstatuscounts` ADD COLUMN `queue_id` int(10) NOT NULL after `ticketstatus_id`;

-- ----------------------------
--  Records of `widgets`
-- ----------------------------
BEGIN;
INSERT INTO `widgets` VALUES ('11', 'Open', '1', '1', 'Widgets/opentickets'), ('12', 'Tickets by source', '3', '2', 'Widgets/tickets_by_source');
COMMIT;

SET FOREIGN_KEY_CHECKS = 1;