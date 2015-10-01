<?php

class Whmedia_Form_Create extends Engine_Form {

    public function init() {
        $this->setAttrib('enctype', 'multipart/form-data')
                ->setAttrib('id', 'form-upload');

        $this->addElement('Text', 'title', array(
            'label' => Zend_Registry::get('Zend_Translate')->_("formTitle"),
            'maxlength' => 100,
            'allowEmpty' => false,
            'required' => true,
            'filters' => array('StringTrim', 'StripTags', new Engine_Filter_Censor(), new Engine_Filter_StringLength(array('max' => '100')))
        ));
        /* original code
        $this->addElement('Text', 'whtags', array(
            'label' => Zend_Registry::get('Zend_Translate')->_('Hashtag 1'),
            'maxlength' => 100,
            'autocomplete' => 'off',
            'description' => Zend_Registry::get('Zend_Translate')->_('Separate tags with spaces. Ex #design  #illustration #music'),
            'allowEmpty' => true,
            'filters' => array('StringTrim', new Engine_Filter_Censor()),
        ));
         */

        $this->addElement('Text', 'whtag1', array(
            'label' => Zend_Registry::get('Zend_Translate')->_('Hashtag 1'),
            'maxlength' => 100,
            'autocomplete' => 'off',
            'description' => Zend_Registry::get('Zend_Translate')->_('Any space will trim automatically'),
            'allowEmpty' => true,
            'filters' => array('StringTrim', new Engine_Filter_Censor()),
        ));

        $this->addElement('Text', 'whtag2', array(
            'label' => Zend_Registry::get('Zend_Translate')->_('Hashtag 2'),
            'maxlength' => 100,
            'autocomplete' => 'off',
            'description' => Zend_Registry::get('Zend_Translate')->_('Any space will trim automatically'),
            'allowEmpty' => true,
            'filters' => array('StringTrim', new Engine_Filter_Censor()),
        ));

        $this->addElement('Text', 'whtag3', array(
            'label' => Zend_Registry::get('Zend_Translate')->_('Hashtag 3'),
            'maxlength' => 100,
            'autocomplete' => 'off',
            'description' => Zend_Registry::get('Zend_Translate')->_('Any space will trim automatically'),
            'allowEmpty' => true,
            'filters' => array('StringTrim', new Engine_Filter_Censor()),
        ));

        $this->addElement('Textarea', 'description', array(
            'label' => Zend_Registry::get('Zend_Translate')->_("Description"),
            'filters' => array('StringTrim', 'StripTags', new Engine_Filter_Censor())
        ));


        $categories = Engine_Api::_()->whmedia()->getCategories();
        $multioptions = array();
        foreach ($categories as $category) {
            $multioptions[$category->category_id] = $category->category_name;
        }

        if (count($multioptions) > 0)
            $this->addElement('Select', 'category_id', array(
                'label' => Zend_Registry::get('Zend_Translate')->_("Category"),
                'required' => true,
                'allowEmpty' => false,
                'multiOptions' => array(
                    '' => Zend_Registry::get('Zend_Translate')->_('Select Category')
                )
            ));

       
        $this->addElement('Checkbox', 'search', array(
            'label' => 'Show this post in search results',
            'value' => 1,
        ));
        if (Engine_Api::_()->authorization()->isAllowed('whmedia_project', null, 'save_original')) {
            $this->addElement('Checkbox', 'allow_download_original', array('label' => 'Allow others to download original images',
                'value' => 1,
            ));
        }
        $user = Engine_Api::_()->user()->getViewer();
        $availableLabels = array(
            'everyone' => 'Everyone',
            'registered' => 'All Registered Members',
            //'owner_network' => 'Friends and Networks',
            'owner_member_member' => 'Followers of Followers',
            'owner_member' => 'Followers Only',
            'owner' => 'Just Me'
        );

        // Element: auth_view
        $viewOptions = (array) Engine_Api::_()->authorization()->getAdapter('levels')->getAllowed('whmedia_project', $user, 'auth_view');
        $viewOptions = array_intersect_key($availableLabels, array_flip($viewOptions));

        if (!empty($viewOptions) && count($viewOptions) >= 1) {
            // Make a hidden field
            if (count($viewOptions) == 1) {
                $this->addElement('hidden', 'auth_view', array('value' => key($viewOptions)));
                // Make select box
            } else {
                $this->addElement('Select', 'auth_view', array(
                    'label' => 'Privacy',
                    'description' => 'Who can see this post?',
                    'multiOptions' => $viewOptions,
                    'value' => key($viewOptions),
                ));
                $this->auth_view->getDecorator('Description')->setOption('placement', 'append');
            }
        }

        // Element: auth_comment
        $commentOptions = (array) Engine_Api::_()->authorization()->getAdapter('levels')->getAllowed('whmedia_project', $user, 'auth_comment');
        $commentOptions = array_intersect_key($availableLabels, array_flip($commentOptions));

        if (!empty($commentOptions) && count($commentOptions) >= 1) {
            // Make a hidden field
            if (count($commentOptions) == 1) {
                $this->addElement('hidden', 'auth_comment', array('value' => key($commentOptions)));
                // Make select box
            } else {
                $this->addElement('Select', 'auth_comment', array(
                    'label' => 'Comment Privacy',
                    'description' => 'Who can post comments on this post?',
                    'multiOptions' => $commentOptions,
                    'value' => key($commentOptions),
                ));
                $this->auth_comment->getDecorator('Description')->setOption('placement', 'append');
            }
        }
        $this->addElement('Hidden', 'task', array('value' => 'save_project'));
        $this->addElement('Hidden', 'project_id', array('value' => ''));

        $this->addElement('Button', 'submit', array(
            'label' => Zend_Registry::get('Zend_Translate')->_('Post'),
            'type' => 'submit',
            'decorators' => array('ViewHelper')
        ));
    }

    public function setErrorMode($is_error){
        $this->_errorsExist = $is_error;
    }

}
