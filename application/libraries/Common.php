<?php
/**
* Common function library
*/
class Common 
{
	public function file_upload($folder="assets/file",$field_name,$rename_file='',$file_type='gif|jpg|png|jpeg')
	{
		$CI = &get_instance();
		$CI->config->load('shared');
		$config['upload_path']  = './'.$folder.'/';
		$config['allowed_types']= $file_type;
		$config['max_size']     = $CI->config->item("max_fize");
		$config['max_width']    = $CI->config->item("max_width");
		$config['max_height']   = $CI->config->item("max_height");
		$config['file_name']    = ($rename_file) ? $rename_file : time();
		$CI->load->library('upload', $config);
		$upload_status=(!$CI->upload->do_upload($field_name))?["error"=>$CI->upload->display_errors()]:['upload_data' => $CI->upload->data()];
		return $upload_status;
	}	

}

?>