<?php

class Grandopening_AdminCoverController extends Core_Controller_Action_Admin {

    protected $_basePath;

    public function init() {
        // Check if folder exists and is writable
        if (!file_exists(APPLICATION_PATH . '/public/opening_cover') ||
                !is_writable(APPLICATION_PATH . '/public/opening_cover')) {
            return $this->_forward('error', null, null, array(
                        'message' => 'The public/opening_cover folder does not exist or is not ' .
                        'writable. Please create this folder and set full permissions ' .
                        'on it (chmod 0777).',
                    ));
        }

        // Set base path
        $this->_basePath = realpath(APPLICATION_PATH . '/public/opening_cover');
    }

    public function indexAction() {
        $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
                                                       ->getNavigation('grandopening_admin_main', array(), 'grandopening_admin_main_cover');
        // Get path
        $this->view->path = $path = $this->_getPath();
        $this->view->relPath = $relPath = $this->_getRelPath($path);

        // List files
        $files = array();
        $dirs = array();
        $contents = array();
        $it = new DirectoryIterator($path);
        foreach ($it as $key => $file) {
            $filename = $file->getFilename();
            if (($it->isDot() && $this->_basePath == $path) || $filename == '.' || ($filename != '..' && $filename[0] == '.')) {
                continue;
            }
            $relPath = trim(str_replace($this->_basePath, '', realpath($file->getPathname())), '/\\');
            $ext = strtolower(ltrim(strrchr($file->getFilename(), '.'), '.'));
            if ($file->isDir())
                $ext = null;
            $type = 'image';

            $dat = array(
                'name' => $file->getFilename(),
                'path' => $file->getPathname(),
                'info' => $file->getPathInfo(),
                'rel' => $relPath,
                'ext' => $ext,
                'type' => $type,
                'is_dir' => $file->isDir(),
                'is_file' => $file->isFile(),
                'is_image' => ( $type == 'image' ),
                'is_text' => ( $type == 'text' ),
                'is_markup' => ( $type == 'markup' ),
            );
            if ($it->isDir()) {
                $dirs[$relPath] = $dat;
            } else if ($it->isFile()) {
                $files[$relPath] = $dat;
            }
            $contents[$relPath] = $dat;
        }
        ksort($contents);

        $this->view->paginator = $paginator = Zend_Paginator::factory($contents);
        $paginator->setItemCountPerPage(20);
        $paginator->setCurrentPageNumber($this->_getParam('page', 1));

        $this->view->files = $files;
        $this->view->dirs = $dirs;
        $this->view->contents = $contents;
        
        $this->view->cover = Engine_Api::_()->getApi('settings', 'core')->getSetting('grandopening_cover');
    }

    public function uploadAction() {
        $this->view->path = $path = $this->_getPath();
        $this->view->relPath = $relPath = $this->_getRelPath($path);

        // Check method
        if (!$this->getRequest()->isPost()) {
            return;
        }

        // Check ul bit
        if (null === $this->_getParam('ul')) {
            return;
        }

        // Prepare
        if (empty($_FILES['Filedata'])) {
            $this->view->error = 'File failed to upload. Check your server settings (such as php.ini max_upload_filesize).';
            return;
        }

        // Prevent evil files from being uploaded
        $disallowedExtensions = array('php');
        if (in_array(end(explode(".", $_FILES['Filedata']['name'])), $disallowedExtensions)) {
            $this->view->error = 'File type or extension forbidden.';
            return;
        }


        $info = $_FILES['Filedata'];
        $info['name'] = str_replace(' ', '', $info['name']);
        $targetFile = $path . '/' . $info['name'];
        $vals = array();

        if (file_exists($targetFile)) {
            $deleteUrl = $this->view->url(array('action' => 'delete')) . '?path=' . $relPath . '/' . $info['name'];
            $deleteUrlLink = '<a href="' . $this->view->escape($deleteUrl) . '">' . Zend_Registry::get('Zend_Translate')->_("delete") . '</a>';
            $this->view->error = Zend_Registry::get('Zend_Translate')->_("File already exists. Please %s before trying to upload.", $deleteUrlLink);
            return;
        }

        if (!is_writable($path)) {
            $this->view->error = Zend_Registry::get('Zend_Translate')->_('Path is not writeable. Please CHMOD 0777 the public/admin directory.');
            return;
        }

        // Try to move uploaded file
        if (!move_uploaded_file($info['tmp_name'], $targetFile)) {
            $this->view->error = Zend_Registry::get('Zend_Translate')->_("Unable to move file to upload directory.");
            return;
        }

        $this->view->status = 1;

        // Redirect
        if (null === $this->_helper->contextSwitch->getCurrentContext()) {
            return $this->_helper->redirector->gotoRoute(array('action' => 'index'));
        } else if ('smoothbox' === $this->_helper->contextSwitch->getCurrentContext()) {
            return $this->_forward('success', 'utility', 'core', array(
                        'smoothboxClose' => true,
                        'parentRefresh' => true,
                    ));
        }
    }

    public function deleteAction() {
        $path = $this->_getPath();

        $this->view->fileIndex = $this->_getParam('index');

        if (!file_exists($path)) {
            return $this->_helper->redirector->gotoRoute(array('action' => 'index'));
        }

        $this->view->form = $form = new Core_Form_Admin_File_Delete();
        $form->setAction($_SERVER['REQUEST_URI']);
        if (!$this->getRequest()->isPost()) {
            return;
        }

        $vals = $this->getRequest()->getPost();
        if (!empty($vals['actions']) && is_array($vals['actions'])) {
            $vals['actions'] = join(PATH_SEPARATOR, $vals['actions']);
        }
        $form->isValid($vals);

        $settings = Engine_Api::_()->getApi('settings', 'core');

        if (is_dir($path)) {
            $actions = $vals['actions'];
            if (is_string($actions)) {
                $actions = explode(PATH_SEPARATOR, $actions);
            } else if (!is_array($actions)) {
                $actions = array(); // blegh
            }

            $it = new DirectoryIterator($path);
            foreach ($it as $file) {
                if (!$file->isFile())
                    continue;
                if (in_array($file->getFilename(), $actions)) {
                    if (!unlink($file->getPathname())) {
                        // Blegh
                    }
                    if ($settings->getSetting('grandopening_cover', '') == $file->getFilename()) {
                        $settings->setSetting('grandopening_cover', '');
                    }
                }
            }
        } else if (is_file($path)) {

            if (!@unlink($path)) {
                return $form->addError('Unable to delete');
            }
            if ($settings->getSetting('grandopening_cover', '') == basename($path)) {
                $settings->setSetting('grandopening_cover', '');
            }
        }

        $this->view->status = true;

        // Redirect
        if (null === $this->_helper->contextSwitch->getCurrentContext()) {
            return $this->_helper->redirector->gotoRoute(array('action' => 'index'));
        } else if ('smoothbox' === $this->_helper->contextSwitch->getCurrentContext()) {
            return $this->_forward('success', 'utility', 'core', array(
                        'smoothboxClose' => true,
                        'parentRefresh' => true,
                    ));
        }
    }
    
    public function coverAction() {
        $bg = $this->_getParam('bg', false);
        
        if ($bg) {
            $settings = Engine_Api::_()->getApi('settings', 'core');
            $settings->setSetting('grandopening_cover', $bg);
        }
        $this->_helper->redirector->gotoRoute(array('action' => 'index'));
    }

    protected function _getPath($key = 'path') {
        return $this->_checkPath(urldecode($this->_getParam($key, '')), $this->_basePath);
    }

    protected function _checkPath($path, $basePath) {
        $path = preg_replace('/\.{2,}/', '.', $path);
        $path = preg_replace('/[\/\\\\]+/', '/', $path);
        $path = trim($path, './\\');
        $path = $basePath . '/' . $path;


        $basePath = realpath($basePath);
        $path = realpath($path);

        if ($basePath != $path && strpos($basePath, $path) !== false) {
            return $this->_helper->redirector->gotoRoute(array());
        }

        return $path;
    }

    protected function _getRelPath($path, $basePath = null) {
        if (null === $basePath)
            $basePath = $this->_basePath;

        $path = realpath($path);
        $basePath = realpath($basePath);
        $relPath = trim(str_replace($basePath, '', $path), '/\\');

        return $relPath;
    }

}
