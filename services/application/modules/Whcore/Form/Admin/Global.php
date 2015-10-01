<?php

class Whcore_Form_Admin_Global extends Engine_Form {

    public function init() {

        $this
                ->setTitle('Global Settings')
                ->setDescription('These settings affect all members in your community.');

        $this->addElement('Radio', 'wh_facebook_type', array(
            'label' => 'Use Facebook App ID',
            'description' => 'This option allows to share media files to Facebook using Facebook App ID. Enable it, if SocialEngine already integrated to Facebook. Notice, you can fill the field below with APP ID if you don\'t use Facebook integration for the whole website.',
            'required' => true,
            'allowEmpty' => false,
            'multiOptions' => array(
                '0' => 'No',
                '1' => 'Yes',
            ),
            'onclick' => 'updateFields();',
            'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('wh_facebook_type'),
        ));

        $this->addElement('Text', 'wh_facebook_appid', array(
            'label' => '',
            'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('wh_facebook_appid', ''),
            'filters' => array('StringTrim'),
        ));
        $this->wh_facebook_appid->addDecorator('HtmlTag2', array('tag' => 'div', 'class' => 'grouped', 'style' => 'padding-bottom: 15px;overflow: hidden;'));

        $this->addElement('Button', 'submit', array(
            'label' => 'Save Changes',
            'type' => 'submit',
            'ignore' => true
        ));
    }

}