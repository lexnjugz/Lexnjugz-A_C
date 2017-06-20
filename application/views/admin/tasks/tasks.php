
<?php echo message_box('success'); ?>
<?php echo message_box('error'); ?>

<style>

</style>
<div class="row">
    <div class="col-sm-12">
        <div class="nav-tabs-custom">
            <!-- Tabs within a box -->
            <ul class="nav nav-tabs">
                <li class="<?= $active == 1 ? 'active' : '' ?>"><a href="#task_list" data-toggle="tab"><?= lang('all_task') ?></a></li>
                <li class="<?= $active == 2 ? 'active' : '' ?>"><a href="#assign_task"  data-toggle="tab"><?= lang('assign_task') ?></a></li>
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
                                        <th><?= lang('task_name') ?></th>                                                                                                                    
                                        <th><?= lang('due_date') ?></th>                                        
                                        <th><?= lang('progress') ?></th>                                        
                                        <th class="col-sm-1"><?= lang('status') ?></th>                                        
                                        <th class="col-sm-2"><?= lang('changes/view') ?></th>                        
                                    </tr>
                                </thead>
                                <tbody>                                    
                                    <?php
                                    $all_task_info = $this->db->get('tbl_task')->result();
                                    if (!empty($all_task_info)):foreach ($all_task_info as $key => $v_task):
                                            ?>
                                            <tr>
                                                <td> <a class="text-info"style="<?php
                                                    if ($v_task->task_progress >= 100) {
                                                        echo 'text-decoration: line-through;';
                                                    }
                                                    ?>" href="<?= base_url() ?>admin/tasks/view_task_details/<?= $v_task->task_id ?>"><?php echo $v_task->task_name; ?></a></td>
                                                
                                                <td><?php
                                                    $due_date = $v_task->due_date;
                                                    $due_time = strtotime($due_date);
                                                    $current_time = time();
                                                    ?>
                                                    <?= strftime(config_item('date_format'), strtotime($due_date)) ?>
                                                    <?php if ($current_time > $due_time && $v_task->task_progress < 100) { ?>
                                                        <span class="label label-danger"><?= lang('overdue') ?></span>
                                                    <?php } ?></td>  
                                                <td>
                                                    <div class="inline ">
                                                        <div class="easypiechart text-success" style="margin: 0px;" data-percent="<?= $v_task->task_progress ?>" data-line-width="5" data-track-Color="#f0f0f0" data-bar-color="#<?php
                                                        if ($v_task->task_progress == 100) {
                                                            echo '8ec165';
                                                        } else {
                                                            echo 'fb6b5b';
                                                        }
                                                        ?>" data-rotate="270" data-scale-Color="false" data-size="50" data-animate="2000">
                                                            <span class="small text-muted"><?= $v_task->task_progress ?>%</span>
                                                        </div>
                                                    </div>

                                                </td>
                                                <td><?php
                                                    if ($v_task->task_status == '0') {
                                                        echo '<span class="label label-warning"> Pending </span>';
                                                    } elseif ($v_task->task_status == '1') {
                                                        echo '<span class="label label-info"> Started </span>';
                                                    } else {
                                                        echo '<span class="label label-success"> Completed </span>';
                                                    }
                                                    ?>
                                                </td>                                              
                                                <td >                                                    
                                                    <?php echo btn_delete('admin/tasks/delete_task/' . $v_task->task_id) ?>
                                                    <?php echo btn_edit('admin/tasks/all_task/' . $v_task->task_id) ?>
                                                    <?php
                                                    
                                                    if ($v_task->timer_status == 'on') { ?>
                                                        <a class="btn btn-xs btn-danger" href="<?= base_url() ?>admin/tasks/tasks_timer/off/<?= $v_task->task_id ?>"><?= lang('stop_timer') ?> </a> 

                                                    <?php } else { ?>
                                                        <a class="btn btn-xs btn-success" href="<?= base_url() ?>admin/tasks/tasks_timer/on/<?= $v_task->task_id ?>"><?= lang('start_timer') ?> </a> 
                                                    <?php } ?>
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
                            <form  id="form_validation" action="<?php echo base_url() ?>admin/tasks/save_task/<?php if (!empty($task_info->task_id)) echo $task_info->task_id; ?>" method="post" class="form-horizontal">

                                <?php
                                if (!empty($task_info->project_id)) {
                                    $project_id = $task_info->project_id;
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
                                            <input type="hidden" name="project_id" required="1" class="form-control" value="<?php echo $project_id ?>" />
                                        </div>
                                    </div>
                                    <div class="form-group" id="border-none">
                                        <label for="field-1" class="col-sm-3 control-label"><?= lang('milestones') ?> <span class="required">*</span></label>
                                        <div class="col-sm-5"> 
                                            <select name="milestones_id" style="width: 100%" class="select_box" required="1">                                                                                          
                                                <?php
                                                $all_milestones_info = $this->db->where('project_id', $project_id)->get('tbl_milestones')->result();
                                                if (!empty($all_milestones_info)) {
                                                    foreach ($all_milestones_info as $v_milestones) {
                                                        ?>
                                                        <option value="<?= $v_milestones->milestones_id ?>" <?php
                                                        if (!empty($task_info->milestones_id)) {
                                                            echo $v_milestones->milestones_id == $task_info->milestones_id ? 'selected' : '';
                                                        }
                                                        ?>><?= $v_milestones->milestone_name ?></option>
                                                                <?php
                                                            }
                                                        }
                                                        ?>	                                            
                                            </select>
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
                                                        if (!empty($task_info->opportunities_id)) {
                                                            echo $v_opportunities->opportunities_id == $task_info->opportunities_id ? 'selected' : '';
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
                                    <label class="col-sm-3 control-label"><?= lang('task_name') ?><span class="required">*</span></label>
                                    <div class="col-sm-5">
                                        <input type="text" name="task_name" required class="form-control" value="<?php if (!empty($task_info->task_name)) echo $task_info->task_name; ?>" />
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
                                                        if (!empty($task_info->assigned_to)) {
                                                            $assign_user = unserialize($task_info->assigned_to);
                                                            foreach ($assign_user['assigned_to'] as $assding_id) {
                                                                echo $v_user->user_id == $assding_id ? 'selected' : '';
                                                            }
                                                        }
                                                        ?>><?= ucfirst($v_user->username) ?></option>
                                                                <?php
                                                            }
                                                        }
                                                        ?>	
                                            </optgroup> 
                                        </select>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label class="control-label col-sm-3" ><?= lang('start_date') ?></label>
                                    <div class="input-group col-sm-5">
                                        <input type="text" name="task_start_date" required="" value="<?php
                                        if (!empty($task_info->task_start_date)) {
                                            echo $task_info->task_start_date;
                                        }
                                        ?>" class="form-control datepicker" data-format="yyyy-mm-dd">
                                        <div class="input-group-addon">
                                            <a href="#"><i class="entypo-calendar"></i></a>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="control-label col-sm-3" ><?= lang('due_date') ?></label>
                                    <div class="input-group col-sm-5">
                                        <input type="text" name="due_date" required="" value="<?php
                                        if (!empty($task_info->due_date)) {
                                            echo $task_info->due_date;
                                        }
                                        ?>" class="form-control datepicker" data-format="yyyy-mm-dd">
                                        <div class="input-group-addon">
                                            <a href="#"><i class="entypo-calendar"></i></a>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-3 control-label"><?= lang('estimated_hour') ?></label>
                                    <div class="col-sm-5">
                                        <input type="text" name="task_hour" required class="form-control" value="<?php if (!empty($task_info->task_hour)) echo $task_info->task_hour; ?>" />
                                        <p class="small"> <?= lang('input_value') ?>.</p>
                                    </div>

                                </div>

                                <div class="form-group">
                                    <label for="field-1" class="col-sm-3 control-label"><?= lang('task_description') ?> <span class="required">*</span></label>
                                    <div class="col-sm-8">
                                        <textarea class="form-control " name="task_description" id="ck_editor" required><?php if (!empty($task_info->task_description)) echo $task_info->task_description; ?></textarea>
                                        <?php echo display_ckeditor($editor['ckeditor']); ?>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label class="control-label col-sm-3" ><?= lang('progress') ?></label>
                                    <div class="input-group col-sm-5">
                                        <input type="text" value="<?php if (!empty($task_info->task_progress)) echo $task_info->task_progress; ?>" name="task_progress" class="slider form-control" data-slider-min="0" data-slider-max="100" data-slider-step="1" data-slider-value="<?php if (!empty($task_info->task_progress)) echo $task_info->task_progress; ?>" data-slider-orientation="horizontal" data-slider-id="red">                                        
                                    </div>
                                </div>

                                <div class="form-group" id="border-none">
                                    <label for="field-1" class="col-sm-3 control-label"><?= lang('task_status') ?> <span class="required">*</span></label>
                                    <div class="col-sm-5">
                                        <select name="task_status" class="form-control" required >                                            
                                            <option value="0" <?php if (!empty($task_info->task_status)) echo $task_info->task_status == 0 ? 'selected' : '' ?>> <?= lang('pending') ?> </option>
                                            <option value="1" <?php if (!empty($task_info->task_status)) echo $task_info->task_status == 1 ? 'selected' : '' ?>> <?= lang('open') ?></option>                                            
                                            <option value="2" <?php if (!empty($task_info->task_status)) echo $task_info->task_status == 2 ? 'selected' : '' ?>> <?= lang('completed') ?></option>                                                                                        
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

