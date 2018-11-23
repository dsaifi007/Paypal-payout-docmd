
<!-- start page content -->
<div class="page-content-wrapper">
    <div class="page-content">
        <div class="page-bar">
            <div class="page-title-breadcrumb">
                <div class="pull-left">
                    <div class="page-title"><?php echo $this->lang->line("add_title"); ?></div>
                </div>
                <ol class="breadcrumb page-breadcrumb pull-right">
                     <li><i class="fa fa-home"></i>&nbsp;<a class="parent-item" href="<?php echo base_url(); ?>dashboard"><?php echo $this->lang->line("home"); ?></a>&nbsp;<i class="fa fa-angle-right"></i>
                    </li>
                    <li>&nbsp;<a class="parent-item" href="<?php echo base_url(); ?>admin/other_information/symptoms_controller"><?php echo $this->lang->line("manage_other_information"); ?></a>&nbsp;<i class="fa fa-angle-right"></i>
                    </li>
                    <li class="active"><?php echo $this->lang->line("add_title"); ?></li>
                </ol>
            </div>
        </div>
        <!-- add content here -->
        <div class="row">
            <div class="col-md-10 col-xs-10 user-details">
                <div class="white-box">
                    <div class="card-body " id="bar-parent6">
                        <!-- .row -->
                        <?php echo display_message_info([1 => @$success, 2 => @$error, 3 => validation_errors()]); ?>
                        <?php
                        $form_attr = ["id" => "add_symptoms", "class" => "form-horizontal" ];
                        echo form_open_multipart("admin/other_information/symptoms_controller/add_symptoms_info", $form_attr);
                        ?>
                        <div class="row text-center m-t-10">
                             <div class="col-md-12">
                                <?php
                                $input_eng_name = ["name" => "name","placeholder"=>"Enter symptoms name(English)" , "class" => "form-control"];
                                echo form_input($input_eng_name);
                                ?> 

                                

                                <?php
                                $input_add_info = ["name" => "additional_info", "class" => "form-control-textarea m-t-20", "placeholder" => "Enter Additional Information", "rows" => "5", "aria-invalid" => "false"];
                                echo form_textarea($input_add_info);
                                ?>                              
                            </div>
                                                       <div class="col-md-12 m-t-20 m-b-20" >


                                <?php
                                $input_eng_name = ["name" => "sp_name","placeholder"=>"Enter symptoms name(Spanish)" ,  "class" => "form-control"];
                                echo form_input($input_eng_name);
                                ?>

                               
                                <?php
                                $input_add_info = ["name" => "sp_additional_info", "class" => "form-control-textarea m-t-20", "placeholder" => "Enter Additional Information", "rows" => "5", "aria-invalid" => "false"];
                                echo form_textarea($input_add_info);
                                ?>


                            </div>
                            <div class="col-md-5 col-sm-5 m-t-10">	<strong><i class="fa fa-id-card-o" aria-hidden="true"></i>  Upload CSV file : </strong>
                            </div>
                            <div class="col-md-7 col-sm-7">
                                <input type="file" name="symptoms_csv" id="file-7" class="inputfile inputfile-6  text-center" data-multiple-caption="{count} files selected" multiple style="display:none" />
                                <label for="file-7">&nbsp;<span></span>  <strong> <i class="fa fa-cloud-upload" aria-hidden="true"></i> Browse</strong>
                                </label>
                            </div>
                        </div>
                        <!-- /.row -->
                        <hr>
                        <!-- .row -->
                        <div class="row text-center">
                            <div class="col-md-12">
                                <?php echo form_submit("symptoms_submit", "Submit", ["class" => "btn btn-info"]); ?>

                            </div>
                        </div>
                    </div>
                </div>
                <?php echo form_close(); ?>
            </div>
        </div>
    </div>
</div>