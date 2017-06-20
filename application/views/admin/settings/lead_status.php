<?= message_box('success'); ?>
<?= message_box('error'); ?>
<div class="nav-tabs-custom col-sm-12">
    <!-- Tabs within a box -->
    <ul class="nav nav-tabs">
        <li class="<?= $active == 1 ? 'active' : ''; ?>"><a href="#manage" data-toggle="tab"><?= lang('lead_status') ?></a></li>
        <li class="<?= $active == 2 ? 'active' : ''; ?>"><a href="#new" data-toggle="tab"><?= lang('new_lead_status') ?></a></li>                                                                     
    </ul>
    <div class="tab-content no-padding">
        <!-- ************** general *************-->
        <div class="tab-pane <?= $active == 1 ? 'active' : ''; ?>" id="manage">
            <div class="table-responsive">
                <table class="table table-striped DataTables " id="DataTables">
                    <thead>
                        <tr>

                            <th ><?= lang('lead_status') ?></th>                            
                            <th ><?= lang('lead_type') ?></th>                            
                            <th ><?= lang('action') ?></th>                      
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        if (!empty($all_lead_status)) {
                            foreach ($all_lead_status as $lead_status) {
                                ?>
                                <tr>
                                    <td><?= $lead_status->lead_status ?></td>
                                    <td><?= lang($lead_status->lead_type) ?></td>
                                    <td>
                                        <?= btn_edit('admin/settings/lead_status/edit_lead_status/' . $lead_status->lead_status_id) ?>
                                        <?= btn_delete('admin/settings/delete_lead_status/' . $lead_status->lead_status_id) ?>
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
            <form method="post" action="<?= base_url() ?>admin/settings/lead_status/update_lead_status/<?php
            if (!empty($lead_status_info)) {
                echo $lead_status_info->lead_status_id;
            }
            ?>" class="form-horizontal"> 
                <div class="form-group">
                    <label class="col-lg-3 control-label"><?= lang('lead_status') ?> <span class="text-danger">*</span></label>
                    <div class="col-lg-7">
                        <input type="text" name="lead_status"  value="<?php
                        if (!empty($lead_status_info)) {
                            echo $lead_status_info->lead_status;
                        }
                        ?>" class="form-control" placeholder="<?= lang('lead_status') ?>" required>
                    </div>
                </div>
                <div class="form-group terms">
                    <label class="col-lg-3 control-label"><?= lang('lead_type') ?> </label>
                    <div class="col-lg-7">
                        <select name="lead_type" class="form-control" >
                            <option value="close" <?= !empty($lead_status_info) && $lead_status_info->lead_type == 'close' ? 'selected' : '' ?>><?= lang('close') ?></option>
                            <option value="open" <?= !empty($lead_status_info) && $lead_status_info->lead_type == 'open' ? 'selected' : '' ?>><?= lang('open') ?></option>
                        </select>                        
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