
<!-- AdminLTE App -->
<script src="<?php echo base_url(); ?>asset/js/bootstrap.min.js" type="text/javascript"></script> 
<script src="<?php echo base_url(); ?>asset/js/menu.js" type="text/javascript"></script>  
<script src="<?php echo base_url(); ?>asset/js/custom-validation.js" type="text/javascript"></script>
<script src="<?php echo base_url(); ?>asset/js/jquery.validate.js" type="text/javascript"></script>
<!-- Jasny Bootstrap for NIce Image Change -->
<script src="<?php echo base_url() ?>asset/js/jasny-bootstrap.min.js"></script>
<script src="<?php echo base_url() ?>asset/js/bootstrap-datepicker.js" ></script>      
<script src="<?php echo base_url() ?>asset/js/timepicker.js" ></script>  

<!-- Data Table -->
<script src="<?php echo base_url(); ?>asset/js/plugins/metisMenu/metisMenu.min.js" type="text/javascript"></script> 
<script src="<?php echo base_url(); ?>asset/js/plugins/dataTables/jquery.dataTables.js" type="text/javascript"></script>  
<script src="<?php echo base_url(); ?>asset/js/plugins/dataTables/dataTables.bootstrap.js" type="text/javascript"></script>
<!--select 2 -->
<script src="<?php echo base_url() ?>asset/js/select2.js"></script>
<!-- Bootstrap Slider -->
<script src="<?php echo base_url() ?>plugins/bootstrap-slider/bootstrap-slider.js" type="text/javascript"></script>
<!-- icheck -->
<script src="<?php echo base_url() ?>plugins/iCheck/icheck.min.js" type="text/javascript"></script>
<!-- Easypichart -->
<script src="<?php echo base_url() ?>plugins/charts/easypiechart/jquery.easy-pie-chart.js" type="text/javascript"></script>
<!-- Bootstrap WYSIHTML5 -->    
<script src="<?php echo base_url() ?>asset/js/bootstrap-wysihtml5.js"></script>
<!-- toastr -->
<script src="<?php echo base_url() ?>asset/js/toastr.min.js"></script>
<!-- summernote Editor -->
<script src="<?php echo base_url() ?>plugins/summernote/summernote.min.js"></script>
<script src="<?php echo base_url() ?>plugins/slimscroll/jquery.slimscroll.min.js"></script>
<script type="text/javascript">
    $(document).ready(function () {
        $("[id^=DataTables]").dataTable();    
    });
    $('.textarea').summernote({
        codemirror: {// codemirror options
            theme: 'monokai'
        }
    });
    $('.note-toolbar .note-fontsize,.note-toolbar .note-help,.note-toolbar .note-fontname,.note-toolbar .note-height,.note-toolbar .note-table').remove();
</script>
<!-- CK Editor -->
<script type="text/javascript">
    $(function () {
        // Replace the <textarea id="editor1"> with a CKEditor
        // instance, using default configuration.        
        //bootstrap WYSIHTML5 - text editor
        $(".wysihtml5").wysihtml5();
    });
</script>
<script>

</script>

</body>
</html>