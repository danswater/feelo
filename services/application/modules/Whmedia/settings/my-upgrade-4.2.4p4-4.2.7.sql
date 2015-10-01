INSERT IGNORE INTO `engine4_activity_actiontypes` (`type`, `module`, `body`, `enabled`, `displayable`, `attachable`, `commentable`, `shareable`, `is_generated`)
                                           VALUES ('whmedia_project_publish', 'whmedia', '{item:$subject} published a new project {item:$object}:', 1, 5, 1, 3, 1, 1);

ALTER TABLE `engine4_whmedia_medias`    
    ADD COLUMN `is_text` tinyint(1) unsigned NOT NULL DEFAULT '0';

ALTER TABLE `engine4_whmedia_projects`    
    ADD COLUMN `is_published` tinyint(1) unsigned NOT NULL DEFAULT '0';

UPDATE `engine4_whmedia_projects` 
    SET `is_published` = 1;

