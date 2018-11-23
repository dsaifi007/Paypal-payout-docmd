<style>
    .label-sm{
        cursor:pointer
    }
    .select2.select2-container {
        width:100% !important;
    }
</style>
<link href="<?php echo base_url(); ?>assets/assets/select2/css/select2.css" rel="stylesheet" type="text/css" />
<link href="<?php echo base_url(); ?>assets/assets/select2/css/select2-bootstrap.min.css" rel="stylesheet" type="text/css" />

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
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="btn-group pull-left">

                                    </div>
                                </div>
                                <div class="col-md-4"></div>
                                <div class="col-md-4">
                                    <div class="btn-group pull-right">
                                        <button type="button" class="btn btn-round btn-success" data-toggle="modal" data-target="#add_new"><?php echo $this->lang->line("add_new_btn"); ?></button>

                                        </a>
                                    </div>
                                </div>
                            </div>


                            <table  id="promocode_list" class="table table-striped table-bordered">
                                <thead> 
                                    <tr>
                                        <th><?php echo $this->lang->line("sr_n"); ?></th>
                                        <th><?php echo $this->lang->line("name_lbl"); ?></th>
                                        <th><?php echo $this->lang->line("code_lbl"); ?></th>
                                        <th><?php echo $this->lang->line("discount_lbl"); ?></th>


                                        <th><?php echo $this->lang->line("expiry_lbl"); ?></th>
                                        <th><?php echo $this->lang->line("description_lbl"); ?></th>
                                        <th><?php echo $this->lang->line("action"); ?></th>
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






<!-- end page container -->

<div class="modal fade" id="add_new" style="background:rgba(0, 0, 0, 0.7);" role="dialog">
    <div class="modal-dialog modal-lg">

        <!-- Modal content-->
        <div class="modal-content ">
            <div class="modal-body ">
               <div class="content">
    <h4 class="text-center "><?php echo $this->lang->line("add_new_promocode"); ?></h4>
    <div class="col-md-12 p-t-20">
        <?php echo form_open("admin/promocode/promocode_controller/index", ["class" => "promocode_add"]); ?>

        <div class="row  m-t-10">
            <div class="col-lg-6 col-md-12 col-xs-12 b-r m-t-20">
                <div class="input-group">
                    <span class="input-group-addon">
                        <i class="fa fa-product-hunt" aria-hidden="true"></i>
                    </span>
                    <?php
                    $name = ["name" => "name", "class" => "form-control", "id" => "name", "placeholder" => $this->lang->line("promocode_name"), 'value' => set_value('promocode_name')];
                    echo form_input($name);
                    ?> </div>
                </div>
                <div class="col-lg-6 col-md-12 col-xs-12 b-r m-t-20">
                    <div class="input-group">
                        <span class="input-group-addon">
                            <i class="fa fa-code" aria-hidden="true"></i>

                        </span>
                        <?php
                        $code = ["name" => "code", "class" => "form-control", "id" => "code", "placeholder" => $this->lang->line("enter_promocode"), 'value' => set_value('code')];
                        echo form_input($code);
                        ?> </div>
                    </div>
                    <div class="col-lg-6 col-md-12 col-xs-12 b-r m-t-20">
                        <div class="input-group">
                            <span class="input-group-addon">
                                <i class="fa fa-percent" aria-hidden="true"></i>

                            </span>
                            <?php
                            $discount = ["name" => "discount", "class" => "form-control", "id" => "discount", "placeholder" => $this->lang->line("enter_discount"), 'value' => set_value('discount')];
                            echo form_input($discount);
                            ?> </div>
                        </div>
                        <div class="col-lg-6 col-md-12 col-xs-12 b-r m-t-20">
                            <div class="input-group">
                                <span class="input-group-addon">
                                    <i class="fa fa-calendar" aria-hidden="true"></i>


                                </span>
                                <?php
                                $expiry = ["name" => "expiry", "type" => "date", "class" => "form-control", "id" => "expiry", "placeholder" => $this->lang->line("enter_expiry"), 'value' => set_value('expiry')];
                                echo form_input($expiry);
                                ?></div>




                            </div>
                            <div class="col-lg-12 col-md-12 col-xs-12 b-r m-t-20">

                                <?php
                                $description = ["name" => "description", "class" => "form-control-textarea", "id" => "description", "placeholder" => $this->lang->line("enter_description"), 'value' => set_value('description')];
                                echo form_textarea($description);
                                echo form_hidden("edit_id", "");
                                ?>


                            </div>

                        </div>
                    </div>
                    <div class="col-lg-12 p-t-20 text-center">
                        <button type="submit"  class="btn btn-round btn-success"><?php echo $this->lang->line("submit"); ?></button>
                    </div>
                </div>
                
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo $this->lang->line("close"); ?></button>
                                </div>
                                </div>
    </form>
                                </div>
                                </div>


                                <div id="send" class="modal fade" role="dialog">
                                    <div class="modal-dialog">

                                        <!-- Modal content-->
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <button type="button" class="close" data-dismiss="modal">&times;</button>
                                            </div>
                                            <div class="modal-body">
                                                <div class="popup">
                                                    <div class="content">
                                                        <?php echo form_open("admin/promocode/promocode_controller/get_email_information", ["id" => "send_email"]); ?>

                                                        <h4 class="text-center ">Send To</h4>
                                                        <div class="col-md-8 p-t-20">
                                                            <div class="p-t-20 p-b-20">
                                                                <label>
                                                                    <input type="radio" class="user-1" name="user_list"  value="1"> &emsp;All Users</label>
                                                            </div>
                                                            <div class="p-b-20">
                                                                <label>
                                                                    <input type="radio" class="user-2" name="user_list" value="2">&emsp;New Users</label>
                                                            </div>
                                                            <div class="p-b-20">
                                                                <label>
                                                                    <input type="radio"  class="user-3"  name="user_list" value="3">&emsp;Existing Users</label>
                                                            </div>
                                                        </div>                       
                                                        <?php echo form_hidden("send_id",""); ?>
                                                        <div class="col-md-12 p-t-20">
                                                            <div class="row user_list" style="display:none">
                                                                <label class="col-lg-12 col-md-12 control-label">Existing Users
                                                                </label>
                                                                <div class="col-lg-12 col-md-12">
                                                                    <?php if (count($existing_users_list) > 0 && !empty($existing_users_list)) {
                                                                        ?>
                                                                        <select id="multiple" name="user_ids[]" class="form-control select2-multiple col-lg-12 col-md-12" multiple>

                                                                            <?php
                                                                            foreach ($existing_users_list as $k => $v) {
                                                                                ?>
                                                                                <option value="<?php echo $v['email']; ?>"><?php echo ucfirst($v['user_name']); ?></option>
                                                                            <?php }
                                                                            ?>
                                                                        </select>

                                                                    <?php }
                                                                    ?>


                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-lg-12 p-t-20 text-center">
                                                            <input type="submit" value="Submit" name="save"  class="mdl-button mdl-js-button mdl-button--raised mdl-js-ripple-effect m-b-10 m-r-20 btn-pink">
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
                                <!--select2-->
                                <script src="<?php echo base_url(); ?>assets/assets/select2/js/select2.js" ></script>
                                <script src="<?php echo base_url(); ?>assets/assets/select2/js/select2-init.js" ></script>


                                <script>
                                    $("input[type='radio']").click(function () {
                                        var checked = $(this).val();
                                        //alert(checked);
                                        if (checked == 3) {
                                            $(".user_list").show();
                                        } else {
                                            $(".user_list").hide();
                                        }
                                    });

                                    $("document").ready(function () {
                                        $(".promocode_add").validate({
                                            // errorPlacement: function(error, element) {
                                            //  error.appendTo(element.closest('.form-group').after());
                                            // },
                                            rules: {
                                                name: {
                                                    required: true,
                                                    minlength: 4,
                                                    maxlength: 30
                                                },
                                                code: {
                                                    required: true,
                                                    minlength: 2,
                                                    maxlength: 24
                                                },
                                                discount: {
                                                    required: true,
                                                    minlength: 2,
                                                    maxlength: 4
                                                },
                                                expiry: {
                                                    required: true,
                                                    minlength: 5
                                                },
                                                description: {
                                                    required: true,
                                                    minlength: 5
                                                },
                                                messages: {
                                                    name: {
                                                        required: "Please enter a name",
                                                        minlength: "Your Subject must consist of at least 6 characters"
                                                    },
                                                    code: {
                                                        required: "Please provide a code",
                                                        minlength: "Your message must be at least 5 characters long"
                                                    }
                                                },
                                                submitHandler: function (form) {
                                                    form.submit();
                                                }
                                            }
                                        });



                                    });
                                    $("document").ready(function () {
                                        $("#send_email").validate({
                                            // errorPlacement: function(error, element) {
                                            //  error.appendTo(element.closest('.form-group').after());
                                            // },
                                            rules: {
                                                user_list: {
                                                    required: true,
                                                },
                                                submitHandler: function (form) {
                                                    form.submit();
                                                }
                                            }
                                        });



                                    });


                                    var datatable;
                                    $(document).ready(function () {
                                        datatable = $('#promocode_list').DataTable({

                                            "processing": true, //Feature control the processing indicator.
                                            "serverSide": true, //Feature control DataTables' server-side processing mode.
                                            "order": [], //Initial no order.
                                            "pageLength": 5, // Set Page Length
                                            "lengthMenu": [[5, 25, 50, 100, -1], [5, 25, 50, 100, "All"]],
                                            // Load data for the table's content from an Ajax source
                                            "ajax": {
                                                "url": site_url + "admin/promocode/promocode_controller/getdata",
                                                "type": "POST",
                                                //Custom Post


                                            },
                                            //Set column definition initialisation properties.
                                            "columnDefs": [
                                                {
                                                    "targets": [0, 1, 2, 3, 4, 5, 6], //first, Fourth, seventh column
                                                    "orderable": false //set not orderable
                                                }
                                            ],
                                            "fnInitComplete": function (oSettings, response) {

                                                $("#countData").text(response.recordsTotal);
                                            }

                                        });
                                    });

                                    $('body').on('click', ".edit_promocode", function () {
                                        var edit_id = $(this).attr("edit-id");
                                        get_promocode_input_value(edit_id);
                                    });
                                    function get_promocode_input_value(edit_id) {
                                        $.ajax({
                                            url: site_url + "admin/promocode/promocode_controller/get_input_values",
                                            cache: false,
                                            type: "POST",
                                            processData: true,
                                            data: {id: edit_id},
                                            success: function (data) {
                                                var obj = JSON.parse(data);

                                                if (obj) {
                                                    $("input[name='name']").val(obj.name);
                                                    $("input[name='code']").val(obj.code);
                                                    $("input[name='discount']").val(obj.discount);
                                                    $("input[name='expiry']").val(obj.expiry);
                                                    $("#description").val(obj.description);
                                                    $("input[name='edit_id']").val(obj.id);
                                                } else {
                                                    alert("No information is available at this time");
                                                    return false;
                                                }
                                            }
                                        });
                                    }
$("body").on("click",".row-id",function(){
    var row_id  = $(this).attr("send-id");
    $("input[name='send_id']").val(row_id);
});
var today = new Date().toISOString().split('T')[0];
document.getElementsByName("expiry")[0].setAttribute('min', today);
                                </script>
