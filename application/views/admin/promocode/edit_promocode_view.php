<?php
//dd($items->pharmacy_timing);
?>
<style>
    .breadcrumb{
        background-color:white;
    }

</style>
<script src="https://maps.googleapis.com/maps/api/js?libraries=places&key=AIzaSyCy0ukcU-KmUDQwyCUyaUQLc66zK0Mk6CU"></script>

<script>

    var pathname = window.location.href;

    var geocoder;
    var map;
    var marker;
    var infowindow = new google.maps.InfoWindow({size: new google.maps.Size(150, 50)});
    function initialize() {
        geocoder = new google.maps.Geocoder();
        var latlng = new google.maps.LatLng(-34.397, 150.644);
        var mapOptions = {
            zoom: 8,
            center: latlng,
            mapTypeId: google.maps.MapTypeId.ROADMAP
        }
        map = new google.maps.Map(document.getElementById('map_canvas'), mapOptions);
        google.maps.event.addListener(map, 'click', function () {
            infowindow.close();
        });
    }

    function clone(obj) {
        if (obj == null || typeof (obj) != 'object')
            return obj;
        var temp = new obj.constructor();
        for (var key in obj)
            temp[key] = clone(obj[key]);
        return temp;
    }


    function geocodePosition(pos) {
        geocoder.geocode({
            latLng: pos
        }, function (responses) {
            if (responses && responses.length > 0) {
                marker.formatted_address = responses[0].formatted_address;
            } else {
                marker.formatted_address = 'Cannot determine address at this location.';
            }
            infowindow.setContent(marker.formatted_address + "<br>coordinates: " + marker.getPosition().toUrlValue(6));
            infowindow.open(map, marker);
        });
    }

    function codeAddress() {
        var address = document.getElementById('address1').value;
        //alert(address);
        geocoder.geocode({'address': address}, function (results, status) {
            if (status == google.maps.GeocoderStatus.OK) {
                map.setCenter(results[0].geometry.location);
                if (marker) {
                    marker.setMap(null);
                    if (infowindow)
                        infowindow.close();
                }
                marker = new google.maps.Marker({
                    map: map,
                    draggable: true,
                    position: results[0].geometry.location
                });
                google.maps.event.addListener(marker, 'dragend', function () {
                    // updateMarkerStatus('Drag ended');
                    document.getElementById("hidden").value = marker.getPosition().toUrlValue(6);
                    geocodePosition(marker.getPosition());
                });
                google.maps.event.addListener(marker, 'click', function () {
                    if (marker.formatted_address) {
                        infowindow.setContent(marker.formatted_address + "<br>coordinates: " + marker.getPosition().toUrlValue(6));
                    } else {
                        infowindow.setContent(address + "<br>coordinates: " + marker.getPosition().toUrlValue(6));
                        document.getElementById("hidden").value = marker.getPosition().toUrlValue(6);
                    }
                    infowindow.open(map, marker);
                });
                google.maps.event.trigger(marker, 'click');
            } else {
                //alert('Geocode was not successful for the following reason: ' + status);
            }
        });
    }

    //$(window).on('load', function () {
    $("document").ready(function () {
        if (pathname == site_url + "admin/pharmacies/pharmacies_controller/edit_pharmacy_info/<?php echo $items->id; ?>") {
            codeAddress();
        }
    });


</script>
<!-- start page content -->
<div class="page-content-wrapper">
    <div class="page-content">
        <div class="page-bar">
            <div class="page-title-breadcrumb">
                <div class="pull-left">
                    <div class="page-title"><?php echo $items->pharmacy_name; ?></div>
                </div>
                <ol class="breadcrumb page-breadcrumb pull-right">
                    <li><i class="fa fa-home"></i>&nbsp;<a class="parent-item" href="manage-users.html">Manage Pharmacies</a>&nbsp;<i class="fa fa-angle-right"></i>
                    </li>
                    <li class="active"><?php echo $items->pharmacy_name; ?></li>
                </ol>
            </div>
        </div>
        <!-- add content here -->
        <div class="row">
            <div class="col-md-12 col-xs-12 user-details">
                <?php
                $form_attr = ["id" => "pharmacy", "class" => "pic-upload mb-30","onsubmit"=>"check_start_end_time(event)"];
                echo form_open_multipart("admin/pharmacies/pharmacies_controller/edit_pharmacy_info/" . $items->id, $form_attr);
                ?>
                <?php
                echo display_message_info([1 => $success, 2 => @$error, 3 => validation_errors()]);
                ?>
                <div class="white-box">
                    <div class="patient-profile">
                        <img src="<?php echo $items->pharmacy_image_url; ?>" class="img-circle" alt="">
                        <!--                        <h5>Add Photo</h5>-->
                        <!--                        <input type="file" name="files" id="imgInp" required>-->
                    </div>
                    <div class="btn-group pull-right"></div>
                    <div class="cardbox">
                        <div class="header">
                            <a href="#" class="btn btn-success btn-xs pull-right" id="edit-form">	
                                <i class="fa fa-pencil"></i>
                            </a>
                        </div>

                        <div class="body">
                            <div class="user-btm-box">
                                <!-- .row -->
                                <div class="row text-center m-t-10">
                                    <div class="col-lg-4 col-md-12 col-sm-6 col-xs-12 text-left"> <strong><?php echo $this->lang->line("p_name") ?></strong>

                                        <?php
                                        $p_name = ["name" => "pharmacy_name", "disabled" => "disabled", "width" => "40%", "class" => "form-control", "id" => "mdl-textfield__input", 'value' => set_value('pharmacy_name', $items->pharmacy_name)];
                                        echo form_input($p_name);
                                        echo form_hidden("id", $items->id);
                                        ?>
                                    </div>

                                    <div class="col-lg-4 col-md-12 col-sm-6 col-xs-12 text-left"> <strong><?php echo $this->lang->line("p_phone") ?></strong>
                                        <p><?php
                                            $phone = ["name" => "phone", "class" => "form-control", "disabled" => "disabled", "id" => "phone", 'value' => set_value('phone', $items->phone)];
                                            echo form_input($phone);
                                            ?></p>
                                    </div>
                                    <div class="col-lg-4 col-md-12 col-sm-6 col-xs-12 text-left"> <strong> <?php echo $this->lang->line("p_city") ?></strong>
                                        <p><?php
                                            $city = ["name" => "city", "class" => "form-control", "disabled" => "disabled", "id" => "city", 'value' => set_value('city', $items->city)];
                                            echo form_input($city);
                                            ?></p>
                                    </div>
                                    <div class="col-lg-8 col-md-12 col-sm-6 col-xs-12 text-left"> <strong><?php echo $this->lang->line("p_state") ?></strong>
                                        <p><?php
                                            $state = ["name" => "state", "class" => "form-control", "disabled" => "disabled", "id" => "state", 'value' => set_value('state', $items->state)];
                                            echo form_input($state);
                                            ?></p>
                                    </div>
                                    <!--                                    <div class="col-lg-4 col-md-12 col-sm-6 col-xs-12 text-left"> 
                                                                            <strong>
                                    <?php //echo $this->lang->line("p_address")  ?></strong>
                                                                            <p><?php
                                    //$address = ["name" => "address1", "class" => "form-control", "disabled" => "disabled", "id" => "address", 'value' => set_value('address', $items->address)];
                                    // echo form_input($address);
                                    ?></p>
                                                                        </div>-->
                                    <div class="col-lg-4 col-md-12 col-sm-6 col-xs-12 text-left"> <strong><?php echo $this->lang->line("p_zip") ?></strong>
                                        <p><?php
                                            $zip = ["name" => "zip", "class" => "form-control", "id" => "zip", "disabled" => "disabled", 'value' => set_value('zip', $items->zip)];
                                            echo form_input($zip);
                                            ?></p>
                                    </div>

                                </div>

                                <div class="row" id="pharmacy_time">
                                    <?php
                                    
                                    $pharmacy_timing = json_decode($items->pharmacy_timing);
                                    //dd($pharmacy_timing);
                                    if (count($pharmacy_timing) > 0 && !empty($pharmacy_timing)) {
                                        foreach ($pharmacy_timing as $key => $value) {
                                            
                                            ?>
                                            <div class="col-md-3 a">
                                                <select class="form-control day_list" name="day[]" disabled="disabled">
                                                    <option value="monday" <?php echo (strtolower($value->day) == "monday")?"selected":"";?> >Monday</option>
                                                    <option value="tuesday" <?php echo (strtolower($value->day) == "tuesday")?"selected":"";?>>Tuesday</option>
                                                    <option value="wednesday" <?php echo (strtolower($value->day) == "wednesday")?"selected":"";?>>Wednesday</option>
                                                    <option value="thursday" <?php echo (strtolower($value->day) == "thursday")?"selected":"";?> >Thursday</option>
                                                    <option value="friday" <?php echo (strtolower($value->day) == "friday")?"selected":"";?>>Friday</option>
                                                    <option value="saturday" <?php echo (strtolower($value->day) == "saturday")?"selected":"";?>>Saturday</option>
                                                    <option value="sunday" <?php echo (strtolower($value->day) == "sunday")?"selected":"";?>>Sunday</option>
                                                </select>
                                            </div>
                                            <div class="col-md-4 a">
                                                <div class="input-group m-b-20">	<span class="input-group-addon"><i class="fa fa-clock-o" aria-hidden="true"></i></span>
                                                    <?php
                                                    $start_time = ["name" => "start_time[]", "disabled"=>"disabled","type" => "time", "class" => "form-control start_time", "placeholder" => $this->lang->line("start_time"), "id" => "start_time", 'value' => $value->open_time];
                                                    echo form_input($start_time);
                                                    ?>                               
                                                </div>
                                            </div>
                                            <div class="col-md-4 a" >
                                                <div class="input-group m-b-20">	<span class="input-group-addon"><i class="fa fa-clock-o" aria-hidden="true"></i></span>
                                                    <?php
                                                    $end_time = ["name" => "end_time[]","disabled"=>"disabled", "type" => "time", "class" => "form-control end_time", "placeholder" => $this->lang->line("end_time"), "id" => "end_time", 'value' => $value->close_time];
                                                    echo form_input($end_time);
                                                    ?>
                                                </div>
                                            </div>
                                         
                                            <?php
                                        }
                                    }
                                    ?>
                                </div>



                                <div class="row">
                                    <div class="col-md-12">
                                        <div>
                                            <input id="address1" name="address" type="textbox" onmouseout="codeAddress()" disabled="disabled" value="<?php echo $items->address; ?>">
                                            <input type="button" id="save" value="Save" onkeyup="codeAddress()">
                                            <input id="hidden" disabled="disabled" name="address_location" type="hidden" value="">
                                        </div><br>
                                        <div id="map_canvas" style="height:480px"></div>
                                        <script src="http://www.google-analytics.com/urchin.js" type="text/javascript">
                                        </script> 
                                    </div>
                                </div>                         


                                <!-- /.row -->
                                <div class="row text-center">
                                    <div class="col-md-12">
                                        <?php echo form_submit("pharmacy_submit", "Save Changes", ["class" => "mdl-button mdl-js-button mdl-button--raised mdl-js-ripple-effect m-b-10 btn-primary hide"]); ?>
                                    </div></div>
                                <!-- .row -->
                                <div class="row text-center">
                                    <div class="col-md-12">Block/Unblock this User: &nbsp;

                                        <?php $status1 = ($items->is_blocked == 1) ? "style='display:none'" : ""; ?>
                                        <?php $status2 = ( $items->is_blocked == 0) ? "style='display:none'" : ''; ?>

                                        <a href="#" <?php echo $status1; ?> class="btn btn-circle btn-danger blk" id='blk' 
                                           phar-id = "<?php echo ($items->id); ?>">Block</a>


                                        <a href="#" <?php echo $status2; ?> class="btn btn-circle btn-success blk"  phar-id = "<?php echo $items->id; ?>" id='unblock' >Unblock</a>


                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <?php echo form_close(); ?>           
            </div>
        </div>
    </div>
</div>





</div>
<!-- end page content -->   









<!-- end page content -->
<script src="<?php //echo base_url()."assets/admin/js/pharmacies";  ?>/fileuploader.min.js" ></script>
<script src="<?php //echo base_url()."assets/admin/js/pharmacies";  ?>/fileuploader-custom2.js" ></script>
<script>
   $("a#edit-form").click(function(event){
            event.preventDefault();
            $("input").removeAttr("disabled");
            $("select").removeAttr("disabled");
            $("input[type='submit']").removeClass("hide");
        });
        
    
    
    
// block/unblock from pharmacy info page
                                                $("body").on("click", ".blk", function (event) {
                                                    event.preventDefault();

                                                    var id = $(this).attr("id");

                                                    var phar_id = $(this).attr("phar-id");
                                                    if (id == "blk") {
                                                        $("#blk").css("display", "none");
                                                        $("#unblock").removeAttr("style");
                                                        status = 1;
                                                    } else {
                                                        $("#blk").removeAttr("style");
                                                        $("#unblock").css("display", "none");
                                                        status = 0;
                                                    }
                                                    update_pharmacy_status(phar_id, status);
                                                });

                                                function update_pharmacy_status(pharmacy_id, status) {
                                                    $.ajax({
                                                        url: site_url + "admin/pharmacies/pharmacies_controller/update_pharmacy_status",
                                                        cache: false,
                                                        type: "POST",
                                                        processData: true,
                                                        data: {id: pharmacy_id, status: status},
                                                        success: function (data) {
                                                            var response = JSON.parse(data);
                                                            if (response.unblock) {
                                                                alert(response.unblock);
                                                            } else {
                                                                alert(response.block);
                                                            }
                                                        }
                                                    });
                                                }

// $("document").ready(function(){
//     $(".fileuploader").after("<input type='hidden' \n\
// name='fileuploader-list-files' value='['0:/avatar-159236_640.png']'">
// )
// });
function check_start_end_time(event){   
    var start;
    var end;
    $(".start_time").each(function(start_index , item){
        start = $(item).val();
        
        $(".end_time").each(function(end_index , item1){
            end = $(item1).val();
            if(start_index == end_index)
            {
              if( start > end && start != '' && end != ''){
                     event.preventDefault();
                    alert("Start time can not greater than end time");
                  //preventDefault();
                  //return false;
              } 
            }
              
        });       
    });
      var day_array  = [];  
      $(".day_list").each(function(index,item2){
         var value = $(item2).val();        
         if($.inArray(value,day_array) && value != ''){ 
             day_array.push(value);                       
         }else{
             event.preventDefault();
             alert("More than one day name can not be same");
         }
         //console.log(day_array);
     });     
    
}  

</script>

