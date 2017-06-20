<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title><?php echo $title; ?><</title>
        <meta name="description" content="<?= $this->config->item('site_desc') ?>" />
        <!-- Tell the browser to be responsive to screen width -->
        <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
        <!-- Bootstrap 3.3.4 -->        
        <!-- Font Awesome Icons -->
        <link href="<?php echo base_url(); ?>asset/css/font-icons/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css" />        
        <!-- Theme style -->
        <link href="<?php echo base_url(); ?>asset/css/main.css" rel="stylesheet" type="text/css" /> 
        <link href="<?php echo base_url(); ?>asset/css/admin.css" rel="stylesheet" type="text/css" />                        
        <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
        <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
        <!--[if lt IE 9]>
            <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
            <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
        <![endif]-->

    </head>
    <body class="login-page">
        <?= $subview; ?>
        <!-- jQuery 2.1.4 -->
        <script src="<?php echo base_url(); ?>asset/js/jquery-1.10.2.min.js"></script>         
        <!-- Bootstrap 3.3.2 JS -->
        <script src="<?php echo base_url(); ?>asset/js/bootstrap.min.js" type="text/javascript"></script>             
        <script type="text/javascript">
            $(document).ready(function() {
                var client_stusus = $('#client_stusus').val();
                if (client_stusus == '2') {
                    $(".company").removeAttr('disabled');
                } else {
                    $('.company').hide();
                    $(".company").attr('disabled', 'disabled');
                }
                $('#client_stusus').change(function() {
                    if ($('#client_stusus').val() == '1') {
                        $('.person').show();
                        $('.company').hide();
                        $(".company").attr('disabled', 'disabled');
                        $(".person").removeAttr('disabled');
                    } else {
                        $('.person').hide();
                        $('.company').show();
                        $(".person").attr('disabled', 'disabled');
                        $(".company").removeAttr('disabled');
                    }
                });
            });
        </script>

    </body>
</html>
