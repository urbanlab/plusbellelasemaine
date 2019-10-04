<?php
    $admin = $this->session->userdata('admin');
?>
<div class="row">
	<div class="col-xs-8">

		<div class="box" id="">
			<div class="box-header">
				<h3 class="box-title"><i class="fa fa-th-list"></i> Liste des admins existants</h3>
			</div>
			<div class="box-body">
				
				<!-- Table -->
				<table class="table table-striped table-hover table-condensed itemsList">
					<thead>
						<tr>
							<th class="col-xs-10">Nom</th>
							<th class="col-xs-2 text-center">Actions</th>
						</tr>
					</thead>
					<tbody>
						<?php
							
							$nbItems = $itemsData->num_rows();
							foreach($itemsData->result() as $row) {
						?>
						<tr>
							<td><?php echo($row->firstname . ' ' . $row->lastname); ?></td>
							<td class="text-center"><a href="<?php echo(base_admin_url(current_section()."/editItem/".$row->id)); ?>" class="btn btn-default btn-sm btn-flat" title="Edit user"><span class="glyphicon glyphicon-pencil"></span></a> 

								<?php if ($row->id!=$admin["id"]) { ?>

								<a class="btn btn-default btn-sm btn-flat userFormDelete" href="<?php echo(base_admin_url(current_section()."/deleteItem/".$row->id)); ?>" title="Delete user"><span class="glyphicon glyphicon-remove"></span></a>

								<?php } ?>
							</td>
						</tr>
						<?php
							}
						?>
					</tbody>
				</table>
				
			</div>
		</div>

	</div>
	<div class="col-xs-4">

		<div class="box" id="userFormBlock">
			<div class="box-header"><h3 class="box-title"><i class="fa fa-edit"></i> <?php echo(isset($itemData) ? 'Editer' : 'Ajouter'); ?> un admin</h3></div>
			<div class="box-body">
				<form id="userForm" action="<?php echo(base_admin_url(current_section().'/saveForm')); ?>" method="post" enctype="multipart/form-data" role="form">
					<input type="hidden" id="editedItemId" name="editedItemId" value="<?php echo(isset($itemData) ? $itemData->id : '-1'); ?>" />
					
					<div class="form-group">
						<label class="control-label" for="firstname">Prénom</label>
						<input type="text" class="form-control " id="firstname" name="firstname" value="<?php echo(set_value('firstname', isset($itemData) ? $itemData->firstname : '')); ?>" required />
					</div>

					<div class="form-group">
						<label class="control-label" for="lastname">Nom</label>
						<input type="text" class="form-control " id="lastname" name="lastname" value="<?php echo(set_value('lastname', isset($itemData) ? $itemData->lastname : '')); ?>" required />
					</div>					
					
					<p>&nbsp;</p>
					
					<div class="form-group">
						<label class="control-label" for="loginInput">Identifiant / email</label>
						<input type="email" class="form-control " id="loginInput" name="loginInput" value="<?php echo(set_value('loginInput', isset($itemData) ? $itemData->login : '')); ?>" required />
						<?php
							if(!empty($error) && $error == 'duplicateLoginError') {
						?>
						<br />
						<div class="alert alert-danger alert-dismissable alert-auto-close" role="alert">
							<i class="fa fa-ban"></i>
							<button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
							<strong>Identifiant déjà utilisé!</strong><br>Choisissez en un autre.
						</div>
						<?php
							}
						?>
					</div>
					
					<div class="form-group">
						<label class="control-label" for="passwordInput"><?php echo(isset($itemData) ? 'Changer le mot de passe' : 'Mot de passe'); ?></label>
						<input type="text" class="form-control " id="passwordInput" name="passwordInput" value="<?php echo(set_value('password','')); ?>" <?php if(!isset($itemData)) { echo('required'); } ?> restrict="" />
						<div class="help-block">Le mot de passe doit avoir au moins 8 caractères, et comprendre au moins une lettre minuscule, une lettre majuscule et un chiffre.</div>
					</div>					
					
					
				</form>
				
			</div>
			<div class="box-footer text-center">
				<button type="submit" class="btn btn-primary btn-flat" id="userFormSubmit">Enregistrer</button>
				<a href="<?php echo(base_admin_url(current_section())); ?>" class="btn btn-default btn-flat">Annuler</a>
			</div>
		</div>
	
	</div>
		
</div>