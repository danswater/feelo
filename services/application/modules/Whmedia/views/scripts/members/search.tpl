<?php echo $this->form ?>
<?php echo $this->partial('members/_paginator.tpl', array('paginator' => $this->paginator,
                                                          'params' => $this->form->getValues()  )) ?>