var userbos = {

	init : function() {
			
		if($('#changePasswordForm').length) {
			this.passwords.init();
		}
		
		if($('#userForm').length) {
			$('#userFormSubmit').click(function(e) {
				e.preventDefault();
				userbos.itemEditionFormCheck();
				
			});
			
			$('.userFormDelete').click(userbos.itemDeleteConfirm);
		}
	},
		
	
	itemDeleteConfirm: function(e) {
		e.preventDefault();
		if(confirm('Etes-vous certains de vouloir supprimer cet utilisateur ?') === true) {
			$(window).attr('location', $(this).attr('href'));
		}else{
			return;
		}
	},
	
	itemEditionFormCheck : function() {
		var ok = true;
		var email_regex = /^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,4}$/i;
		
		if($('#firstname').val() === '') {
			$('#firstname').closest('.form-group').addClass('has-error').focus();
			ok = false;
		}else{
			$('#firstname').closest('.form-group').removeClass('has-error');
		}
		
		if($('#lastname').val() === '') {
			$('#lastname').closest('.form-group').addClass('has-error').focus();
			ok = false;
		}else{
			$('#lastname').closest('.form-group').removeClass('has-error');
		}
		
		if($('#loginInput').val() === '' || !email_regex.test($('#loginInput').val())) {
			$('#loginInput').closest('.form-group').addClass('has-error').focus();
			ok = false;
		}else{
			$('#loginInput').closest('.form-group').removeClass('has-error');
		}
		// at least one number, one lowercase and one uppercase letter
		// at least 8 characters
    	var pass_regex = /(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{8,}/;
    	if($('#editedItemId').val() === '-1' && ($('#passwordInput').val() === '' || !pass_regex.test($('#passwordInput').val()))) {
			$('#passwordInput').closest('.form-group').addClass('has-error').focus();
			ok = false;
			alert("Vous devez spécifier un mot de passe complexe, comprenant au moins 8 caractères, et avec au moins une lettre minuscule, une lettre majuscule et un chiffre.")
		}else{
			$('#passwordInput').closest('.form-group').removeClass('has-error');
		}
		
		if(ok === true) {
			$('#userForm').submit();
		}
	},
	
	passwords : {
		
		init : function() {
			$('#changePasswordBtn').click(function(e) {
				e.preventDefault();
				
				$('#changePasswordForm #currentPassword').val('');
				$('#changePasswordForm #newPassword1').val('');
				$('#changePasswordForm #newPassword2').val('');
							
				$('#alert1').slideUp(0);
				$('#alert2').slideUp(0);
						
				$('#changePasswordModal').modal();
			});
			
			$('#changePasswordForm #savePasswordBtn').click(function(e) {
				e.preventDefault();
				
				// check if strong passowrd
				var pass_regex = /(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{8,}/;
				if($('#changePasswordForm #newPassword1').val() === '' || !pass_regex.test($('#changePasswordForm #newPassword1').val())) {
					alert("Vous devez spécifier un mot de passe complexe, comprenant au moins 8 caractères, et avec au moins une lettre minuscule, une lettre majuscule et un chiffre.");
					
					return false;
				}
				
				// send pass modification
				$.post($('#changePasswordForm').attr('action'),
					'currentPassword='+$('#changePasswordForm #currentPassword').val()+'&newPassword1='+$('#changePasswordForm #newPassword1').val()+'&newPassword2='+$('#changePasswordForm #newPassword2').val()+'',
					function (data) {
						//console.log(data);
						if(data === '-1000') {
							
						}
						
						if(data === '1') {
							$('#changePasswordModal').modal('hide');
						}else{
							if(data === '-1') {						
								//console.log('error -1');
								$('#alert1').slideDown('fast').delay(2000).slideUp('fast');
								
							}else if(data === '-2') {
								$('#alert2').slideDown('fast').delay(2000).slideUp('fast');
							}
						}
					}
				);
			});
		}
		
	}
};