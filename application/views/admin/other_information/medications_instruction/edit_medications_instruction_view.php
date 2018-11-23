

<!-- start page content -->
<div class="page-content-wrapper">
    <div class="page-content">
        <div class="page-bar">
            <div class="page-title-breadcrumb">
                <div class="pull-left">
                    <div class="page-title"><?php echo $this->lang->line("edit_title"); ?></div>
                </div>
                <ol class="breadcrumb page-breadcrumb pull-right">
                    <li><i class="fa fa-home"></i>&nbsp;<a class="parent-item" href="#"><?php echo $this->lang->line("manage_other_information"); ?></a>&nbsp;<i class="fa fa-angle-right"></i>
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
                        $form_attr = ["id" => "add_medications_instruction", "class" => "form-horizontal" ];
                        echo form_open_multipart("admin/other_information/medications_instruction_controller/edit_medications_instruction_info/".$items->id, $form_attr);
                        ?>
                        <div class="row text-center m-t-10">
                             <div class="col-md-12">
                                <?php
                                $input_eng_name = [
                                    "name" => "name",
                                    "class" => "form-control"
                                    ,
                  'value'=>set_value('name',$items->name)
                                    ];
                                echo form_input($input_eng_name);
                                ?> 

                                

                                <?php
                                $input_add_info = ["name" => "additional_info",
                                    "class" => "form-control-textarea m-t-20",
                                    "placeholder" => "Enter Additional Information", 
                                    "rows" => "5", "aria-invalid" => "false"
                                    ,
                'value'=>set_value('additional_info',$items->additional_info)
                                    ];
                                echo form_textarea($input_add_info);
                                ?>                              
                            </div>
                          <div class="col-md-12 m-t-20 m-b-20" >

                                <?php
                                $input_eng_name = ["name" => "sp_name",
                                    "class" => "form-control",
                                    'value'=>set_value('spn_name',$items->sp_name)
                                    ];
                                echo form_input($input_eng_name);
                                ?>

                              
                                <?php
                                $input_add_info = ["name" => "sp_additional_info",
                                    "class" => "form-control-textarea m-t-20", 
                                    "placeholder" => "Enter Additional Information",
                                    "rows" => "5", "aria-invalid" => "false"
                                    ,
                'value'=>set_value('sp_additional_info',$items->sp_additional_info)
                                    ];
                                echo form_textarea($input_add_info);
                                ?>


                            </div>
                       
                        </div>
                        <!-- /.row -->
                        <hr>
                        <!-- .row -->
                        <div class="row text-center">
                            <div class="col-md-12">
                                <?php echo form_submit("medications_instruction_submit", "Submit", ["class" => "btn btn-info"]); ?>

                            </div>
                        </div>
                    </div>
                </div>
                <?php echo form_close(); ?>
            </div>
        </div>
    </div>
</div>


