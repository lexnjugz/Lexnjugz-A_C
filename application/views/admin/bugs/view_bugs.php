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
                <h3 class="box-title"><?= lang('all_bugs') ?></h3>
            </div>           
            <div class="box-body">
                <div class="scrollable">
                    <div class="slim-scroll" data-height="auto" data-disable-fade-out="true" data-distance="0" data-size="5px" data-color="#333333">
                        <ul class="nav" >
                            <?php
                            $bug_id = $this->uri->segment(4);
                            $all_bugs = $this->db->get('tbl_bug')->result();
                            if (!empty($all_bugs)):foreach ($all_bugs as $v_bugs):
                                    ?>
                                    <li class="<?= ($bug_id == $v_bugs->bug_id ? 'active' : ' ') ?>" >
                                        <a href="<?= base_url() ?>admin/bugs/view_bug_details/<?= $v_bugs->bug_id ?>" data-toggle="tooltip" data-original-title="<?= lang($v_bugs->bug_status) ?>">
                                            <?= $v_bugs->bug_title ?>
                                            <p><small class="text-muted">    
                                                    <?= lang('comments') ?> 
                                                    <?php
                                                    $total_comments = count($this->db->where('bug_id', $v_bugs->bug_id)->get('tbl_task_comment')->result());
                                                    if (!empty($total_comments)) {
                                                        $comments = $total_comments;
                                                    } else {
                                                        $comments = 0;
                                                    }
                                                    ?>
                                                    <strong style="margin-left: 5px" class="label label-danger">
                                                        <i class="fa fa-comment"></i> <span style=""><?= $comments ?></span>
                                                    </strong>                                                
                                                </small>

                                                <small class="pull-right text-muted">                                               
                                                    <?= lang('attachment') ?> 
                                                    <?php
                                                    $total_attachment = count($this->db->where('bug_id', $v_bugs->bug_id)->get('tbl_task_attachment')->result());
                                                    if (!empty($total_attachment)) {
                                                        $attachment = $total_attachment;
                                                    } else {
                                                        $attachment = 0;
                                                    }
                                                    ?>
                                                    <strong style="margin-left: 5px" class="pull-right label label-success">
                                                        <i class="fa fa-file"> </i> <span > <?= $attachment ?></span>
                                                    </strong>                                                
                                                </small>
                                            </p>

                                        </a> </li>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </ul>
                    </div>             
                </div>
            </div>
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
            </ul>
            <div class="tab-content no-padding">
                <!-- Task Details tab Starts -->
                <div class="tab-pane <?= $active == 1 ? 'active' : '' ?>" id="task_details" style="position: relative;">
                    <div class="box" style="border: none; padding-top: 15px;" data-collapsed="0">                         
                        <div class="box-body">                                                                               
                            <ul class="list-group no-radius">                                                                
                                <li class="list-group-item">
                                    <span class="pull-right"><?php if (!empty($bug_details->bug_title)) echo $bug_details->bug_title; ?> </span><?= lang('bug_title') ?> 
                                </li>
                                <?php
                                if (!empty($bug_details->project_id)):
                                    $project_info = $this->db->where('project_id', $bug_details->project_id)->get('tbl_project')->row();
                                    ?>
                                    <li class="list-group-item">
                                        <span class="pull-right">
                                            <?php if (!empty($project_info->project_name)) echo $project_info->project_name; ?></span>
                                        <?= lang('project_name') ?></li>
                                <?php endif ?>
                                <?php
                                if (!empty($bug_details->opportunities_id)):
                                    $opportunity_info = $this->db->where('opportunities_id', $bug_details->opportunities_id)->get('tbl_opportunities')->row();
                                    ?>
                                    <li class="list-group-item">
                                        <span class="pull-right"><?php if (!empty($opportunity_info->opportunity_name)) echo $opportunity_info->opportunity_name; ?> </span><?= lang('opportunity_name') ?> 
                                    </li>
                                <?php endif ?>
                                <?php
                                if ($bug_details->bug_status == 'unconfirmed') {
                                    $label = 'warning';
                                } elseif ($bug_details->bug_status == 'confirmed') {
                                    $label = 'info';
                                } elseif ($bug_details->bug_status == 'in_progress') {
                                    $label = 'primary';
                                } else {
                                    $label = 'success';
                                }
                                $user_info = $this->db->where('user_id', $bug_details->reporter)->get('tbl_users')->row();
                                ?>
                                <li class="list-group-item">
                                    <span class="pull-right">
                                        <div class="btn-group">
                                            <button class="btn btn-xs btn-success dropdown-toggle" data-toggle="dropdown">
                                                <?= lang('change_status') ?>
                                                <span class="caret"></span></button>
                                            <ul class="dropdown-menu">

                                                <li><a  href="<?= base_url() ?>admin/bugs/change_status/<?= $bug_details->bug_id ?>/unconfirmed"><?= lang('unconfirmed') ?></a></li>
                                                <li><a  href="<?= base_url() ?>admin/bugs/change_status/<?= $bug_details->bug_id ?>/confirmed"><?= lang('confirmed') ?></a></li>
                                                <li><a  href="<?= base_url() ?>admin/bugs/change_status/<?= $bug_details->bug_id ?>/in_progress"><?= lang('in_progress') ?></a></li>
                                                <li><a  href="<?= base_url() ?>admin/bugs/change_status/<?= $bug_details->bug_id ?>/resolved"><?= lang('resolved') ?></a></li>
                                                <li><a  href="<?= base_url() ?>admin/bugs/change_status/<?= $bug_details->bug_id ?>/verified"><?= lang('verified') ?></a></li>

                                            </ul>                                      
                                        </div>
                                        <strong class="label label-<?= $label ?>"><?php if (!empty($bug_details->bug_status)) echo lang($bug_details->bug_status); ?></strong></span><?= lang('bug_status') ?></li>                                        
                                <div class="row">

                                    <div class="col-lg-6">                                        
                                        <li class="list-group-item">
                                            <span class="pull-right badge"><?php if (!empty($bug_details->reporter)) echo $user_info->username; ?> </span><?= lang('reporter') ?> 
                                        </li>
                                        <li class="list-group-item">
                                            <span class="pull-right"><strong class=""><?php if (!empty($bug_details->priority)) echo $bug_details->priority; ?></strong></span><?= lang('priority') ?></li>
                                    </div>  
                                    <div class="col-lg-6">                                        
                                        <li class="list-group-item">
                                            <span class="pull-right"><?= strftime(config_item('date_format'), strtotime($bug_details->created_time)) . ' ' . date('H:i A', strtotime($bug_details->created_time)) ?></span><?= lang('created_date') ?></li>                                        
                                        <li class="list-group-item">
                                            <span class="pull-right label label-success"><?= strftime(config_item('date_format'), strtotime($bug_details->update_time)) . ' ' . date('H:i A', strtotime($bug_details->update_time)) ?></span><?= lang('update_on') ?></li>                                        
                                    </div>  
                                </div>

                                <blockquote style="font-size: 12px; margin-top: 5px"><?php if (!empty($bug_details->bug_description)) echo $bug_details->bug_description; ?></blockquote>

                                <div class="form-group col-sm-12" id="border-none">
                                    <label class="col-sm-3 control-label"><?= lang('assined_to') ?>
                                        <span id="update" class="pull-right label label-primary"><?php echo lang('update'); ?></span>
                                        <span id="hide" class="pull-right label label-primary "><?php echo lang('close'); ?></span>

                                    </label>
                                    <div class="col-sm-7 ">
                                        <?php $assigned = unserialize($bug_details->assigned_to);
                                        ?>
                                        <div id="update_true" >
                                            <form id="form_validation" action="<?php echo base_url() ?>admin/bugs/update_member/<?php if (!empty($bug_details->bug_id)) echo $bug_details->bug_id; ?>" method="post">
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

                            <form id="form_validation" action="<?php echo base_url() ?>admin/bugs/save_comments" method="post" class="form-horizontal">
                                <input type="hidden" name="bug_id" value="<?php
                                if (!empty($bug_details->bug_id)) {
                                    echo $bug_details->bug_id;
                                }
                                ?>" class="form-control"   >  
                                <div class="form-group"> 
                                    <div class="col-sm-12">
                                        <textarea class="form-control col-sm-12" placeholder="<?= $bug_details->bug_title . ' ' . lang('comments') ?>" name="comment" style="height: 70px;" required ></textarea>
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
                            $comment_details = $this->db->where('bug_id', $bug_details->bug_id)->get('tbl_task_comment')->result();
                            if (!empty($comment_details)):foreach ($comment_details as $key => $v_comment):
                                    $user_info = $this->db->where(array('user_id' => $v_comment->user_id))->get('tbl_users')->row();
                                    $profile_info = $this->db->where(array('user_id' => $v_comment->user_id))->get('tbl_account_details')->row();
                                    if ($user_info->role_id == 1) {
                                        $label = '<small style="font-size:10px;padding:2px;" class="label label-danger ">Admin</small>';
                                    } elseif ($user_info->role_id == 2) {
                                        $label = '<small style="font-size:10px;padding:2px;" class="label label-danger ">Client</small>';
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
                                            <small class="text-muted pull-right"><i class="fa fa-clock-o"></i> <?= $this->bugs_model->get_time_different($today, $comment_time) ?> <?= lang('ago') ?>
                                                <?php if ($v_comment->user_id == $this->session->userdata('user_id')) { ?>
                                                    <?= btn_delete('admin/bugs/delete_bug_comments/' . $v_comment->bug_id . '/' . $v_comment->task_comment_id) ?>                                                
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

                            <form action="<?= base_url() ?>admin/bugs/save_bug_attachment/<?php
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
                                                                <input type="hidden" name="bug_files[]" value="<?php echo $v_files_image->files ?>">                                                                                                    
                                                                <input type="file" name="bug_files[]" >
                                                            </span>                                    
                                                            <span class="fileinput-filename"> <?php echo $v_files_image->file_name ?></span>                                          
                                                        <?php endforeach; ?>
                                                    <?php else: ?>
                                                        <span class="btn btn-default btn-file"><span class="fileinput-new" ><?= lang('select_file') ?></span>
                                                            <span class="fileinput-exists" ><?= lang('change') ?></span>                                            
                                                            <input type="file" name="bug_files[]" >
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
                                <input type="hidden" name="bug_id" value="<?php
                                if (!empty($bug_details->bug_id)) {
                                    echo $bug_details->bug_id;
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
                                                                <?= btn_delete('admin/bugs/delete_bug_files/' . $files_info[$key]->bug_id . '/' . $files_info[$key]->task_attachment_id) ?>                                                
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

                                                                                <a data-toggle="tooltip" data-placement="top" data-original-title="<?= $files_info[$key]->description ?>" class="text-info" href="<?= base_url() ?>admin/tasks/download_files/<?= $files_info[$key]->bug_id ?>/<?= $v_files->uploaded_files_id ?>">
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
                                                                                <a class="btn btn-xs btn-dark" data-toggle="tooltip" data-placement="top" title="Download" href="<?= base_url() ?>admin/tasks/download_files/<?= $files_info[$key]->bug_id ?>/<?= $v_files->uploaded_files_id ?>"><i class="fa fa-download"></i></a>
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

                            <form action="<?= base_url() ?>admin/bugs/save_bugs_notes/<?php
                            if (!empty($bug_details)) {
                                echo $bug_details->bug_id;
                            }
                            ?>" enctype="multipart/form-data" method="post" id="form" class="form-horizontal">
                                <div class="form-group">
                                    <label class="col-lg-1 control-label"><?= lang('notes') ?></label>
                                    <div class="col-lg-11">
                                        <textarea class="form-control textarea"  name="notes"><?= $bug_details->notes ?></textarea>
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
<span class="btn btn-default btn-file"><span class="fileinput-new" >Select file</span><span class="fileinput-exists" >Change</span><input type="file" name="bug_files[]" ></span> <span class="fileinput-filename"></span><a href="#" class="close fileinput-exists" data-dismiss="fileinput" style="float: none;">&times;</a></div></div>\n\<div class="col-sm-2">\n\<strong>\n\
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