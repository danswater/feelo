<div id="loginFormBackground">
	<div class="loginFormCanvas instantAnimation fadeInDown">
		<img src="assets/custom/img/png/yamba-logo@2x.png" style="width: 80%; margin-left:10%; margin-bottom:30px;"/>
		<form id="loginForm" ng-submit="authenticate(loginForm)" name="loginForm">
			<div> {{ errorMsg }} </div>

			<input type="text" ng-model="username" name="username" placeholder="Username" class="test" required/> <br/>
			<span ng-show="loginForm.username.$dirty && loginForm.username.$invalid && loginForm.username.$error.required">Username is required.</span> <br/>

			<input type="password" ng-model="password" name="password" placeholder="Password"  required/> <br/>
			<span ng-show="loginForm.password.$dirty && loginForm.password.$invalid && loginForm.password.$error.required">Password is required.</span> <br/>

			<input type="submit" value="login" class="loginButton" style="background-color: #222; padding: 20px;" />
		</form>
	</div>
</div>



