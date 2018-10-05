<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * affiche un temps xx:xx:xx
 *
 */
if (!function_exists('displaySeconds'))
{
	function displaySeconds($duration) 
	{
		$sec=$duration%60;
		$min=intval($duration/60);
		$hour=intval($min/60);
		$min=$min%60;

		if (strlen($hour)==1) $hour = "0".$hour;
		$min=str_pad($min, 2, "00", STR_PAD_LEFT);
		$sec=str_pad($sec, 2, "00", STR_PAD_LEFT);
		return $hour.':'.$min.':'.$sec;
	}
}

/**
 * escape des caractères gênants de l'xml
 *
 */
if (!function_exists('escapeForXml'))
{
	function escapeForXml($str) 
	{
    	return strtr($str, 
	        array(
	            "<" => "&lt;",
	            ">" => "&gt;",
	            '"' => "&quot;",
	            "'" => "&apos;",
	            "&" => "&amp;"
	        )
	    );
	}
}