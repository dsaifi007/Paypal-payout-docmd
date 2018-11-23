	// validate signup form on keyup and submit
	$("document").ready(function() {
		$("#login_validation").validate({
			// errorPlacement: function(error, element) {
			// 	error.appendTo(element.closest('.form-group').after());
			// },
        rules: {
        	email: {
        		required: true,
        		email: true
        	},
        	password: {
        		required: true,
        		minlength: 5
        	},
        	messages: {
        		email: {
        			required: "Please enter a email",
        			minlength: "Your email must consist of at least 2 characters"
        		},
        		password: {
        			required: "Please provide a password",
        			minlength: "Your password must be at least 5 characters long"
        		}
        	},
        	// Make sure the form is submitted to the destination defined
		    // in the "action" attribute of the form when valid
		    submitHandler: function(form) {
		      form.submit();
		    }
        }
    });
	$("#forgot_validation").validate({
        rules: {
        	email: {
        		required: true,
        		email: true
        	},     	
		    submitHandler: function(form) {
		      form.submit();
		    }
        }
    });	

});
