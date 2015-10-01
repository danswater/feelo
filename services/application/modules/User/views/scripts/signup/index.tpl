<?php
/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    User
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: index.tpl 9747 2012-07-26 02:08:08Z john $
 * @author     John
 */
?>

<h2>
  <?php echo ( $this->title ? $this->translate($this->title) : '' ) ?>
</h2>

<script type="text/javascript">
  function skipForm() {
    document.getElementById("skip").value = "skipForm";
    $('SignupForm').submit();
  }
  function finishForm() {
    document.getElementById("nextStep").value = "finish";
  }
</script>

<style type="text/css">
	
	.global_form>div {
		float: none !important;
	}	
	
	#core_menu_mini_menu{
		display: none;
	}	

	#signin-form{
		margin: 0px auto;
		max-width: 500px;
	}
	#signin-form h3,
	#signin-form label,
	#signin-form p.description{
		text-align: center;
	}
	#signin-form .form-elements .form-label{
		display: none;
	}
	#signin-form form > div > div{
		background: #fff  !important;
		border: 1px solid #fff;
		border-radius: 20px 20px 20px 20px;
		padding: 10px 0px;
	}
	#signin-form form > div > div h3{
		font-size: 20px;
	}
	#signin-form .form-elements textarea{
		margin-left: 85px;
		margin-right: 85px;
		border: none;
		width: 300px;
	}
	#signin-form .form-elements select,
	#signin-form .form-elements input[type="password"],
	#signin-form .form-elements input[type="email"],
	#signin-form .form-elements input[type="text"]{
		height: 30px;
		width: 300px;
		margin-left: 85px;
		margin-right: 85px;
		border: none;
		background: #f9f9f9;
	}
	#signin-form .form-elements select{
		max-width: 300px !important; 
	}
	#signin-form #terms-element{
		margin: 20px 80px;
	}
	#signin-form #submit-element{
		float: right;
		right: 21px;
		position: relative;
	}
	
	.fbbg {
		background: #3b5998;
	}
	
	.hw-box {
		margin: 0px 94px 8px 82px;
		height: 56px;
		border-radius: 5px;
		width: 316px;
	}
		
	img {
		margin: 4px 10px;
		float: left;
	}
	
	.fbbg span {
		color: #ffffff;
		font-size: 26px;
		line-height: 56px;
		margin-left: 50px;
	}
	
	#facebookSignup-element > .facebook-tag {
		text-decoration: none;
	}
</style>

<div id="signin-form">
	<?php echo $this->partial($this->script[0], $this->script[1], array(
	  'form' => $this->form
	)) ?>	
</div>

<script type="text/javascript">
// detect users timezone
( function ( date ) {
    var offset;
    var utcHourOffset = date.getTimezoneOffset() / 60;
    if ( utcHourOffset > 0 ) {
        offset = parseInt( '-' + utcHourOffset, 10 );
    } else {
        offset = Math.abs( utcHourOffset );
    }
    
    var el = document.getElementById( 'timezone' );
    el.value = offset;
} )( new Date() );
</script>
