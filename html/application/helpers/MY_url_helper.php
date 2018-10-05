<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

if ( ! function_exists('urlTitle'))
{
	function urlTitle($str, $dash = '-') {
		return url_title(convert_accented_characters( preg_replace('/\'|"/', '-', html_entity_decode($str)) ), $dash, TRUE );
	}
}

if ( ! function_exists('base_admin_url'))
{
	function base_admin_url($str = '') {
		$ci = & get_instance();
		if(empty($str)) {
			return base_url($ci->config->item('admin_url')) . '/';
		}else{
			return base_url($ci->config->item('admin_url') . '/'.$str);
		}
	}
}

if ( ! function_exists('dowino_api_url'))
{
	function dowino_api_url($str = '') {
		$ci = & get_instance();
		if(empty($str)) {
			return $ci->config->item('dowino_api_url');
		}else{
			return $ci->config->item('dowino_api_url') . $str;
		}
	}
}

// Permet d'utiliser le même controlleur pour plusieurs type de user
if ( ! function_exists('is_section_user_partners'))
{
	function is_section_user_partners() {
		$ci = & get_instance();
		return $ci->uri->segment(2)=="partners";
	}
}

if ( ! function_exists('is_section_user_admins'))
{
	function is_section_user_admins() {
		$ci = & get_instance();
		return $ci->uri->segment(2)=="admins";
	}
}

if ( ! function_exists('current_section'))
{
	function current_section() {
		$ci = & get_instance();
		return $ci->uri->segment(2);
	}
}


if ( ! function_exists('get_client_ip'))
{
	// Function to get the client IP address
	function get_client_ip() {
		$ipaddress = '';
		if (getenv('HTTP_CLIENT_IP'))
			$ipaddress = getenv('HTTP_CLIENT_IP');
		else if(getenv('HTTP_X_FORWARDED_FOR'))
			$ipaddress = getenv('HTTP_X_FORWARDED_FOR');
		else if(getenv('HTTP_X_FORWARDED'))
			$ipaddress = getenv('HTTP_X_FORWARDED');
		else if(getenv('HTTP_FORWARDED_FOR'))
			$ipaddress = getenv('HTTP_FORWARDED_FOR');
		else if(getenv('HTTP_FORWARDED'))
		   $ipaddress = getenv('HTTP_FORWARDED');
		else if(getenv('REMOTE_ADDR'))
			$ipaddress = getenv('REMOTE_ADDR');
		else
			$ipaddress = 'UNKNOWN';
		return $ipaddress;
	}
}

if ( ! function_exists('nofollowUrl'))
{
	function nofollowUrl($html, $skip = null) {
		return preg_replace_callback(
			"#(<a[^>]+?)>#is", function ($mach) use ($skip) {
				return (
					!($skip && strpos($mach[1], $skip) !== false) &&
					strpos($mach[1], 'rel=') === false
				) ? $mach[1] . ' rel="nofollow">' : $mach[0];
			},
			$html
		);
	}
}