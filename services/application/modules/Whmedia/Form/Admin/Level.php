<?php

class Whmedia_Form_Admin_Level extends Engine_Form
{
  protected $_public;

  public function setPublic($public)
  {
    $this->_public = $public;
  }

  public function init()
  {
    $this
      ->setTitle('Member Level Settings')
      ->setDescription('These settings are applied on a per member level basis. Start by selecting the member level you want to modify, then adjust the settings for that level below.')
      ->setAttrib('name', 'level_settings');

    $this->loadDefaultDecorators();
    $this->getDecorator('Description')->setOptions(array('tag' => 'h4', 'placement' => 'PREPEND'));

    // prepare user levels
    $table = Engine_Api::_()->getDbtable('levels', 'authorization');
    $select = $table->select();
    $user_levels = $table->fetchAll($select);

    foreach ($user_levels as $user_level){
      $levels_prepared[$user_level->level_id]= $user_level->getTitle();
    }

    // category field
    $this->addElement('Select', 'level_id', array(
          'label' => 'Member Level',
          'multiOptions' => $levels_prepared,
          'onchange' => "javascript:window.location.href = en4.core.baseUrl + 'admin/whmedia/level/'+this.value;",
          'ignore' => true
        ));

    $this->addElement('Radio', 'view', array(
      'label' => 'Browse Media',
      'description' => 'Do you want to allow this user level to view media?',
      'multiOptions' => array(
        1 => 'Yes, allow this user level to view media.',
        0 => 'No, deny this user level to view media.'
      ),
      'value' => 0,
    ));
if (!$this->_public)
    {
    $this->addElement('Radio', 'create', array(
      'label' => 'Create Media Posts',
      'description' => 'Do you want to allow this user level to create media posts?',
      'multiOptions' => array(
        1 => 'Yes, allow this user level to create media posts.',
        0 => 'No, deny this user level to create media posts.'
      ),
      'onchange' => "javascript:allow_create(this);",
      'value' => 1,
    ));

    $this->addElement('Text', 'medias_count', array(
      'label' => 'Max number of media files in a Post',
      'description' => 'How many media users can add to post? The field must contain an integer, use zero for unlimited.',
      'value' => 10,
      'required' => true,
      'validators' => array('Digits')
    ));

        // Element: file_type
      $video_dop = (trim(Engine_Api::_()->getApi('settings', 'core')->getSetting('whvideo_ffmpeg_path', ''))) ? '' : $this->getView()->translate(" (Video settings are not set. If you want to enable video uploads %sset them%s)", "<a href='".Zend_Controller_Front::getInstance()->getRouter()->assemble(array('module' => 'whmedia', 'controller' => 'settings', 'action' => 'index'), 'admin_default', true)."'>", "</a>");
      $this->addElement('MultiCheckbox', 'file_type', array(
        'label' => 'File Types',
        'description' => 'Choose what file types can be added to posts by users.',
        'multiOptions' => array(
          'image' => 'Image',
          'video' => $this->getView()->translate('Video').$video_dop,
          'audio' => 'Audio',
          'pdf' => 'Adobe Portable Document Format (PDF)',
          'ppt' => 'PowerPoint Presentation (PPT)'
        ),
        'required' => true,
        'escape' => false  ,
        'value' => array('image', 'video', 'audio'),
      ));
      $this->addElement('Radio', 'save_original', array('label' => 'Save original images',
                                                        'description' => 'Save original images(will require additional disk size)',
                                                        'multiOptions' => array(
                                                                                    1 => 'Yes, save original images.',
                                                                                    0 => 'No'
                                                                               ),
                                                        'value' => 1,
                                                       ));
    // Element: comment
      $this->addElement('Radio', 'comment', array(
        'label' => 'Allow Comments on Posts',
        'description' => 'Do you want to let members of this level comment on posts?',
        'multiOptions' => array(
          1 => 'Yes, allow members to comment on posts.',
          0 => 'No, do not allow members to comment on posts.',
        ),
        'value' => 1,
      ));
      
    // Element: auth_view
      $this->addElement('MultiCheckbox', 'auth_view', array(
        'label' => 'Media Entry Privacy',
        'description' => "Your members can choose from any of the options checked below when they decide who can see their post entries. These options appear on your members 'Create a Project' and 'Edit Project' pages. If you do not check any options, everyone will be allowed to view project.",
        'multiOptions' => array(
          'everyone'            => 'Everyone',
          'registered'          => 'All Registered Members',
          'owner_network'       => 'Friends and Networks',
          'owner_member_member' => 'Friends of Friends',
          'owner_member'        => 'Friends Only',
          'owner'               => 'Just Me'
        ),
        'value' => array('everyone', 'owner_network', 'owner_member_member', 'owner_member', 'owner'),
      ));
      // Element: auth_comment
      $this->addElement('MultiCheckbox', 'auth_comment', array(
        'label' => 'Media Comment Options',
        'description' => 'Your members can choose from any of the options checked below when they decide who can post comments on their entries. If you do not check any options, everyone will be allowed to post comments on entries.',
        'multiOptions' => array(
          'everyone'            => 'Everyone',
          'registered'          => 'All Registered Members',
          'owner_network'       => 'Friends and Networks',
          'owner_member_member' => 'Friends of Friends',
          'owner_member'        => 'Friends Only',
          'owner'               => 'Just Me'
        ),
        'value' => array('everyone', 'owner_network', 'owner_member_member', 'owner_member', 'owner'),
      ));
      $this->addDisplayGroup(array('medias_count', 'user_space', 'media_size', 'file_type','auth_view', 'auth_comment'), 'project_element');
      $this->getDisplayGroup('project_element')->setDecorators(array('FormElements',
                                                                                     array('HtmlTag',
                                                                                           array('tag'=>'div', 
                                                                                                 'id' => 'project_element')
                                                                                           )
                                                                                    )
                                                                              );
    }
    $this->addElement('Button', 'submit', array(
      'label' => 'Save Settings',
      'type' => 'submit',
      'ignore' => true
    ));

  }

  public function  populate(array $values) {
        $res = parent::populate($values);
        $this->hideCheck();
        return $res;
  }
  public function hideCheck() {
      if (!$this->_public) {
        $style = ($this->create->getValue()) ? 'block' : 'none';
        $this->getDisplayGroup('project_element')->getDecorator('HtmlTag')->setOption('style', "display:$style;");
      }
  }
}
