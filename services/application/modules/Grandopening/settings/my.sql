INSERT IGNORE INTO engine4_core_menuitems (`name`, `module`, label, plugin, params, menu, submenu, enabled, custom, `order`)
                                   VALUES ('core_admin_main_plugins_grandopening', 'grandopening', 'Grand Opening', '', '{"route":"admin_default","module":"grandopening","controller":"settings"}', 'core_admin_main_plugins', '', 1, 0, 999),
                                          ('grandopening_admin_main_settings', 'grandopening', 'Global Settings', '', '{"route":"admin_default","module":"grandopening","controller":"settings"}', 'grandopening_admin_main', '', 1, 0, 1),
                                          ('grandopening_admin_main_manage', 'grandopening', 'View Emails', '', '{"route":"admin_default","module":"grandopening","controller":"manage"}', 'grandopening_admin_main', '', 1, 0, 2),
                                          ('grandopening_admin_main_cover', 'grandopening', 'Manage Cover', '', '{"route":"admin_default","module":"grandopening","controller":"cover"}', 'grandopening_admin_main', '', 1, 0, 3    );

CREATE TABLE `engine4_grandopening_collections` (
  `collection_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `username` varchar(128) DEFAULT NULL,
  `email` varchar(128) NOT NULL,
  `creation_date` datetime NOT NULL,
  UNIQUE KEY `collection_id` (`collection_id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=latin1;

INSERT IGNORE INTO `engine4_core_mailtemplates` (`type`, `module`, `vars`) 
                                         VALUES ('grandopening_message', 'grandopening', '[host],[email],[recipient_name]');

INSERT IGNORE INTO `engine4_core_mailtemplates` (`type`, `module`, `vars`) 
                                         VALUES ('grandopening_invite', 'grandopening', '[host],[email],[recipient_name],[invite_code],[invite_signup_link]');
