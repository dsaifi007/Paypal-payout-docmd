<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title>Update-password</title>
</head>
<body>
	<center>		
		<?php
		if ($this->session->flashdata('message') && !empty($this->session->flashdata('message'))) {
			echo $this->session->flashdata('message');
		}
		echo "<hr>";
		echo form_open("api/update_password/updtingpassword");
		echo form_hidden('reset_code', $email_key);
		echo form_label('Password', 'password');
		$pass=["name"=>"password","required"=>"requird"];
		echo form_password($pass)."<br>";
		echo form_label('Confirm Password', 'cpassword');
		$cpass=["name"=>"passconf","required"=>"requird"];
		echo form_password($cpass)."<br>";
		echo form_submit('save', 'Submit Password');
		echo form_close();

		?>
	</center>
</body>
</html>







