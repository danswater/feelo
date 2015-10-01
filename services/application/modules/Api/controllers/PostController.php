<?php
class Api_PostController extends Zend_Rest_Controller {
    public function init() {
        $this->_helper->layout ()->disableLayout ();
        $this->_helper->viewRenderer->setNoRender ( true );
        
        $this->_helper->AjaxContext ()
            ->addActionContext ( 'get', 'json' )
            ->addActionContext ( 'post', 'json' )
            ->addActionContext ( 'new', 'json' )
            ->addActionContext ( 'edit', 'json' )
            ->addActionContext ( 'put', 'json' )
            ->addActionContext ( 'delete', 'json' )
            ->initContext ( 'json' );
    }
    public function indexAction() {
        $this->_helper->json ( array (
                'action' => 'index' 
        ) );
    }
    public function getAction() {
        $this->_forward('index');
    }
    public function newAction() {
        $this->_forward ( 'index' );
    }
    public function postAction() {
        $token = $this->_getParam ( 'token', null );

        $table = Engine_Api::_ ()->getDbTable ( 'auth', 'api' );
        $select = $table->select ();
        $select->where ( 'token = ?', $token );
        
        $auth = $table->fetchRow ( $select );
        
        if (count ( $auth ) != 1) {
            return $this->_forward ( 'forbidden' );
        }
        // if ($auth->expire_date < time())
        //    return $this->_forward('expired');
        
        $user = Engine_Api::_ ()->user ()->getUser ( $auth->user_id );
        Engine_Api::_()->user()->setViewer( $user );
        
        date_default_timezone_set( $user->timezone );
        
        $values     = $this->getRequest()->getPost();
        $objProject = Engine_Api::_()->getApi( 'project', 'api' );
        
        $files = $this->formatToFiles( $values );

        $arrResultSet  = $objProject->uploadFeed( $user, $files  );

        if( !empty( $arrResultSet[ 'data' ] ) ) {
            $values[ 'project_id' ] = $arrResultSet[ 'data' ][ 'project_id' ];
        
            $arrResultSet  = $objProject->feedDetails( $user, $values );            
        }
        
        $this->getHelper( 'json' )->sendJson( $arrResultSet );
        
    }
    
    public function formatToFiles( $values ) {
        if ( !empty( $_FILES ) ) {
            $extension = ltrim( strrchr( basename( $_FILES[ 'Filedata' ][ 'name' ] ), '.'), '.');
            $newName = $_FILES[ 'Filedata' ][ 'tmp_name' ] . '.' .$extension;
            rename( $_FILES[ 'Filedata' ][ 'tmp_name' ], $newName );
            $_FILES[ 'Filedata' ][ 'tmp_name' ] = $newName;
            return $_FILES;
        }
        
        if( empty( $values[ 'Filedata' ] ) ) {
            return null;
        }

        $files = array();
        $tmpLocation = '/tmp/php'. time();
    
        $location = $tmpLocation;
        $files[ 'Filedata' ][ 'name' ] = $values[ 'title' ].'.jpg';     
        $files[ 'Filedata' ][ 'type' ] = 'image/jpeg';
        $files[ 'Filedata' ][ 'tmp_name' ] = $this->base64_to_jpeg( $values[ 'Filedata' ], $tmpLocation );
        $files[ 'Filedata' ][ 'error' ] = 0;
        $files[ 'Filedata' ][ 'size' ] = 0;
    
        return $files;
    }
    
    public function base64_to_jpeg( $base64_string, $output_file ) {
        $ifp = fopen( $output_file, "wb" ); 
        fwrite( $ifp, base64_decode( $base64_string) ); 
        fclose( $ifp ); 
        return( $output_file ); 
    } 
/*  
    public function createProject() {
        $newProject = Engine_Api::_()->getItemTable('whmedia_project')->createRow();
        $newProject->user_id = Engine_Api::_()->user()->getViewer()->getIdentity();
        $newProject->owner_type = 'user';
        $newProject->search = 1;
        $newProject->is_published = 0;
        $newProject->save();

        return $newProject;
    }
    
    public function uploadAction() {
    
        $translate = Zend_Registry::get('Zend_Translate');
        try {
    
            if (!$this->getRequest()->isPost()) {
                throw new Engine_Exception('Invalid request method');
            }
    
            $values = $this->getRequest()->getPost();
    
            if (empty($values['Filename'])) {
                throw new Engine_Exception('No file');
            }
            if (!isset($_FILES['Filedata']) || !is_uploaded_file($_FILES['Filedata']['tmp_name'])) {
                throw new Engine_Exception('Invalid Upload or file too large');
            }
        } catch (Exception $e) {
            $this->_helper->json(array('status' => false,
                    'error' => $translate->_($e->getMessage())));
            return;
        }
    
        try {
            if ($this->_getParam('project_id', 0) > 0)
                $newProject = Engine_Api::_()->getItem('whmedia_project', $this->_getParam('project_id'));
            else
                $newProject = $this->createProject();
    
            Engine_Api::_()->core()->setSubject($newProject);
    
            $file_id = Engine_Api::_()->whmedia()->uploadmedia($_FILES['Filedata']);
            $this->view->media = $media = Engine_Api::_()->getItem('whmedia_media', $file_id);
    
            $newProject->cover_file_id = $file_id;
            $newProject->save();
    
            if (Engine_Api::_()->core()->getSubject()->is_published) {
                $wh_session = new Zend_Session_Namespace('whmedia_new_media');
                $session_key = 'activity_' . Engine_Api::_()->core()->getSubject()->getIdentity();
                $api = Engine_Api::_()->getDbtable('actions', 'activity');
                if (!isset($wh_session->$session_key)) {
                    $wh_session->$session_key = $action = $api->addActivity(Engine_Api::_()->user()->getViewer(), Engine_Api::_()->core()->getSubject(), 'whmedia_media_new', null);
                } else {
                    $action = $wh_session->$session_key;
                }
                $api->attachActivity($action, $media, Activity_Model_Action::ATTACH_NORMAL);
            }
            $media->save();
    
            $this->view->form = $form = new Whmedia_Form_Create();
            $form->populate($newProject->toArray());
            $form->project_id->setValue($newProject->getIdentity());
            $form->setAction($this->view->url(array('action' => 'index')));
            if ($this->_getParam('project_id', 0) > 0)
                $form->submit->setLabel('Save Changes');
    
            $this->_helper->json(array('status' => true,
                    'html' => $this->view->render('project/_media_embedded.tpl'),
                    'name' => $_FILES['Filedata']['name'],
                    'media_id' => $file_id,
                    'form' => $form));
        } catch (Exception $e) {
            $this->_helper->json(array('status' => false,
                    'error' => $translate->_("DataBase error.")));
            return;
        }
    }
    
    public function createAction() {
        $viewer = Engine_Api::_()->user()->getViewer();
    
        $file_types = Zend_Json::decode(Engine_Api::_()->authorization()->getPermission($viewer->level_id, 'whmedia_project', 'file_type'));
        $type_name = '';
        $exts = '';
        $types_array = array();
        if (in_array('image', $file_types)) {
            $type_name .= 'images,';
            $exts .= '*.jpg; *.jpeg; *.gif; *.png; ';
            $types_array[] = 'Images (*.jpg; *.jpeg; *.gif; *.png)';
        }
        if (in_array('video', $file_types)) {
            $type_name .= 'videos,';
            $exts .= '*.mpeg; *.mp4; *.mkv; *.mpg; *.mpe; *.qt; *.mov; *.avi; ';
            $types_array[] = 'Videos (*.mpeg; *.mp4; *.mkv; *.mpg; *.mpe; *.qt; *.mov; *.avi)';
        }
        $type_name = rtrim($type_name, ',');
        $max_up = @ini_get('upload_max_filesize');
        $max_post = @ini_get('post_max_size');
        if (( (int) $max_up) < ( (int) $max_post )) {
            $fileSizeMax = $max_up;
        } else {
            $fileSizeMax = $max_post;
        }
        $fileSizeMax = trim($fileSizeMax);
    
        switch (strtolower(substr($fileSizeMax, -1))) {
            case 'g':
                $fileSizeMax = $fileSizeMax * 1024 * 1024 * 1024;
                break;
            case 'm':
                $fileSizeMax = $fileSizeMax * 1024 * 1024;
                break;
            case 'k':
                $fileSizeMax = $fileSizeMax * 1024;
                break;
        }
    
        $this->view->file_types = "{'$type_name': '$exts'}";
        $this->view->fileSizeMax = $fileSizeMax;
        $this->view->file_types_array = $types_array;
        $language = $this->view->locale()->getLocale()->__toString();
        $languages = array(
                'en', 'ar', 'ca', 'el', 'fr', 'hy', 'ka', 'ml', 'pl', 'si', 'te', 'vi',
                'az', 'ch', 'gl', 'ia', 'kl', 'mn', 'ps', 'sk', 'th', 'zh', 'be', 'cs',
                'es', 'gu', 'id', 'ko', 'ms', 'pt', 'sl', 'tr', 'zu', 'bg', 'cy', 'et',
                'he', 'ii', 'lb', 'nb', 'ro', 'sq', 'tt', 'bn', 'da', 'eu', 'hi', 'is',
                'lt', 'nl', 'ru', 'sr', 'tw', 'br', 'de', 'fa', 'hr', 'it', 'lv', 'nn',
                'sc', 'sv', 'uk', 'bs', 'dv', 'fi', 'hu', 'ja', 'mk', 'no', 'se', 'ta',
                'ur',
        );
        if (!in_array($language, $languages)) {
            list($language) = explode('_', $language);
            if (!in_array($language, $languages)) {
                $this->view->language = 'en';
            } else {
                $this->view->language = $language;
            }
        } else {
            $this->view->language = $language;
        }
    }
*/  
    public function editAction() {
        $this->_forward ( 'index' );
    }
    public function putAction() {
        $this->_forward ( 'index' );
    }
    public function deleteAction() {
        $this->_forward ( 'index' );
    }
    public function headAction() {
        $this->_forward ( 'index' );
    }
    
    public function forbiddenAction(){
        
        $message = array('message' => 'Token provided is invalid');
        
        $this->_helper->json ( array (
                'error' => $message
        ) );
    }
    
    public function expiredAction($viewer) {
        
        $message = array('message' => 'Token expired');
        
        $this->_helper->json ( array (
                'error' => $message
        ) );
        
    }
}
