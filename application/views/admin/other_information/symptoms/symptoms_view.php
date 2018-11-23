
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
            <a href="<?php echo base_url(); ?>admin/other_information/symptoms_controller/add_symptoms_info" class="btn btn-success" > <?php echo $this->lang->line("add_new_btn"); ?>
            </a>
              </div>
            </div>
          </div>


        <table  id="symptoms_list" class="table table-striped table-bordered">
          <thead> 
            <tr>
              <th><?php echo $this->lang->line("sr_n"); ?></th>
              <th>Symptom Name(English)</th>
               <th><?php echo $this->lang->line("sympt_desc"); ?></th>
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
                   <td><?php echo $item['name']; ?></td> 
                   <td><?php echo $item['visit_instruction']; ?></td> 
                   <td>
                       <a href="<?php echo base_url('admin/other_information/symptoms_controller/edit_symptoms_info/'.$item['id'])?>"  class="btn btn-primary btn-xs"><i class="fa fa-pencil"></i></a>
                       <a href="<?php echo base_url('admin/other_information/symptoms_controller/delete/'.$item['id'])?>"  class="btn btn-danger btn-xs" onclick="return confirm('Are you sure you want to delete this record')"><i class="fa fa-trash-o"></i></a>
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

<div class="modal fade" id="filter" role="dialog">
  <div class="modal-dialog">

    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-body">
        <div class="card card-box">
          <div class="card-head">
            <header><?php echo $this->lang->line("filter_by"); ?></header>
          </div>
          <div class="card-body " id="bar-parent">
            <form action="<?php echo base_url(); ?>admin/pharmacies/pharmacies_controller/index" method="POST" name="filter_data" id="filter_data">
              <div class="form-group row">
                <label class="col-md-4 control-label"><?php echo $this->lang->line("state"); ?></label>
                <div class="input-group col-md-8">
                  <?php if (count($state)>0){
                   ?>
                   <select name="state" class="form-control" id="state">
                    <option value=''>Select State</option>
                    <?php
                    foreach ($state as $key => $value) {
                      ?>
                      <option value="<?php echo trim($value['state']); ?>"><?php echo $value['state']; ?></option>
                      <?php } ?>
                    </select>
                    <?php } ?>
                  </div>
                </div>
                <div class="form-group row">
                  <label class="col-md-4 control-label"><?php echo $this->lang->line("city"); ?></label>
                  <div class="input-group col-md-8">
                   <?php if (count($state)>0){
                     ?>
                     <select name="city" class="form-control" id="city">
                      <option value=''>Select city</option>
                      <?php
                      foreach ($city as $key => $value) {
                        ?>
                        <option value="<?php echo trim($value['city']); ?>"><?php echo $value['city']; ?></option>
                        <?php } ?>
                      </select>
                      <?php } ?>
                    </div>
                  </div>
                  <button type="submit" class="btn btn-round btn-success"><?php echo $this->lang->line("success"); ?></button>
                </form>
              </div>
            </div>  
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo $this->lang->line("close"); ?></button>
          </div>
        </div>

      </div>
    </div>



  <?php
  //dd($filtering_data);
  // if (count($filtering_data)>0) {
  //  $data = json_encode($filtering_data);	
  // }
  ?>
