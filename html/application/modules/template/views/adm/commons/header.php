<!DOCTYPE html>
<?php
    $admin = $this->session->userdata('admin');
?>
<!--[if lt IE 7]> <html class="no-js lt-ie9 lt-ie8 lt-ie7"> <![endif]-->
<!--[if IE 7]> <html class="no-js lt-ie9 lt-ie8"> <![endif]-->
<!--[if IE 8]> <html class="no-js lt-ie9"> <![endif]-->
<!--[if gt IE 8]><!-->
<html class="no-js" lang="fr">
<!--<![endif]-->
<head>
<title>
<?php 
		if (isset($page_title)) {
		    echo $page_title . ' | ' . $this->config->item('site_name');
		}else{
		    echo $this->config->item('site_name') . ' | ' . $this->config->item('site_baseline');
		}
	    ?>
</title>
<?php
		$this->load->view("commons/metas");
		$this->load->view("commons/favicon");
		$this->load->view("commons/stylesheets");
	?>
</head>

<body class="hold-transition skin-blue sidebar-mini">

<!-- Site wrapper -->
<div class="wrapper">
<header class="main-header"> 
	<!-- Logo --> 
	<a href="<?php echo base_admin_url(); ?>" class="logo"> 
	<!-- mini logo for sidebar mini 50x50 pixels --> 
	<span class="logo-mini"><b>Adm</b></span> 
	<!-- logo for regular state and mobile devices --> 
	<span class="logo-lg"><b>Admin</b></span> </a> 
	<!-- Header Navbar: style can be found in header.less -->
	<nav class="navbar navbar-static-top" role="navigation"> 
		<!-- Sidebar toggle button--> 
		<a href="#" class="sidebar-toggle" data-toggle="offcanvas" role="button"> <span class="sr-only">Toggle navigation</span> <span class="icon-bar"></span> <span class="icon-bar"></span> <span class="icon-bar"></span> </a>
		<div class="navbar-custom-menu">
			<ul class="nav navbar-nav">
				
				<li class="dropdown user user-menu"> <a href="#" class="dropdown-toggle" data-toggle="dropdown"> <i class="glyphicon glyphicon-user"></i> <span><i class="caret"></i></span> </a>
					<ul class="dropdown-menu">
						<!-- User image -->
						<li class="user-header bg-light-blue">
							<p>
								<?php
								//$user = $this->session->userdata('user');
								echo($admin['firstname'].' '.$admin['lastname']); 
							?>
							</p>
						</li>
						<!-- Menu Body -->
						<li class="user-body">
							<div class="col-xs-12 text-center"> <a href="#" id="changePasswordBtn"  title="Changer mon mot de passe">Modifier mon mot de passe</a> </div>
						</li>
						<!-- Menu Footer-->
						<li class="user-footer">
							<div class="pull-right"> <a href="<?php echo(base_admin_url()); ?>logout" class="btn btn-default btn-flat" title="Se déconnecter">Se déconnecter</a> </div>
						</li>
					</ul>
				</li>
				
			</ul>
		</div>
	</nav>
</header>


<!-- =============================================== --> 
<?php 
	if(!isset($currentSubSection)) { 
		$currentSubSection = '';
	}
?>
<!-- Left side column. contains the sidebar -->
<aside class="main-sidebar"> 
	<!-- sidebar: style can be found in sidebar.less -->
	<section class="sidebar"> 
		<!-- sidebar menu: : style can be found in sidebar.less -->
		<ul class="sidebar-menu">

		<li class="<?php echo $currentSection == 'home' ? 'active' : ''; ?>"> <a href="<?php echo base_admin_url(); ?>"> <i class="fa fa-home"></i> <span>Accueil</span> </a> </li>

		

		<li class="<?php echo $currentSection == 'scenarios' ? 'active' : ''; ?>"> <a href="<?php echo base_admin_url('scenarios'); ?>"> <i class="fa  fa-files-o"></i> <span>Gestion des scénarios</span> </a> </li>
        
        
		<li class="treeview <?php echo $currentSection == 'stats' ? 'active' : ''; ?>">
			<a href="#" class=""><i class="fa fa-bar-chart"></i> <span>Statistiques</span> <i class="fa fa-angle-left pull-right"></i></a>
			<ul class="treeview-menu">
				<li class="<?php echo $currentSubSection == 'usage' ? 'active' : ''; ?>"><a href="<?php echo base_admin_url('statistiques_utilisation'); ?>" ><i class="fa fa-circle-o"></i> d'utilisation</a></li>
				<li class="<?php echo $currentSubSection == 'retention' ? 'active' : ''; ?>"><a href="<?php echo base_admin_url('taux_retention'); ?>" ><i class="fa fa-circle-o"></i> taux de rétention</a></li>
				<li class="<?php echo $currentSubSection == 'specific' ? 'active' : ''; ?>"><a href="<?php echo base_admin_url('statistiques_specifiques'); ?>" ><i class="fa fa-circle-o"></i> spécifique</a></li>
			</ul>
		</li>
			
		<?php 
			if(intval($admin['type']) <= 1) {
		?>
        <li class="<?php echo $currentSection == 'userbos' ? 'active' : ''; ?>"> <a href="<?php echo base_admin_url('admins'); ?>"> <i class="fa fa-users"></i> <span>Gestion des admins</span> </a> </li>
			
		<?php
			}
		?>

			
		</ul>
	</section>
	<!-- /.sidebar --> 
</aside>

<!-- =============================================== --> 

<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
