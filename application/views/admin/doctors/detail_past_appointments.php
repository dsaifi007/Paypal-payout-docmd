
<div class="page-content-wrapper" id="print_page">
    <div class="page-content">
        <div class="page-bar">
            <div class="page-title-breadcrumb">
                <div class=" pull-left">
                    <div class="page-title">Prescription</div>
                </div>
                <ol class="breadcrumb page-breadcrumb pull-right">
                    <?php
                    if ($this->uri->segment(6) == 1) {
                        ?>
                        <li> <a class="parent-item" href="<?php echo base_url(); ?>dashboard">Home</a>&nbsp;
                            <i class="fa fa-angle-right"></i><a class="parent-item" href="<?php echo base_url(); ?>admin/doctors/registered_doctors">Registered Provider</a>&nbsp;
                            <i class="fa fa-angle-right"></i><a class="parent-item" href="<?php echo base_url(); ?>admin/doctors/registered_doctors/doctor_view/<?php echo $appointment_data['doctor_id'] ?>">Provider Detail</a>&nbsp;
                            <i class="fa fa-angle-right"></i><a class="parent-item" href="<?php echo base_url(); ?>admin/doctors/registered_doctors/doctor_view/<?php echo $this->uri->segment(5) ?>">View Appointment</a>&nbsp;
                            <i class="fa fa-angle-right"></i><a class="parent-item" href="<?php echo  base_url();?>admin/doctors/appointment_detail_controller/past_appointments/<?php echo $appointment_data['doctor_id'];?>">Past Appointment</a>&nbsp;
                            <i class="fa fa-angle-right"></i><a class="parent-item" href="#">Appointment Summary</a>&nbsp;

                        </li>
                        <?php
                    } else {
                        ?>

                        <li><i class="fa fa-home"></i>&nbsp;
                            <a class="parent-item" href="<?php echo base_url(); ?>dashboard">Home</a>&nbsp;
                            <i class="fa fa-angle-right"></i>
                            <a class="parent-item" href="<?php echo base_url(); ?>admin/prescription/prescription_controller">Manage E-Prescription</a>&nbsp;
                            <i class="fa fa-angle-right"></i>
                            <a class="parent-item" href="#">Prescription detail</a>&nbsp;
                        </li>
                    <?php } ?>
                </ol>
            </div>

        </div>
        <?php
        echo display_message_info([1 => @$success, 2 => @$error, 3 => validation_errors()]);
        ?>
        <!-- add content here -->
        <div class="row">
            <div class="col-md-12">
                <div class="manage-users">
                    <div class="card card-topline-aqua">
                        <div class="card-head">
                            <div class="col-md-4"></div>
                            <div class="col-md-4 img" >
                                
                                <img  style='margin-left:60px;margin-top:10px;' src="<?php echo base_url(); ?>assets/admin/img/docmd-logo.png" width="50%">
                            </div>
                            <div class="pull-right p-r-20">
                                <center><a href="#"   onclick="printDocument()" class="pull-right"><i class="fa fa-file-pdf-o"></i> <u>Export to PDF</u></a></center>
                            </div>
                            <div class="col-md-12">
                                <center class="img" ><h3 style='font-weight:700;'>Appointment Summary</strong></h3>
                                <div class="row">

                                    <div class="col-md-4 tm-text">
                                        <strong>Appointment Date:</strong> &nbsp; 
                                        <?php 
                                        
                                        echo $appointment_data['booking_date']; ?>
                                    </div>
                                    <div class="col-md-4 tm-text">
                                        <strong>Appointment Time:</strong> &nbsp; <?php echo $appointment_data['booking_time']; ?>
                                    </div>
                                    <div class="col-md-4 tm-text">
                                        <strong>Appointment Type:</strong> &nbsp; <?php echo $appointment_data['type']; ?>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="row">
                                    <div class="col-md-4 tm-text">
                                        <strong>Payment Amount:</strong> &nbsp; $<?php echo $appointment_data['amount']; ?>
                                    </div>
                                    <div class="col-md-4 tm-text">
                                        <strong>Payment Method:</strong> &nbsp; <?php echo $appointment_data['payment_method_type']; ?>
                                    </div>
                                    <div class="col-md-4 tm-text">
                                        <strong>Insurance Claim Status:</strong> &nbsp; <?php echo ($appointment_data['insurance_status']) ? "Yes" : "No"; ?>
                                    </div>

                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="row">
                                    <div class="col-md-4 tm-text">
                                        <strong>Promocode:</strong> &nbsp;  <?php echo ($appointment_data['code']) ? $appointment_data['code'] : "N/A"; ?>
                                    </div>                                   
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <table class="table table-striped table-bordered table-hover  order-column" style="width:100%;">
                                <tbody>
                                    <tr>
                                        <td><strong>User Name:</strong></td>
                                        <td><?php echo $appointment_data['name']; ?></td>
                                    </tr>
                                    <tr>
                                        <td><strong>User Med Id Number:</strong></td>
                                        <td><?php echo $appointment_data['patient_med_id']; ?></td>
                                    </tr>
                                    <tr>
                                        <td><strong>Chief Complaint(Symptoms)</strong></td>
                                        <td><?php echo $appointment_data['symptoms']; ?></td>
                                    </tr>
                                    <tr>
                                        <td><strong>Symptom Onset</strong></td>
                                        <td><?php echo date("m-d-Y", strtotime($appointment_data['symptom_start_date'])); ?></td>
                                    </tr>
                                    <tr>
                                        <td><strong>Provider Name:</strong></td>
                                        <td><?php echo $appointment_data['doctor']; ?></td>
                                    </tr>
                                    <tr>
                                        <td><strong>Provider Med Id Number:</strong></td>
                                        <td><?php echo $appointment_data['med_id']; ?></td>
                                    </tr>
                                    <tr>
                                        <td><strong>Dr. Notes </strong></td>
                                        <td><?php echo $appointment_data['exam_name']; ?></td>
                                    </tr>
                                    <tr>
                                        <td><strong>Diagnosis</strong></td>
                                        <td><?php echo $appointment_data['diagnosis_name']; ?></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="card">
                                <div class="card-head text-center">
                                    <header class="text-center">Prescriptions</header>

                                </div>
                                <div class="card-body">
                                    <table class="table table-striped table-bordered table-hover  order-column" style="width:100%;">
                                        <thead>
                                            <tr>

                                                <th>Medication Name</th>
                                                <th>Quantity</th>
                                                <th>Dosage</th>
                                                <th>Refill</th>
                                                <th>Unit</th>
                                                <th>Frequency</th>
                                                <th>Route</th>
                                                <th>Medication Instruction</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            if (count($prescription_data) > 0 && !empty($prescription_data)) {
                                                foreach ($prescription_data as $key => $value) {
                                                    ?>
                                                    <tr>
                                                        <td><?php echo $value['name']; ?></td>
                                                        <td><?php echo $value['quantity']; ?></td>
                                                        <td><?php echo $value['dosage']; ?></td>
                                                        <td><?php echo $value['refill']; ?></td>
                                                        <td><?php echo $value['unit']; ?></td>
                                                        <td><?php echo $value['frequency']; ?></td>
                                                        <td><?php echo $value['route']; ?></td>
                                                        <td><?php echo $value['additional_info'] . "," . $value['medication_instruction']; ?> </td>
                                                    </tr> 
                                                    <?php
                                                }
                                            }
                                            ?>
                                            <tr>

                                                <th colspan="2">Preferred Pharmacy</th>
                                                <?php if (isset($appointment_data['user_preferred_pharmacy'])) { ?>
                                                    <?php $pharmacy = explode("|", $appointment_data['user_preferred_pharmacy']); ?>
                                                    <th colspan="5" pharmacy_id ="<?php echo $pharmacy['0']; ?>" id="myModal" data-toggle="modal" data-target="#myModal">
                                                        <?php echo ucwords($pharmacy['1']) . " (Mouse Hover here to see the Pharmacy detail)"; ?>
                                                    </th>
                                                    <td colspan="5" >
                                                        <?php echo ucwords($pharmacy['2']); ?>
                                                    </td>
                                                <?php } else {
                                                    ?>
                                                    <th colspan="5" >
                                                        No Pharmacy Available
                                                    </th>
                                                <?php } ?>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>


                    <div class="row">
                        <div class="col-md-12">
                            <div class="card card-topline-yellow">
                                <div class="card-head text-center">
                                    <header class="text-center">Admin Note</header>
                                    <button type="button" class="btn btn-round btn-info pull-right admin_note" appt_id="<?php echo $appointment_data['id']; ?>" data-toggle='modal' data-target='#edit_note'>Add New</button>           
                                </div>
                                <div class="card-body">
                                    <table class="table table-striped table-bordered table-hover  order-column" style="width:100%;">
                                        <?php if (count($admin_note) > 0 && !empty($admin_note)) { ?>
                                            <thead>
                                                <tr>
                                                    <th>Admin Note</th>
                                                    <th>Signature</th>
                                                    <th>Time Stamp</th>
                                                    <th>Action</th>                                             
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php
                                                if (count($admin_note) > 0 && !empty($admin_note)) {
                                                    foreach ($admin_note as $k => $v) {
                                                        ?>
                                                        <tr>
                                                            <td ><?php echo $v['note']; ?></td>
                                                            <td><?php echo $v['admin_name']; ?></td>
                                                            <td><?php echo $v['updated_at']; ?></td>
                                                            <td> <a herf='#' note_id="<?php echo $v['id']; ?>"  note="<?php echo $v['note']; ?>"   appt_id ='<?php echo $v['appointment_id']; ?>' class='admin_note' data-toggle='modal' data-target='#edit_note'><i class='fa fa-edit' ></i></a></td>

                                                        </tr> 
                                                        <?php
                                                    }
                                                }
                                                ?>
                                            </tbody>
                                            <?php
                                        } else {
                                            echo "<tr><td> No Information Available at this time</td></tr>";
                                        }
                                        ?>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>




                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal -->
<div class="modal fade model1" id="myModal" role="dialog">
    <div class="modal-dialog modal-lg">

        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Pharmacy Detail</h4>
            </div>
            <div class="modal-body">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Phone</th>
                            <th>Address</th>
                            <th>City</th>
                            <th>State</th>
                            <th>Zip</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr id="pharmacy_data">

                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="modal-footer">
                <a href="<?php echo base_url(); ?>admin/pharmacies/pharmacies_controller/edit_pharmacy_info/<?php echo @$pharmacy['0']; ?>" class="btn btn-success">Pharmacy Detail</a>

                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
        </div>

    </div>
</div>

<div class="modal fade" id="edit_note" role="dialog">
    <div class="modal-dialog modal-lg">

        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Edit Note</h4>
            </div>
            <div class="modal-body">
                <?php echo form_open("admin/doctors/appointment_detail_controller/add_admin_note"); ?>
                <div class= "col-lg-12"><textarea  style="width:750px;height: 154px; " id="note"  name="note" placeholder="Add Note" value='' required></textarea>
                    <?php echo form_hidden("appointment_id", ''); ?>
                    <?php //echo form_hidden("note_id", '');  ?>
                    <input type="submit" name="save" class="btn btn-danger" value="Submit">
                </div>
                <?php echo form_close(); ?>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
        </div>

    </div>
</div>

<script>

    $(document).ready(function () {
        $("#myModal").hover(function () {
            var pharmacy_id = $("#myModal").attr("pharmacy_id");
            get_pharmacy_detail(pharmacy_id);
            $('.model1').modal({
                show: true
            });
        });
    });

    function get_pharmacy_detail(id) {
        $.ajax({
            url: site_url + "admin/doctors/appointment_detail_controller/get_pharmacy_detail",
            cache: false,
            type: "POST",
            processData: true,
            data: {id: id},
            success: function (data) {
                $("#pharmacy_data").html(data);
            }
        });
    }
    $(".img").hide();
//function printDocument(event) {
    //event.preventDefault();
    function printDocument() {
        $(".img").show();
        window.print();
    }
//}


    $("body").on("click", ".admin_note", function (e) {
        e.preventDefault();
        var appt_id = $(this).attr("appt_id");
        $("#note").val($(this).attr("note"));
        //$("input[name='note_id']").val($(this).attr("note_id"));
        $("input[name='appointment_id']").val(appt_id);
    });
</script>