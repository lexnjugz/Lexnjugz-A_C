<?= message_box('success'); ?>
<div class="nav-tabs-custom">
    <!-- Tabs within a box -->
    <ul class="nav nav-tabs">
        <li class="<?= $active == 1 ? 'active' : ''; ?>"><a href="#manage" data-toggle="tab"><?= lang('all_estimates') ?></a></li>        
    </ul>
    <div class="tab-content no-padding">
        <!-- ************** general *************-->
        <div class="tab-pane <?= $active == 1 ? 'active' : ''; ?>" id="manage">
            <div class="table-responsive">
                <table class="table table-striped" id="DataTables">
                    <thead>
                        <tr>
                            <th><?= lang('estimate') ?></th>
                            <th><?= lang('created') ?></th>
                            <th><?= lang('due_date') ?></th>
                            <th><?= lang('client_name') ?></th>
                            <th><?= lang('amount') ?></th>
                            <th><?= lang('status') ?></th>
                            <th><?= lang('action') ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        if (!empty($all_estimates_info)) {
                            foreach ($all_estimates_info as $v_estimates) {
                                if ($v_estimates->status == 'Pending') {
                                    $label = "info";
                                } elseif ($v_estimates->status == 'Accepted') {
                                    $label = "success";
                                } else {
                                    $label = "danger";
                                }
                                ?>
                                <tr>                                    
                                    <td>
                                        <a class="text-info" href="<?= base_url() ?>client/estimates/index/estimates_details/<?= $v_estimates->estimates_id ?>"><?= $v_estimates->reference_no ?></a></td>
                                    <td><?= strftime(config_item('date_format'), strtotime($v_estimates->date_saved)) ?></td>
                                    <td><?= strftime(config_item('date_format'), strtotime($v_estimates->due_date)) ?></td>
                                    <?php
                                    $client_info = $this->estimates_model->check_by(array('client_id' => $v_estimates->client_id), 'tbl_client');
                                    if ($client_info->client_status == 1) {
                                        $status = 'Person';
                                    } else {
                                        $status = 'Company';
                                    }
                                    ?>
                                    <td><?= $client_info->name . ' (' . $status . ')'; ?></td>
                                    <?php $currency = $this->estimates_model->client_currency_sambol($v_estimates->client_id); ?>                                                                                                            
                                    <td><?= $currency->symbol ?> <?= number_format($this->estimates_model->estimate_calculation('estimate_amount', $v_estimates->estimates_id), 2) ?></td>
                                    <td><span class="label label-<?= $label ?>"><?= lang(strtolower($v_estimates->status)) ?></span></td>
                                    <td>                                        
                                        <div class="btn-group">
                                            <button class="btn btn-xs btn-default dropdown-toggle" data-toggle="dropdown">
                                                <?= lang('change_status') ?>
                                                <span class="caret"></span></button>
                                            <ul class="dropdown-menu">		
                                                <li><a href="<?= base_url() ?>client/estimates/change_status/declined/<?= $v_estimates->estimates_id ?>"><?= lang('declined') ?></a></li>
                                                <li><a href="<?= base_url() ?>client/estimates/change_status/accepted/<?= $v_estimates->estimates_id ?>"><?= lang('accepted') ?></a></li>                                    
                                            </ul>
                                        </div>
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

    </div>
</div>