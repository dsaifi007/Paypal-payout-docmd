<body>

    <table width='750px' border='0' cellspacing='0' cellpadding='0' align='center' style='border:solid 1px #efefef;    background: #efefef;
        height: 100%;'>
        <tbody>
            <tr>
                <td valign='top'>
                    <table width='100%' style='max-width:750px;margin:auto;' border='0' cellpadding='0' cellspacing='0'>
                        <tr>
                            <td align='center'>
                                <!-- ID:BG CTA OPTIONAL -->
                                <table align='center' bgcolor='#000000' border='0' cellpadding='0' cellspacing='0'>
                                    <tr>
                                        <td align='center'>

                                            <table align='center' border='0' cellpadding='0' cellspacing='0' width='800px' style='background-color: #ef5770'>
                                                <tr>
                                                    <td align='center' class='res-padding'>
                                                        <table align='center' border='0' class='display-width-inner' cellpadding='0' cellspacing='0' width='600'>
                                                            <tr>
                                                                <td align='center' style='color:#f6f6f6; font-family:Segoe UI, Helvetica Neue, Arial, Verdana, Trebuchet MS, sans-serif; font-size:26px; font-weight:400; line-height:28px; letter-spacing:1px; padding: 20px 0;'>
                                                                    Welcome To DOCMD
                                                                </td>
                                                            </tr>
                                                        </table>
                                                    </td>
                                                </tr>
                                            </table>
                                        </td>
                                    </tr>
                                </table>
                                <table align='center' bgcolor='#333333' border='0px solid red' cellpadding='0' cellspacing='0' width='100%'>
                                    <tr>
                                        <td align='center'>
                                            <table align='center' style='background-color:#fff;' bgcolor='#333333' border='0' cellpadding='0' cellspacing='0' width='100%' data-module='Advanced Options' data-bgcolor='Main BG'>
                                                    <br><br>
                                                    <tr>
                                                        <td style='line-height:20px;'>
                                                            &emsp;&emsp;&nbsp;&nbsp;Dear Provider
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td style='text-align: center; line-height:20px;'>
                                                            <?php echo $message; ?>
                                                        </td>
                                                    </tr>
                                                    <br><br>
                                                    <tr>
                                                        <td align='center'>
                                                            <table align='center' style='width: 100%; border-collapse: collapse;  border: 1px solid #ddd;    border-spacing: 0;' bgcolor='#ffffff' border='0' class='display-width' cellpadding='0' cellspacing='0'>
                                                                <thead style='color: #fff;background: #ef5770;'>
                                                                    <tr>
                                                                        <th style='padding: 10px;border: 1px solid #d0d0d0;text-align:center'>Date</th>
                                                                        <th style='padding: 10px;border: 1px solid #d0d0d0;text-align:center'>Time</th>
                                                                        <th style='padding: 10px;border: 1px solid #d0d0d0;text-align:center'>Type</th>
                                                                    </tr>
                                                                </thead>
                                                                <tbody>
                                                                    <tr>
                                                                        <td style='padding: 10px;border: 1px solid #d0d0d0;text-align:center'>
                                                                            <?php echo date("m-d-Y",strtotime($appointment_detail['date'])); ?>
                                                                        </td>
                                                                        <td style='padding: 10px;border: 1px solid #d0d0d0;text-align:center'>
                                                                            <?php 
                                                                            $datetime = $appointment_detail['date'].' '.$appointment_detail['time'];
                                                                            echo convert_timezone_into_pst($datetime); ?>
                                                                        </td>
                                                                        <td style='padding: 10px;border: 1px solid #d0d0d0;text-align:center'>
                                                                            <?php echo ucfirst($appointment_detail['type']); ?>
                                                                        </td>
                                                                    </tr>
                                                                </tbody>
                                                            </table>
                                                            <br><br>
                                                            <table style='width: 100%; background-color:#fff;' align='center' border='0' cellpadding='0' cellspacing='0'>
                                                                <tr>
                                                                    <!-- ID:TXT CONTENT -->
                                                                    <td align='center'  style='text-align: center;color:#666666; font-family: Montserrat , sans-serif; font-weight:500; font-size:12px; line-height:18px;'>
                                                                        If you have any questions or queries, feel free to contact us at <a href=''>verify@docmdapp.com</a> or simply reply to this email.
                                                                    </td>
                                                                </tr>

                                                                <tr>
                                                                    <td style='padding-top:30px;text-align:center;color:#717171;   font-family: Montserrat , sans-serif; font-weight:600; font-size:13px; line-height:24px;'>
                                                                        Regards,
                                                                    </td>
                                                                </tr>

                                                             <tr>
                                                                <td style='text-align:center; border-bottom:2px solid #333;padding-bottom:10px;'>
                                                                    <img src='<?php echo $this->config->item("base_url");?>assets/admin/img/docmd-logo.png' style='text-align:center;width:40%;margin:auto;' >
                                                                </td>
                                                            </tr>
                                                              
                                                            </table>
                                                            
                                                             <table>
                                                                <tr>
                                                                    <td colspan='3' style='font-size:15px;font-family:Segoe UI, Helvetica Neue, Arial, Verdana, Trebuchet MS, sans-serif;text-align:center; color:#717171; padding-bottom:10px; padding-top:10px;'>Available on:</td>
                                                                </tr>
                                                                <tr>
                                                                    <td style='width:60px;text-align: center;'>
                                                                        <img src='<?php echo $this->config->item("base_url");?>assets/file/ios.png'>
                                                                        <br>
                                                                        <h4 style='color:#717171;font-family:Segoe UI, Helvetica Neue, Arial, Verdana, Trebuchet MS, sans-serif; font-size:12px;'>iOS</h4></td>
                                                                    <td style='width:60px;text-align: center;'>
                                                                        <img src='<?php echo $this->config->item("base_url");?>assets/file/android.png'>
                                                                        <br>
                                                                        <h4 style='color:#717171;font-family:Segoe UI, Helvetica Neue, Arial, Verdana, Trebuchet MS, sans-serif; font-size:12px;'>android</h4></td>
                                                                    <td style='width:60px;text-align: center;'>
                                                                        <img src='<?php echo $this->config->item("base_url");?>assets/file/web.png'>
                                                                        <br>
                                                                        <h4 style='color:#717171; font-size:12px;font-family:Segoe UI, Helvetica Neue, Arial, Verdana, Trebuchet MS, sans-serif;'>web</h4>
                                                                    </td>
                                                                </tr>
                                                            </table>

                                                        </td>
                                                    </tr>
                                            </table>

                                        </td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                    </table>

                </td>
            </tr>
        </tbody>
    </table>
</body>