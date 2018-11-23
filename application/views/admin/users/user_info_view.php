<link href="<?php echo base_url(); ?>assets/assets/summernote/summernote.css" rel="stylesheet">

<!-- start page content -->
<div class="page-content-wrapper">
    <div class="page-content">
        <div class="page-bar">
            <div class="page-title-breadcrumb">
                <div class="pull-left">
                    <div class="page-title"><?php echo ucfirst($user_info['name']); ?></div>
                </div>
                <ol class="breadcrumb page-breadcrumb pull-right">
                    <li><i class="fa fa-home"></i>&nbsp;<a class="parent-item" href="<?php echo base_url(); ?>admin">Home</a>&nbsp;<i class="fa fa-angle-right"></i></li>
                    <a class="parent-item" href="<?php echo base_url(); ?>admin/users/manage_users">Manage Users</a>&nbsp;<i class="fa fa-angle-right"></i>
                    <li class="active"><?php echo ucfirst($user_info['name']); ?></li>
                </ol>
            </div>
        </div>
        <!-- add content here -->
        <div class="row">
            <div class="col-md-12 col-xs-12 user-details">
                <div class="white-box">
                    <div class="patient-profile">
                        <?php if ($user_info['profile_url']) { ?>
                            <img src="<?php echo $user_info['profile_url']; ?>" class="img-responsive" alt="">
                        <?php } else { ?>
                            <img src="<?php echo base_url(); ?>/assets/admin/avatar/avatar.png" class="img-responsive" alt="">
                        <?php } ?>
                          &emsp;&emsp;&emsp;&emsp;&emsp; &emsp;<i class="fa fa-star"></i> <?php echo ($user_info['avg_rating'])?ucfirst($user_info['avg_rating']):"N/A"; ?>
                    </div>
                    <div class="btn-group pull-right">
                        <button class="btn btn-info  btn-sm" id="send_button" data-toggle="modal" data-target="#myModal"> Send Mail                      <i class="fa fa-envelope"></i>
                        </button>
                    </div>
                    <div class="cardbox">
                        <div class="header text-center">
                            <h4 class="font-bold">ABOUT PATIENT</h4>
                        </div>
                        <div class="body">
                            <div class="user-btm-box">
                                <!-- .row -->
                                <div class="row text-center m-t-10">
                                    <div class="col-lg-4 col-md-12 col-sm-6 col-xs-12 b-r">
                                        <strong>Name</strong>
                                        <p><?php echo ucfirst($user_info['name']); ?></p>
                                    </div>
                                    <div class="col-lg-4 col-md-12 col-sm-6 col-xs-12">
                                        <strong>Gender</strong>
                                        <p><?php echo $user_info['gender']; ?></p>
                                    </div>
                                    <div class="col-lg-4 col-md-12 col-sm-6 col-xs-12 b-r">
                                        <strong>Phone</strong>
                                        <p><?php echo $user_info['phone']; ?></p>
                                    </div>
                                    <div class="col-lg-4 col-md-12 col-sm-6 col-xs-12 b-r">
                                        <strong>Date of Birth</strong>
                                        <p><?php echo date("m-d-Y", strtotime($user_info['date_of_birth'])); ?></p>
                                    </div>
                                    <div class="col-lg-4 col-md-12 col-sm-6 col-xs-12 b-r">
                                        <strong>Email</strong>
                                        <p><?php echo $user_info['email']; ?></p>
                                    </div>
                                    <div class="col-lg-4 col-md-12 col-sm-6 col-xs-12">
                                        <strong>Address</strong>
                                        <p><?php echo $user_info['address']; ?></p>
                                    </div>
                                    <div class="col-lg-4 col-md-12 col-sm-6 col-xs-12 b-r">
                                        <strong>City</strong>
                                        <p><?php echo $user_info['city']; ?></p>
                                    </div>
                                    <div class="col-lg-4 col-md-12 col-sm-6 col-xs-12 b-r">
                                        <strong>State</strong>
                                        <p><?php echo $user_info['state']; ?></p>
                                    </div>
                                    <div class="col-lg-4 col-md-12 col-sm-6 col-xs-12 b-r">
                                        <strong>Zip Code</strong>
                                        <p><?php echo $user_info['zip_code']; ?></p>
                                    </div>
                                    <div class="col-lg-4 col-md-12 col-sm-6 col-xs-12">
                                        <strong>Total Past Appointments</strong>
                                        <p><?php echo (!empty($past_appointment)) ? $past_appointment['appointment'] : "N/A"; ?></p>
                                    </div>
                                    <div class="col-lg-4 col-md-12 col-sm-6 col-xs-12 b-r">
                                        <strong>Total Cancelled Appointments</strong>
                                        <p><?php echo (!empty($cancel_appointment)) ? $cancel_appointment['appointment'] : "N/A"; ?></p>
                                    </div>
                                    <div class="col-lg-4 col-md-12 col-sm-6 col-xs-12 b-r">
                                        <strong>Total Upcoming Appointments</strong>
                                        <p><?php echo (!empty($upcoming_appointment)) ? $upcoming_appointment['appointment'] : "N/A"; ?></p>
                                    </div>
                                    <div class="col-lg-4 col-md-12 col-sm-6 col-xs-12">
                                        <strong>Total Completed Payments</strong>
                                        <p>USD 15000</p>
                                    </div>
                                    <div class="col-lg-4 col-md-12 col-sm-6 col-xs-12 b-r">
                                        <strong>Insurance Provider</strong>
                                        <p><?php echo ($user_info['provider']) ? $user_info['provider'] : "N/A"; ?></p>
                                    </div>
                                    <div class="col-lg-4 col-md-12 col-sm-6 col-xs-12 b-r">
                                        <strong>Member ID</strong>
                                        <p><?php echo ($user_info['member_id']) ? $user_info['member_id'] : "N/A"; ?></p>
                                    </div>
                                    <div class="col-lg-4 col-md-12 col-sm-6 col-xs-12 b-r">
                                        <strong>Group #</strong>
                                        <p><?php echo ($user_info['ins_group']) ? $user_info['ins_group'] : "N/A"; ?></p>
                                    </div>
                                </div>
                                <!-- /.row -->
                                <hr>
                                <!-- .row -->
                                <div class="row text-center m-t-10">
                                    <div class="col-md-12">
                                        Block/Unblock this User: &nbsp;
                                        <?php $status1 = ($user_info['is_blocked'] == 1) ? "style='display:none'" : ""; ?>
                                        <?php $status2 = ($user_info['is_blocked'] == 0) ? "style='display:none'" : ''; ?>

                                        <a href="#" <?php echo $status1; ?> class="btn btn-circle btn-danger blk" id='blk' 
                                           user-id = "<?php echo ($user_info['id']); ?>">Block</a>


                                        <a href="#" <?php echo $status2; ?> class="btn btn-circle btn-success blk"  user-id = "<?php echo ($user_info['id']); ?>" id='unblock' >Unblock</a>


                                        <a href="<?php echo base_url('admin/users/appointment_detail_controller/upcoming_appointments/' . $user_info['id']); ?>/" class="btn btn-circle btn-success">View Appointments</a>
                                        <a href="<?php echo base_url('admin/users/manage_users/user_medical_history/' . $user_info['id']); ?>/" class="btn btn-circle btn-success">View Medical History</a>

                                        <a href="<?php echo base_url('admin/users/appointment_detail_controller/view_transcation/' . $user_info['id']); ?>/" class="btn btn-circle btn-danger">View
                                            Transactions</a>
                                        <a href="#" class="btn btn-circle btn-danger get_patient_list" user-id="<?php echo $user_info['id']; ?>"  data-toggle='modal' data-target='#patient_list'>
                                            Additional Accounts</a>

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
<!-- Modal -->
<div class="modal fade" id="myModal" role="dialog">
    <div class="modal-dialog modal-lg" style="min-width:1226px;">
        <?php echo form_open("admin/users/manage_users/send_email_to_users", ["class" => "users_email_validation", "id" => "frm-example", "onsubmit" => "return tsextareavldt()"]); ?>

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
                        </div>
                        <div class="form-group">
                            <label for="simpleFormPassword"><?php echo $this->lang->line("message"); ?></label>
                            <textarea name="message" id="message" class="form-control" placeholder="Enter Email text" required></textarea>
                        </div>
                        <?php echo form_hidden("sltd_emails", $user_info['id']); ?>
                    </div>
                    <div class="text-center">
                        <button type="submit"  class="btn btn-primary submit-btn" width="10%"><?php echo $this->lang->line("submit"); ?></button>
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
</div>

<div class="modal fade" id="patient_list" role="dialog">
    <div class="modal-dialog modal-lg">

        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header text-center">              
                <h4 class="modal-title w-100">List Of Added Patient Accounts</h4>
            </div>
            <div class="modal-body patient_list">

            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo $this->lang->line("close"); ?></button>
            </div>
        </div>

    </div>
</div>

<!-- end page container -->
<script>
    $("body").on("click", ".blk", function (event) {

        event.preventDefault();
        var id = $(this).attr("id");
        var user_id = $(this).attr("user-id");
        if (id === "blk") {
            $("#blk").css("display", "none");
            $("#unblock").removeAttr("style");
            status = 1;
        } else {
            $("#blk").removeAttr("style");
            $("#unblock").css("display", "none");
            status = 0;
        }
        update_user_status(user_id, status);
    });

    function update_user_status(user_id, status) {
        $.ajax({
            url: site_url + "admin/users/manage_users/update_user_status",
            cache: false,
            type: "POST",
            processData: true,
            data: {user_id: user_id, status: status},
            success: function (data) {
                var response = JSON.parse(data);
                if (response.active) {
                    alert(response.active);
                } else {
                    alert(response.unactive);
                }
            }
        });
    }

    $("body").on("click", ".get_patient_list", function () {
        var user_id = $(this).attr("user-id");
        get_all_user_patients(user_id);
    });
    function get_all_user_patients(id) {
        //alert(id);
        $.ajax({
            url: site_url + "admin/users/manage_users/get_all_user_patient",
            cache: false,
            type: "POST",
            processData: true,
            data: {user_id: id},
            success: function (data) {
                $(".patient_list").html(data);
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