<?= message_box('success'); ?>

<div class="nav-tabs-custom">
    <!-- Tabs within a box -->
    <ul class="nav nav-tabs">
        <li class="<?= $active == 1 ? 'active' : ''; ?>"><a href="#manage" data-toggle="tab"><?= lang('all_deposit') ?></a></li>
        <li class="<?= $active == 2 ? 'active' : ''; ?>"><a href="#create" data-toggle="tab"><?= lang('new_deposit') ?></a></li>                                                                     
    </ul>
    <div class="tab-content no-padding">
        <!-- ************** general *************-->
        <div class="tab-pane <?= $active == 1 ? 'active' : ''; ?>" id="manage">

            <div class="table-responsive">
                <table class="table table-striped DataTables " id="DataTables">
                    <thead>
                        <tr>                            
                            <th><?= lang('date') ?></th>
                            <th><?= lang('account') ?></th>                    
                            <th><?= lang('deposit_category') ?></th>                    
                            <th><?= lang('paid_by') ?></th>                    
                            <th><?= lang('description') ?></th>                    
                            <th><?= lang('amount') ?></th>                    
                            <th><?= lang('balance') ?></th>                                        
                            <th><?= lang('action') ?></th>                    
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $curency = $this->transactions_model->check_by(array('code' => config_item('currency')), 'tbl_currencies');
                        $total_amount = 0;
                        $total_credit = 0;
                        $total_balance = 0;
                        $all_deposit_info = $this->db->where(array('type' => 'Income'))->limit(20)->order_by('transactions_id', 'DESC')->get('tbl_transactions')->result();

                        foreach ($all_deposit_info as $v_deposit) :
                            $account_info = $this->transactions_model->check_by(array('account_id' => $v_deposit->account_id), 'tbl_accounts');
                            $client_info = $this->transactions_model->check_by(array('client_id' => $v_deposit->paid_by), 'tbl_client');
                            $category_info = $this->transactions_model->check_by(array('income_category_id' => $v_deposit->category_id), 'tbl_income_category');
                            if (!empty($client_info)) {
                                if ($client_info->client_status == 1) {
                                    $status = '( Person )';
                                } else {
                                    $status = '( Company )';
                                }
                                $client_name = $client_info->name;
                            } else {
                                $client_name = '-';
                                $status = '';
                            }
                            ?>
                            <tr>
                                <td><?= strftime(config_item('date_format'), strtotime($v_deposit->date)); ?></td>
                                <td><?= $account_info->account_name ?></td>
                                <td><?php
                                    if (!empty($category_info)) {
                                        echo $category_info->income_category;
                                    } else {
                                        echo '-';
                                    }
                                    ?></td>
                                <td><?= $client_name . ' ' . $status  ?></td>
                                <td><?= $v_deposit->notes ?></td>
                                <td><?= $curency->symbol . ' ' . number_format($v_deposit->amount, 2) ?></td>                                
                                <td><?= $curency->symbol . ' ' . number_format($v_deposit->total_balance, 2) ?></td>
                                <td><?= btn_edit('admin/transactions/deposit/' . $v_deposit->transactions_id) ?>
                                    <?= btn_delete('admin/transactions/delete_deposit/' . $v_deposit->transactions_id) ?></td>
                            </tr>
                            <?php
                            $total_amount +=$v_deposit->amount;
                            $total_credit +=$v_deposit->credit;
                            $total_balance +=$v_deposit->total_balance;
                            ?>
                            <?php
                        endforeach;
                        ?>
                        <tr class="custom-color-with-td">
                            <td style="text-align: right;" colspan="5"><strong><?= lang('total') ?>:</strong></td>
                            <td  ><strong><?= $curency->symbol . ' ' . number_format($total_amount, 2) ?></strong></td>                            
                            <td colspan="2"><strong><?= $curency->symbol . ' ' . number_format($total_balance, 2) ?></strong></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>        

        <div class="tab-pane <?= $active == 2 ? 'active' : ''; ?>" id="create">
            <form role="form" enctype="multipart/form-data" id="form" action="<?php echo base_url(); ?>admin/transactions/save_deposit/<?php
            if (!empty($deposit_info)) {
                echo $deposit_info->transactions_id;
            }
            ?>" method="post" class="form-horizontal  ">

                <div class="form-group">
                    <label class="col-lg-3 control-label"><?= lang('account') ?> <span class="text-danger">*</span> </label>
                    <div class="col-lg-5">
                        <select class="form-control select_box" style="width: 100%"  name="account_id"  required <?php
                        if (!empty($deposit_info)) {
                            echo 'disabled';
                        }
                        ?>> 

                            <?php
                            $account_info = $this->db->get('tbl_accounts')->result();
                            if (!empty($account_info)) {
                                foreach ($account_info as $v_account) {
                                    ?>
                                    <option value="<?= $v_account->account_id ?>"
                                    <?php
                                    if (!empty($deposit_info->account_id)) {
                                        echo $deposit_info->account_id == $v_account->account_id ? 'selected' : '';
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
                    <label class="col-lg-3 control-label"><?= lang('date') ?></label> 
                    <div class="col-lg-5">
                        <div class="input-group">
                            <input type="text" name="date"  class="form-control datepicker" value="<?php
                            if (!empty($deposit_info->date)) {
                                echo $deposit_info->date;
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

                <div class="form-group terms">
                    <label class="col-lg-3 control-label"><?= lang('notes') ?> </label>
                    <div class="col-lg-5">
                        <textarea name="notes" class="form-control"><?php
                            if (!empty($deposit_info)) {
                                echo $deposit_info->notes;
                            }
                            ?></textarea>                        
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-lg-3 control-label"><?= lang('amount') ?> <span class="text-danger">*</span> </label>
                    <div class="col-lg-5">
                        <div class="input-group  ">
                            <input class="form-control " type="text" value="<?php
                            if (!empty($deposit_info)) {
                                echo $deposit_info->amount;
                            }
                            ?>" name="amount" required="" <?php
                                   if (!empty($deposit_info)) {
                                       echo 'disabled';
                                   }
                                   ?>>  
                        </div>
                    </div>
                </div>        
                <div class="more_option">
                    <div class="form-group">
                        <label class="col-lg-3 control-label"><?= lang('deposit_category') ?> </label>
                        <div class="col-lg-5">
                            <select class="form-control select_box" style="width: 100%"  name="category_id"  > 
                                <option value="0"><?= lang('none') ?></option>
                                <?php
                                $category_info = $this->db->get('tbl_income_category')->result();
                                if (!empty($category_info)) {
                                    foreach ($category_info as $v_category) {
                                        ?>
                                        <option value="<?= $v_category->income_category_id ?>"
                                        <?php
                                        if (!empty($deposit_info->category_id)) {
                                            echo $deposit_info->category_id == $v_category->income_category_id ? 'selected' : '';
                                        }
                                        ?>
                                                ><?= $v_category->income_category ?></option>
                                                <?php
                                            }
                                        }
                                        ?>
                            </select> 
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-lg-3 control-label"><?= lang('paid_by') ?> </label>
                        <div class="col-lg-5">
                            <select class="form-control select_box" style="width: 100%"  name="paid_by"  > 
                                <option value="0"><?= lang('select_payer') ?></option>
                                <?php
                                $all_client = $this->db->get('tbl_client')->result();
                                if (!empty($all_client)) {
                                    foreach ($all_client as $v_client) {
                                        if ($v_client->client_status == 1) {
                                            $status = 'Person';
                                        } else {
                                            $status = 'Company';
                                        }
                                        ?>
                                        <option value="<?= $v_client->client_id ?>"
                                        <?php
                                        if (!empty($deposit_info)) {
                                            echo $deposit_info->paid_by == $v_client->client_id ? 'selected' : '';
                                        }
                                        ?>
                                                ><?= ucfirst($v_client->name) . ' <small>(' . $status . ')</small>' ?></option>
                                                <?php
                                            }
                                        }
                                        ?>
                            </select> 
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-lg-3 control-label"><?= lang('payment_method') ?> </label>
                        <div class="col-lg-5">
                            <select class="form-control select_box" style="width: 100%"  name="payment_methods_id" > 
                                <option value="0"><?= lang('select_payment_method') ?></option>
                                <?php
                                $payment_methods = $this->db->get('tbl_payment_methods')->result();
                                if (!empty($payment_methods)) {
                                    foreach ($payment_methods as $p_method) {
                                        ?>
                                        <option value="<?= $p_method->payment_methods_id ?>" <?php
                                        if (!empty($deposit_info)) {
                                            echo $deposit_info->payment_methods_id == $p_method->payment_methods_id ? 'selected' : '';
                                        }
                                        ?>><?= $p_method->method_name ?></option>
                                                <?php
                                            }
                                        }
                                        ?>	
                            </select> 
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-lg-3 control-label"><?= lang('reference') ?> </label>
                        <div class="col-lg-5">                            
                            <input class="form-control " type="text" value="<?php
                            if (!empty($deposit_info)) {
                                echo $deposit_info->reference;
                            }
                            ?>" name="reference" > 
                            <input class="form-control " type="hidden" value="<?php
                            if (!empty($deposit_info)) {
                                echo $deposit_info->account_id;
                            }
                            ?>" name="old_account_id" > 
                            <span class="help-block"><?= lang('reference_example') ?></span>                            
                        </div>
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
</div>