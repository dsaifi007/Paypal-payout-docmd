<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8" />
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta content="width=device-width, initial-scale=1" name="viewport" />
        <meta name="description" content="Docmd md admin panel" />
        <meta name="author" content="Chromeinfotech" />
        <title>Login - DOCMD</title>
        <!-- google font -->
        <link href="http://fonts.googleapis.com/css?family=Open+Sans:400,300,600,700&amp;subset=all" rel="stylesheet" type="text/css" />
        <!-- icons -->
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
        <link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons">
        <!-- bootstrap -->

        <link href="<?php echo base_url(); ?>assets/admin/css/common/bootstrap.min.css" rel="stylesheet" type="text/css" />
        <!-- style -->
        <link rel="stylesheet" href="<?php echo base_url(); ?>assets/admin/css/login/login.css">
        <!-- favicon -->
        <link rel="shortcut icon" href="<?php echo base_url(); ?>assets/admin/favicon/favicon.ico" /> 
    </head>
    <body>
        <div class="form-title">
            <img src="<?php echo base_url(); ?>assets/admin/img/docmd-logo.png" alt="docmd logo" />
        </div>
        <!-- Login Form-->
        <div class="login-form text-center">
            <div class="toggle"><i class="fa fa-user"></i></div>
            <?php
            if (!$this->session->flashdata('email_not_exist')) {
                ?>
                <div class="form formLogin">
                    <h2>Login to your account</h2>
                    <?php
                    echo display_message_info([1 => $success, 2 => $error, 3 => validation_errors()]);
                    echo form_open("login", ["id" => "login_validation"]);
                    $email = ["name" => "email", "placeholder" => "email"];
                    //echo "<div class='form-group'>";
                    echo form_input($email);
                    //echo "</div>";
                    $password = ["name" => "password", "placeholder" => "password", "class" => "form-group"];
                    echo form_password($password);
                    ?>
                    <div class="remember text-left">
                        <div class="checkbox checkbox-primary">
                            <input id="checkbox2" type="checkbox" name="chkbox" value="1" checked="">
                            <label for="checkbox2">
                                Remember me
                            </label>
                        </div>
                    </div>
                    <?php
                    echo form_submit(["value" => "Login", "class" => "btn2"]);
                    echo form_close();
                    ?>    

                    <div class="forgetPassword"><a href="javascript:void(0)">Forgot your password?</a>
                    </div>
                </div>
                <?php
            }
            $style = $this->session->flashdata('email_not_exist') ? 'display:block' : "display:none";
            ?>
            <div class="form formReset" style = "<?php echo $style; ?>" >
                <?php
                if ($this->session->flashdata('email_not_exist')) {
                    echo display_message_info([2 => $this->session->flashdata('email_not_exist')]);
                }
                ?>
                <div id="hide"  >
                    <h2>Reset your password?</h2>
                    <?php echo form_open("forgotpassword", ['id' => "forgot_validation"]); ?>
                    <?php
                    $email = ["name" => "email", "placeholder" => "email"];
                    echo form_input($email);
                    echo form_submit(["value" => "Forgot Password", "class" => "btn2", "id" => "fgt_pass"]);
                    ?>
                    <?php echo form_close(); ?>
                </div>
                <div class="remember text-left">
                    <div class="checkbox checkbox-primary">
                        <input id="checkbox2" type="checkbox" checked="">
                        <label for="checkbox2">
                            Remember me
                        </label>
                    </div>
                </div>
            </div>
        </div>
        <!-- End Login Form-->

        <!-- start js include path -->
        <script src="<?php echo base_url() . "assets/admin/js/common"; ?>/jquery.min.js" ></script>
        <script src="<?php echo base_url() . "assets/admin/js/common"; ?>/jquery.validate.min.js" ></script>
        <script src="<?php echo base_url() . "assets/admin/js/login"; ?>/login.js" ></script>
        <script src="<?php echo base_url() . "assets/admin/js/login"; ?>/custom.js"></script>
        <script src="<?php echo base_url() . "assets/admin/js/login"; ?>/pages.js" ></script>
        <!-- end js include path -->
    </body>
</html>