

<link rel="stylesheet" href="<?php echo base_url(); ?>assets/admin/css/common/model-2.css"/>
<link rel="stylesheet" href="<?php echo base_url(); ?>assets/admin/css/common/inbox.min.css"/>
<!-- start page content -->
<style>
    .compose-mail input, .compose-mail input:focus{
        border:  1px solid #eaebee; 
        width: 100%;
    }
    .compose-mail .form-group {
        border: none;
        display: inline-block;
        width: 100%;
        margin-bottom: 0;
    }
    .compose-mail .form-group label{
        width: auto; 
        background:transparent;
        padding-left: 0px;
    }
 
</style>
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

                        </div>
                        <div class="card-body">
                            <?php echo display_message_info([1 => $success, 2 => $error, 3 => validation_errors()]); ?>
                            <div class="row">
                                <div class="col-md-4">
                                </div>
                                <div class="col-md-4"></div>
                                <div class="col-md-4">
                                    <div class="btn-group pull-right">

                                    </div>
                                </div>
                            </div>


                            <table  id="symptoms_list" class="table table-striped table-bordered">
                                <thead> 
                                    <tr>
                                        <th><?php echo $this->lang->line("sr_n"); ?></th>
                                        <th><?php echo $this->lang->line("email_event"); ?></th>
                                        <th><?php echo $this->lang->line("user_type"); ?></th>
                                        <th><?php echo $this->lang->line("subject"); ?></th>
                                        <th><?php echo $this->lang->line("message"); ?></th>
                                        <th><?php echo $this->lang->line("action"); ?></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    if (count($items) > 0) {
                                        foreach ($items as $key => $item) {
                                            ?>
                                            <tr>
                                                <td><?php echo $item['id']; ?></td>
                                                <td><?php echo $item['email_event']; ?></td> 
                                                <td><?php echo $item['type']; ?></td> 
                                                <td ><?php echo $item['subject']; ?></td> 
                                                <td><?php echo substr($item['message'], 0, 14) . "..."; ?></td> 
                                                <td>
                                                    <a href="<?php echo base_url();?>admin/email_template/emails_controller/email_auto_edit/<?php echo $item['id'];?>" class="btn btn-primary btn-xs"><i class="fa fa-pencil"></i></a>
                                                </td> 
                                            </tr> 
                                            <?php
                                        }
                                    }
                                    ?>
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



<script>
//document.getElementById("file-upload").onchange = function() {
//  document.getElementById("uploadFile").value = this.value;
//};
    var datatable;
    $(document).ready(function () {
        $('#symptoms_list').DataTable({
            "pageLength": 10, // Set Page Length
            "lengthMenu": [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]],
            "order": [], //Initial no order.
            "columnDefs": [
                {
                    "className": "dt-center",
                    "targets": [0, 1, 2, 3, 4, 5], //first, Fourth, seventh column
                    "orderable": false //set not orderable
                },
            ],

        });
        
        
         function readURL() {
            var $input = $(this);
            if (this.files && this.files[0]) {
                var reader = new FileReader();
                reader.onload = function (e) {
                    reset($input.next('.delbtn'), true);
                    $input.next('.portimg').attr('src', e.target.result).show();
                    $input.after('<input type="button" class="delbtn removebtn" style="width:100px" value="Remove">');
                }
                reader.readAsDataURL(this.files[0]);
            }
        }
        $(".fileUpload").change(readURL);
        $("form").on('click', '.delbtn', function (e) {
            reset($(this));
        });

        function reset(elm, prserveFileName) {
            if (elm && elm.length > 0) {
                var $input = elm;
                $input.next('.portimg').attr('src', '').hide();
                if (!prserveFileName) {
                    $input.prev('.fileUpload').val("");
                }
                elm.remove();
            }
        }
    });

    /*
     Work -- this  function is used for the validtion symptoms 
     */
    $("document").ready(function () {
        $("#edit_aduto_mactic_email").validate({
            // errorPlacement: function(error, element) {
            //  error.appendTo(element.closest('.form-group').after());
            // },
            rules: {
                subject: {
                    required: true,
                    minlength: 5,
                    maxlength: 45
                },
                message: {
                    required: true,
                    minlength: 5
                },

                submitHandler: function (form) {
                    form.submit();
                }
            }
        });

    });
</script>