<link href="<?php echo base_url(); ?>assets/assets/select2/css/select2.css" rel="stylesheet" type="text/css" />
<link href="<?php echo base_url(); ?>assets/assets/select2/css/select2-bootstrap.min.css" rel="stylesheet" type="text/css" />


<style>
    .jr-ratenode{

    }

    .jr-rating{
        float:left;
        margin: 6px 0;
        position: relative;
        display: block;
        color: red;
        width: 0px;
        height: 0px;
        border-right:  10px solid transparent;
        border-bottom: 7px  solid #CC6600;
        border-left:   10px solid transparent;
        -moz-transform:    rotate(35deg);
        -webkit-transform: rotate(35deg);
        -ms-transform:     rotate(35deg);
        -o-transform:      rotate(35deg);
    }
    .jr-rating:before{
        border-bottom: 8px solid #CC6600;
        border-left: 3px solid transparent;
        border-right: 3px solid transparent;
        position: absolute;
        height: 0px;
        width: 0px;
        top: -4.5px;
        left: -6.5px;
        display: block;
        content: '';
        -webkit-transform: rotate(-35deg);
        -moz-transform:    rotate(-35deg);
        -ms-transform:     rotate(-35deg);
        -o-transform:      rotate(-35deg);
    }
    .jr-rating:after{
        position: absolute;
        display: block;
        color: red;
        top: 0.3px;
        left: -10.5px;
        width: 0px;
        height: 0px;
        border-right: 10px solid transparent;
        border-bottom: 7px solid #CC6600;
        border-left: 10px solid transparent;
        -webkit-transform: rotate(-70deg);
        -moz-transform:    rotate(-70deg);
        -ms-transform:     rotate(-70deg);
        -o-transform:      rotate(-70deg);
        content: '';
    }

    .jr-nomal {
        float:left;
        margin: 6px 0;
        position: relative;
        display: block;
        color: red;
        width: 0px;
        height: 0px;
        border-right:  10px solid transparent;
        border-bottom: 7px  solid grey;
        border-left:   10px solid transparent;
        -moz-transform:    rotate(35deg);
        -webkit-transform: rotate(35deg);
        -ms-transform:     rotate(35deg);
        -o-transform:      rotate(35deg);
    }
    .jr-nomal:before {
        border-bottom: 8px solid grey;
        border-left: 3px solid transparent;
        border-right: 3px solid transparent;
        position: absolute;
        height: 0px;
        width: 0px;
        top: -4.5px;
        left: -6.5px;
        display: block;
        content: '';
        -webkit-transform: rotate(-35deg);
        -moz-transform:    rotate(-35deg);
        -ms-transform:     rotate(-35deg);
        -o-transform:      rotate(-35deg);

    }
    .jr-nomal:after {
        position: absolute;
        display: block;
        color: red;
        top: 0.3px;
        left: -10.5px;
        width: 0px;
        height: 0px;
        border-right: 10px solid transparent;
        border-bottom: 7px solid grey;
        border-left: 10px solid transparent;
        -webkit-transform: rotate(-70deg);
        -moz-transform:    rotate(-70deg);
        -ms-transform:     rotate(-70deg);
        -o-transform:      rotate(-70deg);
        content: '';
    }



</style>
<script src = "<?php echo base_url(); ?>assets/admin/js/doctors/jquery-rating.js" ></script>

<!-- start page content -->
<div class="page-content-wrapper">
    <div class="page-content">
        <div class="page-bar">
            <div class="page-title-breadcrumb">
                <div class="pull-left">
                    <div class="page-title"><?php echo ucfirst($doctor_info['name']); ?></div>
                </div>

                <ol class="breadcrumb page-breadcrumb pull-right">
                    <li><i class="fa fa-home"></i>&nbsp;<a class="parent-item" href="<?php echo base_url(); ?>dashboard">Home</a>&nbsp;<i class="fa fa-angle-right"></i></li>
                    <li>&nbsp;<a class="parent-item" href="<?php echo base_url(); ?>admin/doctors/pending_doctors/">Manage Pending Provider</a>&nbsp;<i class="fa fa-angle-right"></i></li>
                    <li class="active"><?php echo ucfirst($doctor_info['name']); ?></li>
                </ol>
            </div>
        </div>
        <!-- add content here -->
        <div class="row">
            <div class="col-md-8 col-xs-12 user-details">
                <div class="white-box">
                    <?php echo display_message_info([1 => $success, 2 => @$error, 3 => validation_errors()]); ?>
                    <div class="patient-profile">
                        <?php if ($doctor_info['profile_url']) { ?>
                            <img src="<?php echo $doctor_info['profile_url']; ?>" class="img-responsive" alt="">
                        <?php } else { ?>
                            <img src="<?php echo base_url(); ?>/assets/admin/avatar/avatar.png" class="img-responsive" alt="">
                        <?php } ?>
                    </div>
                    <div class="btn-group pull-right">
                        <button class="btn btn-info  btn-sm" id="send_button" data-toggle="modal" data-target="#sendemailModal"> Send Mail<i class="fa fa-envelope"></i>
                        </button>
                    </div>
                    <?php echo form_open("admin/doctors/pending_doctors/doctor_view/" . $doctor_info['id'], ["id" => "doctor_form"]); ?>

                    <div class="cardbox">
                        <div class="header text-center">
                            <h4 class="font-bold">About Provider</h4>
                            <a href="#" class="btn btn-success btn-xs pull-right a" id="edit-form">	
                                Edit
                            </a> 
                        </div>
                        <div class="body">
                            <div class="user-btm-box">
                                <!-- .row -->
                                <div class="row text-center m-t-10">
                                    <div class="col-lg-4 col-md-12 col-sm-6 col-xs-12 b-r">
                                        <strong>Provider Med Id</strong>
                                        <p><?php echo (ucfirst($doctor_info['med_id'])) ? ucfirst($doctor_info['med_id']) : "N/A"; ?></p>
                                    </div>
                                    <div class="col-lg-4 col-md-12 col-sm-6 col-xs-12 b-r">
                                        <strong>Name</strong>
                                        <p><?php echo (ucfirst($doctor_info['name'])) ? ucfirst($doctor_info['name']) : "N/A"; ?></p>
                                    </div>
                                    <div class="col-lg-4 col-md-12 col-sm-6 col-xs-12">
                                        <strong>Gender</strong>
                                        <p><?php echo ($doctor_info['gender']) ? $doctor_info['gender'] : "N/A"; ?></p>
                                    </div>
                                    <div class="col-lg-4 col-md-12 col-sm-6 col-xs-12 b-r">
                                        <strong>Phone</strong>
                                        <p><?php echo ($doctor_info['phone']) ? $doctor_info['phone'] : "N/A"; ?></p>
                                    </div>
                                    <div class="col-lg-4 col-md-12 col-sm-6 col-xs-12 b-r">
                                        <strong>Date of Birth</strong>
                                        <p><?php echo ($doctor_info['date_of_birth']) ? date("m-d-Y", strtotime($doctor_info['date_of_birth'])) : "N/A"; ?></p>
                                    </div>
                                    <div class="col-lg-4 col-md-12 col-sm-6 col-xs-12 b-r">
                                        <strong>Email</strong>
                                        <p><?php echo $doctor_info['email']; ?></p>
                                    </div>
                                    <div class="col-lg-4 col-md-12 col-sm-6 col-xs-12">
                                        <strong>Address</strong>
                                        <p><?php echo ($doctor_info['address']) ? $doctor_info['address'] : "N/A"; ?></p>
                                    </div>
                                    <div class="col-lg-4 col-md-12 col-sm-6 col-xs-12 b-r">
                                        <strong>City</strong>
                                        <p><?php echo ($doctor_info['city']) ? $doctor_info['address'] : "N/A"; ?></p>
                                    </div>
                                    <div class="col-lg-4 col-md-12 col-sm-6 col-xs-12 b-r">
                                        <strong>State</strong>
                                        <p><?php echo ($doctor_info['state']) ? $doctor_info['state'] : "N/A"; ?></p>
                                    </div>
                                    <div class="col-lg-4 col-md-12 col-sm-6 col-xs-12 b-r">
                                        <strong>Zip Code</strong>
                                        <p><?php echo ($doctor_info['zip_code']) ? $doctor_info['zip_code'] : "N/A"; ?></p>
                                    </div>
                                    <div class="col-lg-4 col-md-12 col-sm-6 col-xs-12 b-r">
                                        <strong>Undergraduate University</strong>
                                        <p><?php echo ($doctor_info['undergraduate_university']) ? $doctor_info['undergraduate_university'] : "N/A"; ?></p>
                                    </div>
                                    <div class="col-lg-4 col-md-12 col-sm-6 col-xs-12 b-r">
                                        <strong>Medical School</strong>
                                        <p><?php echo ($doctor_info['medical_school']) ? $doctor_info['medical_school'] : "N/A"; ?></p>
                                    </div>
                                    <div class="col-lg-4 col-md-12 col-sm-6 col-xs-12 b-r">
                                        <strong>Residency</strong>
                                        <p><?php echo ($doctor_info['residency']) ? $doctor_info['residency'] : "N/A"; ?></p>
                                    </div>
                                    <div class="col-lg-4 col-md-12 col-sm-6 col-xs-12 b-r">
                                        <strong>Medical License Number</strong>
                                        <p><?php echo ($doctor_info['medical_license_number']) ? $doctor_info['medical_license_number'] : "N/A"; ?></p>
                                    </div>
                                    <div class="col-lg-4 col-md-12 col-sm-6 col-xs-12 b-r">
                                        <strong>language</strong>
                                        <p><?php echo ($doctor_info['language']) ? $doctor_info['language'] : "N/A"; ?></p>
                                    </div>                                 
                                    <div class="col-lg-4 col-md-12 col-sm-6 col-xs-12 b-r">
                                        <br><strong>Specialty</strong>

                                        <?php
                                        if (count($speciality) > 0) {
                                            ?>
                                            <select class="form-control valid dsbld select2-multiple "  onchange="update_doctor_info(this.value, this.name)"  name="speciality_id[]" aria-invalid="false" multiple="multiple">
                                                <?php
                                                foreach ($speciality as $key => $value) {
                                                    $selected_speciality = (in_array($value['id'], explode(",", $doctor_info['spacility_id']))) ? "selected" : '';
                                                    ?>
                                                    <option value="<?php echo $value['id']; ?>" <?php echo $selected_speciality; ?>><?php echo $value['name']; ?></option>

                                                <?php } ?>
                                            </select>
                                        <?php } ?>

                                    </div>
                                    <div class="col-lg-4 col-md-12 col-sm-6 col-xs-12 b-r" >
                                        <br><strong> Professional Degree</strong> - 

                                        <?php
                                        if (count($degree) > 0) {
                                            ?>
                                            <select class="form-control valid dsbld select2-multiple"  onchange="update_doctor_info(this.value, this.name)"  name="degree_id[]" aria-invalid="false" multiple="multiple">
                                                <?php
                                                foreach ($degree as $key => $value) {
                                                    $degree = (in_array($value['id'], explode(",", $doctor_info['degree_id']))) ? "selected" : '';
                                                    ?>
                                                    <option value="<?php echo $value['id']; ?>" <?php echo $degree; ?>><?php echo $value['degree']; ?></option>

                                                <?php } ?>
                                            </select>
                                        <?php } ?> 

                                    </div>
                                    <div class="col-lg-4 col-md-12 col-sm-6 col-xs-12 b-r">

                                    </div>
                                    <div class="col-lg-12 col-md-12 col-sm-6 col-xs-12 b-r edit" style="display:none">
                                        <p><center><input type="submit" name="save" class="btn btn-danger btn-success submit" value="Submit"> </center></p>   
                                    </div>
                                    <?php echo form_close(); ?>
                                </div>
                                <!-- /.row -->
                                <hr>
                                <!-- .row -->
                                <div class="row text-center m-t-10">
                                    <div class="col-md-12">
                                        <a href='#'  class='approve btn btn-circle btn-success' data-toggle='modal' data-target='#myModal1' data-id='<?php echo $doctor_info['id']; ?>' >Approve</a>
                                        <a href="#" class="btn btn-circle  btn-danger" id='reject_email' data-toggle='modal' data-email="<?php echo $doctor_info['email']; ?>" data-target='#sendemailModal'>Reject</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

</div>


<!-- end page container -->
<!-- when doctor will rejected  -->
<div class="modal fade" id="sendemailModal" role="dialog">
    <div class="modal-dialog modal-lg">
        <?php echo form_open("admin/doctors/pending_doctors/send_rejected_mail_to_doctor", ["class" => "doctor_validation", "id" => "frm-example"]); ?>
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-body">
                <div class="casrd zcard-box">
                    <div class="card-head">
                        <header><?php echo $this->lang->line("compose_mail"); ?></header>
                    </div>


                    <div class="card-body " id="bar-parent">
                        <div class="form-group text-center">
                            <input type="radio" name="email-btn" value="1" >&nbsp;Pre-Saved &emsp;
                            <input type="radio" name="email-btn" value="2" >&nbsp;New Email
                        </div>

                        <div class="form-group new-email">
                            <label for="simpleFormEmail"><?php echo $this->lang->line("subject"); ?></label>
                            <input type="text" name="subject" class="form-control" id="subject" placeholder="Enter Subject">
                            <input type="hidden" name="email" value=''>
                        </div>
                        <div class="form-group new-email"  >
                            <label for="simpleFormMessage"><?php echo $this->lang->line("message"); ?></label>
                            <textarea name="message" id="message" class="form-control desc" placeholder="Enter message text" ></textarea>
                        </div>
                        <div class="form-group pre-saved-email ">
                            <?php if (!empty($email_template)) { ?>
                                <select class="form-control slctd_email"  name="subject" aria-invalid="false" >
                                    <option value="">Please select the email template</option>

                                    <?php foreach ($email_template as $key => $value) { ?>
                                        <option value="<?php echo $value['id']; ?>"><?php echo $value['subject']; ?></option>
                                    <?php }
                                    ?>
                                </select>
                                <?php
                            }
                            echo form_hidden("email", $doctor_info['email']);
                            ?>
                        </div>
                        <div class="form-group pre-saved-email displayMsgContent" >

                        </div>
                        <div class="form-group">
                            <button type="submit" name="submit" style="display:none"  class="btn btn-primary submit-btn"><?php echo $this->lang->line("submit"); ?></button>
                        </div>
                    </div>

                </div>  
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo $this->lang->line("close"); ?></button>
            </div>
        </div>
        <?php echo form_close(); ?> 
    </div>
</div>

<!-- Modal -->
<div class="modal fade" id="myModal1" role="dialog">
    <div class="modal-dialog">

        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <div class="content">
                    <div class="col-md-12">
                        <h4 class="text-center " style="border-bottom: 1px solid #eee;padding: 5px 0px 10px 0px;font-weight:600;color: #ff5870;">Set Provider's Intial Rating and Commission</h4>
                        <?php echo form_open("admin/doctors/pending_doctors/update_doctors_status", ["id" => "add_rating"]); ?>
                        <ul class="docListWindow">

                            <li>
                                <div class="form-group row">
                                    <label class="control-label col-md-6" style="font-size: 20px;">Select Score</label>
                                    <div class="col-md-4"> 
                                        <div class="group1">
                                            <div   class="jr-ratenode jr-nomal"></div>
                                            <div   class="jr-ratenode jr-nomal "></div>
                                            <div   class="jr-ratenode jr-nomal "></div>
                                            <div   class="jr-ratenode jr-nomal "></div>
                                            <div   class="jr-ratenode jr-nomal "></div>
                                        </div>
                                        <input type="hidden" value="" name="rating">
                                        <input type="hidden" value="" name="doctor_id">
                                        <p id="info" ></p>
                                    </div>
                                </div>
                            </li>
                            <li>
                                <div class="form-group row">
                                    <label class="control-label col-md-6" style="font-size: 20px;">Enter Commission Percentage</label>
                                    <div class="col-md-2">
                                        <input type="text" name="commission" data-required="1" class="form-control">
                                    </div>
                                    <div class="col-md-4" style="font-size: 20px;">%</div>
                                </div>
                            </li>
                        </ul>
                        <div class="col-lg-12 p-t-20 text-center">
                            <input type="submit" class="mdl-button mdl-js-button mdl-button--raised mdl-js-ripple-effect m-b-10 m-r-20 btn-pink" value="Submit" name="save">
                        </div>
                        <?php echo form_close(); ?>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
        </div>

    </div>
</div>


<script>
    $("select").attr("disabled", "disabled");
    $('body').on('click', "#reject_email", function () {
        var email = $(this).attr("data-email");
        $("input[name='email']").val(email);
    });

    $('body').on('click', '.a', function(e) {
        e.preventDefault();
        $(".dsbld").removeAttr("disabled");
        $(".edit").show();
        $(".submit").show();
    });


    $("document").ready(function () {
        $(".new-email").hide();
        $(".pre-saved-email").hide();
        $("input[name='email-btn']").click(function () {
            var val = $(this).val();
            $(".submit-btn").show();
            if (val == 1) {
                $(".new-email").hide();
                $(".pre-saved-email").show();
            } else {
                $(".new-email").show();
                $(".pre-saved-email").hide();
            }
        });

        $(".slctd_email").change(function () {
            var id = $(this).val();
            get_email_message(id);
        });

        $('.group1').start(function (cur) {
            console.log(cur);
            $('#info').text(cur);
        });
        $(".jr-nomal").on('click', function () {
            $("input[name='rating']").val($('.group1').getCurrentRating());
            //alert($('.group1').getCurrentRating());// + "--" + $('.group2').getCurrentRating());
        });


    });
    $("body").on("click", "a.approve", function () {
        event.preventDefault();
        var doctor_id = $(this).attr("data-id");
        //alert(doctor_id);
        $("input[name='doctor_id']").val(doctor_id);

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
    $("document").ready(function () {
        $("#add_rating").validate({
            // errorPlacement: function(error, element) {
            //  error.appendTo(element.closest('.form-group').after());
            // },
            rules: {
                commission: {
                    required: true,
                    minlength: 1
                },
                rating: {
                    required: true,
                    minlength: 1
                },
                 messages: {
                            rating: {
                                //required: "Please enter rating from 0 to 5 only"
                                minlength: "Please enter rating from 0 to 5 only"
                            }
                        },
                submitHandler: function (form) {
                    form.submit();
                }
            }
        });

    });
</script>
<script src="<?php echo base_url(); ?>assets/assets/select2/js/select2.js" ></script>
<script src="<?php echo base_url(); ?>assets/assets/select2/js/select2-init.js" ></script>
