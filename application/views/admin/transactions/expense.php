<?= message_box('success'); ?>
<?= message_box('error'); ?>

<div class="nav-tabs-custom">
    <!-- Tabs within a box -->
    <ul class="nav nav-tabs">
        <li class="<?= $active == 1 ? 'active' : ''; ?>"><a href="#manage" data-toggle="tab"><?= lang('all_expense') ?></a></li>
        <li class="<?= $active == 2 ? 'active' : ''; ?>"><a href="#create" data-toggle="tab"><?= lang('new_expense') ?></a></li>                                                                     
    </ul>
    <div class="tab-content no-padding">
        <!-- ************** general *************-->
        <div class="tab-pane <?= $active == 1 ? 'active' : ''; ?>" id="manage">

            <div class="table-responsive">
                <table class="table table-striped DataTables " id="DataTables">
                    <thead>
                        <tr>                            
                            <th><?= lang('account_name') ?></th>
                            <th class="col-date"><?= lang('notes') ?></th>                            
                            <th class="col-currency"><?= lang('amount') ?></th>                            
                            <th><?= lang('date') ?></th>
                            <th class="col-options no-sort" ><?= lang('action') ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $all_expense_info = $this->db->where(array('type' => 'Expense'))->order_by('transactions_id', 'DESC')->get('tbl_transactions')->result();
                        foreach ($all_expense_info as $v_expense) :
                            $account_info = $this->transactions_model->check_by(array('account_id' => $v_expense->account_id), 'tbl_accounts');
                            $curency = $this->transactions_model->check_by(array('code' => config_item('currency')), 'tbl_currencies');
                            ?>
                            <tr>
                                <td><?= $account_info->account_name ?></td>
                                <td><?= $v_expense->notes ?></td>
                                <td><?= $curency->symbol . ' ' . number_format($v_expense->amount, 2) ?></td>
                                <td><?= strftime(config_item('date_format'), strtotime($v_expense->date)); ?></td>
                                <td><?= btn_edit('admin/transactions/expense/' . $v_expense->transactions_id) ?>
                                    <?= btn_delete('admin/transactions/delete_expense/' . $v_expense->transactions_id) ?></td>
                            </tr>
                            <?php
                        endforeach;
                        ?>
                    </tbody>
                </table>
            </div>
        </div>        

        <div class="tab-pane <?= $active == 2 ? 'active' : ''; ?>" id="create">
            <form role="form" enctype="multipart/form-data" id="form" action="<?php echo base_url(); ?>admin/transactions/save_expense/<?php
            if (!empty($expense_info)) {
                echo $expense_info->transactions_id;
            }
            ?>" method="post" class="form-horizontal  ">

                <div class="form-group">
                    <label class="col-lg-3 control-label"><?= lang('account') ?> <span class="text-danger">*</span> </label>
                    <div class="col-lg-5">
                        <select class="form-control select_box" style="width: 100%"  name="account_id"  required <?php
                        if (!empty($expense_info)) {
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
                                    if (!empty($expense_info)) {
                                        echo $expense_info->account_id == $v_account->account_id ? 'selected' : '';
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
                            if (!empty($expense_info->date)) {
                                echo $expense_info->date;
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
                            if (!empty($expense_info)) {
                                echo $expense_info->notes;
                            }
                            ?></textarea>                        
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-lg-3 control-label"><?= lang('amount') ?> <span class="text-danger">*</span> </label>
                    <div class="col-lg-5">
                        <div class="input-group  ">
                            <input class="form-control " type="text" value="<?php
                            if (!empty($expense_info)) {
                                echo $expense_info->amount;
                            }
                            ?>" name="amount" required="" <?php
                                   if (!empty($expense_info)) {
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
                                $category_info = $this->db->get('tbl_expense_category')->result();
                                if (!empty($category_info)) {
                                    foreach ($category_info as $v_category) {
                                        ?>
                                        <option value="<?= $v_category->expense_category_id ?>"
                                        <?php
                                        if (!empty($expense_info->category_id)) {
                                            echo $expense_info->category_id == $v_category->expense_category_id ? 'selected' : '';
                                        }
                                        ?>
                                                ><?= $v_category->expense_category ?></option>
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
                                        if (!empty($expense_info)) {
                                            echo $expense_info->paid_by == $v_client->client_id ? 'selected' : '';
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
                                        if (!empty($expense_info)) {
                                            echo $expense_info->payment_methods_id == $p_method->payment_methods_id ? 'selected' : '';
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
                            if (!empty($expense_info)) {
                                echo $expense_info->reference;
                            }
                            ?>" name="reference" > 
                            <span class="help-block"><?= lang('reference_example') ?></span>                            
                        </div>
                    </div>  
                </div>
                <input class="form-control " type="hidden" value="<?php
                if (!empty($expense_info)) {
                    echo $expense_info->account_id;
                }
                ?>" name="old_account_id" > 
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