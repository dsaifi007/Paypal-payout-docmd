<!DOCTYPE html>
<html lang="en">
<!-- BEGIN HEAD -->
<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge">

    <meta content="width=device-width, initial-scale=1" name="viewport" />
    <meta name="description" content="Chromeinfotech" />
    <meta name="author" content="Chromeinfotech" />
    <title><?php 
    echo (isset($page_title)) ? $page_title : "Home";
    //echo ($this->lang->line("page_title")) ? $this->lang->line("page_title") :"Home"; ?> | DOCMD</title>
    <!-- google font   -->
    <link href="http://fonts.googleapis.com/css?family=Open+Sans:400,300,600,700&amp;subset=all" rel="stylesheet" type="text/css" />
    <!-- icons -->
    <link href="<?php echo base_url();?>assets/assets/simple-line-icons/simple-line-icons.min.css" rel="stylesheet" type="text/css" />
    
    <link href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet" type="text/css"/>

  <link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons">
    <script>
        var site_url = "<?php echo base_url();?>";
    </script>
    <!--bootstrap --> 
    <link href="<?php echo base_url();?>assets/admin/css/common/bootstrap.min.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo base_url();?>assets/admin/css/common/dataTables.bootstrap4.min.css" rel="stylesheet" type="text/css"/>

    <!-- Material Design Lite CSS -->
    <link rel="stylesheet" href="<?php echo base_url();?>assets/admin/css/common/material.min.css">
    <link rel="stylesheet" href="<?php echo base_url();?>assets/admin/css/common/material_style.css">

    <!-- Theme Styles -->
    <link href="<?php echo base_url();?>assets/admin/css/common/theme_style.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo base_url();?>assets/admin/css/common/theme-color.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo base_url();?>assets/admin/css/common/style.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo base_url();?>assets/admin/css/common/plugins.min.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo base_url();?>assets/admin/css/common/formlayout.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo base_url();?>assets/admin/css/common/responsive.css" rel="stylesheet" type="text/css" />
    <!-- favicon -->
    <link rel="shortcut icon" href="<?php echo  base_url(); ?>assets/admin/img/favicon.ico" />

    <?php
    if (isset($add_css)) {
        ?>
      <link href="<?php echo base_url();?>assets/admin/css/<?php echo $add_css; ?>"  rel="stylesheet" type="text/css" />
    <?php
    }
    ?>
<script src="<?php echo base_url();?>assets/assets/jquery.min.js" ></script>
<style>
    #datatable1_info{display:none;}
    /*.mdl-textfield--floating-label .mdl-textfield__label{
        display:none;
    }
    .mdl-textfield__input{
        border:1px solid #ccc;
    }*/
    .tools{
        display:none !important;
    }
/*    .breadcrumb{
       display:none !important; 
    }*/
</style>

</head>
<!-- END HEAD -->
<body class="page-header-fixed sidemenu-closed-hidelogo page-content-white page-md header-white dark-sidebar-color logo-indigo" onload="initialize()">
    <div class="top-arrow">
        <a href="#"><i class="icon-arrow-up scrollup"></i></a>
    </div>
    <div class="page-wrapper">


