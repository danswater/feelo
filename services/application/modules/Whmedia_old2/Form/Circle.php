<?php

class Whmedia_Form_Circle extends Engine_Form {

    public function init() {
        $this
                ->setMethod('post')
                ->setAttrib('class', 'global_form_box')
                ->setAction(Zend_Controller_Front::getInstance()->getRouter()->assemble(array('format' => 'smoothbox')));


        $label = new Zend_Form_Element_Text('title');
        $label->setLabel('Box title')
                ->addValidator('NotEmpty')
                ->setRequired(true)
                ->setAttrib('class', 'text');


        $this->addElements(array(
            $label
        ));
        
        $this->addElement('checkbox', 'public', array(
            'label' => 'Public'
        ));

        $this->addElement('radio', 'freq', array(
            'label' => 'Frequency',
            'multioptions' => array(
                '0' => 'Show Everyday',
                '1' => 'Show 3 Times a Week',
                '2' => 'Show Once a Week',
                '3' => 'Not Show at All'
            ),
            'required' => true,
            'allowEmpty' => false
        ));


        // Buttons
        $this->addElement('Button', 'submit', array(
            'label' => 'Add Box',
            'type' => 'submit',
            'ignore' => true,
            'decorators' => array('ViewHelper')
        ));

        $this->addElement('Cancel', 'cancel', array(
            'label' => 'cancel',
            'link' => true,
            'prependText' => ' or ',
            'href' => '',
            'onClick' => 'javascript:parent.Smoothbox.close();',
            'decorators' => array(
                'ViewHelper'
            )
        ));
        $this->addDisplayGroup(array('submit', 'cancel'), 'buttons');
    }

}