<?php
    $admin = $this->session->userdata('admin');
?>
<div class="row">
	<div class="col-xs-6">

		<div class="box" id="">
			<div class="box-header">
				<h3 class="box-title"><i class="fa fa-th-list"></i> Liste des scénarios <strong>complexes</strong> existants</h3>
			</div>
			<div class="box-body">
				
				<!-- Table -->
				<table class="table table-striped table-hover table-condensed itemsList">
					<thead>
						<tr>
							<th class="col-xs-7">Scénario</th>
							<th class="col-xs-5 text-center">Actions</th>
						</tr>
					</thead>
					<tbody>
						
						<tr>
							<td><?php echo($mainScenarioComplexe->title); ?><br><?php 
								if(ENVIRONMENT === 'development') {
									$link = 'http://localhost:8080/';
								}else{
									$link = base_url('app/');
								}
								echo '<a href="'.$link.'" target="_blank">'.$link.'</a>';
							?></td>
							<td class="text-center">
                                <a href="<?php echo(base_admin_url(current_section()."/editItem/1")); ?>" class="btn btn-default btn-sm btn-flat" title="Editer scenario"><span class="glyphicon glyphicon-pencil"></span></a>
								&nbsp;&nbsp;&nbsp;&nbsp;
								<a class="btn btn-default btn-sm btn-flat scenarioFormExport" href="<?php echo(base_admin_url(current_section()."/exportItem/1")); ?>" title="Exporter scenario"><span class="glyphicon glyphicon-export"></span></a>
							</td>
						</tr>
						
					</tbody>
				</table>
				
			</div>
		</div>
		
		<div class="box" id="">
			<div class="box-header">
				<h3 class="box-title"><i class="fa fa-th-list"></i> Liste des scénarios <strong>simples</strong> existants</h3>
			</div>
			<div class="box-body">
				
				<!-- Table -->
				<table class="table table-striped table-hover table-condensed itemsList">
					<thead>
						<tr>
							<th class="col-xs-7">Scénario</th>
							<th class="col-xs-5 text-center">Actions</th>
						</tr>
					</thead>
					<tbody>
						<?php

							$nbItems = $itemsData->num_rows();
							foreach($itemsData->result() as $row) {
						?>
						<tr>
							<td><?php echo($row->title); ?><br><?php 
								if(ENVIRONMENT === 'development') {
									$link = 'http://localhost:8080/?sid='.$row->uid;
								}else{
									$link = base_url('app/?sid='.$row->uid);
								}
								echo '<a href="'.$link.'" target="_blank">'.$link.'</a>';
							?></td>
							<td class="text-center">
                                <a href="<?php echo(base_admin_url(current_section()."/editItem/".$row->id)); ?>" class="btn btn-default btn-sm btn-flat" title="Editer scenario"><span class="glyphicon glyphicon-pencil"></span></a>
								<a class="btn btn-default btn-sm btn-flat scenarioFormDelete" href="<?php echo(base_admin_url(current_section()."/deleteItem/".$row->id)); ?>" title="Supprimer scenario"><span class="glyphicon glyphicon-remove"></span></a>
                                &nbsp;&nbsp;&nbsp;&nbsp;
								<a class="btn btn-default btn-sm btn-flat scenarioFormExport" href="<?php echo(base_admin_url(current_section()."/exportItem/".$row->id)); ?>" title="Exporter scenario"><span class="glyphicon glyphicon-export"></span></a>
								<a class="btn btn-default btn-sm btn-flat scenarioFormView" href="<?php echo(base_admin_url(current_section()."/viewItemEvents/".$row->id)); ?>" title="Voir les cartes"><span class="glyphicon glyphicon-eye-open"></span></a>
								
							</td>
						</tr>
						<?php
							}
						?>
					</tbody>
				</table>
				
			</div>
		</div>
		
		
		<div><a class="btn btn-default btn-sm btn-flat scenarioFormExport" href="<?php echo(base_url("tpl/gabarit_scenario_simple.xlsx")); ?>" title="Télécharger le gabarit Excel de scénario simple" target="_blank"><span class="fa fa-fw fa-file-excel-o"></span> Télécharger le gabarit Excel de scénario simple</a></div>

	</div>
	<div class="col-xs-6">

		<div class="box" id="scenarioFormBlock">
			<div class="box-header"><h3 class="box-title"><i class="fa fa-edit"></i> <?php echo(isset($itemData) ? 'Editer' : 'Ajouter'); ?> un scénario</h3></div>
			<div class="box-body ">

                <?php if($this->session->flashdata('medias_error')) {
                    echo "<div class='form-group flash_error'>".$this->session->flashdata('medias_error')."</div>";
                }?>
                <?php if($this->session->flashdata('xls_error')) {
                    echo "<div class='form-group flash_error'>".$this->session->flashdata('xls_error')."</div>";
                }?>

                <?php 
                if($this->session->flashdata('check_errors')) {
                    echo "<div class='form-group flash_error'>".$this->session->flashdata('check_errors')."</div>";
                    $this->session->set_flashdata('check_errors', null);
                }?>

				<form id="scenarioForm" class="form-horizontal" action="<?php echo(base_admin_url(current_section().'/saveForm')); ?>" method="post" enctype="multipart/form-data" role="form">
					<input type="hidden" name="editedItemId" id="editedItemId" value="<?php echo(isset($itemData) ? $itemData->id : '-1'); ?>" />
					<input type="hidden" name="editedItemType" id="editedItemType" value="<?php echo(isset($itemData) ? $itemData->scenario_type : '-1'); ?>" />

					
					<div class="form-group" style="display:none;">
                        <label class="control-label col-lg-2" for="title">Type de scénario</label>
                        <div class="col-lg-10">
                            <label class="radio-inline disabled">
								<input type="radio" name="type" id="type2" value="2" <?php echo(!isset($itemData) || $itemData->scenario_type == 2 ? 'checked' : ''); ?> disabled>
								Simple
							</label>
                            <label class="radio-inline disabled">
								<input type="radio" name="type" id="type1" value="1" <?php echo(isset($itemData) && $itemData->scenario_type == 1 ? 'checked' : ''); ?> disabled>
								Complexe
							</label>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="control-label col-lg-2" for="title">Titre</label>
                        <div class="col-lg-10">
                            <input type="text" class="form-control" id="title" name="title" value="<?php echo(isset($itemData) ? $itemData->title : ''); ?>"/>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="control-label col-lg-2" for="intro_title">Titre Intro</label>
                        <div class="col-lg-10">
                            <input type="text" class="form-control" id="intro_title" name="intro_title" value="<?php echo(isset($itemData) ? $itemData->intro_title : ''); ?>"/>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="control-label col-lg-2" for="intro_text">Texte Intro</label>
                        <div class="col-lg-10">
                            <textarea rows="4" class="form-control " id="intro_text" name="intro_text" required><?php echo(isset($itemData) ? $itemData->intro_text : ''); ?></textarea>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="control-label col-lg-2" for="about_title">Titre Infos</label>
                        <div class="col-lg-10">
                            <input type="text" class="form-control " id="about_title" name="about_title" value="<?php echo(isset($itemData) ? $itemData->about_title : ''); ?>"/>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-lg-2" for="about_text">Texte Infos</label>
                        <div class="col-lg-10">
                            <textarea rows="4" class="form-control " id="about_text" name="about_text" required><?php echo(isset($itemData) ? $itemData->about_text : ''); ?></textarea>
                        </div>
                    </div>

                    <hr/>
                    <h4></h4>


                    <ul class="nav nav-tabs nav-justified" id="jauges" role="tablist">
                        <li class="nav-item active">
                            <a class="nav-link jauge-tab" id="jauge1-tab" data-toggle="tab" href="#jauge0" role="tab">Jauge 1</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link jauge-tab" id="jauge2-tab" data-toggle="tab" href="#jauge1" role="tab">Jauge 2</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link jauge-tab" id="jauge3-tab" data-toggle="tab" href="#jauge2" role="tab">Jauge 3</a>
                        </li>
                    </ul>

                    <div class="tab-content" id="jaugesContent">
                        <?php for($i = 0; $i <3; $i++) {  ?>

                        <div class="tab-pane <?php if ($i==0) echo " active"; ?>" id="jauge<?=$i?>" role="tabpanel">
                            <p>&nbsp;</p>
                            <div class="form-group">
                                <label class="control-label col-lg-3">Nom de variable</label>
                                <div class="col-lg-9">
                                    <input type="text" class="form-control" name="gauge_var[]"
                                           id ="gauge<?=$i?>_var"
                                       value="<?php echo(isset($itemData) ? $itemData->gauge_var[$i] : ''); ?>" />
									<span class="help-block">Un seul mot, que des lettres, pas d'accent.<br>Ce nom est à utiliser dans le fichier excel pour les effets jauges.</span>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="control-label col-lg-3">Libellé</label>
                                <div class="col-lg-9">
                                    <input type="text" class="form-control" name="gauge_label[]"
                                           id ="gauge<?=$i?>_label"
                                           value="<?php echo(isset($itemData) ? $itemData->gauge_label[$i] : ''); ?>" />
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="control-label col-lg-3">Titre Synthèse</label>
                                <div class="col-lg-9">
                                    <input type="text" class="form-control" name="gauge_summary_title[]"
                                           id ="gauge<?=$i?>_summary_title"
                                           value="<?php echo(isset($itemData) ? $itemData->gauge_summary_title[$i] : ''); ?>" />
                                </div>
                            </div>


                            <div class="form-group">
                                <label class="control-label col-lg-3">Pictogramme</label>

                                <div class="col-lg-9">
                                    <input type="hidden" id="gauge_<?=$i?>_picto" name="gauge_picto[]"
                                           value="<?php echo(isset($itemData) ? $itemData->gauge_picto[$i] : $this->config->item('default_gauge_picto')); ?>" />

                                    <button class="btn btn-secondary iconpicker" id="gauge_<?=$i?>_iconpicker"
                                            role="iconpicker" data-icon="<?php echo(isset($itemData) ? $itemData->gauge_picto[$i] : $this->config->item('default_gauge_picto')); ?>"
                                            data-cols="8" data-rows="8"
                                            data-arrow-prev-icon-class="fa fa-angle-left"
                                            data-arrow-next-icon-class="fa fa-angle-right"
                                            data-search-text="" data-label-footer="{0} - {1} / {2}"
                                            data-iconset="fontawesome4" data-iconset-version="4.7.0">
                                    </button>
                                </div>

                            </div>

							<div style="<?php echo(isset($itemData) && $itemData->scenario_type == 1 ? 'display:none; ' : ''); ?>">
								
								<div class="form-group" >
									<label class="control-label col-lg-3">Valeur initiale</label>
									<div class="col-lg-9">
										<input type="number" class="form-control" name="gauge_initial_value[]"
											   value="<?php echo(isset($itemData) ? $itemData->gauge_initial_value[$i] : $this->config->item('default_gauge_initial_value')); ?>" />
								   </div>
								</div>

								<div class="form-group">
									<label class="control-label col-lg-3">Valeur minimum défaite</label>
									<div class="col-lg-9">
										<input type="number" class="form-control" name="gauge_min_value_to_loose[]"
											   value="<?php echo(isset($itemData) ? $itemData->gauge_min_value_to_loose[$i] : $this->config->item('default_gauge_min_value_to_loose')); ?>" />
									</div>
								</div>

								<div class="form-group">
									<label class="control-label col-lg-3">Titre victoire</label>
									<div class="col-lg-9">
										<input type="text" class="form-control" name="gauge_victory_title[]"
											   id ="gauge<?=$i?>_victory_title"
											   value="<?php echo(isset($itemData) ? $itemData->gauge_victory_title[$i] : $this->config->item('default_gauge_victory_title')); ?>" />
									</div>
								</div>

								<div class="form-group">
									<label class="control-label col-lg-3">Texte victoire</label>
									<div class="col-lg-9">
										<input type="text" class="form-control" name="gauge_victory_text[]"
											   id ="gauge<?=$i?>_victory_text"
											   value="<?php echo(isset($itemData) ? $itemData->gauge_victory_text[$i] : ''); ?>" />
									</div>
								</div>

								<div class="form-group">
									<label class="control-label col-lg-3">Titre défaite</label>
									<div class="col-lg-9">
										<input type="text" class="form-control" name="gauge_defeat_title[]"
											   id ="gauge<?=$i?>_defeat_title"
											   value="<?php echo(isset($itemData) ? $itemData->gauge_defeat_title[$i] : $this->config->item('default_gauge_defeat_title')); ?>" />
									</div>
								</div>

								<div class="form-group">
									<label class="control-label col-lg-3">Texte défaite</label>
									<div class="col-lg-9">
										<input type="text" class="form-control" name="gauge_defeat_text[]"
											   id ="gauge<?=$i?>_defeat_text"
											   value="<?php echo(isset($itemData) ? $itemData->gauge_defeat_text[$i] : ''); ?>" />
									</div>
								</div>
								
							</div>

                        </div>
                        <?php } ?>
                    </div>

                    <hr/>
                    <h4>Temporalité</h4>


                    <div class="form-group">
                        <label class="form-check-label col-lg-3 text-right" for="show_temporality">Afficher</label>
                        <div class="col-lg-9">
                            <input type="checkbox" class="form-check-input" id="show_temporality" name="show_temporality"
                                   <?php if ((isset($itemData) ? $itemData->show_temporality:$this->config->item('default_show_temporality'))=='1') echo "checked"; ?>/>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="control-label col-lg-3" for="temporality_labels">Libellés des périodes</label>
                        <div class="col-lg-9">
                            <input type="text" class="form-control " id="temporality_labels" name="temporality_labels"
                                   value="<?php echo(isset($itemData) ? $itemData->temporality_labels : ''); ?>"/>
							<span class="help-block">Libellés de périodes séparés par une virgule,<br>ex.: lundi,mardi,mercredi</span>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="control-label col-lg-3" for="temporality_periods_to_win">Nb périodes</label>
                        <div class="col-lg-9">
                            <input type="number" class="form-control " id="temporality_periods_to_win" name="temporality_periods_to_win"
                                   value="<?php echo(isset($itemData) ? $itemData->temporality_periods_to_win : $this->config->item('default_temporality_periods_to_win')); ?>"/>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="control-label col-lg-3" for="temporality_questions_per_period">Nb questions par période</label>
                        <div class="col-lg-9">
                            <input type="number" class="form-control " id="temporality_questions_per_period" name="temporality_questions_per_period"
                                   value="<?php echo(isset($itemData) ? $itemData->temporality_questions_per_period : $this->config->item('default_temporality_questions_per_period')); ?>"/>
                        </div>
                    </div>

                    <hr/>
                    <h4>Contextes</h4>

                    <?php
						if(ENVIRONMENT != 'development') {
							$dataUrl = base_url('app/data/');
						}else {
							$dataUrl = 'http://localhost:8080/data/';
						}
						for($i = 0; $i <$this->config->item('nb_medias'); $i++) {
							$media_id = isset($itemData) ? $itemData->media_id[$i]: '';
					?>

                        <input type="hidden" name="media_id[]" value="<?php echo $media_id;?>"
                            id="media_id_<?=$i?>" />
                        <div class="form-group">
                            <label class="control-label col-lg-3">Contexte <?=($i+1)?></label>
                            <div class="col-lg-9">
                                <?php if (!empty($media_id)) {?>
                                    <div class="input-group">
                                <?php } ?>
                                    <input type="text" class="form-control media_label"
                                           name="media_label[]"
                                           id="media_label_<?=$i?>"
                                           value="<?php echo isset($itemData) ? $itemData->media_label[$i]:'';?>"/>
                                <?php if (!empty($media_id)) {?>
                                    <div class="input-group-btn">
                                        <button type="button" class="btn btn-default media_delete" id="media_delete_<?=$i?>"
                                        ><span class="glyphicon glyphicon-trash"></span></button>
                                    </div>
                                </div>
                                <?php } ?>
                            </div>
                        </div>
                        <div class="form-group">
                            <?php 
								if (!empty($media_id) && !empty($itemData->media_image_url[$i])) {
							?>
							<img class="col-xs-3 col-sm-offset-1 col-sm-2" src="<?php echo $dataUrl.$itemData->uid.'/'.$itemData->media_image_url[$i]; ?>" >
							<?php 
								}
							?>
							<div class="<?php if (empty($media_id)) { echo 'col-xs-offset-3'; } ?> col-xs-9">
								<span class="help-block">Un seul mot, que des lettres, pas d'accent.<br>Ce nom est à utiliser dans le fichier excel pour les contextes.</span>
                                <input type="file" class="" id="media_file_<?=$i?>" name="media_file[]"/>
                            </div>
                        </div>
                        <div class="form-group"><div class="col-lg-offset-3 col-lg-9 check_error" id="check_error_media_<?=$i?>"></div></div>

                    <?php } ?>

                    <hr class="final"/>
                    <h4>Fichier Excel</h4>

                    <div class="form-group">
                        <div class="col-lg-12">
                            <input type="file" class="" id="xlsxFile" name="xlsxFile"/>
                        </div>
                    </div>
                    <div class="form-group"><div class="col-lg-12 check_error" id="check_error_xlsx"></div></div>

                </form>
				
			</div>
			<div class="box-footer text-center">
				<button type="submit" class="btn btn-primary btn-flat" id="scenarioFormSubmit">Enregistrer</button>
				<a href="<?php echo(base_admin_url('scenarios')); ?>" class="btn btn-default btn-flat">Annuler</a>
			</div>
		</div>
	
	</div>
	
	<?php if(isset($itemViewData)) { ?>
	<div class="modal fade" tabindex="-1" role="dialog" id="itemViewEventsModal">
		<div class="modal-dialog modal-lg" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-label="Fermer"><span aria-hidden="true">&times;</span></button>
					<h4 class="modal-title"><?php echo $itemViewData->title; ?></h4>
				</div>
				<div class="modal-body">
					<ul>
					<?php foreach($itemViewData->events as $event) { ?>
						<li>
							<strong><?php echo $event->title; ?></strong><br>
							<?php echo $event->description; ?>
							<ul>
								<?php foreach($event->choices as $choice) { ?>
								<li><?php echo $choice->content; ?>
									<ul><li><em>Effets jauges : </em><?php echo $choice->command; ?></li>
										<li><em>Synthèse : </em><strong>[<?php echo $choice->summary_gauge_target.']</strong> '.$choice->summary_text; ?></li></ul>
								</li>
								<?php } ?>
							</ul>
						</li>
					<?php } ?>
					</ul>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-default" data-dismiss="modal">Fermer</button>
				</div>
			</div>
			<!-- /.modal-content -->
		</div>
		<!-- /.modal-dialog -->
	</div> <!-- /.modal -->
	<?php } ?>
		
</div>