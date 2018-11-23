
<style>
    .active{
        display:none;
    }
</style>
<link href="<?php echo base_url(); ?>assets/assets/select2/css/select2.css" rel="stylesheet" type="text/css" />
<link href="<?php echo base_url(); ?>assets/assets/select2/css/select2-bootstrap.min.css" rel="stylesheet" type="text/css" />

<!-- start page content -->
<div class="page-content-wrapper">
    <div class="page-content" >
        <div class="page-bar">
            <div class="page-title-breadcrumb">
                <div class="pull-left">
                    <div class="page-title"><?php echo ucfirst($doctor_info['name']); ?></div>
                </div>
                <ol class="breadcrumb page-breadcrumb pull-right">
                    <li><i class="fa fa-home"></i>&nbsp;<a class="parent-item" href="<?php echo base_url(); ?>admin">Home</a>&nbsp;<i class="fa fa-angle-right"></i></li>
                    <li>&nbsp;<a class="parent-item" href="<?php echo base_url(); ?>admin/doctors/registered_doctors">Manage Registered Provider</a>&nbsp;<i class="fa fa-angle-right"></i></li>
                    <li class="active"><?php echo ucfirst($doctor_info['name']); ?></li>
                </ol>
            </div>
        </div>
        <!-- add content here -->
        <div class="row">
            <div class="col-md-12 col-xs-12 user-details">
                <div class="white-box">
                    <?php
                    if (isset($success)) {
                        echo $success;
                    }
                    ?>
                    <?php
                    echo display_message_info([1 => @$successs, 2 => @$error, 3 => validation_errors()]);
                    ?>
                    <div class="patient-profile">
                        <?php if ($doctor_info['profile_url']) { ?>
                            <img src="<?php echo $doctor_info['profile_url']; ?>" class="img-responsive" alt="">
                        <?php } else { ?>
                            <img src="<?php echo base_url(); ?>/assets/admin/avatar/avatar.png" class="img-responsive" alt="">
                        <?php } ?>
                    </div>
                    <?php echo form_open("admin/doctors/registered_doctors/doctor_view/" . $doctor_info['id'], ["id" => "doctor_form"]); ?>
                    <div class="cardbox">
                        <div class="header text-center">
                            <h4 class="font-bold">ABOUT PROVIDER</h4>
                            <a href="#" class="btn btn-success btn-xs pull-right" id="edit-form">	
                                Edit
                            </a>
                            <i class="fa fa-star" aria-hidden="true"></i>  
                            <input type="text" name="rating" style="width:30px"  value="<?php echo $doctor_info['avg_rating']; ?>">
                        </div>
                        <div class="body">
                            <div class="user-btm-box">
                                <!-- .row -->
                                <div class="row m-t-10">

                                    <div class="col-md-4">
                                        <strong>Name</strong> -
                                        <?php echo ucfirst($doctor_info['name']); ?>
                                    </div>
                                    <div class="col-md-4">
                                        <strong>Provider Med I.D. #</strong> - <?php echo ucfirst($doctor_info['med_id']); ?>
                                    </div>
                                    <div class="col-md-4 ">
                                        <strong>Commission</strong> - 
                                        <input type="text" name="commission"  style="width:80px"  value="<?php echo $doctor_info['commission']; ?>">%                                      
                                    </div>
                                    <div class="col-md-4">
                                        <br><strong>Medical License #</strong> - 
                                        <?php echo $doctor_info['medical_license_number']; ?>
                                    </div>
                                    <div class="col-md-4">
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
                                    <div class="col-lg-4 col-md-12 col-sm-6 col-xs-12 b-r">
                                        <br><strong>Email</strong> - 
                                        <?php echo "<a href='mailto:".$doctor_info['email']."'>".$doctor_info['email']."</a>"; ?>
                                    </div>
                                    <div class="col-lg-4 col-md-12 col-sm-6 col-xs-12 b-r">
                                        <br><strong>Phone</strong> - 
                                        <?php echo $doctor_info['phone']; ?>
                                    </div>
                                    <div class="col-lg-4 col-md-12 col-sm-6 col-xs-12">
                                        <br><strong>Address</strong> - 
                                        <?php echo $doctor_info['address']; ?>
                                    </div>
                                    <div class="col-lg-4 col-md-12 col-sm-6 col-xs-12 b-r">
                                        <br><strong>City</strong>-
                                        <?php echo $doctor_info['city']; ?>
                                    </div>
                                    <div class="col-lg-4 col-md-12 col-sm-6 col-xs-12 b-r">
                                        <br><strong>State</strong>-
                                        <?php echo $doctor_info['state']; ?>
                                    </div>
                                    <div class="col-lg-4 col-md-12 col-sm-6 col-xs-12 b-r">
                                        <br><strong>Zip Code</strong>
                                        <?php echo $doctor_info['zip_code']; ?>
                                    </div>
                                    <div class="col-lg-4 col-md-12 col-sm-6 col-xs-12 b-r">
                                        <br><strong>Date of Birth</strong>
                                        <?php echo date("m-d-Y",strtotime($doctor_info['date_of_birth'])); ?>
                                    </div>
                                    <div class="col-lg-4 col-md-12 col-sm-6 col-xs-12">
                                        <br><strong>Gender</strong>
                                        <?php echo $doctor_info['gender']; ?>
                                    </div>
                                    <div class="col-lg-4 col-md-12 col-sm-6 col-xs-12 b-r">
                                        <br><strong>Language Preferences</strong>
                                        <?php echo $doctor_info['language']; ?>
                                    </div>
                                    <div class="col-lg-4 col-md-12 col-sm-6 col-xs-12 b-r">
                                        <br><strong>Undergraduate University</strong>
                                        <?php echo $doctor_info['undergraduate_university']; ?>
                                    </div>
                                    <div class="col-lg-4 col-md-12 col-sm-6 col-xs-12 b-r">
                                        <br><strong>Medical School</strong>

                                    </div>
                                    <div class="col-lg-4 col-md-12 col-sm-6 col-xs-12 b-r">
                                        <br><strong>Residency</strong>
                                        <?php echo $doctor_info['residency']; ?>
                                    </div>
                                    <div class="col-lg-4 col-md-12 col-sm-6 col-xs-12">
                                        <br><strong>Mal-Practice Insurance information</strong> - 
                                        <?php echo ($doctor_info['mal_practice_information']) ? $doctor_info['mal_practice_information'] : "N/A"; ?>
                                    </div>
                                    <div class="col-lg-4 col-md-12 col-sm-6 col-xs-12 b-r">
                                        <br><strong>Total Money Earned</strong>
                                        <?php echo ($appointment["earning"])?$appointment["earning"]:"N/A"; ?>                              
                                    </div>
                                    <div class="col-lg-4 col-md-12 col-sm-6 col-xs-12 b-r">
                                        <br><strong>Commission</strong>
                                        <?php echo $doctor_info['commission']; ?>                              
                                    </div>
                                    <div class="col-lg-4 col-md-12 col-sm-6 col-xs-12 b-r">
                                        <br><strong>On-Call Status</strong> -
                                        <?php echo ($doctor_info['is_loggedin']) ? "Yes" : "No"; ?>
                                    </div>
                                    <div class="col-lg-4 col-md-12 col-sm-6 col-xs-12 b-r">
                                        <br><strong>Total Appointments</strong> - 
                                        <?php echo ($appointment["total_appointment"])?$appointment["total_appointment"]:"N/A"; ?>

                                    </div>
                                    <div class="col-lg-4 col-md-12 col-sm-6 col-xs-12 b-r">
                                        <br><strong>Total Upcoming Appointments</strong> -
                                        <?php echo ($appointment["upcoming_appointment"])?$appointment["upcoming_appointment"]:"N/A"; ?>

                                    </div>
                                    <div class="col-lg-4 col-md-12 col-sm-6 col-xs-12 b-r">
                                        <br><strong>Total Cancel Appointments</strong> - 
                                        <?php echo ($appointment["cancel_appointment"])?$appointment["cancel_appointment"]:"N/A"; ?>

                                    </div>
                                    <div class="col-lg-4 col-md-12 col-sm-6 col-xs-12 b-r">
                                        <br><strong>Total Past Appointments</strong> - 
                                        <?php echo ($appointment["past_appointment"])?$appointment["past_appointment"]:"N/A"; ?>                                   
                                    </div>
                                    <div class="col-lg-4 col-md-12 col-sm-6 col-xs-12 b-r">
                                        <br><strong>Total On-Call Appointments</strong>
                                        <?php echo (abs($doctor_info['total_decline_appointments'] + $doctor_info['accepted_on_call_appointment']))?abs($doctor_info['total_decline_appointments'] + $doctor_info['accepted_on_call_appointment']):"N/A"; ?>
                                    </div>
                                    <div class="col-lg-4 col-md-12 col-sm-6 col-xs-12 b-r">
                                        <br><strong>Total Rejected On-Call Appointments</strong> - 
                                        <?php echo ($doctor_info['total_decline_appointments']) ? $doctor_info['total_decline_appointments'] : "N/A"; ?>
                                    </div>
                                    <div class="col-lg-4 col-md-12 col-sm-6 col-xs-12 b-r">
                                        <br><strong>Total Accepted On-Call Appointments</strong>
                                        <?php echo ($doctor_info['accepted_on_call_appointment']) ? $doctor_info['accepted_on_call_appointment'] : "N/A"; ?>
                                    </div>
                                    <div class="col-lg-4 col-md-12 col-sm-6 col-xs-12 b-r">
                                        <strong></strong>
                                        <p></p>
                                    </div>

                                    <div class="col-lg-4 col-md-12 col-sm-6 col-xs-12 b-r">
                                        <p></p>
                                        <p><center><input type="submit" style="display:none;margin-left:472px" name="save" class="btn btn-danger btn-success submit" value="Submit"> </center></p>
                                    </div>
                                </div>
                                <!-- /.row -->
                                <hr>
                                <!-- .row -->
                                <div class="row text-center m-t-10">
                                    <div class="col-md-12">
                                        <?php $status1 = ($doctor_info['is_blocked'] == 1) ? "style='display:none'" : ""; ?>
                                        <?php $status2 = ($doctor_info['is_blocked'] == 0) ? "style='display:none'" : ''; ?>

                                        <a href="#" <?php echo $status1; ?> class="btn btn-circle btn-danger blk" id='blk' 
                                           doctor-id = "<?php echo $doctor_info['id']; ?>">Block</a>


                                        <a href="#" <?php echo $status2; ?> class="btn btn-circle btn-success blk"  doctor-id = "<?php echo $doctor_info['id']; ?>" id='unblock' >Unblock</a>
                                        <a href="<?php echo base_url() . "admin/doctors/appointment_detail_controller/view_transcation/" . $doctor_info['id']; ?>" class="btn btn-circle btn-success">View Payments</a>
                                        <a href="<?php echo base_url() . "admin/doctors/appointment_detail_controller/upcoming_appointments/" . $doctor_info['id']; ?>" class="btn btn-circle btn-success">View Appoinments</a>

                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php echo form_close(); ?>
                </div>
            </div>
        </div>
    </div>
</div>

</div>
<!-- end page container -->

<script>
    $("input[type='text']").attr("disabled", "disabled");
    $("select").attr("disabled", "disabled");

    $("document").ready(function () {
        $("#edit-form").click(function (e) {
            e.preventDefault();
            $("input[type='text']").removeAttr("disabled");
            $("select").removeAttr("disabled");
            $(".submit").show();
        });
    });

  $('#doctor_form').validate({ // initialize the plugin
        rules: {
            rating: {
                required: true,
                 minlength: 1,
                 range: [0, 5]
            },
              commission: {
                    required: true,
                    minlength: 1,
                    maxlength: 16,
                    range: [0, 100]
                },
                speciality_id: {
                    required: true,
                    minlength: 1,
                },
                degree_id: {
                    required: true,
                    minlength: 1,
                }
        },
        messages: {
            rating: {
                required: "Please enter rating from 0 to 5 only",
                minlength:"Please enter rating from 0 to 5 only",
                range:"Please enter rating from 0 to 5 only"
            }
        },
        submitHandler: function (form) { // for demo
            alert('valid form submitted'); // for demo
            return false; // for demo
        }
    });


    $('body').on('click', "#reject_email", function () {
        var email = $(this).attr("data-email");
        $("input[name='email']").val(email);
    });
    $("body").on("click", ".blk", function (event) {
        event.preventDefault();
        var id = $(this).attr("id");
        var doctor_id = $(this).attr("doctor-id");
        if (id == "blk") {
            $("#blk").css("display", "none");
            $("#unblock").removeAttr("style");
            status = 1;
        } else {
            $("#blk").removeAttr("style");
            $("#unblock").css("display", "none");
            status = 0;
        }
        //alert(doctor_id);
        update_doctor_status(doctor_id, status);
    });
    function update_doctor_status(doctor_id, status) {
        $.ajax({
            url: site_url + "admin/doctors/registered_doctors/update_doctor_status",
            cache: false,
            type: "POST",
            processData: true,
            data: {doctor_id: doctor_id, status: status},
            success: function (data) {
                var response = JSON.parse(data);
                if (response.active) {
                    //alert(response.active);
                } else {
                    //alert(response.unactive);
                }
            }
        });
    }
</script>
<script src="<?php echo base_url(); ?>assets/assets/select2/js/select2.js" ></script>
<script src="<?php echo base_url(); ?>assets/assets/select2/js/select2-init.js" ></script>
