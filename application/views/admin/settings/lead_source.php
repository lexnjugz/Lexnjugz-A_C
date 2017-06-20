<?= message_box('success'); ?>
<?= message_box('error'); ?>
<div class="nav-tabs-custom col-sm-12">
    <!-- Tabs within a box -->
    <ul class="nav nav-tabs">
        <li class="<?= $active == 1 ? 'active' : ''; ?>"><a href="#manage" data-toggle="tab"><?= lang('lead_source') ?></a></li>
        <li class="<?= $active == 2 ? 'active' : ''; ?>"><a href="#new" data-toggle="tab"><?= lang('new_lead_source') ?></a></li>                                                                     
    </ul>
    <div class="tab-content no-padding">
        <!-- ************** general *************-->
        <div class="tab-pane <?= $active == 1 ? 'active' : ''; ?>" id="manage">
            <div class="table-responsive">
                <table class="table table-striped DataTables " id="DataTables">
                    <thead>
                        <tr>

                            <th ><?= lang('lead_source') ?></th>                                                                              
                            <th ><?= lang('action') ?></th>                      
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        if (!empty($all_lead_source)) {
                            foreach ($all_lead_source as $lead_source) {
                                ?>
                                <tr>
                                    <td><?= $lead_source->lead_source ?></td>                                    
                                    <td>
                                        <?= btn_edit('admin/settings/lead_source/edit_lead_source/' . $lead_source->lead_source_id) ?>
                                        <?= btn_delete('admin/settings/delete_lead_source/' . $lead_source->lead_source_id) ?>
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
            <form method="post" action="<?= base_url() ?>admin/settings/lead_source/update_lead_source/<?php
            if (!empty($lead_source_info)) {
                echo $lead_source_info->lead_source_id;
            }
            ?>" class="form-horizontal"> 
                <div class="form-group">
                    <label class="col-lg-3 control-label"><?= lang('lead_source') ?> <span class="text-danger">*</span></label>
                    <div class="col-lg-7">
                        <input type="text" name="lead_source"  value="<?php
                        if (!empty($lead_source_info)) {
                            echo $lead_source_info->lead_source;
                        }
                        ?>" class="form-control" placeholder="<?= lang('lead_source') ?>" required>
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