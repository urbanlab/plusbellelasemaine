<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * resize_and_crop_image_to_fit_all
 *
 * 
 *
 * @access	public
 * @param	array
 * @param	string
 * @param	int
 * @param	int
 * @return	string
 */
if ( ! function_exists('resize_and_crop_image_to_fit_all'))
{
	function resize_and_crop_image_to_fit_all($source, $destPath, $width, $height)
	{
		$ret = 1;
		
		$CI =& get_instance();
		$CI->load->library('image_lib');
		
		$ratio = $source['image_width'] / $width;
		if($ratio > $source['image_height'] / $height) {
			$ratio = $source['image_height'] / $height;
		}
		
		$config['image_library'] = 'gd2';
		$config['source_image'] = $source['full_path'];
		$config['new_image'] = $destPath;
		$config['maintain_ratio'] = FALSE;
		$config['width'] = $source['image_width'] / $ratio;
		$config['height'] = $source['image_height'] / $ratio;
		
		$CI->image_lib->clear();
		$CI->image_lib->initialize($config); 
		if(!$CI->image_lib->resize()) {
			$CI->image_lib->clear();
			$ret = $CI->image_lib->display_errors();
		}else{
			
			$CI->image_lib->clear();
			unset($config);
			$config['image_library'] = 'gd2';
			$config['source_image'] = $destPath;
			$config['x_axis'] = (($source['image_width'] / $ratio) - $width) / 2;
			$config['y_axis'] = (($source['image_height'] / $ratio) - $height) / 2;
			$config['width'] = $width;
			$config['height'] = $height;
			$CI->image_lib->initialize($config); 
			if ( ! $CI->image_lib->crop())
			{
				$ret = $CI->image_lib->display_errors();
			}else{
				$ret = 1;
			}
		}
		$CI->image_lib->clear();
		
		return $ret;
	}
}

/**
 * resize_image_to_fit_container
 *
 * 
 *
 * @access	public
 * @param	array
 * @param	string
 * @param	int
 * @param	int
 * @return	string
 */
if ( ! function_exists('resize_image_to_fit_container'))
{
	function resize_image_to_fit_container($source, $destPath, $width, $height)
	{
		$ret = 1;
		
		$CI =& get_instance();
		$CI->load->library('image_lib');
		
		$ratio = $source['image_width'] / $width;
		if($ratio < $source['image_height'] / $height) {
			$ratio = $source['image_height'] / $height;
		}
		
		$config['image_library'] = 'gd2';
		$config['source_image'] = $source['full_path'];
		$config['new_image'] = $destPath;
		$config['maintain_ratio'] = FALSE;
		$config['width'] = $source['image_width'] / $ratio;
		$config['height'] = $source['image_height'] / $ratio;
		
		$CI->image_lib->clear();
		$CI->image_lib->initialize($config); 
		if(!$CI->image_lib->resize()) {
			$CI->image_lib->clear();
			$ret = $CI->image_lib->display_errors();
		}else{
			
			$CI->image_lib->clear();
			unset($config);
			$config['image_library'] = 'gd2';
			$config['source_image'] = $destPath;
			$config['x_axis'] = (($source['image_width'] / $ratio) - $width) / 2;
			$config['y_axis'] = (($source['image_height'] / $ratio) - $height) / 2;
			$config['width'] = $width;
			$config['height'] = $height;
			$config['maintain_ratio'] = FALSE;
			
			$CI->image_lib->initialize($config); 
			if ( ! $CI->image_lib->crop())
			{
				$ret = $CI->image_lib->display_errors();
			}else{
				$ret = 1;
			}
		}
		$CI->image_lib->clear();
		
		return $ret;
	}
}


/**
 * resize_image_to_max_width
 *
 * 
 *
 * @access	public
 * @param	array
 * @param	string
 * @param	int
 * @return	string
 */
if ( ! function_exists('resize_image_to_max_width'))
{
	function resize_image_to_max_width($source, $destPath, $maxWidth)
	{
		$ret = 1;
		
		$CI =& get_instance();
		$CI->load->library('image_lib');
		
		if($source['image_width'] <= $maxWidth) {
			$ratio = 1;
		}else{
			$ratio = $source['image_width'] / $maxWidth;
		}
		
		$config['image_library'] = 'gd2';
		$config['source_image'] = $source['full_path'];
		$config['new_image'] = $destPath;
		$config['maintain_ratio'] = FALSE;
		$config['width'] = $source['image_width'] / $ratio;
		$config['height'] = $source['image_height'] / $ratio;
		
		$CI->image_lib->clear();
		$CI->image_lib->initialize($config); 
		if(!$CI->image_lib->resize()) {
			$CI->image_lib->clear();
			$ret = $CI->image_lib->display_errors();
		}
		$CI->image_lib->clear();
		
		return $ret;
	}
}