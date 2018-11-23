 $("document").ready(function() {
 	$("form").submit(function() {
 		event.preventDefault();
 		var filter_data  = $( this ).serializeArray();
 		//$("input[name='gender']").prop("checked",true);
 		$.ajax({
 			url:site_url+"admin/users/users/get_filter_data",
 			type: "POST",
 			cache: false,
 			processData :true,
 			data: filter_data,
 			success: function (response) {
 				window.location = site_url+"admin/users/users/index";
       		},
	       error: function(jqXHR, textStatus, errorThrown) {
	       	console.log(textStatus, errorThrown);
	       }
   		});
 	});
 });