<?php
if (isset($filetration)) {
    $data = json_encode($filetration);
}
?><style>

    table.dataTable thead>tr>th.sorting {padding-right: 15px; min-width: 100px;}
</style>
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
                                        <button type="button" class="btn btn-round btn-success" data-toggle="modal" data-target="#filter"><?php echo $this->lang->line("filter_btn"); ?><i class="fa fa-filter"></i></button>

                                    </div>
                                </div>
                            </div>

                            <div class="table-responsive">
                                <table  id="prescription_list" class="table table-striped table-bordered">
                                    <thead> 
                                        <tr>
                                            <th><?php echo $this->lang->line("user_med_id"); ?></th>
                                            <th><?php echo $this->lang->line("first_name"); ?></th>
                                            <th><?php echo $this->lang->line("last_name"); ?></th>
                                            <th><?php echo $this->lang->line("DOB"); ?></th>
                                            <th><?php echo $this->lang->line("p_med_id"); ?></th>                      
                                            <th><?php echo $this->lang->line("doctor_first_name"); ?></th>
                                            <th><?php echo $this->lang->line("doctor_last_name"); ?></th>
                                            <th><?php echo $this->lang->line("app_date"); ?></th>
                                            <th><?php echo $this->lang->line("app_time"); ?></th>
                                            <th><?php echo $this->lang->line("app_type"); ?></th>
                                            <th><?php echo $this->lang->line("Status"); ?></th>
                                            <th><?php echo $this->lang->line("action"); ?></th>
                                        </tr>
                                    </thead>
                                    <tbody>

                                    </tbody>
                                </table>
                            </div>
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
                        <header><?php echo $this->lang->line("filter_by"); ?></header>
                    </div>
                    <div class="card-body " id="bar-parent">
                        <form action="<?php echo base_url(); ?>admin/prescription/prescription_controller/index" method="POST" name="filter_data" id="filter_data">
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
                                <label class="col-md-4 control-label">Gender</label>
                                <div class="input-group col-md-8">

                                    <select name="gender" class="form-control" >
                                        <option value=''>Select Gender</option>
                                        <option value="male">Male</option>
                                        <option value="female">Female</option>
                                    </select>

                                </div>
                            </div>
                             <div class="form-group row">
                                <label class="col-md-4 control-label">Status</label>
                                <div class="input-group col-md-8">
                                    <select name="status" class="form-control" >
                                        <option value=''>Select Status</option>
                                        <option value="0">Action Required</option>
                                        <option value="1">Requested</option>
                                        <option value="2">Pending</option>
                                        <option value="3">Filled</option>
                                        <option value="4">Completed</option>
                                        <option value="5">On Hold</option>
                                        <option value="6">Denied</option>
                                        <option value="7">Contact Patient!</option>
                                        <option value="8">Contact Prescriber!</option>
                                        <option value="9">Contact Pharmacy!</option>
                                        <option value="10">Prior Authorization Needed</option>
                                        <option value="11">Too Soon</option>
                                    </select>
                                </div>
                            </div>
                            <button type="submit" class="btn btn-round btn-success"><?php echo $this->lang->line("success"); ?></button>
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
<!-- Modal -->
<div id="add_note" class="modal fade" role="dialog">
    <div class="modal-dialog modal-lg">

        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Admin Note</h4>
            </div>
            <div class="modal-body">
                <table class="table table-striped table-bordered table-hover  order-column" style="width:100%;">
                    <thead>
                        <tr>
                            <th>Admin Note</th>
                            <th>Signature</th>
                            <th>Time Stamp</th>
                        </tr>
                    </thead>
                    <tbody class="result">

                    </tbody>

                </table>


                <?php echo form_open("admin/doctors/appointment_detail_controller/add_admin_note"); ?>
                <div class= "col-lg-12"><textarea  style="width:750px;height: 154px; " name="note" placeholder="Add Note" required></textarea>
                    <?php echo form_hidden("appointment_id", ''); ?>
                    <input type="submit" name="save" class="btn btn-danger" value="Submit">
                </div>
                <?php echo form_close(); ?>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
        </div>

    </div>
</div>
<script>
    var datatable;
    var filterdata = '';
    filterdata = '<?php echo (isset($data) && $data != '') ? $data : ''; ?>';
    $(document).ready(function () {
        datatable = $('#prescription_list').DataTable({

            "processing": true, //Feature control the processing indicator.
            "serverSide": true, //Feature control DataTables' server-side processing mode.
            "order": [], //Initial no order.
            //"scrollX":false,
            //"autoWidth": true,
            "pageLength": 50, // Set Page Length
            "lengthMenu": [[5, 25, 50, 100, -1], [5, 25, 50, 100, "All"]],
            // Load data for the table's content from an Ajax source
            "ajax": {
                "url": site_url + "admin/prescription/prescription_controller/getdata",
                "type": "POST",
                //Custom Post
                "data": {"filter_data": filterdata}

            },
            //Set column definition initialisation properties.
            "columnDefs": [
                {
                    "targets": [8, 9], //first, Fourth, seventh column
                    "orderable": false //set not orderable
                }
            ],
            "fnInitComplete": function (oSettings, response) {

                $("#countData").text(response.recordsTotal);
            }

        });
    });
    $('#prescription_list').wrap("<div class='scrolledTable' style='overflow-y: auto; clear:both;'></div>");
    $("body").on("click", ".p-status", function (e) {
        e.preventDefault();
        var row_id = $(this).attr("action-id");
        var status = $(this).attr("status-id");
        //alert(row_id);
        update_appt_presc_status(row_id, status);
    });
    function update_appt_presc_status(row_id, status) {
        //alert(id);
        $.ajax({
            url: site_url + "admin/prescription/prescription_controller/update_appt_presc_status",
            cache: false,
            type: "POST",
            processData: true,
            data: {id: row_id, status: status},
            success: function (data) {
                var msg = JSON.parse(data);
                alert(msg.message);
                window.location.reload();
            }
        });
    }
    $("body").on("click", ".admin_note", function (e) {
        e.preventDefault();
        var appt_id = $(this).attr("appt_id");
        $("input[name='appointment_id']").val(appt_id);
    });
    $("body").on("click", ".admin_note", function () {
        var appt_id = $(this).attr("appt_id");
        $.ajax({
            url: site_url + "admin/prescription/prescription_controller/get_admin_note_detail",
            cache: false,
            type: "POST",
            processData: true,
            data: {appointment_id: appt_id},
            success: function (data) {
                $(".result").html(data);
            }
        });
    });
</script>

<?php
//dd($filtering_data);
// if (count($filtering_data)>0) {
//  $data = json_encode($filtering_data);	
// }
?>
