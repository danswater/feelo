<?php

class Whmedia_Form_Admin_Global extends Engine_Form
{
  public function init()
  {
    
    $this
      ->setTitle('Global Settings')
      ->setDescription('These settings affect all members in your community.');

    $this->addElement('Text', 'media_per_page', array(
      'label' => 'Entries Per Page',
      'description' => 'How many media will be shown per page? (Enter a number between 1 and 999)',
      'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('media_per_page', 15),
      'required' => true,
      'validators' => array('Digits', array('validator' => 'Between', 'options' => array(1, 999)))
    ));
    
    $this->addElement('Text', 'whvideo_ffmpeg_path', array(
      'label' => 'Path to FFMPEG',
      'description' => 'Please enter the full path to your FFMPEG installation. (Environment variables are not present)',
      'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('whvideo_ffmpeg_path', ''),
    ));

    $this->addElement('Text', 'whvideo_flvtool2_path', array(
      'label' => 'Path to FLVtool2',
      'description' => 'Please enter the full path to your FLVtool2 installation. (Environment variables are not present)',
      'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('whvideo_flvtool2_path', ''),
    ));

    $this->addElement('Text', 'whvideo_flvtool2_faad', array(
      'label' => 'Path to FAAD',
      'description' => 'Please enter the full path to your FAAD installation. (Environment variables are not present). FAAD is the fastest ISO AAC audio decoder ussed to convert 5.1 audio in 2 channels',
      'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('whvideo_flvtool2_faad', ''),
    ));

    $this->addElement('Text', 'embed_ly_key', array(
      'label' => 'Embed.ly API key',
      'description' => 'Embedly provides a powerful API to convert standard URLs into embedded videos, images, and rich article previews from 250 leading providers.',
      'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('embed_ly_key', ''),
      'maxlength' => 100,
      'allowEmpty' => true,
      'required' => false,
      'filters' => array('StringTrim', new Engine_Filter_StringLength(array('min' => 1, 'max' => '100'))),  
    ));
    
    $this->addElement('Radio', 'both_video_format', array(
      'label' => 'Convert video for Ipad',
      'description' => 'Video will be also converted to mp4 format (in addition to flv). So users will be able to play it on mobile devices without Flash installed. Note, this will require more disk space on your server',
      'multiOptions' => array(
        1 => 'Yes, do that.',
        0 => 'No, thanks.'
      ),
      'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('both_video_format', 0),
    ));

    $this->addElement('Radio', 'hd_video_format', array(
      'label' => 'Convert video for HD',
      'description' => 'Additional high-quality flv file will be created. Make sure you will have enough disk space on your server to enable this option. HD video files requires 1.5-2 times more disk space than regular format.',
      'multiOptions' => array(
        1 => 'Yes, do that.',
        0 => 'No, thanks.'
      ),
      'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('hd_video_format', 0),
    ));

    //Add form elements for max image dimension

    $this->addElement('Text', 'image_width', array(
      'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('image_width', '600'),
      'size' => 5,
      'validators' => array('Digits', array('validator' => 'Between', 'options' => array(1, 10000))),
      'style' => 'width: auto;',
      'label' => 'Width',
      'description' => 'px'
    ));

    $this->addElement('Text', 'image_height', array(
      'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('image_height', '900'),
      'size' => 5,
      'style' => 'width: auto;',
      'validators' => array('Digits', array('validator' => 'Between', 'options' => array(1, 10000))),
      'label' => 'Height',
      'description' => 'px'
    ));
    $this->image_width->clearDecorators()
          ->addDecorator('ViewHelper')
          ->addDecorator('Label')
          ->addDecorator('Description', array('placement' => Zend_Form_Decorator_Abstract::APPEND, 'tag' => 'span', 'class' => 'null', 'escape' => false));
    $this->image_height->clearDecorators()
          ->addDecorator('ViewHelper')
          ->addDecorator('Label')
          ->addDecorator('Description', array('placement' => Zend_Form_Decorator_Abstract::APPEND, 'tag' => 'span', 'class' => 'null', 'escape' => false));
    $this->addDisplayGroup(array('image_width',
                                 'image_height'),
                           'image_size'
                          );
    $this->image_size
          ->addDecorator('viewScript', array(
                                              'viewScript' => '_global_form.tpl',
                                              'placement'  => '',
                                              'data' => array('label' => $this->getView()->translate( 'Image Size'),
                                                              'description' => $this->getView()->translate('Enter max size of images on page: View Project.'))
                                              ));

    //Add form elements for PDF dimension

    $this->addElement('Text', 'pdf_width', array(
      'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('pdf_width', '650'),
      'size' => 5,
      'validators' => array('Digits', array('validator' => 'Between', 'options' => array(1, 10000))),
      'style' => 'width: auto;',
      'label' => 'Width',
      'description' => 'px'
    ));

    $this->addElement('Text', 'pdf_height', array(
      'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('pdf_height', '794'),
      'size' => 5,
      'style' => 'width: auto;',
      'validators' => array('Digits', array('validator' => 'Between', 'options' => array(1, 10000))),
      'label' => 'Height',
      'description' => 'px'
    ));
    $this->pdf_width->clearDecorators()
         ->addDecorator('ViewHelper')
         ->addDecorator('Label')
         ->addDecorator('Description', array('placement' => Zend_Form_Decorator_Abstract::APPEND, 'tag' => 'span', 'class' => 'null', 'escape' => false));
    $this->pdf_height->clearDecorators()
         ->addDecorator('ViewHelper')
         ->addDecorator('Label')
         ->addDecorator('Description', array('placement' => Zend_Form_Decorator_Abstract::APPEND, 'tag' => 'span', 'class' => 'null', 'escape' => false));
    $this->addDisplayGroup(array('pdf_width',
                                 'pdf_height'),
                           'pdf_size'
                          );
    $this->pdf_size
          ->addDecorator('viewScript', array(
                                              'viewScript' => '_global_form.tpl',
                                              'placement'  => '',
                                              'data' => array('label' => $this->getView()->translate('PDF Size'),
                                                              'description' => $this->getView()->translate('Enter size of PDF documents on view project page.'))
                                              ));
    
    //Add form elements for video Dimension
    $video_size = 1;
    $video_width = Engine_Api::_()->getApi('settings', 'core')->getSetting('video_width', false);
    $video_height = Engine_Api::_()->getApi('settings', 'core')->getSetting('video_height', false);
    if ($video_width and $video_height) {
        if ($video_width == 320 and $video_height == 240) $video_size = 1;
        if ($video_width == 480 and $video_height == 360) $video_size = 2;
        if ($video_width == 640 and $video_height == 480) $video_size = 3;
        if ($video_width == 480 and $video_height == 270) $video_size = 4;
        if ($video_width == 640 and $video_height == 360) $video_size = 5;
    }

    $this->addElement('Select', 'video_resolution', array(
          'label' => 'Video Size',
          'multiOptions' => array('Aspect ratio 4:3' => array(1 => '320x240',
                                                              2 => '480x360',
                                                              3 => '640x480'),
                                  'Aspect ratio 16:9' => array(4 => '480x270',
                                                               5 => '640x360')),
          'description' => 'Select size of videos on view project page.',
          'value' => $video_size
          
        ));
    

    //Add form elements for thumb Dimension

    $this->addElement('Text', 'thumb_width', array(
      'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('thumb_width', '200'),
      'size' => 5,
      'style' => 'width: auto;',
      'validators' => array('Digits', array('validator' => 'Between', 'options' => array(1, 10000))),
      'label' => 'Width',
      'description' => 'px'
    ));

    $this->addElement('Text', 'thumb_height', array(
      'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('thumb_height', '156'),
      'size' => 5,
      'validators' => array('Digits', array('validator' => 'Between', 'options' => array(1, 10000))),
      'style' => 'width: auto;',
      'label' => 'Height',
      'description' => 'px'
    ));
    $this->thumb_width->clearDecorators()
          ->addDecorator('ViewHelper')
          ->addDecorator('Label')
          ->addDecorator('Description', array('placement' => Zend_Form_Decorator_Abstract::APPEND, 'tag' => 'span', 'class' => 'null', 'escape' => false));
    $this->thumb_height->clearDecorators()
          ->addDecorator('ViewHelper')
          ->addDecorator('Label')
          ->addDecorator('Description', array('placement' => Zend_Form_Decorator_Abstract::APPEND, 'tag' => 'span', 'class' => 'null', 'escape' => false));
    $this->addDisplayGroup(array('thumb_width',
                                 'thumb_height'),
                           'thumb_size'
                          );
    $this->thumb_size
          ->addDecorator('viewScript', array(
                                              'viewScript' => '_global_form.tpl',
                                              'placement'  => '',
                                              'data' => array('label' => $this->getView()->translate('Thumbnails size'),    
                                                              'description' => $this->getView()->translate('Enter size of thumbs(reduced images) on page: Browse Projects.'))
                                              ));

    $this->addElement('Text', 'url_main_world', array(
      'label' => "Replace 'whmedia' url",  
      'description' => "Url is set to 'whmedia' by default to ensure full compatibility with other plugins. But you can easily change it enter preferred title in the field below. Examples: media, portfolio, etc. (only letters (a-z), numbers (0-9) and lowercase are allowed)",
      'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('url_main_world', 'whmedia'),
      'required' => true,
      'allowEmpty' => false,
      'validators' => array(
                            array('Regex', true, array('/^[a-z][a-z0-9-_]+$/'))
                      ),
      'ErrorMessages' => array('Please complete this field - it is required. Pick a lowercase, alphanumeric, "-" and "_" only.')
    ));

    $this->addElement('Radio', 'arrow_sliding', array(
      'label' => 'Sliding images with keyboard arrows',
      'description' => 'Enable sliding images with keyword arrows (left, right and space keys). Note, keys may not work correctly with flash objects (videos, embedded documents)',
      'multiOptions' => array(
        1 => 'Yes, do that.',
        0 => 'No, thanks.'
      ),
      'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('arrow_sliding', 1),
    ));
    
    $this->addElement('Radio', 'mime_info_method', array(
      'label' => 'MIME info method (advanced)',
      'description' => 'This option defines a method of file types determining',
      'required' => true,
      'allowEmpty' => false,  
      'multiOptions' => array(
        1 => "PHP function 'mime_content_type'.",
        2 => "PHP function 'finfo_file'.",
        3 => "External shell command 'file' (Linux server only)."  
      ),
      'value' => Engine_Api::_()->whmedia()->check_mime_info()
    ));

// Add submit button
    $this->addElement('Button', 'submit', array(
      'label' => 'Save Changes',
      'type' => 'submit',
      'ignore' => true
    ));

  }
}
