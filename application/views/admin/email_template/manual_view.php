<link href="<?php echo base_url(); ?>assets/assets/summernote/summernote.css" rel="stylesheet">
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
    #button{
        position: relative;
        left:-1px;
        background-color:#446655;
    } 
    .custom-file-upload {
        border: 1px solid #ccc;
        display: inline-block;
        padding: 6px 12px;
        cursor: pointer; 
    }
</style>
<div class="page-content-wrapper">
    <div class="page-content">
        <div class="page-bar">
            <div class="page-title-breadcrumb">
                <div class=" pull-left">
                    <div class="page-title"><?php echo $this->lang->line("manual_page_title"); ?></div>
                </div>
                <ol class="breadcrumb page-breadcrumb pull-right">
                    <li><i class="fa fa-home"></i>&nbsp;<a class="parent-item" href="<?php echo base_url(); ?>dashboard"><?php echo $this->lang->line("home"); ?></a>&nbsp;<i class="fa fa-angle-right"></i></li>
                    <li class="active"><?php echo $this->lang->line("manual_page_title"); ?></li>
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
                                <div class="col-md-12">
                                    <div class="row">
                                        <div class="col-md-5"></div>
                                        <div class="col-md-3">

                                            <?php echo form_open("admin/email_template/emails_controller/manual_email_template"); ?>
                                            <select class="form-control" name="filter" onchange="this.form.submit();">
                                                <option  value="accept" <?php echo (isset($filter['filter']) && $filter['filter'] === "accept") ? "selected" : ''; ?>>Pending Provider Accept</option>
                                                <option value="reject" <?php echo (isset($filter['filter']) && $filter['filter'] === "reject") ? "selected" : ''; ?>>Pending Provider Reject</option>
                                                <option value="other_emails" <?php echo (isset($filter['filter']) && $filter['filter'] === "other_emails") ? "selected" : ''; ?> >Other Emails</option>
                                            </select>
                                            <?php echo form_close(); ?>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="btn-group pull-right">
                                                <button  data-toggle="modal" data-target="#manual-email"  class="btn btn-success">Add New Email Template <i class="fa fa-plus-circle" aria-hidden="true"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>


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

                                        <th><?php echo $this->lang->line("subject"); ?></th>
                                        <th><?php echo $this->lang->line("message"); ?></th>
                                        <th><?php echo $this->lang->line("attachment"); ?></th>
                                        <th><?php echo $this->lang->line("action"); ?></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    //dd($items);
                                    if (count($items) > 0) {
                                        foreach ($items as $key => $item) {
                                            ?>
                                            <tr>
                                                <td><?php echo $item['id']; ?></td>

                                                <td ><?php echo $item['subject']; ?></td> 
                                                <td><?php echo $item['message']; ?></td> 
                                                <td ><?php echo ($item['file_name']) ? $item['file_name'] : ''; ?></td> 
                                                <td>
                                                    <a href="<?php echo base_url("admin/email_template/emails_controller/email_edit/" . $item['id']); ?>"    class="btn btn-primary btn-xs"><i class="fa fa-pencil"></i></a>
                                                    <a href="<?php echo base_url("admin/email_template/emails_controller/delete/" . $item['id']); ?>" onclick="return confirm('Are you sure you want to delete this record?');"   class="btn btn-primary btn-xs"><i class="fa fa-trash-o "></i></a>
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

<div class="modal fade" id="manual-email" role="dialog" style="background: rgba(0, 0, 0, 0.7);">
    <div class="modal-dialog modal-lg" style="min-width:1226px;">

        <!-- Modal content-->
        <div class="modal-content " >
            <div class="content">
                <?php echo form_open_multipart("admin/email_template/emails_controller/add_manual_email_info", ["id" => "add_manual_email"]); ?>
                <div class="col-md-12 p-t-20">

                    <div class="row  m-t-10 compose-mail">
                        <h4 class="text-center ">Add/Edit Automatic Email</h4>
                        <div class="col-lg-12 col-md-12 col-xs-12 b-r m-t-20">
                            <div class="form-group">
                                <?php
                                echo form_input(["id" => "subject", "placeholder" => "Enter Subject", "value" => set_value("message", ""), "name" => "subject", "class" => "form-control"]);
                                echo form_hidden("id", "");
                                ?>

                            </div>
                        </div>
                        <div class="col-lg-12 col-md-12 col-xs-12 b-r m-t-20">
                            <div class="form-group">
                                <?php
                                $textarea = [
                                    "id" => "message",
                                    "name" => "message",
                                    "placeholder" => "Enter Message Text",
                                    "value" => set_value("message", ""),
                                    "class" => "form-control-textarea",
                                    "rows" => "5",
                                    "aria-invalid" => "false"
                                ];
                                echo form_textarea($textarea);
                                ?>
                            </div>
                        </div>
                        <div class="col-lg-12 col-md-12 col-xs-12 b-r m-t-20 type_hide">
                            <div class="form-group">
                                <select class="form-control" name="type" >
                                    <option  value="accept"  >Pending Provider Accept</option>
                                    <option value="reject"  >Pending Provider Reject</option>
                                    <option value="other_emails" >Other Emails</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-lg-12 col-md-12 col-xs-12 b-r m-t-20">
                            <div class="form-group">
                                <label for="file-upload" class="custom-file-upload">
                                    &nbsp;<i class="fa fa-paperclip"></i> Attachment
                                </label>
                                <p id="fl"></p>
                                <input id="file-upload" name='email_attechment' value='' type="file" class="fileUpload"  style="display:none;">

                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-12 p-t-20 text-center">
                    <input type="submit" name="save" value="Sumbit" class="mdl-button mdl-js-button mdl-button--raised mdl-js-ripple-effect m-b-10 m-r-20 btn-pink" data-upgraded=",MaterialButton,MaterialRipple">
                </div>
                <?php echo form_close(); ?>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo $this->lang->line("close"); ?></button>
            </div>
        </div>

    </div>
</div>



<script>

    var datatable;
    $(document).ready(function () {
        $('#symptoms_list').DataTable({
            "pageLength": 10, // Set Page Length
            "lengthMenu": [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]],
            "order": [], //Initial no order.
            "columnDefs": [
                {
                    "className": "dt-center",
                    "targets": [0, 1, 2, 3], //first, Fourth, seventh column
                    "orderable": false //set not orderable
                },
            ],

        });
        $("#file-upload").change(function () {
            var a = $("#file-upload").val();
            $("p").text(a);
            $("#fl").show();
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
            $("#fl").hide();
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
                    minlength: 2,
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
    $("document").ready(function () {
        $("#add_manual_email").validate({
            // errorPlacement: function(error, element) {
            //  error.appendTo(element.closest('.form-group').after());
            // },
            rules: {
                subject: {
                    required: true,
                    minlength: 2,
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
<script src="<?php echo base_url() ?>assets/assets/summernote/summernote.js" ></script>
<script >
    $(document).ready(function () {
        $('#message').summernote();
    });
</script>