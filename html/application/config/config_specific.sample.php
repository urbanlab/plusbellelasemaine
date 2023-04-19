<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');


$config['site_name'] = 'POC - Plus belle la semaine';
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
$config['global_email_from'] = 'no-reply@erasme.org';
$config['global_email_from_name'] = 'Admin POC Plus belle la semaine';
$config['contact_form_to_email'] = 'no-reply@erasme.org';
$config['global_admin_email'] = 'no-reply@erasme.org';

/*
|	Default scenario values
*/
$config['nb_medias'] = 10;

$config['default_gauge_initial_value'] = '30';
$config['default_gauge_min_value_to_loose'] = '10';
$config['default_gauge_picto'] = 'fa-eur';
$config['default_gauge_victory_title'] = 'Victoire !';
$config['default_gauge_defeat_title'] = 'Game Over';
$config['default_show_temporality'] = '1';
$config['default_temporality_periods_to_win'] = '1';
$config['default_temporality_questions_per_period'] = '3';


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
    'iconpicker' => 'css/libs/iconpicker.min.css',

	'stats' => 'css/stats.css',
	'scenario' => 'css/scenario.css',

);

/*
|	additional js files
*/
$config['additional_js_file'] = array(
	'jqueryui' => 'plugins/jquery-ui-1.12.1/jquery-ui.min.js',
	'datatables' => ['plugins/datatables/jquery.dataTables.min.js', 'plugins/datatables/dataTables.bootstrap.min.js'],
	'chartjs' => ['bower_components/moment/min/moment-with-locales.min.js','bower_components/chart.js/dist/Chart.min.js'],
	'underscore' => 'bower_components/underscore/underscore-min.js',
    'iconpicker' => 'js/libs/iconpicker.min.js',
	
    'stats'=> 'js/stats.js',
    'scenario'=> 'js/scenario.js',
);




/*
*	Statistiques
*
*   A noter que la configuration du compte ga pour les stats nécessite aussi le fichier gareporting.json
*   qui est récupéré de la console developer et qui génère un user ayant accès aux stats
*/


// Google analytics View id
$config['googleAnalyticsViewId'] = '178245522'; 

// dates de démarrage des stats
$config['googleAnalyticsStatsStart'] = '2018-01-01';


