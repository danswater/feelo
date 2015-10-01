<?php

class Whmedia_Form_Admin_Category extends Engine_Form
{
  protected $_field;

  public function init()
  {
    $this
      ->setMethod('post')
      ->setAttrib('class', 'global_form_box')
      ->setAction(Zend_Controller_Front::getInstance()->getRouter()->assemble(array('format' => 'smoothbox')));


    $label = new Zend_Form_Element_Text('label');
    $label->setLabel('Category Name')
      ->addValidator('NotEmpty')
      ->setRequired(true)
      ->setAttrib('class', 'text');

        
    $id = new Zend_Form_Element_Hidden('id');


    $this->addElements(array(
      $label,
      $id
    ));
    
    $this->addElement('Text', 'url', array(
                                            'label' => 'Category URL',   
                                            'maxlength' => 34,
                                            'validators' => array('NotEmpty',
                                                                  array('validator' => 'StringLength', 'options' => array('min' => 1, 'max' => 34))
                                                                 ),
                                            'filters' => array(
                                                                'StringTrim', 'StripTags', 'StringToLower', new Zend_Filter_PregReplace('/(  )/i', ' '), new Zend_Filter_PregReplace('/( )/i', '_')
                                                              ),
    ));
    
    // Buttons
    $this->addElement('Button', 'submit', array(
      'label' => 'Add Category',
      'type' => 'submit',
      'ignore' => true,
      'decorators' => array('ViewHelper')
    ));

    $this->addElement('Cancel', 'cancel', array(
      'label' => 'cancel',
      'link' => true,
      'prependText' => ' or ',
      'href' => '',
      'onClick'=> 'javascript:parent.Smoothbox.close();',
      'decorators' => array(
        'ViewHelper'
      )
    ));
    $this->addDisplayGroup(array('submit', 'cancel'), 'buttons');
    $button_group = $this->getDisplayGroup('buttons');


  }

  public function setField($category)
  {
    $this->_field = $category;

    // Set up elements
  
    $this->label->setValue($category->category_name);
    $this->id->setValue($category->category_id);
    $this->submit->setLabel('Edit Category');
    $this->url->setValue($category->url);

  }
  
  public function getValues($suppressArrayNotation = false) {
      $values = parent::getValues($suppressArrayNotation);
      if (empty($values["url"])) {
          $filterChain = new Zend_Filter();
          $filters = $this->url->getFilters();
          foreach ($filters as $filter) {
              $filterChain->addFilter($filter);
          }
          $values["url"] = $filterChain->filter($values["label"]);
      }
      $values["url"] = urlencode($values["url"]);
      return $values;
  }
}