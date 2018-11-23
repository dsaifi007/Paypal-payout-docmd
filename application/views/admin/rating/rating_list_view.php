
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
              <header><?php 
              if(isset($doctor_id)){
               echo $this->lang->line("list_doctor"); 
              }else{
                   echo $this->lang->line("list"); 
              }
              ?></header>
             
            </div>
            <div class="card-body">
             <?php 
             echo display_message_info([1=>$success,2=>$error,3=>validation_errors()]);?>
             


        <table  id="rating_list_list" class="table table-striped table-bordered">
          <thead> 
            <tr>
              <th><?php echo $this->lang->line("sr_lbl"); ?></th>
              <th><?php 
              if(isset($doctor_id)){
                  echo $this->lang->line("user_lbl");
              }
              else{
              echo $this->lang->line("doctor_lbl");
              }
              ?></th>
               <th><?php echo $this->lang->line("rating_date_lbl"); ?></th>
              <th><?php echo $this->lang->line("rating_lbl"); ?></th>         
              <th><?php echo $this->lang->line("rating_review_lbl"); ?></th>
             
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


<script>
var datatable;
var userid ='<?php echo $user_id; ?>';
var doctorid ='<?php echo $doctor_id; ?>';
$(document).ready(function () {
    datatable = $('#rating_list_list').DataTable({
        
        "processing": true, //Feature control the processing indicator.
        "serverSide": true, //Feature control DataTables' server-side processing mode.
        "order": [], //Initial no order.
        "pageLength": 5, // Set Page Length
        "lengthMenu": [[5, 25, 50, 100, -1], [5, 25, 50, 100, "All"]],
        // Load data for the table's content from an Ajax source
        "ajax": {
            "url": site_url + "admin/rating/rating_list_controller/getdata",
            "type": "POST",
            //Custom Post
            "data": {"user_id":userid,"doctor_id":doctorid}

        },
        //Set column definition initialisation properties.
        "columnDefs": [
            {
                "targets": [0, 2,3,4], //first, Fourth, seventh column
                "orderable": false //set not orderable
            }
        ],
        "fnInitComplete": function (oSettings, response) {

            $("#countData").text(response.recordsTotal);
        }

    });
});
</script>

  <?php
  //dd($filtering_data);
  // if (count($filtering_data)>0) {
  //  $data = json_encode($filtering_data);	
  // }
  ?>
