<div class="login-logo" style="margin: 7% auto;margin-bottom: 0px;margin-left: 80px;">
    <a href=""><?php if (config_item('logo_or_icon') == 'logo_title') { ?>
        <img style="width: 50px;height: 50px;border-radius: 50px" src="<?= base_url() . config_item('company_logo') ?>" class="m-r-sm">
        <?php } elseif ($this->config->item('logo_or_icon') == 'icon') { ?>
            <i class="fa <?= $this->config->item('site_icon') ?>"></i>
        <?php } ?> <span style="font-size: 20px;"><?= config_item('company_name') ?></span></a>
</div><!-- /.login-logo -->
<div class="login-box">    
    <div class="error_login">
        <?php echo validation_errors(); ?>
        <?php
        $error = $this->session->flashdata('error');
        if (!empty($error)) {
            ?>
            <div class="alert alert-danger"><?php echo $this->session->flashdata('error'); ?></div>
        <?php } ?>
        <?= message_box('success'); ?>        
    </div>
    <div class="login-box-body">
        <p class="login-box-msg">Sign in to start your session</p>           
        <form role="form" action="<?php echo base_url() ?>login/forgot_password" method="post">
            <div class="form-group has-feedback">
                <input type="text" name="email_or_username" required="true" class="form-control" placeholder="<?= lang('email') ?>/<?= lang('username') ?>" />                        
            </div>            
            <div class="row">
                <div class="col-xs-4">
                    <button type="submit" name="flag" value="1" class="btn btn-danger btn-block btn-flat"><?= lang('submit') ?></button>
                </div><!-- /.col -->                        
                <div class="col-xs-8">
                    <label class="btn pull-right"><a href="<?= base_url() ?>login"><?= lang('remember_password') ?></a></label>
                </div><!-- /.col -->
            </div>            
        </form>
        <br/>
        <?php if (config_item('allow_client_registration') == 'TRUE') { ?>
            <div class="social-auth-links text-center">            
                <p class="text-muted text-center"><?= lang('do_not_have_an_account') ?></p> 
                <a href="<?= base_url() ?>login/register" class="btn btn-block btn-social btn-facebook btn-flat"><i class="fa fa-sign-in"></i> <?= lang('get_your_account') ?></a>
            </div>
        <?php } ?>
    </div><!-- /.login-box-body -->
</div><!-- /.login-box -->