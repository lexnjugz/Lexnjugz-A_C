<div id="printReport">
    <div class="show_print">
        <div style="width: 100%; border-bottom: 2px solid black;">
            <table style="width: 100%; vertical-align: middle;">
                <tr>                                        
                    <td style="width: 50px; border: 0px;">
                        <img style="width: 50px;height: 50px;margin-bottom: 5px;" src="<?= base_url() . config_item('company_logo') ?>" alt="" class="img-circle"/>
                    </td>

                    <td style="border: 0px;">
                        <p style="margin-left: 10px; font: 14px lighter;"><?= config_item('company_name') ?></p>
                    </td>

                </tr>
            </table>
        </div>
        <br/>
    </div>
    <div class="panel panel-default">
        <!-- Default panel contents -->
        <div class="panel-heading">
            <div class="panel-title">
                <strong><?= lang('transactions_report') ?></strong>

                <?php
                $all_transaction_info = $this->db->order_by('transactions_id','DESC')->get('tbl_transactions')->result();
                if (!empty($all_transaction_info)):
                    ?>
                    <div class="pull-right hidden-print">
                        <a href="<?php echo base_url() ?>admin/transactions/transactions_report_pdf/" class="btn btn-xs btn-success" data-toggle="tooltip" data-placement="top" title="<?= lang('pdf') ?>"><?= lang('pdf') ?></a>
                        <a onclick="print_sales_report('printReport')" class="btn btn-xs btn-danger" data-toggle="tooltip" data-placement="top" title="<?= lang('print') ?>"><?= lang('print') ?></a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
        <div class="table-responsive">
            <table class="table table-striped DataTables " id="DataTables">
                <thead>
                    <tr>
                        <th style="width: 15%"><?= lang('date') ?></th>
                        <th style="width: 15%"><?= lang('account') ?></th>
                        <th><?= lang('type') ?></th>
                        <th><?= lang('notes') ?></th>
                        <th><?= lang('amount') ?></th>
                        <th ><?= lang('credit') ?></th>
                        <th ><?= lang('debit') ?></th>
                        <th><?= lang('balance') ?></th>
                    </tr>
                </thead>
                <tbody>                        
                    <?php
                    $total_amount = 0;
                    $total_debit = 0;
                    $total_credit = 0;
                    $total_balance = 0;
                    $curency = $this->transactions_model->check_by(array('code' => config_item('currency')), 'tbl_currencies');
                    if (!empty($all_transaction_info)): foreach ($all_transaction_info as $v_transaction) :
                            $account_info = $this->transactions_model->check_by(array('account_id' => $v_transaction->account_id), 'tbl_accounts');
                            ?>
                            <tr class="custom-tr custom-font-print">
                                <td><?= strftime(config_item('date_format'), strtotime($v_transaction->date)); ?></td>
                                <td class="vertical-td"><?= $account_info->account_name ?></td>
                                <td class="vertical-td"><?= lang($v_transaction->type) ?> </td>
                                <td class="vertical-td"><?= $v_transaction->notes ?></td>
                                <td><?= $curency->symbol . ' ' . number_format($v_transaction->amount, 2) ?></td>                                                       
                                <td><?= $curency->symbol . ' ' . number_format($v_transaction->credit, 2) ?></td>                                                       
                                <td><?= $curency->symbol . ' ' . number_format($v_transaction->debit, 2) ?></td>                                                       
                                <td><?= $curency->symbol . ' ' . number_format($v_transaction->total_balance, 2) ?></td>      
                            </tr>
                            <?php
                            $total_amount +=$v_transaction->amount;
                            $total_debit +=$v_transaction->debit;
                            $total_credit +=$v_transaction->credit;
                            $total_balance +=$v_transaction->total_balance;
                            ?>
                        <?php endforeach; ?> 
                        <tr class="custom-color-with-td">
                            <td style="text-align: right;" colspan="4"><strong><?= lang('total') ?>:</strong></td>
                            <td  ><strong><?= $curency->symbol . ' ' . number_format($total_amount, 2) ?></strong></td>
                            <td ><strong><?= $curency->symbol . ' ' . number_format($total_credit, 2) ?></strong></td>
                            <td ><strong><?= $curency->symbol . ' ' . number_format($total_debit, 2) ?></strong></td>
                            <td ><strong><?= $curency->symbol . ' ' . number_format($total_balance, 2) ?></strong></td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<script type="text/javascript">

    function print_sales_report(printReport) {
        var printContents = document.getElementById(printReport).innerHTML;
        var originalContents = document.body.innerHTML;
        document.body.innerHTML = printContents;
        window.print();
        document.body.innerHTML = originalContents;
    }

</script>
