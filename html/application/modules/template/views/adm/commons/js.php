<!-- SCRIPTS --> 

<!-- jQuery 2.1.4 -->
<script type="text/javascript" src="//ajax.googleapis.com/ajax/libs/jquery/2.1.4/jquery.min.js"></script>
<!-- Bootstrap 3.3.5 -->
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js" integrity="sha384-0mSbJDEHialfmuBBQP6A4Qrprq5OVfW37PRR3j5ELqxss1yVqOtnepnHVP9aJ7xS" crossorigin="anonymous"></script>
<?php /*
<!-- SlimScroll -->
<script type="text/javascript" src="../../plugins/slimScroll/jquery.slimscroll.min.js"></script>
<!-- FastClick -->
<script type="text/javascript" src="../../plugins/fastclick/fastclick.min.js"></script>
*/ ?>
<!-- Date picker -->
<script type="text/javascript" src="<?php echo base_url(); ?>js/libs/bootstrap-datepicker.min.js"></script>
<script type="text/javascript" src="<?php echo base_url(); ?>js/libs/bootstrap-datepicker.fr.min.js"></script>

<!-- Bootstrap Dialog -->
<script type="text/javascript" src="<?php echo base_url(); ?>js/libs/bootstrap-dialog.min.js"></script>

<!-- AdminLTE App -->
<script type="text/javascript" src="<?php echo base_url(); ?>js/adminLTE/app.min.js"></script>
<!-- Admin -->
<script type="text/javascript" src="<?php echo base_url(); ?>js/admin/master.js"></script> 

<?php 
	if(Modules::run('security/_is_admin_connected') == TRUE) {
?>
	<script type="text/javascript" src="<?php echo base_url(); ?>js/userbos.js"></script>
	<script type="text/javascript">
		userbos.init();
	</script>
<?php
	}
?>


<!-- cookie consent : https://cookieconsent.insites.com -->
<link rel="stylesheet" type="text/css" href="//cdnjs.cloudflare.com/ajax/libs/cookieconsent2/3.1.0/cookieconsent.min.css" />
<script src="//cdnjs.cloudflare.com/ajax/libs/cookieconsent2/3.1.0/cookieconsent.min.js"></script>
<script>
window.addEventListener("load", function(){
window.cookieconsent.initialise({
  "palette": {
    "popup": {
      "background": "#656668",
      "text": "#ffffff"
    },
    "button": {
      "background": "#3c8dbc",
      "text": "#ffffff"
    }
  },
  "showLink": false,
  "content": {
    "message": "En poursuivant votre navigation sur ce site, vous acceptez l’utilisation de Cookies. Ceux-ci sont nécessaires et uniquement à l'usage de la navigation et l'accès aux zones sécurisées du back-office. Aucun cookies traçeurs ou à des fins de statistiques ne sont utilisés. ",
    "dismiss": "J'accepte"
  }
})});
</script>



<script type="text/javascript">
	var CI = {
	  'base_url': '<?php echo base_url(); ?>',
	  'base_admin_url' : '<?php echo base_admin_url(); ?>',
	  'base_admin_section_url' : '<?php echo(base_admin_url(current_section())); ?>'
	};
</script>


<?php echo additionnal_js_file_call($additionnalJsFiles);?>

<?php echo additionnal_js_script_call_wready($additionnalJsCmd_wready);?>

<?php echo additionnal_js_script_call_wload($additionnalJsCmd_wload);?>

<?php echo additionnal_js_script_call_wscroll($additionnalJsCmd_wscroll);?>

<?php echo additionnal_js_script_call_wresize($additionnalJsCmd_wresize);?>

<!-- / SCRIPTS -->