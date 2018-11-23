<?php
dd($upcoming_appointment);
?>

<!-- start page content -->
<div class="page-content-wrapper">
    <div class="page-content">
        <div class="page-bar">
            <div class="page-title-breadcrumb">
                <ol class="breadcrumb page-breadcrumb pull-right">
                    <li><i class="fa fa-home"></i>&nbsp;<a class="parent-item" href="<?php echo base_url(); ?>dashboard">Home</a>&nbsp;<i class="fa fa-angle-right"></i>
                    </li>
                    <li class="active">Appointments Details</li>
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
                                <a href="<?php echo base_url();?>admin/users/appointment_detail_controller/index/<?php echo $this->uri->segment(5);?>" class="mdl-tabs__tab is-active">Upcoming Appointments</a>
                                <a href="<?php echo base_url();?>admin/users/appointment_detail_controller/past_appointments/<?php echo $this->uri->segment(5);?>" class="mdl-tabs__tab">Past Appointments</a>
                                <a href="<?php echo base_url();?>admin/users/appointment_detail_controller/cancel_appointments/<?php echo $this->uri->segment(5);?>" class="mdl-tabs__tab cancel">Cancelled Appointments</a>
                            </div>
                            <div class="mdl-tabs__panel is-active p-t-20" id="tab1-panel">
                                <div class="manage-users">
                                    <div class="card card-topline-aqua">
                                        <div class="card-body">
                                            <table id="" class="display" style="width:100%;">
                                                <thead>
                                                    <tr>
                                                        <th>Appointment ID</th>
                                                        <th>Provider Name</th>
                                                        <th>Appointment Type</th>
                                                        <th>Appointment Date</th>
                                                        <th>Appointment Time</th>
                                                        <th>Total Amount</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php
                                                    if(count($upcoming_appointment)>0){
                                                        foreach($upcoming_appointment as $value){
                                                        ?>
                                                    <tr>
                                                    <td><?php echo $value['id'] ?></td>
                                                    <td><?php echo $value['provider'] ?></td>
                                                    <td><?php echo $value['title'] ?></td>
                                                    <td><?php echo $value['patient_availability_date'] ?></td>
                                                    <td><?php echo $value['patient_availability_time'] ?></td>
                                                    <td><?php echo $value['amount'] ?></td>
                                                    </tr>
                                                    <?php 
                                                    }
                                                    }
                                                    else{
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
            <form action="<?php echo base_url(); ?>admin/users/appointment_detail_controller/index/<?php echo $this->uri->segment(5);?>" method="POST" name="filter_data" id="filter_data">
              <div class="form-group row">
                <label class="col-md-4 control-label">Appointment Type</label>
                <div class="input-group col-md-8">
                  <?php if (count($appointment_type)>0){
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
                   <?php if (count($upcoming_appointment)>0){
                     ?>
                     <select name="appointment_dot_patient_availability_date" class="form-control" id="patient_availability_date">
                      <option value=''>Select Date</option>
                      <?php
                      foreach ($upcoming_appointment as $k => $v) {
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
