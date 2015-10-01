<?php

class Grandopening_Api_Core extends Core_Api_Abstract
{
    public function getEndTime() {
        $settings = Engine_Api::_()->getApi('settings', 'core');
        date_default_timezone_set(Engine_Api::_()->getApi('settings', 'core')->getSetting('core_locale_timezone', 'UTC'));
        return strtotime($settings->getSetting('grandopening_endtime', 0));
    }

    public function isAdmin(User_Model_User $user) {
        // Not logged in, not an admin
        if( !$user->getIdentity() || empty($user->level_id) ) {
          return false;
        }

        // Check level
        $level = Engine_Api::_()->getItem('authorization_level', $user->level_id);
        if( $level->type == 'admin' || $level->type == 'moderator' ) {
          return true;
        }

        return false;
    }
}
