

<div class="page-content-wrapper" id="print_page">
    <div class="page-content">
        <div class="page-bar">
            <div class="page-title-breadcrumb">
                <div class=" pull-left">
                    <div class="page-title">Appointment Summary</div>
                </div>
                <ol class="breadcrumb page-breadcrumb pull-right">
                    <li><a class="parent-item" href="<?php echo base_url(); ?>dashboard">Home</a>
                        <i class="fa fa-angle-right"></i><a class="parent-item" href="<?php echo base_url(); ?>admin/users/manage_users">Manage Users</a>&nbsp;
                        <i class="fa fa-angle-right"></i><a class="parent-item" href="<?php echo base_url(); ?>admin/users/manage_users/user_view/<?php echo $appointment_data['user_id']; ?>">User Details</a>&nbsp;

                        <?php
                        if ($this->uri->segment(6) == 1) {
                            ?>
                            <i class="fa fa-angle-right"></i><a class="parent-item" href="<?php echo base_url();?>admin/users/appointment_detail_controller/view_transcation/<?php echo $appointment_data['user_id']; ?>">View Transactions</a><i class="fa fa-angle-right"></i>Transaction Details
                            <?php
                        } else {
                            ?>
                            <i class="fa fa-angle-right"></i><a class="parent-item" href="<?php echo base_url();?>admin/users/appointment_detail_controller/past_appointments/<?php echo $appointment_data['user_id']; ?>">Past Appointment Details</a>&nbsp;<i class="fa fa-angle-right"></i>
                            Appointment Summary
                            <?php
                        }
                        ?>

                    </li>

                </ol>
            </div>
        </div>
        <!-- add content here -->
        <div class="row">
            <div class="col-md-12">
                <div class="manage-users">
                    <div class="card card-topline-aqua">
                        <div class="card-head">
                            <div class="col-md-4"></div>
                            <div class="col-md-4 img" >
                                <img  style='margin-left:60px;margin-top:10px;' src="<?php echo base_url(); ?>assets/admin/img/docmd-logo.png" width="50%">
                                <center class="img" ><h3 style='font-weight:700;'>Appointment Summary</strong></h3>
                            </div>
                            <div class="pull-right p-r-20">
                                <center><a href="#"   onclick="printDocument()" class="pull-right"><i class="fa fa-file-pdf-o"></i> <u>Export to PDF</u></a></center>
                            </div>
                            <div class="col-md-12">
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
                                        <strong>Insurance Claim Status:</strong> &nbsp;<?php echo ($appointment_data['insurance_status']) ? "N/A" : "Yes"; ?>
                                    </div>

                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="row">
                                    <div class="col-md-4 tm-text">
                                        <strong>Promocode:</strong> &nbsp; <?php echo ($appointment_data['code']) ? $appointment_data['code'] : "N/A"; ?>
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

                                        <td><strong>Provider Notes </strong></td>
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
                                                        <td><?php echo $value['medication_instruction']; ?></td>
                                                    </tr> 
                                                    <?php
                                                }
                                            }
                                            ?>
                                        </tbody>
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
<script>
    $(".img").hide();
    function printDocument() {
        $(".img").show();
        window.print();

    }
</script>