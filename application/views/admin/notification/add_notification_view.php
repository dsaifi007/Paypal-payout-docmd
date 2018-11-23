<div class="page-content-wrapper">
    <div class="page-content">
        <div class="page-bar">
            <div class="page-title-breadcrumb">
                <div class="pull-left">
                    <div class="page-title">Add Notifications</div>
                </div>
                <ol class="breadcrumb page-breadcrumb pull-right">
                    <li><i class="fa fa-home"></i>&nbsp;<a class="parent-item" href="<?php echo base_url(); ?>admin/notification/admin_notification_controller/">Manage Notifications</a>&nbsp;<i class="fa fa-angle-right"></i>
                    </li>
                    <li class="active">Add New Notifications</li>
                </ol>
            </div>
        </div>
        <!-- add content here -->
        <div class="row">
            <div class="col-md-10 col-xs-10 user-details">
                <div class="white-box">
                    <div class="card-body " id="bar-parent6">
                        <?php echo display_message_info([1 => @$success, 2 => @$error, 3 => validation_errors()]); ?>

                        <?php echo form_open_multipart("admin/notification/admin_notification_controller/add_notification_info"); ?>
                        <!-- .row -->
                        <div class="row text-center m-t-10">
                             <div class="col-md-12">
                                <input class="form-control" name="name" placeholder="Enter Notifications Title (English)" type="text">
                                <textarea name="additional_info" placeholder="Enter Notifications Description (English)" class="form-control-textarea m-t-20" rows="5" aria-invalid="false"></textarea>
                             </div>
                            <div class="col-md-12"><br>
                                <input class="form-control" name="sp_name" type="text" placeholder="Enter Notifications Title (Spanish)">
                                <textarea name="sp_additional_info" placeholder="Enter Notifications Description (Spanish)" class="form-control-textarea m-t-20" rows="5" aria-invalid="false"></textarea>
                             </div>
                            <div class="col-md-5 col-sm-5 m-t-10">	<strong><i class="fa fa-id-card-o" aria-hidden="true"></i>  Upload CSV file : </strong>
                            </div>
                            <div class="col-md-7 col-sm-7">
                                <input type="file" name="notification_csv[]" id="file-7" class="inputfile inputfile-6  text-center" data-multiple-caption="{count} files selected" multiple style="display:none" />
                                <label for="file-7">&nbsp; Upload CSV<span></span>  <strong> <i class="fa fa-cloud-upload" aria-hidden="true"></i> Browse</strong>
                                </label>
                            </div>
                        </div>
                        <!-- /.row -->
                        <hr>
                        <!-- .row -->
                        <div class="row text-center">
                            <div class="col-md-12">
                                <input type="submit" name="save" value="Submit" class="btn btn-circle btn-primary">
                            </div>
                        </div>
                        <?php echo form_close(); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>