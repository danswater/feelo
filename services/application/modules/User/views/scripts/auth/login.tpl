<?php
/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    User
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: login.tpl 9747 2012-07-26 02:08:08Z john $
 * @author     John
 */
?>
<?php #echo $this->form->render($this) ?>
<style type="text/css">
	#custom-signup-form{width: 504px; background: #fff; margin: 100px auto; border-radius:5px; height: 441px }
	#signup-header-form{text-align: center; margin:58px 0px 40px 0px; }
	#signup-body-form .column, .hw-box{width: 316px; }
	.hw-box{margin: 0px 94px 0px 94px; height: 56px; border-radius:5px;}
	#signup-body-form input[type="password"],	
	#signup-body-form input[type="email"]{font-size: 24px !important; border: none !important; background: #f9f9f9 }
	#signup-body-form input[type="submit"]{background: #34c9e8; width: 139px; height: 41px; border-radius:5px; color:#ffffff; font-size: 18px; }
	#signup-body-form input[type="checkbox"]{-ms-transform: scale(1); /* IE */ -moz-transform: scale(1); /* FF */ -webkit-transform: scale(1); /* Safari and Chrome */ -o-transform: scale(1); /* Opera */ padding: 10px; }
	#signup-body-form input[type="checkbox"]{ clip: auto !important;}
	#signup-body-form p{margin-bottom: 8px; }
	#signup-body-form .signup-button{margin-top: 28px; }
	#signup-body-form .column{clear: both; }
	#signup-body-form .column .col1,
	#signup-body-form .column .col2{width: 158px; float: left; }
	.fbbg{background: #3b5998; }
	.twitterbg a:hover,
	.fbbg a:hover{text-decoration: none; }
	.twitterbg span,
	.fbbg span{color: #ffffff; font-size: 26px; line-height: 56px; margin-left: 50px; }
	.twitterbg{background: #43d5fd; }
	#signup-body-form img{margin: 4px 10px; float: left; }
	#error-message{padding-bottom: 20px; text-align: center; color: #ff0018; }
</style>

<div id="signup-overlay" style="background-color:#f9f9f9; position: absolute; top: -54px; left: 0px; visibility: visible; z-index:10">
	<div id="custom-signup-form" >
		<div id="signup-header-form" style="padding-top:20px;">
			<p>If you already have an account, please enter your details below.</p>
			<p>If you dont have one yet, please <a href="<?php echo $this->layout()->staticBaseUrl ?>signup"><b>sign up</b></a> first.</p>
		</div>
		<?php if(isset($this->error) && $this->error != "No action taken"): ?>
			<div id="error-message">
				<?php echo $this->error; ?>	
			</div>
		<?php endif; ?>
		<form action="<?php echo $this->layout()->staticBaseUrl ?>login" id="user_form_login" enctype="application/x-www-form-urlencoded"  method="post">
			<div id="signup-body-form">
				<p> <input type="email" name="email" id="email" value="" tabindex="1" autofocus="autofocus" class="hw-box" placeholder="email address"  maxlength="100"/> </p>
				<p> <input  type="password" name="password" id="password" value="" tabindex="2" class="hw-box" placeholder="password" maxlength="100"/> </p>
				
				<?php /*
				<p> 
					<div class="hw-box twitterbg">
						<img src="<?php echo $this->layout()->staticBaseUrl ?>application/modules/User/externals/images/twitter-logo.png"/>
						<a href="<?php echo $this->layout()->staticBaseUrl ?>user/auth/twitter">
							<span> twitter </span>
						</a>
					</div> 
				</p>
				*/ ?>
				<div class="signup-button column hw-box">
					<div class="col1"> 
						<input name="submit" id="submit" type="submit" tabindex="3" value="sign in"/>
					</div>
					<div class="col2" style=""> 
						<div style="padding:10px 0px; float:right;">
							<label style="float:left; padding-right:20px;" >Remember Me</label>
							<input  type="checkbox" name="remember" id="remember" value="1" tabindex="4" style="width: 15px; height: 15px; margin-left:100px; margin-top:2px;" />
						</div>
					</div>
				</div>

				<div class="forgot-password column hw-box">
					<div class="col1"> 
						<a href="<?php echo $this->layout()->staticBaseUrl ?>user/auth/forgot">
							forgot password?
						</a>
					</div>
				</div>
				
				<?php 
					if ( $this->facebook ) {
				?>
					<p> 
						<a href="<?php echo $this->layout()->staticBaseUrl ?>user/auth/facebook">
							<div class="hw-box fbbg"> 
								<img src="<?php echo $this->layout()->staticBaseUrl ?>application/modules/User/externals/images/facebook-logo.png"/>
								<span> facebook </span>
							</div> 
						</a>
					</p>
				<?php 
					}
				?>
			</div>
		</form>
	</div>
</div>

<script type="text/javascript">
	var resize = function(){
		var width = window.getSize().x;
		var height = window.getSize().y;

		$('signup-overlay').setStyle("width", width + "px");
		$('signup-overlay').setStyle("height", height + "px");
	}

	resize();
	window.addEvent("resize", resize);
</script>