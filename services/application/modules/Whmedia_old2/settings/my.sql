INSERT IGNORE INTO `engine4_core_menuitems`(`name`,`module`,`label`,`plugin`,`params`,`menu`,`submenu`,`enabled`,`custom`,`order`)
				    VALUES ('core_admin_main_plugins_whmedia','whmedia','Media Plugin','','{\"route\":\"admin_default\",\"module\":\"whmedia\",\"controller\":\"settings\"}','core_admin_main_plugins','',1,0,999);

INSERT IGNORE INTO `engine4_core_menuitems`(`name`,`module`,`label`,`plugin`,`params`,`menu`,`submenu`,`enabled`,`custom`,`order`)
				    VALUES ('whmedia_admin_main_manage','whmedia','View Media','','{\"route\":\"admin_default\",\"module\":\"whmedia\",\"controller\":\"manage\"}','whmedia_admin_main','',1,0,1);

INSERT IGNORE INTO `engine4_core_menuitems`(`name`,`module`,`label`,`plugin`,`params`,`menu`,`submenu`,`enabled`,`custom`,`order`)
				    VALUES ('whmedia_admin_main_settings','whmedia','Global Settings','','{\"route\":\"admin_default\",\"module\":\"whmedia\",\"controller\":\"settings\"}','whmedia_admin_main','',1,0,2);

INSERT IGNORE INTO `engine4_core_menuitems`(`name`,`module`,`label`,`plugin`,`params`,`menu`,`submenu`,`enabled`,`custom`,`order`)
				    VALUES ('whmedia_admin_main_level','whmedia','Member Level Settings','','{\"route\":\"admin_default\",\"module\":\"whmedia\",\"controller\":\"level\"}','whmedia_admin_main','',1,0,3);

INSERT IGNORE INTO `engine4_core_menuitems`(`name`,`module`,`label`,`plugin`,`params`,`menu`,`submenu`,`enabled`,`custom`,`order`)
				    VALUES ('whmedia_admin_main_categories','whmedia','Categories','','{\"route\":\"admin_default\",\"module\":\"whmedia\",\"controller\":\"settings\", \"action\":\"categories\"}','whmedia_admin_main','',1,0,4),
                                           ('core_main_whmedia','whmedia','Media','','{\"route\":\"whmedia_default\"}','core_main','',1,0,999),
                                           ('mobi_browse_whmedia','whmedia','Media','','{\"route\":\"whmedia_default\"}','mobi_browse','',1,0,999);

insert IGNORE into `engine4_activity_notificationtypes`(`type`,`module`,`body`,`is_request`,`handler`,`default`)
                                                values ('whmedia_processed_failed','whmedia','Your media file has failed to process in {item:$object:$label}.',0,'',1);

INSERT IGNORE INTO `engine4_activity_notificationtypes`(`type`,`module`,`body`,`is_request`,`handler`,`default`)
                                                VALUES ('whmedia_following','whmedia','{item:$subject} is now following you.',0,'',1);

INSERT IGNORE INTO `engine4_core_mailtemplates`(`type`,`module`,`vars`)
                                        VALUES ('notify_whmedia_following','whmedia','[host],[email],[recipient_title],[recipient_link],[recipient_photo],[sender_title],[sender_link],[sender_photo]');

INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'whmedia_project' as `type`,
    'auth_comment' as `name`,
    5 as `value`,
    '[\"everyone\",\"registered\",\"owner_network\",\"owner_member_member\",\"owner_member\",\"owner\"]' as `params`
  FROM `engine4_authorization_levels` WHERE `type` != 'public';

INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'whmedia_project' as `type`,
    'auth_view' as `name`,
    5 as `value`,
    '[\"everyone\",\"registered\",\"owner_network\",\"owner_member_member\",\"owner_member\",\"owner\"]' as `params`
  FROM `engine4_authorization_levels` WHERE `type` != 'public';

INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'whmedia_project' as `type`,
    'comment' as `name`,
    1 as `value`,
    null as `params`
  FROM `engine4_authorization_levels` WHERE `type` != 'public';

INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'whmedia_project' as `type`,
    'create' as `name`,
    1 as `value`,
    null as `params`
  FROM `engine4_authorization_levels` WHERE `type` != 'public';

INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'whmedia_project' as `type`,
    'file_type' as `name`,
    5 as `value`,
    '[\"image\",\"video\",\"audio\"]' as `params`
  FROM `engine4_authorization_levels` WHERE `type` != 'public';

INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'whmedia_project' as `type`,
    'medias_count' as `name`,
    3 as `value`,
    '100' as `params`
  FROM `engine4_authorization_levels` WHERE `type` != 'public';

INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'whmedia_project' as `type`,
    'save_original' as `name`,
    1 as `value`,
    null as `params`
  FROM `engine4_authorization_levels` WHERE `type` != 'public';

INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'whmedia_project' as `type`,
    'view' as `name`,
    1 as `value`,
    null as `params`
  FROM `engine4_authorization_levels`;

insert IGNORE into `engine4_core_jobtypes`(`title`,`type`,`module`,`plugin`,`form`,`enabled`,`priority`,`multi`)
                                   values ('WhMedia Encode','whmedia_encode','whmedia','Whmedia_Plugin_Job_Encode',NULL,1,75,2);

insert IGNORE into `engine4_core_mailtemplates`(`type`,`module`,`vars`)
                                        values ('notify_whmedia_processed_failed','whmedia','[host],[email],[recipient_title],[recipient_link],[recipient_photo],[sender_title],[sender_link],[sender_photo],[object_title],[object_link],[object_photo],[object_description]');
/**
ALTER TABLE `engine4_whmedia_categories` ADD COLUMN `url` VARCHAR(34) CHARSET utf8 COLLATE utf8_bin NULL AFTER `order`; 			
ALTER TABLE `engine4_whmedia_categories` ADD UNIQUE `url` (`url`); 
*/

CREATE TABLE `engine4_whmedia_categories` (
  `category_id` int(11) NOT NULL AUTO_INCREMENT,
  `category_name` varchar(128) NOT NULL,
  `order` smallint(6) NOT NULL DEFAULT '999',
  `url` varchar(34) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL,
  PRIMARY KEY (`category_id`),
  UNIQUE KEY `url` (`url`),
  KEY `order` (`order`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

insert IGNORE into `engine4_whmedia_categories`(`category_name`,`order`) values ('Default Category',1);

CREATE TABLE `engine4_whmedia_medias` (
  `media_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `title` text NOT NULL,
  `description` text NOT NULL,
  `project_id` int(10) unsigned NOT NULL,
  `code` text,
  `order` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `encode` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `invisible` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `creation_date` datetime NOT NULL,
  `duration` int(10) unsigned DEFAULT NULL,
  `size` varchar(128) DEFAULT NULL,
  `is_text` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `is_url` VARCHAR(255) NOT NULL,
  UNIQUE KEY `media_id` (`media_id`),
  KEY `project_id` (`project_id`),
  KEY `order` (`order`),
  KEY `encode` (`encode`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

CREATE TABLE `engine4_whmedia_projects` (
  `project_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) unsigned NOT NULL,
  `category_id` int(11) NOT NULL,
  `title` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `description` text CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `creation_date` datetime NOT NULL,
  `project_views` int(11) NOT NULL DEFAULT '0',
  `owner_type` varchar(64) CHARACTER SET utf8 NOT NULL,
  `search` int(1) NOT NULL DEFAULT '1',
  `cover_file_id` int(11) unsigned DEFAULT NULL,
  `comment_count` int(11) unsigned NOT NULL DEFAULT '0',
  `is_published` tinyint(1) unsigned NOT NULL DEFAULT '0',
  UNIQUE KEY `id` (`project_id`),
  KEY `user_id` (`user_id`),
  KEY `category_id` (`category_id`),
  KEY `cover_file_id` (`cover_file_id`),
  KEY `project_views` (`project_views`),
  CONSTRAINT `FK_engine4_whmedia_projects_cover` FOREIGN KEY (`cover_file_id`) REFERENCES `engine4_whmedia_medias` (`media_id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `FK_engine4_whmedia_projects_user` FOREIGN KEY (`user_id`) REFERENCES `engine4_users` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

ALTER TABLE `engine4_whmedia_medias` ADD CONSTRAINT `FK_engine4_whmedia_medias` FOREIGN KEY (`project_id`) REFERENCES `engine4_whmedia_projects` (`project_id`) ON DELETE CASCADE ON UPDATE CASCADE;

INSERT IGNORE INTO `engine4_activity_actiontypes` (`type`, `module`, `body`, `enabled`, `displayable`, `attachable`, `commentable`, `shareable`, `is_generated`)
                                           VALUES ('whmedia_media_new', 'whmedia', '{item:$subject} added new media files to the project {item:$object}:', 1, 5, 1, 3, 1, 1);

INSERT IGNORE INTO `engine4_activity_actiontypes` (`type`, `module`, `body`, `enabled`, `displayable`, `attachable`, `commentable`, `shareable`, `is_generated`)
                                           VALUES ('whmedia_project_publish', 'whmedia', '{item:$subject} published a new project {item:$object}:', 1, 5, 1, 3, 1, 1);

INSERT IGNORE INTO `engine4_core_menus` (`name`, `type`, `title`) 
                                 VALUES ('whmedia_main', 'standard', 'Media Plugin Main Navigation Menu');

INSERT IGNORE INTO `engine4_core_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `order`)
                                     VALUES ('whmedia_main_browse', 'whmedia', 'Browse All', 'Whmedia_Plugin_Menus::canViewProjects', '{"route":"whmedia_default"}', 'whmedia_main', '', 1),
                                            ('whmedia_main_manage', 'whmedia', 'My Projects', 'Whmedia_Plugin_Menus::canCreateProjects', '{"route":"whmedia_default","action":"manage"}', 'whmedia_main', '', 2),
                                            ('whmedia_main_create', 'whmedia', 'Create a Project', 'Whmedia_Plugin_Menus::canCreateProjects', '{"route":"whmedia_default","controller":"project","action":"create"}', 'whmedia_main', '', 3),
                                            ('whmedia_main_project', 'whmedia', 'Manage Project', 'Whmedia_Plugin_Menus', '{"route":"whmedia_project"}', 'whmedia_main', '', 4);

INSERT IGNORE INTO `engine4_core_tasks` (`title`, `module`, `plugin`, `timeout`)
                                 VALUES ('Cleanup temporary files', 'whmedia', 'Whmedia_Plugin_Task_CleanupTemporary', 36000);

CREATE TABLE `engine4_whmedia_follow` (
  `follow_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned NOT NULL,
  `follower_id` int(10) unsigned NOT NULL,
  `creation_date` datetime NOT NULL,
  UNIQUE KEY `follow_id` (`follow_id`),
  UNIQUE KEY `user_follower_id` (`user_id`,`follower_id`),
  KEY `user_id` (`user_id`),
  KEY `follower_id` (`follower_id`),
  KEY `creation_date` (`creation_date`),  
  CONSTRAINT `FK_engine4_whmedia_follower` FOREIGN KEY (`follower_id`) REFERENCES `engine4_users` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_engine4_whmedia_follow_user` FOREIGN KEY (`user_id`) REFERENCES `engine4_users` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT IGNORE INTO `engine4_core_menuitems`(`name`,`module`,`label`,`plugin`,`params`,`menu`,`submenu`,`enabled`,`custom`,`order`)
				    VALUES ('core_main_whmembers','whmedia','Follow','','{\"route\":\"whmedia_members\"}','core_main','',1,0,999);

CREATE TABLE `engine4_whmedia_stream` (
  `stream_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) unsigned NOT NULL,
  `project_id` int(11) unsigned NOT NULL,
  `creation_date` datetime NOT NULL,
  UNIQUE KEY `stream_id` (`stream_id`),
  KEY `user_id` (`user_id`),
  KEY `project_id` (`project_id`),
  KEY `creation_date` (`creation_date`),
  CONSTRAINT `FK_engine4_whmedia_stream_project` FOREIGN KEY (`project_id`) REFERENCES `engine4_whmedia_projects` (`project_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_engine4_whmedia_stream_user` FOREIGN KEY (`user_id`) REFERENCES `engine4_users` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `engine4_whmedia_circles` (
  `circle_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned NOT NULL,
  `title` varchar(64) NOT NULL,
  UNIQUE KEY `circle_id` (`circle_id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `FK_engine4_whmedia_circles` FOREIGN KEY (`user_id`) REFERENCES `engine4_users` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `engine4_whmedia_circleitems` (
  `circleitem_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `circle_id` int(10) unsigned NOT NULL,
  `user_id` int(10) unsigned NOT NULL,
  UNIQUE KEY `circleitem_id` (`circleitem_id`),
  UNIQUE KEY `circle_user_id` (`circle_id`,`user_id`),
  KEY `circle_id` (`circle_id`),
  KEY `FK_engine4_whmedia_circleitems_user_id` (`user_id`),
  CONSTRAINT `FK_engine4_whmedia_circleitems_user_id` FOREIGN KEY (`user_id`) REFERENCES `engine4_users` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_engine4_whmedia_circleitems_circle_id` FOREIGN KEY (`circle_id`) REFERENCES `engine4_whmedia_circles` (`circle_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT IGNORE INTO `engine4_core_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `order`)
                                     VALUES ('core_mini_boxes', 'whmedia', 'My Boxes', 'Whmedia_Plugin_Menus::isLogged', '{"route":"whmedia_circles"}', 'core_mini', '', 6);
