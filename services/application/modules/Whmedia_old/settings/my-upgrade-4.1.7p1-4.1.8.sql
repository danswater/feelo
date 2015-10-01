UPDATE `engine4_core_menuitems` SET `params` = '{\"route\":\"whmedia_default\"}'
    WHERE `module` = 'whmedia' and (`name` = 'mobi_browse_whmedia' or `name` = 'core_main_whmedia');