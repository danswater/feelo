<?php
/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    User
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: password.tpl 9747 2012-07-26 02:08:08Z john $
 * @author     Steve
 */
$addClass = "";
?>
<?php if( Zend_Controller_Front::getInstance()->getRequest()->getParam('format') != 'smoothbox' ): ?>
<div class="headline">
  <h2>
    <?php echo $this->translate('My Settings');?>
  </h2>
  <div class="tabs">
    <?php
      // Render the menu
      echo $this->navigation()
        ->menu()
        ->setContainer($this->navigation)
        ->render();
    ?>
  </div>
</div>
<?php else: ?>
  <?php $addClass = "global_iframe_form"; ?>
<?php endif; ?>

<div class="<?php echo $addClass ?>">
	<?php if ( $this->facebookSigned ) { ?>	
		<script>
		  window.addEvent('domready', function() {
				$( 'facebook-signed' ).addEvent( 'click', function ( e ) {
					e.event.preventDefault();
					parent.Smoothbox.close();
				} );	
		  });
		</script>
		<a href="#" style="float: right" id="facebook-signed"> Close </a>
	<?php } ?>
  <?php echo $this->form->render($this) ?>
</div>