SET NAMES utf8;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
--  Table structure for `dashboards`
-- ----------------------------
ALTER TABLE `dashboards` ADD COLUMN `show_tickets_top_x` tinyint(1) NOT NULL DEFAULT '0' after `show_sla_violations`;
ALTER TABLE `dashboards` ADD COLUMN `show_clock` tinyint(1) NOT NULL DEFAULT '0' after `show_tickets_top_x`;


-- ----------------------------
--  Table structure for `dashboardwidgetsettings`
-- ----------------------------
CREATE TABLE `dashboardwidgetsettings` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `dashboardwidget_id` int(10) NOT NULL,
  `name` varchar(255) NOT NULL,
  `value` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)) ENGINE=`InnoDB` AUTO_INCREMENT=76 DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci ROW_FORMAT=COMPACT CHECKSUM=0 DELAY_KEY_WRITE=0;


-- ----------------------------
--  Table structure for `timeentries`
-- ----------------------------
CREATE TABLE `timeentries` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `created` datetime NOT NULL,
  `resource_id` int(10) NOT NULL,
  `ticket_id` int(10) NOT NULL,
  `hours_to_bill` double(10,2) NOT NULL,
  `hours_worked` double(10,2) NOT NULL,
  `non_billable` tinyint(1) NOT NULL,
  `offset_hours` double(10,2) NOT NULL,
  PRIMARY KEY (`id`)) ENGINE=`InnoDB` AUTO_INCREMENT=10 DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci ROW_FORMAT=COMPACT CHECKSUM=0 DELAY_KEY_WRITE=0;

-- ----------------------------
--  Records of `widgets`
-- ----------------------------
BEGIN;
INSERT INTO `widgets` VALUES ('9', 'Latest tickets', '3', '2', 'Widgets/tickets_top_x'), ('10', 'Clock', '1', '1', 'Widgets/clock');
COMMIT;

SET FOREIGN_KEY_CHECKS = 1;