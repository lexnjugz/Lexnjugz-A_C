<link href="<?php echo base_url() ?>asset/css/fullcalendar.css" rel="stylesheet" type="text/css" >
<style type="text/css">
    .datepicker{z-index:1151 !important;}   
</style>
<?php
echo message_box('success');
echo message_box('error');
$curency = $this->admin_model->check_by(array('code' => config_item('currency')), 'tbl_currencies');
?>

<div class="dashboard row" >
    <div class="container-fluid">                    
        <div class="row">            
            <div class="col-sm-3">
                <div class="info-box">
                    <span class="info-box-icon bg-sucees"><i class="fa fa-plus"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text"><?= lang('income_today') ?></span>
                        <span class="info-box-number">
                            <?php
                            if (!empty($today_income)) {
                                $today_income = $today_income;
                            } else {
                                $today_income = '0';
                            }
                            echo $curency->symbol . ' ' . number_format($today_income, 2);
                            ?>
                        </span>
                        <a href = "<?= base_url() ?>admin/transactions/deposit" class = "small-box-footer">More info <i class = "fa fa-arrow-circle-right"></i></a>
                    </div><!--/.info-box-content -->
                </div><!--/.info-box -->
            </div>
            <div class = "col-sm-3">
                <div class = "info-box">
                    <span class = "info-box-icon bg-yellow"><i class = "fa fa-minus"></i></span>
                    <div class = "info-box-content">
                        <span class = "info-box-text"><?= lang('expense_today') ?></span>
                        <span class="info-box-number">
                            <?php
                            if (!empty($today_expense)) {
                                $today_expense = $today_expense;
                            } else {
                                $today_expense = '0';
                            }
                            echo $curency->symbol . ' ' . number_format($today_expense, 2);
                            ?>
                        </span>
                        <a href="<?= base_url() ?>admin/transactions/expense" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a>
                    </div><!-- /.info-box-content -->
                </div><!-- /.info-box -->                
            </div>            
            <div class="col-sm-3">
                <div class="info-box">
                    <span class="info-box-icon bg-aqua"><i class="fa fa-ticket"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text"><?= lang('tickets') ?></span>
                        <span class="info-box-number"><?= count($this->db->get('tbl_tickets')->result()); ?></span>
                        <a href="<?= base_url() ?>admin/tickets" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a>
                    </div><!-- /.info-box-content -->
                </div><!-- /.info-box -->                
            </div>
            <div class="col-sm-3">
                <div class="info-box">
                    <span class="info-box-icon bg-red"><i class="fa fa-folder-open-o"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text"><?= lang('project') ?></span>
                        <span class="info-box-number"><?= count($this->db->get('tbl_project')->result()); ?></span>
                        <a href="<?= base_url() ?>admin/project" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a>
                    </div><!-- /.info-box-content -->
                </div><!-- /.info-box -->                
            </div>
            <!-- fix for small devices only -->

            <div class="clearfix visible-sm-block"></div>
            <div class="col-sm-9">   

                <div class="panel panel-default">
                    <div class="panel-heading" style="background: #D8D8D8;border: 1px solid #D8D8D8"></div>
                    <div id="calendar"></div>            
                </div>                    
            </div>                    
            <div class="col-md-3">                                                           
                <!-- Total Employee-->
                <div class="info-box">
                    <span class="info-box-icon bg-yellogreen"><i class="fa fa-money"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text"><?= lang('paid_amount') ?></span>
                        <span class="info-box-number"> <?php
                            if (!empty($invoce_total)) {
                                if (!empty($invoce_total['paid'])) {
                                    $paid = 0;
                                    foreach ($invoce_total['paid'] as $cur => $total) {
                                        $paid += $total;
                                    }
                                    echo $invoce_total['symbol'][$cur] . ' ' . number_format($paid, 2);
                                } else {
                                    echo '0.00';
                                }
                            } else {
                                echo '0.00';
                            }
                            ?> </span>
                        <a href="<?= base_url() ?>admin/invoice/all_payments" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a>
                    </div><!-- /.info-box-content -->
                </div><!-- /.info-box -->                
                <div class="info-box">
                    <span class="info-box-icon bg-blue"><i class="fa fa-usd"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text"><?= lang('due_amount') ?></span>
                        <span class="info-box-number"> <?php
                            if (!empty($invoce_total)) {
                                $total_due = 0;
                                if (!empty($invoce_total['due'])) {
                                    foreach ($invoce_total['due'] as $cur => $d_total) {
                                        $total_due += $d_total;
                                    } echo $invoce_total['symbol'][$cur] . ' ' . number_format($total_due, 2);
                                } else {
                                    echo '0.00';
                                }
                            } else {
                                echo '0.00';
                            }
                            ?> </span>
                        <a href="<?= base_url() ?>admin/invoice/manage_invoice" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a>
                    </div><!-- /.info-box-content -->
                </div><!-- /.info-box -->                
                <div class="info-box">
                    <span class="info-box-icon bg-purple"><i class="fa fa-envelope"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text"><?= lang('messages') ?></span>
                        <span class="info-box-number"><?= count($this->db->where(array('user_id' => $this->session->userdata('user_id'), 'deleted' => 'No'))->get('tbl_inbox')->result()); ?></span>
                        <a href="<?= base_url() ?>admin/mailbox" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a>
                    </div><!-- /.info-box-content -->
                </div><!-- /.info-box -->                


                <!-- fix for small devices only -->
                <div class="clearfix visible-sm-block"></div>

                <div class="info-box">
                    <span class="info-box-icon bg-green"><i class="fa fa-suitcase "></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text"><?= lang('invoices') ?> </span>
                        <span class="info-box-number">
                            <?= count($this->db->where(array('status' => 'Unpaid'))->get('tbl_invoices')->result()); ?> </span>
                        <a href="<?= base_url() ?>admin/invoice/manage_invoice" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a>
                    </div><!-- /.info-box-content -->
                </div><!-- /.info-box -->
                <!-- Total Expense-->
                <div class="info-box">
                    <span class="info-box-icon bg-sucees"><i class="fa fa-plus"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text"><?= lang('total_income') ?></span>
                        <span class="info-box-number"><?php
                            if (!empty($total_aincome)) {
                                $total_aincome = $total_aincome;
                            } else {
                                $total_aincome = '0';
                            }

                            echo $curency->symbol . ' ' . number_format($total_aincome, 2);
                            ?></span>
                        <a href="<?php echo base_url() ?>admin/transactions/deposit" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a>
                    </div>
                </div><!-- end Total Expense-->
                <div class="info-box">
                    <span class="info-box-icon bg-yellow"><i class="fa fa-minus"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text"><?= lang('total_expense') ?></span>
                        <span class="info-box-number"><?php
                            if (!empty($total_aexpense)) {
                                $total_aexpense = $total_aexpense;
                            } else {
                                $total_aexpense = '0';
                            }

                            echo $curency->symbol . ' ' . number_format($total_aexpense, 2);
                            ?></span>
                        <a href="<?php echo base_url() ?>admin/transactions/expense" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a>
                    </div>
                </div><!-- end Total Expense-->
                <div class="clearfix visible-sm-block"></div>


            </div>

            <div class="col-md-8" style="margin-top: 25px;">
                <section class="panel panel-default">
                    <aside class="nav-tabs-custom">                        
                        <ul class="nav nav-tabs">
                            <li class="active"><a href="#projects" data-toggle="tab"><?= lang('recent_projects') ?></a></li>
                            <li class=""><a href="#tasks" data-toggle="tab"><?= lang('recent_tasks') ?></a></li>                                
                            <li class=""><a href="#invoice" data-toggle="tab"><?= lang('recent_invoice') ?></a></li>
                            <li class=""><a href="#recent_leads" data-toggle="tab"><?= lang('recent_leads') ?></a></li>
                            <li class=""><a href="#recent_opportunities" data-toggle="tab"><?= lang('recent_opportunities') ?></a></li>
                        </ul>                        
                        <section class="scrollable">
                            <div class="tab-content">
                                <div class="tab-pane active" id="projects">
                                    <table class="table table-striped m-b-none text-sm">
                                        <thead>                                            
                                            <tr>          
                                                <th><?= lang('project_name') ?></th>                            
                                                <th><?= lang('client') ?></th>                                                                                                                              
                                                <th><?= lang('end_date') ?></th>                            
                                                <th><?= lang('status') ?></th>                                                                            
                                                <th class="col-options no-sort"><?= lang('action') ?></th>
                                            </tr>                                            
                                        </thead>
                                        <tbody>
                                            <?php
                                            $all_project = $this->db->get('tbl_project')->result();
                                            if (!empty($all_project)) {
                                                foreach ($all_project as $v_project):
                                                    ?>
                                                    <tr>                                    
                                                        <?php
                                                        $client_info = $this->db->where('client_id', $v_project->client_id)->get('tbl_client')->row();
                                                        if (!empty($client_info)) {
                                                            if ($client_info->client_status == 1) {
                                                                $status = 'Person';
                                                            } else {
                                                                $status = 'Company';
                                                            }
                                                            $name = $client_info->name . ' (' . $status . ')';
                                                        } else {
                                                            $name = '-';
                                                        }
                                                        ?>  
                                                        <td>
                                                            <a class="text-info" href="<?= base_url() ?>admin/project/project_details/<?= $v_project->project_id ?>"><?= $v_project->project_name ?></a>
                                                            <?php if (time() > strtotime($v_project->end_date) AND $v_project->progress < 100) { ?>
                                                                <span class="label label-danger pull-right"><?= lang('overdue') ?></span>
                                                            <?php } ?>

                                                            <div class="progress progress-xs progress-striped active">
                                                                <div class="progress-bar progress-bar-<?php echo ($v_project->progress >= 100) ? 'success' : 'primary'; ?>" data-toggle="tooltip" data-original-title="<?= $v_project->progress ?>%" style="width: <?= $v_project->progress; ?>%"></div>
                                                            </div>

                                                        </td>
                                                        <td><?= $name ?></td>

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
                                                            <?= btn_view(base_url() . 'admin/project/project_details/' . $v_project->project_id) ?>
                                                        </td>
                                                    </tr>
                                                    <?php
                                                endforeach;
                                            } else {
                                                ?>
                                                <tr>
                                                    <td colspan="4"><?= lang('nothing_to_display') ?></td>
                                                </tr>
                                            <?php } ?>
                                        </tbody>
                                    </table>
                                </div>                                
                                <div class="tab-pane" id="tasks">
                                    <table class="table table-striped m-b-none text-sm">
                                        <thead>
                                            <tr>                                                
                                                <th><?= lang('task_name') ?></th>
                                                <th><?= lang('end_date') ?></th>
                                                <th><?= lang('progress') ?></th>
                                                <th class="col-options no-sort col-md-1"><?= lang('action') ?></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            $task_info = $this->db->limit(5)->get('tbl_task')->result();
                                            if (!empty($task_info)):foreach ($task_info as $v_task):
                                                    ?>
                                                    <tr>                                                        
                                                        <td> <a class="text-info"style="<?php
                                                            if ($v_task->task_progress >= 100) {
                                                                echo 'text-decoration: line-through;';
                                                            }
                                                            ?>" href="<?= base_url() ?>admin/tasks/view_task_details/<?= $v_task->task_id ?>"><?php echo $v_task->task_name; ?></a></td>
                                                        <td><?php
                                                            $due_date = $v_task->due_date;
                                                            $due_time = strtotime($due_date);
                                                            $current_time = time();
                                                            ?>
                                                            <?= strftime(config_item('date_format'), strtotime($due_date)) ?>
                                                            <?php if ($current_time > $due_time && $v_task->task_progress < 100) { ?>
                                                                <span class="label label-danger"><?= lang('overdue') ?></span>
                                                            <?php } ?></td>  
                                                        <td>
                                                            <div class="inline ">
                                                                <div class="easypiechart text-success" style="margin: 0px;" data-percent="<?= $v_task->task_progress ?>" data-line-width="5" data-track-Color="#f0f0f0" data-bar-color="#<?php
                                                                if ($v_task->task_progress == 100) {
                                                                    echo '8ec165';
                                                                } else {
                                                                    echo 'fb6b5b';
                                                                }
                                                                ?>" data-rotate="270" data-scale-Color="false" data-size="50" data-animate="2000">
                                                                    <span class="small text-muted"><?= $v_task->task_progress ?>%</span>
                                                                </div>
                                                            </div>

                                                        </td>

                                                        <td><?= btn_view('admin/tasks/view_task_details/' . $v_task->task_id) ?></td>
                                                    </tr>
                                                <?php endforeach; ?>
                                            <?php else: ?>
                                                <tr>
                                                    <td colspan="4"><?= lang('nothing_to_display') ?></td>
                                                </tr>
                                            <?php endif; ?>
                                        </tbody>
                                    </table>
                                </div>                                
                                <div class="tab-pane" id="invoice">
                                    <table class="table table-striped m-b-none text-sm">
                                        <thead>
                                            <tr>                                                
                                                <th><?= lang('invoice') ?></th>
                                                <th class="col-date"><?= lang('due_date') ?></th>
                                                <th><?= lang('client_name') ?></th>                                                
                                                <th class="col-currency"><?= lang('due_amount') ?></th>
                                                <th><?= lang('status') ?></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            $all_invoices_info = $this->db->limit(5)->get('tbl_invoices')->result();
                                            if (!empty($all_invoices_info)) {
                                                foreach ($all_invoices_info as $v_invoices) {

                                                    if ($this->invoice_model->get_payment_status($v_invoices->invoices_id) == lang('fully_paid')) {
                                                        $invoice_status = lang('fully_paid');
                                                        $label = "success";
                                                    } elseif ($v_invoices->emailed == 'Yes') {
                                                        $invoice_status = lang('sent');
                                                        $label = "info";
                                                    } else {
                                                        $invoice_status = lang('draft');
                                                        $label = "default";
                                                    }
                                                    ?>
                                                    <tr>
                                                        <td><a class="text-info" href="<?= base_url() ?>admin/invoice/manage_invoice/invoice_details/<?= $v_invoices->invoices_id ?>"><?= $v_invoices->reference_no ?></a></td>
                                                        <td><?= strftime(config_item('date_format'), strtotime($v_invoices->due_date)) ?></td>
                                                        <?php
                                                        $client_info = $this->invoice_model->check_by(array('client_id' => $v_invoices->client_id), 'tbl_client');

                                                        if ($client_info->client_status == 1) {
                                                            $status = 'Person';
                                                        } else {
                                                            $status = 'Company';
                                                        }
                                                        ?>
                                                        <td><?= $client_info->name . ' (' . $status . ')'; ?></td>
                                                        <?php $currency = $this->invoice_model->client_currency_sambol($v_invoices->client_id); ?>                                                        
                                                        <td><?= $currency->symbol ?> <?= number_format($this->invoice_model->calculate_to('invoice_due', $v_invoices->invoices_id), 2) ?></td>
                                                        <td><span class="label label-<?= $label ?>"><?= $invoice_status ?></span>
                                                            <?php if ($v_invoices->recurring == 'Yes') { ?>
                                                                <span  data-toggle="tooltip" data-placement="top" title="<?= lang('recurring') ?>" class="label label-primary"><i class="fa fa-retweet"></i></span>
                                                            <?php } ?>

                                                        </td>                                                        

                                                    </tr>
                                                    <?php
                                                }
                                            } else {
                                                ?>
                                                <tr>
                                                    <td colspan="4"><?= lang('nothing_to_display') ?></td>
                                                </tr>
                                            <?php } ?>
                                        </tbody>
                                    </table>
                                </div>                                
                                <div class="tab-pane" id="recent_leads">
                                    <table class="table table-striped m-b-none text-sm">
                                        <thead>
                                            <tr>                            
                                                <th><?= lang('lead_name') ?></th>                            
                                                <th><?= lang('contact_name') ?></th>                            
                                                <th><?= lang('email') ?></th>                                                                                                   
                                                <th><?= lang('lead_status') ?></th>                                                                            
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            $all_leads = $this->db->limit(5)->get('tbl_leads')->result();
                                            if (!empty($all_leads)) {
                                                foreach ($all_leads as $v_leads) {
                                                    ?>
                                                    <tr>
                                                        <td><a href="<?= base_url() ?>admin/leads/leads_details/<?= $v_leads->leads_id ?>"><?= $v_leads->lead_name ?></a></td>
                                                        <td><?= $v_leads->contact_name ?></td>
                                                        <td><?= $v_leads->email ?></td>                                                        
                                                        <td><?php
                                                            if (!empty($v_leads->lead_status_id)) {
                                                                $lead_status = $this->db->where('lead_status_id', $v_leads->lead_status_id)->get('tbl_lead_status')->row();

                                                                if ($lead_status->lead_type == 'open') {
                                                                    $status = "<span class='label label-success'>" . lang($lead_status->lead_type) . "</span>";
                                                                } else {
                                                                    $status = "<span class='label label-warning'>" . lang($lead_status->lead_type) . "</span>";
                                                                }
                                                                echo $status . ' ' . $lead_status->lead_status;
                                                            }
                                                            ?>      </td>                                                             
                                                    </tr>
                                                    <?php
                                                }
                                            } else {
                                                ?>
                                                <tr>
                                                    <td colspan="4"><?= lang('nothing_to_display') ?></td>
                                                </tr>
                                            <?php } ?>
                                        </tbody>
                                    </table>
                                </div>                                
                                <div class="tab-pane" id="recent_opportunities">
                                    <table class="table table-striped m-b-none text-sm">
                                        <thead>
                                            <tr>                                                
                                                <th><?= lang('opportunity_name') ?></th>                                                        
                                                <th><?= lang('state') ?></th>                            
                                                <th><?= lang('stages') ?></th>                            
                                                <th><?= lang('expected_revenue') ?></th>                            
                                                <th><?= lang('next_action') ?></th>                            
                                                <th><?= lang('next_action_date') ?></th>  
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            $all_opportunity = $this->db->limit(5)->get('tbl_opportunities')->result();
                                            if (!empty($all_opportunity)) {
                                                foreach ($all_opportunity as $v_opportunity) {

                                                    $opportunities_state_info = $this->db->where('opportunities_state_reason_id', $v_opportunity->opportunities_state_reason_id)->get('tbl_opportunities_state_reason')->row();
                                                    if ($opportunities_state_info->opportunities_state == 'open') {
                                                        $label = 'primary';
                                                    } elseif ($opportunities_state_info->opportunities_state == 'won') {
                                                        $label = 'success';
                                                    } elseif ($opportunities_state_info->opportunities_state == 'suspended') {
                                                        $label = 'info';
                                                    } else {
                                                        $label = 'danger';
                                                    }
                                                    $currency = $this->db->where('code', config_item('default_currency'))->get('tbl_currencies')->row();
                                                    ?>
                                                    <tr>
                                                        <td>                                        
                                                            <a class="text-info" href="<?= base_url() ?>admin/opportunities/opportunity_details/<?= $v_opportunity->opportunities_id ?>"><?= $v_opportunity->opportunity_name ?></a>
                                                            <?php if (time() > strtotime($v_opportunity->close_date) AND $v_opportunity->probability < 100) { ?>
                                                                <span class="label label-danger pull-right"><?= lang('overdate') ?></span>
                                                            <?php } ?>

                                                            <div class="progress progress-xs progress-striped active">
                                                                <div class="progress-bar progress-bar-<?php echo ($v_opportunity->probability >= 100 ) ? 'success' : 'primary'; ?>" data-toggle="tooltip" data-original-title="<?= lang('probability') . ' ' . $v_opportunity->probability ?>%" style="width: <?= $v_opportunity->probability ?>%"></div>
                                                            </div>
                                                        </td>                                    
                                                        <td><?= lang($v_opportunity->stages) ?></td>
                                                        <td><span class="label label-<?= $label ?>"><?= lang($opportunities_state_info->opportunities_state) ?></span></td>
                                                        <td><?php
                                                            if (!empty($v_opportunity->expected_revenue)) {
                                                                echo $currency->symbol . ' ' . number_format($v_opportunity->expected_revenue, 2);
                                                            }
                                                            ?></td>     
                                                        <td><?= $v_opportunity->next_action ?></td>                                         
                                                        <td><?= strftime(config_item('date_format'), strtotime($v_opportunity->next_action_date)) ?></td>                                                        
                                                    </tr>
                                                    <?php
                                                }
                                            } else {
                                                ?>
                                                <tr>
                                                    <td colspan="4"><?= lang('nothing_to_display') ?></td>
                                                </tr>
                                            <?php } ?>
                                        </tbody>
                                    </table>
                                </div>                                
                            </div>
                        </section>
                    </aside>

                    <footer class="panel-footer bg-white no-padder">
                        <div class="row text-center no-gutter">
                            <div class="col-xs-3 b-r b-light">
                                <span class="h4 font-bold m-t block"><?= count($this->db->where('project_status', 'completed')->get('tbl_project')->result()) ?>
                                </span> <small class="text-muted m-b block"><?= lang('complete_projects') ?></small>
                            </div> 
                            <div class="col-xs-3 b-r b-light">
                                <span class="h4 font-bold m-t block"><?= count($this->db->where('task_status', '2')->get('tbl_task')->result()) ?>
                                </span> <small class="text-muted m-b block"><?= lang('complete_tasks') ?></small>
                            </div>                                                       
                            <div class="col-xs-3">
                                <span class="h4 font-bold m-t block"><?= count($this->db->get('tbl_leads')->result()) ?>
                                </span> <small class="text-muted m-b block"><?= lang('leads') ?></small>

                            </div>
                            <div class="col-xs-3">
                                <span class="h4 font-bold m-t block"><?= count($this->db->get('tbl_opportunities')->result()) ?>
                                </span> <small class="text-muted m-b block"><?= lang('opportunities') ?></small>

                            </div>
                        </div>
                    </footer>
                </section>
            </div>  
            <div class="wrap-fpanel col-sm-4 " style="margin-top: 20px;">  
                <section class="box box-primary">
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
                            if (!empty($recently_paid)) {
                                foreach ($recently_paid as $key => $v_paid) {

                                    $invoices_info = $this->db->where(array('invoices_id' => $v_paid->invoices_id))->get('tbl_invoices')->row();

                                    $payment_method = $this->db->where(array('payment_methods_id' => $v_paid->payment_method))->get('tbl_payment_methods')->row();

                                    $currency = $this->admin_model->client_currency_sambol($invoices_info->client_id);

                                    if ($v_paid->payment_method == '1') {
                                        $label = 'success';
                                    } elseif ($v_paid->payment_method == '2') {
                                        $label = 'danger';
                                    } else {
                                        $label = 'dark';
                                    }
                                    ?>
                                    <a href="<?= base_url() ?>admin/invoice/manage_invoice/invoice_details/<?= $v_paid->invoices_id ?>" class="list-group-item">
                                        <?= $invoices_info->reference_no ?> - <small class="text-muted"><?= $currency->symbol ?> <?= $v_paid->amount ?> <span class="label label-<?= $label ?> pull-right"><?= $payment_method->method_name; ?></span></small>
                                    </a>
                                    <?php
                                }
                            }
                            ?>
                        </div>
                    </div>
                    <div class="panel-footer">
                        <small><?= lang('total_receipts') ?>: <strong>
                                <?php
                                if (!empty($invoce_total)) {
                                    if (!empty($invoce_total['paid'])) {
                                        foreach ($invoce_total['paid'] as $curency => $v_total) {
                                            $total_paid [] = $invoce_total['symbol'][$curency] . " " . number_format($v_total, 2);
                                        }echo implode(", ", $total_paid);
                                    } else {
                                        echo '0.00';
                                    }
                                } else {
                                    echo '0.00';
                                }
                                ?>                                
                            </strong></small>
                    </div>
                </section>

            </div>
            <div class="col-md-6" style="margin-top: 20px;">
                <div class="box box-primary">
                    <div class="box-header with-border">
                        <h3 class="box-title"><?= lang('income_report') ?></h3>
                        <div class="box-tools pull-right">
                            <button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>                            
                            <button class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
                        </div>
                    </div><!-- /.box-header -->
                    <div class="box-body">
                        <p class="text-center">
                        <form role="form" id="form" action="<?php echo base_url(); ?>admin/dashboard/index/Income" method="post" class="form-horizontal form-groups-bordered">
                            <div class="form-group">
                                <label  class="col-sm-3 control-label">Select Year<span class="required">*</span></label>                                        
                                <div class="col-sm-5"> 
                                    <div class="input-group">
                                        <input type="text" name="Income" value="<?php
                                        if (!empty($Income)) {
                                            echo $Income;
                                        }
                                        ?>" class="form-control years"><span class="input-group-addon"><a href="#"><i class="glyphicon glyphicon-calendar"></i></a></span>
                                    </div>
                                </div>
                                <button type="submit" data-toggle="tooltip" data-placement="top" title="Search" class="btn btn-custom"><i class="fa fa-search"></i></button>
                            </div>
                        </form>
                        </p>
                        <!--End select input year -->
                        <div class="chart-responsive">
                            <!--Sales Chart Canvas -->
                            <canvas id="income" class="col-sm-12"></canvas>
                        </div><!-- /.chart-responsive -->
                    </div><!-- ./box-body -->

                </div>
            </div>
            <div class="col-md-6" style="margin-top: 20px;">
                <div class="box box-primary">
                    <div class="box-header with-border">
                        <h3 class="box-title"><?= lang('expense_report') ?></h3>
                        <div class="box-tools pull-right">
                            <button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>                            
                            <button class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
                        </div>
                    </div><!-- /.box-header -->
                    <div class="box-body">
                        <p class="text-center">
                        <form role="form" id="form" action="<?php echo base_url(); ?>admin/dashboard" method="post" class="form-horizontal form-groups-bordered">
                            <div class="form-group">
                                <label  class="col-sm-3 control-label">Select Year<span class="required">*</span></label>                                        
                                <div class="col-sm-5"> 
                                    <div class="input-group">
                                        <input type="text" name="year" value="<?php
                                        if (!empty($year)) {
                                            echo $year;
                                        }
                                        ?>" class="form-control years"><span class="input-group-addon"><a href="#"><i class="glyphicon glyphicon-calendar"></i></a></span>
                                    </div>
                                </div>
                                <button type="submit" data-toggle="tooltip" data-placement="top" title="Search" class="btn btn-custom"><i class="fa fa-search"></i></button>
                            </div>
                        </form>
                        </p>
                        <!--End select input year -->
                        <div class="chart-responsive">
                            <!--Sales Chart Canvas -->
                            <canvas id="buyers" class="col-sm-12"></canvas>
                        </div><!-- /.chart-responsive -->
                    </div><!-- ./box-body -->

                </div>
            </div>
            <div class="col-md-6" style="margin-top: 20px;">
                <div class="box box-primary">
                    <div class="box-header with-border">
                        <h3 class="box-title"><?= lang('payments_report') ?></h3>
                        <div class="box-tools pull-right">
                            <button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>                            
                            <button class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
                        </div>
                    </div><!-- /.box-header -->
                    <div class="box-body">
                        <div class="text-center">
                            <form role="form" id="form" action="<?php echo base_url(); ?>admin/dashboard/index/payments" method="post" class="form-horizontal form-groups-bordered">
                                <div class="form-group">
                                    <label  class="col-sm-3 control-label">Select Year<span class="required">*</span></label>                                        
                                    <div class="col-sm-5"> 
                                        <div class="input-group">
                                            <input type="text" name="yearly" value="<?php
                                            if (!empty($yearly)) {
                                                echo $yearly;
                                            }
                                            ?>" class="form-control years"><span class="input-group-addon"><a href="#"><i class="glyphicon glyphicon-calendar"></i></a></span>
                                        </div>
                                    </div>
                                    <button type="submit" data-toggle="tooltip"  data-placement="top" title="Search" class="btn btn-custom"><i class="fa fa-search"></i></button>
                                </div>
                            </form>
                        </div>
                        <canvas id="yearly_report" class="col-sm-12"></canvas>
                    </div><!-- ./box-body -->
                </div>
            </div>
            <div class="col-md-6" style="margin-top: 20px;">
                <!-- DONUT CHART -->
                <div class="box box-primary">
                    <div class="box-header with-border">
                        <h3 class="box-title"><?= lang('income_expense') ?></h3>
                        <div class="box-tools pull-right">
                            <button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                            <button class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
                        </div>
                    </div>
                    <div class="box-body chart-responsive">
                        <p class="text-center">
                        <form role="form" id="form" action="<?php echo base_url(); ?>admin/dashboard/index/month" method="post" class="form-horizontal form-groups-bordered">
                            <div class="form-group">
                                <label  class="col-sm-3 control-label">Select Month<span class="required">*</span></label>                                        
                                <div class="col-sm-5"> 
                                    <div class="input-group">
                                        <input type="text" name="month" value="<?php
                                        if (!empty($month)) {
                                            echo $month;
                                        }
                                        ?>" class="form-control monthyear"><span class="input-group-addon"><a href="#"><i class="glyphicon glyphicon-calendar"></i></a></span>
                                    </div>
                                </div>
                                <button type="submit" data-toggle="tooltip" data-placement="top" title="Search" class="btn btn-custom"><i class="fa fa-search"></i></button>
                            </div>
                        </form>
                        </p>
                        <div class="chart" id="sales-chart" style="height: 300px; position: relative;"></div>
                    </div><!-- /.box-body -->
                </div><!-- /.box -->
            </div>            
            <div class="wrap-fpanel col-sm-6 " style="margin-top: 20px;">  
                <section class="box box-primary">
                    <header class="box-header with-border">
                        <h3 class="box-title"><?= lang('recent_activities') ?></h3></header>
                    <div class="panel-body">
                        <section class="comment-list block">
                            <section class="slim-scroll" style="height:400px;overflow-x: scroll" >
                                <?php
                                $activities = $this->db
                                        ->order_by('activity_date', 'desc')
                                        ->get('tbl_activities', 10)
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
                                                    <?= $v_activities->activity ?>  <strong> <?= $v_activities->value1 . ' ' . $v_activities->value2 ?></strong>                                                                                                           
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

            <div class="col-sm-6" style="margin-top: 20px;">
                <div class="wrap-fpanel">                           
                    <div class="box box-success">
                        <div class="box-header with-border">
                            <h3 class="box-title"><?= lang('recent_mail') ?></h3>
                            <span class="pull-right text-white"><a href="<?php echo base_url() ?>admin/mailbox" class=" view-all-front">View All</a></span>
                        </div>               
                        <div class="panel-body slim-scroll">
                            <form method="post" action="<?php echo base_url() ?>admin/mailbox/delete_mail/inbox" >
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
                                                <a href="<?php echo base_url() ?>admin/mailbox/index/compose" class="btn btn-danger">Compose +</a>                                
                                            </div>
                                            <br />
                                            <div class="table-responsive mailbox-messages slim-scroll">
                                                <table class="table table-hover table-striped">
                                                    <tbody>

                                                        <?php
                                                        $get_inbox_message = $this->db
                                                                ->where(array('to' => $this->session->userdata('email'), 'deleted' => 'no'))
                                                                ->order_by('message_time', 'desc')
                                                                ->get('tbl_inbox', 10)
                                                                ->result();
                                                        if (!empty($get_inbox_message)):foreach ($get_inbox_message as $v_inbox_msg):
                                                                ?>
                                                                <tr>                                                                                                                                
                                                                    <td><input class="child_present" type="checkbox" name="selected_id[]" value="<?php echo $v_inbox_msg->inbox_id; ?>"/></td>

                                                                    <td class="mailbox-star">
                                                                        <?php if ($v_inbox_msg->favourites == 1) { ?>
                                                                            <a href="<?php echo base_url() ?>admin/mailbox/index/added_favourites/<?php echo $v_inbox_msg->inbox_id ?>/0"><i class="fa fa-star text-yellow"></i></a>
                                                                        <?php } else { ?>
                                                                            <a href="<?php echo base_url() ?>admin/mailbox/index/added_favourites/<?php echo $v_inbox_msg->inbox_id ?>/1"><i class="fa fa-star-o text-yellow"></i></a>    
                                                                        <?php } ?>                                                
                                                                    </td>                                                                                         
                                                                    <td class="mailbox-name"><a href="<?php echo base_url() ?>admin/mailbox/index/read_inbox_mail/<?php echo $v_inbox_msg->inbox_id ?>"><?php
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
            </div>
        </div>
        <!-- Morris.js charts -->
        <script src="https://cdnjs.cloudflare.com/ajax/libs/raphael/2.1.0/raphael-min.js"></script>
        <script src="<?= base_url() ?>/plugins/morris/morris.min.js" type="text/javascript"></script>
        <!-- FastClick -->
        <script src="<?= base_url() ?>/plugins/fastclick/fastclick.min.js" type="text/javascript"></script>
        <!--Calendar-->
        <script type="text/javascript">
                                                    $(document).ready(function() {
                                                    if ($('#calendar').length) {
                                                    var date = new Date();
                                                    var d = date.getDate();
                                                    var m = date.getMonth(); var y = date.getFullYear();
                                                    var calendar = $('#calendar').fullCalendar({
                                                    header: {
                                                    center: 'prev title next',
                                                            left: 'month agendaWeek agendaDay today',
                                                            right: ''
                                                    },
                                                            buttonText: {
                                                            prev: '<i class="fa fa-angle-left" />',
                                                                    next: '<i class="fa fa-angle-right" />'
                                                            },
                                                            selectable: true,
                                                            selectHelper: true,
                                                            select: function(start, end, allDay) {
                                                            var endtime = $.fullCalendar.formatDate(end, 'h:mm tt');
                                                            var starttime = $.fullCalendar.formatDate(start, 'yyyy/MM/dd');
                                                            var mywhen = starttime + ' - ' + endtime;
                                                            $('#event_modal #apptStartTime').val(starttime);
                                                            $('#event_modal #apptEndTime').val(starttime);
                                                            $('#event_modal #apptAllDay').val(allDay);
                                                            $('#event_modal #when').text(mywhen);
                                                            $('#event_modal').modal('show');
                                                            },
                                                            events: [
<?php
if ($role == 1) {
    $payments_info = $this->db->get('tbl_payments')->result();
    if (!empty($payments_info)) {
        foreach ($payments_info as $v_payments) :
            $start_day = date('d', strtotime($v_payments->payment_date));
            $smonth = date('n', strtotime($v_payments->payment_date));
            $start_month = $smonth - 1;
            $start_year = date('Y', strtotime($v_payments->payment_date));
            $end_year = date('Y', strtotime($v_payments->payment_date));
            $end_day = date('d', strtotime($v_payments->payment_date));
            $emonth = date('n', strtotime($v_payments->payment_date));
            $end_month = $emonth - 1;
            $invoice = $this->db->where(array('invoices_id' => $v_payments->invoices_id))->get('tbl_invoices')->row();
            $client_info = $this->db->where(array('client_id' => $invoice->client_id))->get('tbl_client')->row();
            $currency = $this->admin_model->client_currency_sambol($invoice->client_id);
            ?>
                                                                        {
                                                                        title  : '<?= $client_info->name . ' (' . $currency->symbol . $v_payments->amount . ')' ?>',
                                                                                start: new Date(<?php echo $start_year . ',' . $start_month . ',' . $start_day; ?>),
                                                                                end: new Date(<?php echo $end_year . ',' . $end_month . ',' . $end_day; ?>),
                                                                                color  : '#78ae54',
                                                                                url: '<?= base_url() ?>admin/invoice/manage_invoice/payments_details/<?= $v_payments->payments_id ?>'
                                                                                            },
            <?php
        endforeach;
    }
    $invoice_info = $this->db->get('tbl_invoices')->result();
    if (!empty($invoice_info)) {
        foreach ($invoice_info as $v_invoice) :
            $start_day = date('d', strtotime($v_invoice->due_date));
            $smonth = date('n', strtotime($v_invoice->due_date));
            $start_month = $smonth - 1;
            $start_year = date('Y', strtotime($v_invoice->due_date));
            $end_year = date('Y', strtotime($v_invoice->due_date));
            $end_day = date('d', strtotime($v_invoice->due_date));
            $emonth = date('n', strtotime($v_invoice->due_date));
            $end_month = $emonth - 1;
            ?>
                                                                                            {
                                                                                            title  : '<?php echo $v_invoice->reference_no ?>',
                                                                                                    start: new Date(<?php echo $start_year . ',' . $start_month . ',' . $start_day; ?>),
                                                                                                    end: new Date(<?php echo $end_year . ',' . $end_month . ',' . $end_day; ?>),
                                                                                                    color  : '#DE4E6C',
                                                                                                    url: '<?= base_url() ?>admin/invoice/manage_invoice/invoice_details/<?= $v_invoice->invoices_id ?>'
                                                                                                                },
            <?php
        endforeach;
    }
    $estimates_info = $this->db->get('tbl_estimates')->result();
    if (!empty($estimates_info)) {
        foreach ($estimates_info as $v_estimates) :
            $start_day = date('d', strtotime($v_estimates->due_date));
            $smonth = date('n', strtotime($v_estimates->due_date));
            $start_month = $smonth - 1;
            $start_year = date('Y', strtotime($v_estimates->due_date));
            $end_year = date('Y', strtotime($v_estimates->due_date));
            $end_day = date('d', strtotime($v_estimates->due_date));
            $emonth = date('n', strtotime($v_estimates->due_date));
            $end_month = $emonth - 1;
            ?>
                                                                                                                {
                                                                                                                title  : '<?php echo $v_estimates->reference_no ?>',
                                                                                                                        start: new Date(<?php echo $start_year . ',' . $start_month . ',' . $start_day; ?>),
                                                                                                                        end: new Date(<?php echo $end_year . ',' . $end_month . ',' . $end_day; ?>),
                                                                                                                        color  : '#E8AE00',
                                                                                                                        url: '<?= base_url() ?>admin/estimates/index/estimates_details/<?= $v_estimates->estimates_id ?>'
                                                                                                                                    },
            <?php
        endforeach;
    }
    $project_info = $this->db->get('tbl_project')->result();
    if (!empty($project_info)) {
        foreach ($project_info as $v_project) :
            $start_day = date('d', strtotime($v_project->end_date));
            $smonth = date('n', strtotime($v_project->end_date));
            $start_month = $smonth - 1;
            $start_year = date('Y', strtotime($v_project->end_date));
            $end_year = date('Y', strtotime($v_project->end_date));
            $end_day = date('d', strtotime($v_project->end_date));
            $emonth = date('n', strtotime($v_project->end_date));
            $end_month = $emonth - 1;
            ?>
                                                                                                                                    {
                                                                                                                                    title  : '<?php echo $v_project->project_name ?>',
                                                                                                                                            start: new Date(<?php echo $start_year . ',' . $start_month . ',' . $start_day; ?>),
                                                                                                                                            end: new Date(<?php echo $end_year . ',' . $end_month . ',' . $end_day; ?>),
                                                                                                                                            color  : '#7266BA',
                                                                                                                                            url: '<?= base_url() ?>admin/project/project_details/<?= $v_project->project_id ?>'
                                                                                                                                                        },
            <?php
            $milestone_info = $this->db->where(array('project_id' => $v_project->project_id))->get('tbl_milestones')->result();
            if (!empty($milestone_info)) {
                foreach ($milestone_info as $v_milestone) :
                    $start_day = date('d', strtotime($v_milestone->end_date));
                    $smonth = date('n', strtotime($v_milestone->end_date));
                    $start_month = $smonth - 1;
                    $start_year = date('Y', strtotime($v_milestone->end_date));
                    $end_year = date('Y', strtotime($v_milestone->end_date));
                    $end_day = date('d', strtotime($v_milestone->end_date));
                    $emonth = date('n', strtotime($v_milestone->end_date));
                    $end_month = $emonth - 1;
                    ?>
                                                                                                                                                                {
                                                                                                                                                                title  : '<?php echo $v_milestone->milestone_name ?>',
                                                                                                                                                                        start: new Date(<?php echo $start_year . ',' . $start_month . ',' . $start_day; ?>),
                                                                                                                                                                        end: new Date(<?php echo $end_year . ',' . $end_month . ',' . $end_day; ?>),
                                                                                                                                                                        color  : '#00A65A',
                                                                                                                                                                        url: '<?= base_url() ?>admin/project/project_details/<?= $v_project->project_id ?>/5'
                                                                                                                                                                },
                    <?php
                endforeach;
            }
        endforeach;
    }
    $task_info = $this->db->get('tbl_task')->result();
    if (!empty($task_info)) {
        foreach ($task_info as $v_task) :
            $start_day = date('d', strtotime($v_task->due_date));
            $smonth = date('n', strtotime($v_task->due_date));
            $start_month = $smonth - 1;
            $start_year = date('Y', strtotime($v_task->due_date));
            $end_year = date('Y', strtotime($v_task->due_date));
            $end_day = date('d', strtotime($v_task->due_date));
            $emonth = date('n', strtotime($v_task->due_date));
            $end_month = $emonth - 1;
            ?>
                                                                                                                                                        {
                                                                                                                                                        title  : '<?php echo $v_task->task_name ?>',
                                                                                                                                                                start: new Date(<?php echo $start_year . ',' . $start_month . ',' . $start_day; ?>),
                                                                                                                                                                end: new Date(<?php echo $end_year . ',' . $end_month . ',' . $end_day; ?>),
                                                                                                                                                                color  : '#3A87AD',
                                                                                                                                                                url: '<?= base_url() ?>admin/tasks/view_task_details/<?= $v_task->task_id ?>'
                                                                                                                                                                            },
            <?php
        endforeach;
    }
    $opportunity_info = $this->db->get('tbl_opportunities')->result();
    if (!empty($opportunity_info)) {
        foreach ($opportunity_info as $v_opportunity) :
            $start_day = date('d', strtotime($v_opportunity->close_date));
            $smonth = date('n', strtotime($v_opportunity->close_date));
            $start_month = $smonth - 1;
            $start_year = date('Y', strtotime($v_opportunity->close_date));
            $end_year = date('Y', strtotime($v_opportunity->close_date));
            $end_day = date('d', strtotime($v_opportunity->close_date));
            $emonth = date('n', strtotime($v_opportunity->close_date));
            $end_month = $emonth - 1;
            // next action
            $next_start_day = date('d', strtotime($v_opportunity->next_action_date));
            $next_smonth = date('n', strtotime($v_opportunity->next_action_date));
            $next_start_month = $next_smonth - 1;
            $next_start_year = date('Y', strtotime($v_opportunity->next_action_date));
            $next_end_year = date('Y', strtotime($v_opportunity->next_action_date));
            $next_end_day = date('d', strtotime($v_opportunity->next_action_date));
            $next_emonth = date('n', strtotime($v_opportunity->next_action_date));
            $next_end_month = $next_emonth - 1;
            ?>
                                                                                                                                                                            {
                                                                                                                                                                            title  : '<?php echo $v_opportunity->opportunity_name ?>',
                                                                                                                                                                                    start: new Date(<?php echo $start_year . ',' . $start_month . ',' . $start_day; ?>),
                                                                                                                                                                                    end: new Date(<?php echo $end_year . ',' . $end_month . ',' . $end_day; ?>),
                                                                                                                                                                                    color  : '#f05722',
                                                                                                                                                                                    url: '<?= base_url() ?>admin/tasks/view_task_details/<?= $v_opportunity->opportunities_id ?>'
                                                                                                                                                                                                },
                                                                                                                                                                                                {
                                                                                                                                                                                                title  : '<?php echo $v_opportunity->next_action ?>',
                                                                                                                                                                                                        start: new Date(<?php echo $next_start_year . ',' . $next_start_month . ',' . $next_start_day; ?>),
                                                                                                                                                                                                        end: new Date(<?php echo $next_end_year . ',' . $next_end_month . ',' . $next_end_day; ?>),
                                                                                                                                                                                                        color  : '#0f9058',
                                                                                                                                                                                                        url: '<?= base_url() ?>admin/opportunities/opportunity_details/<?= $v_opportunity->opportunities_id ?>'
                                                                                                                                                                                                                    },
            <?php
        endforeach;
    }
}
?>

                                                                                                                                                                                                        ],
                                                                                                                                                                                                        eventColor: '#3A87AD',
                                                                                                                                                                                                });
                                                                                                                                                                                                }

                                                                                                                                                                                                });</script>
        <script src="<?php echo base_url(); ?>asset/js/fullcalendar.js"></script>
        <script src="<?php echo base_url(); ?>asset/js/jquery-ui.min.js"></script>
        <!-- / Chart.js Script -->
        <script src="<?php echo base_url(); ?>asset/js/chart.min.js" type="text/javascript"></script>

        <script>
                                                                                                                                                                                                // line chart data
                                                                                                                                                                                                var buyerData = {

                                                                                                                                                                                                labels: [
<?php
// yearle result name = month name 
foreach ($all_income as $name => $v_income):
    $month_name = date('F', strtotime($year . '-' . $name)); // get full name of month by date query
    ?>
                                                                                                                                                                                                    "<?php echo $month_name; ?>", // echo the whole month of the year
<?php endforeach; ?>
                                                                                                                                                                                                ],
                                                                                                                                                                                                        datasets: [
                                                                                                                                                                                                        {
                                                                                                                                                                                                        fillColor: "rgba(172,194,132,0.4)",
                                                                                                                                                                                                        strokeColor: "#ACC26D",
                                                                                                                                                                                                        pointColor: "#fff",
                                                                                                                                                                                                        pointStrokeColor: "#9DB86D",
                                                                                                                                                                                                        data: [
<?php
// get monthly result report 
foreach ($all_income as $v_income):
    ?>
                                                                                                                                                                                                            "<?php
    if (!empty($v_income)) { // if the report result is exist 
        $total_income = 0;
        foreach ($v_income as $income) {
            $total_income += $income->amount;
        }
        echo $total_income; // view the total report in a  month
    }
    ?>",
    <?php
endforeach;
?>
                                                                                                                                                                                                        ]
                                                                                                                                                                                                        }
                                                                                                                                                                                                        ]
                                                                                                                                                                                                }

                                                                                                                                                                                                // get line chart canvas
                                                                                                                                                                                                var buyers = document.getElementById('income').getContext('2d');
                                                                                                                                                                                                // draw line chart
                                                                                                                                                                                                new Chart(buyers).Line(buyerData);</script>
        <script>
            // line chart data
            var buyerData = {

            labels: [
<?php
// yearle result name = month name 
foreach ($all_expense as $name => $v_expense):
    $month_name = date('F', strtotime($year . '-' . $name)); // get full name of month by date query
    ?>
                "<?php echo $month_name; ?>", // echo the whole month of the year
<?php endforeach; ?>
            ],
                    datasets: [
                    {
                    fillColor: "rgba(172,194,132,0.4)",
                            strokeColor: "#ACC26D",
                            pointColor: "#fff",
                            pointStrokeColor: "#9DB86D",
                            data: [
<?php
// get monthly result report 
foreach ($all_expense as $v_expense):
    ?>
                                "<?php
    if (!empty($v_expense)) { // if the report result is exist 
        $total_expense = 0;
        foreach ($v_expense as $exoense) {
            $total_expense += $exoense->amount;
        }
        echo $total_expense; // view the total report in a  month
    }
    ?>",
    <?php
endforeach;
?>
                            ]
                    }
                    ]
            }

            // get line chart canvas
            var buyers = document.getElementById('buyers').getContext('2d');
            // draw line chart
            new Chart(buyers).Line(buyerData);</script>
        <script>
            // line chart data
            var buyerData = {

            labels: [
<?php
// yearle result name = month name 
for ($i = 1; $i <= 12; $i++) {
    $month_name = date('F', strtotime($year . '-' . $i)); // get full name of month by date query
    ?>
                "<?php echo $month_name; ?>", // echo the whole month of the year
<?php }; ?>
            ],
                    datasets: [
                    {
                    fillColor: "rgba(172,194,132,0.4)",
                            strokeColor: "#ACC26D",
                            pointColor: "#fff",
                            pointStrokeColor: "#9DB86D",
                            data: [
<?php
// get monthly result report 
foreach ($yearly_overview as $v_overview):
    ?>
                                "<?php
    echo $v_overview; // view the total report in a  month
    ?>",
    <?php
endforeach;
?>
                            ]
                    }
                    ]
            }

        // get line chart canvas
            var buyers = document.getElementById('yearly_report').getContext('2d');
            // draw line chart
            new Chart(buyers).Line(buyerData);</script>
        <script type="text/javascript">
            $(function () {
            "use strict";
            //DONUT CHART
            var donut = new Morris.Donut({
            element: 'sales-chart',
                    resize: true,
                    colors: ["#00a65a", "#f56954"],
                    data: [
                    {label: "<?= lang('Income') ?>", value:
<?php
$total_vincome = 0;
if (!empty($income_expense)):foreach ($income_expense as $v_income_expense):
        if ($v_income_expense->type == 'Income') {

            $total_vincome += $v_income_expense->amount;
            ?>

            <?php
        }
    endforeach;
endif;
echo $total_vincome;
?>
                    },
                    {label: "<?= lang('Expense') ?>", value: <?php
$total_vexpense = 0;
if (!empty($income_expense)):foreach ($income_expense as $v_income_expense):
        if ($v_income_expense->type == 'Expense') {
            $total_vexpense +=$v_income_expense->amount;
            ?>

            <?php
        }
    endforeach;
endif;
echo $total_vexpense;
?>},
                    ],
                    hideHover: 'auto'
            });
            });
        </script>