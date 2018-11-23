<!-- start page container -->


<!-- end sidebar menu -->
<!-- start page content -->
<div class="page-content-wrapper">
  <div class="page-content">
    <div class="page-bar">
      <div class="page-title-breadcrumb">
        <div class=" pull-left">
          <div class="page-title">Manage Users</div>
        </div>
        <ol class="breadcrumb page-breadcrumb pull-right">
          <li><i class="fa fa-home"></i>&nbsp;<a class="parent-item" href="index.html">Home</a>&nbsp;<i class="fa fa-angle-right"></i></li>
          <li class="active">Manage Users</li>
        </ol>
      </div>
    </div>
    <!-- add content here -->
    <div class="row">
      <div class="col-md-12">
        <div class="manage-users">
          <div class="card card-topline-aqua">
            <div class="card-head">
              <header>List Of Users</header>
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
                <div class="col-md-12">
                  <div class="btn-group pull-right">
                    <button type="button" class="btn btn-round btn-success" data-toggle="modal" data-target="#filter">Filter<i class="fa fa-filter"></i></button>
                    <button class="btn btn-info  btn-sm" id="send_button" data-toggle="modal" data-target="#myModal" disabled = "disabled" > Send Mail
                      <i class="fa fa-envelope"></i>
                    </button>

                  </div>
                </div>
              </div>
              <input type='hidden' id='myDivv' value='' />
              <?php echo form_open("admin/users/users/send_email_to_users",["class"=>"users_email_validation","id"=>"frm-example","onsubmit"=>"return tsextareavldt()"]);?>
              <!-- <input type="checkbox" name="selectall" id="selectall" value='0' style='align:right'>Select All -->
              <!-- Modal -->
              <div class="modal fade" id="myModal" role="dialog">
                <div class="modal-dialog">

                  <!-- Modal content-->
                  <div class="modal-content">
                    <div class="modal-body">
                      <div class="card card-box">
                        <div class="card-head">
                          <header>Compose Mail</header>
                        </div>
                        <div class="card-body " id="bar-parent">

                         <div class="form-group">
                          <label for="simpleFormEmail">Subject</label>
                          <input type="text" name="subject" class="form-control" id="subject" placeholder="Enter Subject">
                        </div>
                        <div class="form-group">
                          <label for="simpleFormPassword">Message</label>
                          <textarea name="message" id="message" class="form-control required" placeholder="Enter Email text" required>
                          </textarea>
                        </div>
                      </div>
                    </div>  
                  </div>
                  <button type="submit"  class="btn btn-primary submit-btn">Submit</button>
                  <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                  </div>
                </div>

              </div>
            </div>

            <table id="table" class="display" style="width:100%;">
              <thead>
                <tr>
                  <tr>
                    <th><input type="checkbox" name="selectall" id="selectall" value='0' style='align:right'>Select All</th>
                    <th>Id</th>
                    <th>Name</th>
                    <th>Gender</th>
                    <th>DOB</th>
                    <th>Email</th>
                    <th>Phone</th>
                    <th>Action</th>
                  </tr>
                </tr>
              </thead>
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
                          <header>Filtered By</header>
                        </div>
                        <div class="card-body " id="bar-parent">
                          <form action="" method="GET" name="filter_data" id="filter_data">
                          <div class="form-group row">
                              <label class="col-md-4 control-label">Gender</label>
                              <div class="input-group col-md-8">
                                <div class="radio">
                                  <input id="male" name="gender" type="radio" value='male'  />
                                  <label style="line-height: normal;" for="male">Male</label>
                                </div> 
                                <div class="radio">
                                  <input id="female" name="gender" type="radio" value='female' >
                                  <label for="female">Female</label>
                                </div>
                              </div>
                          </div>
                          <div class="form-group row">
                              <label class="col-md-4 control-label">State</label>
                              <div class="input-group col-md-8">
                                <select name="state" class="form-control" id="state">
                                  <option value=''>Select State</option>
                                  <option value="Utter Predesh">State1</option>
                                  <option value="utter predesh">State2</option>
                                </select>
                              </div>
                          </div>
                          <div class="form-group row">
                              <label class="col-md-4 control-label">City</label>
                              <div class="input-group col-md-8">
                                <select name="city" class="form-control" id="city">
                                  <option value=''>Select city</option>
                                  <option value="gzb">City1</option>
                                  <option value="meerut">City2</option>
                                </select>
                              </div>
                          </div>
                          <div class="radio radio-aqua">
                            <input id="health_insurance" name="health_insurance" value='health_insurance' type="radio" >
                            <label for="health_insurance">
                              Health Insurance
                            </label>
                          </div>

                          <button type="submit" class="btn btn-round btn-success">Success</button>
                        </form>
                      </div>
                    </div>  
                  </div>
                  <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                  </div>
                </div>

              </div>
            </div>