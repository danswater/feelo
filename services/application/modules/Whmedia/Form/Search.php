<?php

class Whmedia_Form_Search extends Engine_Form
{
  protected static $_instance;

  public static function getInstance() {
    if (self::$_instance===null) {
        self::$_instance = new self;
    }
    return self::$_instance;
  }

  public function init()
  {
    $this
      ->setAttribs(array(
        'id' => 'filter_form',
        'class' => 'global_form_box',
      ))
      ->setAction(Zend_Controller_Front::getInstance()->getRouter()->assemble(array()))
      ;

    $this->addElement('Text', 'search', array(
      'label' => Zend_Registry::get('Zend_Translate')->_("Search Projects"),
      'onchange' => 'this.form.submit();',
    ));

    $this->addElement('Text', 'tags', array(
      'label' => Zend_Registry::get('Zend_Translate')->_("Keywords"),
      'autocomplete' => 'off',
      'allowEmpty' => true,
      'filters' => array('StringTrim'),
      'onKeyPress' => "return submitenter(this,event)",
      'onchange' => 'var forms = function(){$("filter_form").submit();}; setTimeout(forms, 500);'
    ));
    $this->tags->addDecorator('viewScript', array('viewScript' => 'application/modules/Whmedia/views/scripts/_Tags.tpl',
                                                  'placement'  => 'prepend'
                                                 ));
    $this->addElement('Select', 'orderby', array(
      'label' => Zend_Registry::get('Zend_Translate')->_('Sort by'),
      'multiOptions' => array(
        'creation_date' => Zend_Registry::get('Zend_Translate')->_('Most Recent'),       
        'count_likes' => Zend_Registry::get('Zend_Translate')->_("Most Appreciated"),
        'count_comments' => Zend_Registry::get('Zend_Translate')->_("Most Comments"),
        'project_views' => Zend_Registry::get('Zend_Translate')->_("Most Viewed")  
      ),
      'onchange' => 'this.form.submit();',
    ));

    $this->addElement('Select', 'bytime', array(
      'label' => Zend_Registry::get('Zend_Translate')->_("By Time"),
      'multiOptions' => array(
        0 => Zend_Registry::get('Zend_Translate')->_("All Time"),
        'today' => Zend_Registry::get('Zend_Translate')->_("Today"),
        'week' => Zend_Registry::get('Zend_Translate')->_("This Week"),
        'month' => Zend_Registry::get('Zend_Translate')->_("This Month"),
        'featured' => Zend_Registry::get('Zend_Translate')->_("Featured")  
      ),
      'onchange' => 'this.form.submit();',
    ));
    if (Engine_Api::_()->user()->getViewer()->getIdentity()) {
        $this->addElement('Select', 'show', array(
          'label' => Zend_Registry::get('Zend_Translate')->_('Show'),
          'multiOptions' => array(
            '1' => Zend_Registry::get('Zend_Translate')->_("Everyone's Projects"),
            '2' => Zend_Registry::get('Zend_Translate')->_("Only My Friends' Projects"),
          ),
          'onchange' => 'this.form.submit();',
        ));
    }
    
    $categories = Engine_Api::_()->whmedia()->getCategories();
    
    $cat_multiOptions = array('0' => Zend_Registry::get('Zend_Translate')->_("All Categories"));
    foreach( $categories as $category )
    {
      $cat_multiOptions[$category->url] = $category->category_name;
    }
    $this->addElement('Select', 'category', array(
      'label' => Zend_Registry::get('Zend_Translate')->_('Category'),
      'multiOptions' => $cat_multiOptions,
      'onchange' => 'this.form.submit();'
    ));
    
    $this->addElement('Hidden', 'page', array(
      'order' => 1
    ));

  }
}
