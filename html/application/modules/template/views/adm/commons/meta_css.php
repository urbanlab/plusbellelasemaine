<!DOCTYPE html>
<!--[if lt IE 7]> <html class="no-js lt-ie9 lt-ie8 lt-ie7"> <![endif]-->
<!--[if IE 7]> <html class="no-js lt-ie9 lt-ie8"> <![endif]-->
<!--[if IE 8]> <html class="no-js lt-ie9"> <![endif]-->
<!--[if gt IE 8]><!--> <html class="no-js" lang="fr"> <!--<![endif]-->
<head>
	<title><?php 
		if (isset($page_title)) {
		    echo $page_title . ' | ' . $this->config->item('site_name');
		}else{
		    echo $this->config->item('site_name') . ' | ' . $this->config->item('site_baseline');
		}
	    ?></title>

	<?php
		$this->load->view("commons/metas");
		$this->load->view("commons/favicon");
		$this->load->view("commons/stylesheets");
	?>

</head>



