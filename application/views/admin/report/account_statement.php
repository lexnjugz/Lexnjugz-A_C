<div class="box box-primary">
    <div class="box-header">
        <h3 class="box-title"><?= lang('account_statement') ?></h3>
    </div>
    <div class="box-body" >
        <form role="form" enctype="multipart/form-data" id="form" action="<?php echo base_url(); ?>admin/report/account_statement" method="post" class="form-horizontal  ">

            <div class="form-group">
                <label class="col-lg-3 control-label"><?= lang('account') ?> <span class="text-danger">*</span> </label>
                <div class="col-lg-5">
                    <select class="form-control select_box" style="width: 100%"  name="account_id"  required > 
                        <?php
                        $account_info = $this->db->get('tbl_accounts')->result();
                        if (!empty($account_info)) {
                            foreach ($account_info as $v_account) {
                                ?>
                                <option value="<?= $v_account->account_id ?>"
                                <?php
                                if (!empty($account_id)) {
                                    echo $account_id == $v_account->account_id ? 'selected' : '';
                                }
                                ?>
                                        ><?= $v_account->account_name ?></option>
                                        <?php
                                    }
                                }
                                ?>
                    </select> 
                </div>
            </div>
            <div class="form-group">
                <label class="col-lg-3 control-label"><?= lang('start_date') ?></label> 
                <div class="col-lg-5">
                    <div class="input-group">
                        <input type="text" name="start_date"  class="form-control datepicker" value="<?php
                        if (!empty($start_date)) {
                            echo $start_date;
                        } else {
                            echo date('Y-m-d');
                        }
                        ?>" data-date-format="<?= config_item('date_picker_format'); ?>">
                        <div class="input-group-addon">
                            <a href="#"><i class="entypo-calendar"></i></a>
                        </div>
                    </div>                        
                </div> 
            </div> 
            <div class="form-group">
                <label class="col-lg-3 control-label"><?= lang('end_date') ?></label> 
                <div class="col-lg-5">
                    <div class="input-group">
                        <input type="text" name="end_date"  class="form-control datepicker" value="<?php
                        if (!empty($end_date)) {
                            echo $end_date;
                        } else {
                            echo date('Y-m-d');
                        }
                        ?>" data-date-format="<?= config_item('date_picker_format'); ?>">
                        <div class="input-group-addon">
                            <a href="#"><i class="entypo-calendar"></i></a>
                        </div>
                    </div>                        
                </div> 
            </div> 
            <div class="form-group">
                <label class="col-lg-3 control-label"><?= lang('type') ?> <span class="text-danger">*</span> </label>
                <div class="col-lg-5">
                    <select class="form-control " name="transaction_type"  required > 
                        <option value="all_transactions" <?= (!empty($transaction_type) && $transaction_type == 'all_transactions' ? 'selected' : '') ?>><?= lang('all_transactions') ?></option>
                        <option value="debit" <?= (!empty($transaction_type) && $transaction_type == 'debit' ? 'selected' : '') ?>><?= lang('debit') ?></option>
                        <option value="credit" <?= (!empty($transaction_type) && $transaction_type == 'credit' ? 'selected' : '') ?>><?= lang('credit') ?></option>
                    </select> 
                </div>
            </div>

            <div class="form-group">
                <label class="col-lg-3 control-label"></label> 
                <div class="col-lg-5">
                    <button type="submit" class="btn btn-sm btn-primary"><i class="fa fa-check"></i> <?= lang('submit') ?></button>
                </div>
            </div>
        </form>
    </div>
</div>
<?php if (!empty($report)): ?>
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
                    <strong><?= lang('statement_report') ?></strong>

                    <?php
                    if (!empty($all_transaction_info)):
                        ?>
                        <div class="pull-right hidden-print">
                            <a href="<?php echo base_url() ?>admin/report/account_statement_pdf/<?= $account_id . '/' . date('Y-m-d', strtotime($start_date)) . '/' . date('Y-m-d', strtotime($end_date)) . '/' . $transaction_type ?>" class="btn btn-xs btn-success" data-toggle="tooltip" data-placement="top" title="<?= lang('pdf') ?>"><?= lang('pdf') ?></a>
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
                        $curency = $this->report_model->check_by(array('code' => config_item('currency')), 'tbl_currencies');
                        if (!empty($all_transaction_info)): foreach ($all_transaction_info as $v_transaction) :
                                $account_info = $this->report_model->check_by(array('account_id' => $v_transaction->account_id), 'tbl_accounts');
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

<?php endif; ?>
