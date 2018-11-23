<link href="<?php echo base_url(); ?>assets/assets/summernote/summernote.css" rel="stylesheet">

<style>
    .jr-ratenode{

    }
    .label-danger{background-color: #337ab7 !important;}
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
                <div class=" pull-left">
                    <div class="page-title"><?php echo $this->lang->line("page_title"); ?></div>
                </div>
                <ol class="breadcrumb page-breadcrumb pull-right">
                    <li><i class="fa fa-home"></i>&nbsp;<a class="parent-item" href="<?php echo base_url(); ?>dashboard"><?php echo $this->lang->line("home"); ?></a>&nbsp;<i class="fa fa-angle-right"></i></li>
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
                                       <!--  <button type="button" class="btn btn-round btn-success" data-toggle="modal" data-target="#filter"><?php //echo $this->lang->line("filter_btn"); ?><i class="fa fa-filter"></i></button> -->
                                        <button class="btn btn-info  btn-sm" id="send_button" data-toggle="modal" data-target="#myModal" disabled = "disabled" > <?php echo $this->lang->line("send_btn"); ?>
                                            <i class="fa fa-envelope"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <input type='hidden' id='myDivv' value='' />
                            <?php echo form_open("admin/doctors/rejected_doctors/send_email_to_doctors", ["class" => "users_email_validation", "id" => "frm-example", "onsubmit" => "return tsextareavldt()"]); ?>
                            <!-- <input type="checkbox" name="selectall" id="selectall" value='0' style='align:right'>Select All -->
                            <!-- Modal -->
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
                                        <th><input type="checkbox" name="selectall" id="selectall" value='0' style='align:right'></th>
                                        <th><?php echo $this->lang->line("sr_n"); ?></th>
                                        <th>First Name</th>
                                        <th>Last Name</th>
                                        <th><?php echo $this->lang->line("email"); ?></th>
                                        <th><?php echo $this->lang->line("phone"); ?></th>
                                        <th><?php echo $this->lang->line("gender"); ?></th>
                                        <th><?php echo $this->lang->line("dob"); ?></th>
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
                        <form action="<?php echo base_url(); ?>admin/doctors/rejected_doctors/index" method="POST" name="filter_data" id="filter_data">
                            <div class="form-group row">
                                <label class="col-md-4 control-label"><?php echo $this->lang->line("gender"); ?></label>
                                <div class="input-group col-md-8">
                                    <div class="radio">
                                        <input id="male" name="doctors_gender" type="radio" value='male'  />
                                        <label style="line-height: normal;" for="male"><?php echo $this->lang->line("male"); ?></label>
                                    </div> 
                                    <div class="radio">
                                        <input id="female" name="doctors_gender" type="radio" value='female' >
                                        <label style="line-height: normal;" for="female"><?php echo $this->lang->line("female"); ?></label>
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
        <?php echo form_open("admin/doctors/rejected_doctors/send_rejected_mail_to_doctor", ["class" => "doctor_validation", "id" => "frm-example"]); ?>
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
                        <?php echo form_open("admin/doctors/rejected_doctors/update_doctors_status"); ?>
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


<!-- The Approve popup -->
<script type="text/javascript">
    $("body").on("click", "a.approve", function () {
        event.preventDefault();
        var doctor_id = $(this).attr("data-id");
        //alert(doctor_id);
        $("input[name='doctor_id']").val(doctor_id);

    });



    $('.group1').start(function (cur) {
        console.log(cur);
        $('#info').text(cur);
    });
    $(".jr-nomal").on('click', function () {
        $("input[name='rating']").val($('.group1').getCurrentRating());
        alert($('.group1').getCurrentRating());// + "--" + $('.group2').getCurrentRating());
    });

</script>


<?php
//dd($filtering_data);
if (count(@$filtering_data) > 0) {
    $data = json_encode(@$filtering_data);
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