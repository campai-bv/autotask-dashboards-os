SET NAMES utf8;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
--  Table structure for `killratecounts`
-- ----------------------------
CREATE TABLE `killratecounts` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `created` date NOT NULL,
  `new_count` int(10) NOT NULL,
  `completed_count` int(10) NOT NULL,
  `dashboard_id` int(10) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- ----------------------------
--  Table structure for `queuehealthcounts`
-- ----------------------------
CREATE TABLE `queuehealthcounts` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `created` date NOT NULL,
  `dashboard_id` int(10) NOT NULL,
  `queue_id` int(10) NOT NULL,
  `average_days_open` int(10) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8;

-- ----------------------------
--  Table structure for `dashboardwidgets`
-- ----------------------------
CREATE TABLE `dashboardwidgets` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `created` datetime NOT NULL,
  `modified` datetime NOT NULL,
  `dashboard_id` int(10) NOT NULL,
  `widget_id` int(10) NOT NULL,
  `display_name` varchar(255) NOT NULL,
  `ticketstatus_id` int(10) NOT NULL,
  `type` varchar(255) NOT NULL,
  `col` int(1) NOT NULL,
  `row` int(2) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8;

-- ----------------------------
--  Table structure for `widgets`
-- ----------------------------
DROP TABLE IF EXISTS `widgets`;
CREATE TABLE `widgets` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `default_name` varchar(255) NOT NULL,
  `data_sizex` int(10) NOT NULL,
  `data_sizey` int(10) NOT NULL,
  `element` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;

-- ----------------------------
--  Records of `widgets`
-- ----------------------------
BEGIN;
INSERT INTO `widgets` VALUES ('1', 'Kill Rate', '3', '1', 'Widgets/kill_rate'), ('2', 'New vs. Closed', '3', '1', 'Widgets/kill_rate_graph'), ('3', 'Average Days Open', '3', '1', 'Widgets/queue_health_graph'), ('4', 'Account Top X', '2', '2', 'Widgets/accounts'), ('5', 'Queue Health', '2', '0', 'Widgets/queues'), ('6', 'Resources', '2', '0', 'Widgets/resources'), ('7', 'Ticket Status', '1', '1', 'Widgets/ticketstatus'), ('8', 'New vs. Closed', '3', '1', 'Widgets/kill_rate_bars');
COMMIT;

-- ----------------------------
--  Table structure for `dashboards`
-- ----------------------------
ALTER TABLE `dashboards` ADD `show_rolling_week` tinyint(1) NOT NULL DEFAULT '1';
ALTER TABLE `dashboards` ADD `show_rolling_week_bars` tinyint(1) NOT NULL DEFAULT '1';
ALTER TABLE `dashboards` ADD `show_queue_health` tinyint(1) NOT NULL DEFAULT '1';
ALTER TABLE `dashboards` ADD `show_sla_violations` tinyint(1) NOT NULL DEFAULT '1';

-- ----------------------------
--  Table structure for `tickets`
-- ----------------------------
ALTER TABLE `tickets` ADD `due` datetime NOT NULL;
ALTER TABLE `tickets` ADD `priority` int(2) NOT NULL;
ALTER TABLE `tickets` ADD `has_met_sla` tinyint(1) NOT NULL DEFAULT '0';

SET FOREIGN_KEY_CHECKS = 1;