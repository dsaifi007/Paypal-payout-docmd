<style>
    .cat-head{
        background: #ff5870;
        height: 35px;
        padding-top: 6px;
        color: #fff;
        font-weight: 600;
    }	</style>

<div class="page-content-wrapper">
    <div class="page-content">
        <div class="page-bar">
            <div class="page-title-breadcrumb">
                <div class="pull-left">
                    <div class="page-title">Manage In-App Content</div>
                </div>
                <ol class="breadcrumb page-breadcrumb pull-right">
                    <li><i class="fa fa-home"></i>&nbsp;<a class="parent-item" href="index.html">Home</a>&nbsp;<i class="fa fa-angle-right"></i>
                    </li>
                    <li class="active">Manage In-App Content</li>
                </ol>
            </div>
        </div>
        <!-- add content here -->
        <div class="row">
            <div class="col-md-5 user-details">
                <div class="white-box">
                    <div class="cat-head p-b-20">
                        <header class="text-center p-b-20">Add New Category</header>

                    </div>
                    <div class="card-body p-t-20 " id="bar-parent6">
                        <?php echo display_message_info([1 => @$success, 2 => @$error, 3 => validation_errors()]); ?>

                        <!-- .row -->
                        <div class="row text-center m-t-20">

                            <?php
                            $id = (isset($items['id'])) ? $items['id'] : '';
                            echo form_open("admin/manage_app_content/manage_appcontent_controller/faq_submited/1/$id", ["class" => "faq_cat col-md-12"]);
                            ?>
                            <div class="input-group m-b-20">
                                <span class="input-group-addon"><i class="fa fa-pie-chart" aria-hidden="true"></i></span>
                                <select class="form-control" name="type">
                                    <option value="doctor">Provider</option>
                                    <option value="user">User</option>                               
                                </select>

                            </div>
                            <div class="input-group m-b-20">	
                                <span class="input-group-addon"><i class="fa fa-pie-chart" aria-hidden="true"></i></span>
                                <input type="text" name='category'  class="form-control" placeholder="Enter Category (English)" value="<?php echo @$items['category']; ?>">
                            </div>
                            <div class="input-group m-b-20">	
                                <span class="input-group-addon"><i class="fa fa-pie-chart" aria-hidden="true"></i></span>
                                <input type="text" name='sp_category' class="form-control" placeholder="Enter Category (Spanish)" value="<?php echo @$items['sp_category']; ?>">
                            </div>

                        </div>
                        <!-- /.row -->
                        <hr>
                        <!-- .row -->
                        <div class="row text-center">
                            <div class="col-md-12">
                                <input type="submit" class="btn btn-circle btn-primary" name="save" value="Submit">
                            </div>
                        </div>
<?php echo form_close(); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</div>

<script>
    $("document").ready(function () {
        $(".faq_cat").validate({
            // errorPlacement: function(error, element) {
            //  error.appendTo(element.closest('.form-group').after());
            // },
            rules: {
                category: {
                    required: true,
                    minlength: 2
                },
                sp_category: {
                    required: true,
                    minlength: 2
                },
                submitHandler: function (form) {
                    form.submit();
                }
            }
        });
    });
</script>