<style type="text/css">
	.yambai-login{text-align: center; }
	.yambai-login div{margin-bottom: 10px; text-align: center;}
	.yambai-login p{ padding: 10px;}
	.yambai-login a:hover{ text-decoration: none; }
	.yambai-login div span{ font-size: 18px; color: #6a6a6a;}
</style>

<div class="yambai-login">
	<div class="bgBox">
		<p>
			<a href="<?php echo $this->layout()->staticBaseUrl ?>/user/auth/facebook">
				<span> Facebook </span>
			</a>
		</p>
	</div>
	<div class="bgBox">
		<p>
			<a href="<?php echo $this->layout()->staticBaseUrl ?>signup">
				<span> Sign Up with Email </span>
			</a>
		</p>
	</div>
	<div class="bgBox">
		<p>
			<a href="<?php echo $this->layout()->staticBaseUrl ?>login">
				<span> Login </span>
			</a>
		</p>
	</div>
</div>
