


<style>
    .bg-grey {
     color: #0a0a0a;
    background-color: #ececec;
    }
    .info-box-icon.push-bottom {
        margin-bottom: 12px;
    }
    .push-bottom{
        position: relative;
        bottom: 13px;
    }
	.material-icons {
		    line-height: 2;
	}
    .active_frame{
       box-shadow: 0 0 14px rgba(0,0,0,0.6);
    display: block;
    border: 2px solid #fff;
    cursor: not-allowed;
    background: #ff5870;
    color: #fff;
    }
    .raphael-group-23-creditgroup{
        display:none;
    }
    tspan{}
</style>
<script src="http://static.fusioncharts.com/code/latest/fusioncharts.js" type="text/javascript" ></script>
<script src="http://static.fusioncharts.com/code/latest/fusioncharts.charts.js" type="text/javascript" ></script>
<script src="http://static.fusioncharts.com/code/latest/themes/fusioncharts.theme.zune.js" type="text/javascript" ></script>

<script>
    $("document").ready(function(){
    FusionCharts.ready(function () {
        new FusionCharts({
            type: "column2d",
            renderAt: "chart-container",
            width: "1050px",
            height: "500px",
            dataFormat: "json",
            dataSource:<?php echo json_encode($chart_data) ?>
        }).render();
    });     
});
</script>
<div class="page-content-wrapper">
    <div class="page-content">
        <!-- add content here -->
        <div class="row">
            <div class="col-md-12">
                <!--                <div class="pfanel">
                
                                    <div class="alert alert-info">
                                        <strong>Warning!</strong> We Will lancuh DOCMD admin panel very soon, Thank you for patience and Support.... 
                                    </div>
                                </div>-->
            </div>
        </div>
        <!-- add content here -->
        <div class="state-overview">
            <div class="row">
                <div class="col-xl-4 col-md-6 col-12">
                    <a href="<?php echo base_url(); ?>dashboard" >
                        <div class="info-box bg-grey <?php echo (@$active == '') ? "active_frame" : ""; ?>"  >  <span class="info-box-icon push-bottom"><i class="material-icons">group</i></span>
                            <div class="info-box-content"> <span class="info-box-text">Total Number of Users</span>
                                <span class="info-box-number"><?php echo $items['users']; ?></span>
                            </div>
                            <!-- /.info-box-content -->
                        </div>
                    </a>
                    <!-- /.info-box -->
                </div>
                <!-- /.col -->
                <div class="col-xl-4 col-md-6 col-12">
                    <a href="<?php echo base_url(); ?>dashboard/1">
                        <div class="info-box bg-grey <?php echo (@$active == 1) ? "active_frame" : ""; ?>"> <span class="info-box-icon push-bottom"><i class="material-icons">person</i></span>
                            <div class="info-box-content"> <span class="info-box-text">Total Number of Providers</span>
                                <span class="info-box-number" ><?php echo $items['doctors']; ?></span>
                            </div>
                        </div>
                    </a>
                </div>
                <!-- /.col -->
                <div class="col-xl-4 col-md-6 col-12">
                    <a href="<?php echo base_url(); ?>dashboard/2">                   
                        <div class="info-box bg-grey <?php echo (@$active == 2) ? "active_frame" : ""; ?>"> <span class="info-box-icon push-bottom"><i class="material-icons">monetization_on</i></span>
                            <div class="info-box-content"> <span class="info-box-text">Total Earnings</span>
                                <span class="info-box-number">$<?php echo $items['earning']; ?></span><span> </span>
                            </div>
                            <!-- /.info-box-content -->
                        </div>
                    </a>  
                    <!-- /.info-box -->
                </div>
            </div>
        </div>
        <div class="state-overview">
            <div class="row">
                <div class="col-xl-3 col-md-6 col-12">
                    <a href="<?php echo base_url(); ?>dashboard/3">
                        <div class="info-box bg-grey <?php echo (@$active == 3) ? "active_frame" : ""; ?>"> <span class="info-box-icon push-bottom"><i class="material-icons">group</i></span>
                            <div class="info-box-content"> <span class="info-box-text">Total Appointments</span>
                                <span class="info-box-number"><?php echo $items['total_appointment']; ?></span>
                            </div>
                        
                        </div>
                    </a>
                    <!-- /.info-box -->
                </div>
                <!-- /.col -->
                <div class="col-xl-3 col-md-6 col-12">
                    <a href="<?php echo base_url(); ?>dashboard/4">
                        <div class="info-box bg-grey <?php echo (@$active == 4) ? "active_frame" : ""; ?>"> <span class="info-box-icon push-bottom"><i class="material-icons">group</i></span>
                            <div class="info-box-content"> <span class="info-box-text">Canceled Appointments</span>
                                <span class="info-box-number"><?php echo $items['cancel_appointment']; ?></span>
                            </div>
                           
                        </div>
                    </a>
                </div>
                <!-- /.col -->
                <div class="col-xl-3 col-md-6 col-12">
                    <a href="<?php echo base_url(); ?>dashboard/5">
                        <div class="info-box bg-grey <?php echo (@$active == 5) ? "active_frame" : ""; ?>"> <span class="info-box-icon push-bottom"><i class="material-icons">group</i></span>
                            <div class="info-box-content"> <span class="info-box-text">Completed Appointments</span>
                                <span class="info-box-number"><?php echo $items['past_appointment']; ?></span>
                            </div>
                           
                        </div>
                    </a>
                </div>
                <!-- /.col -->
                <div class="col-xl-3 col-md-6 col-12">
                    <a href="<?php echo base_url(); ?>dashboard/6">
                        <div class="info-box bg-grey <?php echo (@$active == 6) ? "active_frame" : ""; ?>"> <span class="info-box-icon push-bottom"><i class="material-icons">group</i></span>
                            <div class="info-box-content"> <span class="info-box-text">Upcoming Appointments</span>
                                <span class="info-box-number"><?php echo $items['upcoming_appointment']; ?></span>
                            </div>
                           
                        </div>
                    </a>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="card card-topline-lightblue">
                    <div class="card-head">
                        <header>BAR CHART</header>
                        <!--                        <div class="tools">
                                                    <a class="fa fa-repeat btn-color box-refresh" href="javascript:;"></a>
                                                    <a class="t-collapse btn-color fa fa-chevron-down" href="javascript:;"></a>
                                                    <a class="t-close btn-color fa fa-times" href="javascript:;"></a>
                                                </div>-->
                    </div>
                    <div class="card-body" id="chartjs_bar_parent">
                        <div class="row ">                           
                            <div id="chart-container">No Data Found!</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        






    </div>
</div>
</div
<!-- end page container -->
<script>
//function change_graph(id) {
//  //alert(id);
//  $.ajax({
//      url : site_url+"admin/dashboard/dashboard/user_doctor_graph1/1",
//      cache: false,
//      type: "POST",
//      processData :true,
//      data: {id : id},
//      success : function(data) {
//          alert(data);
//        console.log(data);
//      }
//  });
//}
</script>