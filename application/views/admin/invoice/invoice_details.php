<?= message_box('success') ?>
<?= message_box('error') ?>

<section class="content-header">
    <div class="row">
        <div class="col-sm-8">                        
            <?php
            $client_info = $this->invoice_model->check_by(array('client_id' => $invoice_info->client_id), 'tbl_client');

            $client_lang = $client_info->language;
            $language_info = $this->lang->load('en_lang', $client_lang, TRUE, FALSE, '', TRUE);
            ?>

            <?php if ($role == '1') { ?>
                <a data-toggle="modal" data-target="#myModal" href="<?= base_url() ?>admin/invoice/insert_items/<?= $invoice_info->invoices_id ?>" title="<?= lang('item_quick_add') ?>" class="btn btn-sm btn-primary" >
                    <i class="fa fa-list-alt text-white"></i> <?= lang('from_items') ?></a>
            <?php }
            ?>
            <?php if ($invoice_info->show_client == 'Yes') { ?>                
                <a class="btn btn-sm btn-success" href="<?= base_url() ?>admin/invoice/change_status/hide/<?= $invoice_info->invoices_id ?>" title="<?= lang('hide_to_client') ?>"><i class="fa fa-eye-slash"></i> <?= lang('hide_to_client') ?>
                </a><?php } else { ?>
                <a class="btn btn-sm btn-warning" href="<?= base_url() ?>admin/invoice/change_status/show/<?= $invoice_info->invoices_id ?>" title="<?= lang('show_to_client') ?>"><i class="fa fa-eye"></i> <?= lang('show_to_client') ?>
                </a><?php } ?>

            <?php if ($role == '1') { ?>
                <?php if ($this->invoice_model->get_invoice_cost($invoice_info->invoices_id) > 0) { ?>
                    <a class="btn btn-sm btn-danger" href="<?= base_url() ?>admin/invoice/manage_invoice/payment/<?= $invoice_info->invoices_id ?>"
                       title="<?= lang('add_payment') ?>"><i class="fa fa-credit-card"></i> <?= lang('pay_invoice') ?>
                    </a>                    
                    <?php
                }
            }
            ?>


            <div class="btn-group">
                <button class="btn btn-sm btn-default dropdown-toggle" data-toggle="dropdown">
                    <?= lang('more_actions') ?>
                    <span class="caret"></span></button>
                <ul class="dropdown-menu">
                    <?php if ($this->invoice_model->get_invoice_cost($invoice_info->invoices_id) > 0) { ?>
                        <li>
                            <a href="<?= base_url() ?>admin/invoice/manage_invoice/email_invoice/<?= $invoice_info->invoices_id ?>" title="<?= lang('email_invoice') ?>"><?= lang('email_invoice') ?></a>
                        </li>

                        <li>
                            <a href="<?= base_url() ?>admin/invoice/manage_invoice/send_reminder/<?= $invoice_info->invoices_id ?>" title="<?= lang('send_reminder') ?>"><?= lang('send_reminder') ?></a>
                        </li>
                        <li>
                            <a href="<?= base_url() ?>admin/invoice/manage_invoice/send_overdue/<?= $invoice_info->invoices_id ?>" title="<?= lang('send_invoice_overdue') ?>"><?= lang('send_invoice_overdue') ?></a>
                        </li>
                        <li><a href="<?= base_url() ?>admin/invoice/manage_invoice/invoice_history/<?= $invoice_info->invoices_id ?>"><?= lang('invoice_history') ?></a></li>
                    <?php } ?>

                    <?php if ($role == '1') { ?>
                        <li class="divider"></li>
                        <li><a href="<?= base_url() ?>admin/invoice/manage_invoice/create_invoice/<?= $invoice_info->invoices_id ?>"><?= lang('edit_invoice') ?></a></li>
                    <?php } ?>
                    <?php if ($role == '1') { ?>
                        <li><a href="<?= base_url() ?>admin/invoice/delete/delete_invoice/<?= $invoice_info->invoices_id ?>" ><?= lang('delete_invoice') ?></a></li>
                    <?php } ?>
                </ul>
            </div>
            <?php if ($role == '1' AND $invoice_info->recurring == 'Yes') { ?>
                <a onclick="return confirm('<?= lang('stop_recurring_alert') ?>')" class="btn btn-sm btn-warning" href="<?= base_url() ?>admin/invoice/stop_recurring/<?= $invoice_info->invoices_id ?>"
                   title="<?= lang('stop_recurring') ?>" ><i class="fa fa-retweet"></i> <?= lang('stop_recurring') ?>
                </a>
            <?php } ?>

        </div>
        <div class="col-sm-4 pull-right">                                    
            <a onclick="print_invoice('print_invoice')" href="#" data-toggle="tooltip" data-placement="top" title="" data-original-title="Print" class="btn btn-sm btn-danger pull-right"  >
                <i class="fa fa-print"></i>
            </a>            

            <a style="margin-right: 5px" href="<?= base_url() ?>admin/invoice/manage_invoice/pdf_invoice/<?= $invoice_info->invoices_id ?>" data-toggle="tooltip" data-placement="top" title="" data-original-title="PDF" class="btn btn-sm btn-success pull-right" >
                <i class="fa fa-file-pdf-o"></i>
            </a>
        </div>                            
    </div> 
</section>            
<section class="content">
    <!-- Start Display Details -->
    <?php
    $payment_status = $this->invoice_model->get_payment_status($invoice_info->invoices_id);
    if (strtotime($invoice_info->due_date) < time() AND $payment_status != lang('fully_paid')) {
        ?>
        <div class="alert alert-danger hidden-print">
            <button type="button" class="close" data-dismiss="alert">×</button> <i class="fa fa-warning"></i>
            <?= lang('invoice_overdue') ?>
        </div>
        <?php
    }
    ?>
    <!-- Main content -->
    <div class="row" >                
        <section class="invoice" id="print_invoice">
            <!-- title row -->
            <div class="row">
                <div class="col-xs-12">
                    <h2 class="page-header">
                        <img style="width: 60px;width: 60px;margin-top: -10px;margin-right: 10px;" src="<?= base_url() . config_item('invoice_logo') ?>" ><?= config_item('company_name') ?>
                        <small class="pull-right"><?= $language_info['invoice_date'] ?> : <?= strftime(config_item('date_format'), strtotime($invoice_info->date_saved)); ?></small>
                    </h2>
                </div><!-- /.col -->
            </div>
            <!-- info row -->
            <div class="row invoice-info">
                <div class="col-sm-4 invoice-col">
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
                <div class="col-sm-4 invoice-col">
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
                <div class="col-sm-4 invoice-col">
                    <b>Invoice # <?= $invoice_info->reference_no ?></b><br/>                                        
                    <b><?= $language_info['payment_due'] ?> :</b> <?= strftime(config_item('date_format'), strtotime($invoice_info->due_date)); ?><br/>
                    <?php
                    if ($payment_status == lang('fully_paid')) {
                        $label = 'success';
                    } else {
                        $label = 'danger';
                    }
                    ?>
                    <b><?= $language_info['payment_status'] ?>:</b> <span class="label label-<?= $label ?>"><?= $payment_status ?></span>
                </div><!-- /.col -->
            </div><!-- /.row -->
            <div class="row">
                <div class="col-xs-12 table-responsive">
                    <form method="post" action="<?= base_url() ?>admin/invoice/add_item/<?php
                    if (!empty($item_info)) {
                        echo $item_info->items_id;
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
                                $invoice_items = $this->invoice_model->ordered_items_by_id($invoice_info->invoices_id);

                                if (!empty($invoice_items)) :
                                    foreach ($invoice_items as $v_item) :
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
                                                    <?= btn_edit('admin/invoice/manage_invoice/invoice_details/' . $v_item->invoices_id . '/' . $v_item->items_id) ?>                                                
                                                    <?= btn_delete('admin/invoice/delete/delete_item/' . $v_item->invoices_id . '/' . $v_item->items_id) ?>                                                
                                                <?php } ?></td>
                                        </tr>                                         
                                    <?php endforeach; ?>
                                <?php endif ?>
                                <?php if ($role == '1') { ?>
                                    <?php if ($invoice_info->status != 'Paid') { ?>

                                        <tr class="hidden-print">
                                    <input type="hidden" name="invoices_id" value="<?= $invoice_info->invoices_id ?>">
                                    <input type="hidden" name="item_order" value="<?= count($invoice_items) + 1 ?>">                                     
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

                                    <td>               
                                        <strong><a href="javascript:void(0);" id="add_more" class="addCF "><i class="fa fa-plus"></i>&nbsp;&nbsp;More</a></strong>                            
                                    </td>                                    
                                    </tr>
                                    <table id="add_new">

                                    </table>                                    
                                <?php } ?>
                            <?php } ?>        
                            <div class="hidden-print pull-right" style="margin-bottom: 10px">
                                <td colspan="8" style="text-align: right;"><button type="submit" class="btn btn-primary"><?= lang('save') ?></button>                     </td>
                            </div>                              
                            </tbody>
                        </table>
                    </form>
                    <div class="row">
                        <div class="col-xs-8">

                            <p class="text-muted well well-sm no-shadow" style="margin-top: 10px;">
                                <?= $invoice_info->notes ?>
                            </p>
                        </div><!-- /.col -->
                        <div class="col-xs-4">                                    
                            <div class="table-responsive">
                                <table class="table">
                                    <tr>
                                        <th style="width:50%" class="text-right"><?= $language_info['sub_total'] ?> :</th>
                                        <td> <?= number_format($this->invoice_model->calculate_to('invoice_cost', $invoice_info->invoices_id), 2) ?></td>
                                    </tr>
                                    <?php if ($invoice_info->tax > 0.00): ?>
                                        <tr>
                                            <td class="text-right">
                                                <strong><?= $language_info['tax'] ?> : <?php echo $invoice_info->tax; ?>%</strong></td>
                                            <td><?= number_format($this->invoice_model->calculate_to('tax', $invoice_info->invoices_id), 2) ?> </td>
                                        </tr>
                                    <?php endif ?>
                                    <?php if ($invoice_info->discount > 0) { ?>
                                        <tr>
                                            <td class="text-right">
                                                <strong><?= $language_info['discount'] ?> : - <?php echo $invoice_info->discount; ?>%</strong></td>
                                            <td><?= number_format($this->invoice_model->calculate_to('discount', $invoice_info->invoices_id), 2) ?> </td>
                                        </tr>
                                        <?php
                                    }
                                    $paid_amount = number_format($this->invoice_model->calculate_to('paid_amount', $invoice_info->invoices_id), 2);
                                    if ($paid_amount > 0.00) {
                                        ?>
                                        <tr>
                                            <td class="text-right"><strong><?= $language_info['paid_amount'] ?>:</strong></td>
                                            <td><?= $paid_amount ?> </td>
                                        </tr>
                                        <?php
                                    }
                                    $currency = $this->invoice_model->client_currency_sambol($invoice_info->client_id);
                                    ?>
                                    <tr>

                                        <td class="text-right"><strong>
                                                <?php
                                                if ($paid_amount > 0.00) {
                                                    $total = $language_info['total_due'];
                                                } else {
                                                    $total = $language_info['total'];
                                                }
                                                ?>
                                                <?= $total ?> :</strong></td>
                                        <td><?= $currency->symbol ?> <?= number_format($this->invoice_model->calculate_to('invoice_due', $invoice_info->invoices_id), 2) ?></td>
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
    function print_invoice(print_invoice) {
        var printContents = document.getElementById(print_invoice).innerHTML;
        var originalContents = document.body.innerHTML;
        document.body.innerHTML = printContents;
        window.print();
        document.body.innerHTML = originalContents;
    }
</script>
<script type="text/javascript">
    $(document).ready(function() {
        var maxAppend = 0;
        $("#add_more").click(function() {
            if (maxAppend >= 4)
            {
                alert("Maximum 5 File is allowed");
            } else {
                var add_new = $('<tr style="display: block;margin-bottom: 9px;"><td><input type="text" name="quantity[]" placeholder="1" required class="form-control"></td>">\n\
                    <td><input type="text" name="item_name[]" required  placeholder="Item Name" class="form-control"></td>\n\
                        <td><textarea rows="1" name="item_desc[]" placeholder="Item Description" class="form-control"></textarea></td>\n\
                        <td><select name="item_tax_rate[]" class="form-control"><option value="0.00"><?= lang('none') ?></option>\n\\n\
<?php
$tax_rates = $this->db->get('tbl_tax_rates')->result();
if (!empty($tax_rates)) {
    foreach ($tax_rates as $v_tax) {
        ?><option value="<?= $v_tax->tax_rate_percent ?>"><?= $v_tax->tax_rate_name ?></option><?php
    }
}
?></select></td>\n\
    <td><input type="text" name="unit_cost[]" required placeholder="100" class="form-control"></td>\n\
<td><input type="text" name="tax" placeholder="0.00" readonly="" class="form-control"></td>\n\
<td><a href="javascript:void(0);" class="remCF">&nbsp;&nbsp;&nbsp;<i class="fa fa-times"></i></a></strong></td></tr>\n<br/>');
                maxAppend++;
                $("#add_new").append(add_new);
            }
        });

        $("#add_new").on('click', '.remCF', function() {
            $(this).parent().parent().remove();
        });
    });
</script>       