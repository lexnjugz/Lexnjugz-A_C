<link href="<?php echo base_url() ?>asset/css/fullcalendar.css" rel="stylesheet" type="text/css" >
<style type="text/css">
    .datepicker{z-index:1151 !important;}   
    .easypiechart { 
        margin: 0px auto;
    }
</style>
<?php echo message_box('success'); ?>
<?php
$user_id = $this->session->userdata('user_id');

$client_id = $this->session->userdata('client_id');

$client_outstanding = $this->invoice_model->client_outstanding($user_id);

$client_payments = $this->invoice_model->get_sum('tbl_payments', 'amount', $array = array('paid_by' => $client_id));

$client_payable = $client_payments + $client_outstanding;

$client_currency = $this->invoice_model->client_currency_sambol($client_id);
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
$all_project = $this->db->where('client_id', $client_id)->get('tbl_project')->result();
?>
<?php if ($client_outstanding > 0) { ?>

    <div class="alert alert-danger">
        <button type="button" class="close" data-dismiss="alert">×</button> <i class="fa fa-warning"></i>
        <?= lang('your_balance_due') ?>: <?= $cur ?> <?= number_format($client_outstanding, 2) ?></strong>
    </div>
<?php } ?>
<div class="dashboard row" >
    <div class="container-fluid">                    
        <div class="row">
            <div class="col-md-9">

                <div class="row">
                    <div class="col-sm-6">
                        <section class="box box-success">
                            <header class="box-header with-border">
                                <h3 class="box-title"><?= lang('recently_paid_invoices') ?></h3>
                            </header>
                            <div class="panel-body inv-slim-scroll">
                                <div class="list-group bg-white" >
                                    <?php
                                    $recently_paid = $this->db
                                            ->order_by('created_date', 'desc')
                                            ->get('tbl_payments', 5)
                                            ->result();
                                    $total_amount = 0;
                                    if (!empty($recently_paid)) {

                                        foreach ($recently_paid as $key => $v_paid) {

                                            $invoices_info = $this->db->where(array('invoices_id' => $v_paid->invoices_id))->get('tbl_invoices')->row();
                                            if ($invoices_info->client_id == $client_id) {
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
                                                <a href="<?= base_url() ?>client/invoice/manage_invoice/invoice_details/<?= $v_paid->invoices_id ?>" class="list-group-item">
                                                    <?= $invoices_info->reference_no ?> - <small class="text-muted"><?= $cur ?> <?= $v_paid->amount ?> <span class="label label-<?= $label ?> pull-right"><?= $payment_method->method_name; ?></span></small>
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
                                        echo $cur . ' ' . number_format($total_amount, 2);
                                        ?>
                                    </strong></small>
                            </div>
                        </section>

                    </div>
                    <div class="col-sm-6">
                        <section class="box box-success">
                            <header class="box-header with-border">
                                <h3 class="box-title"><?= lang('payments') ?></h3>
                            </header>
                            <div class="panel-body text-center">
                                <h4><small> <?= lang('paid_amount') ?> : </small>
                                    <?= $cur ?>

                                    <?= number_format($this->invoice_model->get_sum('tbl_payments', 'amount', array('paid_by' => $client_id)), 2) ?> </h4>
                                <small class="text-muted text-center block">
                                    <?= lang('outstanding') ?> : <?= $cur ?> <?= number_format($client_outstanding, 2) ?>
                                </small>
                                <div class="inline">

                                    <div class="easypiechart" data-percent="<?= $perc_paid ?>" data-line-width="16" data-loop="false" data-size="188">

                                        <span class="h2 step"><?= $perc_paid ?></span>%
                                        <div class="easypie-text"><?= lang('paid') ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="panel-footer"><small><?= lang('invoice_amount') ?>: <strong><?= $cur ?>
                                        <?= number_format($client_payable, 2) ?></strong></small>
                            </div>
                        </section>

                    </div>

                </div>
            </div>
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
                        <a href="<?= base_url() ?>client/invoice/all_payments" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a>
                    </div><!-- /.info-box-content -->
                </div><!-- /.info-box -->                
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
                        <a href="<?= base_url() ?>client/invoice/all_payments" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a>
                    </div><!-- /.info-box-content -->
                </div><!-- /.info-box -->                
                <div class="info-box">
                    <span class="info-box-icon bg-purple"><i class="fa fa-envelope"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text"><?= lang('messages') ?></span>
                        <span class="info-box-number"><?= count($this->db->where(array('user_id' => $this->session->userdata('user_id'), 'deleted' => 'No'))->get('tbl_inbox')->result()); ?></span>
                        <a href="<?= base_url() ?>client/mailbox" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a>
                    </div><!-- /.info-box-content -->
                </div><!-- /.info-box -->                




                <!-- fix for small devices only -->
                <div class="clearfix visible-sm-block"></div>

                <div class="info-box">
                    <span class="info-box-icon bg-green"><i class="fa fa-suitcase "></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text"><?= lang('invoices') ?> </span>
                        <span class="info-box-number">
                            <?= count($this->db->where(array('status' => 'Unpaid', 'client_id' => $this->session->userdata('client_id')))->get('tbl_invoices')->result()); ?> </span>
                        <a href="<?= base_url() ?>client/invoice/manage_invoice" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a>
                    </div><!-- /.info-box-content -->
                </div><!-- /.info-box -->
                
                <div class="info-box">
                    <span class="info-box-icon bg-red"><i class="fa fa-folder-open-o"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text"><?= lang('project') ?></span>
                        <span class="info-box-number"><?= count($all_project); ?></span>
                        <a href="<?= base_url() ?>admin/project" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a>
                    </div><!-- /.info-box-content -->
                </div><!-- /.info-box -->


                <!-- Total Expense-->

                <div class="clearfix visible-sm-block"></div>


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
                                        $client_invoices = $this->db->where('client_id', $client_id)->get('tbl_invoices')->result();
                                        if (!empty($client_invoices)) {
                                            foreach ($client_invoices as $key => $invoice) {
                                                ?>
                                                <tr>
                                                    <td><a class="text-info" href="<?= base_url() ?>admin/invoice/manage_invoice/invoice_details/<?= $invoice->invoices_id ?>"><?= $invoice->reference_no ?></a></td>
                                                    <td><?= strftime(config_item('date_format'), strtotime($invoice->date_saved)); ?> </td>
                                                    <td><?= strftime(config_item('date_format'), strtotime($invoice->due_date)); ?> </td>
                                                    <td><small><?php $cur = $this->invoice_model->client_currency_sambol($invoice->client_id); ?><?= $cur->symbol ?></small> 
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
                        <div class="box box-success">
                            <div class="box-header with-border">
                                <div class="box-title">
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
            </div>
            <div class="wrap-fpanel col-sm-6 " style="margin-top: 20px;">  
                <div class="box box-success">
                    <div class="box-header">
                        <h3 class="box-title"><?= lang('recent_mail') ?></h3>
                        <span class="pull-right text-white">
                            <a href="<?php echo base_url() ?>client/mailbox" class=" view-all-front">View All</a></span>
                    </div>               
                    <div class="panel-body slim-scroll">
                        <form method="post" action="<?php echo base_url() ?>client/mailbox/delete_mail/inbox" >
                            <!-- Main content -->
                            <section class="content">                
                                <div class="box box-primary">                    
                                    <div class="box-body no-padding">
                                        <div class="mailbox-controls">

                                            <!-- Check all button -->
                                            <div class="mail_checkbox">
                                                <input type="checkbox" id="parent_present">
                                            </div>
                                            <div class="btn-group">
                                                <button class="btn btn-default btn-sm"><i class="fa fa-trash-o"></i></button>                                
                                            </div><!-- /.btn-group -->
                                            <a href="#" onClick="history.go(0)" class="btn btn-default btn-sm"><i class="fa fa-refresh"></i></a>      
                                            <a href="<?php echo base_url() ?>client/mailbox/index/compose" class="btn btn-danger">Compose +</a>                                
                                        </div>
                                        <br />
                                        <div class="table-responsive mailbox-messages slim-scroll">
                                            <table class="table table-hover table-striped">
                                                <tbody>

                                                    <?php
                                                    $get_inbox_message = $this->db
                                                            ->where(array('deleted' => 'no', 'to' => $this->session->userdata('email')))
                                                            ->order_by('message_time', 'desc')
                                                            ->get('tbl_inbox', 10)
                                                            ->result();
                                                    if (!empty($get_inbox_message)):foreach ($get_inbox_message as $v_inbox_msg):
                                                            ?>
                                                            <tr>                                                                                                                                
                                                                <td><input class="child_present" type="checkbox" name="selected_id[]" value="<?php echo $v_inbox_msg->inbox_id; ?>"/></td>

                                                                <td class="mailbox-star">
                                                                    <?php if ($v_inbox_msg->favourites == 1) { ?>
                                                                        <a href="<?php echo base_url() ?>client/mailbox/index/added_favourites/<?php echo $v_inbox_msg->inbox_id ?>/0"><i class="fa fa-star text-yellow"></i></a>
                                                                    <?php } else { ?>
                                                                        <a href="<?php echo base_url() ?>client/mailbox/index/added_favourites/<?php echo $v_inbox_msg->inbox_id ?>/1"><i class="fa fa-star-o text-yellow"></i></a>    
                                                                    <?php } ?>                                                
                                                                </td>                                                                                         
                                                                <td class="mailbox-name"><a href="<?php echo base_url() ?>client/mailbox/index/read_inbox_mail/<?php echo $v_inbox_msg->inbox_id ?>"><?php
                                                                        $string = (strlen($v_inbox_msg->to) > 13) ? substr($v_inbox_msg->to, 0, 10) . '...' : $v_inbox_msg->to;
                                                                        if ($v_inbox_msg->view_status == 1) {
                                                                            echo '<span style="color:#000">' . $string . '</span>';
                                                                        } else {
                                                                            echo '<b style="color:#000;font-size:13px;">' . $string . '</b>';
                                                                        }
                                                                        ?></a></td>
                                                                <td class="mailbox-subject" style="font-size:13px"><b class="pull-left"><?php
                                                                        $subject = (strlen($v_inbox_msg->subject) > 20) ? substr($v_inbox_msg->subject, 0, 15) . '...' : $v_inbox_msg->subject;
                                                                        echo $subject;
                                                                        ?> - &nbsp;</b> <span class="pull-left "> <?php
                                                                        $body = (strlen($v_inbox_msg->message_body) > 40) ? substr($v_inbox_msg->message_body, 0, 40) . '...' : $v_inbox_msg->message_body;
                                                                        echo $body;
                                                                        ?></span></td>                                                
                                                                <td style="font-size:13px">
                                                                    <?php
                                                                    //$oldTime = date('h:i:s', strtotime($v_inbox_msg->send_time));
                                                                    // Past time as MySQL DATETIME value
                                                                    $oldtime = date('Y-m-d H:i:s', strtotime($v_inbox_msg->message_time));

                                                                    // Current time as MySQL DATETIME value
                                                                    $csqltime = date('Y-m-d H:i:s');
                                                                    // Current time as Unix timestamp
                                                                    $ptime = strtotime($oldtime);
                                                                    $ctime = strtotime($csqltime);

                                                                    //Now calc the difference between the two
                                                                    $timeDiff = floor(abs($ctime - $ptime) / 60);

                                                                    //Now we need find out whether or not the time difference needs to be in
                                                                    //minutes, hours, or days
                                                                    if ($timeDiff < 2) {
                                                                        $timeDiff = "Just now";
                                                                    } elseif ($timeDiff > 2 && $timeDiff < 60) {
                                                                        $timeDiff = floor(abs($timeDiff)) . " minutes ago";
                                                                    } elseif ($timeDiff > 60 && $timeDiff < 120) {
                                                                        $timeDiff = floor(abs($timeDiff / 60)) . " hour ago";
                                                                    } elseif ($timeDiff < 1440) {
                                                                        $timeDiff = floor(abs($timeDiff / 60)) . " hours ago";
                                                                    } elseif ($timeDiff > 1440 && $timeDiff < 2880) {
                                                                        $timeDiff = floor(abs($timeDiff / 1440)) . " day ago";
                                                                    } elseif ($timeDiff > 2880) {
                                                                        $timeDiff = floor(abs($timeDiff / 1440)) . " days ago";
                                                                    }
                                                                    echo $timeDiff;
                                                                    ?>
                                                                </td>
                                                            </tr>                   
                                                        <?php endforeach; ?>
                                                    <?php else: ?>
                                                        <tr>
                                                            <td><strong>There is no email to display</strong></td>
                                                        </tr> 
                                                    <?php endif; ?>
                                                </tbody>
                                            </table><!-- /.table -->
                                        </div><!-- /.mail-box-messages -->
                                    </div><!-- /.box-body -->                        
                                </div><!-- /. box -->                    
                            </section><!-- /.content -->
                        </form>
                    </div>
                </div> 
            </div>
            <div class="wrap-fpanel col-sm-6 margin" style="margin-top: 20px;">  
                <section class="box box-success">
                    <header class="box-header with-border">
                        <h3 class="box-title"><?= lang('recent_activities') ?></h3></header>
                    <div class="panel-body">
                        <section class="comment-list block">
                            <section class="slim-scroll" data-height="400" data-disable-fade-out="true" data-distance="0" data-size="5px" data-color="#333333">
                                <?php
                                $activities = $this->db
                                        ->where('user', $user_id)
                                        ->order_by('activity_date', 'desc')
                                        ->get('tbl_activities', 50)
                                        ->result();
                                if (!empty($activities)) {
                                    foreach ($activities as $v_activities) {
                                        $profile_info = $this->db->where(array('user_id' => $v_activities->user))->get('tbl_account_details')->row();
                                        ?>
                                        <article id="comment-id-1" class="comment-item" style="font-size: 11px;">
                                            <div class="pull-left recect_task  ">
                                                <a class="pull-left recect_task  ">
                                                    <?php if (!empty($profile_info)) {
                                                        ?>
                                                        <img style="width: 30px;margin-left: 18px;
                                                             height: 29px;
                                                             border: 1px solid #aaa;" src="<?= base_url() . $profile_info->avatar ?>" class="img-circle">
                                                         <?php } ?>                                                    
                                                </a>
                                            </div>
                                            <section class="comment-body m-b-lg">
                                                <header class=" ">
                                                    <strong>
                                                        <?= $profile_info->fullname ?></strong>
                                                    <span class="text-muted text-xs"> <?php
                                                        $today = time();
                                                        $activity_day = strtotime($v_activities->activity_date);
                                                        echo $this->admin_model->get_time_different($today, $activity_day);
                                                        ?> <?= lang('ago') ?>
                                                    </span>
                                                </header>
                                                <div>                                                    
                                                    <?php echo sprintf(lang($v_activities->activity) . ' <strong style="color:#000"> <em>' . $v_activities->value1 . '</em>' . '<em>' . $v_activities->value2 . '</em></strong>'); ?>                                                                                                             
                                                </div>
                                                <hr/>
                                            </section>
                                        </article>


                                        <?php
                                    }
                                }
                                ?>
                            </section>
                        </section>
                    </div>
                </section>
            </div>                                                               
        </div>
    </div> 
</div>