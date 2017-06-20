<?= message_box('success'); ?>
<div class="nav-tabs-custom">
    <!-- Tabs within a box -->
    <ul class="nav nav-tabs">
        <li class="<?= $active == 1 ? 'active' : ''; ?>"><a href="#manage" data-toggle="tab"><?= lang('all_estimates') ?></a></li>
        <li class="<?= $active == 2 ? 'active' : ''; ?>"><a href="#new" data-toggle="tab"><?= lang('create_estimate') ?></a></li>                                                                     
    </ul>
    <div class="tab-content no-padding">
        <!-- ************** general *************-->
        <div class="tab-pane <?= $active == 1 ? 'active' : ''; ?>" id="manage">
            <div class="table-responsive">
                <table class="table table-striped DataTables " id="DataTables">
                    <thead>
                        <tr>
                            <th><?= lang('estimate') ?></th>
                            <th><?= lang('created') ?></th>
                            <th><?= lang('due_date') ?></th>
                            <th><?= lang('client_name') ?></th>
                            <th><?= lang('amount') ?></th>
                            <th><?= lang('status') ?></th>
                            <th><?= lang('action') ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        if (!empty($all_estimates_info)) {
                            foreach ($all_estimates_info as $v_estimates) {
                                if ($v_estimates->status == 'Pending') {
                                    $label = "info";
                                } elseif ($v_estimates->status == 'Accepted') {
                                    $label = "success";
                                } else {
                                    $label = "danger";
                                }
                                ?>
                                <tr>                                    
                                    <td>
                                        <a class="text-info" href="<?= base_url() ?>admin/estimates/index/estimates_details/<?= $v_estimates->estimates_id ?>"><?= $v_estimates->reference_no ?></a></td>
                                    <td><?= strftime(config_item('date_format'), strtotime($v_estimates->date_saved)) ?></td>
                                    <td><?= strftime(config_item('date_format'), strtotime($v_estimates->due_date)) ?></td>
                                    <?php
                                    $client_info = $this->estimates_model->check_by(array('client_id' => $v_estimates->client_id), 'tbl_client');
                                    if ($client_info->client_status == 1) {
                                        $status = 'Person';
                                    } else {
                                        $status = 'Company';
                                    }
                                    ?>
                                    <td><?= $client_info->name . ' (' . $status . ')'; ?></td>
                                    <?php $currency = $this->estimates_model->client_currency_sambol($v_estimates->client_id); ?>                                                                                                            
                                    <td><?= $currency->symbol ?> <?= number_format($this->estimates_model->estimate_calculation('estimate_amount', $v_estimates->estimates_id), 2) ?></td>
                                    <td><span class="label label-<?= $label ?>"><?= lang(strtolower($v_estimates->status)) ?></span></td>
                                    <td>
                                        <?= btn_edit('admin/estimates/index/edit_estimates/' . $v_estimates->estimates_id) ?>
                                        <?= btn_delete('admin/estimates/delete/delete_estimates/' . $v_estimates->estimates_id) ?>
                                        <div class="btn-group">
                                            <button class="btn btn-xs btn-default dropdown-toggle" data-toggle="dropdown">
                                                <?= lang('change_status') ?>
                                                <span class="caret"></span></button>
                                            <ul class="dropdown-menu">		

                                                <?php if ($role == '1') { ?>	                                                                                                        
                                                    <li><a href="<?= base_url() ?>admin/estimates/index/email_estimates/<?= $v_estimates->estimates_id ?>"><?= lang('send_email') ?></a></li>
                                                    <li><a href="<?= base_url() ?>admin/estimates/index/estimates_details/<?= $v_estimates->estimates_id ?>"><?= lang('view_details') ?></a></li>
                                                    <li><a href="<?= base_url() ?>admin/estimates/index/estimates_history/<?= $v_estimates->estimates_id ?>"><?= lang('history') ?></a></li>
                                                    <li><a href="<?= base_url() ?>admin/estimates/change_status/declined/<?= $v_estimates->estimates_id ?>"><?= lang('declined') ?></a></li>
                                                    <li><a href="<?= base_url() ?>admin/estimates/change_status/accepted/<?= $v_estimates->estimates_id ?>"><?= lang('accepted') ?></a></li>                    
                                                <?php }
                                                ?>                                                
                                            </ul>
                                        </div>
                                    </td>
                                </tr>
                                <?php
                            }
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="tab-pane <?= $active == 2 ? 'active' : ''; ?>" id="new">
            <form role="form" enctype="multipart/form-data" id="form" action="<?php echo base_url(); ?>admin/estimates/save_estimates/<?php
            if (!empty($estimates_info)) {
                echo $estimates_info->estimates_id;
            }
            ?>" method="post" class="form-horizontal  ">
                <div class="form-group">
                    <label class="col-lg-3 control-label"><?= lang('reference_no') ?> <span class="text-danger">*</span></label>
                    <div class="col-lg-5">
                        <?php $this->load->helper('string'); ?>
                        <input type="text" class="form-control" value="<?php
                        if (!empty($estimates_info)) {
                            echo $estimates_info->reference_no;
                        } else {
                            echo config_item('estimate_prefix');
                            echo random_string('nozero', 5);
                        }
                        ?>" name="reference_no">
                    </div>

                </div>

                <div class="form-group">
                    <label class="col-lg-3 control-label"><?= lang('client') ?> <span class="text-danger">*</span> </label>
                    <div class="col-lg-5">
                        <select class="form-control select_box" style="width: 100%"  name="client_id"  required> 

                            <?php
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
                                    if (!empty($estimates_info)) {
                                        $estimates_info->client_id == $v_client->client_id ? 'selected' : '';
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
                    <label class="col-lg-3 control-label"><?= lang('due_date') ?></label> 
                    <div class="col-lg-5">
                        <div class="input-group">
                            <input type="text" name="due_date"  class="form-control datepicker" value="<?php
                            if (!empty($estimates_info->due_date)) {
                                echo $estimates_info->due_date;
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
                    <label class="col-lg-3 control-label"><?= lang('default_tax') ?> </label>
                    <div class="col-lg-4">
                        <div class="input-group  ">
                            <input class="form-control " value="<?php
                            if (!empty($estimates_info)) {
                                echo $estimates_info->tax;
                            } else {
                                echo $this->config->item('default_tax');
                            }
                            ?>" type="text" value="<?= $this->config->item('default_tax') ?>" name="tax">
                            <span class="input-group-addon">%</span>
                        </div>

                    </div>                   
                </div>	

                <!-- Start discount fields -->

                <div>

                    <div class="form-group">
                        <label class="col-lg-3 control-label"><?= lang('discount') ?> </label>
                        <div class="col-lg-4">
                            <div class="input-group  ">
                                <input class="form-control " value="<?php
                                if (!empty($estimates_info)) {
                                    echo $estimates_info->discount;
                                } else {
                                    echo '0';
                                }
                                ?>" type="text" value="0" name="discount">
                                <span class="input-group-addon">%</span>
                            </div>
                        </div>
                    </div>

                </div> 
                <!-- End discount Fields -->


                <div class="form-group terms">
                    <label class="col-lg-3 control-label"><?= lang('notes') ?> </label>
                    <div class="col-lg-7">
                        <textarea name="notes" class="form-control textarea"><?php
                            if (!empty($estimates_info)) {
                                echo $estimates_info->notes;
                            } else {
                                echo $this->config->item('estimate_terms');
                            }
                            ?></textarea>                        
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-lg-3 control-label"></label> 
                    <div class="col-lg-5">
                        <button type="submit" class="btn btn-sm btn-primary"><i class="fa fa-plus"></i> <?= lang('create_estimate') ?></button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>