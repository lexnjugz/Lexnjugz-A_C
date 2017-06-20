<div class="box box-primary">
    <div class="box-header">
        <h3 class="box-title"><?= lang('all_income') ?></h3>
    </div>
    <div class="box-body" >
        <div class="table-responsive">
            <table class="table table-striped DataTables " id="DataTables">
                <thead>
                    <tr>                            
                        <th><?= lang('date') ?></th>
                        <th><?= lang('account_name') ?></th>
                        <th class="col-date"><?= lang('notes') ?></th>                            
                        <th class="col-currency"><?= lang('amount') ?></th>                            
                        <th class="col-currency"><?= lang('credit') ?></th>                            
                        <th class="col-currency"><?= lang('debit') ?></th>                            
                        <th class="col-currency"><?= lang('balance') ?></th>                            
                        <th class="col-options no-sort" ><?= lang('action') ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $curency = $this->report_model->check_by(array('code' => config_item('currency')), 'tbl_currencies');
                    $total_amount = 0;
                    $total_credit = 0;
                    $total_debit = 0;
                    $total_balance = 0;
                    $all_expense_info = $this->db->where(array('type' => 'Income'))->order_by('transactions_id', 'DESC')->get('tbl_transactions')->result();
                    foreach ($all_expense_info as $v_expense) :
                        $account_info = $this->report_model->check_by(array('account_id' => $v_expense->account_id), 'tbl_accounts');
                        ?>
                        <tr>
                            <td><?= strftime(config_item('date_format'), strtotime($v_expense->date)); ?></td>
                            <td><?= $account_info->account_name ?></td>
                            <td><?= $v_expense->notes ?></td>
                            <td><?= $curency->symbol . ' ' . number_format($v_expense->amount, 2) ?></td>
                            <td><?= $curency->symbol . ' ' . number_format($v_expense->credit, 2) ?></td>
                            <td><?= $curency->symbol . ' ' . number_format($v_expense->debit, 2) ?></td>
                            <td><?= $curency->symbol . ' ' . number_format($v_expense->total_balance, 2) ?></td>
                            <td><?= btn_edit('admin/transactions/expense/' . $v_expense->transactions_id) ?>
                                <?= btn_delete('admin/transactions/delete_expense/' . $v_expense->transactions_id) ?></td>
                        </tr>
                        <?php
                        $total_amount +=$v_expense->amount;
                        $total_credit +=$v_expense->credit;
                        $total_debit +=$v_expense->debit;
                        $total_balance +=$v_expense->total_balance;
                        ?>
                        <?php
                    endforeach;
                    ?>
                    <tr class="custom-color-with-td">
                        <td style="text-align: right;" colspan="3"><strong><?= lang('total') ?>:</strong></td>
                        <td  ><strong><?= $curency->symbol . ' ' . number_format($total_amount, 2) ?></strong></td>
                        <td ><strong><?= $curency->symbol . ' ' . number_format($total_credit, 2) ?></strong></td>                    
                        <td ><strong><?= $curency->symbol . ' ' . number_format($total_debit, 2) ?></strong></td>                    
                        <td colspan="2"><strong><?= $curency->symbol . ' ' . number_format($total_balance, 2) ?></strong></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>