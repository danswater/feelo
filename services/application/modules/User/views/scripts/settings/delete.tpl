<?php
/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    User
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: delete.tpl 9747 2012-07-26 02:08:08Z john $
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

<?php if( $this->isLastSuperAdmin ):?>
  <div class="tip">
    <span>
      <?php echo $this->translate('This is the last super admin account. Please reconsider before deleting this account.'); ?>
    </span>
  </div>
<?php endif;?>

<div class="<?php echo $addClass ?>">
  <?php echo $this->form->setAttrib('id', 'user_form_settings_delete')->render($this) ?>
</div>