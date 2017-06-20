<?= message_box('success') ?>
<section class="panel panel-default">
    <header class="panel-heading"><?= lang('all_payments') ?> </header>

    <div class="panel-body">
        <table class="table table-striped DataTables " id="DataTables">
            <thead>
                <tr>
                    <th><?= lang('payment_date') ?></th>
                    <th><?= lang('invoice_date') ?></th>
                    <th><?= lang('invoice') ?></th>
                    <th><?= lang('client') ?></th>
                    <th><?= lang('amount') ?></th>
                    <th><?= lang('payment_method') ?></th>
                    <th ><?= lang('action') ?></th>
                </tr>
            </thead>
            <tbody>
                <?php
                if (!empty($all_payments_info)) {
                    foreach ($all_payments_info as $v_payments_info) {
                        ?>
                        <tr>
                            <?php
                            $invoice_info = $this->invoice_model->check_by(array('invoices_id' => $v_payments_info->invoices_id), 'tbl_invoices');

                            $client_info = $this->invoice_model->check_by(array('client_id' => $invoice_info->client_id), 'tbl_client');
                            $payment_methods = $this->invoice_model->check_by(array('payment_methods_id' => $v_payments_info->payment_method), 'tbl_payment_methods');
                            ?>

                            <td><?= strftime(config_item('date_format'), strtotime($v_payments_info->payment_date)); ?></td>
                            <td><?= strftime(config_item('date_format'), strtotime($invoice_info->date_saved)) ?></td>
                            <td><a class="text-info" href="<?= base_url() ?>admin/invoice/manage_invoice/invoice_details/<?= $v_payments_info->invoices_id ?>"><?= $invoice_info->reference_no; ?></a></td>

                            <td><?= $client_info->name; ?></td>
                            <?php $currency = $this->invoice_model->client_currency_sambol($invoice_info->client_id); ?>
                            <td><?= $currency->symbol ?> <?= number_format($v_payments_info->amount, 2) ?></td>
                            <td><?= $payment_methods->method_name ?></td>
                            <td>
                                <?= btn_edit('admin/invoice/all_payments/' . $v_payments_info->payments_id) ?>                                                
                                <?= btn_view('admin/invoice/manage_invoice/payments_details/' . $v_payments_info->payments_id) ?>                                                
                                <?= btn_delete('admin/invoice/delete/delete_payment/' . $v_payments_info->payments_id) ?>                                                                                
                            </td>
                        </tr>
                        <?php
                    }
                }
                ?>
            </tbody>
        </table>
    </div>
</section>