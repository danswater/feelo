INSERT IGNORE INTO `engine4_core_mailtemplates` (`type`, `module`, `vars`) 
                                         VALUES ('grandopening_message', 'grandopening', '[host],[email],[recipient_name]');

INSERT IGNORE INTO `engine4_core_mailtemplates` (`type`, `module`, `vars`) 
                                         VALUES ('grandopening_invite', 'grandopening', '[host],[email],[recipient_name],[invite_code],[invite_signup_link]');
