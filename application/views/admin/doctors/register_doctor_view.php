<link href="<?php echo base_url(); ?>assets/assets/summernote/summernote.css" rel="stylesheet">

<style>

    th.sorting_disabled {padding-right: 15px; min-width: 100px;}
    table.dataTable thead>tr>th.sorting {min-width: 100px;}
    .label-danger{background-color: #337ab7 !important;}
</style>
<!-- start page content -->
<div class="page-content-wrapper">
    <div class="page-content">
        <div class="page-bar">
            <div class="page-title-breadcrumb">
                <div class=" pull-left">
                    <div class="page-title"><?php echo $this->lang->line("page_title"); ?></div>
                </div>
                <ol class="breadcrumb page-breadcrumb pull-right">
                    <li><i class="fa fa-home"></i>&nbsp;<a class="parent-item" href="<?php echo base_url()?>dashboard"><?php echo $this->lang->line("home"); ?></a>&nbsp;<i class="fa fa-angle-right"></i></li>
                    <li class="active"><?php echo $this->lang->line("page_title"); ?></li>
                </ol>
            </div>
        </div>
        <!-- add content here -->
        <div class="row">
            <div class="col-md-12">
                <div class="manage-users">
                    <div class="card card-topline-aqua">
                        <div class="card-head">
                            <header><?php echo $this->lang->line("list"); ?></header>
                            <div class="tools">
                                <a class="fa fa-repeat btn-color box-refresh" href="javascript:;"></a>
                                <a class="t-collapse btn-color fa fa-chevron-down" href="javascript:;"></a>
                                <a class="t-close btn-color fa fa-times" href="javascript:;"></a>
                            </div>
                        </div>
                        <div class="card-body">
                            <?php echo display_message_info([1 => $success, 2 => $error, 3 => validation_errors()]); ?>
                            <div class="row">
                                <div class="col-md-4">

                                </div>
                                <div class="col-md-4"></div>
                                <div class="col-md-4">
                                    <div class="btn-group pull-right">
                                        <button type="button" class="btn btn-round btn-success" data-toggle="modal" data-target="#filter"><?php echo $this->lang->line("filter_btn"); ?><i class="fa fa-filter"></i></button>
                                        <button class="btn btn-info  btn-sm" id="send_button" data-toggle="modal" data-target="#myModal" disabled = "disabled" > <?php echo $this->lang->line("send_btn"); ?>
                                            <i class="fa fa-envelope"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <input type='hidden' id='myDivv' value='' />
                            <?php echo form_open("admin/doctors/registered_doctors/send_email_to_doctors", ["class" => "users_email_validation", "id" => "frm-example", "onsubmit" => "return tsextareavldt()"]); ?>
                         
                            <div class="modal fade" id="myModal" role="dialog">
                                <div class="modal-dialog modal-lg" style="min-width:1203px">

                                    <!-- Modal content-->
                                    <div class="modal-content">
                                        <div class="modal-body">
                                            <div class="">
                                                <div class="card-head">
                                                    <header><?php echo $this->lang->line("compose_mail"); ?></header>
                                                </div>
                                                <div class="form-group text-center">
                                                    <input type="radio" name="email-btn" value="1" >&nbsp;Pre-Saved &emsp;
                                                    <input type="radio" name="email-btn" value="2" >&nbsp;New Email
                                                </div>

                                                <div class="form-group new-email">
                                                    <label for="simpleFormEmail"><?php echo $this->lang->line("subject"); ?></label>
                                                    <input type="text" name="subject" class="form-control subject" id="subject" placeholder="Enter Subject">
                                                </div>
                                                <div class="form-group new-email"  >
                                                    <label for="simpleFormMessage"><?php echo $this->lang->line("message"); ?></label>
                                                    <textarea name="message" id="message" class="form-control" placeholder="Enter Email text" ></textarea>
                                                </div>
                                                <div class="form-group pre-saved-email">
                                                    <?php
                                                    $other_template = get_email_templates(["type" => "other_emails"]);
                                                    if (!empty($other_template)) {
                                                        ?>
                                                        <select class="form-control slctd_email slct_subject" id="slct_subject"  name="subject" aria-invalid="false" >
                                                            <option value="">Please select the email template</option>

                                                            <?php foreach ($other_template as $k => $v) { ?>
                                                                <option value="<?php echo $v['id']; ?>"><?php echo $v['subject']; ?></option>
                                                            <?php }
                                                            ?>
                                                        </select>
                                                    <?php }
                                                    ?>
                                                </div>
                                                <div class="form-group pre-saved-email displayMsgContent" >
                                                    
                                                </div>
                                                <div class="form-group">
                                                    <button type="submit" name="submit" style="display:none"  class="btn btn-primary submit-btn"><?php echo $this->lang->line("submit"); ?></button>
                                                </div>
                                            </div>  
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo $this->lang->line("close"); ?></button>
                                        </div>
                                    </div>

                                </div>
                            </div>

                            <table  id="datatable1" class="table table-striped table-bordered">
                                <thead> 
                                    <tr>
                                        <th style="min-width:30px"><input type="checkbox" name="selectall" id="selectall" value='0' style='align:right'></th>
                                        <th><?php echo $this->lang->line("sr_n"); ?></th>
                                        <th>Provider Med ID #</th>
                                        <th>First Name</th>
                                        <th>Last Name</th>
                                        <th>Email Address</th>
                                        <th><?php echo $this->lang->line("phone"); ?></th>
                                        <th><?php echo $this->lang->line("gender"); ?></th>
                                        <th><?php echo $this->lang->line("dob"); ?></th>
                                        <th>Language Preference</th>
                                        <th>On-Call</th>
                                        <th><?php echo $this->lang->line("action"); ?></th>
                                    </tr>
                                </thead>
                                <tbody>
                                </tbody>
                            </table>
                            </form>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- end page content -->
</div>

<!-- end page container -->

<div class="modal fade" id="filter" role="dialog">
    <div class="modal-dialog">

        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-body">
                <div class="card card-box">
                    <div class="card-head">
                        <header><?php echo $this->lang->line("filter_by"); ?></header>
                    </div>
                    <div class="card-body " id="bar-parent">
                        <form action="<?php echo base_url(); ?>admin/doctors/registered_doctors/index" method="POST" name="filter_data" id="filter_data">
                            <div class="form-group row">
                                <label class="col-md-4 control-label"><?php echo $this->lang->line("gender"); ?></label>
                                <div class="input-group col-md-8">
                                    <div class="radio">
                                        <input id="male" name="doctors_gender" type="radio" value='male'  />
                                        <label style="line-height: normal;" for="male"><?php echo $this->lang->line("male"); ?></label>
                                    </div> 
                                    <div class="radio">
                                        <input id="female" name="doctors_gender" type="radio" value='female' >
                                        <label for="female"><?php echo $this->lang->line("female"); ?></label>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-md-4 control-label"><?php echo $this->lang->line("state"); ?></label>
                                <div class="input-group col-md-8">
                                    <?php if (count($state) > 0) {
                                        ?>
                                        <select name="address_state" class="form-control" id="state">
                                            <option value=''>Select State</option>
                                            <?php
                                            foreach ($state as $key => $value) {
                                                ?>
                                                <option value="<?php echo trim($value['state']); ?>"><?php echo $value['state']; ?></option>
                                            <?php } ?>
                                        </select>
                                    <?php } ?>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-md-4 control-label"><?php echo $this->lang->line("city"); ?></label>
                                <div class="input-group col-md-8">
                                    <?php if (count($state) > 0) {
                                        ?>
                                        <select name="address_city" class="form-control" id="city">
                                            <option value=''>Select city</option>
                                            <?php
                                            foreach ($city as $key => $value) {
                                                ?>
                                                <option value="<?php echo trim($value['city']); ?>"><?php echo $value['city']; ?></option>
                                            <?php } ?>
                                        </select>
                                    <?php } ?>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-md-4 control-label"><?php echo $this->lang->line("specility"); ?></label>
                                <div class="input-group col-md-8">
                                    <?php if (count($specility) > 0) {
                                        ?>
                                        <select name="splct_name" class="form-control" id="city">
                                            <option value=''>Select Specility</option>
                                            <?php
                                            foreach ($specility as $key => $value) {
                                                ?>
                                                <option value="<?php echo trim($value['name']); ?>"><?php echo $value['name']; ?></option>
                                            <?php } ?>
                                        </select>
                                    <?php } ?>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-md-4 control-label">On Call</label>
                                <div class="input-group col-md-8">
                                    <select name="is_loggedin" class="form-control" id="on_call">
                                        <option value=''>Select On Call</option>
                                        <option value="1">Yes</option>
                                        <option value="2">No</option>
                                    </select>
                                </div>
                            </div>
                            <button type="submit" class="btn btn-round btn-success"><?php echo $this->lang->line("success"); ?></button>
                        </form>
                    </div>
                </div>  
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo $this->lang->line("close"); ?></button>
            </div>
        </div>

    </div>
</div>


<!-- when doctor will rejected  -->
<div class="modal fade" id="modalRegister" role="dialog">
    <div class="modal-dialog">
        <?php echo form_open("admin/doctors/pending_doctors/send_rejected_mail_to_doctor", ["class" => "doctor_validation", "id" => "frm-example"]); ?>
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-body">
                <div class="card card-box">
                    <div class="card-head">
                        <header><?php echo $this->lang->line("compose_mail"); ?></header>
                    </div>
                    <div class="card-body " id="bar-parent">

                        <div class="form-group">
                            <label for="simpleFormEmail"><?php echo $this->lang->line("subject"); ?></label>
                            <input type="text" name="subject" class="form-control" id="subject" placeholder="Enter Subject">
                            <input type="hidden" name="email" value=''>
                        </div>
                        <div class="form-group">
                            <label for="simpleFormPassword"><?php echo $this->lang->line("message"); ?></label>
                            <textarea name="message" id="message" class="form-control" placeholder="Enter Email text" ></textarea>
                        </div>
                    </div>
                    <button type="submit" name="submit"  class="btn btn-primary submit-btn"><?php echo $this->lang->line("submit"); ?></button>

                </div>  
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo $this->lang->line("close"); ?></button>
            </div>
        </div>
        <?php echo form_close(); ?> 
    </div>
</div>




<?php
//dd($filtering_data);
if (count($filtering_data) > 0) {
    $data = json_encode($filtering_data);
}
?>
<script>
    var fd = '';
    fd = '<?php echo (isset($data)) ? $data : ""; ?>';
    console.log(fd);

    $("document").ready(function () {
        $(".new-email").hide();
        $(".pre-saved-email").hide();
        $("input[name='email-btn']").click(function () {
            var val = $(this).val();
            $(".submit-btn").show();
            if (val == 1) {
                $(".new-email").hide();
                $(".pre-saved-email").show();

                $(".subject").removeAttr('name');
                $(".slct_subject").attr('name', "subject");
            } else {
                $(".new-email").show();
                $(".pre-saved-email").hide();
                $(".slct_subject").removeAttr('name');
                $(".subject").attr('name', "subject");
            }
        });

        $(".slctd_email").change(function () {
            var id = $(this).val();
            get_email_message(id);
        });

    });

    function get_email_message(id) {
        //alert(id);
        $.ajax({
            url: site_url + "admin/doctors/pending_doctors/get_email_data",
            cache: false,
            type: "POST",
            processData: true,
            data: {id: id},
            success: function (data) {
                var response = JSON.parse(data);
                console.log(response.message);
                if (id != '') {
                    $(".msg").show();
                    $(".displayMsgContent").html(response.message);
                    $(".message").text(response.message);
                } else {
                    $(".msg").hide();
                }
            }
        });
    }




</script>
<script src="<?php echo base_url() ?>assets/assets/summernote/summernote.js" ></script>
<script >
    $(document).ready(function () {
        $("#message").summernote();
    });
</script>