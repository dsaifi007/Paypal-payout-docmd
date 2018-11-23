<style>
    .breadcrumb{
        background-color:white;
    }

</style>
<link href="<?php echo base_url(); ?>assets/admin/css/common/component.css" rel="stylesheet" type="text/css" />


<!-- start page content -->
<div class="page-content-wrapper">
    <div class="page-content">

        <!-- add content here -->
        <div class="row">
            <div class="col-md-12 col-xs-12 user-details">
                <div class="white-box">
                    <div class="card-head">
                        <ol class="breadcrumb page-breadcrumb pull-left">
                            <li><i class="fa fa-home"></i>&nbsp;<a class="parent-item" href="<?php echo base_url() . "admin/pharmacies/pharmacies_controller" ?>"><?php echo $this->lang->line("home"); ?></a>&nbsp;<i class="fa fa-angle-right"></i>
                            </li>
                            <li><a class="parent-item" href="#"><?php echo $this->lang->line("pharmacy"); ?></a>&nbsp;<i class="fa fa-angle-right"></i>
                            </li>
                            <li class="active"><?php echo $this->lang->line("add_pharmacy"); ?></li>
                        </ol>
                    </div>
                    <?php
                    $form_attr = ["id" => "pharmacy", "onsubmit" => "check_start_end_time(event)"];
                    echo form_open_multipart("admin/pharmacies/pharmacies_controller/add_pharmacy_info", $form_attr);
                    ?>
                    <?php
                    echo display_message_info([1 => @$success, 2 => @$error, 3 => validation_errors()]);
                    ?>
                    <div class="card-body " id="bar-parent6">
                        <!-- .row -->
                        <div class="row text-center m-t-10">
                            <div class="col-md-6 col-sm-6">
                                <div class="input-group m-b-20">
                                    <span class="input-group-addon"><i class="fa fa-pie-chart" aria-hidden="true"></i></span>
                                    <?php
                                    $p_name = ["name" => "pharmacy_name", "class" => "form-control", "placeholder" => $this->lang->line("pharmacy_name"), "id" => "pharmacy_name", 'value' => set_value('pharmacy_name')];
                                    echo form_input($p_name);
                                    ?>


                                </div>
                                <!--                                <div class="input-group m-b-20">    
                                                                    <span class="input-group-addon"><i class="fa fa-envelope"></i></span>
                                <?php
                                //$email = ["name" => "email", "type" => "email", "class" => "form-control", "id" => "email", "placeholder" => $this->lang->line("email"), 'value' => set_value('email')];
                                //echo form_input($email);
                                ?>
                                                                </div>-->
                                <div class="input-group m-b-20">    <span class="input-group-addon"><i class="fa fa-futbol-o" aria-hidden="true"></i></span>
                                    <?php
                                    $state = ["name" => "state", "class" => "form-control", "id" => "state", "placeholder" => $this->lang->line("state"), 'value' => set_value('state')];
                                    echo form_input($state);
                                    ?>
                                </div>
                                <div class="input-group m-b-20">    
                                   <span class="input-group-addon"><i class="fa fa-image" aria-hidden="true"></i></span>
                                       <input type="file"  name='pharmacy_img' id="file-7" class="inputfile inputfile-6" data-multiple-caption="{count} files selected" multiple style="display:none" />
                                        <label for="file-7" class="form-control" style="cursor: pointer;" >&nbsp; Upload Logo<span></span>  <strong> <i class="fa fa-cloud-upload" aria-hidden="true"></i> </strong>
                                                                 
                                </div>

                            </div>
                            <div class="col-md-6 col-sm-6">
                                <div class="input-group m-b-20">    <span class="input-group-addon"><i class="fa fa-mobile" aria-hidden="true"></i></span>
                                    <?php
                                    $phone = ["name" => "phone", "class" => "form-control", "id" => "phone", "placeholder" => $this->lang->line("phone_number"), 'value' => set_value('phone')];
                                    echo form_input($phone);
                                    ?>                                </div>
                                <div class="input-group m-b-20">    <span class="input-group-addon"><i class="fa fa-building" aria-hidden="true"></i></span>
                                    <?php
                                    $city = ["name" => "city", "class" => "form-control", "placeholder" => $this->lang->line("city"), "id" => "city", 'value' => set_value('city')];
                                    echo form_input($city);
                                    ?>                                </div>
                                <div class="input-group m-b-20">    
                                    <span class="input-group-addon"><i class="fa fa-futbol-o" aria-hidden="true"></i></span>
                                    <?php
                                    $zip = ["name" => "zip", "class" => "form-control", "id" => "zip", "placeholder" => $this->lang->line("zip"), 'value' => set_value('zip')];
                                    echo form_input($zip);
                                    ?>
                                </div>

                                </label>

                                       
                            </div>

                        </div>
                         <div class="row container">
                            <div class="col-md-12">
                                <h2><center>Open/Close</center></h2>
                            </div>
                        </div>

                        <div class="row container" id="pharmacy_time">
                            <hr>
                            <div class="col-md-3 a">
                                <select class="form-control day_list" name="day[]">
                                    <option value="monday">Monday</option>
                                    <option value="tuesday">Tuesday</option>
                                    <option value="wednesday">Wednesday</option>
                                    <option value="thursday">Thursday</option>
                                    <option value="friday">Friday</option>
                                    <option value="saturday">Saturday</option>
                                    <option value="sunday">Sunday</option>
                                </select>
                            </div>
                            <div class="col-md-4 a">
                                <div class="input-group m-b-20">    <span class="input-group-addon"><i class="fa fa-clock-o" aria-hidden="true"></i></span>
                                    <?php
                                    $start_time = ["name" => "start_time[]", "type" => "time", "class" => "form-control start_time", "placeholder" => $this->lang->line("start_time"), "id" => "start_time", 'value' => ''];
                                    echo form_input($start_time);
                                    ?>                               
                                </div>
                            </div>
                            <div class="col-md-4 a" >
                                <div class="input-group m-b-20">    <span class="input-group-addon"><i class="fa fa-clock-o" aria-hidden="true"></i></span>
                                    <?php
                                    $end_time = ["name" => "end_time[]", "type" => "time", "class" => "form-control end_time", "placeholder" => $this->lang->line("end_time"), "id" => "end_time", 'value' => ''];
                                    echo form_input($end_time);
                                    ?>
                                </div>
                            </div>
                            <div class="col-md-1">
                                <div class="icon-holder" id="add_new_day">
                                    <i class="material-icons f-left" style="font-size:37px;line-height:0.9;cursor: pointer;">add_box</i> 
                                </div>                            
                            </div>

                        </div>


                        <div class="row container">
                            <div class="col-md-12">
                                <div>
                                    <input id="address1" name="address"  onmouseout="codeAddress()" type="textbox" value="" placeholder="Enter the address">
                                    <input type="button" value="Save" onclick="codeAddress()">
                                    <input id="hidden" name="address_location" type="hidden" value="">
                                </div><br>
                                <div id="map_canvas" style="height:480px"></div>
                                <script src="http://www.google-analytics.com/urchin.js" type="text/javascript">
                                </script> 
                            </div>
                        </div>
                        <!-- /.row -->
                        <hr>


                        <!-- .row -->
                        <div class="row text-center">
                            <div class="col-md-12">
                                <?php echo form_submit("pharmacy_submit", "Save Changes", ["class" => "mdl-button mdl-js-button mdl-button--raised mdl-js-ripple-effect m-b-10 btn-primary"]); ?>
                            </div>
                        </div>

                    </div>
                    <?php echo form_close(); ?>
                </div>
            </div>
        </div>





    </div>
    <?php //echo form_close(); ?>
</div>
</div>
<!-- end page content -->   
<script src="https://maps.googleapis.com/maps/api/js?libraries=places&key=AIzaSyCy0ukcU-KmUDQwyCUyaUQLc66zK0Mk6CU"></script>

<script>
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

                                        /// 
                                        var inc = 1;
                                        $("document").ready(function () {
                                            $("#add_new_day").click(function () {
                                                if (inc < 7) {
                                                    //alert($("div #pharmacy_time:last").index);
                                                    $("div #pharmacy_time:last").clone().insertAfter("#pharmacy_time");
                                                    $(".f-left:last").text("remove");
                                                    $(".f-left:last").addClass("remove");
                                                    inc++;
                                                }
                                            });
//     $(".remove").click(function(){
//         alert("eee");
//         //alert($(this).html());
//     });
                                        });
                                        $("body").on("click", ".remove", function () {
                                            if (inc <= 7) {
                                                $(this).remove();
                                                $("div #pharmacy_time:last").remove();
                                                inc--;
                                            }
                                        });
                                        function check_start_end_time(event) {
                                            var start;
                                            var end;
                                            $(".start_time").each(function (start_index, item) {
                                                start = $(item).val();

                                                $(".end_time").each(function (end_index, item1) {
                                                    end = $(item1).val();
                                                    if (start_index == end_index)
                                                    {
                                                        if (start > end && start != '' && end != '') {
                                                            alert("Start time can not greater than end time");
                                                            event.preventDefault();
                                                        }
                                                    }

                                                });
                                            });
                                            var day_array = [];
                                            $(".day_list").each(function (index, item2) {
                                                var value = $(item2).val();
                                                if ($.inArray(value, day_array) && value != '') {
                                                    day_array.push(value);
                                                } else {
                                                    alert("More than one day name can not be same");
                                                    event.preventDefault();
                                                }
                                                //console.log(day_array);
                                            });

                                        }
</script>


