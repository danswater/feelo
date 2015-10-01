<?php

class Grandopening_EmailController extends Core_Controller_Action_Standard
{
  public function addAction()
  {
    $this->_helper->layout->disableLayout(true);
    $this->view->form = $form = new Grandopening_Form_Collection();

    if( $this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost()) ) {
        $db = Engine_Db_Table::getDefaultAdapter();
        $db->beginTransaction();

        try {
            $row = $this->_helper->api()->getDbtable('collections', 'grandopening')->createRow();
            $row->setFromArray($form->getValues());
            $row->save();

            $db->commit();
        }

        catch( Exception $e ) {
            $db->rollBack();
            throw $e;
        }
        $form->reset();
        $form->addNotice(Zend_Registry::get('Zend_Translate')->_('Your email was added.'));
    }
  }
}
