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
    <div class="box box-primary">
        <div class="box-header">
            <div class="box-title">
                <h4><?= lang('income_expense_report') ?></h4>        
            </div>
            <div class="pull-right hidden-print">
                <a href="<?php echo base_url() ?>admin/report/income_expense_pdf/" class="btn btn-xs btn-success" data-toggle="tooltip" data-placement="top" title="<?= lang('pdf') ?>"><?= lang('pdf') ?></a>
                <a onclick="print_sales_report('printReport')" class="btn btn-xs btn-danger" data-toggle="tooltip" data-placement="top" title="<?= lang('print') ?>"><?= lang('print') ?></a>
            </div>
        </div>
        <div class="box-body">


            <h5><strong><?= lang('income_expense') ?></strong></h5>
            <?php
            $curency = $this->report_model->check_by(array('code' => config_item('currency')), 'tbl_currencies');
            $mdate = date('Y-m-d');
            //first day of month
            $first_day_month = date('Y-m-01');
            //first day of Weeks
            $this_week_start = date('Y-m-d', strtotime('previous sunday'));
            // 30 days before
            $before_30_days = date('Y-m-d', strtotime('today - 30 days'));

            $total_income = $this->db->select_sum('credit')->get('tbl_transactions')->row();
            $total_expense = $this->db->select_sum('debit')->get('tbl_transactions')->row();

            $income_this_month = $this->db->where(array('date >=' => $first_day_month, 'date <=' => $mdate))->select_sum('credit')->get('tbl_transactions')->row();
            $income_this_week = $this->db->where(array('date >=' => $this_week_start, 'date <=' => $mdate))->select_sum('credit')->get('tbl_transactions')->row();
            $income_this_30_days = $this->db->where(array('date >=' => $before_30_days, 'date <=' => $mdate))->select_sum('credit')->get('tbl_transactions')->row();

            $exoense_this_month = $this->db->where(array('date >=' => $first_day_month, 'date <=' => $mdate))->select_sum('debit')->get('tbl_transactions')->row();
            $exoense_this_week = $this->db->where(array('date >=' => $this_week_start, 'date <=' => $mdate))->select_sum('debit')->get('tbl_transactions')->row();
            $exoense_this_30_days = $this->db->where(array('date >=' => $before_30_days, 'date <=' => $mdate))->select_sum('debit')->get('tbl_transactions')->row();
            ?>
            <hr>
            <p><?= lang('total_income') ?>: <?= $curency->symbol . ' ' . number_format($total_income->credit, 2) ?></p>
            <p><?= lang('total_expense') ?>: <?= $curency->symbol . ' ' . number_format($total_expense->debit, 2) ?></p>
            <hr>
            <p><strong><?= lang('Income') ?> - <?= lang('Expense') ?> </strong>: <?= $curency->symbol . ' ' . number_format($total_income->credit - $total_expense->debit, 2) ?></p>            
            <hr>
            <p><?= lang('total_income_this_month') ?>:  <?= $curency->symbol . ' ' . number_format($income_this_month->credit, 2) ?></p>
            <p><?= lang('total_expense_this_month') ?>:  <?= $curency->symbol . ' ' . number_format($exoense_this_month->debit, 2) ?></p>
            <p><strong><?= lang('total') ?></strong>:  <?= $curency->symbol . ' ' . number_format($income_this_month->credit - $exoense_this_month->debit, 2) ?></p>
            <hr>
            <p><?= lang('total_income_this_week') ?>:  <?= $curency->symbol . ' ' . number_format($income_this_week->credit, 2) ?></p>
            <p><?= lang('total_expense_this_week') ?>:  <?= $curency->symbol . ' ' . number_format($exoense_this_week->debit, 2) ?></p>
            <p><strong><?= lang('total') ?></strong>:  <?= $curency->symbol . ' ' . number_format($income_this_week->credit - $exoense_this_week->debit, 2) ?></p>
            <hr>
            <p><?= lang('total_income_last_30') ?>:  <?= $curency->symbol . ' ' . number_format($income_this_30_days->credit, 2) ?></p>        
            <p><?= lang('total_expense_last_30') ?>:  <?= $curency->symbol . ' ' . number_format($exoense_this_30_days->debit, 2) ?></p>        
            <p><strong><?= lang('total') ?></strong>:  <?= $curency->symbol . ' ' . number_format($income_this_30_days->credit - $exoense_this_30_days->debit, 2) ?></p>
            <hr>
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
