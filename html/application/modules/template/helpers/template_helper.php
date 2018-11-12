<?php

/**
 * additionnal_js_file_call
 *
 * Chargement des fichier JS supplémentaires fournis par le controller ou le module 
 */
if (!function_exists('additionnal_js_file_call')) {

    function additionnal_js_file_call(array $additionnalJsFilesTab) {
        if (isset($additionnalJsFilesTab) && count($additionnalJsFilesTab) > 0 ) {
            $CI = & get_instance();
            $tabJsFile = $CI->config->item('additional_js_file');
            $return = '';
            foreach ($additionnalJsFilesTab as $key => $value) {
               if (array_key_exists($value, $tabJsFile)) {
               		if(is_array($tabJsFile[$value])) {
						foreach($tabJsFile[$value] as $val) {
							if(preg_match('/^http|\/\//', $val) === 0) {
								$return .= '<script type="text/javascript" src="' . base_url() . $val . '"></script>' . "\n";
							}else{
								$return .= '<script type="text/javascript" src="' . $val . '"></script>' . "\n";
							}
						}
					}else{
						if(preg_match('/^http|\/\//', $tabJsFile[$value]) === 0) {
							$return .= '<script type="text/javascript" src="' . base_url() . $tabJsFile[$value] . '"></script>' . "\n";
						}else{
							$return .= '<script type="text/javascript" src="' . $tabJsFile[$value] . '"></script>' . "\n";
						}	
					}
                }
            }
            return $return;
        }
    }

}

/**
 * additionnal_css_file_call
 *
 * Chargement des fichier CSS supplémentaires fournis par le controller ou le module 
 */
if (!function_exists('additionnal_css_file_call')) {

    function additionnal_css_file_call(array $additionnalCssFilesTab) {
        if (isset($additionnalCssFilesTab) && count($additionnalCssFilesTab) > 0 ) {
            $CI = & get_instance();
            $tabCssFile = $CI->config->item('additional_css_file');
            $return = '';
            foreach ($additionnalCssFilesTab as $key => $value) {
                if (array_key_exists($value, $tabCssFile)) {
                	if(is_array($tabCssFile[$value])) {
						foreach($tabCssFile[$value] as $val) {
							if(preg_match('/^http|\/\//', $val) === 0) {
								$return .= '	<link href="'.base_url().$val.'" rel="stylesheet" type="text/css" />'.PHP_EOL;
							}else {
								$return .= '	<link href="'.$val.'" rel="stylesheet" type="text/css" />'.PHP_EOL;
							}
						}
					}else{
						if(preg_match('/^http|\/\//', $tabCssFile[$value]) === 0) {
							$return .= '	<link href="'.base_url().$tabCssFile[$value].'" rel="stylesheet" type="text/css" />'.PHP_EOL;
						}else {
							$return .= '	<link href="'.$tabCssFile[$value].'" rel="stylesheet" type="text/css" />'.PHP_EOL;
						}
					}
                }
            }
            return $return;
        }
    }

}

/**
 * additionnal_js_script_call_wready
 *
 * Call des fonctions JS sur le ready
 */
if (!function_exists('additionnal_js_script_call_wready')) {

    function additionnal_js_script_call_wready(array $additionnalJsCommandesTab) {
        if (isset($additionnalJsCommandesTab) && count($additionnalJsCommandesTab) > 0) {
            $return = '<script>jQuery(document).ready(function(){';
            foreach ($additionnalJsCommandesTab as $commande) {
                $return .= $commande;
            }
            $return .= '});</script>';
            return $return;
        }
    }

}

/**
 * additionnal_js_script_call_wload
 *
 * Call des fonctions JS sur le load
 */
if (!function_exists('additionnal_js_script_call_wload')) {

    function additionnal_js_script_call_wload(array $additionnalJsCommandesTab) {
        if (isset($additionnalJsCommandesTab) && count($additionnalJsCommandesTab) > 0) {
            $return = '<script>jQuery(window).load(function() {';
            foreach ($additionnalJsCommandesTab as $commande) {
                $return .= $commande;
            }
            $return .= '});</script>';
            return $return;
        }
    }

}

/**
 * additionnal_js_script_call_wload
 *
 * Call des fonctions JS sur le scroll
 */
if (!function_exists('additionnal_js_script_call_wscroll')) {

    function additionnal_js_script_call_wscroll(array $additionnalJsCommandesTab) {
        if (isset($additionnalJsCommandesTab) && count($additionnalJsCommandesTab) > 0) {
            $return = '<script>jQuery(window).scroll(function() {';
            foreach ($additionnalJsCommandesTab as $commande) {
                $return .= $commande;
            }
            $return .= '});</script>';
            return $return;
        }
    }

}

/**
 * additionnal_js_script_call_wload
 *
 * Call des fonctions JS sur le resize 
 */
if (!function_exists('additionnal_js_script_call_wresize')) {

    function additionnal_js_script_call_wresize(array $additionnalJsCommandesTab) {
        if (isset($additionnalJsCommandesTab) && count($additionnalJsCommandesTab) > 0) {
            $return = '<script>jQuery(window).smartresize(function() {';
            foreach ($additionnalJsCommandesTab as $commande) {
                $return .= $commande;
            }
            $return .= '});</script>';
            return $return;
        }
    }

}

/**
 * breadcrumb_generate
 *
 * Génération du breadcrumb
 */
if (! function_exists('breadcrumb_generate')) {
    function breadcrumb_generate(array $breadCrumbTab){        
        if (isset($breadCrumbTab) && count($breadCrumbTab) > 0) {
            $return = "<div class=\"row breadcrumb-row\"><div class=\"col-xs-12\"><ol class=\"breadcrumb\">";
            foreach ($breadCrumbTab as $key => $value) {
                $classActive = ($value == NULL ? ' class="active"' : '');
                $label = ($value != NULL ? '<a href="'.$value.'">'.$key.'</a>' : $key);
                $return .= "<li$classActive>".$label."</li>";
                
            }
            $return .= "</ol></div></div>";
            return $return;
        }
    }
}


/**
 * Hydratation des variables
 */
if( !function_exists('hydratation_variables_tpl')){
    function hydratation_variables_tpl($data){
        if ( isset($data['additionnalJsFiles']) && !is_array($data['additionnalJsFiles'])) {           
            show_error('La variable <code>$additionnalJsFiles</code> n\'est pas un tableau');die();
        }
        if( !isset($data['additionnalJsFiles']) ){
            $data['additionnalJsFiles'] = array();
        }
        
        if ( isset($data['additionnalCssFiles']) && !is_array($data['additionnalCssFiles'])) {           
            show_error('La variable <code>$additionnalCssFiles</code> n\'est pas un tableau');die();
        }
        if( !isset($data['additionnalCssFiles']) ){
            $data['additionnalCssFiles'] = array();
        }
        
        if ( isset($data['additionnalJsCmd_wready']) && !is_array($data['additionnalJsCmd_wready'])) {           
            show_error('La variable <code>$additionnalJsCmd_wready</code> n\'est pas un tableau');die();
        }
        if( !isset($data['additionnalJsCmd_wready']) ){
            $data['additionnalJsCmd_wready'] = array();
        }
        
        if ( isset($data['additionnalJsCmd_wload']) && !is_array($data['additionnalJsCmd_wload'])) {           
            show_error('La variable <code>$additionnalJsCmd_wload</code> n\'est pas un tableau');die();
        }
        if( !isset($data['additionnalJsCmd_wload']) ){
            $data['additionnalJsCmd_wload'] = array();
        }
        
        if ( isset($data['additionnalJsCmd_wscroll']) && !is_array($data['additionnalJsCmd_wscroll'])) {           
            show_error('La variable <code>$additionnalJsCmd_wscroll</code> n\'est pas un tableau');die();
        }
        if( !isset($data['additionnalJsCmd_wscroll']) ){
            $data['additionnalJsCmd_wscroll'] = array();
        }
        
        if ( isset($data['additionnalJsCmd_wresize']) && !is_array($data['additionnalJsCmd_wresize'])) {           
            show_error('La variable <code>$additionnalJsCmd_wresize</code> n\'est pas un tableau');die();
        }
        if( !isset($data['additionnalJsCmd_wresize']) ){
            $data['additionnalJsCmd_wresize'] = array();
        }
        
        if ( isset($data['breadCrumb']) && !is_array($data['breadCrumb'])) {           
            show_error('La variable <code>$breadCrumb</code> n\'est pas un tableau');die();
        }
        if( !isset($data['breadCrumb']) ){
            $data['breadCrumb'] = array();
        }
        return $data;
    }
}