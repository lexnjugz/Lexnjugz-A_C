<form method="post" action="<?= base_url() ?>login/registered_user">
    <div class="register-logo" style="margin: 7% auto;margin-bottom: 0px;margin-left: 80px;">
        <a href=""><?php if (config_item('logo_or_icon') == 'logo_title') { ?>
            <img style="width: 50px;height: 50px;border-radius: 50px" src="<?= base_url() . config_item('company_logo') ?>" class="m-r-sm">
            <?php } elseif ($this->config->item('logo_or_icon') == 'icon') { ?>
                <i class="fa <?= $this->config->item('site_icon') ?>"></i>
            <?php } ?> <span style="font-size: 20px;"><?= config_item('company_name') ?></span></a>
    </div>
    <div class="register-box">                
        <?= message_box('error'); ?>
        <div class="register-box-body">
            <p class="text-center" style="border-bottom: 1px solid #E8e8e8;padding-bottom: 10px;"><?= lang('sign_up_form') ?> <?= $this->config->item('company_name') ?></p>            
            <div class="form-group">
                <label class="control-label"><?= lang('client_status') ?></label>                    
                <select class="form-control" name="client_status" id="client_stusus">
                    <option value="1" >Person</option>
                    <option value="2" >Company</option>
                </select>
            </div> 
            <div class="person">
                <div class="form-group" >
                    <label class="control-label"><?= lang('full_name') ?></label>
                    <input type="text" name="name" required="true" class="form-control person" placeholder="<?= lang('full_name') ?>" >                        
                </div>            
                <div class="form-group">
                    <label class="control-label"><?= lang('email') ?></label>                
                    <input type="email" required="true" class="form-control person" value="" name="email">                
                </div>
                <div class="form-group">
                    <label class="control-label"><?= lang('username') ?></label>                
                    <input type="text" required="true" class="form-control person"  name="username">                
                </div>
                <div class="form-group">
                    <label class="control-label"><?= lang('password') ?></label>                
                    <input type="password" required="true" class="form-control person"  name="password">                
                </div>
                <div class="form-group">
                    <label class="control-label"><?= lang('confirm_password') ?> </label>            
                    <input type="password" required="true" class="form-control person" value="" name="confirm_password">
                </div>
            </div>        
            <div class="company">
                <div class="form-group">
                    <label class="control-label"><?= lang('company_name') ?></label>                
                    <input type="text" required="true" class="form-control company" value="" name="name" >                
                </div>                
                <div class="form-group">
                    <label class="control-label"><?= lang('company_email') ?></label>                
                    <input type="email" required="true" class="form-control company" value="" name="email" >                
                </div>
                <div class="form-group">
                    <label class="control-label"><?= lang('username') ?></label>               
                    <input type="text" required="true" class="form-control company" value="" name="username" >
                </div>
                <div class="form-group">
                    <label class="control-label"><?= lang('password') ?></label>                
                    <input type="password" required="true" class="form-control company" value="" name="password" >                
                </div>
                <div class="form-group">
                    <label class="control-label"><?= lang('confirm_password') ?> </label>            
                    <input type="password" required="true" class="form-control company" value="" name="confirm_password" >
                </div>
            </div>        


            <div class="form-group">
                <button type="submit" class="btn btn-primary"><?= lang('sign_up') ?></button>
            </div>
            <div class="social-auth-links text-center">
                <p>- OR -</p>
                <a href="<?= base_url() ?>login" class="text-center"><?= lang('already_have_an_account') ?></a>
                <a href="<?= base_url() ?>login" class="btn btn-danger btn-block"><?= lang('sign_in') ?></a>
            </div>
        </div><!-- /.form-box -->
    </div><!-- /.register-box -->
</form>