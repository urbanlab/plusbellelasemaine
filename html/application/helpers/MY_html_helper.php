<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

if ( ! function_exists('pr'))
{
	function pr($str, $return = FALSE) {
		$ret = "<pre>\n".print_r($str, TRUE)."\n</pre>\n";
		if($return) {
			return $ret;
		}else{
			echo $ret;
		}
	}
}