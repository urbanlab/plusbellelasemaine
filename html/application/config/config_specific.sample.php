<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');


$config['site_name'] = '';
$config['site_baseline'] = '';
$config['site_meta_author'] = '';
$config['site_meta_keywords'] = '';
$config['site_meta_description'] = '';
$config['site_meta_robots'] = 'index,follow';

$config['favicon_path'] = 'images/favicon/';

/*
|	urls
*/
$config['admin_url'] = '/admin';

/*
|	global constants
*/
$config['global_email_from'] = '';
$config['global_email_from_name'] = '';
$config['contact_form_to_email'] = '';
$config['global_admin_email'] = '';

/*
|	list / paging constants
*/
$config['statsMaxChartDays'] = 93; // 3 mois max affichés en jours
$config['statsMaxChartWeeks'] = 52 + 26; // 1,5 an max affichés en semaines

/*
|	additional css files
*/
$config['additional_css_file'] = array(
	'jqueryui' => 'plugins/jquery-ui-1.12.1/jquery-ui.min.css',
	//'datatables' => 'plugins/datatables/dataTables.bootstrap.css',
	'datatables' => 'plugins/datatables/jquery.dataTables.min.css',
	'stats' => 'css/stats.css',
	
);

/*
|	additional js files
*/
$config['additional_js_file'] = array(
	'jqueryui' => 'plugins/jquery-ui-1.12.1/jquery-ui.min.js',
	'datatables' => ['plugins/datatables/jquery.dataTables.min.js', 'plugins/datatables/dataTables.bootstrap.min.js'],
	'chartjs' => ['bower_components/moment/min/moment-with-locales.min.js','bower_components/chart.js/dist/Chart.min.js'],
	'underscore' => 'bower_components/underscore/underscore-min.js',
	
    'stats'=> 'js/stats.js'
);




/*
*	Statistiques
*
*   A noter que la configuration du compte ga pour les stats nécessite aussi le fichier gareporting.json
*   qui est récupéré de la console developer et qui génère un user ayant accès aux stats
*/

// Google analytics View id
$config['googleAnalyticsViewId'] = ''; 

// dates de démarrage des stats
$config['googleAnalyticsStatsStart'] = '';


