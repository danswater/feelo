<?php

class Whmedia_Plugin_Menus
{
  public function canCreateProjects()
  {
    // Must be logged in
    $viewer = Engine_Api::_()->user()->getViewer();
    if( !$viewer || !$viewer->getIdentity() ) {
      return false;
    }

    // Must be able to create media projects
    if( !Engine_Api::_()->authorization()->isAllowed('whmedia_project', $viewer, 'create') ) {
      return false;
    }
    if (Engine_Api::_()->whmedia()->isApple()) {
        return false;
    }
    return true;
  }

  public function canViewProjects()
  {
    $viewer = Engine_Api::_()->user()->getViewer();
    
    // Must be able to view media projects
    if( !Engine_Api::_()->authorization()->isAllowed('whmedia_project', $viewer, 'view') ) {
      return false;
    }

    return true;
  }

  public function onMenuInitialize_WhmediaMainProject($row)
  {
    if( !Engine_Api::_()->core()->hasSubject('whmedia_project') ) {
      return false;
    }

    $viewer = Engine_Api::_()->user()->getViewer();
    $project = Engine_Api::_()->core()->getSubject('whmedia_project');

    if( !$project->isOwner($viewer)) {
      return false;
    }
    if (Engine_Api::_()->whmedia()->isApple()) {
        return false;
    }
    // Modify params
    $params = $row->params;
    $params['params']['project_id'] = $project->getIdentity();
    return $params;
  }

  public function onMenuInitialize_CoreMiniAuth($row)
  {
    $viewer = Engine_Api::_()->user()->getViewer();
    if( $viewer->getIdentity() ) {
      return array(
        'label' => 'Sign Out',
        'route' => 'user_logout',
        'class' => 'no-dloader',
      );
    } else {
      return array(
        'label' => 'Sign In',
        'class' => 'smoothbox',  
        'route' => 'whmedia_user_login',
        'params' => array(
          // Nasty hack
          'return_url' => '64-' . base64_encode($_SERVER['REQUEST_URI']),
        ),
      );
    }
  }
  
  public function isLogged($row) {
      return (bool) Engine_Api::_()->user()->getViewer()->getIdentity();
  }
}