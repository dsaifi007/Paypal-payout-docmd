<?php
if(isset($filetration))
{
     $data = json_encode($filetration);
}
?>
<style>
    
    table.dataTable thead>tr>th.sorting {min-width: 167px;}
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
              <div class="tools">
                <a class="fa fa-repeat btn-color box-refresh" href="javascript:;"></a>
                <a class="t-collapse btn-color fa fa-chevron-down" href="javascript:;"></a>
                <a class="t-close btn-color fa fa-times" href="javascript:;"></a>
              </div>
            </div>
            <div class="card-body">
             <?php 
             echo display_message_info([1=>$success,2=>$error,3=>validation_errors()]);?>
             <div class="row">
              <div class="col-md-4">
                <div class="btn-group pull-left">
                 
              </div>
            </div>
            <div class="col-md-4"></div>
            <div class="col-md-4">
              <div class="btn-group pull-right">
                <button type="button" class="btn btn-round btn-success" data-toggle="modal" data-target="#filter"><?php echo $this->lang->line("filter_btn"); ?><i class="fa fa-filter"></i></button>
            <a href="<?php echo base_url(); ?>admin/pharmacies/pharmacies_controller/add_pharmacy_info" class="btn btn-info  btn-sm" > <?php echo $this->lang->line("add_new_btn"); ?>
                  <i class="fa fa-envelope"></i>
                </a>
              </div>
            </div>
          </div>


        <table  id="pharmacy_list" class="table table-striped table-bordered">
          <thead> 
            <tr>
              <th  style="min-width:14px"><?php echo $this->lang->line("sr_n"); ?></th>
              <th><?php echo $this->lang->line("name_lbl"); ?></th>
               <th><?php echo $this->lang->line("phone_lbl"); ?></th>
              <th><?php echo $this->lang->line("city_lbl"); ?></th>
             
              
              <th><?php echo $this->lang->line("state_lbl"); ?></th>
              <th><?php echo $this->lang->line("zip_lbl"); ?></th>
              <th style="min-width:144px"><?php echo $this->lang->line("action"); ?></th>
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

<script>
var datatable;
var filterdata ='';
filterdata ='<?php echo (isset($data) && $data!='')? $data : ''; ?>';
$(document).ready(function () {
    datatable = $('#pharmacy_list').DataTable({     
        "processing": true, //Feature control the processing indicator.
        "serverSide": true, //Feature control DataTables' server-side processing mode.
        "order": [], //Initial no order.
        "pageLength": 50, // Set Page Length
        "lengthMenu": [[5, 25, 50, 100, -1], [5, 25, 50, 100, "All"]],
        // Load data for the table's content from an Ajax source
        "ajax": {
            "url": site_url + "admin/pharmacies/pharmacies_controller/getdata",
            "type": "POST",
            //Custom Post
            "data": {"filter_data":filterdata}

        },
        //Set column definition initialisation properties.
        "columnDefs": [
            {
               "targets": [6], //first, Fourth, seventh column
                "orderable": false //set not orderable
            }
        ],
        "fnInitComplete": function (oSettings, response) {

            $("#countData").text(response.recordsTotal);
        }

    });
});
$('#pharmacy_list').wrap("<div class='scrolledTable' style='overflow-y: auto; clear:both;'></div>");

</script>

  <?php
  //dd($filtering_data);
  // if (count($filtering_data)>0) {
  //  $data = json_encode($filtering_data);	
  // }
  ?>
