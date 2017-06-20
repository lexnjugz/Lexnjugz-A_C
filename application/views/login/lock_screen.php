<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title><?= $title ?></title>
        <!-- Tell the browser to be responsive to screen width -->
        <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
        <link href="<?php echo base_url(); ?>asset/css/main.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo base_url(); ?>asset/css/admin.css" rel="stylesheet" type="text/css" />
        <!-- Font Awesome Icons -->
        <link href="<?php echo base_url(); ?>asset/css/font-icons/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css" />
        <!-- Theme style -->

        <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
        <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
        <!--[if lt IE 9]>
            <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
            <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
        <![endif]-->
    </head>
    <body class="lockscreen">
        <?php
        $user_id = $this->session->userdata('user_id');
        $profile_info = $this->db->where('user_id', $user_id)->get('tbl_account_details')->row();
        ?>
        <div class="lockscreen-logo" style="margin: 7% auto;margin-bottom: 0px;margin-left: 80px;">
            <a href=""><?php if (config_item('logo_or_icon') == 'logo_title') { ?>
                    <img style="width: 50px;height: 50px;border-radius: 50px" src="<?= base_url() . config_item('company_logo') ?>" class="m-r-sm">
                <?php } elseif ($this->config->item('logo_or_icon') == 'icon') { ?>
                    <i class="fa <?= $this->config->item('site_icon') ?>"></i>
                <?php } ?> <span style="font-size: 20px;"><?= config_item('company_name') ?></span></a>
        </div>
        <!-- Automatic element centering -->
        <div class="lockscreen-wrapper">
            <?php
            $error = $this->session->flashdata('error');

            if (!empty($error)) {
                ?>
                <div class="alert alert-danger"><?php echo $this->session->flashdata('error'); ?></div>
            <?php } ?>


            <!-- User name -->
            <div class="lockscreen-name"><?= $profile_info->fullname ?></div>

            <!-- START LOCK SCREEN ITEM -->
            <div class="lockscreen-item">
                <!-- lockscreen image -->
                <div class="lockscreen-image">
                    <img src="<?= base_url() . $profile_info->avatar ?>" alt="User Image" />
                </div>
                <!-- /.lockscreen-image -->

                <!-- lockscreen credentials (contains the form) -->

                <form action="<?php echo base_url() ?>locked/check_login/<?= $this->session->userdata('user_name') ?>" method="post" class="lockscreen-credentials">
                    <div class="input-group">
                        <input type="password" class="form-control" name="password" required="" placeholder="password" />
                        <div class="input-group-btn">
                            <button class="btn"><i class="fa fa-arrow-right text-muted"></i></button>
                        </div>
                    </div>
                </form><!-- /.lockscreen credentials -->

            </div><!-- /.lockscreen-item -->
            <div class="help-block text-center">
                <?= lang('retrive_session') ?>
            </div>
            <div class="text-center">
                <a class="text-danger" href="<?= base_url() ?>login/logout">Or sign in as a different user</a>
            </div>
            <br/>
            <br/>
            <br/>
            <br/>
            <div class="lockscreen-footer text-center">
                <div class="pull-right hidden-xs">
                    <b>Version</b> 1.1
                </div>
                <strong>Copyright &copy; <a href="#">Lexnjugz</a>.</strong> All rights reserved.
            </div>
        </div><!-- /.center -->

        <script src="<?php echo base_url(); ?>asset/js/jquery-1.10.2.min.js"></script>
        <!-- Bootstrap 3.3.2 JS -->
        <script src="<?php echo base_url(); ?>asset/js/bootstrap.min.js" type="text/javascript"></script>
    </body>
</html>
