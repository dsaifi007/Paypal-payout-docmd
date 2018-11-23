			<!-- start page content -->
            <div class="page-content-wrapper">
                <div class="page-content">
                    <div class="page-bar">
                        <div class="page-title-breadcrumb">
                            <div class=" pull-left">
                                <div class="page-title">
                                    <?php echo $this->lang->line("page_title"); ?></div>
                                </div>
                            <?php echo breadcrumb($this->lang->line("page_title")); ?>
                            </div>
                        </div>
                        <!-- add content here -->
                        <div class="row">
                            <div class="col-md-12">
                                <div class="card card-topline-aqua change-password">
                                    <div class="card-body no-padding height-9">
                                        <?php 
                                        echo display_message_info([1=>$success,2=>$error,3=>validation_errors()]);
                                        echo form_open('admin/login/change_password/formsubmitted' , ["id"=>"change_pass"]); ?>
                                        
                                            <?php
                                            echo form_input_wrapper("password","current_passsword","current_passsword",$this->lang->line("current_passsword"),'current_passsword');
                                            echo form_input_wrapper("password","password","password",$this->lang->line("password"),'password');
                                            echo form_input_wrapper("password","passconf","passconf",$this->lang->line("passconf"),'passconf');
                                            ?>                                 
                                        <div class="text-center">
                                            <?php echo form_submit('submit', 'Submit',["class" =>"btn btn-primary"]); ?>
                                        </div>
                                        <?php echo form_close(); ?>                                   
                                        <!-- END SIDEBAR BUTTONS -->
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <!-- end page content -->