<?php
/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    User
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: account.tpl 9765 2012-08-20 21:42:23Z matthew $
 * @author     John
 */
?>

<script type="text/javascript">
//<![CDATA[
  window.addEvent('load', function() {
    if( $('username') && $('profile_address') ) {
      $('profile_address').innerHTML = $('profile_address')
        .innerHTML
        .replace('<?php echo /*$this->translate(*/'yourname'/*)*/?>',
          '<span id="profile_address_text"><?php echo $this->translate('yourname') ?></span>');

      $('username').addEvent('keyup', function() {
        var text = '<?php echo $this->translate('yourname') ?>';
        if( this.value != '' ) {
          text = this.value;
        }
        
        $('profile_address_text').innerHTML = text.replace(/[^a-z0-9]/gi,'');
      });
      // trigger on page-load
      if( $('username').value.length ) {
          $('username').fireEvent('keyup');
      }
    }
  });
//]]>
</script>

<?php echo $this->form->render($this) ?>
