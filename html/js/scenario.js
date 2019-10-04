var scenario = {

	init : function() {
			
		if($('#scenarioForm').length) {
			$('#scenarioFormSubmit').click(function(e) {
				e.preventDefault();
				scenario.itemEditionFormCheck();
				
			});

			$('.scenarioFormDelete').click(scenario.itemDeleteConfirm);

            scenario.showTemporality();
            $('#show_temporality').change(scenario.showTemporality);
            
            $('.iconpicker').on('change', scenario.iconPicker);

            $('.media_delete').click(scenario.mediaDeleteConfirm);

            // focus sur la première erreur
            $('.flash_error').focus();
		}
		
		if($('#itemViewEventsModal').length) {
			$('#itemViewEventsModal').on('hide.bs.modal', function (e) {
				$(window).attr('location', CI.base_admin_section_url);
			});
			$('#itemViewEventsModal').modal('show');
		}
	},


    itemDeleteConfirm: function(e) {
        e.preventDefault();
        if(confirm('Etes-vous certain de vouloir supprimer ce scénario ?') === true) {
            $(window).attr('location', $(this).attr('href'));
        }else{
            return;
        }
    },

    mediaDeleteConfirm: function(e) {
        e.preventDefault();
        if(confirm('Etes-vous certain de vouloir supprimer ce contexte ?') === true) {
            var _id = $(this).attr('id');
            $("#"+_id.replace("delete", "label")).val('');
        }else{
            return;
        }
    },

    showTemporality: function(e) {
	    // on désactive les champs associés pour montrer qu'ils ne sont plus utilisés
        var _checked = $('#show_temporality').is(":checked");
        $("#temporality_labels").prop('readonly', !_checked);
        //$("#temporality_periods_to_win").prop('readonly', !_checked);
        //$("#temporality_questions_per_period").prop('readonly', !_checked);
    },

    iconPicker: function(e) {
        // mise à jour de l'input picto
        var _id = $(e.currentTarget).attr('id').replace("iconpicker", "picto");
        $("#"+_id).val(e.icon);
    },
	
	itemEditionFormCheck : function() {
		var ok = true;

		// d'abord on retire toutes les erreurs
        $('.has-error').removeClass('has-error');
        $('.check_error').html('');

		// vérification des informations générales obligatoires
        if($('#title').val() === '') {
            $('#title').closest('.form-group').addClass('has-error');
            if (ok) $('#title').focus();

            ok = false;
        }

        if($('#intro_title').val() === '') {
            $('#intro_title').closest('.form-group').addClass('has-error');
            if (ok) $('#intro_title').focus();
            ok = false;
        }

        if($('#intro_text').val() === '') {
            $('#intro_text').closest('.form-group').addClass('has-error');
            if (ok) $('#intro_text').focus();
            ok = false;
        }

        if($('#about_title').val() === '') {
            $('#about_title').closest('.form-group').addClass('has-error');
            if (ok) $('#about_title').focus();
            ok = false;
        }

        if($('#about_text').val() === '') {
            $('#about_text').closest('.form-group').addClass('has-error');
            if (ok) $('#about_text').focus();
            ok = false;
        }

        var _nb;

        // vérification des jauges
        _nb = $('.jauge-tab').size();
        for(var i=0;i<_nb;i++)
        {
            var fail = false;

            var _id = '#gauge' + i + '_var';
            if ($(_id).val() === '')
            {
                // la première doit être renseignée
                if (i == 0) {
                    $(_id).closest('.form-group').addClass('has-error');
                    if (ok) $(_id).focus();
                    ok = false;
                    fail = true;
                }
            }
            else
            {
                // s'il y a un nom de variable alors le reste doit être renseigné
                _id = '#gauge' + i + '_label';
                if ($(_id).val() === '')
                {
                    $(_id).closest('.form-group').addClass('has-error');
                    if (ok) $(_id).focus();
                    ok = false;
                    fail = true;
                }

                _id = '#gauge' + i + '_summary_title';
                if ($(_id).val() === '')
                {
                    $(_id).closest('.form-group').addClass('has-error');
                    if (ok) $(_id).focus();
                    ok = false;
                    fail = true;
                }
				
				if($('#editedItemType').val() !== '1') {

					_id = '#gauge' + i + '_victory_title';
					if ($(_id).val() === '')
					{
						$(_id).closest('.form-group').addClass('has-error');
						if (ok) $(_id).focus();
						ok = false;
						fail = true;
					}

					_id = '#gauge' + i + '_victory_text';
					if ($(_id).val() === '')
					{
						$(_id).closest('.form-group').addClass('has-error');
						if (ok) $(_id).focus();
						ok = false;
						fail = true;
					}

					_id = '#gauge' + i + '_defeat_title';
					if ($(_id).val() === '')
					{
						$(_id).closest('.form-group').addClass('has-error');
						if (ok) $(_id).focus();
						ok = false;
						fail = true;
					}

					_id = '#gauge' + i + '_defeat_text';
					if ($(_id).val() === '')
					{
						$(_id).closest('.form-group').addClass('has-error');
						if (ok) $(_id).focus();
						ok = false;
						fail = true;
					}
				
				}
            }

            if (fail) {
                $("#jauge" + (i + 1) + "-tab").addClass('has-error');
            }
        }

        // vérification de la temporalité
        var _checked = $('#show_temporality').is(":checked");
        if (_checked) {
            _id = '#temporality_labels';
            if ($(_id).val() === '') {
                $(_id).closest('.form-group').addClass('has-error');
                if (ok) $(_id).focus();
                ok = false;
            }
        }

        // vérification des médias
        _nb = $('.media_label').size();
        var _labels = [];
        for(var i=0;i<_nb;i++)
        {
            var _errors = [];

            var _media_label = $('#media_label_'+i).val();
            _media_label = (typeof _media_label == 'string')?_media_label:'';

            // tout est ok si le label n'est pas renseigné
            if (_media_label.length==0)
            {
                // sauf si c'est le premier contexte, il doit y en avoir au moins un
                if (i==0)
                {
                    _errors.push('Il doit y avoir au moins un contexte');
                }
                else
                    continue;
            }

            var _media_file = $('#media_file_'+i).val();
            _media_file = (typeof _media_file == 'string')?_media_file:'';
            var _media_id = $('#media_id_'+i).val();
            _media_id = (typeof _media_id == 'string')?_media_id:'';

            // pas de doublons dans les lables
            if (_labels.indexOf(_media_label)!=-1)
                _errors.push('Ce libellé est déjà utilisé');
            else
                _labels.push(_media_label);

            // s'il y a un label, il doit y avoir un fichier uploadé ou à uploader
            if (_media_label.length>0 && _media_file.length==0 && _media_id.length==0)
                _errors.push('Fichier manquant');

            if (_errors.length>0) {
                $('#media_label_' + i).closest('.form-group').addClass('has-error');
                if (ok) $('#media_label_' + i).focus();
                ok = false;
                _errors.forEach(function(_error) {
                    $('#check_error_media_' + i).append('<p>'+_error+'</p>');
                })
            }
        }

        // fichier excel nécessaire lors de l'ajout
        var _editedItemId = $('#editedItemId').val();
        if (_editedItemId=='-1')
        {
            var _excel = $('#xlsxFile').val();
            _excel = (typeof _excel == 'string')?_excel:'';

            if (_excel.length==0)
            {
                $('#xlsxFile').closest('.form-group').addClass('has-error');
                ok = false;
                $('#check_error_xlsx').append('<p>Fichier Excel manquant</p>');
            }
        }

        if(ok === true) {
			$('#scenarioForm').submit();
		}
	}
};