<?php
//if(isset($id)){
//    dd($id);
//}
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

                            <div clas="row">
                                <div class="col-md-4">
                                    <?php echo form_open("admin/rating/rating_controller/index"); ?>
                                    <select class="form-control valid" name="id" onchange="this.form.submit()" aria-invalid="false">
                                        <option value="1" >User</option>
                                        <option value="2" <?php echo (@$id == "2") ? "selected" : ''; ?>>Doctor</option>

                                    </select>
                                    <?php echo form_close(); ?>
                                </div>
                            </div>
                            <table  id="rating_list" class="table table-striped table-bordered">
                                <thead> 
                                    <tr>
                                        <th><?php echo $this->lang->line("med_id_lbl"); ?></th>
                                        <th><?php
                                            if (@$id == 2) {
                                                echo $this->lang->line("doctor_lbl");
                                            } else {
                                                echo $this->lang->line("user_lbl");
                                            }
                                            ?></th>
                                        <th><?php echo $this->lang->line("email_lbl"); ?></th>
                                        <th><?php echo $this->lang->line("phone_lbl"); ?></th>


                                        <th><?php echo $this->lang->line("gender_lbl"); ?></th>
                                        <th><?php echo $this->lang->line("dob_lbl"); ?></th>
                                        <th><?php echo $this->lang->line("rating_lbl"); ?></th>
                                        <th><?php echo $this->lang->line("action"); ?></th>

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
<div class="modal fade" id="myModal" role="dialog">
    <div class="modal-dialog">

        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <div class="content">
                    <div class="col-md-12">
                        <?php echo form_open("admin/rating/rating_controller/update_avg_rating", ["class" => "avg_rating"]); ?>

                        <div class="form-group row">
                            <label class="control-label col-md-7" style="font-size: 20px;">Edit the Average Rating</label>
                            <div class="col-md-3">
                                <input type="text" name="avg_rating" data-required="1" class="form-control">
                                <input type="hidden" name="user_id" value=''  >
                                <input type="hidden" name="doctor_id" value=''  >
                            </div>
                            <div class="col-md-2" style="font-size: 20px;"></div>
                        </div>

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
<script>
    var datatable;
    //var filterdata = "";
    var id = '<?php echo (isset($id) && $id != '') ? $id : ''; ?>';
    $(document).ready(function () {
        datatable = $('#rating_list').DataTable({

            "processing": true, //Feature control the processing indicator.
            "serverSide": true, //Feature control DataTables' server-side processing mode.
            "order": [], //Initial no order.
            "pageLength": 50, // Set Page Length
            "lengthMenu": [[5, 25, 50, 100, -1], [5, 25, 50, 100, "All"]],
            // Load data for the table's content from an Ajax source
            "ajax": {
                "url": site_url + "admin/rating/rating_controller/getdata",
                "type": "POST",
                //Custom Post
                "data": {"id": id}

            },
            //Set column definition initialisation properties.
            "columnDefs": [
                {
                    "targets": [0, 2, 3, 4, 6, 7], //first, Fourth, seventh column
                    "orderable": false //set not orderable
                }
            ],
            "fnInitComplete": function (oSettings, response) {

                $("#countData").text(response.recordsTotal);
            }

        });

    });
    $("body").on("click", "#edit_doctor_rating", function () {
        //e.prevenDefault();
        var rating = $(this).attr("rating");
        var user_id = $(this).attr("user-id");
        var doctor_id = $(this).attr("doctor-id");

        $("input[name='avg_rating']").val(rating);
        $("input[name='user_id']").val(user_id);
        $("input[name='doctor_id']").val(doctor_id);
    })
    $("document").ready(function () {
        $(".avg_rating").validate({
            // errorPlacement: function(error, element) {
            //  error.appendTo(element.closest('.form-group').after());
            // },
            rules: {
                avg_rating: {
                    required: true,
                    minlength: 1
                },
                user_id: {
                    required: true,
                    minlength: 1
                },
                doctor_id: {
                    required: true,
                    minlength: 1
                },
                submitHandler: function (form) {
                    form.submit();
                }
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
