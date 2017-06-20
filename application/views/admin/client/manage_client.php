<?php echo message_box('success'); ?>
<?php echo message_box('error'); ?>

<div class="row">
    <div class="col-sm-12" data-spy="scroll" data-offset="0">
        <div class="wrap-fpanel">
            <div class="panel panel-default">
                <!-- Default panel contents -->
                <div class="panel-heading">
                    <div class="panel-title">
                        <strong><?= lang('client_list') ?></strong>
                    </div>
                </div>
                <br />
                <div class="row">
                    <div class="col-lg-12">                                        
                        <table class="table table-striped DataTables " id="DataTables">
                            <thead>
                                <tr>

                                    <th><?= lang('name') ?> </th>
                                    <th><?= lang('contacts') ?></th>
                                    <th class="hidden-sm"><?= lang('primary_contact') ?></th>
                                    <th><?= lang('website') ?> </th>
                                    <th><?= lang('email') ?> </th>
                                    <th><?= lang('type') ?> </th>
                                    <th class=""><?= lang('action') ?></th>
                                </tr> </thead> <tbody>
                                <?php
                                if (!empty($all_client_info)) {
                                    foreach ($all_client_info as $client_details) {
                                        ?>
                                        <tr>

                                            <td><a href="<?= base_url() ?>admin/client/client_details/<?= $client_details->client_id ?>" class="text-info">
                                                    <?= $client_details->name ?></a></td>
                                            <td><span class="label label-success" data-toggle="tooltip" data-palcement="top" title="<?= lang('contacts') ?>"><?= $this->client_model->count_rows('tbl_account_details', array('company' => $client_details->client_id)) ?></span></td>
                                            <td class="hidden-sm"><?php
                                                if ($client_details->primary_contact != 0) {
                                                    $contacts = $client_details->primary_contact;
                                                } else {
                                                    $contacts = NULL;
                                                }
                                                $primary_contact = $this->client_model->check_by(array('account_details_id' => $contacts), 'tbl_account_details');
                                                if ($primary_contact) {
                                                    echo $primary_contact->fullname;
                                                }
                                                ?></td>
                                            <td><a href="<?= $client_details->website ?>" class="text-info" target="_blank">
                                                    <?= $client_details->website ?></a>
                                            </td>
                                            <td><?= $client_details->email ?></td>
                                            <td><?php
                                                if ($client_details->client_status == 1) {
                                                    echo 'Person';
                                                } else {
                                                    echo 'Company';
                                                }
                                                ?></td>
                                            <td>
                                                <?php echo btn_edit('admin/client/new_client/' . $client_details->client_id) ?>                                                 
                                                <?php echo btn_view('admin/client/client_details/' . $client_details->client_id) ?>                                                 
                                                <?php echo btn_delete('admin/client/delete_client/' . $client_details->client_id) ?>                                                                                                
                                            </td>
                                        </tr>
                                        <?php
                                    }
                                } else {
                                    ?>
                                    <tr><td colspan="7">
                                            There is no data to display
                                        </td></tr>
                                <?php }
                                ?>


                            </tbody>
                        </table>

                    </div>
                </div>                
            </div>
        </div>
    </div>
</div>