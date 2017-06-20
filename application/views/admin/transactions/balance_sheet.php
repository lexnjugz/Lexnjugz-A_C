<div class="box box-primary">
    <div class="box-header">
        <h3 class="box-title"><?= lang('balance_sheet') ?></h3>
    </div>
    <div class="box-body" >

        <div class="table-responsive">
            <table class="table table-striped DataTables " id="DataTables">
                <thead>
                    <tr>                            
                        <th><?= lang('account') ?></th>                            
                        <th><?= lang('balance') ?></th>                                                                                                                  
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $curency = $this->transactions_model->check_by(array('code' => config_item('currency')), 'tbl_currencies');
                    $total_amount = 0;
                    $all_account = $this->db->get('tbl_accounts')->result();
                    foreach ($all_account as $v_account):
                        ?>
                        <tr>
                            <td><?= $v_account->account_name ?></td>
                            <td><?= $v_account->balance ?></td>                            
                        </tr>
                        <?php
                        $total_amount+=$v_account->balance;
                    endforeach;
                    ?>
                    <tr class="custom-color-with-td">
                        <th style="text-align: right;" colspan="1"><strong><?= lang('total') ?>:</strong></th>
                        <td  ><strong><?= $curency->symbol . ' ' . number_format($total_amount, 2) ?></strong></td>                        
                    <tr>
                </tbody>
            </table>
        </div>
    </div>
</div>