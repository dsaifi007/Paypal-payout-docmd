
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
                            <li class="nav-item"><a href="#" class="active show">FAQs</a>
                            </li>
                            <li class="nav-item"><a href="<?php echo base_url(); ?>admin/manage_app_content/manage_appcontent_controller/faq_display/5" class="">Categories List</a>
                            </li>
                        </ul>
                    </header>
                    <div class="panel-body">
                        <div class="tab-content">
                            <div class="col-md-12">
                                <div class="row">
                                    <div class="col-md-4">
                                        <?php
                                        echo form_open("admin/manage_app_content/manage_appcontent_controller/faq_display");
                                        ?>

                                        <select class="form-control valid" name="type" onchange="this.form.submit()" aria-invalid="false">
                                            <option value="">Please select</option>
                                            <option value="doctor" <?php echo (isset($filter_data) && (@$filter_data == "doctor")) ? "selected" : ''; ?>>Provider</option>
                                            <option value="user" <?php echo (isset($filter_data) && (@$filter_data == "user")) ? "selected" : ''; ?>>User</option>                               
                                        </select>
                                        <?php echo form_close(); ?>
                                    </div>
                                    <div class="col-md-3"></div>
                                    <div class="col-md-5">
                                        <div class="btn-group">
                                            <a href="<?php echo base_url(); ?>admin/manage_app_content/manage_appcontent_controller/faq_display/1" class="btn btn-default">Add New Category Name <i class="fa fa-plus-circle" aria-hidden="true"></i>
                                            </a>
                                        </div>
                                        <div class="btn-group pull-right">
                                            <a href="<?php echo base_url(); ?>admin/manage_app_content/manage_appcontent_controller/faq_display/2" class="btn btn-default">Add Faq <i class="fa fa-plus-circle" aria-hidden="true"></i>
                                            </a>
                                        </div>
                                    </div>

                                </div>
                                <?php
                                echo display_message_info([1 => $success, 2 => $error, 3 => validation_errors()]);
                                ?>
                            </div>
                            <table class="table table-striped table-bordered table-hover table-checkable order-column valign-middle" id="faq_list" class="display" style="width:100%;">
                                <thead>
                                    <tr>
                                        <th>S.No.</th>
                                        <th>Category</th>
                                        <th class="text-center">FAQs</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>

                                    <?php
                                    $i = 0;
                                    echo "<pre>";
                                    //dd($items);
                                    if (count($items) > 0 && !empty($items)) {
                                        foreach ($items as $k => $v) {
                                            ?>
                                            <tr>
                                                <td><?php echo $v['id']; ?></td>
                                                <td><?php echo $v['category']; ?>
                                                    <br><?php echo $v['sp_category']; ?></td>
                                                <td>
                                                    <?php
                                                    foreach ($v['qus_ans_eng'] as $key => $value) {
                                                        foreach ($value as $kk => $val) {
                                                            foreach ($val as $kk => $vv) {
                                                                ?>
                                                                <?php
                                                                $sr = ($i == 0) ? "Qusetion -   " : '  Answer - ';
                                                                //echo $vv."<br>";
                                                                //print_r($val);
                                                                echo $sr . ucfirst($vv) . "<br>";
                                                                //echo $sr . ucfirst($val[$kk]) . "<br>";
                                                                $i++;
                                                                
                                                            }
                                                             $i = 0;
                                                            ?>

                                                            <?php
                                                        }
                                                       
                                                    }
                                                    ?>
                                                </td>
                                                <td>
                                                    <a href="<?php echo base_url(); ?>admin/manage_app_content/manage_appcontent_controller/faq_display/3/<?php echo $v['id']; ?>" class="btn btn-primary btn-xs">	<i class="fa fa-pencil"></i>
                                                    </a>
                                                    <a href="<?php echo base_url(); ?>admin/manage_app_content/manage_appcontent_controller/delete_faq/<?php echo $v['cat_id']; ?>" class="btn btn-danger btn-xs" onclick="return confirm('Are you sure you want to delete this item')">	<i class="fa fa-remove"></i>
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
