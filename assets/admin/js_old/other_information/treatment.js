var datatable;
$(document).ready(function () {
  $('#treatment_list').DataTable({
       "pageLength": 10, // Set Page Length
       "lengthMenu":[[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]],
       "order": [], //Initial no order.
       "columnDefs": [
       {
          "targets": [0,2,4], //first, Fourth, seventh column
          "orderable": false //set not orderable
       }, 
     ],
     
   });
});

/*
  Work -- this  function is used for the validtion symptoms 
  */
  $("document").ready(function() {
    $("#add_symptomss").validate({
      // errorPlacement: function(error, element) {
      //  error.appendTo(element.closest('.form-group').after());
      // },
      rules: {
        name: {
          required: true,
          minlength: 5,
          maxlength: 45
        },
        additional_info: {
          required: true,
          minlength: 5
        },
        spn_name: {
          required: true,
          minlength: 5,
          maxlength: 45
        },
        spn_additional_info: {
          required: true,
          minlength: 5
        },
        submitHandler: function(form) {
          form.submit();
        }
      }
    });
    
  });


$("body").on("change","#file-7",function(){
    $("form").removeAttr("id");
});
















