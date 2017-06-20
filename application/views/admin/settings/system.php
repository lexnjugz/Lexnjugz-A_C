<?php echo message_box('success') ?>
<div class="row">
    <!-- Start Form -->
    <div class="col-lg-12">
        <form role="form" id="form" action="<?php echo base_url(); ?>admin/settings/save_system" method="post" class="form-horizontal  ">
            <section class="panel panel-default">
                <header class="panel-heading  "><?= lang('system_settings') ?></header>
                <div class="panel-body">
                    <?php echo validation_errors(); ?>
                    <input type="hidden" name="settings" value="<?= $load_setting ?>">
                    <div class="form-group">
                        <label class="col-lg-3 control-label"><?= lang('default_language') ?></label>
                        <div class="col-lg-4">
                            <select name="default_language" class="form-control">

                                <?php
                                if (!empty($languages)) {
                                    foreach ($languages as $lang) :
                                        ?>
                                        <option lang="<?= $lang->code ?>" value="<?= $lang->name ?>"<?= (config_item('default_language') == $lang->name ? ' selected="selected"' : '') ?>><?= ucfirst($lang->name) ?></option>
                                        <?php
                                    endforeach;
                                }
                                ?>
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-lg-3 control-label"><?= lang('locale') ?></label>
                        <div class="col-lg-4">
                            <select name="locale" class="form-control select_box" required>
                                <?php foreach ($locales as $loc) : ?>
                                    <option lang="<?= $loc->code ?>" value="<?= $loc->locale ?>"<?= (config_item('locale') == $loc->locale ? ' selected="selected"' : '') ?>><?= $loc->name ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-lg-3 control-label"><?= lang('timezone') ?> <span class="text-danger">*</span></label>
                        <div class="col-lg-5">
                            <select  name="timezone" class="form-control select_box" required>
                                <?php foreach ($timezones as $timezone => $description) : ?>
                                    <option value="<?= $timezone ?>"<?= (config_item('timezone') == $timezone ? ' selected="selected"' : '') ?>><?= $description ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-lg-3 control-label"><?= lang('default_currency') ?></label>
                        <div class="col-lg-3">
                            <select name="default_currency" class="form-control select_box" >
                                <?php $cur = $this->db->where('code', config_item('default_currency'))->get('tbl_currencies')->row(); ?>

                                <?php foreach ($currencies as $cur) : ?>
                                    <option value="<?= $cur->code ?>"<?= (config_item('default_currency') == $cur->code ? ' selected="selected"' : '') ?>><?= $cur->name ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-lg-3 control-label"><?= lang('default_tax') ?></label>
                        <div class="col-lg-2">
                            <input type="text" class="form-control" value="<?= config_item('default_tax') ?>" name="default_tax">
                        </div>
                    </div>
                    <?php
                    $this->settings_model->set_locale();
                    $date_format = config_item('date_format');
                    ?>
                    <div class="form-group">
                        <label class="col-lg-3 control-label"><?= lang('date_format') ?></label>
                        <div class="col-lg-3">
                            <select name="date_format" class="form-control">
                                <option value="%d-%m-%Y"<?= ($date_format == "%d-%m-%Y" ? ' selected="selected"' : '') ?>><?= strftime("%d-%m-%Y", time()) ?></option>
                                <option value="%m-%d-%Y"<?= ($date_format == "%m-%d-%Y" ? ' selected="selected"' : '') ?>><?= strftime("%m-%d-%Y", time()) ?></option>
                                <option value="%Y-%m-%d"<?= ($date_format == "%Y-%m-%d" ? ' selected="selected"' : '') ?>><?= strftime("%Y-%m-%d", time()) ?></option>                            
                                <option value="%d-%m-%y"<?= ($date_format == "%d-%m-%y" ? ' selected="selected"' : '') ?>><?= strftime("%d-%m-%y", time()) ?></option>                            
                                <option value="%m-%d-%y"<?= ($date_format == "%m-%d-%y" ? ' selected="selected"' : '') ?>><?= strftime("%m-%d-%y", time()) ?></option>
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-lg-3 control-label"><?= lang('enable_languages') ?></label>
                        <div class="col-lg-6">
                            <label >
                                <input type="hidden" value="off" name="enable_languages" />
                                <input type="checkbox" <?php
                                if (config_item('enable_languages') == 'TRUE') {
                                    echo "checked=\"checked\"";
                                }
                                ?> name="enable_languages">
                                <span></span>
                            </label>
                        </div>
                    </div>                                                    
                </div>
                <div class="form-group">
                    <label class="col-lg-3 control-label"></label>
                    <div class="col-lg-6">
                        <button type="submit" class="btn btn-sm btn-primary"><?= lang('save_changes') ?></button>
                    </div>
                </div>    
            </section>
        </form>
    </div>
    <!-- End Form -->
</div>