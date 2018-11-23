var input = $('#start_time,#end_time');
input.clockpicker({
	autoclose: true
});
$("document").ready(function() {
	$("#pharmacy").validate({
		rules: {
			pharmacy_name: {
				required: true,
				minlength: 5
			},
			phone: {
				required: true,
				minlength: 10,
			},
			email: {
				required: true,
				email: true,
			},
			city: {
				required: true,
				minlength: 2,
			},
			state: {
				required: true,
			},
			zip: {
				required: true,
			},
			address: {
				required: true,
			},
			start_time: {
				required: true,
			},
			end_time: {
				required: true,
			},
			submitHandler: function(form) {
				form.submit();
			}
		}
	});
         $("a#edit-form").click(function(event){
            event.preventDefault();
            $("input").removeAttr("disabled");
            $("input[type='submit']").removeClass("hide");
        });
       
        
        
});

