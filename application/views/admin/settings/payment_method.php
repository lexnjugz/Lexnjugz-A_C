<?= message_box('success'); ?>
<div class="nav-tabs-custom col-sm-12">
    <!-- Tabs within a box -->
    <ul class="nav nav-tabs">
        <li class="<?= $active == 1 ? 'active' : ''; ?>"><a href="#manage" data-toggle="tab"><?= lang('payment_method') ?></a></li>
        <li class="<?= $active == 2 ? 'active' : ''; ?>"><a href="#new" data-toggle="tab"><?= lang('new_method') ?></a></li>                                                                     
    </ul>
    <div class="tab-content no-padding">
        <!-- ************** general *************-->
        <div class="tab-pane <?= $active == 1 ? 'active' : ''; ?>" id="manage">
            <div class="table-responsive">
                <table class="table table-striped DataTables " id="DataTables">
                    <thead>
                        <tr>

                            <th ><?= lang('method_name') ?></th>                            
                            <th ><?= lang('action') ?></th>                      
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        if (!empty($all_method_info)) {
                            foreach ($all_method_info as $v_method_info) {
                                ?>
                                <tr>
                                    <td><?= $v_method_info->method_name ?></td>
                                    <td>
                                        <?= btn_edit('admin/settings/payment_method/edit_payment_method/' . $v_method_info->payment_methods_id) ?>
                                        <?= btn_delete('admin/settings/delete_payment_method/' . $v_method_info->payment_methods_id) ?>
                                    </td>
                                </tr>
                                <?php
                            }
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>    
        <div class="tab-pane <?= $active == 2 ? 'active' : ''; ?>" id="new">
            <form method="post" action="<?= base_url() ?>admin/settings/payment_method/update_payment_method/<?php
            if (!empty($method_info)) {
                echo $method_info->payment_methods_id;
            }
            ?>" class="form-horizontal"> 
                <div class="form-group">
                    <label class="col-lg-3 control-label"><?= lang('method_name') ?> <span class="text-danger">*</span></label>
                    <div class="col-lg-5">
                        <input type="text" name="method_name"  value="<?php
                        if (!empty($method_info)) {
                            echo $method_info->method_name;
                        }
                        ?>" class="form-control" placeholder="<?= lang('method_name') ?>" required>
                    </div>                                
                    <div class="col-lg-2">
                        <button type="submit" class="btn btn-sm btn-primary"><?= lang('save') ?></button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>