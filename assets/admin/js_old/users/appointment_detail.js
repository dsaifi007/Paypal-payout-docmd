var datatable;

$(document).ready(function () {  
  $("table.display").DataTable({
       "pageLength": 10, // Set Page Length
       "lengthMenu":[[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]],
       "order": [], //Initial no order.
       "columnDefs": [
       {
          "targets": [0,1,2,3,4,5], //first, Fourth, seventh column
          "orderable": false //set not orderable
       }     
     ]
     // "columns": [
     //    { "width": "1%" },
     //    { "width": "4%" },
     //    null,
     //    { "width": "20%" },
     //    null,
     //    null,
     //    null,
     //    null
     //  ]
   });
});




// $("body").on("click",".blk",function() {
// 	 alert("block");
// });

// function change_doctor_status(doctor_id,status) {
//     // if ( status == 1 ) {
//     //   $("#unblocked"+doctor_id).hide();
//     //   $("#blocked"+doctor_id).show();
//     // }else{
//     //    $("#unblocked"+doctor_id).show();
//     //   $("#blocked"+doctor_id).hide();
//     // }
//     update_user_status(doctor_id,status);
// }
// block/unblock from pharmacy info page
$("body").on("click",".blk",function(event) {
            event.preventDefault();
            var id = $(this).attr("id");
            alert(id);
            var phar_id = $(this).attr("phar-id");
            if (id == "blk") {
                $("#blk").css("display","none");
                $("#unblock").removeAttr("style");
                status = 1;
            } else {
                $("#blk").removeAttr("style");
                $("#unblock").css("display","none");
                status = 0;
            }
            //update_pharmacy_status(phar_id,status);
     });


// block/unblock from listing
$('body').on('change', "#user_block", function() {
  var pharmacy_id;
  var status;
  if($(this).prop('checked') != true){
    status = 0;
  }else{
   status = 1;
 }
 pharmacy_id = $(this).attr("data-id");
 update_pharmacy_status(pharmacy_id,status);
});

function update_pharmacy_status(pharmacy_id , status) {
  $.ajax({
    url : site_url+"admin/pharmacies/pharmacies_controller/update_pharmacy_status",
    cache: false,
    type: "POST",
    processData :true,
    data: {id : pharmacy_id,status:status},
    success : function(data) {
      var response = JSON.parse(data);
      if (response.unblock) {
        alert(response.unblock);
      } else {
        alert(response.block);
      }
    }
  });
}
/*
  Work -- this  function is used for the validtion for the email send to the users 
  */
  //validate signup form on keyup and submit
  $("document").ready(function() {
    $(".users_email_validation").validate({
      // errorPlacement: function(error, element) {
      //  error.appendTo(element.closest('.form-group').after());
      // },
      rules: {
        subject: {
          required: true,
          minlength: 5
        },
        message: {
          required: true,
          minlength: 5
        },
        messages: {
          subject: {
            required: "Please enter a Subject",
            minlength: "Your Subject must consist of at least 6 characters"
          },
          message: {
            required: "Please provide a message",
            minlength: "Your message must be at least 5 characters long"
          }
        },
        submitHandler: function(form) {
          form.submit();
        }
      }
    });
    

    
  });

  $("document").ready(function() {
    $(".doctor_validation").validate({
      // errorPlacement: function(error, element) {
      //  error.appendTo(element.closest('.form-group').after());
      // },
      rules: {
        subject: {
          required: true,
          minlength: 5
        },
        message: {
          required: true,
          minlength: 5
        },
        messages: {
          subject: {
            required: "Please enter a Subject",
            minlength: "Your Subject must consist of at least 6 characters"
          },
          message: {
            required: "Please provide a message",
            minlength: "Your message must be at least 5 characters long"
          }
        },
        submitHandler: function(form) {
          form.submit();
        }
      }
    });
  });

  function textareavldt() {
    var textarea = $.trim($("#message").val());
    if (textarea == '' || textarea == "undefined"){
      alert("Please fill the message");
      return false;
    }
  }



/*
  ------------------------------------------------------------------------------------------
                    Used for  get the reject email id
  ------------------------------------------------------------------------------------------
  */
  $('body').on('click', "#reject_email", function() {
    var email =$(this).attr("data-email");
    $("input[name='email']").val(email);
  });















/*
  This Jquery is used for select all/individual  
  */
  $('body').on('change', "#selectall", function() {
    $("#send_button").attr('disabled', false);
    var totoal_checked =0;
    totoal_checked =$('input[name="sltd_emails[]"]:checked').length;
    if($(this).is(':checked',true))  
    {
     $("#selectall").val("all");
     $("input[name='sltd_emails[]'").prop('checked', true);   
   }  
   else  
   {  
    $("#send_button").attr('disabled', true);
    $("input[name='sltd_emails[]'").prop('checked', false);   
  }  
});
  $('body').on('change', "#chek", function() {
    $("#send_button").attr('disabled', false);
    var totoal_checked =$('input[name="sltd_emails[]"]:checked').length;
    if(totoal_checked>0)  
    {
     $("#selectall").prop('checked', false); 
   }  
   else  
   { 
    $("#send_button").attr('disabled', true);
    $("#selectall").attr("disabled", false);  
  }  
});
