SET NAMES utf8;
SET FOREIGN_KEY_CHECKS = 0;
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