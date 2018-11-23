
<style>
    th.sorting_disabled {padding-right: 15px; min-width: 140px;}
    table.dataTable thead>tr>th.sorting {min-width: 140px;}
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
                    <li><i class="fa fa-home"></i>&nbsp;<a class="parent-item" href="<?php echo base_url();?>dashboard"><?php echo $this->lang->line("home"); ?></a>&nbsp;<i class="fa fa-angle-right"></i></li>
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
                            <table  id="user_payment"  style="width:100%"  class=" s table table-striped table-bordered table-hover table-checkable order-column">
                                <thead> 
                                    <tr >
                                        <th style="min-width: 60px;">Med-Id</th>
                                        <th>UserName</th>
                                        <th>Provider Name</th>
                                        <th>Appointment-Type</th>
                                        <th>Appointment-Date</th>
                                        <th>Appointment-Time</th>
                                        <th>Paid-Amount</th>
                                        <th>Payment-Mode</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                </tbody>
                            </table>
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


<script>
    var datatable;
    $(document).ready(function () {
    datatable = $('#user_payment').DataTable({

            "processing": true, //Feature control the processing indicator.
            "serverSide": true, //Feature control DataTables' server-side processing mode.
            "order": [], //Initial no order.
            "scrollX":false,
            "autoWidth": false,
            "pageLength": 50, // Set Page Length
            "lengthMenu":[[5, 25, 50, 100, - 1], [5, 25, 50, 100, "All"]],
            // Load data for the table's content from an Ajax source
            "ajax": {
            "url": site_url + "admin/payment/user_payment/getdata",
                    "type": "POST",
                    //Custom Post
                    // "data": {"filter_data":v}

            },
            //Set column definition initialisation properties.
            "columnDefs": [
                    //{
                    //"targets": [0,1,2,3,4,5,6,7,8,9,10,11,12,13], //first, Fourth, seventh column
                    //"orderable": false //set not orderable,

                    //},
            ],
            "fnInitComplete": function (oSettings, response) {

            $("#countData").text(response.recordsTotal);
            }

    });
    });
    $('#user_payment').wrap("<div class='scrolledTable' style='overflow-y: auto; clear:both;'></div>");
    $('body').on('change', "#user_block", function() {
    var user_id;
    var status;
    if ($(this).prop('checked') != true){
    // when user not blocked
    //user_id   = $(this).attr("data-id");
    status = 0;
    } else{
    //user_id = $(this).attr("data-id");
    status = 1;
    }
    user_id = $(this).attr("data-id");
    update_user_status(user_id, status);
    });
    function update_user_status(user_id, status) {
    $.ajax({
    url : site_url + "admin/users/manage_users/update_insurance_action_status",
            cache: false,
            type: "POST",
            processData :true,
            data: {user_id : user_id, status:status},
            success : function(data) {
            var response = JSON.parse(data);
            if (response.active) {
            alert(response.active);
            } else {
            alert(response.unactive);
            }
            }
    });
    }
</script>



