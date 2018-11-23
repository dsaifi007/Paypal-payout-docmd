<link href="<?php echo base_url(); ?>assets/assets/select2/css/select2.css" rel="stylesheet" type="text/css" />
<link href="<?php echo base_url(); ?>assets/assets/select2/css/select2-bootstrap.min.css" rel="stylesheet" type="text/css" />


<!-- start page content -->
<div class="page-content-wrapper">
    <div class="page-content">
        <div class="page-bar">
            <div class="page-title-breadcrumb">
                <div class="pull-left">
                    <div class="page-title"><?php echo $this->lang->line("edit_title"); ?></div>
                </div>
                <ol class="breadcrumb page-breadcrumb pull-right">
                    <li><i class="fa fa-home"></i>&nbsp;<a class="parent-item" href="<?php echo base_url(); ?>dashboard"><?php echo $this->lang->line("home"); ?></a>&nbsp;<i class="fa fa-angle-right"></i></li>

                    <li>&nbsp;<a class="parent-item" href="<?php echo base_url(); ?>admin/other_information/treatment_controller"><?php echo $this->lang->line("manage_other_information"); ?></a>&nbsp;<i class="fa fa-angle-right"></i>
                    </li>
                    <li class="active"><?php echo $this->lang->line("edit_title"); ?></li>
                </ol>
            </div>
        </div>
        <!-- add content here -->
        <div class="row">
            <div class="col-md-10 col-xs-10 user-details">
                <div class="white-box">
                    <div class="card-body " id="bar-parent6">
                        <!-- .row -->
                        <?php echo display_message_info([1 => $success, 2 => @$error, 3 => validation_errors()]); ?>
                        <?php
                        $form_attr = ["id" => "add_treatment", "class" => "form-horizontal"];
                        echo form_open_multipart("admin/other_information/treatment_controller/edit_treatment_info/" . $items->id, $form_attr);
                        ?>
                        <div class="row text-center m-t-10">
                            <div class="col-md-12">
                                <?php
                                $input_eng_name = [
                                    "name" => "title",
                                    "class" => "form-control"
                                    ,
                                    'value' => set_value('name', $items->title)
                                ];
                                echo form_input($input_eng_name);
                                ?> 



                                <?php
                                $input_add_info = ["name" => "description",
                                    "class" => "form-control-textarea m-t-20",
                                    "placeholder" => "Enter Additional Information",
                                    "rows" => "5", "aria-invalid" => "false"
                                    ,
                                    'value' => set_value('additional_info', $items->description)
                                ];
                                echo form_textarea($input_add_info);
                                ?>                              
                            </div>
                            <div class="col-md-12 m-t-20 m-b-20" >

                                <?php
                                $input_eng_name = ["name" => "title_spn",
                                    "class" => "form-control",
                                    'value' => set_value('spn_name', $items->title_spn)
                                ];
                                echo form_input($input_eng_name);
                                ?>


                                <?php
                                $input_add_info = ["name" => "description_spn",
                                    "class" => "form-control-textarea m-t-20",
                                    "placeholder" => "Enter Additional Information",
                                    "rows" => "5", "aria-invalid" => "false"
                                    ,
                                    'value' => set_value('spn_additional_info', $items->description_spn)
                                ];
                                echo form_textarea($input_add_info);
                                ?>


                            </div>
                            <div class="col-lg-12 col-md-12">
                                <?php if (count($symptoms_list) > 0) { ?>
                                    <select id="multiple" name="treatment_plan_id[]" class="form-control select2-multiple col-lg-12 col-md-12" multiple>
                                        <?php
                                        foreach ($symptoms_list as $key => $value) {
                                            $s_id = get_symptoms_id(["treatment_id" => $items->id, "symptom_id" => $value['id']]);
                                            ?>
                                            <option value="<?php echo $value['id']; ?>" <?php echo (isset($s_id['symptom_id'])) ? "selected" : ''; ?>><?php echo $value['name']; ?></option>
                                            <?php
                                        }
                                        ?>
                                    </select>                         
                                    <?php
                                } else {
                                    echo "No data found ";
                                }
                                ?>

                            </div><hr>
                        </div>
                        <!-- /.row -->


                        <!-- .row -->
                        <div class="row text-center">
                            <div class="col-md-12">
                                <?php echo form_submit("treatment_submit", "Submit", ["class" => "btn btn-info"]); ?>

                            </div>
                        </div>
                    </div>
                </div>
                <?php echo form_close(); ?>
            </div>
        </div>
    </div>
</div>
<script>
    $("document").ready(function () {
        $("input[type='search']").attr("placeholder", "Select treatment Provider Plan");
    });
</script>
<script src="<?php echo base_url(); ?>assets/assets/select2/js/select2.js" ></script>
<script src="<?php echo base_url(); ?>assets/assets/select2/js/select2-init.js" ></script>

