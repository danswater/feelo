<?php

class Grandopening_AdminManageController extends Core_Controller_Action_Admin
{
  public function indexAction()
  {
    $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
                                                           ->getNavigation('grandopening_admin_main', array(), 'grandopening_admin_main_manage');

    $table = Engine_Api::_()->getDbtable('collections', 'grandopening');
    $rName = $table->info('name');
    
    $this->view->form = $form = new Grandopening_Form_Admin_TestMail();
    $this->view->user_signup_inviteonly = $user_signup_inviteonly = Engine_Api::_()->getApi('settings', 'core')->getSetting('user_signup.inviteonly', 0);
    
    if ($this->getRequest()->isPost() ) {
        if ($this->_getParam('task') == 'test' and $form->isValid($this->getRequest()->getPost())) {
            $type_mail = ($user_signup_inviteonly) ? 'grandopening_invite' : 'grandopening_message' ;
            $email = $form->email->getValue();
            $form->email->setValue();
            $params = array(  //[host],[email],[recipient_title]
                            'host' => $_SERVER['HTTP_HOST'],
                            'email' => $email,                                                                                                        
                            'recipient_name' => $email
                            );
            if ($user_signup_inviteonly) {

                $inviteCode = substr(md5(rand(0, 999) . $email), 10, 7);  

                //[invite_code],[invite_signup_link]
                $params = array_merge($params, array('invite_code' => $inviteCode,
                                                     'invite_signup_link' => Zend_Controller_Front::getInstance()->getRouter()->assemble(array('module' => 'invite',
                                                                                                                                             'controller' => 'signup',
                                                                                                                                            ), 
                                                                                                                                       'default', 
                                                                                                                                       true) . '?' . http_build_query(array('code' => $inviteCode, 'email' => $email['email']))
                                                    ));
            }
            Engine_Api::_()->getApi('mail', 'core')->sendSystem($email, $type_mail, $params);
            $form->addNotice("Test email successfully sent.");
        }
    }
    
    $inviteTable = Engine_Api::_()->getDbtable('invites', 'invite');
    $inviteTableName = $inviteTable->info('name');
    $select = $table->select()->from($rName)
                              ->setIntegrityCheck(false)
                              ->joinLeft($inviteTableName, "{$inviteTableName}.recipient = {$rName}.email", array('invites' => "COUNT(`{$inviteTableName}`.`recipient`)",
                                                                                                                  'new_user_id' => "MAX({$inviteTableName}.new_user_id)"))  
                              ->group($rName.'.email')                                                                                    
                              ->order( $rName.'.creation_date DESC' );
    $t = (string)$select;                                                                                                                  
    $this->view->paginator = $paginator =  Zend_Paginator::factory($select);
    $items_per_page = 40;
    $paginator->setCurrentPageNumber( $this->_getParam('page'));
    $paginator->setItemCountPerPage($items_per_page);
    $mailTemplateTable = Engine_Api::_()->getDbtable('MailTemplates', 'core');
    $this->view->template_message = $mailTemplateTable->fetchRow($mailTemplateTable->select()->where('module = "grandopening"')
                                                                                             ->where('type = "grandopening_message"'))->getIdentity();
    $this->view->template_invite = $mailTemplateTable->fetchRow($mailTemplateTable->select()->where('module = "grandopening"')
                                                                                            ->where('type = "grandopening_invite"'))->getIdentity();
    if ($this->_getParam('empty')) {
        $this->view->message = "You didn't select any email. Please select email using checkbox.";
    }
  }

  public function deleteAction()
  {
    // In smoothbox
    $this->_helper->layout->setLayout('admin-simple');
    $this->view->delete_title = 'Delete Email?';
    $this->view->delete_description = 'Are you sure that you want to delete this email? It wo\'nt be recoverable after being deleted.';

    // Check post
    if( $this->getRequest()->isPost())
    {
      $db = Engine_Db_Table::getDefaultAdapter();
      $db->beginTransaction();

      try
      {
        $this->_helper->api()->getDbtable('collections', 'grandopening')->findRow($this->_getParam('id'))->delete();
        
        $db->commit();
      }

      catch( Exception $e )
      {
        $db->rollBack();
        throw $e;
      }

      $this->_forward('success', 'utility', 'core', array(
          'smoothboxClose' => 10,
          'parentRefresh'=> 10,
          'messages' => array('')
      ));
    }

    // Output
    $this->renderScript('etc/delete.tpl');
  }

  public function mailAction()
  {
    $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
                                                           ->getNavigation('grandopening_admin_main', array());

    if( !$this->getRequest()->isPost() ) {
        return $this->_forward('index');
    }   
    $ids = $this->_getParam('collection_id');
    if (!count($ids)) {
        return $this->_forward('index', NULL, null, array('empty' => TRUE));
    }
    $table = Engine_Api::_()->getDbtable('collections', 'grandopening');
    $select = new Zend_Db_Select($table->getAdapter());
    $select
      ->from($table->info('name'), array('email' , 'username'))
      ->where('collection_id in (?)', new Zend_Db_Expr(implode(',', $ids)));

    $emails = $select->query()->fetchAll();

    $user_signup_inviteonly = Engine_Api::_()->getApi('settings', 'core')->getSetting('user_signup.inviteonly', 0);
    $type_mail = ($user_signup_inviteonly) ? 'grandopening_invite' : 'grandopening_message';
    foreach( $emails as $email ) {
      $params = array(  //[host],[email],[recipient_title]
                        'host' => $_SERVER['HTTP_HOST'],
                        'email' => $email['email'],                                                                                                        
                        'recipient_name' => ($email['username']) ? $email['username'] : $email['email']
                     );
      if ($user_signup_inviteonly) {
          $inviteTable = Engine_Api::_()->getDbtable('invites', 'invite');
          do {
            $inviteCode = substr(md5(rand(0, 999) . $email['email']), 10, 7);
          } while( null !== $inviteTable->fetchRow(array('code = ?' => $inviteCode)) );

          $row = $inviteTable->createRow();
          $row->user_id = Engine_Api::_()->user()->getViewer()->getIdentity();
          $row->recipient = $email['email'];
          $row->code = $inviteCode;
          $row->timestamp = new Zend_Db_Expr('NOW()');
          $row->message = '';
          $row->save();
          //[invite_code],[invite_signup_link]
          $params = array_merge($params, array('invite_code' => $inviteCode,
                                               'invite_signup_link' => Zend_Controller_Front::getInstance()->getRouter()->assemble(array('module' => 'invite',
                                                                                                                                         'controller' => 'signup',
                                                                                                                                        ), 
                                                                                                                                   'default', 
                                                                                                                                   true) . '?' . http_build_query(array('code' => $inviteCode, 'email' => $email['email']))
                                               ));
      }
      Engine_Api::_()->getApi('mail', 'core')->sendSystem($email['email'], $type_mail, $params);

    }

    $this->view->status = true;
  }
  
  public function exportAction() {
     $res = Engine_Api::_()->getDbtable('collections', 'grandopening')->fetchAll();
     $outstream = fopen("php://output", 'w');
     header("Content-Type: application/csv");
     header("Content-Disposition: attachment;Filename=export-emails.csv");
     fputcsv($outstream, array('username', 'email'), ';', '"');

     foreach ($res as $row) {
         fputcsv($outstream, array($row->username, $row->email), ';', '"');
     } 

     fclose($outstream);
     $this->_helper->layout->disableLayout();
     $this->_helper->viewRenderer->setNoRender(true);
  }
  
}
