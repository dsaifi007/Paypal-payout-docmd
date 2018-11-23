

<div class="page-content-wrapper">
    <div class="page-content">
        <div class="page-bar">
            <div class="page-title-breadcrumb">
                <div class="pull-left">
                    <div class="page-title">Manage In-App Content</div>
                </div>
                <ol class="breadcrumb page-breadcrumb pull-right">
                    <li><i class="fa fa-home"></i>&nbsp;<a class="parent-item" href="<?php echo base_url();?>dashboard">Home</a>&nbsp;<i class="fa fa-angle-right"></i>
                    </li>
                    <li class="active">Manage In-App Content</li>
                </ol>
            </div>
        </div>
        <!-- add content here -->
        <div class="row">
            <div class="col-md-10 col-xs-10 user-details">
                <div class="white-box">
                    <div class="card-body " id="bar-parent6">
                        <?php echo display_message_info([1 => @$success, 2 => @$error, 3 => validation_errors()]); ?>

                        <?php echo form_open("admin/manage_app_content/manage_appcontent_controller/faq_submited/1", ['class' => "add_faq"]); ?>
                        <!-- .row -->
                        <div class="row text-center m-t-10">
                            <div class="col-md-6">
                                <?php
                                if (count($faq_cat)) {
                                    ?>
                                    <select class="form-control" name="faq_cat_id">
                                        <?php
                                        foreach ($faq_cat as $value) {
                                            ?>
                                            <option value="<?php echo $value['id']; ?>"><?php echo $value['category']; ?></option>
                                        <?php } ?>
                                    </select>
                                <?php } ?>
                            </div>
                            <div class="mdl-textfield mdl-js-textfield mdl-textfield--floating-label txt-full-width is-upgraded" data-upgraded=",MaterialTextfield">
                                <?php
                                $ques_eng = [
                                    "class" => "form-control-textarea m-t-20",
                                    "name" => "question",
                                    "value" => '',
                                    "placeholder" => "Enter Question (English)",
                                    "rows" => "4",
                                    "aria-invalid" => "false"
                                ];
                                echo form_textarea($ques_eng);
                                ?>

                            </div>
                            <div class="mdl-textfield mdl-js-textfield mdl-textfield--floating-label txt-full-width is-upgraded" data-upgraded=",MaterialTextfield">
                                <?php
                                $ques_eng = [
                                    "class" => "form-control-textarea m-t-20",
                                    "name" => "answer",
                                    "value" => '',
                                    "placeholder" => "Enter Answer (English)",
                                    "rows" => "4",
                                    "aria-invalid" => "false"
                                ];
                                echo form_textarea($ques_eng);
                                ?>
                            </div>
                            <div class="mdl-textfield mdl-js-textfield mdl-textfield--floating-label txt-full-width is-upgraded" data-upgraded=",MaterialTextfield">
                                <?php
                                $ques_eng = [
                                    "class" => "form-control-textarea m-t-20",
                                    "style" => "width:100%",
                                    "name" => "sp_question",
                                    "value" => '',
                                    "placeholder" => "Enter Question (Spanish)",
                                    "rows" => "4",
                                    "aria-invalid" => "false"
                                ];
                                echo form_textarea($ques_eng);
                                ?>
                            </div>
                            <div class="mdl-textfield mdl-js-textfield mdl-textfield--floating-label txt-full-width is-upgraded" data-upgraded=",MaterialTextfield">

                                <?php
                                $ques_eng = [
                                    "class" => "form-control-textarea m-t-20",
                                    "name" => "sp_answer",
                                    "value" => '',
                                    "placeholder" => "Enter Answer (Spanish)",
                                    "rows" => "4",
                                    "aria-invalid" => "false"
                                ];
                                echo form_textarea($ques_eng);
                                ?>
                            </div>
                        </div>
                        <!-- /.row -->
                        <hr>
                        <!-- .row -->
                        <div class="row text-center">
                            <div class="col-md-12">
                                <input type="submit" name="faq_save" class="btn btn-circle btn-primary" value="Submit">
                            </div>
                        </div>
                        <?php echo form_close(); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    $("document").ready(function () {
        $(".add_faq").validate({
            // errorPlacement: function(error, element) {
            //  error.appendTo(element.closest('.form-group').after());
            // },
            rules: {
                question: {
                    required: true,
                    minlength: 2
                },
                answer: {
                    required: true,
                    minlength: 2
                },
                sp_question: {
                    required: true,
                    minlength: 2
                },
                sp_answer: {
                    required: true,
                    minlength: 2
                },
                submitHandler: function (form) {
                    form.submit();
                }
            }
        });
    });
    
</script>