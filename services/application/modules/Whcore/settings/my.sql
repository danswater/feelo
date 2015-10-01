INSERT IGNORE INTO `engine4_core_modules` (`name`, `title`, `description`, `version`, `enabled`, `type`) VALUES  ('whcore', 'WebHive Core', '', '4.3.0', 1, 'extra') ;

INSERT IGNORE INTO `engine4_core_settings` (`name`, `value`) VALUES
  ('wh.facebook.type', ''),
  ('wh.facebook.appid', '');

INSERT IGNORE INTO `engine4_core_menuitems`(`name`,`module`,`label`,`plugin`,`params`,`menu`,`submenu`,`enabled`,`custom`,`order`)
				    VALUES ('core_admin_main_plugins_whcore','whcore','WebHive Core','','{\"route\":\"admin_default\",\"module\":\"whcore\",\"controller\":\"settings\"}','core_admin_main_plugins','',1,0,999),
                                           ('whcore_admin_main_settings','whcore','Global Settings','','{\"route\":\"admin_default\",\"module\":\"whcore\",\"controller\":\"settings\",\"action\":\"index\"}','whcore_admin_main','',1,0,1);
