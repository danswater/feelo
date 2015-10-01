<?php
/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    Core
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: default-simple.tpl 9747 2012-07-26 02:08:08Z john $
 * @author     John
 */
?>
<?php echo $this->doctype()->__toString() ?>
<?php $locale = $this->locale()->getLocale()->__toString(); $orientation = ( $this->layout()->orientation == 'right-to-left' ? 'rtl' : 'ltr' ); ?>

<?php
  $counter = (int) $this->layout()->counter;
  $staticBaseUrl = $this->layout()->staticBaseUrl;
	$headIncludes = $this->layout()->headIncludes;
?>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="<?php echo $locale ?>" lang="<?php echo $locale ?>" dir="<?php echo $orientation ?>">
	<head>
  		<base href="<?php echo rtrim((constant('_ENGINE_SSL') ? 'https://' : 'http://') . $_SERVER['HTTP_HOST'] . $this->baseUrl(), '/'). '/' ?>" />

      <?php
        $request = Zend_Controller_Front::getInstance()->getRequest();
        $this->headTitle()
          ->setSeparator(' - ');
        $pageTitleKey = 'pagetitle-' . $request->getModuleName() . '-' . $request->getActionName()
            . '-' . $request->getControllerName();
        $pageTitle = $this->translate($pageTitleKey);
        if( $pageTitle && $pageTitle != $pageTitleKey ) {
          $this
            ->headTitle($pageTitle, Zend_View_Helper_Placeholder_Container_Abstract::PREPEND);
        }
        $this
          ->headTitle($this->translate($this->layout()->siteinfo['title']), Zend_View_Helper_Placeholder_Container_Abstract::PREPEND)
          ;
        $this->headMeta()
          ->appendHttpEquiv('Content-Type', 'text/html; charset=UTF-8')
          ->appendHttpEquiv('Content-Language', $this->locale()->getLocale()->__toString());

        // Make description and keywords
        $description = '';
        $keywords = '';

        $description .= ' ' .$this->layout()->siteinfo['description'];
        $keywords = $this->layout()->siteinfo['keywords'];

        if( $this->subject() && $this->subject()->getIdentity() ) {
          $this->headTitle($this->subject()->getTitle());

          $description .= ' ' .$this->subject()->getDescription();
          if (!empty($keywords)) $keywords .= ',';
          $keywords .= $this->subject()->getKeywords(',');
        }
        
        $this->headMeta()->appendName('description', trim($description));
        $this->headMeta()->appendName('keywords', trim($keywords));

        // Get body identity
        if( isset($this->layout()->siteinfo['identity']) ) {
          $identity = $this->layout()->siteinfo['identity'];
        } else {
          $identity = $request->getModuleName() . '-' .
              $request->getControllerName() . '-' .
              $request->getActionName();
        }
      ?>
      <?php echo $this->headTitle()->toString()."\n" ?>
      <?php echo $this->headMeta()->toString()."\n" ?>

      <?php
        $this->headLink(array(
          'rel' => 'favicon',
          'href' => ( isset($this->layout()->favicon)
            ? $staticBaseUrl . $this->layout()->favicon
            : '/favicon.ico' ),
          'type' => 'image/x-icon'),
          'PREPEND');
        $themes = array();
        if( !empty($this->layout()->themes) ) {
          $themes = $this->layout()->themes;
        } else {
          $themes = array('default');
        }


        foreach( $themes as $theme ) {
           if($theme === "wazzap2day"){
             $this->headLink()
              ->prependStylesheet($staticBaseUrl . 'application/css.php?request=application/themes/' . $theme . '/front-page.css');
          }

          if( APPLICATION_ENV != 'development' ) {
            $this->headLink()
              ->prependStylesheet($staticBaseUrl . 'application/css.php?request=application/themes/' . $theme . '/theme.css');
          } else {
            $this->headLink()
              ->prependStylesheet(rtrim($this->baseUrl(), '/') . '/application/css.php?request=application/themes/' . $theme . '/theme.css');
          }
        }
        // Process
        foreach( $this->headLink()->getContainer() as $dat ) {
          if( !empty($dat->href) ) {
            if( false === strpos($dat->href, '?') ) {
              $dat->href .= '?c=' . $counter;
            } else {
              $dat->href .= '&c=' . $counter;
            }
          }
        }
      ?>
      <?php echo $this->headLink()->toString()."\n" ?>

      <link rel="stylesheet" type="text/css" href="<?php echo $staticBaseUrl; ?>application/externals/bootstrap/css/bootstrap.min.css">
      <link rel="stylesheet" type="text/css" href="<?php echo $staticBaseUrl; ?>application/externals/bootstrap/css/bootstrap-theme.min.css">
  	</head>
  	<body>
      <div id="frontpage-container">
        <div id="logo-rapper">
          <img src="<?php echo $staticBaseUrl; ?>application/modules/Grandopening/externals/images/front-page/logo.png" id="img-logo"/>
        </div>

        <div id="moto-rapper">
          <img src="<?php echo $staticBaseUrl; ?>application/modules/Grandopening/externals/images/front-page/moto.png" id="img-moto" />
        </div>

        <div id="join-rapper">
          <a href="<?php echo $staticBaseUrl; ?>signup">
            <div class="join-buttom-rapper">
               <span> join </span>
            </div>
          </a>
          <span class="join-desc">takes only 30 seconds to join the awesome</span>
        </div>

        <div id="login-rapper">
          <div class="login-buttom-rapper">
            <a href="<?php echo $staticBaseUrl; ?>login">
              <span>log in</span>
            </a>
          </div>
        </div>

        <div id="footer-rapper">
          <ul>
            <li>
              <a href="<?php echo $staticBaseUrl; ?>members/home">
                <span>featured</span>
              </a>
            </li>
            <li>
              <a href="<?php echo $staticBaseUrl; ?>search?type=tags">
                <span>explore</span>
              </a>
            </li>
            <li>
              <a href="<?php echo $staticBaseUrl; ?>login">
                <span>how it works</span>
              </a>
            </li>
            <li>
              <a href="<?php echo $staticBaseUrl; ?>help/contact">
                <span>contact us</span>
              </a>
            </li>
          </ul>
        </div>
      <div>

      <script type="text/javascript" src="<?php echo $staticBaseUrl; ?>application/externals/bootstrap/js/jquery-2.1.0.min.js"></script>
      <script type="text/javascript" src="<?php echo $staticBaseUrl; ?>application/externals/bootstrap/js/bootstrap.min.js"></script>
  	</body>
</html>