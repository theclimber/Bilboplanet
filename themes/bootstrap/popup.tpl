<!-- BEGIN login.popup -->
<div class="login-box popup modal fade" id="loginModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true"><i class="fa fa-times"></i></span></button>
        <h4 class="modal-title">Login into {$planet.title}</h4>
      </div>
			<form action="{$planet.url}/auth.php" method="post" id="login-form" class="form-horizontal">
      	<div class="modal-body">

					<input type="hidden" name="came_from" value="{$came_from}" id="came_from"  />
					<div class="form-group">
						<label class="col-sm-3 control-label">{_Username or email}</label>
						<div class="col-sm-9">
							<input type="text" name="user_id" maxlength="32" class="text form-control" tabindex="1"  />
						</div>
					</div>
					<div class="form-group">
						<label class="col-sm-3 control-label">{_Password}</label>
						<div class="col-sm-9">
							<input type="password" name="user_pwd" maxlength="255" class="text form-control" tabindex="2" id=login_password />
							<a href="auth.php?recover=1" title="{_I forgot my password}"><i class="fa fa-question-circle"></i></a>
						</div>
					</div>
					<div class="form-group">
						<div class="col-sm-offset-3 col-sm-9">
				      <div class="checkbox">
				        <label>
									<input type="checkbox" name="user_remember" value="1" class="crirHiddenJS" tabindex="3" id="checkbox1" /> {_Remember me}
								</label>
							</div>
						</div>
					</div>
	      </div>
	      <div class="modal-footer">
	        <button type="submit" tabindex="4" class="btn btn-primary">{_Login}</button>
					{_or} <a href="{$planet.url}/signup.php">{_register}</a>
	      </div>
			</form>
    </div>
  </div>
</div>
<!-- END login.popup -->
