
<style>
    .radio{
        line-height:normal;
    }
    div.dtp{
        z-index: 999999;
    }
</style>
<link rel="stylesheet" href="<?php echo base_url(); ?>assets/assets/material-datetimepicker/bootstrap-material-datetimepicker.css" />
<link href="<?php echo base_url(); ?>assets/assets/select2/css/select2.css" rel="stylesheet" type="text/css" />
<link href="<?php echo base_url(); ?>assets/assets/select2/css/select2-bootstrap.min.css" rel="stylesheet" type="text/css" />

<!-- start page content -->
<div class="page-content-wrapper">
    <div class="page-content">
        <div class="page-bar">
            <div class="page-title-breadcrumb">
                <div class=" pull-left">
                    <div class="page-title">List of Added Notifications</div>
                </div>
                <ol class="breadcrumb page-breadcrumb pull-right">
                    <li><i class="fa fa-home"></i>&nbsp;<a class="parent-item" href="<?php echo base_url(); ?>dashboard">Home</a>&nbsp;<i class="fa fa-angle-right"></i>
                    </li>
                    <li class="active">Manage Notifications</li>
                </ol>
            </div>
        </div>
        <!-- add content here -->
        <div class="row">
            <div class="col-md-12">
                <div class="card card-topline-aqua">
                    <div class="card-body">
                        <?php echo display_message_info([1 => @$success, 2 => @$error, 3 => validation_errors()]); ?>

                        <div class="col-md-12">
                            <div class="row">
                                <div class="col-md-6 col-sm-6"></div>
                                <div class="col-md-6 col-sm-6">
                                    <div class="btn-group pull-right">
                                        <a href="<?php echo base_url(); ?>admin/notification/admin_notification_controller/add_notification_info" class="btn btn-success">Add New Notification <i class="fa fa-plus-circle" aria-hidden="true"></i>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <table class="table table-striped table-bordered table-hover table-checkable order-column valign-middle" id="notification_view" class="display" style="width:100%;">
                            <thead>
                                <tr>
                                    <th>S.No.</th>
                                    <th>Title</th>
                                    <th>Notification Description</th>
                                    <th class="text-center">Action</th>
                                </tr>
                            </thead>
                            <tbody>

                                <?php
                                if (count($items) > 0 && !empty($items)) {
                                    foreach ($items as $key => $value) {
                                        ?>
                                        <tr>
                                            <td><?php echo $value['id']; ?></td>
                                            <td><?php echo $value['name']; ?></td>
                                            <td><?php echo $value['additional_info']; ?></td>
                                            <td>
                                                <a href="<?php echo base_url(); ?>admin/notification/admin_notification_controller/edit_notification_info/<?php echo $value['id']; ?>" class="btn btn-primary btn-xs">	<i class="fa fa-pencil"></i> Edit</a>
                                                <button type="button" class="btn btn-danger btn-xs send"  data-toggle="modal" user-type="<?php echo $value['notification_type']; ?>"   data-id="<?php echo $value['id']; ?>" data-target="#send"><i class="fa fa-paper-plane" aria-hidden="true"></i>Send</button>
                                                <button type="button" class="btn btn-default btn-xs setting" data-toggle="modal"  notification-scheduler-id="<?php echo $value['notification_scheduler_id']; ?>"  item-id="<?php echo $value['id']; ?>" data-target="#setting"><i class="fa fa-cogs" aria-hidden="true"></i>Settting</button>
                                                <a href="<?php echo base_url('admin/notification/admin_notification_controller/delete/' . $value['id']) ?>"  class="btn btn-danger btn-xs" onclick="return confirm('Are you sure you want to delete this record')"><i class="fa fa-trash-o"></i></a>

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



<!-- Modal send for user or provider -->
<div id="send" class="modal fade" role="dialog">
    <div class="modal-dialog">

        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <div class="popup">
                    <div class="content">
                        <?php echo form_open("admin/notification/admin_notification_controller/update_user_ids"); ?>

                        <h4 class="text-center ">Send To</h4>
                        <div class="col-md-8 p-t-20">
                            <div class="p-t-20 p-b-20">
                                <label>
                                    <input type="radio" class="user-1" name="notification_type"  value="1"> &emsp;All Users</label>
                            </div>
                            <div class="p-b-20">
                                <label>
                                    <input type="radio" class="user-2" name="notification_type" value="2">&emsp;All Providers</label>
                            </div>
                            <div class="p-b-20">
                                <label>
                                    <input type="radio"  class="user-3"  name="notification_type" value="3">&emsp;Select Users from list</label>
                            </div>
                            <div class="p-b-20">
                                <label>
                                    <input type="radio" class="user-4"  name="notification_type" value="4">&emsp;Select Providers from list</label>
                            </div>
                            <div class="p-b-20">
                                <label>
                                    <input type="radio" class="user-4"  name="notification_type" value="5">&emsp;Select New Users </label>
                            </div>
                            <div class="p-b-20">
                                <label>
                                    <input type="radio" class="user-4"  name="notification_type" value="6">&emsp;Select New Providers</label>
                            </div>
                        </div>
                        <input type="hidden" name="id" id="hidden" value='' >
                        <div class="col-md-12 p-t-20">
                            <div class="row user_list" style="display:none">
                                <label class="col-lg-12 col-md-12 control-label">Select Multiple Users
                                </label>
                                <div class="col-lg-12 col-md-12">
                                    <?php if (count($users_list) > 0 && !empty($users_list)) {
                                        ?>
                                        <select id="multiple" name="user_ids[]" class="form-control select2-multiple col-lg-12 col-md-12" multiple>

                                            <?php
                                            foreach ($users_list as $k => $v) {
                                                ?>
                                                <option value="<?php echo $v['user_id']; ?>"><?php echo ucfirst($v['name']); ?></option>
                                            <?php }
                                            ?>
                                        </select>

                                    <?php }
                                    ?>


                                </div>
                            </div>


                            <div class="row doctor_list" style="display:none">
                                <label class="col-lg-12 col-md-12 control-label">Select Multiple Providers
                                </label>
                                <div class="col-lg-12 col-md-12">
                                    <?php if (count($doctors_list) > 0 && !empty($doctors_list)) {
                                        ?>
                                        <select id="multiple" name="doctor_ids[]" class="form-control select2-multiple col-lg-12 col-md-12" multiple>

                                            <?php
                                            foreach ($doctors_list as $key => $value) {
                                                ?>
                                                <option value="<?php echo $value['id']; ?>"><?php echo ucfirst($value['name']); ?></option>
                                            <?php }
                                            ?>
                                        </select>

                                    <?php }
                                    ?>


                                </div>
                            </div>

                            <div class="row new_user" style="display:none">
                                <label class="col-lg-12 col-md-12 control-label">Select Multiple New Users
                                </label>
                                <div class="col-lg-12 col-md-12">
                                    <?php
                                    if (count($new_users_list) > 0 && !empty($new_users_list)) {
                                        ?>
                                        <select id="multiple" name="user_ids[]" class="form-control select2-multiple col-lg-12 col-md-12" multiple>

                                            <?php
                                            foreach ($new_users_list as $key => $value) {
                                                ?>
                                                <option value="<?php echo $value['user_id']; ?>"><?php echo ucfirst($value['full_name']); ?></option>
                                            <?php }
                                            ?>
                                        </select>

                                    <?php }
                                    ?>


                                </div>
                            </div>
                            <div class="row new_doctor_list" style="display:none">
                                <label class="col-lg-12 col-md-12 control-label">Select Multiple New Providers
                                </label>
                                <div class="col-lg-12 col-md-12">
                                    <?php if (count($new_doctor_list) > 0 && !empty($new_doctor_list)) {
                                        ?>
                                        <select id="multiple" name="doctor_ids[]" class="form-control select2-multiple col-lg-12 col-md-12" multiple>

                                            <?php
                                            foreach ($new_doctor_list as $key => $value) {
                                                ?>
                                                <option value="<?php echo $value['id']; ?>"><?php echo ucfirst($value['full_name']); ?></option>
                                            <?php }
                                            ?>
                                        </select>

                                    <?php }
                                    ?>


                                </div>
                            </div>

                        </div>
                        <div class="col-lg-12 p-t-20 text-center">
                            <input type="submit" value="Submit" name="save"  class="mdl-button mdl-js-button mdl-button--raised mdl-js-ripple-effect m-b-10 m-r-20 btn-pink">
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

<!-- Modal send for settting -->
<div id="setting" class="modal fade" role="dialog">
    <div class="modal-dialog">

        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <div class="content">

                    <?php echo form_open("admin/notification/admin_notification_controller/updatescheduler_id"); ?>
                    <h4 class="text-center ">Setting</h4>
                    <div class="col-md-8 p-t-20">
                        <div class="p-b-20">
                            <input type="hidden" name="item_id" id="scheduler_id" value="" >
                            <input type="radio" name="notification_scheduler_id" class='scheduler-<?php echo $schedule_list[0]['id']; ?>' id='time_settting'  value="<?php echo $schedule_list[0]['id']; ?>">
                            <label for="optionsRadios2">
                                <?php echo $schedule_list[0]['name']; ?>
                            </label>
                            <input type="text" name="schedule_time" id="min-date" class="floating-label mdl-textfield__input"  disabled="disabled" placeholder="Select DateTime">

                        </div>
                        &emsp;&emsp;Recurring Notification
                        <div class="p-b-20">
                            <br>
                            <ul style="list-style: none;">
                                <?php
                                if (count($schedule_list) > 0 && !empty($schedule_list)) {
                                    foreach ($schedule_list as $key => $value) {
                                        if ($value['id'] != 1) {
                                            ?>
                                            <li> 
                                                <div class=" p-b-20">
                                                    <input type="radio" class="scheduler-<?php echo $value['id'] ?>" name="notification_scheduler_id"  value="<?php echo $value['id'] ?>">
                                                    <label for="optionsRadios2">
                                                        <?php echo $value['name'] ?>
                                                    </label>
                                                </div>
                                            </li>
                                            <?php
                                        }
                                    }
                                }
                                ?>
                            </ul>
                        </div>

                    </div>
                    <div class="col-lg-12 p-t-20 text-center">
                        <input type="submit" name="confirm" value="Confirm" class="mdl-button mdl-js-button mdl-button--raised mdl-js-ripple-effect m-b-10 m-r-20 btn-pink">
                    </div>
                    <?php echo form_close(); ?>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
        </div>

    </div>
</div>

<script>
    var datatable;
    $(document).ready(function () {
        $('#notification_view').DataTable({
            "pageLength": 50, // Set Page Length
            "lengthMenu": [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]],
            "order": [], //Initial no order.
            "columnDefs": [
                {
                    //"targets": [0, 2, 4], //first, Fourth, seventh column
                    "orderable": false //set not orderable
                },
            ],

        });

        $("span.select2").removeAttr("style");
        $("input[type='radio']").click(function () {
            var checked = $(this).val();
            if (checked == 3) {
                $(".user_list").show();
                $(".doctor_list").hide();
                $(".new_user").hide();
                $(".new_doctor_list").hide();
            } else if (checked == 4) {
                $(".doctor_list").show();
                $(".user_list").hide();
                $(".new_user").hide();
                $(".new_doctor_list").hide();
            } else if (checked == 5) {
                $(".new_user").show();
                $(".user_list").hide();
                $(".doctor_list").hide();
                $(".new_doctor_list").hide();
            } else if (checked == 6) {
                $(".new_doctor_list").show();
                $(".new_user").hide();
                $(".user_list").hide();
                $(".doctor_list").hide();
            } else {
                $(".user_list").hide();
                $(".doctor_list").hide();
            }
        });
        $(".select2-search__field").attr("placeholder", "Select User");
    });

    /*
     Work -- this  function is used for the validtion symptoms 
     */
    $("document").ready(function () {
        $("#add_symptomss").validate({
            // errorPlacement: function(error, element) {
            //  error.appendTo(element.closest('.form-group').after());
            // },
            rules: {
                name: {
                    required: true,
                    minlength: 5,
                    maxlength: 45
                },
                additional_info: {
                    required: true,
                    minlength: 5
                },
                spn_name: {
                    required: true,
                    minlength: 5,
                    maxlength: 45
                },
                spn_additional_info: {
                    required: true,
                    minlength: 5
                },
                submitHandler: function (form) {
                    form.submit();
                }
            }
        });
        $("input[name='notification_scheduler_id']").click(function () {
            var checked = $(this).val();
            if (checked == 1) {
                $("#min-date").removeAttr("disabled");
                $("#min-date").attr("required", "required");
            } else {
                $("#min-date").attr("disabled", "disabled");
            }
        });
    });

    $("body").on("click", ".send", function () {
        var user_type = $(this).attr("user-type");
        $(".user-" + user_type).attr("checked", "checked");
        $("#hidden").val($(this).attr("data-id"));
    });
    $("body").on("click", ".setting", function () {
        var id = $(this).attr("notification-scheduler-id");
        $(".scheduler-" + id).attr("checked", "checked");
        $("#scheduler_id").val($(this).attr("item-id"));
    });

</script>

<!--select2-->
<script src="<?php echo base_url(); ?>assets/assets/select2/js/select2.js" ></script>
<script src="<?php echo base_url(); ?>assets/assets/select2/js/select2-init.js" ></script>
<script  src="<?php echo base_url(); ?>assets/assets/material-datetimepicker/moment-with-locales.min.js"></script>
<script  src="<?php echo base_url(); ?>assets/assets/material-datetimepicker/bootstrap-material-datetimepicker.js"></script>
<script  src="<?php echo base_url(); ?>assets/assets/material-datetimepicker/datetimepicker.js"></script>