INSERT IGNORE INTO engine4_core_menuitems (`name`, `module`, label, plugin, params, menu, submenu, enabled, custom, `order`)
                                   VALUES ('grandopening_admin_main_cover', 'grandopening', 'Manage Cover', '', '{"route":"admin_default","module":"grandopening","controller":"cover"}', 'grandopening_admin_main', '', 1, 0, 3);

UPDATE engine4_core_menuitems SET `order` = 1 WHERE `name` = 'grandopening_admin_main_settings';
UPDATE engine4_core_menuitems SET `order` = 2 WHERE `name` = 'grandopening_admin_main_manage';
