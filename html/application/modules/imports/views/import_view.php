<?php
    $admin = $this->session->userdata('admin');
?>
<div class="row">
	<div class="col-sm-6 ">
        <form action="<?php echo(base_admin_url(current_section().'/importValidate')) ?>" method="post" enctype="multipart/form-data">
            <div class="box">

                <div class="box-header">
                    Séléctionner un fichier XLSX à charger
                </div>

                <div class="box-body">
					<input type="hidden" id="scenarioId" name="scenarioId" value="1" />
                    <input type="file" name="xlsxFile" />
                    <br>
                    <input type="submit" value="Importer" />
                </div>
                <div class="box-footer">

                    <?php if($this->session->flashdata('importErrorMessages')) {
                        foreach ($this->session->flashdata('importErrorMessages') as $row){
                            ?>
                            <div class="alert alert-danger alert-dismissable ">
                                <i class="fa fa-ban"></i> &nbsp;
                                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                                <?php echo($row); ?>
                            </div>
                            <?php
                        }
                    } ?>
                    <?php if($this->session->flashdata('importFileUploadError')){
                        ?>
                        <div class="alert alert-danger alert-dismissable ">
                            <i class="fa fa-ban"></i> &nbsp;
                            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                            <?php echo( $this->session->flashdata('importFileUploadError')); ?>
                        </div>
                        <?php
                    } ?>

                    <?php if($this->session->flashdata('importConfirmMessage')) { ?>
                        <div class="alert alert-success alert-dismissable alert-auto-close">
                            <i class="glyphicon glyphicon-ok"> </i>&nbsp;
                            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                            <?php echo 'Table is valid'; ?>
                        </div>
                    <?php } ?>
                </div>
            </div>
        </form>
    </div>

    <div class="col-sm-6">
        <form action="<?php echo(base_admin_url('export')) ?>" method="post" enctype="multipart/form-data">
            <div class="box">

                <div class="box-header">
                    Exportez la base de donnée vers un fichier XLSX
                </div>

                <div class="box-body">
                    <input type="submit" value="Exporter" />
                </div>
                <div class="box-footer">

                </div>
            </div>
        </form>




    </div>
	<?php /*
    <div class="col-sm-6 ">
        <form action="<?php echo(base_admin_url(current_section().'/importJson')) ?>" method="post" enctype="multipart/form-data">
            <div class="box">

                <div class="box-header">
                    Séléctionner un fichier json à charger
                </div>

                <div class="box-body">
                    <input type="file" name="jsonFile" />
                    <br>
                    <input type="submit" value="Importer" />
                </div>
            </div>
        </form>
    </div>
	*/ ?>
</div>