<!DOCTYPE html>
<html>
    <head>
        <title>Invoice</title>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <style>
            th
            {
                padding: 10px 0px 5px 5px; text-align: left; font-size: 13px;;
            }
            td
            {
                padding: 5px 0px 0px 5px; text-align: left; font-size: 13px;
            }
            .notes{
                color:#777;
                min-height: 20px;
                padding: 19px;
                margin-bottom: 20px;
                background-color: #f5f5f5;
                border: 1px solid #e3e3e3;
                border-radius: 4px;
                -webkit-box-shadow: inset 0 1px 1px rgba(0, 0, 0, .05);
                box-shadow: inset 0 1px 1px rgba(0, 0, 0, .05);
            }
        </style>

    </head>
    <body style="min-width: 98%; min-height: 100%; overflow: hidden; alignment-adjust: central;">

        <?php
        $client_info = $this->estimates_model->check_by(array('client_id' => $estimates_info->client_id), 'tbl_client');

        $client_lang = $client_info->language;
        $language_info = $this->lang->load('en_lang', $client_lang, TRUE, FALSE, '', TRUE);
        ?>    
        <br />
        <div style="width: 100%; border-bottom: 2px solid black;">
            <table style="width: 100%; vertical-align: middle;">
                <tr>

                    <td style="width: 35px; border: 0px;padding-bottom: 10px;">
                        <img style="width: 60px;width: 60px;margin-top: -10px;margin-right: 10px;" src="<?= base_url() . config_item('invoice_logo') ?>" >
                    </td>
                    <td style="border: 0px;">
                        <p style="margin-left: 10px; font: 22px lighter;"><?= config_item('company_name') ?></p>
                    </td>                    
                    <td style="border: 0px;float: right;">
                        <small style="float: right;"><?= $language_info['estimate_date'] ?> : <?= strftime(config_item('date_format'), strtotime($estimates_info->date_saved)); ?></small>
                    </td>                    
                </tr>
            </table>
        </div>
        <br />
        <div style="width: 100%;">
            <table style="width: 100%; font-family: Arial, Helvetica, sans-serif; border-collapse: collapse;">
                <thead>

                    <tr style="width: 100%;margin-top: 15px;">
                        <th>
                            <strong><?= $language_info['received_from'] ?></strong>
                        </th>
                        <th>
                            <strong><?= $language_info['bill_to'] ?>:</strong>
                        </th>
                        <th>

                        </th>
                    </tr>

                </thead>
                <tbody>
                    <tr style="width: 100%;">
                        <td>
                            <address>                        
                                <strong><?= (config_item('company_legal_name_' . $client_lang) ? config_item('company_legal_name_' . $client_lang) : config_item('company_legal_name')) ?></strong><br/>
                                <?= (config_item('company_address_' . $client_lang) ? config_item('company_address_' . $client_lang) : config_item('company_address')) ?><br/>
                                <?= (config_item('company_city_' . $client_lang) ? config_item('company_city_' . $client_lang) : config_item('company_city')) ?><br/>
                                <?= config_item('company_zip_code') ?><br/>
                                <?= (config_item('company_country_' . $client_lang) ? config_item('company_country_' . $client_lang) : config_item('company_country')) ?><br/>
                                <?= $language_info['phone'] ?> :<?= config_item('company_phone') ?><br/>
                            </address>
                        </td>
                        <td>
                            <address>
                                <?php
                                if ($client_info->client_status == 1) {
                                    $status = 'Person';
                                } else {
                                    $status = 'Company';
                                }
                                ?>                                    
                                <strong><?= ucfirst($client_info->name . ' (' . $status . ')') ?></strong><br/>
                                <?= ucfirst($client_info->address) ?><br/>
                                <?= ucfirst($client_info->city) ?><br/>
                                <?= ucfirst($client_info->country) ?> <br/>
                                <?= $language_info['phone'] ?>: <a href="tel:<?= ucfirst($client_info->phone) ?>"><?= ucfirst($client_info->phone) ?></a><br/>
                            </address>
                        </td>
                        <td>
                            <p><b>Invoice # <?= $estimates_info->reference_no ?></b><br/>
                            <p></p>            
                            <p><b><?= $language_info['valid_until'] ?> :</b> <?= strftime(config_item('date_format'), strtotime($estimates_info->due_date)); ?></p>
                            <?php
                            if ($estimates_info->status == 'Accepted') {
                                $label = 'success';
                            } else {
                                $label = 'danger';
                            }
                            ?>
                            <p><b><?= $language_info['estimate_status'] ?>:</b> <?= $estimates_info->status ?>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        <br/>
        <table style="width: 100%; font-family: Arial, Helvetica, sans-serif; border-collapse: collapse;">
            <thead>
                <tr style="width: 100%;margin-top: 15px;">                    
                    <th style="width: 8.33333333%;"><?= $language_info['qty'] ?></th>
                    <th style="width: 25%;"><?= $language_info['item_name'] ?></th>                                                
                    <th style="width: 25%;;"><?= $language_info['description'] ?></th>
                    <th style=""><?= $language_info['tax_rate'] ?> </th>
                    <th style=""><?= $language_info['unit_price'] ?></th>
                    <th style="width: 8.33333333%;"><?= $language_info['tax'] ?></th>
                    <th style=""><?= $language_info['total'] ?></th>    
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
                        </tr>                                         
                    <?php endforeach; ?>
                <?php endif ?>
                <tr>
                    <td colspan="4"><p class="notes" style="margin-top: 10px;">
                            <?= $estimates_info->notes ?>
                        </p></td>
                    <td colspan="2">
                        <table class="table1" style="width: 100%;border:0px">
                            <tr >
                                <th style="border:0px;text-align: right" ><?= $language_info['sub_total'] ?> :</th>
                                <td> <?= number_format($this->estimates_model->estimate_calculation('estimate_cost', $estimates_info->estimates_id), 2) ?></td>
                            </tr>
                            <?php if ($estimates_info->tax > 0.00): ?>
                                <tr >
                                    <td style="text-align: right">
                                        <strong><?= $language_info['tax'] ?> : <?php echo $estimates_info->tax; ?>%</strong></td>
                                    <td><?= number_format($this->estimates_model->estimate_calculation('tax', $estimates_info->estimates_id), 2) ?> </td>
                                </tr>
                            <?php endif ?>
                            <?php if ($estimates_info->discount > 0) { ?>
                                <tr >
                                    <td style="text-align: right">
                                        <strong><?= $language_info['discount'] ?> : - <?php echo $estimates_info->discount; ?>%</strong></td>
                                    <td><?= number_format($this->estimates_model->estimate_calculation('discount', $estimates_info->estimates_id), 2) ?> </td>
                                </tr>
                                <?php
                            }
                            $currency = $this->estimates_model->client_currency_sambol($estimates_info->client_id);
                            ?>
                            <tr >
                                <td style="text-align: right"><strong><?= $language_info['total'] ?> :</strong></td>
                                <td><?= $currency->symbol ?> <?= number_format($this->estimates_model->estimate_calculation('estimate_amount', $estimates_info->estimates_id), 2) ?></td>
                            </tr>
                        </table>   
                    </td>
                </tr>
            </tbody>

        </table>

    </body>
</html>
