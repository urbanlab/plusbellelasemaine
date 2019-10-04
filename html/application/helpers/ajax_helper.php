<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');


if ( ! function_exists('getAjaxPost'))
{
	function getAjaxPost() {
		//return json_decode(file_get_contents('php://input'));
		//return (object) $_POST;
		if(empty($_POST)) {
			return json_decode(file_get_contents('php://input'));
		}else{
			return (object) $_POST;
		}
	}
}
	
if ( ! function_exists('setAjaxHeaders'))
{
	function setAjaxHeaders() {
		header("Access-Control-Allow-Origin: *");//{$_SERVER['HTTP_ORIGIN']}");
		header("Access-Control-Allow-Methods: GET,POST,OPTIONS");
        header("Access-Control-Allow-Headers: X-Requested-With, Accept, Content-Type, Access-Control-Allow-Headers, Authorization");
        header('Content-Type: application/json');
		
		if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
			echo 1;
			die();
		}
	}
}

if ( ! function_exists('setAjaxReturn'))
{
	function setAjaxReturn($success, $data) {
		$ret = new stdClass();
		$ret->success = $success;
		$ret->data = $data;
		echo json_encode($ret);
	}
}