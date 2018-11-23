var datatable;
$(document).ready(function () {


    datatable = $('#datatable1').DataTable({

        "processing": true, //Feature control the processing indicator.
        "serverSide": true, //Feature control DataTables' server-side processing mode.
        "order": [], //Initial no order.
        "scrollX": false,
        "autoWidth": false,
        "pageLength": 50, // Set Page Length

        "lengthMenu": [[5, 25, 50, 100, -1], [5, 25, 50, 100, "All"]],
        // Load data for the table's content from an Ajax source
        "ajax": {
            "url": site_url + "admin/doctors/registered_doctors/getdata",
            "type": "POST",
            //Custom Post
            "data": {"filter_data": fd}

        },
        //Set column definition initialisation properties.
        "columnDefs": [
            {
                "targets": [0,9,10,11], //first, Fourth, seventh column
                "orderable": false //set not orderable
            }
        ],
        "fnInitComplete": function (oSettings, response) {

            $("#countData").text(response.recordsTotal);
        }

    });
});
$('#datatable1').wrap("<div class='scrolledTable' style='overflow-y: auto; clear:both;'></div>");




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


$('body').on('change', "#user_block", function () {
    var doctor_id;
    var status;
    if ($(this).prop('checked') != true) {
        status = 0;
    } else {
        status = 1;
    }
    doctor_id = $(this).attr("data-id");
    update_user_status(doctor_id, status);
});

function update_user_status(doctor_id, status) {
    $.ajax({
        url: site_url + "admin/doctors/registered_doctors/update_doctor_status",
        cache: false,
        type: "POST",
        processData: true,
        data: {doctor_id: doctor_id, status: status},
        success: function (data) {
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
$("document").ready(function () {
    $(".users_email_validation").validate({
        // errorPlacement: function(error, element) {
        //  error.appendTo(element.closest('.form-group').after());
        // },
        rules: {
            subject: {
                required: true,
                minlength: 1
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
            submitHandler: function (form) {
                form.submit();
            }
        }
    });
});

$("document").ready(function () {
    $(".doctor_validation").validate({
        // errorPlacement: function(error, element) {
        //  error.appendTo(element.closest('.form-group').after());
        // },
        rules: {
            subject: {
                required: true,
                minlength: 1
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
            submitHandler: function (form) {
                form.submit();
            }
        }
    });
});

function textareavldt() {
    var textarea = $.trim($("#message").val());
    if (textarea == '' || textarea == "undefined") {
        alert("Please fill the message");
        return false;
    }
}



/*
 ------------------------------------------------------------------------------------------
 Used for  get the reject email id
 ------------------------------------------------------------------------------------------
 */
$('body').on('click', "#reject_email", function () {
    var email = $(this).attr("data-email");
    $("input[name='email']").val(email);
});















/*
 This Jquery is used for select all/individual  
 */
$('body').on('change', "#selectall", function () {
    $("#send_button").attr('disabled', false);
    var totoal_checked = 0;
    totoal_checked = $('input[name="sltd_emails[]"]:checked').length;
    if ($(this).is(':checked', true))
    {
        $("#selectall").val("all");
        $("input[name='sltd_emails[]'").prop('checked', true);
    } else
    {
        $("#send_button").attr('disabled', true);
        $("input[name='sltd_emails[]'").prop('checked', false);
    }
});
$('body').on('change', "#chek", function () {
    $("#send_button").attr('disabled', false);
    var totoal_checked = $('input[name="sltd_emails[]"]:checked').length;
    if (totoal_checked > 0)
    {
        $("#selectall").prop('checked', false);
    } else
    {
        $("#send_button").attr('disabled', true);
        $("#selectall").attr("disabled", false);
    }
});
