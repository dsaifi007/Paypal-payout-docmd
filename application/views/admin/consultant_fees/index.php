<div class="page-content-wrapper">
    <div class="page-content">
        <div class="page-bar">
            <div class="page-title-breadcrumb">
                <div class="pull-left">
                    <div class="page-title"><?php echo $this->lang->line("page_title"); ?></div>
                </div>
                <ol class="breadcrumb page-breadcrumb pull-right">
                    <li><i class="fa fa-home"></i>&nbsp;<a class="parent-item" href="<?php echo base_url(); ?>dashboard"><?php echo $this->lang->line("home") ?></a>&nbsp;<i class="fa fa-angle-right"></i>
                    </li>
                    <li class="active"><?php echo $this->lang->line("list"); ?></li>
                </ol>
            </div>
        </div>
        <!-- add content here -->
        <div class="row">
            <div class="col-sm-10 user-details">
                <div class="card-box">
                    <?php echo display_message_info([1 => @$success, 2 => @$error, 3 => validation_errors()]); ?>

                    <div class="card-body  row p-t-20">
                        <div class="col-lg-4 text-center"><b><?php echo $this->lang->line("type"); ?></b></div><div class="col-lg-4 text-bold text-center"><b><?php echo $this->lang->line("fee"); ?></b></div>
                        <div class="col-lg-4 text-bold text-center"><b><?php //echo $this->lang->line("action"); ?></b></div>
<!--                        <form action="<?" class="row" method="POST" id="form1">-->
                        <?php echo form_open("admin/consultant_fees/consultant_fees_controller/form_submited", ['class' => 'row']); ?>
                        <?php
                        if (!empty($items)) {
                            foreach ($items as $key => $value) {
                                ?>

                                <div class="col-lg-4 p-t-20 text-center"> 
                                    <!-- Basic Chip -->
                                    <?php echo $value['title']; ?>
                                </div>
                                <div class="col-lg-4 p-t-20 text-center">
                                    <!-- Deletable Chip -->
                                    <span class="mdl-chipd">
                                        <span class="mdl-chip__text">
                                            <input type="text" name="title_<?php echo $key; ?>"  disabled="disabled" value="<?php echo $value['amount'] ?>" data="<?php echo $value['amount'] ?>"   required="required">
                                        </span>
                                        <input type="hidden" name='id_<?php echo $key; ?>' value="<?php echo strtolower($value['id']); ?>">
                                    </span>
                                </div>
                                <div class="col-lg-4 p-t-20 text-center">
                                    <!-- Button Chip -->
                                    <?php if((int)$key == 1) {?>
                                    <a href="#"  style="margin-top:-171px;" class="btn btn-primary btn-xs edit" id="edit">	<i class="fa fa-pencil"></i>
                                    </a>
                                    <?php }?>
                                </div>

                                <?php
                            }
                        } else {
                            echo "No data found";
                        }
                        ?>
                        <div class="row text-center p-b-20 p-t-20">
                            <div class="col-md-12">
                                <input type="submit" id="submit" style="display:none;margin-left: 360px;"  class="btn btn-circle btn-primary save" value="Save Change">
                            </div>
                        </div>
                        </form>
                    </div>

                </div>
            </div>
        </div>

    </div>
</div>
<script>
    var form_data = new Array();
    var input = '';
    $("document").ready(function () {
        $("a.edit").click(function (e) {
            e.preventDefault();
            $("input").removeAttr("disabled");
            $(".save").show();
            $(".submit").show();
        });

//        $(".submit").click(function (e) {
//            //var id= $         
//           e.preventDefault();
//           var plan_id = $(this).attr("value");
//           var data = $(this).attr("data");
//               
//                $.ajax({
//                    url:'<?php //echo base_url(); ?>admin/consultant_fees/consultant_fees_controller/test',
//                    method:"POST",
//                    cache:false,
//                    data:{"id":plan_id,"amount":data},
//                    success:function(res){
//                        alert(res);
//                    }
//                });
//        });

    });
</script>