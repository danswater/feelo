<?php

class Whmedia_Bootstrap extends Engine_Application_Bootstrap_Abstract {

    public function __construct($application) {
        parent::__construct($application);
        $this->initViewHelperPath();

        $baseUrl = Zend_Registry::get('StaticBaseUrl');
        $headScript = new Zend_View_Helper_HeadScript();
        $headScript->appendFile($baseUrl . 'externals/autocompleter/Observer.js')
                ->appendFile($baseUrl . 'externals/autocompleter/Autocompleter.js')
                ->appendFile($baseUrl . 'externals/autocompleter/Autocompleter.Local.js')
                ->appendFile($baseUrl . 'externals/autocompleter/Autocompleter.Request.js');
        $script = "window.addEvent('domready', function() {
                    new Autocompleter.Request.JSON('global_search_field', '" . $baseUrl . "whmedia/index/projects', {
                            'minLength': 3,
                            'delay' : 250,
                            'selectMode': 'pick',
                            'autocompleteType': 'message',
                            'multiple': false,
                            'className': 'project-autosuggest',
                            'filterSubset' : true,
                            'tokenFormat' : 'object',
                            'tokenValueKey' : 'label',
                            'injectChoice': function(token){
                                var choice = new Element('li', {
                                    'class': 'autocompleter-choices',
                                    'id':token.label
                                });
                                new Element('a', {
                                    'html' : token.photo + this.markQueryValue(token.label),
                                    'href' : token.url,
                                    'class': 'autocompleter-choice-link'
                                }).inject(choice);
                                this.addChoiceEvents(choice).inject(this.choices);
                                choice.store('autocompleteChoice', token);
                            }
                    });
                  });";
        $headScript->appendScript($script);
    }

    protected function _initRouter() {

        $router = Zend_Controller_Front::getInstance()->getRouter();
        defined('WHMEDIA_URL_WORLD') || define('WHMEDIA_URL_WORLD', Engine_Api::_()->getApi('settings', 'core')->getSetting('url_main_world', 'whmedia'));
        $userConfig = array('create_whproject' => array(
                'route' => WHMEDIA_URL_WORLD . '/create',
                'defaults' => array(
                    'module' => 'whmedia',
                    'controller' => 'project',
                    'action' => 'create'
                )
            ),
            'add_whmedia' => array(
                'route' => WHMEDIA_URL_WORLD . '/addmedia/:project_id',
                'defaults' => array(
                    'module' => 'whmedia',
                    'controller' => 'project',
                    'action' => 'index'
                ),
                'reqs' => array(
                    'project_id' => '\d+',
                ),
            ),
            'user_projects' => array(
                'route' => WHMEDIA_URL_WORLD . '/:user_id',
                'defaults' => array(
                    'module' => 'whmedia',
                    'controller' => 'index',
                    'action' => 'index',
                ),
                'reqs' => array(
                    'user_id' => '\d+'
                )
            ),
            'whmedia_admin_manage_level' => array(
                'route' => 'admin/whmedia/level/:level_id',
                'defaults' => array(
                    'module' => 'whmedia',
                    'controller' => 'admin-level',
                    'action' => 'index',
                    'level_id' => 1
                )
            ),
            'whmedia_project' => array(
                'route' => WHMEDIA_URL_WORLD . '/project/:project_id/:action/*',
                'defaults' => array(
                    'module' => 'whmedia',
                    'controller' => 'project',
                    'action' => 'index',
                ),
                'reqs' => array(
                    'action' => '(index|edit|delmedia|delproject|videourlpreview|delselectedmedia|add-text|edit-text|publish|get-media-content|get-url-content|save-url)',
                    'project_id' => '\d+'
                )
            ),
            'whmedia_default' => array(
                'route' => WHMEDIA_URL_WORLD . '/:controller/:action/*',
                'defaults' => array(
                    'module' => 'whmedia',
                    'controller' => 'index',
                    'action' => 'index',
                ),
                'reqs' => array(
                    'controller' => '\D+',
                    'action' => '\D+',
                )
            ),
            'whmedia_project_view' => array(
                'route' => WHMEDIA_URL_WORLD . '/view/:project_id/:slug',
                'defaults' => array(
                    'module' => 'whmedia',
                    'controller' => 'index',
                    'action' => 'view',
                    'slug' => ''
                ),
                'reqs' => array(
                    'project_id' => '\d+'
                )
            ),
            'whmedia_project_popular' => array(
                'route' => WHMEDIA_URL_WORLD . '/popular/:time_period',
                'defaults' => array(
                    'module' => 'whmedia',
                    'controller' => 'index',
                    'action' => 'popular',
                    'time_period' => 'today'
                ),
                'reqs' => array(
                    'time_period' => '(today|week|month|overall)'
                )
            ),
            'whmedia_project_livefeed' => array(
                'route' => WHMEDIA_URL_WORLD . '/livefeed/*',
                'defaults' => array(
                    'module' => 'whmedia',
                    'controller' => 'index',
                    'action' => 'livefeed'
                )
            ),
            'whmedia_project_activityfeed' => array(
                'route' => WHMEDIA_URL_WORLD . '/activity-feed/*',
                'defaults' => array(
                    'module' => 'whmedia',
                    'controller' => 'index',
                    'action' => 'activityfeed'
                )
            ),
            'whmedia_members' => array('route' => '/follow/:action/:page/*',
                'defaults' => array(
                    'module' => 'whmedia',
                    'controller' => 'members',
                    'action' => 'search',
                    'page' => 1
                ),
                'reqs' => array(
                    'action' => '(search|featured|most-active|new|follow-suggestion)',
                    'page' => '\d+'
                )
            ),
            'whmedia_video_edit_cover' => array('route' => WHMEDIA_URL_WORLD . '/video/:video_id/:action/*',
                'defaults' => array(
                    'module' => 'whmedia',
                    'controller' => 'video',
                    'action' => 'index'
                ),
                'reqs' => array(
                    'action' => '(index|get-frame|set-cover)',
                    'video_id' => '\d+'
                )
            ),
            'whmedia_category' => array(
                'route' => WHMEDIA_URL_WORLD . '/category/:category',
                'defaults' => array(
                    'module' => 'whmedia',
                    'controller' => 'index',
                    'action' => 'index',
                )
            ),
            'whmedia_circles' => array(
                'route' => 'boxes/:action/*',
                'defaults' => array(
                    'module' => 'whmedia',
                    'controller' => 'circles',
                    'action' => 'index',
                )
            ),
            'whmedia_circles_action' => array(
                'route' => 'box/:action/:box_id/*',
                'defaults' => array(
                    'module' => 'whmedia',
                    'controller' => 'circles',
                    'action' => 'view',
                ),
                'reqs' => array(
                    'action' => '(view|remove|edit|delete)',
                    'box_id' => '\d+'
                )
            )
        );
        $router->addConfig(new Zend_Config($userConfig));

        return $router;
    }

    protected function _initHelper() {
        $priority = Zend_Controller_Action_HelperBroker::getStack()->getNextFreeHigherPriority(-1);
        Zend_Controller_Action_HelperBroker::getStack()->offsetSet($priority, new Whmedia_Controller_Action_Helper_Message());
    }

}