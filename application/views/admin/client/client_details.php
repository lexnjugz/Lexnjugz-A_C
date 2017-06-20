<?php

$all_project = $this->db->where('client_id', $client_details->client_id)->get('tbl_project')->result();
$client_outstanding = $this->invoice_model->client_outstanding($client_details->client_id);
$client_payments = $this->invoice_model->get_sum('tbl_payments', 'amount', $array = array('paid_by' => $client_details->client_id));
$client_payable = $client_payments + $client_outstanding;
$client_currency = $this->invoice_model->client_currency_sambol($client_details->client_id);
if (!empty($client_currency)) {
    $cur = $client_currency->symbol;
} else {
    $currency = $this->db->where(array('code' => config_item('default_currency')))->get('tbl_currencies')->row();
    $cur = $currency->symbol;
}
if ($client_payable > 0 AND $client_payments > 0) {
    $perc_paid = round(($client_payments / $client_payable) * 100, 1);
    if ($perc_paid > 100) {
        $perc_paid = '100';
    }
} else {
    $perc_paid = 0;
}
?>
<div class="row">
    <div class="col-md-3">
        <!-- Total Employee-->
        <div class="info-box">
            <span class="info-box-icon bg-yellogreen"><i class="fa fa-money"></i></span>
            <div class="info-box-content">
                <span class="info-box-text"><?= lang('paid_amount') ?></span>
                <span class="info-box-number"> <?php
                    if (!empty($client_payments)) {
                        echo $cur . ' ' . number_format($client_payments, 2);
                    } else {
                        echo '0.00';
                    }
                    ?> </span>
                <a href="<?= base_url() ?>admin/invoice/all_payments" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a>
            </div><!-- /.info-box-content -->
        </div><!-- /.info-box -->                
    </div>
    <div class="col-md-3">
        <div class="info-box">
            <span class="info-box-icon bg-blue"><i class="fa fa-usd"></i></span>
            <div class="info-box-content">
                <span class="info-box-text"><?= lang('due_amount') ?></span>
                <span class="info-box-number"> <?php
                    if ($client_outstanding > 0) {
                        echo $cur . ' ' . number_format($client_outstanding, 2);
                    } else {
                        echo '0.00';
                    }
                    ?> </span>
                <a href="<?= base_url() ?>admin/invoice/all_payments" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a>
            </div><!-- /.info-box-content -->
        </div><!-- /.info-box -->                
    </div>
    <div class="col-md-3">
        <div class="info-box">
            <span class="info-box-icon bg-red"><i class="fa fa-folder-open-o"></i></span>
            <div class="info-box-content">
                <span class="info-box-text"><?= lang('project') ?></span>
                <span class="info-box-number"><?= count($all_project); ?></span>
                <a href="<?= base_url() ?>admin/project" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a>
            </div><!-- /.info-box-content -->
        </div><!-- /.info-box -->                




        <!-- fix for small devices only -->
        <div class="clearfix visible-sm-block"></div>
    </div>
    <div class="col-md-3">
        <div class="info-box">
            <span class="info-box-icon bg-green"><i class="fa fa-suitcase "></i></span>
            <div class="info-box-content">
                <span class="info-box-text"><?= lang('invoices') ?> </span>
                <span class="info-box-number">
                    <?= count($this->db->where(array('status' => 'Unpaid', 'client_id' => $client_details->client_id))->get('tbl_invoices')->result()); ?> </span>
                <a href="<?= base_url() ?>admin/invoice/manage_invoice" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a>
            </div><!-- /.info-box-content -->
        </div><!-- /.info-box -->


        <!-- Total Expense-->

        <div class="clearfix visible-sm-block"></div>


    </div>
</div>
<div class="content-header">
    <a href="<?php echo base_url() ?>admin/client/new_client/<?= $client_details->client_id ?>" class="btn btn-sm btn-primary pull-right"><i class="fa fa-edit"></i> <?= lang('edit') ?></a>
    <p style="font-size: 20px"><strong><?= $client_details->name ?> - <?= lang('details') ?> </strong></p>
</div>
<section class="panel panel-default">	
    <span class="text-danger"><?= $this->session->flashdata('form_errors') ?></span>

    <div class="panel-body">	
        <!-- Details START -->
        <div class="col-md-6">
            <div class="group">
                <h4 class="subdiv text-muted"><?= lang('contact_details') ?></h4>
                <div class="row inline-fields">
                    <div class="col-md-4"><?= lang('name') ?></div>
                    <div class="col-md-6"><?= $client_details->name ?></div>
                </div>
                <div class="row inline-fields">
                    <div class="col-md-4"><?= lang('contact_person') ?></div>
                    <div class="col-md-6">
                        <?php
                        if ($client_details->primary_contact != 0) {
                            $contacts = $client_details->primary_contact;
                        } else {
                            $contacts = NULL;
                        }
                        $primary_contact = $this->client_model->check_by(array('account_details_id' => $contacts), 'tbl_account_details');
                        if ($primary_contact) {
                            echo $primary_contact->fullname;
                        }
                        ?>
                    </div>
                </div>
                <div class="row inline-fields">
                    <div class="col-md-4"><?= lang('email') ?></div>
                    <div class="col-md-6"><?= $client_details->email ?></div>
                </div>
            </div>

            <div class="row inline-fields">
                <div class="col-md-4"><?= lang('city') ?></div>
                <div class="col-md-6"><?= $client_details->city ?></div>
            </div>
            <div class="row inline-fields">
                <div class="col-md-4"><?= lang('country') ?></div>
                <div class="col-md-6 text-success"><?= $client_details->country ?></div>
            </div>									
        </div>
        <div class="col-md-6">
            <div class="group">
                <div class="row" style="margin-top: 5px">
                    <div class="rec-pay col-md-12">
                        <h4 class="subdiv text-muted"><?= lang('received_amount') ?></h4>
                        <h3 class="amount text-danger cursor-pointer"><strong>
                                <?php
                                $get_curency = $this->client_model->check_by(array('client_id' => $client_details->client_id), 'tbl_client');
                                $curency = $this->client_model->check_by(array('code' => $get_curency->currency), 'tbl_currencies');
                                ?><?= $curency->symbol ?>
                                <?= number_format($this->client_model->client_paid($client_details->client_id), 2) ?>
                            </strong></h3>
                        <div class="row inline-fields">
                            <div class="col-md-4"><?= lang('address') ?></div>
                            <div class="col-md-6"><?= $client_details->address ?></div>
                        </div>
                        <div class="row inline-fields">
                            <div class="col-md-4"><?= lang('phone') ?></div>
                            <div class="col-md-6"><a href="tel:<?= $client_details->phone ?>"><?= $client_details->phone ?></a></div>
                        </div>
                        <div class="row inline-fields">
                            <div class="col-md-4"><?= lang('website') ?></div>
                            <div class="col-md-6"><a href="<?= $client_details->website ?>" class="text-info" target="_blank"><?= $client_details->website ?></a></div>
                        </div>


                    </div>
                </div>
            </div>
        </div>

        <!-- Details END -->
    </div>

    <div class="panel-body">
        <div class="col-md-12">            
            <section class="box box-success">
                <div class="box-header with-border">
                    <div class="box-title">                        
                        <?= lang('contacts') ?>
                    </div>                 
                    <a href="<?= base_url() ?>admin/client/add_contacts/<?= $client_details->client_id ?>" class=" pull-right" ><?= lang('add_contact') ?></a>
                </div>
                <div class="panel-body">
                    <table class="table" id="DataTables">
                        <thead>
                            <tr>                                                    
                                <th><?= lang('full_name') ?></th>
                                <th><?= lang('email') ?></th>
                                <th><?= lang('phone') ?> </th>
                                <th><?= lang('mobile') ?> </th>
                                <th><?= lang('skype_id') ?></th>
                                <th class="col-date"><?= lang('last_login') ?> </th>
                                <th ><?= lang('action') ?></th>
                            </tr> </thead> <tbody>
                            <?php
                            if (!empty($client_contacts)) {
                                foreach ($client_contacts as $key => $contact) {
                                    ?>
                                    <tr>                                                            
                                        <td><?= $contact->fullname ?></td>
                                        <td class="text-info" ><?= $contact->email ?> </td>
                                        <td><a href="tel:<?= $contact->phone ?>"><?= $contact->phone ?></a></td>
                                        <td><a href="tel:<?= $contact->mobile ?>"><?= $contact->mobile ?></a></td>
                                        <td><a href="skype:<?= $contact->skype ?>?call"><?= $contact->skype ?></a></td>
                                        <?php
                                        if ($contact->last_login == '0000-00-00 00:00:00') {
                                            $login_time = "-";
                                        } else {
                                            $login_time = strftime(config_item('date_format') . " %H:%M:%S", strtotime($contact->last_login));
                                        }
                                        ?>
                                        <td><?= $login_time ?> </td>				
                                        <td>  
                                            <a href="<?= base_url() ?>admin/client/make_primary/<?= $contact->user_id ?>/<?= $client_details->client_id ?>" data-toggle="tooltip"  class="btn <?php
                                            if ($client_details->primary_contact == $contact->user_id) {
                                                echo "btn-success";
                                            } else {
                                                echo "btn-default";
                                            }
                                            ?> btn-xs " title="<?= lang('primary_contact') ?>" >
                                                <i class="fa fa-chain"></i> </a>
                                            <a href="<?= base_url() ?>admin/client/add_contacts/<?= $client_details->client_id . '/' . $contact->user_id ?>" class="btn btn-primary btn-xs" title="<?= lang('edit') ?>"  >
                                                <i class="fa fa-edit"></i> </a>
                                            <a href="<?= base_url() ?>admin/client/delete_contacts/<?= $client_details->client_id . '/' . $contact->user_id ?>" class="btn btn-danger btn-xs" title="<?= lang('delete') ?>" >
                                                <i class="fa fa-trash-o"></i> </a>
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
        </div>
        <div class="row">
            <div class="col-md-12">
                <!-- Client Invoices -->
                <div class="col-md-6">
                    <section class="box box-success">
                        <div class="box-header with-border">
                            <div class="box-title">                        
                                <?= lang('invoices') ?> 
                            </div>
                        </div>
                        <div class="box-body">
                            <table  class="table" id="DataTables">
                                <thead>
                                    <tr>
                                        <th><?= lang('reference_no') ?></th>
                                        <th><?= lang('date_issued') ?></th>
                                        <th><?= lang('due_date') ?> </th>
                                        <th class="col-currency"><?= lang('amount') ?> </th>
                                    </tr> </thead> <tbody>
                                    <?php
                                    setlocale(LC_ALL, config_item('locale') . ".UTF-8");
                                    if (!empty($client_invoices)) {
                                        foreach ($client_invoices as $key => $invoice) {
                                            ?>
                                            <tr>
                                                <td><a class="text-info" href="<?= base_url() ?>admin/invoice/manage_invoice/invoice_details/<?= $invoice->invoices_id ?>"><?= $invoice->reference_no ?></a></td>
                                                <td><?= strftime(config_item('date_format'), strtotime($invoice->date_saved)); ?> </td>
                                                <td><?= strftime(config_item('date_format'), strtotime($invoice->due_date)); ?> </td>
                                                <td><small><?php $cur = $this->client_model->client_currency_sambol($invoice->client_id); ?><?= $cur->symbol ?></small> 
                                                    <?= number_format($this->client_model->invoice_payable($invoice->invoices_id), 2) ?> </td>
                                            </tr>
                                            <?php
                                        }
                                    }
                                    ?>
                                </tbody>
                            </table>
                        </div>
                    </section>            
                </div>
                <div class="col-sm-6">
                    <section class="box box-success">
                        <div class="box-header with-border">
                            <div class="box-title"><?= lang('recently_paid_invoices') ?></div>
                        </div>
                        <div class="panel-body inv-slim-scroll">
                            <div class="list-group bg-white" >
                                <?php
                                $cur = $this->client_model->client_currency_sambol($client_details->client_id);
                                $recently_paid = $this->db
                                        ->order_by('created_date', 'desc')
                                        ->get('tbl_payments', 5)
                                        ->result();
                                $total_amount = 0;
                                if (!empty($recently_paid)) {
                                    foreach ($recently_paid as $key => $v_paid) {

                                        $invoices_info = $this->db->where(array('invoices_id' => $v_paid->invoices_id))->get('tbl_invoices')->row();
                                        if ($invoices_info->client_id == $client_details->client_id) {
                                            $payment_method = $this->db->where(array('payment_methods_id' => $v_paid->payment_method))->get('tbl_payment_methods')->row();

                                            if ($v_paid->payment_method == '1') {
                                                $label = 'success';
                                            } elseif ($v_paid->payment_method == '2') {
                                                $label = 'danger';
                                            } else {
                                                $label = 'dark';
                                            }
                                            $total_amount+=$v_paid->amount;
                                            ?>
                                            <a href="<?= base_url() ?>admin/invoice/manage_invoice/invoice_details/<?= $v_paid->invoices_id ?>" class="list-group-item">
                                                <?= $invoices_info->reference_no ?> - <small class="text-muted"><?= $cur->symbol ?> <?= $v_paid->amount ?> <span class="label label-<?= $label ?> pull-right"><?= $payment_method->method_name; ?></span></small>
                                            </a>
                                            <?php
                                        }
                                    }
                                }
                                ?>
                            </div>
                        </div>
                        <div class="panel-footer">
                            <small><?= lang('total_receipts') ?>: <strong>
                                    <?php
                                    echo $cur->symbol . ' ' . number_format($total_amount, 2);
                                    ?>
                                </strong></small>
                        </div>
                    </section>

                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <!-- Client Projects -->
                <div class="col-md-6">
                    <section class="box box-success">
                        <div class="box-header with-border">
                            <div class="box-title">                        
                                <?= lang('transactions') ?> 
                            </div>
                        </div>
                        <div class="panel-body">
                            <table class="table" id="DataTables">
                                <thead>
                                    <tr>
                                        <th><?= lang('date') ?></th>
                                        <th><?= lang('account') ?></th>
                                        <th><?= lang('type') ?> </th>                                
                                        <th><?= lang('amount') ?> </th>                                
                                        <th><?= lang('action') ?> </th>
                                    </tr> 
                                </thead> 
                                <tbody>
                                    <?php
                                    $curency = $this->client_model->check_by(array('code' => config_item('currency')), 'tbl_currencies');
                                    if (!empty($client_transactions)):foreach ($client_transactions as $v_transactions) :
                                            $account_info = $this->client_model->check_by(array('account_id' => $v_transactions->account_id), 'tbl_accounts');
                                            ?>
                                            <tr>
                                                <td><?= strftime(config_item('date_format'), strtotime($v_transactions->date)); ?></td>
                                                <td><?= $account_info->account_name ?></td>
                                                <td><?= $v_transactions->type ?></td>                                                                                
                                                <td><?= $curency->symbol . ' ' . number_format($v_transactions->amount, 2) ?></td>                                
                                                <td>
                                                    <?php
                                                    if ($v_transactions->type == 'Income') {
                                                        ?>
                                                        <?= btn_edit('admin/transactions/deposit/' . $v_transactions->transactions_id) ?>
                                                        <?= btn_delete('admin/transactions/delete_deposit/' . $v_transactions->transactions_id) ?>
                                                        <?php
                                                    } else {
                                                        ?>                                            
                                                        <?= btn_edit('admin/transactions/expense/' . $v_transactions->transactions_id) ?>
                                                        <?= btn_delete('admin/transactions/delete_expense/' . $v_transactions->transactions_id) ?>
                                                    <?php } ?>
                                                </td>
                                            </tr>                                    
                                            <?php
                                        endforeach;
                                        ?>
                                        <tr class="custom-color-with-td">
                                            <td style="text-align: right;" ><strong><?= lang('total_income') ?>:</strong></td>
                                            <td><strong class="label label-success"><?= $curency->symbol . ' ' . number_format($total_income->credit, 2) ?></strong></td>                                                                

                                            <td style="text-align: right;" ><strong><?= lang('total_expense') ?>:</strong></td>
                                            <td ><strong class="label label-danger"><?= $curency->symbol . ' ' . number_format($total_expense->debit, 2) ?></strong></td>                                                                
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </section>
                </div>
                <div class="col-sm-6">
                    <section class="box box-success">
                        <header class="box-header with-border">
                            <h3 class="box-title"><?= lang('payments') ?></h3>
                        </header>
                        <div class="panel-body text-center block">
                            <h4><small> <?= lang('paid_amount') ?> : </small>
                                <?= $cur->symbol ?>

                                <?= number_format($this->client_model->get_sum('tbl_payments', 'amount', array('paid_by' => $client_details->client_id)), 2) ?> </h4>
                            <small class="text-muted ">
                                <?= lang('outstanding') ?> : <?= $cur->symbol ?> <?= number_format($client_outstanding, 2) ?>
                            </small>
                            <div class="inline ">

                                <div class="easypiechart" style="margin: 0 auto" data-percent="<?= $perc_paid ?>" data-line-width="16" data-loop="false" data-size="188">

                                    <span class="h2 step"><?= $perc_paid ?></span>%
                                    <div class="easypie-text"><?= lang('paid') ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="panel-footer"><small><?= lang('invoice_amount') ?>: <strong><?= $cur->symbol ?>
                                    <?= number_format($client_payable, 2) ?></strong></small>
                        </div>
                    </section>

                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-12">
                <div class="box box-success">
                    <div class="panel-heading">
                        <div class="panel-title">
                            <?= lang('all_project') ?>
                        </div>
                    </div>
                    <div class="box-body">
                        <div class="table-responsive">
                            <table class="table table-striped DataTables " id="DataTables">
                                <thead>
                                    <tr>                            
                                        <th><?= lang('project_name') ?></th>                                                                                                                         
                                        <th><?= lang('start_date') ?></th>                            
                                        <th><?= lang('end_date') ?></th>                            
                                        <th><?= lang('status') ?></th>                            
                                        <th class="col-options no-sort" ><?= lang('action') ?></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    if (!empty($all_project)):foreach ($all_project as $v_project):
                                            ?>
                                            <tr>                                                                    
                                                <td><a class="text-info" href="<?= base_url() ?>client/project/project_details/<?= $v_project->project_id ?>"><?= $v_project->project_name ?></a>
                                                    <?php if (time() > strtotime($v_project->end_date) AND $v_project->progress < 100) { ?>
                                                        <span class="label label-danger pull-right"><?= lang('overdue') ?></span>
                                                    <?php } ?>

                                                    <div class="progress progress-xs progress-striped active">
                                                        <div class="progress-bar progress-bar-<?php echo ($v_project->progress >= 100 ) ? 'success' : 'primary'; ?>" data-toggle="tooltip" data-original-title="<?= $v_project->progress ?>%" style="width: <?= $v_project->progress; ?>%"></div>
                                                    </div>

                                                </td>                                
                                                <td><?= strftime(config_item('date_format'), strtotime($v_project->start_date)) ?></td>
                                                <td><?= strftime(config_item('date_format'), strtotime($v_project->end_date)) ?></td>

                                                <td><?php
                                                    if (!empty($v_project->project_status)) {
                                                        if ($v_project->project_status == 'completed') {
                                                            $status = "<span class='label label-success'>" . lang($v_project->project_status) . "</span>";
                                                        } elseif ($v_project->project_status == 'in_progress') {
                                                            $status = "<span class='label label-primary'>" . lang($v_project->project_status) . "</span>";
                                                        } elseif ($v_project->project_status == 'cancel') {
                                                            $status = "<span class='label label-danger'>" . lang($v_project->project_status) . "</span>";
                                                        } else {
                                                            $status = "<span class='label label-warning'>" . lang($v_project->project_status) . "</span>";
                                                        }
                                                        echo $status;
                                                    }
                                                    ?>      </td>     
                                                <td>
                                                    <?= btn_view('client/project/project_details/' . $v_project->project_id) ?>                                                                        
                                                </td>
                                            </tr>
                                            <?php
                                        endforeach;
                                    endif;
                                    ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

            </div>
        </div>
        <!-- End -->
    </div>
</section>                
