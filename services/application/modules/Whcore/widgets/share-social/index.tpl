<?php
$request = Zend_Controller_Front::getInstance()->getRequest();
$fullURL = $request->getScheme() . '://' . $request->getHttpHost();
$frontController = Zend_Controller_Front::getInstance()->getRequest();

$params = array();
if ($frontController->getModuleName() == 'whmedia') {
    $params['media'] = urlencode($fullURL . $this->subject()->getPhotoUrl('thumb.normal'));
    $params['description'] = urlencode($this->subject()->getTitle());

    $this->headMeta()->appendProperty('og:title', $this->subject()->getTitle());
    $this->headMeta()->appendProperty('og:type', 'article');
    $this->headMeta()->appendProperty('og:url', $fullURL . $this->url());
    $this->headMeta()->appendProperty('og:image', $this->subject()->getCoverMedia() ? $fullURL . $this->subject()->getCoverMedia()->getOriginal()->map() : '');
    $this->headMeta()->appendProperty('og:site_name', Engine_Api::_()->getApi('settings', 'core')->getSetting('core_general_site_title', $this->translate('_SITE_TITLE')));
    //$this->headMeta()->appendProperty('og:description', 'Lorem Ipsum Dolor Sit Amet');
} elseif ($frontController->getActionName() == 'grandopening') {
    $settings = Engine_Api::_()->getApi('settings', 'core');
    $params['description'] = $settings->getSetting('core_general_site_title', $this->translate('_SITE_TITLE'));

    $contentTable = Engine_Api::_()->getDbTable('content', 'core');
    $select = $contentTable->select();
    $select->where('name = ?', 'core.menu-logo');
    $widgetParams = $contentTable->fetchRow($select)->params;

    $params['media'] = $fullURL . $this->baseUrl() . (isset($widgetParams['logo']) ? '/' . $widgetParams['logo'] : '/application/modules/Grandopening/externals/images/gIcon.png');

    $this->headMeta()->appendProperty('og:title', $params['description']);
    $this->headMeta()->appendProperty('og:type', 'article');
    $this->headMeta()->appendProperty('og:url', $fullURL . $this->url());
    $this->headMeta()->appendProperty('og:image', $params['media']);
    $this->headMeta()->appendProperty('og:site_name', Engine_Api::_()->getApi('settings', 'core')->getSetting('core_general_site_title', $this->translate('_SITE_TITLE')));
}
$params['url'] = urlencode($fullURL . $this->url());
$url = http_build_query($params);
?>


<!-- AddThis Button BEGIN -->
<div class="addthis_toolbox addthis_default_style wh_share">
    <a class="addthis_button_facebook_like" fb:like:layout="button_count"></a>
    <a class="addthis_button_tweet"></a>
    <a class="addthis_button_google_plusone" g:plusone:size="medium"></a>
    <a href="http://pinterest.com/pin/create/button/?<?php echo $url ?>" class="pin-it-button" count-layout="horizontal"><img border="0" src="//assets.pinterest.com/images/PinExt.png" title="Pin It" /></a>
</div>
<script type="text/javascript" src="http://s7.addthis.com/js/250/addthis_widget.js#pubid=xa-4f33a4c907e62718"></script>
<!-- AddThis Button END -->

<script type="text/javascript" src="http://assets.pinterest.com/js/pinit.js"></script>
