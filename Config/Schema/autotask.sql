SET NAMES utf8;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
--  Table structure for `accounts`
-- ----------------------------
DROP TABLE IF EXISTS `accounts`;
CREATE TABLE `accounts` (
  `id` int(10) NOT NULL,
  `name` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;

-- ----------------------------
--  Table structure for `dashboardqueues`
-- ----------------------------
DROP TABLE IF EXISTS `dashboardqueues`;
CREATE TABLE `dashboardqueues` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `dashboard_id` int(10) NOT NULL,
  `queue_id` int(10) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;

-- ----------------------------
--  Table structure for `dashboardresources`
-- ----------------------------
DROP TABLE IF EXISTS `dashboardresources`;
CREATE TABLE `dashboardresources` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `dashboard_id` int(10) NOT NULL,
  `resource_id` int(10) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;

-- ----------------------------
--  Table structure for `dashboards`
-- ----------------------------
DROP TABLE IF EXISTS `dashboards`;
CREATE TABLE `dashboards` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `show_kill_rate` tinyint(1) NOT NULL DEFAULT '1',
  `show_accounts` tinyint(1) NOT NULL DEFAULT '1',
  `show_queues` tinyint(1) NOT NULL DEFAULT '1',
  `show_resources` tinyint(1) NOT NULL DEFAULT '1',
  `show_unassigned` tinyint(1) NOT NULL DEFAULT '1',
  `show_missing_issue_type` tinyint(1) NOT NULL DEFAULT '1',
  `show_rolling_week` tinyint(1) NOT NULL DEFAULT '1',
  `show_rolling_week_bars` tinyint(1) NOT NULL,
  `show_queue_health` tinyint(1) NOT NULL DEFAULT '1',
  `show_sla_violations` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;

-- ----------------------------
--  Table structure for `dashboardticketstatuses`
-- ----------------------------
DROP TABLE IF EXISTS `dashboardticketstatuses`;
CREATE TABLE `dashboardticketstatuses` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `dashboard_id` int(10) NOT NULL,
  `ticketstatus_id` int(10) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;

-- ----------------------------
--  Table structure for `dashboardwidgets`
-- ----------------------------
DROP TABLE IF EXISTS `dashboardwidgets`;
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;

-- ----------------------------
--  Table structure for `issuetypes`
-- ----------------------------
DROP TABLE IF EXISTS `issuetypes`;
CREATE TABLE `issuetypes` (
  `id` int(10) NOT NULL,
  `name` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;

-- ----------------------------
--  Table structure for `killratecounts`
-- ----------------------------
DROP TABLE IF EXISTS `killratecounts`;
CREATE TABLE `killratecounts` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `created` date NOT NULL,
  `new_count` int(10) NOT NULL,
  `completed_count` int(10) NOT NULL,
  `dashboard_id` int(10) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci ROW_FORMAT=COMPACT;

-- ----------------------------
--  Table structure for `queuehealthcounts`
-- ----------------------------
DROP TABLE IF EXISTS `queuehealthcounts`;
CREATE TABLE `queuehealthcounts` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `created` date NOT NULL,
  `dashboard_id` int(10) NOT NULL,
  `queue_id` int(10) NOT NULL,
  `average_days_open` int(10) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;

-- ----------------------------
--  Table structure for `queues`
-- ----------------------------
DROP TABLE IF EXISTS `queues`;
CREATE TABLE `queues` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;

-- ----------------------------
--  Table structure for `resources`
-- ----------------------------
DROP TABLE IF EXISTS `resources`;
CREATE TABLE `resources` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;

-- ----------------------------
--  Table structure for `settings`
-- ----------------------------
DROP TABLE IF EXISTS `settings`;
CREATE TABLE `settings` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `app_title` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;

-- ----------------------------
--  Records of `settings`
-- ----------------------------
BEGIN;
INSERT INTO `settings` VALUES ('1', 'Autotask Dashboards');
COMMIT;

-- ----------------------------
--  Table structure for `subissuetypes`
-- ----------------------------
DROP TABLE IF EXISTS `subissuetypes`;
CREATE TABLE `subissuetypes` (
  `id` int(10) NOT NULL,
  `name` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;

-- ----------------------------
--  Table structure for `tickets`
-- ----------------------------
DROP TABLE IF EXISTS `tickets`;
CREATE TABLE `tickets` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `created` datetime NOT NULL,
  `number` varchar(255) NOT NULL,
  `title` varchar(255) NOT NULL,
  `ticketstatus_id` int(10) NOT NULL,
  `queue_id` int(10) NOT NULL,
  `resource_id` int(10) NOT NULL,
  `completed` datetime DEFAULT NULL,
  `account_id` int(10) DEFAULT NULL,
  `issuetype_id` int(10) DEFAULT NULL,
  `subissuetype_id` int(10) DEFAULT NULL,
  `due` datetime NOT NULL,
  `priority` int(2) NOT NULL,
  `has_met_sla` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;

-- ----------------------------
--  Table structure for `ticketstatuscounts`
-- ----------------------------
DROP TABLE IF EXISTS `ticketstatuscounts`;
CREATE TABLE `ticketstatuscounts` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `created` date NOT NULL,
  `ticketstatus_id` int(10) NOT NULL,
  `count` int(10) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;

-- ----------------------------
--  Table structure for `ticketstatuses`
-- ----------------------------
DROP TABLE IF EXISTS `ticketstatuses`;
CREATE TABLE `ticketstatuses` (
  `id` int(10) NOT NULL,
  `name` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;

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

SET FOREIGN_KEY_CHECKS = 1;
