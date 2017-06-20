<?= message_box('success') ?>

<section class="content-header">
    <div class="row">
        <div class="col-sm-8">                        
            <?php
            $client_info = $this->estimates_model->check_by(array('client_id' => $estimates_info->client_id), 'tbl_client');

            $client_lang = $client_info->language;
            $language_info = $this->lang->load('en_lang', $client_lang, TRUE, FALSE, '', TRUE);
            ?>

            <div class="btn-group">
                <button class="btn btn-sm btn-default dropdown-toggle" data-toggle="dropdown">
                    <?= lang('more_actions') ?>
                    <span class="caret"></span></button>
                <ul class="dropdown-menu">                    
                    <li><a href="<?= base_url() ?>client/estimates/change_status/declined/<?= $estimates_info->estimates_id ?>"><?= lang('declined') ?></a></li>
                    <li><a href="<?= base_url() ?>client/estimates/change_status/accepted/<?= $estimates_info->estimates_id ?>"><?= lang('accepted') ?></a></li>                    
                </ul>
            </div>            
        </div>
        <div class="col-sm-4 pull-right">                                    
            <a onclick="print_estimates('print_estimates')" href="#" data-toggle="tooltip" data-placement="top" title="" data-original-title="Print" class="btn btn-sm btn-danger pull-right"  >
                <i class="fa fa-print"></i>
            </a>            

            <a style="margin-right: 5px" href="<?= base_url() ?>client/estimates/index/pdf_estimates/<?= $estimates_info->estimates_id ?>" data-toggle="tooltip" data-placement="top" title="" data-original-title="PDF" class="btn btn-sm btn-success pull-right" >
                <i class="fa fa-file-pdf-o"></i>
            </a>
        </div>                            
    </div> 
</section>            
<section class="content">
    <!-- Start Display Details -->
    <?php
    if (strtotime($estimates_info->due_date) < time() AND $estimates_info->status == 'Pending') {
        ?>
        <div class="alert alert-warning hidden-print">
            <button type="button" class="close" data-dismiss="alert">×</button> <i class="fa fa-warning"></i>
            <?= lang('estimate_overdue') ?>
        </div>
        <?php
    }
    ?>
    <!-- Main content -->
    <div class="row" >                
        <section class="invoice" id="print_estimates">
            <!-- title row -->
            <div class="row">
                <div class="col-xs-12">
                    <h2 class="page-header">
                        <img style="width: 60px;width: 60px;margin-top: -10px;margin-right: 10px;" src="<?= base_url() . config_item('invoice_logo') ?>" ><?= config_item('company_name') ?>
                        <small class="pull-right"><?= $language_info['estimate_date'] ?> : <?= strftime(config_item('date_format'), strtotime($estimates_info->date_saved)); ?></small>
                    </h2>
                </div><!-- /.col -->
            </div>
            <!-- info row -->
            <div class="row invoice-info">
                <div class="col-sm-4 estimates-col">
                    <?= $language_info['received_from'] ?>
                    <address>                        
                        <strong><?= (config_item('company_legal_name_' . $client_lang) ? config_item('company_legal_name_' . $client_lang) : config_item('company_legal_name')) ?></strong><br>
                        <?= (config_item('company_address_' . $client_lang) ? config_item('company_address_' . $client_lang) : config_item('company_address')) ?><br>
                        <?= (config_item('company_city_' . $client_lang) ? config_item('company_city_' . $client_lang) : config_item('company_city')) ?>
                        <?= config_item('company_zip_code') ?><br>
                        <?= (config_item('company_country_' . $client_lang) ? config_item('company_country_' . $client_lang) : config_item('company_country')) ?><br/>
                        <?= $language_info['phone'] ?> : <?= config_item('company_phone') ?>
                    </address>
                </div><!-- /.col -->
                <div class="col-sm-4 estimates-col">
                    <?= $language_info['bill_to'] ?>:
                    <address>
                        <?php
                        if ($client_info->client_status == 1) {
                            $status = 'Person';
                        } else {
                            $status = 'Company';
                        }
                        ?>                                    
                        <strong><?= ucfirst($client_info->name . ' (' . $status . ')') ?></strong><br>
                        <?= ucfirst($client_info->address) ?><br>
                        <?= ucfirst($client_info->city) ?><br>
                        <?= ucfirst($client_info->country) ?> <br>
                        <?= $language_info['phone'] ?>: <?= ucfirst($client_info->phone) ?><br>                                       
                    </address>
                </div><!-- /.col -->
                <div class="col-sm-4 estimates-col">
                    <b>Estimates # <?= $estimates_info->reference_no ?></b><br/>                                        
                    <b><?= $language_info['valid_until'] ?> :</b> <?= strftime(config_item('date_format'), strtotime($estimates_info->due_date)); ?><br/>
                    <?php
                    if ($estimates_info->status == 'Accepted') {
                        $label = 'success';
                    } else {
                        $label = 'danger';
                    }
                    ?>
                    <b><?= $language_info['estimate_status'] ?>:</b> <span class="label label-<?= $label ?>"><?= $estimates_info->status ?></span>
                </div><!-- /.col -->
            </div><!-- /.row -->
            <div class="row">
                <div class="col-xs-12 table-responsive">
                    <form method="post" action="<?= base_url() ?>client/estimates/add_item/<?php
                    if (!empty($item_info)) {
                        echo $item_info->estimate_items_id;
                    }
                    ?>">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th class="col-sm-1"><?= $language_info['qty'] ?></th>
                                    <th><?= $language_info['item_name'] ?></th>                                                
                                    <th><?= $language_info['description'] ?></th>
                                    <th><?= $language_info['tax_rate'] ?> </th>
                                    <th class="col-sm-2"><?= $language_info['unit_price'] ?></th>
                                    <th class="col-sm-1" ><?= $language_info['tax'] ?></th>
                                    <th><?= $language_info['total'] ?></th>
                                    <th class="col-sm-1 hidden-print" ><?= $language_info['action'] ?></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $estimates_items = $this->estimates_model->ordered_items_by_id($estimates_info->estimates_id);

                                if (!empty($estimates_items)) :
                                    foreach ($estimates_items as $v_item) :
                                        $item_name = $v_item->item_name ? $v_item->item_name : $v_item->item_desc;
                                        ?>
                                        <tr>
                                            <td><?= $v_item->quantity ?></td>
                                            <td><?= $item_name ?></td>                                                
                                            <td><?= nl2br($v_item->item_desc) ?></td>
                                            <td><?= $v_item->item_tax_rate ?>%</td>
                                            <td><?= number_format($v_item->unit_cost, 2) ?></td>
                                            <td><?= number_format($v_item->item_tax_total, 2) ?></td>
                                            <td><?= number_format($v_item->total_cost, 2) ?></td>
                                            <td class="hidden-print"><?php if ($role == '1') { ?>
                                                    <?= btn_edit('admin/estimates/index/estimates_details/' . $v_item->estimates_id . '/' . $v_item->estimate_items_id) ?>                                                
                                                    <?= btn_delete('admin/estimates/delete/delete_item/' . $v_item->estimates_id . '/' . $v_item->estimate_items_id) ?>                                                
                                                <?php } ?></td>
                                        </tr>                                         
                                    <?php endforeach; ?>
                                <?php endif ?>
                                <?php if ($role == '1') { ?>
                                    <?php if ($estimates_info->status != 'Paid') { ?>

                                        <tr class="hidden-print">
                                    <input type="hidden" name="estimates_id" value="<?= $estimates_info->estimates_id ?>">
                                    <input type="hidden" name="item_order" value="<?= count($estimates_items) + 1 ?>">                                     
                                    <td><input type="text" name="quantity[]" value="<?php
                                        if (!empty($item_info)) {
                                            echo $item_info->quantity;
                                        }
                                        ?>" placeholder="1" required class="form-control"></td>
                                    <td><input type="text" name="item_name[]" value="<?php
                                        if (!empty($item_info)) {
                                            echo $item_info->item_name;
                                        }
                                        ?>" required  placeholder="Item Name" class="form-control"></td>
                                    <td><textarea rows="1" name="item_desc[]" placeholder="Item Description" class="form-control"><?php
                                            if (!empty($item_info)) {
                                                echo $item_info->item_desc;
                                            }
                                            ?></textarea></td>
                                    <td>
                                        <select name="item_tax_rate[]" class="form-control  ">
                                            <option value="0.00"><?= lang('none') ?></option>
                                            <?php
                                            $tax_rates = $this->db->get('tbl_tax_rates')->result();
                                            if (!empty($tax_rates)) {
                                                foreach ($tax_rates as $v_tax) {
                                                    ?>
                                                    <option value="<?= $v_tax->tax_rate_percent ?>" <?php
                                                    if (!empty($item_info) && $item_info->item_tax_rate == $v_tax->tax_rate_percent) {
                                                        echo 'selected';
                                                    }
                                                    ?>><?= $v_tax->tax_rate_name ?></option>
                                                            <?php
                                                        }
                                                    }
                                                    ?>
                                        </select>
                                    </td>
                                    <td><input type="text" name="unit_cost[]" value="<?php
                                        if (!empty($item_info)) {
                                            echo $item_info->unit_cost;
                                        }
                                        ?>" required placeholder="100" class="form-control"></td>
                                    <td><input type="text" value="<?php
                                        if (!empty($item_info)) {
                                            echo $item_info->item_tax_total;
                                        }
                                        ?>" name="tax" placeholder="0.00" readonly="" class="form-control"></td>

                                    <td></td>
                                    </div>

                                <?php } ?>
                            <?php } ?>                                    
                            </tbody>
                        </table>
                    </form>
                    <div class="row">
                        <div class="col-xs-8">

                            <p class="text-muted well well-sm no-shadow" style="margin-top: 10px;">
                                <?= $estimates_info->notes ?>
                            </p>
                        </div><!-- /.col -->
                        <div class="col-xs-4">                                    
                            <div class="table-responsive">
                                <table class="table">
                                    <tr>
                                        <th style="width:50%" class="text-right"><?= $language_info['sub_total'] ?> :</th>
                                        <td> <?= number_format($this->estimates_model->estimate_calculation('estimate_cost', $estimates_info->estimates_id), 2) ?></td>
                                    </tr>
                                    <?php if ($estimates_info->tax > 0.00): ?>
                                        <tr>
                                            <td class="text-right">
                                                <strong><?= $language_info['tax'] ?> : <?php echo $estimates_info->tax; ?>%</strong></td>
                                            <td><?= number_format($this->estimates_model->estimate_calculation('tax', $estimates_info->estimates_id), 2) ?> </td>
                                        </tr>
                                    <?php endif ?>
                                    <?php if ($estimates_info->discount > 0) { ?>
                                        <tr>
                                            <td class="text-right">
                                                <strong><?= $language_info['discount'] ?> : - <?php echo $estimates_info->discount; ?>%</strong></td>
                                            <td><?= number_format($this->estimates_model->estimate_calculation('discount', $estimates_info->estimates_id), 2) ?> </td>
                                        </tr>
                                        <?php
                                    }
                                    $currency = $this->estimates_model->client_currency_sambol($estimates_info->client_id);
                                    ?>
                                    <tr>
                                        <td class="text-right"><strong><?= $language_info['total'] ?> :</strong></td>
                                        <td><?= $currency->symbol ?> <?= number_format($this->estimates_model->estimate_calculation('estimate_amount', $estimates_info->estimates_id), 2) ?></td>
                                    </tr>
                                </table>
                            </div>
                        </div><!-- /.col -->
                    </div>                                
                </div>
            </div>
        </section>
    </div>                

</section>  
<script type="text/javascript">
    function print_estimates(print_estimates) {
        var printContents = document.getElementById(print_estimates).innerHTML;
        var originalContents = document.body.innerHTML;
        document.body.innerHTML = printContents;
        window.print();
        document.body.innerHTML = originalContents;
    }
</script>
