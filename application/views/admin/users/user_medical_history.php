<?php
//dd($patient_medical_history);
?>
<style>
    table { border-collapse: collapse;margin:0 auto }
    tr { display: block; float: left; }
    th, td { display: block;}
</style>
<div class="page-content-wrapper" id="print_page">
    <div class="page-content">
        <div class="page-bar">
            <div class="page-title-breadcrumb">
                <div class="pull-left">
                    <div class="page-title"><?php echo (isset($patient_medical_history[0]['fullname'])) ? ucwords($patient_medical_history[0]['fullname']) : "N/A" ?></div>
                </div>
                <ol class="breadcrumb page-breadcrumb pull-right">
                    <li><i class="fa fa-home"></i>&nbsp;
                        <a class="parent-item" href="<?php echo base_url() ?>/admin/users/manage_users">Manage Users</a>
                        &nbsp;<i class="fa fa-angle-right"></i>&nbsp;<a class="parent-item" href="<?php echo base_url(); ?>admin/users/manage_users/user_view/<?php echo $this->uri->segment(5);?>">User Details</a>&nbsp;<i class="fa fa-angle-right"></i>
                    </li>
                    <li class="active">View Medical History</li>
                </ol>
            </div>
        </div>
        <!-- add content here -->
        <div class="col-md-12">
            <div class="card card-topline-green">
                <div class="card-head text-center">
                    <header class="text-center">Medical History</header>

                </div>
                <div class="card-body">
                    <table class="table table-striped table-bordered table-hover  order-column" style="width:100%;">

                        <tr>
                            <th>Patieent Name</th>
                            <th>Medications</th>
                            <th>Allergies</th>
                            <th>Past Medical History</th>
                            <th>Social History</th>
                            <th>Family History</th>
                        </tr>
                        <?php
                        if (count($patient_medical_history) > 0 && !empty($patient_medical_history)) {
                            foreach ($patient_medical_history as $key => $value) {
                                ?>
                                <tr>

                                    <td><?php echo $value['name']; ?></td>
                                    <td><?php echo $value['medications']; ?></td>
                                    <td><?php echo $value['allergies']; ?></td>
                                    <td><?php echo $value['past_medical_history']; ?></td>
                                    <td><?php echo $value['social_history']; ?></td>
                                    <td><?php echo $value['family_history']; ?></td>
                                </tr>
                                <?php
                            }
                        }
                        ?>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
//function printDocument(event) {
    //event.preventDefault();
    function printDocument() {
        window.print();
    }
//}
</script>