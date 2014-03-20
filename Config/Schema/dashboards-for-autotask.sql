/*
SQL file to setup a brand new Dashboards for Autotask installation.
Built for version 1.5.2.
*/

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
) ENGINE=InnoDB DEFAULT CHARSET=latin1 ROW_FORMAT=COMPACT;

-- ----------------------------
--  Table structure for `dashboardqueues`
-- ----------------------------
DROP TABLE IF EXISTS `dashboardqueues`;
CREATE TABLE `dashboardqueues` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `dashboard_id` int(10) NOT NULL,
  `queue_id` int(10) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `dashboard_x_queue` (`dashboard_id`,`queue_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 ROW_FORMAT=COMPACT;

-- ----------------------------
--  Table structure for `dashboardresources`
-- ----------------------------
DROP TABLE IF EXISTS `dashboardresources`;
CREATE TABLE `dashboardresources` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `dashboard_id` int(10) NOT NULL,
  `resource_id` int(10) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `dashboard_x_resource` (`dashboard_id`,`resource_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 ROW_FORMAT=COMPACT;

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
  `show_tickets_top_x` tinyint(1) NOT NULL DEFAULT '0',
  `show_clock` tinyint(1) NOT NULL DEFAULT '0',
  `show_open_tickets` tinyint(1) NOT NULL DEFAULT '0',
  `show_tickets_by_source` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 ROW_FORMAT=COMPACT;

-- ----------------------------
--  Table structure for `dashboardticketstatuses`
-- ----------------------------
DROP TABLE IF EXISTS `dashboardticketstatuses`;
CREATE TABLE `dashboardticketstatuses` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `dashboard_id` int(10) NOT NULL,
  `ticketstatus_id` int(10) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `dashboard_x_ticketstatus` (`dashboard_id`,`ticketstatus_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 ROW_FORMAT=COMPACT;

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
  PRIMARY KEY (`id`),
  KEY `dashboard_x_widget` (`dashboard_id`,`widget_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;

-- ----------------------------
--  Table structure for `dashboardwidgetsettings`
-- ----------------------------
DROP TABLE IF EXISTS `dashboardwidgetsettings`;
CREATE TABLE `dashboardwidgetsettings` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `dashboardwidget_id` int(10) NOT NULL,
  `name` varchar(255) NOT NULL,
  `value` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `dashboardwidget_id` (`dashboardwidget_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;

-- ----------------------------
--  Table structure for `issuetypes`
-- ----------------------------
DROP TABLE IF EXISTS `issuetypes`;
CREATE TABLE `issuetypes` (
  `id` int(10) NOT NULL,
  `name` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 ROW_FORMAT=COMPACT;

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
  PRIMARY KEY (`id`),
  KEY `dashboard_id` (`dashboard_id`)
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
  PRIMARY KEY (`id`),
  KEY `dashboard_id` (`dashboard_id`),
  KEY `queue_id` (`queue_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;

-- ----------------------------
--  Table structure for `queues`
-- ----------------------------
DROP TABLE IF EXISTS `queues`;
CREATE TABLE `queues` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 ROW_FORMAT=COMPACT;

-- ----------------------------
--  Table structure for `resources`
-- ----------------------------
DROP TABLE IF EXISTS `resources`;
CREATE TABLE `resources` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 ROW_FORMAT=COMPACT;

-- ----------------------------
--  Table structure for `settings`
-- ----------------------------
DROP TABLE IF EXISTS `settings`;
CREATE TABLE `settings` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `app_title` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=latin1 ROW_FORMAT=COMPACT;

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
) ENGINE=InnoDB DEFAULT CHARSET=latin1 ROW_FORMAT=COMPACT;

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
  `ticketsource_id` int(10) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `ticketstatus_id` (`ticketstatus_id`),
  KEY `queue_id` (`queue_id`),
  KEY `ticketsource_id` (`ticketsource_id`),
  KEY `resource_id` (`resource_id`),
  KEY `account_id` (`account_id`),
  KEY `issuetype_id` (`issuetype_id`),
  KEY `subissuetype_id` (`subissuetype_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 ROW_FORMAT=COMPACT;

-- ----------------------------
--  Table structure for `ticketsourcecounts`
-- ----------------------------
DROP TABLE IF EXISTS `ticketsourcecounts`;
CREATE TABLE `ticketsourcecounts` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `created` date NOT NULL,
  `ticketsource_id` int(10) NOT NULL,
  `count` int(10) NOT NULL,
  `queue_id` int(10) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `ticketsource_id` (`ticketsource_id`),
  KEY `queue_id` (`queue_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci ROW_FORMAT=COMPACT;

-- ----------------------------
--  Table structure for `ticketsources`
-- ----------------------------
DROP TABLE IF EXISTS `ticketsources`;
CREATE TABLE `ticketsources` (
  `id` int(10) NOT NULL,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci ROW_FORMAT=COMPACT;

-- ----------------------------
--  Table structure for `ticketstatuscounts`
-- ----------------------------
DROP TABLE IF EXISTS `ticketstatuscounts`;
CREATE TABLE `ticketstatuscounts` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `created` date NOT NULL,
  `ticketstatus_id` int(10) NOT NULL,
  `count` int(10) NOT NULL,
  `queue_id` int(10) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `queue_id` (`queue_id`),
  KEY `ticketstatus_id` (`ticketstatus_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 ROW_FORMAT=COMPACT;

-- ----------------------------
--  Table structure for `ticketstatuses`
-- ----------------------------
DROP TABLE IF EXISTS `ticketstatuses`;
CREATE TABLE `ticketstatuses` (
  `id` int(10) NOT NULL,
  `name` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 ROW_FORMAT=COMPACT;

-- ----------------------------
--  Table structure for `timeentries`
-- ----------------------------
DROP TABLE IF EXISTS `timeentries`;
CREATE TABLE `timeentries` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `created` datetime NOT NULL,
  `resource_id` int(10) NOT NULL,
  `ticket_id` int(10) NOT NULL,
  `hours_to_bill` double(10,2) NOT NULL,
  `hours_worked` double(10,2) NOT NULL,
  `non_billable` tinyint(1) NOT NULL,
  `offset_hours` double(10,2) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `resource_id` (`resource_id`),
  KEY `ticket_id` (`ticket_id`)
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
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;

-- ----------------------------
--  Records of `widgets`
-- ----------------------------
BEGIN;
INSERT INTO `widgets` VALUES ('1', 'Kill Rate', '3', '1', 'Widgets/kill_rate'), ('2', 'New vs. Closed', '3', '1', 'Widgets/kill_rate_graph'), ('3', 'Average Days Open', '3', '1', 'Widgets/queue_health_graph'), ('4', 'Account Top X', '2', '2', 'Widgets/accounts'), ('5', 'Queue Health', '2', '0', 'Widgets/queues'), ('6', 'Resources', '2', '0', 'Widgets/resources'), ('7', 'Ticket Status', '1', '1', 'Widgets/ticketstatus'), ('8', 'New vs. Closed', '3', '1', 'Widgets/kill_rate_bars'), ('9', 'Latest tickets', '3', '2', 'Widgets/tickets_top_x'), ('10', 'Clock', '1', '1', 'Widgets/clock'), ('11', 'Open', '1', '1', 'Widgets/opentickets'), ('12', 'Tickets by source', '3', '2', 'Widgets/tickets_by_source');
COMMIT;

SET FOREIGN_KEY_CHECKS = 1;
