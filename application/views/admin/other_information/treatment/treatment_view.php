<style>
    
 
</style>
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
              
            </div>
            <div class="card-body">
             <?php 
             echo display_message_info([1=>$success,2=>$error,3=>validation_errors()]);?>
             <div class="row">
              <div class="col-md-4">
            </div>
            <div class="col-md-4"></div>
            <div class="col-md-4">
              <div class="btn-group pull-right">
            <a href="<?php echo base_url(); ?>admin/other_information/treatment_controller/add_treatment_info" class="btn btn-success" > <?php echo $this->lang->line("add_new_btn"); ?>
            </a>
              </div>
            </div>
          </div>


        <table  id="treatment_list" class="table table-bordered">
          <thead> 
            <tr>
              <th><?php echo $this->lang->line("sr_n"); ?></th>
              <th><?php echo $this->lang->line("name"); ?></th>
               <th><?php echo $this->lang->line("desc"); ?></th>
                <th><?php echo $this->lang->line("title_spn"); ?></th>
                 <th><?php echo $this->lang->line("description_spn"); ?></th>
                 <th><?php echo $this->lang->line("recommended"); ?></th>
              <th><?php echo $this->lang->line("action"); ?></th>
            </tr>
          </thead>
          <tbody>
            <?php
              if (count($items)>0) {
                  $sr=1;
                foreach ($items as $key => $item) {
                ?>
                  <tr>
                   <td><?php echo $sr; ?></td>
                   <td><?php echo $item['title']; ?></td> 
                   <td><?php echo $item['description']; ?></td> 
                   <td><?php echo $item['title_spn']; ?></td> 
                   <td><?php echo $item['description_spn']; ?></td> 
                   <td>
                       <input type="radio" class="rcmd" name="rcmd" id="<?php echo $item['id'] ?>" <?php echo ($item['is_recommended']==1)?"checked":'' ?> >
                   </td> 
                   <td>
                       <a href="<?php echo base_url('admin/other_information/treatment_controller/edit_treatment_info/'.$item['id'])?>"  class="btn btn-primary btn-xs"><i class="fa fa-pencil"></i></a>
                       <a href="<?php echo base_url('admin/other_information/treatment_controller/delete/'.$item['id'])?>"  class="btn btn-danger btn-xs" onclick="return confirm('Are you sure you want to delete this record')"><i class="fa fa-trash-o"></i></a>
                       <a href="#" data-toggle="popover" title="" data-content="<?php echo ($item['symptoms'])?$item['symptoms']:"No Symptoms Found";?> ">Link</a>
                   </td> 
                   </tr> 
                <?php
                $sr++;
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
$(document).ready(function(){
    $('[data-toggle="popover"]').popover();   
});
    $("body").on("click", ".rcmd", function () {
        var id = $(this).attr("id");      
        update_rcmd_status(id);
    });
    function update_rcmd_status(id) {
        //alert(id);
        $.ajax({
            url: site_url + "admin/other_information/treatment_controller/update_rcmd_status",
            cache: false,
            type: "POST",
            processData: true,
            data: {id: id},
            success: function (data) {
                var msg = JSON.parse(data);
                alert(msg.message);
                //$(".patient_list").html(data);
            }
        });
    }

</script>