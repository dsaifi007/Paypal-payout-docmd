
<!-- start page content -->
<div class="page-content-wrapper">
    <div class="page-content">
        <div class="page-bar">
            <div class="page-title-breadcrumb">
                <ol class="breadcrumb page-breadcrumb pull-right">
                    <li><i class="fa fa-home"></i>&nbsp;
                        <a class="parent-item" href="<?php echo base_url(); ?>admin">Home</a><i class="fa fa-angle-right"></i>
                        &nbsp;<a class="parent-item" href="<?php echo base_url(); ?>admin/users/manage_users">Manage Users</a><i class="fa fa-angle-right"></i>
                        <a class="parent-item" href="<?php echo base_url(); ?>admin/users/manage_users/user_view/<?php echo $this->uri->segment(5) ?>"><?php echo $user_name['user_name'] ?></a><i class="fa fa-angle-right"></i>
                         <li class="active">Past Appointments Details</li>
                    </li>
                </ol>
            </div>
        </div>
        <!-- add content here -->
        <div class="row">
            <div class="col-md-12">
                <div class="card-box appo-details">
                    <div class="card-head">
                        <header>Appointments Details</header>
                        <div class="btn-group pull-right">
                            <ul class="dropdown-menu pull-right">
                                <li>
                                    <a href="javascript:;">   Appointment Type </a>
                                </li>
                                <li>
                                    <a href="javascript:;">Date </a>
                                </li>

                            </ul>


                        </div>

                    </div>
                    <div class="card-body ">
                        <div class="row">
                            <div class="col-md-4">

                            </div>
                            <div class="col-md-4"></div>
                            <div class="col-md-4">
                                <div class="btn-group pull-right">
                                    <button type="button" class="btn btn-round btn-success" data-toggle="modal" data-target="#filter">Filter<i class="fa fa-filter"></i></button>
                                </div>
                            </div>
                        </div>



                        <div class="mdl-tabs mdl-js-tabs">
                            <div class="mdl-tabs__tab-bar">
                                <a href="<?php echo base_url(); ?>admin/users/appointment_detail_controller/upcoming_appointments/<?php echo $this->uri->segment(5); ?>" class="mdl-tabs__tab ">Upcoming Appointments</a>
                                <a href="<?php echo base_url(); ?>admin/users/appointment_detail_controller/past_appointments/<?php echo $this->uri->segment(5); ?>" class="mdl-tabs__tab is-active">Past Appointments</a>
                                <a href="<?php echo base_url(); ?>admin/users/appointment_detail_controller/cancel_appointments/<?php echo $this->uri->segment(5); ?>" class="mdl-tabs__tab cancel">Canceled Appointments</a>
                            </div>
                            <div class="mdl-tabs__panel is-active p-t-20" id="tab1-panel">
                                <div class="manage-users">
                                    <div class="card card-topline-aqua">
                                        <div class="card-body">
                                            <table id="" class="display" style="width:100%;">
                                                <thead>
                                                    <tr>
                                                        <th>Appointment ID</th>
                                                        <th>Provider Med ID</th>
                                                        <th>Provider Name</th>
                                                        <th>Appointment Type</th>
                                                        <th>Appointment Date</th>
                                                        <th>Appointment Time</th>
                                                        <th>Total Amount</th>
                                                        <th>Action</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php
                                                    if (count($past_appointment) > 0) {
                                                        $data = '';
                                                        foreach ($past_appointment as $value) {
                                                            if ($value['time_abbreviation']) {
                                                                $data = get_time_zone($value['time_abbreviation'], $value['patient_availability_date_and_time']);
                                                            }
                                                            ?>
                                                            <tr>
                                                                <td><?php echo $value['id'] ?></td>
                                                                <td><?php echo $value['doctor_med_id'] ?></td>
                                                                <td><?php echo $value['provider'] ?></td>
                                                                <td><?php echo $value['title'] ?></td>
                                                                <td><?php echo date("m-d-Y", strtotime($value['patient_availability_date'])); ?></td>
                                                                <td><?php echo ($data) ? date("h:i:s", strtotime($data)) : "No Time Zone Set"; ?></td>

                                                                <td><?php echo $value['amount'] ?></td>
                                                                <td> <a href="<?php echo base_url(); ?>admin/users/appointment_detail_controller/detail_past_appointment/<?php echo $value['id'] ?>" class="btn btn-info btn-custm"><i class="fa fa-eye"></i></a></td>
                                                            </tr>
                                                            <?php
                                                        }
                                                    } else {
                                                        echo "<tr><td colspan='7'><center>No Data Found</center></td></tr>";
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
        </div>
    </div>
</div>
<!-- end page content -->

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
                        <form action="<?php echo base_url(); ?>admin/users/appointment_detail_controller/past_appointments/<?php echo $this->uri->segment(5); ?>" method="POST" name="filter_data" id="filter_data">
                            <div class="form-group row">
                                <label class="col-md-4 control-label">Appointment Type</label>
                                <div class="input-group col-md-8">
                                    <?php if (count($appointment_type) > 0) {
                                        ?>
                                        <select name="provider_plan_dot_title" class="form-control" id="title">
                                            <option value=''>Select Type</option>
                                            <?php
                                            foreach ($appointment_type as $key => $value) {
                                                ?>
                                                <option value="<?php echo trim($value['title']); ?>"><?php echo $value['title']; ?></option>
                                            <?php } ?>
                                        </select>
                                    <?php } ?>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-md-4 control-label">Appointment Date</label>
                                <div class="input-group col-md-8">
                                    <?php if (count($past_appointment) > 0) {
                                        ?>
                                        <select name="appointment_dot_patient_availability_date" class="form-control" id="patient_availability_date">
                                            <option value=''>Select Date</option>
                                            <?php
                                            foreach ($past_appointment as $k => $v) {
                                                ?>
                                                <option value="<?php echo trim($v['patient_availability_date']); ?>"><?php echo $v['patient_availability_date']; ?></option>
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
