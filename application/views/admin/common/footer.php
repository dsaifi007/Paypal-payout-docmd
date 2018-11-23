</div>
<!-- end page container -->



<!-- start footer -->
<div class="page-footer" id="page-footer">
    <div class="page-footer-inner"> 2018 &copy; DocMD. All Rights Reserved.</div>
    <div class="dashborad1" >
        <a href="#"><i class="icon-arrow-down scrolldown"></i></a>
    </div>
</div>
<!-- end footer -->
</div>



<!-- start js include path -->
<!-- <script src="<?php //echo base_url(); ?>assets/assets/jquery.min.js" ></script> -->
<script src="<?php echo base_url(); ?>assets/assets/popper/popper.js" ></script> 
<!-- <script src="<?php //base_url(); ?>assets/assets/jquery.blockui.min.js" ></script> -->

<script src="<?php echo base_url(); ?>assets/assets/jquery.slimscroll.js"></script>
<!-- bootstrap -->
<script src="<?php echo base_url(); ?>assets/assets/bootstrap/js/bootstrap.min.js" ></script>
<!-- data tables -->
<script src="https://cdn.datatables.net/1.10.16/js/jquery.dataTables.min.js"></script>


<?php
if (isset($add_datatable_js)) {
    ?>
    <script src = "<?php echo base_url() . 'assets/admin/js/' . $add_datatable_js; ?>" ></script>
    <?php
}
?>      
<script src="<?php echo base_url(); ?>assets/assets/datatables/plugins/bootstrap/dataTables.bootstrap4.min.js" ></script>
    <!-- <script src="<?php //echo base_url(); ?>fassets/assets/table_data.js" ></script>
-->
<!-- custom js-->
<script src="<?php echo base_url() . "assets/admin/js/common"; ?>/jquery.validate.min.js" ></script>
 <script>
$(document).ready(function () {

    $('.scrolldown').click(function (e) {
        e.preventDefault();    
       $("body").find(".sidemenu").scrollTop($(document).height());
       $(".slimScrollBar").animate({top: 300},"slow");
    });

    $('.scrollup').click(function (e) {
        e.preventDefault();
       $("body").find(".sidemenu").scrollTop(0);
       $(".slimScrollBar").animate({top: 0},"slow");
    });

});
     
 </script>

<?php
if (isset($add_js)) {
    if (is_array($add_js) && !empty($add_js)) {
        foreach ($add_js as $js_file) {
            ?>
            <script src = "<?php echo base_url() . 'assets/admin/js/' . $js_file; ?>" ></script>
            <?php
        }
    } else {
        ?>
        <script src = "<?php echo base_url() . 'assets/admin/js/' . $add_js; ?>" ></script>
        <?php
    }
}
?>

<!-- Common js-->
<script src="<?php echo base_url(); ?>assets/assets/app.js" ></script>
<script src="<?php echo base_url(); ?>assets/assets/layout.js" ></script>
<script src="<?php echo base_url(); ?>assets/assets/theme-color.js" ></script> 
<!-- Material -->
<script src="<?php echo base_url(); ?>assets/assets/material/material.min.js"></script>


</body>

</html>
