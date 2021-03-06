<?php echo message_box('success') ?>
<div class="row">
    <!-- Start Form -->
    <div class="col-lg-12">
        <form action="<?php echo base_url() ?>admin/settings/save_estimate" enctype="multipart/form-data" class="form-horizontal" method="post">        
            <div class="panel panel-default">
                <header class="panel-heading  "><?= lang('estimate_settings') ?></header>
                <div class="panel-body">
                    <input type="hidden" name="settings" value="<?= $load_setting ?>">

                    <div class="form-group">
                        <label class="col-lg-3 control-label"><?= lang('estimate_prefix') ?> <span class="text-danger">*</span></label>
                        <div class="col-lg-7">
                            <input type="text"  name="estimate_prefix" class="form-control" style="width:260px" value="<?= config_item('estimate_prefix') ?>" required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-lg-3 control-label"><?= lang('show_item_tax') ?></label>
                        <div class="col-lg-6">
                            <label >                                
                                <input type="hidden" value="off" name="show_estimate_tax" />
                                <input type="checkbox" <?php
                                if (config_item('show_estimate_tax') == 'TRUE') {
                                    echo "checked=\"checked\"";
                                }
                                ?> name="show_estimate_tax">
                                <span></span>
                            </label>
                        </div>
                    </div>
                    <div class="form-group terms">
                        <label class="col-lg-3 control-label"><?= lang('estimate_terms') ?></label>
                        <div class="col-lg-9">
                            <textarea class="form-control textarea" name="estimate_terms"><?= config_item('estimate_terms') ?></textarea>
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <div class="col-lg-3 control-label"></div>
                    <div class="col-lg-6">
                        <button type="submit" class="btn btn-sm btn-primary"><?= lang('save_changes') ?></button>
                    </div>
                </div>
            </div>
        </form>
    </div>
    <!-- End Form -->
</div>