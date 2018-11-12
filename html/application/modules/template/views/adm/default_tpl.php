<?php $this->load->view('commons/header'); ?>

<!-- Content Header (Page header) -->
<section class="content-header">
	<h1><?php echo $mainTitle; /*<small>it all starts here</small>*/ ?></h1>
  	<?php if (isset($breadcrumb)) { ?>
	<ol class="breadcrumb">
		<li><a href="<?php echo base_admin_url(); ?>"><i class="fa fa-home"></i></a></li>
		<?php
			foreach($breadcrumb as $key => $value) {
		?>
		<li <?php echo $value == '' ? 'class="active"' : ''; ?>><?php echo $value != '' ? '<a href="'.base_url().'admin/'.$value.'">' : ''; ?><?php echo $key; ?><?php echo $value != '' ? '</a>' : ''; ?></li>
		<?php
			}
		?>
	</ol>
	<?php } ?>
</section>

<!-- Main content -->
<section class="content">

	<?php $this->load->view($view_file); ?>

</section><!-- /.content -->

<?php $this->load->view('commons/footer'); ?>