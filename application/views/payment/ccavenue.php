<?php
$cur = $this->invoice_model->check_by(array('code' => $invoice_info['currency']), 'tbl_currencies');
?>
<div class="panel panel-default">
    <div class="panel-heading">
        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
        <h4 class="modal-title">Paying <strong><?=
                $cur->symbol
                ?> <?= number_format($invoice_info['amount'], 2) ?></strong> for Invoice # <?= $invoice_info['item_name'] ?> via CCAvenue</h4>
    </div>
    <div class="modal-body">

        <?php
        $attributes = array('id' => 'ccavenue', 'class' => 'form-horizontal');
        echo form_open('https://www.ccavenue.com/shopzone/cc_details.jsp', $attributes);
        ?>
        <p><strong>Are you sure to paid by CCAvenue </strong></p>

        <input type="hidden" name="invoice_id" value="<?= $invoice_info['item_number'] ?>">
        <input type="hidden" name="amount" value="<?= number_format($invoice_info['amount'], 2) ?>">
        <input type="hidden" name="amount" value="<?= number_format($invoice_info['amount'], 2) ?>">
        <input type="hidden" name="amount" value="<?= number_format($invoice_info['amount'], 2) ?>">
        <input type="hidden" name="amount" value="<?= number_format($invoice_info['amount'], 2) ?>">

        <input type=hidden name="Merchant_Id" value="<?= $this->config->item('ccavenue_merchant_id') ?>">
        <input type="hidden" name="Currency" value="<?= $invoice_info['currency'] ?>">
        <input type="hidden" name="Amount" value="<?= number_format($invoice_info['amount'], 2) ?>">
        <input type="hidden" name="Order_Id" value="<?= $invoice_info['item_name'] ?>">                
        <div class="modal-footer"> 
            <a href="#" class="btn btn-default" data-dismiss="modal"><?= lang('close') ?></a> 
            <button type="submit" class="btn btn-success" id="submitBtn">Process Payment</button>
        </div>
        </form>

    </div>
</div>