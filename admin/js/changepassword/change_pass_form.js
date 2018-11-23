$("document").ready(function() {
      $("#change_pass").validate({
            rules: {
             current_passsword: {
                  required: true,
                  minlength: 5
              },
              password: {
                  required: true,
                  minlength: 5
              },
              passconf: {
                 required: true,
                 minlength: 5,
                 equalTo : '[name="password"]'
             },
             submitHandler: function(form) {
                form.submit();
            }
        }
    });
  });
