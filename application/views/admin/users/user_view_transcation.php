<?php
?>
<!-- start page content -->
<div class="page-content-wrapper">
    <div class="page-content">
        <div class="page-bar">
            <div class="page-title-breadcrumb">
                <div class=" pull-left">
                    <div class="page-title">Payment Details</div>
                </div>
                <ol class="breadcrumb page-breadcrumb pull-right">
                    <li><a class="parent-item" href="<?php echo base_url(); ?>dashboard">Home</a>&nbsp;<i class="fa fa-angle-right"></i><a class="parent-item" href="<?php echo base_url(); ?>admin/users/manage_users">Manage User</a>&nbsp;<i class="fa fa-angle-right"></i>
                        <a class="parent-item" href="<?php echo base_url(); ?>admin/users/manage_users/user_view/<?php echo $this->uri->segment(5); ?>"><?php echo $user_name['user_name'];?> </a>&nbsp;<i class="fa fa-angle-right"></i>
                    </li>
                    <li class="active">View Transactions</li>
                </ol>
            </div>
        </div>
        <!-- add content here -->
        <div class="row">
            <div class="col-md-12">
                <div class="manage-users">
                    <div class="card card-topline-aqua">
                        <div class="card-body">
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
                            
                            
                            <table class="table table-striped table-bordered table-hover table-checkable order-column valign-middle" id="user_transcation_list" class="display" style="width:100%;">
                                <thead>
                                    <tr>
                                        <th>S.No.</th>
                                        <th>Provider Name</th>
                                        <th>Appointment Type</th>
                                        <th>Appointment Date</th>
                                        <th>Appointment Time</th>
                                        <th>Payment Date</th>
                                        <th>Total Amount</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    
                                        <?php
                                        if (count($items) > 0) {
                                            foreach ($items as $key => $item) {
                                                ?>
                                                <tr>
                                                <td><?php echo $item['id']; ?></td> 
                                                <td><?php echo $item['provider']; ?></td> 
                                                <td><?php echo $item['title']; ?></td>
                                                <td><?php echo date("d-M-Y",strtotime($item['patient_availability_date'])) ; ?></td>
                                                <td><?php echo $item['patient_availability_time']; ?></td>
                                                <td><?php echo date("m-d-Y H:i:s",strtotime($item['created_date'])); ?></td>
                                                <td><?php echo "USD ".$item['amount']; ?></td> 
                                                <td><a href="<?php echo base_url();?>admin/users/appointment_detail_controller/detail_past_appointment/<?php echo $item['id']; ?>/1" class="btn btn-info btn-custm" style="margin-left:10px;"><i class="fa fa-eye"></i></a></td> 
                                                </tr>
                                                <?php
                                            }
                                        }else{                                    
                                            echo "<td colspan='7'>No Data Found</td>";                                       
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
<!-- end page con-->

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
            <form action="<?php echo base_url(); ?>admin/users/appointment_detail_controller/view_transcation/<?php echo $this->uri->segment(5);?>" method="POST" name="filter_data" id="filter_data">
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
                   <?php if (count($items)>0){
                     ?>
                     <select name="appointment_dot_patient_availability_date" class="form-control" id="patient_availability_date">
                      <option value=''>Select Date</option>
                      <?php
                      foreach ($items as $k => $v) {
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
