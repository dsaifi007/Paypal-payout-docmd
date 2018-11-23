<style>
    .nav-tabs .nav-item{
        width: 33%;
        text-align: center;
        border: 1px solid #e9e9e9;
    }
    .nav-tabs .nav-item>a{
        font-size:18px;
    }
    .tab-heading{
        padding: 10px;
        border-bottom: 1px solid #eee;
        font-weight: 600;
    }
    .custom-tab .nav-tabs .nav-item {
    width: 25%;
}
</style>
<?php
//dd($items);
?>
<div class="page-content-wrapper">
    <div class="page-content">
        <div class="page-bar">
            <div class="page-title-breadcrumb">
                <div class=" pull-left">
                    <div class="page-title"><?php echo $this->lang->line("page_title"); ?></div>
                </div>
                <ol class="breadcrumb page-breadcrumb pull-right">
                    <li><i class="fa fa-home"></i>&nbsp;<a class="parent-item" href="<?php echo base_url();?>dashboard"><?php echo $this->lang->line("home"); ?></a>&nbsp;<i class="fa fa-angle-right"></i>
                    </li>
                    <li class="active"><?php echo $this->lang->line("page_title"); ?></li>
                </ol>
            </div>
        </div>
        <!-- add content here -->
        <div class="row">
            <div class="col-md-12">
                <div class="panel tab-border card-topline-green tab-50">
                    <header class="panel-heading panel-heading-gray custom-tab ">
                        <ul class="nav nav-tabs">
                            <li class="nav-item"><a href="<?php echo base_url();?>admin/manage_app_content/manage_appcontent_controller" ><?php echo $this->lang->line("term_condition"); ?></a>
                            </li>
                           <li class="nav-item"><a href="<?php echo base_url();?>admin/manage_app_content/manage_appcontent_controller/faq_display/4" class="active show">Consent to care</a>
                            </li>
                            <li class="nav-item"><a href="<?php echo base_url();?>admin/manage_app_content/manage_appcontent_controller/faq_display" class=""><?php echo $this->lang->line("faq"); ?></a>
                            </li>
<li class="nav-item"><a href="<?php echo base_url(); ?>admin/manage_app_content/manage_appcontent_controller/faq_display/5" class="">Categories List</a>
                            </li>
                        </ul>
                    </header>
                    <div class="panel-body">
                        <div class="tab-content">
                            <div class="col-lg-12">

                                <?php 
                                //dd($items);
                                echo display_message_info([1 => @$success, 2 => @$error, 3 => validation_errors()]); ?>
                                <div class="panel">

                                    <div class="panel-heading tab-heading">Consent To Care
                                        (English)
                                        <a href="#" class="btn btn-primary btn-xs pull-right"  id="edit_eng">	<i class="fa fa-pencil"></i> Edit</a></div>
                                    <div class="panel-body">
                                        <?php echo form_open("admin/manage_app_content/manage_appcontent_controller/consent_submit",['class'=>'form1']); ?>
                                        
                                        <?php
                                        $data = [
                                            "class" => "consent_eng_text",
                                            "style" => "width:100%",
                                            "disabled" => "disabled",
                                            "name" => "consent_eng_text",
                                            "value" => $items['consent_eng_text']
                                        ];
                                        echo form_textarea($data);
                                        ?>
                                        <input type="submit" style="display:none" class="btn btn-primary des_submit" name="save" value="submit">
                                        <?php echo form_close(); ?>
                                    </div>
                                </div>
                                <div class="panel">

                                    <div class="panel-heading tab-heading">Consent To Care (Spanish)
                                        <a href="#" class="btn btn-primary btn-xs pull-right" id="edit_spn">	<i class="fa fa-pencil"></i> Edit</a></div>
                                    <div class="panel-body">
                                        <?php echo form_open("admin/manage_app_content/manage_appcontent_controller/consent_submit",['class'=>'form2']); ?>
                                        <?php
                                        $data = [
                                            "class" => "consent_spn_text",
                                            "style" => "width:100%",
                                            "disabled" => "disabled",
                                            "name" => "consent_spn_text",
                                            "value" => $items['consent_spn_text']
                                        ];
                                        echo form_textarea($data);
                                        ?>
                                        <input type="submit" style="display:none" class="btn btn-primary sp_des_submit" name="save" value="submit">
                                        <?php echo form_close(); ?>                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    $("document").ready(function () {
        $("#edit_eng").click(function (e) {
            e.preventDefault();
            $(".consent_eng_text").removeAttr("disabled");
            $(".des_submit").show();
        });
        $("#edit_spn").click(function (e) {
            e.preventDefault();
            $(".consent_spn_text").removeAttr("disabled");
            $(".sp_des_submit").show();
        });
    });
   $("document").ready(function() {
    $(".form1").validate({
      // errorPlacement: function(error, element) {
      //  error.appendTo(element.closest('.form-group').after());
      // },
      rules: {
        consent_eng_text: {
          required: true,
          minlength: 5
        },
        submitHandler: function(form) {
          form.submit();
        }
      }
    });
  });
   $("document").ready(function() {
    $(".form2").validate({
      // errorPlacement: function(error, element) {
      //  error.appendTo(element.closest('.form-group').after());
      // },
      rules: {
        consent_spn_text: {
          required: true,
          minlength: 5
        },
        submitHandler: function(form) {
          form.submit();
        }
      }
    });
  });
</script>