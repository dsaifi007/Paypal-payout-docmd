var datatable;
$(document).ready(function () {
  $('#faq_list').DataTable({
       "pageLength": 10, // Set Page Length
       "lengthMenu":[[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]],
       "order": [], //Initial no order.
       "columnDefs": [
       {
          "targets": [3], //first, Fourth, seventh column
          "orderable": false //set not orderable
       }
     ]   
   });
});
