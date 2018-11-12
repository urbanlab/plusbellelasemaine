
<div class="login-box">
	<div class="login-logo"><b>Admin</b> <?=$this->config->item('site_name')?></div>
	<!-- /.login-logo -->
	<div class="login-box-body">
		<p class="login-box-msg">Accès sécurisé</p>
		<form action="<?php echo(base_admin_url().'login'); ?>" method="post">
			<?php if($this->session->flashdata('errorMessage')) { ?>
			<div class="alert alert-danger alert-dismissable alert-auto-close">
				<i class="fa fa-ban"></i>
				<button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
				<?php echo($this->session->flashdata('errorMessage')); ?>
			</div>
			<?php } ?>
			<div class="form-group has-feedback">
				<input name="login" type="email" class="form-control" placeholder="Identifiant (Email)" value="<?php echo(ENVIRONMENT == 'development' ? 'a@a.com' : ''); ?>">
				<span class="glyphicon glyphicon-envelope form-control-feedback"></span> </div>
			<div class="form-group has-feedback">
				<input name="password" type="password" class="form-control" placeholder="Password"  value="<?php echo(ENVIRONMENT == 'development' ? 'a' : ''); ?>">
				<span class="glyphicon glyphicon-lock form-control-feedback"></span> </div>
			<div class="row">
				<div class="col-xs-8">
					<?php /*
		  <div class="checkbox icheck">
			<label>
			  <input type="checkbox"> Se rappeler de moi
			</label>
		  </div>
		  */ ?>
				</div>
				<!-- /.col -->
				<div class="col-xs-4">
					<button type="submit" class="btn btn-primary btn-block btn-flat">Valider</button>
				</div>
				<!-- /.col --> 
			</div>
		</form>
		<br>
		<a href="<?php echo(base_url().'admin/identification/forgotPassword'); ?>">J'ai oublié mon mot de passe</a><br>
	</div>
	<!-- /.login-box-body --> 
</div>
<!-- /.login-box --> 

