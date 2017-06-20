<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title><?php echo $title; ?></title>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta http-equiv="X-UA-Compatible" content="IE=edge"> 


        <link href="<?php echo base_url(); ?>asset/css/normalize.css" rel="stylesheet" type="text/css" /> 
        
        <!-- Theme style -->   
        <link href="<?php echo base_url(); ?>asset/css/main.css" rel="stylesheet" type="text/css" /> 
        <link href="<?php echo base_url(); ?>asset/css/admin.css" rel="stylesheet" type="text/css" />        
        <!-- Date and Time Picker CSS -->   
        <link href="<?php echo base_url(); ?>asset/css/datepicker.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo base_url(); ?>asset/css/timepicker.css" rel="stylesheet" type="text/css" />
        <!-- All Icon  CSS -->  
        <link href="<?php echo base_url(); ?>asset/css/font-icons/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css" />        
        <link rel="stylesheet" href="<?php echo base_url(); ?>asset/css/font-icons/entypo/css/entypo.css" >        
        <!-- Data Table  CSS --> 
        <link href="<?php echo base_url(); ?>asset/css/plugins/metisMenu/metisMenu.min.css" rel="stylesheet" type="text/css" /> 
        <link href="<?php echo base_url(); ?>asset/css/plugins/dataTables.bootstrap.css" rel="stylesheet" type="text/css" /> 
        <!--Select 2 -->
        <link href="<?php echo base_url() ?>asset/css/select2.css" rel="stylesheet"/>
        <link href="<?php echo base_url() ?>asset/css/bootstrap-wysihtml5.css" rel="stylesheet"/>
        <!-- toastr -->
        <link href="<?php echo base_url() ?>asset/css/toastr.min.css" rel="stylesheet"/>

        <!--bootstrap Slider -->
        <link href="<?php echo base_url(); ?>plugins/bootstrap-slider/slider.css" rel="stylesheet" type="text/css" />
        <!-- iCheck for checkboxes and radio inputs -->
        <link href="<?php echo base_url(); ?>plugins/iCheck/all.css" rel="stylesheet" type="text/css" />
        <!-- summernote Editor -->
        <link href="<?php echo base_url(); ?>plugins/summernote/summernote.min.css" rel="stylesheet"  type="text/css">
        <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
        <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
        <!--[if lt IE 9]>
        <script src="asset/js/html5shiv.js" type="text/javascript"></script> 
        <script src="asset/js/respond.min.js" type="text/javascript"></script> 
        <![endif]-->
        <script src="<?php echo base_url(); ?>asset/js/jquery-1.10.2.min.js"></script>    
        <!-- ALl Custom Scripts -->  
        <script src="<?php echo base_url(); ?>asset/js/custom.js"></script>
        <script>

            $(document).ready(function() {

                $(window).resize(function() {
                    ellipses1 = $("#bc1 :nth-child(2)")
                    if ($("#bc1 a:hidden").length > 0) {
                        ellipses1.show()
                    } else {
                        ellipses1.hide()
                    }
                    ellipses2 = $("#bc2 :nth-child(2)")
                    if ($("#bc2 a:hidden").length > 0) {
                        ellipses2.show()
                    } else {
                        ellipses2.hide()
                    }
                })
            });
        </script>
    </head>    