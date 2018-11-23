var datatable;
$(document).ready(function () {
  $('#user_transcation_list').DataTable({
       "pageLength": 10, // Set Page Length
       "lengthMenu":[[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]],
       "order": [], //Initial no order.
       "columnDefs": [
       {
          "targets": [0,2,3,4,5,6], //first, Fourth, seventh column
          "orderable": false //set not orderable
       }     
     ]
   });
});


