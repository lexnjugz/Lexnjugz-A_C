<?= message_box('success'); ?>
<?= message_box('error'); ?>
<div class="nav-tabs-custom col-sm-12">
    <!-- Tabs within a box -->
    <ul class="nav nav-tabs">
        <li class="active"><a href="#manage" data-toggle="tab"><?= lang('opportunities_state_reason') ?></a></li>                                                                           
    </ul>
    <div class="tab-content no-padding">
        <!-- ************** general *************-->
        <div class="tab-pane active" id="manage">
            <div class="table-responsive">
                <table class="table table-striped DataTables " >
                    <thead>
                        <tr>

                            <th class="col-sm-3"><?= lang('opportunities_state') ?></th>                            
                            <th ><?= lang('reason') ?></th>                            
                            <th class="col-sm-1"><?= lang('action') ?></th>                      
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $all_opportunities_state_reason = $this->db->get('tbl_opportunities_state_reason')->result();
                        if (!empty($all_opportunities_state_reason)) {
                            foreach ($all_opportunities_state_reason as $opportunities_state) {
                                ?>
                                <tr>                                    
                                    <td><?= lang($opportunities_state->opportunities_state) ?></td>
                            <form method="post" action="<?= base_url() ?>admin/settings/opportunities_state_reason/<?= $opportunities_state->opportunities_state_reason_id ?>">
                                <td>
                                    <input name="opportunities_state_reason_<?= $opportunities_state->opportunities_state_reason_id ?>" value="<?= $opportunities_state->opportunities_state_reason ?>"  class="form-control" />                                        

                                </td>
                                <td><button type="submit" name="flag" value="1" class="btn btn-primary"><?= lang('update') ?></button></td>
                            </form>
                            </tr>
                            <?php
                        }
                    }
                    ?>
                    </tbody>
                </table>
            </div>
        </div>            
    </div>
</div>