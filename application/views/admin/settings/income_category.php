<?= message_box('success'); ?>
<?= message_box('error'); ?>
<div class="nav-tabs-custom col-sm-12">
    <!-- Tabs within a box -->
    <ul class="nav nav-tabs">
        <li class="<?= $active == 1 ? 'active' : ''; ?>"><a href="#manage" data-toggle="tab"><?= lang('category') ?></a></li>
        <li class="<?= $active == 2 ? 'active' : ''; ?>"><a href="#new" data-toggle="tab"><?= lang('new_category') ?></a></li>                                                                     
    </ul>
    <div class="tab-content no-padding">
        <!-- ************** general *************-->
        <div class="tab-pane <?= $active == 1 ? 'active' : ''; ?>" id="manage">
            <div class="table-responsive">
                <table class="table table-striped DataTables " id="DataTables">
                    <thead>
                        <tr>

                            <th ><?= lang('income_category') ?></th>                            
                            <th ><?= lang('description') ?></th>                            
                            <th ><?= lang('action') ?></th>                      
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        if (!empty($all_income_category)) {
                            foreach ($all_income_category as $income_category) {
                                ?>
                                <tr>
                                    <td><?= $income_category->income_category ?></td>
                                    <td><?= $income_category->description ?></td>
                                    <td>
                                        <?= btn_edit('admin/settings/income_category/edit_income_category/' . $income_category->income_category_id) ?>
                                        <?= btn_delete('admin/settings/delete_income_category/' . $income_category->income_category_id) ?>
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
            <form method="post" action="<?= base_url() ?>admin/settings/income_category/update_income_category/<?php
            if (!empty($income_category_info)) {
                echo $income_category_info->income_category_id;
            }
            ?>" class="form-horizontal"> 
                <div class="form-group">
                    <label class="col-lg-3 control-label"><?= lang('income_category') ?> <span class="text-danger">*</span></label>
                    <div class="col-lg-7">
                        <input type="text" name="income_category"  value="<?php
                        if (!empty($income_category_info)) {
                            echo $income_category_info->income_category;
                        }
                        ?>" class="form-control" placeholder="<?= lang('income_category') ?>" required>
                    </div>
                </div>
                <div class="form-group terms">
                    <label class="col-lg-3 control-label"><?= lang('description') ?> </label>
                    <div class="col-lg-7">
                        <textarea name="description" class="form-control"><?php
                            if (!empty($income_category_info)) {
                                echo $income_category_info->description;
                            }
                            ?></textarea>                        
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