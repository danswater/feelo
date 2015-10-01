INSERT IGNORE INTO `engine4_core_tasks` (`title`, `module`, `plugin`, `timeout`)
                                 VALUES ('Cleanup temporary files', 'whmedia', 'Whmedia_Plugin_Task_CleanupTemporary', 36000);

INSERT IGNORE INTO `engine4_core_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `order`)
                                     VALUES ('whmedia_main_browse', 'whmedia', 'Browse All', 'Whmedia_Plugin_Menus::canViewProjects', '{"route":"whmedia_default"}', 'whmedia_main', '', 1),
                                            ('whmedia_main_manage', 'whmedia', 'My Projects', 'Whmedia_Plugin_Menus::canCreateProjects', '{"route":"whmedia_default","action":"manage"}', 'whmedia_main', '', 2),
                                            ('whmedia_main_create', 'whmedia', 'Create a Project', 'Whmedia_Plugin_Menus::canCreateProjects', '{"route":"whmedia_default","controller":"project","action":"create"}', 'whmedia_main', '', 3),
                                            ('whmedia_main_project', 'whmedia', 'Manage Project', 'Whmedia_Plugin_Menus', '{"route":"whmedia_project"}', 'whmedia_main', '', 4);

ALTER TABLE `engine4_whmedia_medias`
    CHANGE `title`  `title` text NOT NULL,
    ADD COLUMN `duration` int(10) unsigned DEFAULT NULL,
    ADD COLUMN `size` varchar(128) DEFAULT NULL;

DELETE d FROM engine4_whmedia_medias d
LEFT JOIN engine4_whmedia_projects ON engine4_whmedia_projects.project_id = d.project_id
WHERE engine4_whmedia_projects.project_id IS NULL;

ALTER TABLE `engine4_whmedia_medias` ADD CONSTRAINT `FK_engine4_whmedia_medias` FOREIGN KEY (`project_id`) REFERENCES `engine4_whmedia_projects` (`project_id`) ON DELETE CASCADE ON UPDATE CASCADE;

INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'whmedia_project' as `type`,
    'save_original' as `name`,
    1 as `value`,
    null as `params`
  FROM `engine4_authorization_levels` WHERE `type` != 'public';