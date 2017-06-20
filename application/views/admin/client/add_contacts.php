<?php include_once 'asset/admin-ajax.php'; ?>
<?php
$eeror_message = $this->session->userdata('error');

if (!empty($eeror_message)):foreach ($eeror_message as $key => $message):
        ?>
        <div class="alert alert-danger">
            <?php echo $message; ?>
        </div>   
        <?php
    endforeach;
endif;
$this->session->unset_userdata('error');
?>
<form role="form" enctype="multipart/form-data" id="form" action="<?php echo base_url(); ?>admin/client/save_contact/<?php
if (!empty($account_details)) {
    echo $account_details->user_id;
}
?>" method="post" class="form-horizontal  ">
    <div class="row">
        <div class="col-sm-12" >        
            <div class="panel panel-default">
                <!-- Default panel contents -->
                <div class="panel-heading">
                    <div class="panel-title">
                        <?= lang('add_contact') ?>
                    </div>
                </div>
                <div class="panel-body">
                    <input type="hidden" name="r_url" value="<?= base_url() ?>admin/client/client_details/<?= $company ?>">
                    <input type="hidden" name="company" value="<?= $company ?>">
                    <input type="hidden" name="role_id" value="2">
                    <div class="form-group">
                        <label class="col-lg-3 control-label"><?= lang('full_name') ?> <span class="text-danger"> *</span></label>
                        <div class="col-lg-5">
                            <input type="text" class="form-control" value="<?php
                            if (!empty($account_details)) {
                                echo $account_details->fullname;
                            }
                            ?>" placeholder="E.g John Doe" name="fullname" required>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-lg-3 control-label"><?= lang('email') ?><span class="text-danger"> *</span></label>
                        <div class="col-lg-5">                            
                            <input class="form-control" id='email' type="email" value="<?php
                            if (!empty($user_info)) {
                                echo $user_info->email;
                            }
                            ?>" placeholder="me@domin.com" name="email" required>                                
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-lg-3 control-label"><?= lang('phone') ?> </label>
                        <div class="col-lg-5">
                            <input type="text" class="form-control" value="<?php
                            if (!empty($account_details)) {
                                echo $account_details->phone;
                            }
                            ?>" name="phone" placeholder="+52 782 983 434">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-lg-3 control-label"><?= lang('mobile') ?> <span class="text-danger"> *</span></label>
                        <div class="col-lg-5">
                            <input type="text" class="form-control" value="<?php
                            if (!empty($account_details)) {
                                echo $account_details->mobile;
                            }
                            ?>" name="mobile" placeholder="+8801723611125">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-lg-3 control-label"><?= lang('skype_id') ?> </label>
                        <div class="col-lg-5">
                            <input type="text" class="form-control" value="<?php
                            if (!empty($account_details)) {
                                echo $account_details->skype;
                            }
                            ?>" name="skype" placeholder="john">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-lg-3 control-label"><?= lang('language') ?></label>
                        <div class="col-lg-5">
                            <select name="language" class="form-control">
                                <?php foreach ($languages as $lang) : ?>
                                    <option value="<?= $lang->name ?>"<?php
                                    if (!empty($account_details->language) && $account_details->language == $lang->name) {
                                        echo 'selected="selected"';
                                    } else {
                                        echo ($this->config->item('language') == $lang->name ? ' selected="selected"' : '');
                                    }
                                    ?>><?= ucfirst($lang->name) ?></option>
                                        <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-lg-3 control-label"><?= lang('locale') ?></label>
                        <div class="col-lg-5">
                            <select class="  form-control" name="locale">
                                <?php foreach ($locales as $loc) : ?>
                                    <option lang="<?= $loc->code ?>" value="<?= $loc->locale ?>"<?= ($this->config->item('locale') == $loc->locale ? ' selected="selected"' : '') ?>><?= $loc->name ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <?php if (empty($account_details)): ?>                        
                        <div class="form-group">
                            <label class="col-lg-3 control-label"><?= lang('username') ?> <span class="text-danger">*</span></label>
                            <div class="col-lg-5">                            
                                <input class="form-control" id='username' type="text" value="<?= set_value('username') ?>" onchange="check_user_name(this.value)" placeholder="johndoe" name="username" required>
                                <div class="required" id="username_result"></div>
                            </div>
                        </div>                    
                        <div class="form-group">
                            <label class="col-lg-3 control-label"><?= lang('password') ?> <span class="text-danger"> *</span></label>
                            <div class="col-lg-5">
                                <input type="password" class="form-control" id="password" value="<?= set_value('password') ?>" name="password">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-lg-3 control-label"><?= lang('confirm_password') ?> <span class="text-danger"> *</span></label>
                            <div class="col-lg-5">
                                <input type="password" class="form-control" value="<?= set_value('confirm_password') ?>" name="confirm_password">
                            </div>
                        </div>
                    <?php endif; ?>
                    <div class="form-group">
                        <label class="col-lg-3 control-label"></label>
                        <div class="col-lg-5">
                            <button type="submit" id="sbtn" class="btn btn-primary"><?= lang('add_contact') ?></button>
                        </div>

                    </div>                    
                </div>
                <!-- /.modal-content -->
            </div>
        </div>
    </div>
</form>
<!-- /.modal-dialog -->