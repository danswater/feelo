<?php

class Whmedia_IndexController extends Whmedia_Controller_Action_Follow {

    public function init() {
        parent::init();
        // Render
        $this->_helper->content->setEnabled();
    }

    public function indexAction() {
        if (!$this->_helper->requireAuth()->setAuthParams('whmedia_project', null, 'view')->isValid())
            return;
        $viewer = Engine_Api::_()->user()->getViewer();
        $this->view->whmedia_title = Zend_Registry::get('Zend_Translate')->_('Media');
        $this->view->form = $form = Whmedia_Form_Search::getInstance();
        $url_cat = $this->getFrontController()->getRouter()->assemble(array('category' => ''), 'whmedia_category', true);
        $url = $this->getFrontController()->getRouter()->assemble(array(), 'whmedia_default', true);
        $form->category->setAttrib('onchange', 'if (this.value == 0) {this.form.set("action", "' . $url . '");} else {this.form.set("action", "' . $url_cat . '/" + this.value);}this.form.submit();');

        // Populate form
        $this->view->categories = $categories = Engine_Api::_()->whmedia()->getCategories();
        // Process form
        if ($this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost())) {
            $values = $form->getValues();
        } else {
            $values = array();
            if ($this->_hasParam('category')) {
                $category = $this->_getParam('category');
                $tmp_categories = $categories->getRowMatching('url', $category);
                if (empty($tmp_categories)) {
                    return $this->_helper->redirector->gotoRouteAndExit(array(), 'whmedia_default');
                } else {
                    $values['category'] = $category;
                    $form->category->setValue($category);
                }
            }
        }

        if ($user_whmedia_id = $this->_getParam('user_id', false)) {
            $values['user_id'] = $user_whmedia_id;
            $this->view->owner = Engine_Api::_()->getItem('user', $user_whmedia_id);
        }
        // Do the show thingy
        if (@$values['show'] == 2) {

            $table = Engine_Api::_()->getItemTable('user');
            $select = $viewer->membership()->getMembersSelect('user_id');
            $friends = $table->fetchAll($select);
            // Get stuff
            $ids = array();
            foreach ($friends as $friend) {
                $ids[] = $friend->user_id;
            }
            //unset($values['show']);
            $values['users'] = $ids;
        }
        $this->view->assign($values);
        $values['is_published'] = true;
        $this->view->paginator = $paginator = Engine_Api::_()->whmedia()->getWhmediaPaginator($values);
        $items_per_page = Engine_Api::_()->getApi('settings', 'core')->getSetting('media_per_page', 15);
        $paginator->setCurrentPageNumber($this->_getParam('page'));
        $paginator->setItemCountPerPage($items_per_page);
        $this->view->can_create = $this->_helper->requireAuth()->setAuthParams('whmedia_project', null, 'create')->checkRequire();

        $this->view->thumb_width = 264;
        $this->view->followApi = Engine_Api::_()->getDbtable('follow', 'whmedia');
        if (Zend_Controller_Action_HelperBroker::getStaticHelper('contextSwitch')->getCurrentContext() == 'html') {
            $this->view->only_items = true;
            $this->_helper->layout->disableLayout(true);
            $this->_helper->content->setEnabled(false);
        } else {
            $this->view->only_items = false;
        }
    }

    public function viewAction() {
        $project = Engine_Api::_()->getItem('whmedia_project', $this->_getParam('project_id'));
        if (!Engine_Api::_()->core()->hasSubject('whmedia_project') and $project instanceof Whmedia_Model_Project) {
            Engine_Api::_()->core()->setSubject($project);
        }
        if (!$this->_helper->requireSubject()->isValid())
            return;
        $viewer = Engine_Api::_()->user()->getViewer();
        if (!$project->is_published) {
            if (!$project->isOwner($viewer) and !Engine_Api::_()->whmedia()->isAdmin($viewer)) {
                Engine_Api::_()->core()->clearSubject();
                $this->_helper->content->setEnabled(false);
                return $this->_helper->Message("Project isn't published.", false, false)->setError();
            }
        }
        if (!Engine_Api::_()->whmedia()->isAdmin($viewer)) {
            if (!$this->_helper->requireAuth()->setAuthParams($project, $viewer, 'view')->isValid()) {
                Engine_Api::_()->core()->clearSubject();
                $this->_helper->content->setEnabled(false);
                return;
            }
        }
        $project->project_views++;
        $project->save();

        $this->view->project = $project;
        $this->view->categories = Engine_Api::_()->whmedia()->getCategories();
        $this->view->img_width = (int) Engine_Api::_()->getApi('settings', 'core')->getSetting('image_width', '600') + 90;
        $this->view->isOwner = $isOwner = $project->isOwner($viewer);
        $addition_media_filtr = array();
        if (!$isOwner and !Engine_Api::_()->whmedia()->isAdmin($viewer)) {
            $addition_media_filtr[] = 'encode <= 1';
        }
        $this->view->medias = $project->getMedias($addition_media_filtr);
        $this->view->allow_d_orig = Engine_Api::_()->authorization()->context->isAllowed($project, 'everyone', 'allow_d_orig');
        $this->view->isMobile = Engine_Api::_()->whmedia()->isMobile();
    }

    public function manageAction() {
        if (!$this->_helper->requireUser()->isValid())
            return;
        if (!$can_create = $this->_helper->requireAuth()->setAuthParams('whmedia_project', null, 'create')->checkRequire())
            return;
        $viewer = Engine_Api::_()->user()->getViewer();
        $this->view->whmedia_title = Zend_Registry::get('Zend_Translate')->_('Media');
        $this->view->form = $form = Whmedia_Form_Search::getInstance();
        $form->removeElement('show');
        $multi = $form->orderby->getMultiOptions();
        unset($multi['project_user']);
        $form->orderby->setMultiOptions($multi);
        // Process form
        $values = array();
        if ($form->isValid($this->getRequest()->getPost()))
            $values = $form->getValues();
        $values['user_id'] = $viewer->getIdentity();
        $this->view->paginator = $paginator = Engine_Api::_()->whmedia()->getWhmediaPaginator($values);
        $items_per_page = Engine_Api::_()->getApi('settings', 'core')->getSetting('media_per_page', 15);
        $paginator->setCurrentPageNumber($this->_getParam('page'));
        $paginator->setItemCountPerPage($items_per_page);
        $this->view->can_create = $can_create;
        $this->view->isApple = Engine_Api::_()->whmedia()->isApple();
        $this->view->isMobile = Engine_Api::_()->whmedia()->isMobile();

        $this->view->thumb_width = 264;
        $this->view->followApi = Engine_Api::_()->getDbtable('follow', 'whmedia');
        if (Zend_Controller_Action_HelperBroker::getStaticHelper('contextSwitch')->getCurrentContext() == 'html') {
            $this->view->only_items = true;
            $this->_helper->layout->disableLayout(true);
            $this->_helper->content->setEnabled(false);
        } else {
            $this->view->only_items = false;
        }
    }

    public function showMediaAction() {
        if (!$this->_helper->requireAuth()->setAuthParams('whmedia_project', null, 'view')->isValid())
            return;
        $viewer = Engine_Api::_()->user()->getViewer();
        $media = (int) $this->_getParam('media', false);
        if (empty($media)) {
            return $this->_helper->Message('Media is empty.', false, false)->setError();
        }
        $this->view->media = $media = Engine_Api::_()->getItem('whmedia_media', $media);
        if (empty($media)) {
            return $this->_helper->Message('Media is empty.', false, false)->setError();
        }
        $this->view->project = $project = $media->getProject();
        if (!$this->_helper->requireAuth()->setAuthParams($project, $viewer, 'view')->isValid())
            return;
        $this->_helper->content->setEnabled(false);
        $project_medias = $project->getMedias(array('is_text = 0'));
        $project_medias_count = $project_medias->count(array('is_text = 0'));
        $this->view->previous = false;
        $this->view->next = false;
        $this->view->hot_keys_enable = Engine_Api::_()->getApi('settings', 'core')->getSetting('arrow_sliding', 1);
        if ($project_medias_count > 1) {
            foreach ($project_medias as $project_media) {
                if ($project_media->getIdentity() == $media->getIdentity()) {
                    $key = $project_medias->key();
                    if ($key > 0) {
                        $this->view->previous = array('route' => 'whmedia_default', 'action' => 'show-media', 'media' => $project_medias->getRow($key - 1)->getIdentity(), 'format' => 'smoothbox');
                    }
                    if ($key < ($project_medias_count - 1)) {
                        $this->view->next = array('route' => 'whmedia_default', 'action' => 'show-media', 'media' => $project_medias->getRow($key + 1)->getIdentity(), 'format' => 'smoothbox');
                    }
                    break;
                }
            }
        }
    }

    public function popularAction() {
        $params = array('page' => $this->_getParam('page', 1),
            'limit' => 10,
            'is_published' => true,
            'orderby' => 'count_likes');
        switch ($this->_getParam('time_period')) {
            case 'today':
                $res_time = 86400;
                break;
            case 'month':
                $res_time = 2592000;
                break;
            case 'week':
            default :
                $res_time = 604800;
                break;
        }
        if (!empty($res_time))
            $params['start_date'] = date('Y-m-d H:i:s', time() - $res_time);
        // Get paginator
        $this->view->paginator = $paginator = Engine_Api::_()->whmedia()->getWhmediaPaginator($params);

        $this->view->thumb_width = 259;
        $this->view->followApi = Engine_Api::_()->getDbtable('follow', 'whmedia');
        if (Zend_Controller_Action_HelperBroker::getStaticHelper('contextSwitch')->getCurrentContext() == 'html') {
            $this->view->only_items = true;
            $this->_helper->layout->disableLayout(true);
            $this->_helper->content->setEnabled(false);
        } else {
            $this->view->only_items = false;
            $this->view->pageTitle = 'Popular Projects';
            $navigation = $this->view->navigation = new Zend_Navigation();

            $navigation->addPage(array(
                'label' => Zend_Registry::get('Zend_Translate')->_('Today'),
                'route' => 'whmedia_project_popular',
                'action' => 'popular',
                'params' => array('time_period' => 'today')
            ));
            $navigation->addPage(array(
                'label' => Zend_Registry::get('Zend_Translate')->_('This week'),
                'route' => 'whmedia_project_popular',
                'action' => 'popular',
                'params' => array('time_period' => 'week')
            ));
            $navigation->addPage(array(
                'label' => Zend_Registry::get('Zend_Translate')->_('This month'),
                'route' => 'whmedia_project_popular',
                'action' => 'popular',
                'params' => array('time_period' => 'month')
            ));
            $navigation->addPage(array(
                'label' => Zend_Registry::get('Zend_Translate')->_('Overall'),
                'route' => 'whmedia_project_popular',
                'action' => 'popular',
                'params' => array('time_period' => 'overall')
            ));
        }
    }

    public function livefeedAction() {
        if (!$this->_helper->requireUser()->isValid())
            return;
        $params = array('page' => $this->_getParam('page', 1),
            'limit' => 10,
            'is_published' => true);
        $this->view->followApi = $followApi = Engine_Api::_()->getDbtable('follow', 'whmedia');
        $viewer = Engine_Api::_()->user()->getViewer();
        $following = $followApi->getFollowing($viewer);
        $this->view->pageTitle = 'Live Feed';
        if ($following->count() > 0) {
            $params['users'] = array();
            foreach ($following as $following_one) {
                $params['users'][] = $following_one->user_id;
            }
        } else {
            return $this->renderScript('index/_nofollowing.tpl');
        }
        // Get paginator
        $this->view->paginator = $paginator = Engine_Api::_()->whmedia()->getWhmediaPaginator($params);

        $this->view->thumb_width = 350;
        $this->view->followApi = Engine_Api::_()->getDbtable('follow', 'whmedia');
        if (Zend_Controller_Action_HelperBroker::getStaticHelper('contextSwitch')->getCurrentContext() == 'html') {
            $this->view->only_items = true;
            $this->_helper->layout->disableLayout(true);
            $this->_helper->content->setEnabled(false);
        } else {
            $this->view->only_items = false;
        }
        $this->renderScript('index/popular.tpl');
    }

    public function activityfeedAction() {
        if (!$this->_helper->requireUser()->isValid())
            return;
        $viewer = Engine_Api::_()->user()->getViewer();
        $lists = Engine_Api::_()->getDbTable('circles', 'whmedia')->fetchAll(array('user_id = ?' => $viewer->getIdentity()));
        if (count($lists)) {
            $this->view->navigation = $navigation = new Zend_Navigation();
            $viewer = Engine_Api::_()->user()->getViewer();

            $box_id = $this->_getParam('box_id');

            $navigation->addPage(array(
                'label' => Zend_Registry::get('Zend_Translate')->_('All'),
                'route' => 'whmedia_project_activityfeed',
                'active' => (!$box_id)
            ));

            foreach ($lists as $box) {
                $navigation->addPage(array(
                    'label' => Zend_Registry::get('Zend_Translate')->_($box->getTitle()),
                    'route' => 'whmedia_project_activityfeed',
                    'params' => array('box_id' => $box->getIdentity()),
                    'active' => ($box_id == $box->getIdentity())
                ));
            }
        }
    }

    public function projectsAction() {
        $value = $this->_getParam('value');
        if ($value != null && strlen($value) >= 3) {
            $table = Engine_Api::_()->getDbTable('projects', 'whmedia');
            $select = $table->select();
            $select->where('title LIKE ?', '%' . $value . '%')
                    ->limit(10);
            foreach ($table->fetchAll($select) as $project) {
                $data[] = array(
                    'type' => 'project',
                    'id' => $project->getIdentity(),
                    'guid' => $project->getGuid(),
                    'label' => $project->getTitle(),
                    'photo' => $this->view->itemPhoto($project, 'thumb.icon'),
                    'url' => $project->getHref(),
                );
            }

            return $this->_helper->json($data);
        }
    }

}
