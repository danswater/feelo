<?php
/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    Core
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: SearchController.php 9747 2012-07-26 02:08:08Z john $
 * @author     John
 */

/**
 * @category   Application_Core
 * @package    Core
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 */
class Core_SearchController extends Core_Controller_Action_Standard
{
  public function indexAction()
  {
    $searchApi = Engine_Api::_()->getApi('search', 'core');

    // check public settings
    $require_check = Engine_Api::_()->getApi('settings', 'core')->core_general_search;
    if( !$require_check ) {
      if( !$this->_helper->requireUser()->isValid() ) return;
    }

    // Prepare form
    $this->view->form = $form = new Core_Form_Search();

    // Get available types
    $availableTypes = $searchApi->getAvailableTypes();
    if( is_array($availableTypes) && count($availableTypes) > 0 ) {
      $options = array();
      foreach( $availableTypes as $index => $type ) {
        $options[$type] = strtoupper('ITEM_TYPE_' . $type);
      }
    }

    // Check form validity?
    $values = array();
    if( $form->isValid($this->_getAllParams()) ) {
      $values = $form->getValues();
      $requestParams = $this->getRequest()->getParams();
      $values[ 'type' ] = $requestParams[ 'type' ];
    }

    if( isset( $values[ 'query' ] ) ) {
      if( $values[ 'query' ][ 0 ] == '#' ) {
        $values[ 'query' ] = substr( $values[ 'query' ], 1 );
      }
    }

    $filter = null;
    if( $this->getRequest()->getParam( 'filter' ) ) {
      $this->view->filter = $filter = $this->getRequest()->getParam( 'filter' );
    }

    $this->view->query = $query = (string) @$values['query'];
    $this->view->type  = $type  = (string) @$values['type'];
    $this->view->page  = $page  = (int) $this->_getParam('page');
    if( $query ) {
      $this->view->result = $searchApi->getHashTag( $query, $type, $filter );
    }
  }
}