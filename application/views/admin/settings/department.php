<?= message_box('success'); ?>
<div class="nav-tabs-custom col-sm-12">
    <!-- Tabs within a box -->
    <ul class="nav nav-tabs">
        <li class="<?= $active == 1 ? 'active' : ''; ?>"><a href="#manage" data-toggle="tab"><?= lang('department') ?></a></li>
        <li class="<?= $active == 2 ? 'active' : ''; ?>"><a href="#new" data-toggle="tab"><?= lang('new_department') ?></a></li>                                                                     
    </ul>
    <div class="tab-content no-padding">
        <!-- ************** general *************-->
        <div class="tab-pane <?= $active == 1 ? 'active' : ''; ?>" id="manage">
            <div class="table-responsive">
                <table class="table table-striped DataTables " id="DataTables">
                    <thead>
                        <tr>

                            <th ><?= lang('department_name') ?></th>                            
                            <th ><?= lang('action') ?></th>                      
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        if (!empty($all_dept_info)) {
                            foreach ($all_dept_info as $v_dept_info) {
                                ?>
                                <tr>
                                    <td><?= $v_dept_info->deptname ?></td>
                                    <td>
                                        <?= btn_edit('admin/settings/department/edit_dept/' . $v_dept_info->departments_id) ?>
                                        <?= btn_delete('admin/settings/delete_dept/' . $v_dept_info->departments_id) ?>
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
            <form method="post" action="<?= base_url() ?>admin/settings/department/update_dept/<?php
            if (!empty($dept_info)) {
                echo $dept_info->departments_id;
            }
            ?>" class="form-horizontal"> 
                <div class="form-group">
                    <label class="col-lg-3 control-label"><?= lang('department_name') ?> <span class="text-danger">*</span></label>
                    <div class="col-lg-5">
                        <input type="text" name="deptname"  value="<?php
                        if (!empty($dept_info)) {
                            echo $dept_info->deptname;
                        }
                        ?>" class="form-control" placeholder="<?= lang('department_name') ?>" required>
                    </div>                                
                    <div class="col-lg-2">
                        <button type="submit" class="btn btn-sm btn-primary"><?= lang('save') ?></button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>