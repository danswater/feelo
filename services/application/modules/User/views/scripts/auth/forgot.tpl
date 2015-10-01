<?php
/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    User
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: forgot.tpl 9747 2012-07-26 02:08:08Z john $
 * @author     John
 */
?>

<style type="text/css">
	
	#user_form_auth_forgot > div{
		margin: 30px 348px;
		width: 350px;
	}
	#user_form_auth_forgot > div h3{
		font-size: 24px;
		text-align: left;
		padding: 10px 10px;
	}
	#user_form_auth_forgot > div p{
		font-size: 18px;
	}
	#user_form_auth_forgot > div > div #email-label{
		display: none;
	}
	#user_form_auth_forgot > div > div input[ type="text" ]{
		width: 285px !important;
		height: 40px !important;
		padding: 0px 5px;
		background: #f9f9f9;
		border: none;
		font-size: 16px;
	}
	#user_form_auth_forgot > div > div {
		background: #fff !important;
		padding: 20px !important;
		border: none;

	}
	#user_form_auth_forgot > div > div button,
	#user_form_auth_forgot > div > div a{
		background-color: #34c9e8;
		color: #fff;
		font-weight: bold;
		padding: 10px;
		border: none;
	}
	#user_form_auth_forgot > div > div #buttons-element{
		float: right;
	}

	#core_menu_mini_menu{
		display: none;
	}

</style>

<?php if( empty($this->sent) ): ?>

  <?php echo $this->form->render($this) ?>

<?php else: ?>

  <div class="tip">
    <span>
      <?php echo $this->translate("USER_VIEWS_SCRIPTS_AUTH_FORGOT_DESCRIPTION") ?>
    </span>
  </div>

<?php endif; ?>
