
<?php echo message_box('success'); ?>
<?php echo message_box('error'); ?>

<style>

</style>
<div class="row">
    <div class="col-sm-12">
        <div class="nav-tabs-custom">
            <!-- Tabs within a box -->
            <ul class="nav nav-tabs">
                <li class="<?= $active == 1 ? 'active' : '' ?>"><a href="#task_list" data-toggle="tab"><?= lang('all_bugs') ?></a></li>
                <li class="<?= $active == 2 ? 'active' : '' ?>"><a href="#assign_task"  data-toggle="tab"><?= lang('new_bugs') ?></a></li>
            </ul>
            <div class="tab-content no-padding">
                <!-- Stock Category List tab Starts -->
                <div class="tab-pane <?= $active == 1 ? 'active' : '' ?>" id="task_list" style="position: relative;">
                    <div class="box" style="border: none; padding-top: 15px;" data-collapsed="0">                        
                        <div class="box-body">
                            <!-- Table -->
                            <table class="table table-bordered table-hover" id="DataTables">
                                <thead>
                                    <tr>                                        
                                        <th><?= lang('bug_title') ?></th>                                                                                                                    
                                        <th ><?= lang('status') ?></th>                                        
                                        <th ><?= lang('priority') ?></th>                                        
                                        <th><?= lang('reporter') ?></th>                                                                                
                                        <th ><?= lang('action') ?></th>                        
                                    </tr>
                                </thead>
                                <tbody>                                    
                                    <?php
                                    $all_bugs_info = $this->db->get('tbl_bug')->result();
                                    if (!empty($all_bugs_info)):foreach ($all_bugs_info as $key => $v_bugs):
                                            $reporter = $this->db->where('user_id', $v_bugs->reporter)->get('tbl_users')->row();
                                            if ($reporter->role_id == '1') {
                                                $role_label = ' ( admin)';
                                            } elseif ($reporter->role_id == '2') {
                                                $role_label = '( client)';
                                            } else {
                                                $role_label = '( Staff)';
                                            }
                                            ?>
                                            <tr>
                                                <td> <a class="text-info"style="<?php
                                                    if ($v_bugs->bug_status == 'resolve') {
                                                        echo 'text-decoration: line-through;';
                                                    }
                                                    ?>" href="<?= base_url() ?>admin/bugs/view_bug_details/<?= $v_bugs->bug_id ?>"><?php echo $v_bugs->bug_title; ?></a></td>                                                                                        
                                                </td>
                                                <td><?php
                                                    if ($v_bugs->bug_status == 'unconfirmed') {
                                                        $label = 'warning';
                                                    } elseif ($v_bugs->bug_status == 'confirmed') {
                                                        $label = 'info';
                                                    } elseif ($v_bugs->bug_status == 'in_progress') {
                                                        $label = 'primary';
                                                    } else {
                                                        $label = 'success';
                                                    }
                                                    ?>
                                                    <span class="label label-<?= $label ?>"><?= lang("$v_bugs->bug_status") ?></span>
                                                </td>   
                                                <td><?= ucfirst($v_bugs->priority) ?></td>
                                                <td>
                                                    <?= '<span class="badge">' . $reporter->username . '</span>' ?>
                                                </td>
                                                <td >                                                    
                                                    <?php echo btn_edit('admin/bugs/index/' . $v_bugs->bug_id) ?>                                                   
                                                    <?php echo btn_delete('admin/bugs/delete_bug/' . $v_bugs->bug_id) ?>
                                                    <div class="btn-group">
                                                        <button class="btn btn-xs btn-success dropdown-toggle" data-toggle="dropdown">
                                                            <?= lang('change_status') ?>
                                                            <span class="caret"></span></button>
                                                        <ul class="dropdown-menu">

                                                            <li><a  href="<?= base_url() ?>admin/bugs/change_status/<?= $v_bugs->bug_id ?>/unconfirmed"><?= lang('unconfirmed') ?></a></li>
                                                            <li><a  href="<?= base_url() ?>admin/bugs/change_status/<?= $v_bugs->bug_id ?>/confirmed"><?= lang('confirmed') ?></a></li>
                                                            <li><a  href="<?= base_url() ?>admin/bugs/change_status/<?= $v_bugs->bug_id ?>/in_progress"><?= lang('in_progress') ?></a></li>
                                                            <li><a  href="<?= base_url() ?>admin/bugs/change_status/<?= $v_bugs->bug_id ?>/resolved"><?= lang('resolved') ?></a></li>
                                                            <li><a  href="<?= base_url() ?>admin/bugs/change_status/<?= $v_bugs->bug_id ?>/verified"><?= lang('verified') ?></a></li>

                                                        </ul>                                      
                                                    </div>
                                                </td>                                
                                            </tr>                
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div> 
                    </div>        
                </div>

                <!-- Add Stock Category tab Starts -->
                <div class="tab-pane <?= $active == 2 ? 'active' : '' ?>" id="assign_task" style="position: relative;">
                    <div class="box" style="border: none; padding-top: 15px;" data-collapsed="0">                        
                        <div class="panel-body">
                            <form  id="form_validation" action="<?php echo base_url() ?>admin/bugs/save_bug/<?php if (!empty($bug_info->bug_id)) echo $bug_info->bug_id; ?>" method="post" class="form-horizontal">

                                <?php
                                if (!empty($bug_info->project_id)) {
                                    $project_id = $bug_info->project_id;
                                } elseif (!empty($project_id)) {
                                    $project_id = $project_id;
                                }
                                if (!empty($project_id)):
                                    $project_info = $this->db->where('project_id', $project_id)->get('tbl_project')->row();
                                    ?>
                                    <div class="form-group" id="border-none">
                                        <label for="field-1" class="col-sm-3 control-label"><?= lang('project') ?> <span class="required">*</span></label>
                                        <div class="col-sm-5"> 
                                            <span class="form-control"><?= $project_info->project_name ?></span>
                                            <input type="hidden" name="project_id" class="form-control" value="<?php echo $project_id ?>" />
                                        </div>
                                    </div>                                    
                                <?php endif ?>
                                <?php if (!empty($opportunities_id)): ?>
                                    <div class="form-group" id="border-none">
                                        <label for="field-1" class="col-sm-3 control-label"><?= lang('opportunities') ?> <span class="required">*</span></label>
                                        <div class="col-sm-5"> 
                                            <select name="opportunities_id" style="width: 100%" class="select_box" required="1">                                                                                          
                                                <?php
                                                if (!empty($all_opportunities_info)) {
                                                    foreach ($all_opportunities_info as $v_opportunities) {
                                                        ?>
                                                        <option value="<?= $v_opportunities->opportunities_id ?>" <?php
                                                        if (!empty($bug_info->opportunities_id)) {
                                                            echo $v_opportunities->opportunities_id == $bug_info->opportunities_id ? 'selected' : '';
                                                        } else {
                                                            echo $v_opportunities->opportunities_id == $opportunities_id ? 'selected' : '';
                                                        }
                                                        ?>><?= $v_opportunities->opportunity_name ?></option>
                                                                <?php
                                                            }
                                                        }
                                                        ?>	                                            
                                            </select>
                                        </div>
                                    </div>
                                <?php endif ?>
                                <div class="form-group">
                                    <label class="col-sm-3 control-label"><?= lang('bug_title') ?><span class="required">*</span></label>
                                    <div class="col-sm-5">
                                        <input type="text" name="bug_title" required class="form-control" value="<?php if (!empty($bug_info->bug_title)) echo $bug_info->bug_title; ?>" />
                                    </div>
                                </div>
                                <div class="form-group" id="border-none">
                                    <label for="field-1" class="col-sm-3 control-label"><?= lang('reporter') ?> <span class="required">*</span></label>
                                    <div class="col-sm-5"> 
                                        <select  name="reporter" style="width: 100%" class="select_box" required="">                                                                                          
                                            <?php
                                            $reporter_info = $this->db->get('tbl_users')->result();
                                            if (!empty($reporter_info)) {
                                                foreach ($reporter_info as $key => $v_reporter) {
                                                    if ($v_reporter->role_id == 2) {
                                                        $role = '(client)';
                                                    } elseif ($v_reporter->role_id == 3) {
                                                        $role = '(staff)';
                                                    } else {
                                                        $role = '(admin)';
                                                    }
                                                    ?>
                                                    <option value="<?= $v_reporter->user_id ?>" <?php
                                                    if (!empty($bug_info->reporter)) {
                                                        echo $v_reporter->user_id == $bug_info->reporter ? 'selected' : '';
                                                    }
                                                    ?>><?= $v_reporter->username . $role ?></option>
                                                            <?php
                                                        }
                                                    }
                                                    ?>	                                            
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-lg-3 control-label"><?= lang('priority') ?> <span class="text-danger">*</span> </label>
                                    <div class="col-lg-5">
                                        <div class=" "> 
                                            <select name="priority" class="form-control">
                                                <?php
                                                $priorities = $this->db->get('tbl_priorities')->result();
                                                if (!empty($priorities)) {
                                                    foreach ($priorities as $v_priorities):
                                                        ?>
                                                        <option value="<?= $v_priorities->priority ?>" <?php
                                                        if (!empty($bug_info) && $bug_info->priority == $bug_info->priority) {
                                                            echo 'selected';
                                                        }
                                                        ?>><?= lang(strtolower($v_priorities->priority)) ?></option>
                                                                <?php
                                                            endforeach;
                                                        }
                                                        ?>
                                            </select> 
                                        </div> 
                                    </div>
                                </div>                                
                                <div class="form-group">
                                    <label for="field-1" class="col-sm-3 control-label"><?= lang('description') ?> <span class="required">*</span></label>
                                    <div class="col-sm-8">
                                        <textarea class="form-control " name="bug_description" id="ck_editor" required><?php if (!empty($bug_info->bug_description)) echo $bug_info->bug_description; ?></textarea>
                                        <?php echo display_ckeditor($editor['ckeditor']); ?>
                                    </div>
                                </div>

                                <div class="form-group" id="border-none">
                                    <label for="field-1" class="col-sm-3 control-label"><?= lang('bug_status') ?> <span class="required">*</span></label>
                                    <div class="col-sm-5">

                                        <select name="bug_status" class="form-control" required >                                            
                                            <option value="unconfirmed" <?php if (!empty($bug_info->task_status)) echo $bug_info->task_status == 'unconfirmed' ? 'selected' : '' ?>> <?= lang('unconfirmed') ?> </option>                                            
                                            <option value="confirmed" <?php if (!empty($bug_info->task_status)) echo $bug_info->task_status == 'confirmed' ? 'selected' : '' ?>> <?= lang('confirmed') ?> </option>                                            
                                            <option value="in_progress" <?php if (!empty($bug_info->task_status)) echo $bug_info->task_status == 'in_progress' ? 'selected' : '' ?>> <?= lang('in_progress') ?> </option>                                            
                                            <option value="resolved" <?php if (!empty($bug_info->task_status)) echo $bug_info->task_status == 'resolved' ? 'selected' : '' ?>> <?= lang('resolved') ?> </option>                                            
                                            <option value="verified" <?php if (!empty($bug_info->task_status)) echo $bug_info->task_status == 'verified' ? 'selected' : '' ?>> <?= lang('verified') ?> </option>                                            
                                        </select>                                        
                                    </div>
                                </div>
                                <div class="form-group" id="border-none">
                                    <label for="field-1" class="col-sm-3 control-label"><?= lang('assined_to') ?> <span class="required">*</span></label>
                                    <div class="col-sm-5"> 
                                        <select multiple="multiple" name="assigned_to[]" style="width: 100%" class="select_multi" required="">                                              

                                            <optgroup label="<?= lang('admin_staff') ?>"> 
                                                <?php
                                                if (!empty($assign_user)) {
                                                    foreach ($assign_user as $key => $v_user) {
                                                        ?>
                                                        <option value="<?= $v_user->user_id ?>" <?php
                                                        if (!empty($bug_info->assigned_to)) {
                                                            $assign_user = unserialize($bug_info->assigned_to);
                                                            foreach ($assign_user['assigned_to'] as $assding_id) {
                                                                echo $v_user->user_id == $assding_id ? 'selected' : '';
                                                            }
                                                        }
                                                        ?>><?= $v_user->username ?></option>
                                                                <?php
                                                            }
                                                        }
                                                        ?>	
                                            </optgroup> 
                                        </select>
                                    </div>
                                </div>


                                <div class="">
                                    <div class="col-sm-offset-3 col-sm-5">
                                        <button type="submit" id="sbtn" class="btn btn-primary"><?= lang('save') ?></button>                            
                                    </div>
                                </div>
                            </form>
                        </div>      
                    </div>   
                </div>                
            </div>
        </div>
    </div>
</div>

