
<style>
    .active{
        display:none;
    }
</style>
<!-- start page content -->
<div class="page-content-wrapper">
    <div class="page-content">
        <div class="page-bar">
            <div class="page-title-breadcrumb">
                <div class="pull-left">
                    <div class="page-title"><?php echo ucfirst($doctor_info['name']); ?></div>
                </div>
                 
                <ol class="breadcrumb page-breadcrumb pull-right">
                    <li><i class="fa fa-home"></i>&nbsp;<a class="parent-item" href="<?php echo base_url();?>dashboard">Home</a>&nbsp;<i class="fa fa-angle-right"></i></li>
                    <li class="active"><?php echo ucfirst($doctor_info['name']); ?></li>
                </ol>
            </div>
        </div>
        <!-- add content here -->
        <div class="row">
            <div class="col-md-8 col-xs-12 user-details">
                <div class="white-box">
                    <?php
                        if (isset($success)) {
                            echo $success;                        }
                    ?>
                    <div class="patient-profile">
                        <?php if($doctor_info['profile_url']){ ?>
                        <img src="<?php echo $doctor_info['profile_url']; ?>" class="img-responsive" alt="">
                        <?php  } else{  ?>
                        <img src="<?php echo base_url();?>/assets/admin/avatar/avatar.png" class="img-responsive" alt="">
                        <?php } ?>
                    </div>
                    <div class="cardbox">
                        <div class="header text-center">
                            <h4 class="font-bold">ABOUT PROVIDER</h4>
                        </div>
                        <div class="body">
                            <div class="user-btm-box">
                                <!-- .row -->
                                <div class="row text-center m-t-10">
                                    <div class="col-lg-4 col-md-12 col-sm-6 col-xs-12 b-r">
                                        <strong>Name</strong>
                                        <p><?php echo ucfirst($doctor_info['name']); ?></p>
                                    </div>
                                    <div class="col-lg-4 col-md-12 col-sm-6 col-xs-12">
                                        <strong>Gender</strong>
                                        <p><?php echo $doctor_info['gender']; ?></p>
                                    </div>
                                    <div class="col-lg-4 col-md-12 col-sm-6 col-xs-12 b-r">
                                        <strong>Phone</strong>
                                        <p><?php echo $doctor_info['phone']; ?></p>
                                    </div>
                                    <div class="col-lg-4 col-md-12 col-sm-6 col-xs-12 b-r">
                                        <strong>Date of Birth</strong>
                                        <p><?php echo $doctor_info['date_of_birth']; ?></p>
                                    </div>
                                    <div class="col-lg-4 col-md-12 col-sm-6 col-xs-12 b-r">
                                        <strong>Email</strong>
                                        <p><?php echo $doctor_info['email']; ?></p>
                                    </div>
                                    <div class="col-lg-4 col-md-12 col-sm-6 col-xs-12">
                                        <strong>Address</strong>
                                        <p><?php echo $doctor_info['address']; ?></p>
                                    </div>
                                    <div class="col-lg-4 col-md-12 col-sm-6 col-xs-12 b-r">
                                        <strong>City</strong>
                                        <p><?php echo $doctor_info['city']; ?></p>
                                    </div>
                                    <div class="col-lg-4 col-md-12 col-sm-6 col-xs-12 b-r">
                                        <strong>State</strong>
                                        <p><?php echo $doctor_info['state']; ?></p>
                                    </div>
                                    <div class="col-lg-4 col-md-12 col-sm-6 col-xs-12 b-r">
                                        <strong>Zip Code</strong>
                                        <p><?php echo $doctor_info['zip_code']; ?></p>
                                    </div>
                                    <div class="col-lg-4 col-md-12 col-sm-6 col-xs-12 b-r">
                                        <strong>Undergraduate University</strong>
                                        <p><?php echo $doctor_info['undergraduate_university']; ?></p>
                                    </div>
                                    <div class="col-lg-4 col-md-12 col-sm-6 col-xs-12 b-r">
                                        <strong>Medical School</strong>
                                        <p><?php echo $doctor_info['medical_school']; ?></p>
                                    </div>
                                    <div class="col-lg-4 col-md-12 col-sm-6 col-xs-12 b-r">
                                        <strong>Residency</strong>
                                        <p><?php echo $doctor_info['residency']; ?></p>
                                    </div>
                                    <div class="col-lg-4 col-md-12 col-sm-6 col-xs-12 b-r">
                                        <strong>Medical License Number</strong>
                                        <p><?php echo $doctor_info['medical_license_number']; ?></p>
                                    </div>
                                    <div class="col-lg-4 col-md-12 col-sm-6 col-xs-12 b-r">
                                        <strong>Spacility</strong>
                                        <p><?php echo $doctor_info['spacility']; ?></p>
                                    </div>
                                </div>
                                <!-- /.row -->
                                <hr>
                                <!-- .row -->
                                <div class="row text-center m-t-10">
                                    <div class="col-md-12">
                                        <?php  $status1 = ($doctor_info['is_blocked'] == 1)?"style='display:none'":""; ?>
                                        <?php  $status2 = ($doctor_info['is_blocked'] == 0)?"style='display:none'":''; ?>

  <a href="#" <?php echo $status1; ?> class="btn btn-circle btn-danger blk" id='blk' 
                                            doctor-id = "<?php echo ($doctor_info['id']);?>">Block</a>


                                        <a href="#" <?php echo $status2; ?> class="btn btn-circle btn-success blk"  doctor-id = "<?php echo ($doctor_info['id']);?>" id='unblock' >Unblock</a>
                                <a href="#" class="btn btn-circle btn-success">View Payment</a>
                                <a href="#" class="btn btn-circle btn-success">View Appoinment</a>

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

  <script>
      $('body').on('click', "#reject_email", function() {
    var email =$(this).attr("data-email");
     $("input[name='email']").val(email);
});
      $("body").on("click",".blk",function(event) {
            event.preventDefault();
            var id = $(this).attr("id");
            var doctor_id = $(this).attr("doctor-id");
            if (id == "blk") {
                $("#blk").css("display","none");
                $("#unblock").removeAttr("style");
                status = 1;
            } else {
                $("#blk").removeAttr("style");
                $("#unblock").css("display","none");
                status = 0;
            }
            update_doctor_status(doctor_id,status);
     });
function update_doctor_status(doctor_id , status) {
  $.ajax({
      url : site_url+"admin/doctors/registered_doctors/update_doctor_status",
      cache: false,
      type: "POST",
      processData :true,
      data: {doctor_id : doctor_id,status:status},
      success : function(data) {
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