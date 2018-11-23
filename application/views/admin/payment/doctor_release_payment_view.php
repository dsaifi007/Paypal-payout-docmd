<style>
       th.sorting_disabled {padding-right: 15px; min-width: 140px;}
        table.dataTable thead>tr>th.sorting {min-width: 180px;}
</style>
<?php
?>
<!-- start page content -->
<div class="page-content-wrapper">
    <div class="page-content">
        <div class="page-bar">
            <div class="page-title-breadcrumb">
                <div class=" pull-left">
                    <div class="page-title"><?php echo $this->lang->line("release_page_title"); ?></div>
                </div>
                <ol class="breadcrumb page-breadcrumb pull-right">
                    <li><i class="fa fa-home"></i>&nbsp;<a class="parent-item" href="<?php echo base_url(); ?>dashboard"><?php echo $this->lang->line("home"); ?></a>&nbsp;<i class="fa fa-angle-right"></i></li>
                    <li class="active"><?php echo $this->lang->line("release_page_title"); ?></li>
                </ol>
            </div>
        </div>
        <!-- add content here -->
        <div class="row">
            <div class="col-md-12">
                <div class="manage-users">
                    <div class="card card-topline-aqua">
                        <div class="card-head">
                            <header><?php echo $this->lang->line("release_list"); ?></header>
                            <div class="tools">
                                <a class="fa fa-repeat btn-color box-refresh" href="javascript:;"></a>
                                <a class="t-collapse btn-color fa fa-chevron-down" href="javascript:;"></a>
                                <a class="t-close btn-color fa fa-times" href="javascript:;"></a>
                            </div>
                        </div>
                        <div class="card-body">


                            <div class="row">
                                <div class="col-md-12 col-sm-3 col-xs-3">
                                    <ul class="nav nav-tabs">
                                        <li class="nav-item active">
                                            <a href="<?php echo base_url(); ?>admin/payment/doctor_payment/index"  class=""> Pending Payment </a>
                                        </li> 
                                        <li class="nav-item ">
                                            <a href="#tab_6_1" data-toggle="tab" class="active show">  Released Payment</a>
                                        </li>

                                    </ul>
                                </div>
                                <div class="col-md-12 col-sm-12 col-xs-12">
                                    <div class="tab-content">
                                        <div class="tab-pane fade active show" id="tab_6_2">
                                            <?php echo display_message_info([1 => $success, 2 => $error, 3 => validation_errors()]); ?>
                                            <table  id="doctor_payment"  style="width:100%"  class=" s table table-striped table-bordered table-hover table-checkable order-column">
                                                <thead> 
                                                    <tr >
                                                        <th><?php echo $this->lang->line("p_med_id"); ?></th>
                                                        <th><?php echo $this->lang->line("first_name"); ?></th>
                                                        <th><?php echo $this->lang->line("last_name"); ?></th>
                                                        <th><?php echo $this->lang->line("total_completed_appt"); ?></th>
                                                        <th><?php echo $this->lang->line("total_release_payment"); ?></th>
                                                        <th><?php echo $this->lang->line("pay_receiving"); ?></th>
                                                        
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                </tbody>
                                            </table>

                                        </div>
                                        <div class="tab-pane" id="tab_6_1">
                                            <p>Lorem ipsum dolor sit amet, sumo impetus ea sit, ut pri mucius eruditi dolorum. Wisi liberavisse theophrastus mea cu, id enim elit erroribus nec. Et ridens fuisset volumus mel. Te duo prompta lucilius suavitate, viderer ocurreret ea ius.</p>
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
</div>

<div class="modal fade" id="payModal" role="dialog">
    <div class="modal-dialog">
      <!-- Modal content-->
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal">&times;</button>
          <h4 class="modal-title">Modal Header</h4>
        </div>
        <div class="modal-body">
          <p>Some text in the modal.</p>
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
        datatable = $('#doctor_payment').DataTable({

            "processing": true, //Feature control the processing indicator.
            "serverSide": true, //Feature control DataTables' server-side processing mode.
            "order": [], //Initial no order.
            "scrollX": false,
            "autoWidth": false,
            "pageLength": 50, // Set Page Length
            "lengthMenu": [[5, 25, 50, 100, -1], [5, 25, 50, 100, "All"]],
            // Load data for the table's content from an Ajax source
            "ajax": {
                "url": site_url + "admin/payment/doctor_release_payment/getdata",
                "type": "POST",
                //Custom Post
                "data": {"filter_data": "1"}

            },
            //Set column definition initialisation properties.
            "columnDefs": [
                {
                    "targets": [3, 4, 5], //first, Fourth, seventh column
                    "orderable": false //set not orderable,
                },
            ],
            "fnInitComplete": function (oSettings, response) {
                $("#countData").text(response.recordsTotal);
            }

        });
    });
    $('#doctor_payment').wrap("<div class='scrolledTable' style='overflow-y: auto; clear:both;'></div>");

    $('body').on('change', "#user_block", function () {
        var user_id;
        var status;
        if ($(this).prop('checked') != true) {
            // when user not blocked
            //user_id   = $(this).attr("data-id");
            status = 0;
        } else {
            //user_id = $(this).attr("data-id");
            status = 1;
        }
        user_id = $(this).attr("data-id");
        update_user_status(user_id, status);
    });
    function update_user_status(user_id, status) {
        $.ajax({
            url: site_url + "admin/users/manage_users/update_insurance_action_status",
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
</script>



