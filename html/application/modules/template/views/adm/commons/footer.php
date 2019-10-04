</div>
<!-- /.content-wrapper -->

<footer class="main-footer hidden">

</footer>

</div>
<!-- ./wrapper --> 

<!-- modal pour le changement de mot de passe -->
<div class="modal fade" id="changePasswordModal" tabindex="-1" role="dialog" aria-labelledby="changePasswordModalLabel">
	<div class="modal-dialog modal-sm">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
				<h4 class="modal-title">Changement du mot de passe</h4>
			</div>
			<form id="changePasswordForm" method="post" action="<?php echo base_admin_url(); ?>admins/changePassword" role="form">
				<div class="modal-body">
					<div class="form-group">
						<label for="currentPassword">Mot de passe actuel</label>
						<input type="password" class="form-control" id="currentPassword" name="currentPassword" value="" required="">
					</div>
					<div class="form-group">
						<label for="newPassword1">Votre nouveau mot de passe</label>
						<input type="password" class="form-control" id="newPassword1" name="newPassword1" value="" required="">
						<div class="help-block">Le mot de passe doit avoir au moins 8 caractères, et comprendre au moins une lettre minuscule, une lettre majuscule et un chiffre.</div>
					</div>
					<div class="form-group">
						<label for="newPassword2">Retapez votre nouveau mot de passe</label>
						<input type="password" class="form-control" id="newPassword2" name="newPassword2" value="" required="">
					</div>
					
					<div class="alert alert-danger alert-dismissable" id="alert1" style="display: none;">
						<button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
						Mot de passe courant invalide!
					</div>
					
					<div class="alert alert-danger alert-dismissable" id="alert2" style="display: none;">
						<button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
						Les 2 mots de passe ne correspondent pas!
					</div>
					  
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-default pull-left" data-dismiss="modal">Annuler</button>
					<button type="submit" class="btn btn-primary" id="savePasswordBtn">Enregistrer</button>
				</div>
			</form>
			
		</div>
		<!-- /.modal-content --> 
	</div>
	<!-- /.modal-dialog --> 
</div>
<!-- ./modal pour le changement de mot de passe -->

<?php $this->load->view('commons/js'); ?>
</body></html>