<?= message_box('success'); ?>
<?= message_box('error'); ?>
<div class="nav-tabs-custom">
    <!-- Tabs within a box -->
    <ul class="nav nav-tabs">
        <li class="<?= $active == 1 ? 'active' : ''; ?>"><a href="#manage" data-toggle="tab"><?= lang('manage_account') ?></a></li>
        <li class="<?= $active == 2 ? 'active' : ''; ?>"><a href="#create" data-toggle="tab"><?= lang('new_account') ?></a></li>                                                                     
    </ul>
    <div class="tab-content no-padding">
        <!-- ************** general *************-->
        <div class="tab-pane <?= $active == 1 ? 'active' : ''; ?>" id="manage">

            <div class="table-responsive">
                <table class="table table-striped DataTables " id="DataTables">
                    <thead>
                        <tr>                            
                            <th><?= lang('account') ?></th>                            
                            <th><?= lang('description') ?></th>                            
                            <th class="col-options no-sort" ><?= lang('action') ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $all_account = $this->db->get('tbl_accounts')->result();
                        $currency = $this->db->where('code', config_item('default_currency'))->get('tbl_currencies')->row();
                        $total_balance = 0;
                        foreach ($all_account as $v_account):
                            $total_balance += $v_account->balance;
                            ?>
                            <tr>
                                <td><?= $v_account->account_name ?></td>
                                <td><?= $v_account->description ?></td>
                                <td><?= $currency->symbol . ' ' . number_format($v_account->balance, 2); ?></td>     
                                <td>
                                    <?= btn_edit('admin/account/manage_account/' . $v_account->account_id) ?>
                                    <?= btn_delete('admin/account/delete_account/' . $v_account->account_id) ?>
                                </td>
                            </tr>
                            <?php
                        endforeach;
                        ?>
                        <tr class="total_amount">                            
                            <td colspan="2" style="text-align: right;"><strong><?= lang('total_amount') ?> : </strong></td>
                            <td colspan="2" style="padding-left: 8px;"><strong><?= $currency->symbol . ' ' . number_format($total_balance, 2); ?></strong></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>        
        <div class="tab-pane <?= $active == 2 ? 'active' : ''; ?>" id="create">
            <form role="form" enctype="multipart/form-data" id="form" action="<?php echo base_url(); ?>admin/account/save_account/<?php
            if (!empty($account_info)) {
                echo $account_info->account_id;
            }
            ?>" method="post" class="form-horizontal  ">
                <div class="form-group">
                    <label class="col-lg-3 control-label"><?= lang('account_name') ?> <span class="text-danger">*</span></label>
                    <div class="col-lg-5">
                        <input type="text" class="form-control" value="<?php
                        if (!empty($account_info)) {
                            echo $account_info->account_name;
                        }
                        ?>" name="account_name" required="">
                    </div>

                </div>               
                <!-- End discount Fields -->               
                <div class="form-group terms">
                    <label class="col-lg-3 control-label"><?= lang('description') ?> </label>
                    <div class="col-lg-5">
                        <textarea name="description" class="form-control"><?php
                            if (!empty($account_info)) {
                                echo $account_info->description;
                            }
                            ?></textarea>                        
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-lg-3 control-label"><?= lang('initial_balance') ?> <span class="text-danger">*</span></label>
                    <div class="col-lg-5">
                        <input type="number" class="form-control" value="<?php
                        if (!empty($account_info)) {
                            echo $account_info->balance;
                        }
                        ?>" name="balance" required="">
                    </div>

                </div> 
                <div class="form-group">
                    <label class="col-lg-3 control-label"></label> 
                    <div class="col-lg-5">
                        <button type="submit" class="btn btn-sm btn-primary"><?= lang('create_acount') ?></button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- end -->