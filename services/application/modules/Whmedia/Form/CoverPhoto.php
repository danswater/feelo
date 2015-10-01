<?php

class Whmedia_Form_CoverPhoto extends Engine_Form{

	public function coverPhotoForm(){
	
		$baseUrl = Zend_Controller_Front::getInstance()->getBaseUrl();
            $form = new Zend_Form(array(
                 "target" => "submit_cover_photo_here",
                 "style"=> "display:none",
                 "id" => "submit_cover_photo"
            ));
            $form->setEnctype(Zend_Form::ENCTYPE_MULTIPART);
            $form->setAction($baseUrl.'/boxes/favphoto');

            $image = new Zend_Form_Element_File('cover_photo');
            $image->setLabel('Upload an image:')
                  ->setAttribs(array(
                        "id" => "cover_photo",
                        "onchange" => "upload_cover_photo()"
                  ))
                  ->setDestination(APPLICATION_PATH.'/public/temporary/')
                  ->setRequired(true)
                  ->setMaxFileSize(10240000) // limits the filesize on the client side
                  ->setDescription('Click Browse and click on the image file you would like to upload');
            $image->addValidator('Count', false, 1);                // ensure only 1 file
            $image->addValidator('Extension', false, 'jpg,jpeg,png,gif');// only JPEG, PNG, and GIFs

            $form->addElement($image);

    return $form;
	}
}