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
                <strong><?= lang('transfer_report') ?></strong>
                <?php
                $all_transaction_info = $this->db->order_by('transfer_id', 'DESC')->get('tbl_transfer')->result();
                if (!empty($all_transaction_info)):
                    ?>
                    <div class="pull-right hidden-print">
                        <a href="<?php echo base_url() ?>admin/transactions/transfer_report_pdf" class="btn btn-xs btn-success" data-toggle="tooltip" data-placement="top" title="<?= lang('pdf') ?>"><?= lang('pdf') ?></a>
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
                        <th style="width: 15%"><?= lang('from_account') ?></th>
                        <th style="width: 15%"><?= lang('to_account') ?></th>
                        <th><?= lang('type') ?></th>
                        <th><?= lang('notes') ?></th>
                        <th><?= lang('amount') ?></th>                    
                    </tr>
                </thead>
                <tbody>                        
                    <?php
                    $curency = $this->transactions_model->check_by(array('code' => config_item('currency')), 'tbl_currencies');
                    $total_amount = 0;
                    if (!empty($all_transaction_info)): foreach ($all_transaction_info as $v_transaction) :
                            $from_account_info = $this->transactions_model->check_by(array('account_id' => $v_transaction->from_account_id), 'tbl_accounts');
                            $to_account_info = $this->transactions_model->check_by(array('account_id' => $v_transaction->to_account_id), 'tbl_accounts');
                            ?>
                            <tr class="custom-tr custom-font-print">
                                <td><?= strftime(config_item('date_format'), strtotime($v_transaction->date)); ?></td>
                                <td class="vertical-td"><?= $from_account_info->account_name ?></td>
                                <td class="vertical-td"><?= $to_account_info->account_name ?></td>
                                <td class="vertical-td"><?= lang($v_transaction->type) ?> </td>
                                <td class="vertical-td"><?= $v_transaction->notes ?></td>                                      
                                <td><?= $curency->symbol . ' ' . number_format($v_transaction->amount, 2) ?></td>                                                                                   
                            </tr>
                            <?php
                            $total_amount +=$v_transaction->amount;
                            ?>
                        <?php endforeach; ?>                    
                        <tr class="custom-color-with-td">
                            <th style="text-align: right;" colspan="5"><strong><?= lang('total') ?>:</strong></th>
                            <td  ><strong><?= $curency->symbol . ' ' . number_format($total_amount, 2) ?></strong></td>                        
                        <tr>
                    </tbody>
                <?php endif; ?>
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
