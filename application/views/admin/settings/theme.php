<?php echo message_box('success') ?>
<?php echo message_box('error') ?>
<div class="row">
    <!-- Start Form -->
    <div class="col-lg-12">
        <form action="<?php echo base_url() ?>admin/settings/save_theme" enctype="multipart/form-data" class="form-horizontal" method="post">        
            <div class="panel panel-default">
                <header class="panel-heading  "><?= lang('theme_settings') ?></header>
                <div class="panel-body">                    
                    <input type="hidden" name="settings" value="<?= $load_setting ?>">
                    <div class="form-group">
                        <label class="col-lg-3 control-label"><?= lang('site_name') ?></label>
                        <div class="col-lg-7">
                            <input type="text" name="website_name" class="form-control" value="<?= config_item('website_name') ?>">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-lg-3 control-label"><?= lang('logo') ?></label>
                        <div class="col-lg-4">
                            <select name="logo_or_icon" class="form-control">
                                <?php $logoicon = config_item('logo_or_icon'); ?>                                
                                <option value="logo_title"<?= ($logoicon == "logo_title" ? ' selected="selected"' : '') ?>><?= lang('logo') ?> & <?= lang('site_name') ?></option>
                                <option value="logo"<?= ($logoicon == "logo" ? ' selected="selected"' : '') ?>><?= lang('logo') ?></option>
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-lg-3 control-label"><?= lang('company_logo') ?></label>
                        <div class="col-lg-7" >                                                        
                            <div class="fileinput fileinput-new" data-provides="fileinput">
                                <div class="fileinput-new thumbnail" style="width: 210px;" >
                                    <?php if (config_item('company_logo') != '') : ?>
                                        <img src="<?php echo base_url() . config_item('company_logo'); ?>" >  
                                    <?php else: ?>
                                        <img src="http://placehold.it/350x260" alt="Please Connect Your Internet">     
                                    <?php endif; ?>                                 
                                </div>
                                <div class="fileinput-preview fileinput-exists thumbnail" style="width: 210px;" ></div>
                                <div>
                                    <span class="btn btn-default btn-file">
                                        <span class="fileinput-new">
                                            <input type="file" name="company_logo"  value="upload"  data-buttonText="<?= lang('choose_file') ?>" id="myImg" />
                                            <span class="fileinput-exists"><?= lang('change') ?></span>    
                                        </span>
                                        <a href="#" class="btn btn-default fileinput-exists" data-dismiss="fileinput"><?= lang('remove') ?></a>

                                </div>

                                <div id="valid_msg" style="color: #e11221"></div>

                            </div>    
                        </div>
                    </div>                                           
                    <div class="form-group">
                        <label class="col-lg-3 control-label"><?= lang('sidebar_theme') ?></label>
                        <div class="col-lg-4">
                            <?php $theme = config_item('sidebar_theme'); ?>
                            <select name="sidebar_theme" class="form-control">
                                <option value="default"<?= ($theme == "default" ? ' selected="selected"' : '') ?>>Default</option>
                                <option value="yellowgreen"<?= ($theme == "yellowgreen" ? ' selected="selected"' : '') ?>>Yellow Green</option>
                                <option value="blue"<?= ($theme == "blue" ? ' selected="selected"' : '') ?>>Blue</option>
                                <option value="purple"<?= ($theme == "purple" ? ' selected="selected"' : '') ?>>Purple</option>
                                <option value="green"<?= ($theme == "green" ? ' selected="selected"' : '') ?>>Green</option>
                                <option value="paste"<?= ($theme == "paste" ? ' selected="selected"' : '') ?>>Paste</option>
                                <option value="red"<?= ($theme == "red" ? ' selected="selected"' : '') ?>>Red</option>                                
                                <option value="light"<?= ($theme == "light" ? ' selected="selected"' : '') ?>>Light</option>                                
                            </select>
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-lg-3 control-label"></label>
                    <div class="col-lg-4">
                        <button type="submit" class="btn btn-sm btn-primary"><?= lang('save_changes') ?></button>
                    </div>
                </div>
            </div>
        </form>
    </div>                        
</div>

