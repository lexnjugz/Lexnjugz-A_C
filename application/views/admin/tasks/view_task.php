<?php echo message_box('success'); ?>
<?php echo message_box('error'); ?>
<style>
    .note-editor .note-editable{
        height: 150px;
    }
</style>
<div class="row">
    <div class="col-sm-3">
        <div class="box box-primary" data-collapsed="0">
            <div class="box-header with-border">
                <h3 class="box-title"><?= lang('task_initials') ?></h3>
            </div>
            <form id="form_validation" action="<?php echo base_url() ?>admin/tasks/update_status/<?php if (!empty($task_details->task_id)) echo $task_details->task_id; ?>" method="post">
                <div class="box-body">
                    <div class="form-group" id="border-none">                    
                        <div class="col-sm-12">
                            <div class="form-group">
                                <label class="control-label" ><?= lang('start_date') ?></label>                           
                                <input type="text" value="<?php
                                if (!empty($task_details->task_start_date)) {
                                    echo $task_details->task_start_date;
                                }
                                ?>" class="form-control" data-format="yyy-mm-dd" readonly >                                                   
                            </div>
                        </div>
                    </div>
                    <div class="form-group" id="border-none">                    
                        <div class="col-sm-12">
                            <div class="form-group">
                                <label class="control-label" ><?= lang('due_date') ?></label>                           
                                <input type="text" value="<?php
                                if (!empty($task_details->due_date)) {
                                    echo $task_details->due_date;
                                }
                                ?>" class="form-control" data-format="yyy-mm-dd" readonly >                                                    
                            </div>
                        </div>
                    </div>

                    <div class="form-group">                    
                        <div class="col-sm-12">
                            <div class="form-group">
                                <label class="control-label" ><?= lang('progress') ?></label>                               
                                <input type="text"  name="task_progress" 
                                       class="slider form-control" data-slider-min="0" data-slider-max="100" 
                                       data-slider-step="1" data-slider-value="<?php
                                       if (!empty($task_details->task_progress))
                                           echo $task_details->task_progress;
                                       ?>" data-slider-orientation="horizontal" 
                                       data-slider-id="red">                                                                                              
                            </div>
                        </div>
                    </div>
                    <div class="form-group" id="border-none">                    
                        <div class="col-sm-12">
                            <div class="form-group">
                                <label class="control-label"><?= lang('task_status') ?></label>

                                <select name="task_status" class="form-control" required >                                            
                                    <option value="0" <?php echo $task_details->task_status == 0 ? 'selected' : '' ?>> <?= lang('pending') ?> </option>
                                    <option value="1" <?php echo $task_details->task_status == 1 ? 'selected' : '' ?>> <?= lang('open') ?> </option>
                                    <option value="2" <?php echo $task_details->task_status == 2 ? 'selected' : '' ?>> <?= lang('completed') ?> </option>                                                                                        
                                </select>

                            </div>
                        </div>
                    </div>
                    <div class="form-group" id="border-none">                    
                        <div class="col-sm-12">
                            <div class="form-group">
                                <div class="">
                                    <div class="pull-right">
                                        <button type="submit" id="sbtn" class="btn btn-primary"><?= lang('updates') ?></button>                            
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>
                </div> 
            </form>
        </div>
    </div>
    <div class="col-sm-9">
        <div class="nav-tabs-custom">
            <!-- Tabs within a box -->
            <ul class="nav nav-tabs">
                <li class="<?= $active == 1 ? 'active' : '' ?>"><a href="#task_details" data-toggle="tab"><?= lang('details') ?></a></li>
                <li class="<?= $active == 2 ? 'active' : '' ?>"><a href="#task_comments"  data-toggle="tab"><?= lang('comments') ?></a></li>
                <li class="<?= $active == 3 ? 'active' : '' ?>"><a href="#task_attachments"  data-toggle="tab"><?= lang('attachment') ?></a></li>
                <li class="<?= $active == 4 ? 'active' : '' ?>"><a href="#task_notes"  data-toggle="tab"><?= lang('notes') ?></a></li>
                <li class="<?= $active == 5 ? 'active' : '' ?>"><a href="#timesheet"  data-toggle="tab"><?= lang('timesheet') ?></a></li>
            </ul>
            <div class="tab-content no-padding">
                <!-- Task Details tab Starts -->
                <div class="tab-pane <?= $active == 1 ? 'active' : '' ?>" id="task_details" style="position: relative;">
                    <div class="box" style="border: none; padding-top: 15px;" data-collapsed="0">                         
                        <div class="box-body">  
                            <?php
                            if ($task_details->task_progress < 49) {
                                $progress = 'progress-bar-danger';
                            } elseif ($task_details->task_progress > 50 && $task_details->task_progress < 99) {
                                $progress = 'progress-bar-primary';
                            } else {
                                $progress = 'progress-bar-success';
                            }
                            ?>
                            <div class="progress progress-striped ">                                 
                                <div class="progress-bar <?= $progress ?> " data-toggle="tooltip" data-original-title="<?= $task_details->task_progress ?>%" style="width: <?= $task_details->task_progress ?>%"></div>
                            </div>                        
                            <ul class="list-group no-radius">                                                                
                                <li class="list-group-item">
                                    <span class="pull-right"><?php if (!empty($task_details->task_name)) echo $task_details->task_name; ?> </span><?= lang('task_name') ?> 
                                </li>
                                <?php
                                if (!empty($task_details->project_id)):
                                    $project_info = $this->db->where('project_id', $task_details->project_id)->get('tbl_project')->row();
                                    $milestones_info = $this->db->where('milestones_id', $task_details->milestones_id)->get('tbl_milestones')->row();
                                    ?>
                                    <li class="list-group-item">
                                        <span class="pull-right">
                                            <?php if (!empty($project_info->project_name)) echo $project_info->project_name; ?></span>
                                        <?= lang('project_name') ?></li>
                                    <li class="list-group-item">
                                        <span class="pull-right">
                                            <?php if (!empty($milestones_info->milestone_name)) echo $milestones_info->milestone_name; ?></span>
                                        <?= lang('milestone_name') ?></li>
                                <?php endif ?>
                                <?php
                                if (!empty($task_details->opportunities_id)):
                                    $opportunity_info = $this->db->where('opportunities_id', $task_details->opportunities_id)->get('tbl_opportunities')->row();
                                    ?>
                                    <li class="list-group-item">
                                        <span class="pull-right"><?php if (!empty($opportunity_info->opportunity_name)) echo $opportunity_info->opportunity_name; ?> </span><?= lang('opportunity_name') ?> 
                                    </li>
                                <?php endif ?>

                                <div class="row">
                                    <div class="col-lg-6">
                                        <li class="list-group-item">
                                            <span class="pull-right"><strong><?= $this->tasks_model->get_time_spent_result($this->tasks_model->task_spent_time_by_id($task_details->task_id)) ?></strong></span><?= lang('time_spend') ?></li>


                                        <li class="list-group-item">
                                            <span class="pull-right"><?php if (!empty($task_details->task_hour)) echo $task_details->task_hour; ?> <?= lang('hours') ?></span><?= lang('estimated_hours') ?> 
                                        </li>
                                    </div>  
                                    <div class="col-lg-6">
                                        <?php
                                        $due_date = $task_details->due_date;
                                        $due_time = strtotime($due_date);
                                        $current_time = time();
                                        ?>                                        
                                        <?php if ($current_time > $due_time && $task_details->task_progress < 100) { ?>
                                            <li class="list-group-item">
                                                <span class="pull-right"><?= strftime(config_item('date_format'), strtotime($due_date)) ?></span><?= lang('due_date') ?></li>
                                        <?php } ?>
                                        <li class="list-group-item">
                                            <?php if ($task_details->timer_status == 'on') { ?>
                                                <div class="pull-right">
                                                    <span class="label label-success"><?= lang('on') ?></span>
                                                    <a class="btn btn-xs btn-danger " href="<?= base_url() ?>admin/tasks/tasks_timer/off/<?= $task_details->task_id ?>/details"><?= lang('stop_timer') ?> </a> 
                                                </div>
                                            <?php } else {
                                                ?>
                                                <div class="pull-right">
                                                    <span class="label label-danger"><?= lang('off') ?></span>
                                                    <a class="btn btn-xs btn-success" href="<?= base_url() ?>admin/tasks/tasks_timer/on/<?= $task_details->task_id ?>/details"><?= lang('start_timer') ?> </a> 
                                                </div>
                                            <?php }
                                            ?>  
                                            <?= lang('timer_status') ?>
                                        </li>
                                    </div>  
                                </div>

                                <blockquote style="font-size: 12px; margin-top: 5px"><?php if (!empty($task_details->task_description)) echo $task_details->task_description; ?></blockquote>




                                <div class="form-group col-sm-12" id="border-none">
                                    <label class="col-sm-3 control-label"><?= lang('assined_to') ?>
                                        <span id="update" class="pull-right label label-primary"><?php echo lang('update'); ?></span>
                                        <span id="hide" class="pull-right label label-primary "><?php echo lang('close'); ?></span>

                                    </label>
                                    <div class="col-sm-7 ">
                                        <?php $assigned = unserialize($task_details->assigned_to);
                                        ?>
                                        <div id="update_true" >
                                            <form id="form_validation" action="<?php echo base_url() ?>admin/tasks/update_member/<?php if (!empty($task_details->task_id)) echo $task_details->task_id; ?>" method="post">
                                                <div class="col-sm-11">
                                                    <div class="form-group" id="border-none">                                                                                         
                                                        <select multiple="multiple" name="assigned_to[]" style="width: 100%" class="select_multi" required="">                                              

                                                            <optgroup label="<?= lang('admin_staff') ?>"> 
                                                                <?php
                                                                if (!empty($assign_user)) {
                                                                    foreach ($assign_user as $key => $v_user) {
                                                                        ?>
                                                                        <option value="<?= $v_user->user_id ?>" <?php
                                                                        if (!empty($assigned['assigned_to'])) {
                                                                            foreach ($assigned['assigned_to'] as $assding_id) {
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
                                                <div class="col-sm-1">

                                                    <button type="submit" class="btn btn-primary pull-left"><?php echo lang('update'); ?></button>                                                
                                                </div>
                                            </form>                                                                
                                        </div>                                            
                                        <div id="update_false">                                                                           
                                            <table class="table table-bordered " style="background-color: #EEE;">
                                                <tbody>
                                                    <?php
                                                    if (!empty($assigned['assigned_to'])) :
                                                        foreach ($assigned['assigned_to'] as $v_assign) :
                                                            if (!empty($v_assign)) {
                                                                $user_info = $this->db->where(array('user_id' => $v_assign))->get('tbl_users')->row();
                                                                if ($user_info->role_id == 1) {
                                                                    $label = '<small style="font-size:10px;padding:2px;" class="label label-danger ">Admin</small>';
                                                                } else {
                                                                    $label = '<small style="font-size:10px;padding:2px;" class="label label-primary">Staff</small>';
                                                                }
                                                                $profile_info = $this->db->where(array('user_id' => $v_assign))->get('tbl_account_details')->row();
                                                                ?>
                                                                <tr>
                                                                    <td style="width: 75px; border: 0px;">                                                            
                                                                        <img style="width: 40px;height: 40px" src="<?= base_url() . $profile_info->avatar ?>" alt="" class="img-circle"/>
                                                                    </td>
                                                                    <td>
                                                                        <h4 class="pull-left"><?= ($profile_info->fullname) . ' ' . $label ?></h4>
                                                                    </td>                                                        
                                                                </tr>
                                                                <?php
                                                            }
                                                        endforeach;
                                                    endif;
                                                    ?>
                                                </tbody>
                                            </table>                                    
                                        </div>
                                    </div>
                                </div>
                            </ul>

                        </div>                         

                    </div>        
                </div>
                <!-- Task Details tab Ends -->


                <!-- Task Comments Panel Starts --->
                <div class="tab-pane <?= $active == 2 ? 'active' : '' ?>" id="task_comments" style="position: relative;">
                    <div class="box" style="border: none; padding-top: 15px;" data-collapsed="0">                        
                        <div class="box-body chat" id="chat-box">

                            <form id="form_validation" action="<?php echo base_url() ?>admin/tasks/save_comments" method="post" class="form-horizontal">
                                <input type="hidden" name="task_id" value="<?php
                                if (!empty($task_details->task_id)) {
                                    echo $task_details->task_id;
                                }
                                ?>" class="form-control"   >  
                                <div class="form-group"> 
                                    <div class="col-sm-12">
                                        <textarea class="form-control col-sm-12" placeholder="<?= $task_details->task_name . ' ' . lang('comments') ?>" name="comment" style="height: 70px;" required ></textarea>
                                    </div>
                                </div>                                
                                <div class="form-group">                    
                                    <div class="col-sm-12">
                                        <div class="pull-right">
                                            <button type="submit" id="sbtn" class="btn btn-primary"><?= lang('post_comment') ?></button>                            
                                        </div>
                                    </div>
                                </div>                                
                            </form> 
                            <hr />

                            <?php
                            $comment_details = $this->db->where('task_id', $task_details->task_id)->get('tbl_task_comment')->result();
                            if (!empty($comment_details)):foreach ($comment_details as $key => $v_comment):
                                    $user_info = $this->db->where(array('user_id' => $v_comment->user_id))->get('tbl_users')->row();
                                    $profile_info = $this->db->where(array('user_id' => $v_comment->user_id))->get('tbl_account_details')->row();
                                    if ($user_info->role_id == 1) {
                                        $label = '<small style="font-size:10px;padding:2px;" class="label label-danger ">Admin</small>';
                                    } else {
                                        $label = '<small style="font-size:10px;padding:2px;" class="label label-primary">Staff</small>';
                                    }
                                    ?>

                                    <div class="col-sm-12 item ">                                      

                                        <img src="<?php echo base_url() . $profile_info->avatar ?>" alt="user image" class="img-circle"/>


                                        <p class="message">
                                            <?php
                                            $today = time();
                                            $comment_time = strtotime($v_comment->comment_datetime);
                                            ?>                                             
                                            <small class="text-muted pull-right"><i class="fa fa-clock-o"></i> <?= $this->tasks_model->get_time_different($today, $comment_time) ?> <?= lang('ago') ?>
                                                <?php if ($v_comment->user_id == $this->session->userdata('user_id')) { ?>
                                                    <?= btn_delete('admin/tasks/delete_task_comments/' . $v_comment->task_id . '/' . $v_comment->task_comment_id) ?>                                                
                                                <?php } ?></small>                                            
                                            <a href="#" class="name">
                                                <?= ($profile_info->fullname) . ' ' . $label ?>
                                            </a>

                                            <?php if (!empty($v_comment->comment)) echo $v_comment->comment; ?>
                                        </p>

                                    </div><!-- /.item -->
                                <?php endforeach; ?>
                            <?php endif; ?>                            
                        </div>      
                    </div>   
                </div> 
                <!-- Task Comments Panel Ends--->

                <!-- Task Attachment Panel Starts --->
                <div class="tab-pane <?= $active == 3 ? 'active' : '' ?>" id="task_attachments" style="position: relative;">
                    <div class="box" style="border: none; padding-top: 15px;" data-collapsed="0">                        
                        <div class="panel-body">

                            <form action="<?= base_url() ?>admin/tasks/save_task_attachment/<?php
                            if (!empty($add_files_info)) {
                                echo $add_files_info->task_attachment_id;
                            }
                            ?>" enctype="multipart/form-data" method="post" id="form" class="form-horizontal">
                                <div class="form-group">
                                    <label class="col-lg-3 control-label"><?= lang('file_title') ?> <span class="text-danger">*</span></label>
                                    <div class="col-lg-6">
                                        <input name="title" class="form-control" value="<?php
                                        if (!empty($add_files_info)) {
                                            echo $add_files_info->title;
                                        }
                                        ?>" required placeholder="<?= lang('file_title') ?>"/>
                                    </div>
                                </div>                                
                                <div class="form-group">
                                    <label class="col-lg-3 control-label"><?= lang('description') ?></label>
                                    <div class="col-lg-6">
                                        <textarea name="description" class="form-control" placeholder="<?= lang('description') ?>" ><?php
                                            if (!empty($add_files_info)) {
                                                echo $add_files_info->description;
                                            }
                                            ?></textarea>
                                    </div>
                                </div>
                                <?php if (empty($add_files_info)) { ?>
                                    <div id="add_new" >
                                        <div class="form-group" style="margin-bottom: 0px">
                                            <label for="field-1" class="col-sm-3 control-label"><?= lang('upload_file') ?></label>                        
                                            <div class="col-sm-6">
                                                <div class="fileinput fileinput-new"  data-provides="fileinput">
                                                    <?php if (!empty($project_files)):foreach ($project_files as $v_files_image): ?>
                                                            <span class=" btn btn-default btn-file"><span class="fileinput-new" style="display: none" >Select file</span>
                                                                <span class="fileinput-exists" style="display: block"><?= lang('change') ?></span>
                                                                <input type="hidden" name="task_files[]" value="<?php echo $v_files_image->files ?>">                                                                                                    
                                                                <input type="file" name="task_files[]" >
                                                            </span>                                    
                                                            <span class="fileinput-filename"> <?php echo $v_files_image->file_name ?></span>                                          
                                                        <?php endforeach; ?>
                                                    <?php else: ?>
                                                        <span class="btn btn-default btn-file"><span class="fileinput-new" ><?= lang('select_file') ?></span>
                                                            <span class="fileinput-exists" ><?= lang('change') ?></span>                                            
                                                            <input type="file" name="task_files[]" >
                                                        </span> 
                                                        <span class="fileinput-filename"></span>                                        
                                                        <a href="#" class="close fileinput-exists" data-dismiss="fileinput" style="float: none;">&times;</a>                                                                                                            
                                                    <?php endif; ?>
                                                </div>  
                                                <div id="msg_pdf" style="color: #e11221"></div>                        
                                            </div>
                                            <div class="col-sm-2">                            
                                                <strong><a href="javascript:void(0);" id="add_more" class="addCF "><i class="fa fa-plus"></i>&nbsp;<?= lang('add_more') ?></a></strong>
                                            </div>
                                        </div>                    
                                    </div>  
                                <?php } ?>
                                <br/>
                                <input type="hidden" name="task_id" value="<?php
                                if (!empty($task_details->task_id)) {
                                    echo $task_details->task_id;
                                }
                                ?>" class="form-control"   >  
                                <div class="form-group">
                                    <div class="col-sm-3">
                                    </div>
                                    <div class="col-sm-3">
                                        <button type="submit" class="btn btn-primary"><?= lang('upload_file') ?></button>                
                                    </div>
                                </div>
                            </form>

                        </div>      
                    </div>   
                    <div class="box box-success">                        
                        <h5><strong><?= lang('attach_file_list') ?></strong></h5>                        
                        <div class="box-body">
                            <?php
                            $this->load->helper('file');

                            if (!empty($project_files_info)) {
                                foreach ($project_files_info as $key => $v_files_info) {
                                    ?>
                                    <div class="panel-group" id="accordion" style="margin:8px 5px" role="tablist" aria-multiselectable="true">
                                        <div class="box box-info" style="border-radius: 0px ">
                                            <div class="panel-heading"  role="tab" id="headingOne">
                                                <h4 class="panel-title">
                                                    <a data-toggle="collapse" data-parent="#accordion" href="#<?php echo $key ?>" aria-expanded="true" aria-controls="collapseOne">
                                                        <strong><?php echo $files_info[$key]->title; ?> </strong>
                                                        <small class="pull-right">
                                                            <?php if ($files_info[$key]->user_id == $this->session->userdata('user_id')) { ?>
                                                                <?= btn_delete('admin/tasks/delete_task_files/' . $files_info[$key]->task_id . '/' . $files_info[$key]->task_attachment_id) ?>                                                
                                                            <?php } ?></small>                                            
                                                    </a>                                                   
                                                </h4>
                                            </div>
                                            <div id="<?php echo $key ?>" class="panel-collapse collapse <?php
                                            if (!empty($in) && $files_info[$key]->files_id == $in) {
                                                echo 'in';
                                            }
                                            ?>" role="tabpanel" aria-labelledby="headingOne">
                                                <div class="content">
                                                    <div class="table-responsive">
                                                        <table id="table-files" class="table table-striped ">
                                                            <thead>
                                                                <tr>
                                                                    <th width="45%"><?= lang('files') ?></th>
                                                                    <th class=""><?= lang('size') ?></th>
                                                                    <th ><?= lang('date') ?></th>
                                                                    <th ><?= lang('uploaded_by') ?></th>
                                                                    <th><?= lang('action') ?></th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                <?php
                                                                $this->load->helper('file');

                                                                if (!empty($v_files_info)) {
                                                                    foreach ($v_files_info as $v_files) {
                                                                        $user_info = $this->db->where(array('user_id' => $files_info[$key]->user_id))->get('tbl_users')->row();
                                                                        ?>
                                                                        <tr class="file-item">
                                                                            <td>
                                                                                <?php if ($v_files->is_image == 1) : ?>                                                           
                                                                                    <div class="file-icon"><a href="<?= base_url() . $v_files->files ?>" >
                                                                                            <img style="width: 50px;border-radius: 5px;" src="<?= base_url() . $v_files->files ?>" /></a></div>
                                                                                <?php else : ?>
                                                                                    <div class="file-icon"><i class="fa fa-file-o"></i>
                                                                                        <a href="<?= base_url() . $v_files->files ?>" ><?= $v_files->file_name ?></a>
                                                                                    </div>
                                                                                <?php endif; ?>

                                                                                <a data-toggle="tooltip" data-placement="top" data-original-title="<?= $files_info[$key]->description ?>" class="text-info" href="<?= base_url() ?>admin/tasks/download_files/<?= $files_info[$key]->task_id ?>/<?= $v_files->uploaded_files_id ?>">
                                                                                    <?= $files_info[$key]->title ?>
                                                                                    <?php if ($v_files->is_image == 1) : ?>
                                                                                        <em><?= $v_files->image_width . "x" . $v_files->image_height ?></em>
                                                                                    <?php endif; ?>
                                                                                </a>
                                                                                <p class="file-text"><?= $files_info[$key]->description ?></p>
                                                                            </td>
                                                                            <td class=""><?= $v_files->size ?> Kb</td>
                                                                            <td class="col-date"><?= date('Y-m-d' . "<br/> h:m A", strtotime($files_info[$key]->upload_time)); ?></td>
                                                                            <td>
                                                                                <?= $user_info->username ?>
                                                                            </td>
                                                                            <td >                                                                               
                                                                                <a class="btn btn-xs btn-dark" data-toggle="tooltip" data-placement="top" title="Download" href="<?= base_url() ?>admin/tasks/download_files/<?= $files_info[$key]->task_id ?>/<?= $v_files->uploaded_files_id ?>"><i class="fa fa-download"></i></a>
                                                                            </td>

                                                                        </tr>
                                                                        <?php
                                                                    }
                                                                } else {
                                                                    ?>
                                                                    <tr><td colspan="5">
                                                                            <?= lang('nothing_to_display') ?>
                                                                        </td></tr>
                                                                <?php } ?>
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>
                                            </div>  
                                        </div>
                                    </div>
                                    <?php
                                }
                            }
                            ?>
                        </div>
                    </div>
                </div>  
                <!-- Task Attachment Panel Ends --->     
                <div class="tab-pane <?= $active == 4 ? 'active' : '' ?>" id="task_notes" style="position: relative;">
                    <div class="box" style="border: none; " data-collapsed="0">                        
                        <div class="panel-body">

                            <form action="<?= base_url() ?>admin/tasks/save_tasks_notes/<?php
                            if (!empty($task_details)) {
                                echo $task_details->task_id;
                            }
                            ?>" enctype="multipart/form-data" method="post" id="form" class="form-horizontal">
                                <div class="form-group">
                                    <label class="col-lg-1 control-label"><?= lang('notes') ?></label>
                                    <div class="col-lg-11">
                                        <textarea class="form-control textarea"  name="tasks_notes"><?= $task_details->tasks_notes ?></textarea>
                                    </div>                                    
                                </div>
                                <div class="form-group">
                                    <label class="col-lg-1 control-label"></label>
                                    <div class="col-sm-2"> 
                                        <button type="submit" id="sbtn" class="btn btn-primary"><?= lang('updates') ?></button>                                                                        
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                <div class="tab-pane <?= $active == 5 ? 'active' : '' ?>" id="timesheet" style="position: relative;">
                    <style>
                        .tooltip-inner {
                            white-space:pre-wrap;
                        }
                    </style>
                    <div class="nav-tabs-custom">
                        <!-- Tabs within a box -->
                        <ul class="nav nav-tabs">
                            <li class="<?= $time_active == 1 ? 'active' : ''; ?>"><a href="#general" data-toggle="tab"><?= lang('timesheet') ?></a></li>
                            <li class="<?= $time_active == 2 ? 'active' : ''; ?>"><a href="#contact" data-toggle="tab"><?= lang('manual_entry') ?></a></li>                                                                     
                        </ul>
                        <div class="tab-content no-padding">
                            <!-- ************** general *************-->
                            <div class="tab-pane <?= $time_active == 1 ? 'active' : ''; ?>" id="general">
                                <div class="table-responsive">
                                    <table id="table-tasks-timelog" class="table table-striped     DataTables">
                                        <thead>
                                            <tr>                                                
                                                <th><?= lang('user') ?></th>                                                
                                                <th><?= lang('start_time') ?></th>
                                                <th><?= lang('stop_time') ?></th>

                                                <th><?= lang('task_name') ?></th>
                                                <th class="col-time"><?= lang('time_spend') ?></th>                                                
                                                <th ><?= lang('action') ?></th>                                                
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            $total_timer = $this->db->where(array('task_id' => $task_details->task_id))->get('tbl_tasks_timer')->result();

                                            if (!empty($total_timer)) {
                                                foreach ($total_timer as $v_tasks) {
                                                    $task_info = $this->db->where(array('task_id' => $v_tasks->task_id))->get('tbl_task')->row();
                                                    if (!empty($task_info)) {
                                                        ?>
                                                        <tr>                                                            
                                                            <td class="small">

                                                                <a class="pull-left recect_task  ">
                                                                    <?php
                                                                    $profile_info = $this->db->where(array('user_id' => $v_tasks->user_id))->get('tbl_account_details')->row();

                                                                    $user_info = $this->db->where(array('user_id' => $v_tasks->user_id))->get('tbl_users')->row();
                                                                    if (!empty($profile_info)) {
                                                                        ?>
                                                                        <img style="width: 30px;margin-left: 18px;
                                                                             height: 29px;
                                                                             border: 1px solid #aaa;" src="<?= base_url() . $profile_info->avatar ?>" class="img-circle">
                                                                         <?php } ?>
                                                                         <?= ucfirst($user_info->username) ?>
                                                                </a>


                                                            </td>                                                            

                                                            <td><span class="label label-success"><?= strftime(config_item('date_format') . ' %H:%M', $v_tasks->start_time) ?></span></td>
                                                            <td><span class="label label-danger"><?= strftime(config_item('date_format') . ' %H:%M', $v_tasks->end_time) ?></span></td>

                                                            <td><a href="<?= base_url() ?>admin/tasks/view_task_details/<?= $v_tasks->task_id ?>" class="text-info small"><?= $task_info->task_name ?>
                                                                    <?php
                                                                    if (!empty($v_tasks->reason)) {
                                                                        $edit_user_info = $this->db->where(array('user_id' => $v_tasks->edited_by))->get('tbl_users')->row();
                                                                        echo '<i class="text-danger" data-html="true" data-toggle="tooltip" data-placement="top" title="Reason : ' . $v_tasks->reason . '<br>' . ' Edited By : ' . $edit_user_info->username . '">Edited</i>';
                                                                    }
                                                                    ?>
                                                                </a></td>
                                                            <td><small class="small text-muted"><?= $this->tasks_model->get_time_spent_result($v_tasks->end_time - $v_tasks->start_time) ?></small></td>                                                            
                                                            <td>                                    
                                                                <?= btn_edit('admin/tasks/view_task_details/' . $v_tasks->tasks_timer_id . '/5/edit') ?>
                                                                <?= btn_delete('admin/tasks/update_tasks_timer/' . $v_tasks->tasks_timer_id . '/delete_task_timmer') ?> 
                                                            </td>                                                            

                                                        </tr>
                                                        <?php
                                                    }
                                                }
                                            }
                                            ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <div class="tab-pane <?= $time_active == 2 ? 'active' : ''; ?>" id="contact">
                                <form role="form" enctype="multipart/form-data" id="form" action="<?php echo base_url(); ?>admin/tasks/update_tasks_timer/<?php
                                if (!empty($tasks_timer_info)) {
                                    echo $tasks_timer_info->tasks_timer_id;
                                }
                                ?>" method="post" class="form-horizontal">
                                      <?php
                                      if (!empty($tasks_timer_info)) {
                                          $start_date = date('Y-m-d', $tasks_timer_info->start_time);
                                          $start_time = date('H:i', $tasks_timer_info->start_time);
                                          $end_date = date('Y-m-d', $tasks_timer_info->end_time);
                                          $end_time = date('H:i', $tasks_timer_info->end_time);
                                      } else {
                                          $start_date = '';
                                          $start_time = '';
                                          $end_date = '';
                                          $end_time = '';
                                      }
                                      ?>                                       
                                      <?php if (empty($tasks_timer_info->tasks_timer_id)) { ?>
                                        <div class="form-group margin">                
                                            <div class="col-sm-8 center-block">                
                                                <label class="control-label">Select Task <span class="required">*</span></label>
                                                <select class="form-control select_box" name="task_id" required="" style="width: 100%">
                                                    <?php
                                                    $all_tasks_info = $this->db->get('tbl_task')->result();
                                                    if (!empty($all_tasks_info)):foreach ($all_tasks_info as $v_task_info):
                                                            ?>
                                                            <option value="<?= $v_task_info->task_id ?>"><?= $v_task_info->task_name ?></option>
                                                        <?php endforeach; ?>
                                                    <?php endif; ?>
                                                </select>
                                            </div>
                                        </div>
                                    <?php }else { ?>
                                        <input type="hidden" name="task_id" value="<?= $tasks_timer_info->task_id ?>">
                                    <?php } ?>
                                    <div class="form-group margin">
                                        <div class="col-sm-4">
                                            <label class="control-label">Start Date </label>                                    
                                            <div class="input-group">
                                                <input type="text" name="start_date" class="form-control datepicker" value="<?= $start_date ?>" data-date-format="<?= config_item('date_picker_format'); ?>">
                                                <div class="input-group-addon">
                                                    <a href="#"><i class="entypo-calendar"></i></a>
                                                </div>                                          
                                            </div>                                    
                                        </div>  
                                        <div class="col-sm-4">
                                            <label class="control-label">Start Time</label>                                                                    
                                            <div class="input-group">                        
                                                <input type="text" name="start_time" class="form-control timepicker2" value="<?= $start_time ?>" >
                                                <div class="input-group-addon">
                                                    <a href="#"><i class="entypo-clock"></i></a>
                                                </div>                                         
                                            </div>                                    
                                        </div>
                                    </div>            
                                    <div class="form-group margin">                                                                                 
                                        <div class="col-sm-4">
                                            <label class="control-label">End Date</label>                                    
                                            <div class="input-group">
                                                <input type="text" name="end_date" class="form-control datepicker" value="<?= $end_date ?>" data-date-format="<?= config_item('date_picker_format'); ?>">
                                                <div class="input-group-addon">
                                                    <a href="#"><i class="entypo-calendar"></i></a>
                                                </div>                                          
                                            </div>                                    
                                        </div>  
                                        <div class="col-sm-4">
                                            <label class="control-label">End Time</label>                                                                    
                                            <div class="input-group">
                                                <input type="text" name="end_time" class="form-control timepicker2" value="<?= $end_time ?>" >
                                                <div class="input-group-addon">
                                                    <a href="#"><i class="entypo-clock"></i></a>
                                                </div>                                         
                                            </div>                                    
                                        </div>
                                    </div>                       
                                    <div class="form-group margin">                
                                        <div class="col-sm-8 center-block">
                                            <label class="control-label">Reason for Edit <span class="required">*</span></label>
                                            <div >
                                                <textarea class="form-control" name="reason" required="" rows="6"><?php
                                                    if (!empty($tasks_timer_info)) {
                                                        echo $tasks_timer_info->reason;
                                                    }
                                                    ?></textarea>
                                            </div>
                                        </div>
                                    </div>            
                                    <div class="form-group" style="margin-top: 20px;">                
                                        <div class="col-lg-6">
                                            <button type="submit" class="btn btn-sm btn-primary"><?= lang('updates') ?></button>
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
</div>

<script type="text/javascript">
    $(document).ready(function () {
        $('#hide').hide();
        $('#update_true').hide();
        $(function () {
            $('#update').click(function () {
                $('#update_true').slideDown();
                $('#update').hide();
                $('#hide').show();
                $('#update_false').slideUp();
            });
            $('#hide').click(function () {
                $('#update_true').slideUp();
                $('#update_false').slideDown();
                $('#update').show();
                $('#hide').hide();
            });
        });
        var maxAppend = 0;
        $("#add_more").click(function () {
            if (maxAppend >= 4)
            {
                alert("Maximum 5 File is allowed");
            } else {
                var add_new = $('<div class="form-group" style="margin-bottom: 0px">\n\
                    <label for="field-1" class="col-sm-3 control-label"><?= lang('upload_file') ?></label>\n\
        <div class="col-sm-5">\n\
        <div class="fileinput fileinput-new" data-provides="fileinput">\n\
<span class="btn btn-default btn-file"><span class="fileinput-new" >Select file</span><span class="fileinput-exists" >Change</span><input type="file" name="task_files[]" ></span> <span class="fileinput-filename"></span><a href="#" class="close fileinput-exists" data-dismiss="fileinput" style="float: none;">&times;</a></div></div>\n\<div class="col-sm-2">\n\<strong>\n\
<a href="javascript:void(0);" class="remCF"><i class="fa fa-times"></i>&nbsp;Remove</a></strong></div>');
                maxAppend++;
                $("#add_new").append(add_new);
            }
        });

        $("#add_new").on('click', '.remCF', function () {
            $(this).parent().parent().parent().remove();
        });
    });
</script>    