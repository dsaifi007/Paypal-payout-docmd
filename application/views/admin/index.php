<?php
defined('BASEPATH') OR exit('No direct script access allowed');
/*
This is the main index file, all view load here
*/
$this->load->view("admin/common/header");
$this->load->view("admin/common/top_header");
$this->load->view("admin/common/navigation");

if (isset($view)) {
	echo $view;
}else{
	echo "view not found";
}
$this->load->view("admin/common/footer");
?>