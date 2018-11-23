
<style>
    .nav-tabs .nav-item{
        width: 33%;
        text-align: center;
        border: 1px solid #e9e9e9;
    }
    .nav-tabs .nav-item>a{
        font-size:18px;
    }
    .tab-heading{
        padding: 10px;
        border-bottom: 1px solid #eee;
        font-weight: 600;
    }
    .custom-tab .nav-tabs .nav-item {
        width: 25%;
    }
</style>
<?php
?>
<div class="page-content-wrapper">
    <div class="page-content">
        <div class="page-bar">
            <div class="page-title-breadcrumb">
                <div class=" pull-left">
                    <div class="page-title">Manage In-App Content</div>
                </div>
                <ol class="breadcrumb page-breadcrumb pull-right">
                    <li><i class="fa fa-home"></i>&nbsp;<a class="parent-item" href="<?php echo base_url();?>dashboard">Home</a>&nbsp;<i class="fa fa-angle-right"></i>
                    </li>
                    <li class="active">Manage In-App Content</li>
                </ol>
            </div>
        </div>
        <!-- add content here -->
        <div class="row">
            <div class="col-md-12">
                <div class="panel tab-border card-topline-green">
                    <header class="panel-heading panel-heading-gray custom-tab ">
                        <ul class="nav nav-tabs">
                            <li class="nav-item"><a href="<?php base_url(); ?><?php echo base_url(); ?>admin/manage_app_content/manage_appcontent_controller" class="">Term and Condition</a>
                            </li>
                            <li class="nav-item"><a href="<?php echo base_url(); ?>admin/manage_app_content/manage_appcontent_controller/faq_display/4" >Consent to care</a>
                            </li>
                            <li class="nav-item"><a href="<?php echo base_url(); ?>admin/manage_app_content/manage_appcontent_controller/faq_display" >FAQs</a>
                            </li>
                            <li class="nav-item"><a href="<?php echo base_url(); ?>admin/manage_app_content/manage_appcontent_controller/faq_display/5" class="active show">Categories List</a>
                            </li>
                        </ul>
                    </header>
                    <div class="panel-body">
                        <div class="tab-content">
                            <div class="col-md-12">

                                <?php
                                echo display_message_info([1 => $success, 2 => $error, 3 => validation_errors()]);
                                ?>
                            </div>
                            <table class="table table-striped table-bordered table-hover table-checkable order-column valign-middle" id="faq_list" class="display" style="width:100%;">
                                <thead>
                                    <tr>
                                        <th>S.No.</th>
                                        <th>Category Name</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    if (count($items) > 0 && !empty($items)) {
                                        foreach ($items as $k => $v) {
                                            ?>
                                            <tr>
                                                <td><?php echo $v['id']; ?></td>
                                                <td><?php echo $v['category']; ?>
                                                    <br><?php echo $v['sp_category']; ?></td>                                   
                                                <td>
                                                    <a href="<?php echo base_url(); ?>admin/manage_app_content/manage_appcontent_controller/faq_display/1/<?php echo $v['id']; ?>" class="btn btn-primary btn-xs">	<i class="fa fa-pencil"></i>
                                                    </a>
                                                    <a href="<?php echo base_url(); ?>admin/manage_app_content/manage_appcontent_controller/delete_cat/<?php echo $v['id']; ?>" class="btn btn-danger btn-xs" onclick="return confirm('Are you sure you want to delete this item')">	<i class="fa fa-remove"></i>
                                                    </a>
                                                </td>
                                            </tr>

                                            <?php
                                        }
                                    }
                                    ?>        
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</div>
