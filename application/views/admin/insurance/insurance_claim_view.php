<?php
if (isset($filetration)) {
    $data = json_encode($filetration);
}
?>
<style>

    table.dataTable thead>tr>th.sorting {padding-right: 15px; min-width: 100px;}
</style>
<?php
?>
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
                                    <div class="btn-group pull-left">

                                    </div>
                                </div>
                                <div class="col-md-4"></div>
                                <div class="col-md-4">
                                    <div class="btn-group pull-right">
                                        <button type="button" class="btn btn-round btn-success" data-toggle="modal" data-target="#filter">Filter<i class="fa fa-filter"></i></button>

                                    </div>
                                </div>
                            </div>
                            <?php echo form_open("admin/users/manage_users/send_email_to_users", ["class" => "users_email_validation", "id" => "frm-example", "onsubmit" => "return tsextareavldt()"]); ?>                           

                            <table  id="insurance_claim"  style="width:100%"  class=" s table table-striped table-bordered table-hover table-checkable order-column">
                                <thead> 
                                    <tr >
                                        <th><?php echo $this->lang->line("sr_n"); ?></th>
                                        <th><?php echo $this->lang->line("patient_med_id"); ?></th>
                                        <th><?php echo $this->lang->line("first_name"); ?></th>
                                        <th><?php echo $this->lang->line("last_name"); ?></th>
                                        <th><?php echo $this->lang->line("doctor_first_name"); ?></th>
                                        <th><?php echo $this->lang->line("doctor_last_name"); ?></th>
                                        <th><?php echo $this->lang->line("app_type"); ?></th>
                                        <th><?php echo $this->lang->line("app_date"); ?></th>
                                        <th><?php echo $this->lang->line("app_time"); ?></th>
                                        <th><?php echo $this->lang->line("paid_amount"); ?></th>
<!--                                        <th><?php //echo $this->lang->line("payment_mode");  ?></th>-->
                                        <th><?php echo $this->lang->line("ins_provide"); ?></th>
                                        <th><?php echo $this->lang->line("member_id"); ?></th>
                                        <th><?php echo $this->lang->line("group_id"); ?></th>
                                        <th><?php echo $this->lang->line("claim_request"); ?></th>
                                        <th><?php echo $this->lang->line("status"); ?></th>
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

<div class="modal fade" id="filter" role="dialog">
    <div class="modal-dialog modal-lg">

        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-body">
                <div class="card card-box">
                    <div class="card-head">
                        <header>Filter</header>
                    </div>
                    <div class="card-body " id="bar-parent">
                        <form action="<?php echo base_url(); ?>admin/insurance/insurance_claim/index" method="POST" name="filter_data" id="filter_data">
                            <div class="form-group row">
                                <label class="col-md-4 control-label">Select Appointment Type</label>
                                <div class="input-group col-md-8">
                                    <?php
                                    $ids = array_column($filter_data, 'title');

                                    $ids = array_unique($ids);
                                    $array = array_filter($filter_data, function ($key, $value) use ($ids) {
                                        return in_array($value, array_keys($ids));
                                    }, ARRAY_FILTER_USE_BOTH);

                                    if (count($ids) > 0 && isset($ids)) {
                                        ?>
                                        <select name="title" class="form-control" id="state">
                                            <option value=''>Select Appointment Type</option>
                                            <?php
                                            foreach ($ids as $key => $value) {
                                                ?>
                                                <option value="<?php echo trim($value); ?>"><?php echo $value; ?></option>
                                            <?php } ?>
                                        </select>
                                    <?php } ?>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-md-4 control-label">Select Appointment Date</label>
                                <div class="input-group col-md-8">
                                    <?php
                                    if (count($filter_data) > 0 && isset($filter_data)) {
                                        ?>
                                        <select name="patient_availability_date" class="form-control" id="city">
                                            <option value=''>Select Appointment Date</option>
                                            <?php
                                            foreach ($filter_data as $key => $value) {
                                                ?>
                                                <option value="<?php echo trim($value['date']); ?>"><?php echo date("m-d-Y", strtotime($value['date'])); ?></option>
                                            <?php } ?>
                                        </select>
                                    <?php } ?>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-md-4 control-label">Claim Status</label>
                                <div class="input-group col-md-8">
                                    <select name="status" class="form-control" >
                                        <option value=''>Select Status</option>
                                        <option value="1">Pending</option>
                                        <option value="2">Action Required!</option>
                                        <option value="3">Claim Submited</option>
                                        <option value="4">Rejected</option>
                                        <option value="5">Accepted</option>
                                        <option value="6">Completed</option>
                                    </select>
                                </div>
                            </div>
                            <button type="submit" class="btn btn-round btn-success">Submit</button>
                        </form>
                    </div>
                </div>  
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
        </div>

    </div>
</div>

<!-- end page container -->

<script>
    var filterdata = '';
    filterdata = '<?php echo (isset($data) && $data != '') ? $data : ''; ?>';
    
    $("body").on("click", ".insurance-status", function () {
        var action_id = $(this).attr("action-id");
        var status_id = $(this).attr("status-id");
        var uid = $(this).attr("user_id");
        var p_id = $(this).attr("patient_id");
        //alert(uid);
        update_insurance_claim_status(action_id, status_id, uid, p_id);
    });

    function update_insurance_claim_status(action_id, status, uid, p_id) {
        $.ajax({
            url: site_url + "admin/insurance/insurance_claim/update_insurance_action_status",
            cache: false,
            type: "POST",
            processData: true,
            data: {appointment_id: action_id, status: status, user_id: uid, patient_id: p_id},
            success: function (data) {
                if (data) {
                    alert("Status Successfully updated");
                }
                location.reload();
            }
        });
    }
</script>



