<?= message_box('success'); ?>
<div class="nav-tabs-custom">
    <!-- Tabs within a box -->
    <ul class="nav nav-tabs">
        <li class="<?= $active == 1 ? 'active' : ''; ?>"><a href="#manage" data-toggle="tab"><?= lang('tax_rates') ?></a></li>
        <li class="<?= $active == 2 ? 'active' : ''; ?>"><a href="#new" data-toggle="tab"><?= lang('new_tax_rate') ?></a></li>                                                                     
    </ul>
    <div class="tab-content no-padding">
        <!-- ************** general *************-->
        <div class="tab-pane <?= $active == 1 ? 'active' : ''; ?>" id="manage">
            <div class="table-responsive">
                <table class="table table-striped DataTables " id="DataTables">
                    <thead>
                        <tr>
                            <th><?= lang('tax_rate_name') ?></th>
                            <th><?= lang('tax_rate_percent') ?></th>
                            <th class="col-options no-sort"><?= lang('action') ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $all_tax_rates = $this->db->get('tbl_tax_rates')->result();
                        if (!empty($all_tax_rates)) {
                            foreach ($all_tax_rates as $v_tax_rates) {
                                ?>
                                <tr>
                                    <td><?= $v_tax_rates->tax_rate_name ?></td>
                                    <td><?= $v_tax_rates->tax_rate_percent ?> %</td>

                                    <td>
                                        <?= btn_edit('admin/invoice/tax_rates/edit_tax_rates/' . $v_tax_rates->tax_rates_id) ?>
                                        <?= btn_delete('admin/invoice/tax_rates/delete_tax_rates/' . $v_tax_rates->tax_rates_id) ?>
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
            <form method="post" action="<?= base_url() ?>admin/invoice/save_tax_rate/<?php
            if (!empty($tax_rates_info)) {
                echo $tax_rates_info->tax_rates_id;
            }
            ?>" class="form-horizontal">    
                <div class="form-group">
                    <label class="col-lg-4 control-label"><?= lang('tax_rate_name') ?> <span class="text-danger">*</span></label>
                    <div class="col-lg-5">
                        <input type="text" class="form-control" required value="<?php
                        if (!empty($tax_rates_info)) {
                            echo $tax_rates_info->tax_rate_name;
                        }
                        ?>" name="tax_rate_name">
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-lg-4 control-label"><?= lang('tax_rate_percent') ?> <span class="text-danger">*</span></label>
                    <div class="col-lg-5">
                        <input type="text" class="form-control" required value="<?php
                        if (!empty($tax_rates_info)) {
                            echo $tax_rates_info->tax_rate_percent;
                        }
                        ?>" name="tax_rate_percent">
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-lg-4 control-label"></label>
                    <div class="col-lg-5">
                        <button type="submit" class="btn btn-primary"><?= lang('save_changes') ?></button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>