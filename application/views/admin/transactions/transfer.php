<?= message_box('success'); ?>
<?= message_box('error'); ?>

<div class="nav-tabs-custom">
    <!-- Tabs within a box -->
    <ul class="nav nav-tabs">
        <li class="<?= $active == 1 ? 'active' : ''; ?>"><a href="#manage" data-toggle="tab"><?= lang('all_transfer') ?></a></li>
        <li class="<?= $active == 2 ? 'active' : ''; ?>"><a href="#create" data-toggle="tab"><?= lang('new_transfer') ?></a></li>                                                                     
    </ul>
    <div class="tab-content no-padding">
        <!-- ************** general *************-->
        <div class="tab-pane <?= $active == 1 ? 'active' : ''; ?>" id="manage">

            <div class="table-responsive">
                <table class="table table-striped DataTables " id="DataTables">
                    <thead>
                        <tr>                            
                            <th><?= lang('from_account') ?></th>
                            <th><?= lang('to_account') ?></th>
                            <th class="col-currency"><?= lang('amount') ?></th>                            
                            <th><?= lang('date') ?></th>
                            <th class="col-date"><?= lang('notes') ?></th>                            
                            <th class="col-options no-sort" ><?= lang('action') ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $all_transfer_info = $this->db->order_by('transfer_id', 'DESC')->get('tbl_transfer')->result();
                        foreach ($all_transfer_info as $v_transfer) :
                            if ($v_transfer->transfer_id == $v_transfer->transfer_id) {
                                $to_account_info = $this->transactions_model->check_by(array('account_id' => $v_transfer->to_account_id), 'tbl_accounts');
                                $from_account_info = $this->transactions_model->check_by(array('account_id' => $v_transfer->from_account_id), 'tbl_accounts');
                                $curency = $this->transactions_model->check_by(array('code' => config_item('currency')), 'tbl_currencies');
                                ?>
                                <tr>
                                    <td><?= $from_account_info->account_name ?></td>
                                    <td><?= $to_account_info->account_name ?></td>
                                    <td><?= $curency->symbol . ' ' . number_format($v_transfer->amount, 2) ?></td>
                                    <td><?= strftime(config_item('date_format'), strtotime($v_transfer->date)); ?></td>
                                    <td><?= $v_transfer->notes ?></td>
                                    <td><?= btn_edit('admin/transactions/transfer/' . $v_transfer->transfer_id) ?>
                                        <?= btn_delete('admin/transactions/delete_transfer/' . $v_transfer->transfer_id) ?></td>
                                </tr>
                                <?php
                            }
                        endforeach;
                        ?>
                    </tbody>
                </table>
            </div>               
        </div>               

        <div class="tab-pane <?= $active == 2 ? 'active' : ''; ?>" id="create">
            <form role="form" enctype="multipart/form-data" id="form" action="<?php echo base_url(); ?>admin/transactions/save_transfer/<?php
            if (!empty($transfer_info)) {
                echo $transfer_info->transfer_id;
            }
            ?>" method="post" class="form-horizontal  ">

                <div class="form-group">
                    <label class="col-lg-3 control-label"><?= lang('from_account') ?> <span class="text-danger">*</span> </label>
                    <div class="col-lg-5">
                        <select class="form-control select_box" style="width: 100%"  name="from_account_id"  required <?php
                        if (!empty($transfer_info)) {
                            echo 'disabled';
                        }
                        ?>> 
                            <option value=""><?= lang('choose_from_account') ?></option>
                            <?php
                            $f_account_info = $this->db->get('tbl_accounts')->result();
                            if (!empty($f_account_info)) {
                                foreach ($f_account_info as $v_f_account) {
                                    ?>
                                    <option value="<?= $v_f_account->account_id ?>"
                                    <?php
                                    if (!empty($transfer_info->from_account_id)) {
                                        echo $transfer_info->from_account_id == $v_f_account->account_id ? 'selected' : '';
                                    }
                                    ?>
                                            ><?= $v_f_account->account_name ?></option>
                                            <?php
                                        }
                                    }
                                    ?>
                        </select> 
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-lg-3 control-label"><?= lang('to_account') ?> <span class="text-danger">*</span> </label>
                    <div class="col-lg-5">
                        <select class="form-control select_box" style="width: 100%"  name="to_account_id"  required <?php
                        if (!empty($transfer_info)) {
                            echo 'disabled';
                        }
                        ?>> 
                            <option value=""><?= lang('choose_to_account') ?></option>
                            <?php
                            $account_info = $this->db->get('tbl_accounts')->result();
                            if (!empty($account_info)) {
                                foreach ($account_info as $v_account) {
                                    ?>
                                    <option value="<?= $v_account->account_id ?>"
                                    <?php
                                    if (!empty($transfer_info->to_account_id)) {
                                        echo $transfer_info->to_account_id == $v_account->account_id ? 'selected' : '';
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
                            if (!empty($transfer_info->date)) {
                                echo $transfer_info->date;
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
                            if (!empty($transfer_info)) {
                                echo $transfer_info->notes;
                            }
                            ?></textarea>                        
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-lg-3 control-label"><?= lang('amount') ?> <span class="text-danger">*</span> </label>
                    <div class="col-lg-5">
                        <div class="input-group  ">
                            <input class="form-control " type="text" value="<?php
                            if (!empty($transfer_info)) {
                                echo $transfer_info->amount;
                            }
                            ?>" name="amount" required="" <?php
                                   if (!empty($transfer_info)) {
                                       echo 'disabled';
                                   }
                                   ?>>  
                        </div>
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
                                    if (!empty($transfer_info)) {
                                        echo $transfer_info->payment_methods_id == $p_method->payment_methods_id ? 'selected' : '';
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
                        if (!empty($transfer_info)) {
                            echo $transfer_info->reference;
                        }
                        ?>" name="reference" > 
                        <input class="form-control " type="hidden" value="<?php
                        if (!empty($transfer_info)) {
                            echo $transfer_info->from_account_id;
                        }
                        ?>" name="old_from_account_id" > 
                        <input class="form-control " type="hidden" value="<?php
                        if (!empty($transfer_info)) {
                            echo $transfer_info->amount;
                        }
                        ?>" name="old_amount" > 
                        <input class="form-control " type="hidden" value="<?php
                        if (!empty($transfer_info)) {
                            echo $transfer_info->to_account_id;
                        }
                        ?>" name="old_to_account_id" > 
                        <span class="help-block"><?= lang('reference_example') ?></span>                            
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