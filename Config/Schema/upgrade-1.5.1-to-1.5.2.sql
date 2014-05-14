SET NAMES utf8;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
--  Add a field to the dashboards table
-- ----------------------------
ALTER TABLE `dashboards` ADD `show_tickets_by_issuetype` tinyint(2) NOT NULL DEFAULT '0';

-- ----------------------------
--  Table structure for `issuetypecounts`
--  Used by the new widget "Tickets by Issue Type"
-- ----------------------------
DROP TABLE IF EXISTS `issuetypecounts`;
CREATE TABLE `issuetypecounts` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `created` date NOT NULL,
  `issuetype_id` int(10) NOT NULL,
  `queue_id` int(10) DEFAULT NULL,
  `count` int(10) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `issuetype_id` (`issuetype_id`) USING BTREE,
  KEY `queue_id` (`queue_id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=latin1 ROW_FORMAT=COMPACT;

-- ----------------------------
--  There are a lot of fields atm that don't allow NULL, which is giving a lot of trouble on some
--  hosting environments.
-- ----------------------------
ALTER TABLE `dashboardwidgets` MODIFY `display_name` varchar(255) DEFAULT NULL;
ALTER TABLE `dashboardwidgets` MODIFY `ticketstatus_id` int(10) DEFAULT NULL;
ALTER TABLE `dashboardwidgets` MODIFY `type` varchar(255) DEFAULT NULL;
ALTER TABLE `dashboardwidgets` MODIFY `col` int(1) DEFAULT NULL;
ALTER TABLE `dashboardwidgets` MODIFY `row` int(2) DEFAULT NULL;
ALTER TABLE `tickets` MODIFY `completed` datetime DEFAULT NULL;
ALTER TABLE `ticketsourcecounts` MODIFY `queue_id` int(10) DEFAULT NULL;
ALTER TABLE `ticketstatuscounts` MODIFY `queue_id` int(10) DEFAULT NULL;
ALTER TABLE `timeentries` MODIFY `non_billable` tinyint(2) DEFAULT NULL;
ALTER TABLE `timeentries` MODIFY `offset_hours` double(10,2) DEFAULT NULL;

-- ----------------------------
--  Add indexes to tables that can really benefit from it
-- ----------------------------
ALTER TABLE `dashboardqueues` ADD KEY `dashboard_x_queue` (`dashboard_id`,`queue_id`) USING BTREE;
ALTER TABLE `dashboardresources` ADD KEY `dashboard_x_resource` (`dashboard_id`,`resource_id`) USING BTREE;
ALTER TABLE `dashboardticketstatuses` ADD KEY `dashboard_x_ticketstatus` (`dashboard_id`,`ticketstatus_id`) USING BTREE;
ALTER TABLE `dashboardwidgets` ADD KEY `dashboard_x_widget` (`dashboard_id`,`widget_id`) USING BTREE;
ALTER TABLE `dashboardwidgetsettings` ADD KEY `dashboardwidget_id` (`dashboardwidget_id`) USING BTREE;
ALTER TABLE `killratecounts` ADD KEY `dashboard_id` (`dashboard_id`) USING BTREE;
ALTER TABLE `queuehealthcounts` ADD KEY `dashboard_id` (`dashboard_id`) USING BTREE;
ALTER TABLE `queuehealthcounts` ADD KEY `queue_id` (`queue_id`) USING BTREE;
ALTER TABLE `tickets` ADD KEY `ticketstatus_id` (`ticketstatus_id`) USING BTREE;
ALTER TABLE `tickets` ADD KEY `queue_id` (`queue_id`) USING BTREE;
ALTER TABLE `tickets` ADD KEY `ticketsource_id` (`ticketsource_id`) USING BTREE;
ALTER TABLE `tickets` ADD KEY `resource_id` (`resource_id`) USING BTREE;
ALTER TABLE `tickets` ADD KEY `account_id` (`account_id`) USING BTREE;
ALTER TABLE `tickets` ADD KEY `issuetype_id` (`issuetype_id`) USING BTREE;
ALTER TABLE `tickets` ADD KEY `subissuetype_id` (`subissuetype_id`) USING BTREE;
ALTER TABLE `ticketsourcecounts` ADD KEY `ticketsource_id` (`ticketsource_id`) USING BTREE;
ALTER TABLE `ticketsourcecounts` ADD KEY `queue_id` (`queue_id`) USING BTREE;
ALTER TABLE `ticketstatuscounts` ADD KEY `ticketstatus_id` (`ticketstatus_id`) USING BTREE;
ALTER TABLE `ticketstatuscounts` ADD KEY `queue_id` (`queue_id`) USING BTREE;
ALTER TABLE `timeentries` ADD KEY `resource_id` (`resource_id`) USING BTREE;
ALTER TABLE `timeentries` ADD KEY `ticket_id` (`ticket_id`) USING BTREE;

SET FOREIGN_KEY_CHECKS = 1;