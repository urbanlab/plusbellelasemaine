
<div class="login-box">
	<div class="login-logo"><b>Admin</b> <?=$this->config->item('site_name')?></div>
	<!-- /.login-logo -->
	<div class="login-box-body">
		<p class="login-box-msg">Mot de passe oublié</p>
		<form action="<?php echo(base_admin_url().'forgotPassword'); ?>" method="post">
			<?php if($this->session->flashdata('errorMessage')) { ?>
			<div class="alert alert-danger alert-dismissable alert-auto-close">
				<i class="fa fa-ban"></i>
				<button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
				<?php echo($this->session->flashdata('errorMessage')); ?>
			</div>
			<?php } ?>
			<?php if($this->session->flashdata('confirmMessage')) { ?>
			<div class="alert alert-info alert-dismissable alert-auto-close">
				<i class="fa fa-info"></i>
				<button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
				<?php echo($this->session->flashdata('confirmMessage')); ?>
			</div>
			<?php } ?>
			<div class="form-group has-feedback">
				<input name="email" type="email" class="form-control" placeholder="Email" value=""  required autofocus >
				<span class="glyphicon glyphicon-envelope form-control-feedback"></span> </div>
			
			<div class="row">
				<div class="col-sm-9">
					<button type="submit" class="btn btn-primary btn-block btn-flat">Envoyer un nouveau mot de passe</button>
				</div>
				<!-- /.col -->
				<div class="col-sm-3">
					<a href="<?php echo base_url(); ?>admin/identification" class="btn btn-default btn-block btn-flat">Retour</a>
				</div>
				<!-- /.col -->
			</div>
		</form>
		
	</div>
	<!-- /.login-box-body --> 
</div>
<!-- /.login-box --> 

