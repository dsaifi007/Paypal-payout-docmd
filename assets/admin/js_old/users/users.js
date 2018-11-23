
var table;
var array = [];
$(document).ready(function() {

    table = $('#table').DataTable({ 
        "processing": true,
        "serverSide": true, 
        "order": [], 
        "ajax": {
            "url": site_url+"admin/users/users/ajax_list",
            "type": "POST"
        },
        "aoColumnDefs": [{ "bSortable": false, "aTargets": [ -1,0,1,3,5,6,7 ] },
      ],
      'order': [[1, 'asc']]
    });


 $("#table tbody td").each( function ( i ) {
        var select = $('<select><option value=""></option></select>')
            .appendTo( $(this).empty() )
            .on( 'change', function () {
                table.column( i )
                    .search( $(this).val() )
                    .draw();
            } );
 
        table.column( i ).data().unique().sort().each( function ( d, j ) {
            select.append( '<option value="'+d+'">'+d+'</option>' )
        } );
    } );










    // For datatable rows
    $(".dataTables_info").hide();



    // Handle form submission event
   $('#frm-example').on('submit', function(e){
      var form = this;
      var rows_selected = table.column(0).checkboxes.selected();
      // Iterate over all selected checkboxes
      $.each(rows_selected, function(index, rowId){
         // Create a hidden element
         $(form).append(
             $('<input>')
                .attr('type', 'hidden')
                .attr('name', 'id[]')
                .attr('data-row', rowId)
                .val(rowId)
         );
         
         //console.log(array);
      });

   });
});




$('body').on('click', "input[name='user_block']", function() {
    var user_id;
    var status;
    if($(this).prop('checked') != true){
      // when user not blocked
      user_id   = $(this).attr("data-id");
      status = 0;
      console.log(status);
    }else{
       user_id = $(this).attr("data-id");
       status = 1;
       console.log(status);
    }
    //update_user_status(user_id,status);
});
function update_user_status(user_id , status) {
  $.ajax({
      url : site_url+"admin/users/users/update_user_status",
      cache: false,
      type: "POST",
      processData :true,
      data: {user_id : user_id,status:status},
      success : function(data) {
        var response = JSON.parse(data);
        if (response.active) {
            alert(response.active);
        } else {
          alert(response.unactive);
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
  function textareavldt() {
    var textarea = $.trim($("#message").val());
    if (textarea == '' || textarea == "undefined"){
      alert("Please fill the message");
      return false;
    }
  }


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


  /*
    This jquery is used for the filtered by mail/femail other
  */

/*  $("document").ready(function() {
    $("input[name='filter']").click(function() {
      alert($(this).val());
    });
  });*/