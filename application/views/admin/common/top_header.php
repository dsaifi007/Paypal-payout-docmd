<!-- start header -->
<div class="page-header navbar navbar-fixed-top">
    <div class="page-header-inner ">
        <!-- logo start -->
        <div class="page-logo">
            <a href="<?php echo base_url(); ?>/dashboard">
                <img src="<?php echo base_url(); ?>assets/admin/img/docmd-white-logo.png" alt="" />
            </a>
        </div>
        <!-- logo end -->
        <ul class="nav navbar-nav navbar-left in">
            <li><a href="#" class="menu-toggler sidebar-toggler"><i class="icon-menu"></i></a></li>
        </ul>
        <!-- start mobile menu -->
        <a href="javascript:;" class="menu-toggler responsive-toggler" data-toggle="collapse" data-target=".navbar-collapse">
            <span></span>
        </a>
        <!-- end mobile menu -->
        <!-- start header menu -->
        <div class="top-menu">
            <ul class="nav navbar-nav pull-right">

                <!-- start notification dropdown -->

                <!-- end notification dropdown -->
                <!-- start manage user dropdown -->
                <li class="dropdown dropdown-user">
                    <a href="javascript:;" class="dropdown-toggle" data-toggle="dropdown" data-hover="dropdown" data-close-others="true">
                        <span class="username username-hide-on-mobile"> Admin </span>
                        <i class="fa fa-angle-down"></i>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-default">
                        <li>
                            <a href="<?php echo base_url(); ?>admin/login/change_password">
                                <i class="icon-lock"></i> Change Password
                            </a>
                        </li>
                        <li>
                            <a href="<?php echo base_url(); ?>admin/login/admin_login/logout"><i class="icon-logout"></i> Log Out </a>
                        </li>
                    </ul>
                </li>
            </ul>
        </div>
    </div>
</div>
<!-- end header -->

<!-- start page container -->
<div class="page-container">