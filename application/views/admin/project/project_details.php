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
                <h3 class="box-title"><?= lang('all_project') ?></h3>
            </div>           
            <div class="box-body">
                <div class="scrollable">
                    <div class="slim-scroll" data-height="auto" data-disable-fade-out="true" data-distance="0" data-size="5px" data-color="#333333">
                        <ul class="nav" >
                            <?php
                            $project_id = $this->uri->segment(4);
                            $all_project = $this->db->get('tbl_project')->result();
                            if (!empty($all_project)):foreach ($all_project as $v_project):
                                    ?>
                                    <li class="<?= ($project_id == $v_project->project_id ? 'active' : ' ') ?>" >
                                        <a href="<?= base_url() ?>admin/project/project_details/<?= $v_project->project_id ?>" data-toggle="tooltip" data-original-title="<?= $v_project->project_name ?>">
                                            <?php
                                            $client_info = $this->items_model->check_by(array('client_id' => $v_project->client_id), 'tbl_client');
                                            if (!empty($client_info)) {
                                                if ($client_info->client_status == 1) {
                                                    $status = 'Person';
                                                } else {
                                                    $status = 'Company';
                                                }
                                                echo $name = $client_info->name . ' (' . $status . ')';
                                            } else {
                                                echo $name = $v_project->project_name;
                                            }
                                            ?>
                                            <div class="pull-right">
                                                <small class="text-muted">   

                                                    <?php
                                                    if (!empty($project_details->client_id)) {
                                                        $currency = $this->items_model->client_currency_sambol($project_details->client_id);
                                                    } else {
                                                        $currency = $this->db->where('code', config_item('default_currency'))->get('tbl_currencies')->row();
                                                    }
                                                    ?>
                                                    <strong><?= $currency->symbol ?> <?= number_format($project_details->project_cost, 2) ?></strong>                                                    
                                                </small>
                                            </div>
                                            <p><small class="text-muted">    
                                                    <?= lang('milestones') ?> 
                                                    <?php
                                                    $total_milestones = count($this->db->where('project_id', $v_project->project_id)->get('tbl_milestones')->result());
                                                    if (!empty($total_milestones)) {
                                                        $milestones = $total_milestones;
                                                    } else {
                                                        $milestones = 0;
                                                    }
                                                    ?>
                                                    <strong style="margin-left: 5px" class="label label-danger">
                                                        <i class="fa fa-rocket"></i> <span style=""><?= $milestones ?></span>
                                                    </strong>                                                
                                                </small>

                                                <small class="pull-right text-muted">                                               
                                                    <?= lang('tasks') ?> 
                                                    <?php
                                                    $total_tasks = count($this->db->where('project_id', $v_project->project_id)->get('tbl_task')->result());
                                                    if (!empty($total_tasks)) {
                                                        $tasks = $total_tasks;
                                                    } else {
                                                        $tasks = 0;
                                                    }
                                                    ?>

                                                    <strong style="margin-left: 5px" class="pull-right label label-success">
                                                        <i class="fa fa-tasks"> </i> <span > <?= $tasks ?></span>
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
                <li class="btn-success" style="margin-right: 0px; "><a style="color: #fff" onclick="return confirm('Are you sure to <?= lang('create_invoice') ?> of <?= $project_details->project_name ?> ?')" data-toggle="tooltip" data-placement="top" title="<?= lang('generate_invoice') ?>" href="<?= base_url() ?>admin/project/invoice/<?= $project_details->project_id ?>" class="btn btn-dark btn-sm" ><i class="fa fa-money"></i> <?= lang('create_invoice') ?></a></li>
                <li class="<?= $active == 1 ? 'active' : '' ?>" style="margin-right: 0px; "><a href="#task_details" data-toggle="tab"><?= lang('project_details') ?></a></li>                
                <li class="<?= $active == 2 ? 'active' : '' ?>" style="margin-right: 0px; "><a href="#activities"  data-toggle="tab"><?= lang('activities') ?></a></li>                
                <li class="<?= $active == 3 ? 'active' : '' ?>"><a href="#task_comments"  data-toggle="tab"><?= lang('comments') ?></a></li>
                <li class="<?= $active == 4 ? 'active' : '' ?>"><a href="#task_attachments"  data-toggle="tab"><?= lang('attachment') ?></a></li>                
                <li class="<?= $active == 5 ? 'active' : '' ?>"><a href="#milestones"  data-toggle="tab"><?= lang('milestones') ?></a></li>                
                <li class="<?= $active == 6 ? 'active' : '' ?>"><a href="#task"  data-toggle="tab"><?= lang('tasks') ?></a></li>                                
                <li class="<?= $active == 7 ? 'active' : '' ?>"><a href="#bugs"  data-toggle="tab"><?= lang('bugs') ?></a></li>                                
            </ul>
            <div class="tab-content no-padding">                
                <!-- Task Details tab Starts -->
                <div class="tab-pane <?= $active == 1 ? 'active' : '' ?>" id="task_details" style="position: relative;">
                    <div class="box" style="border: none; padding-top: 15px;" data-collapsed="0">                         
                        <div class="box-body">                                                                       
                            <div class="form-group col-sm-12">                                
                                <div class="col-sm-12">                                          
                                    <div class="progress progress-striped ">
                                        <div class="progress-bar progress-bar-success " data-toggle="tooltip" data-original-title="<?= $project_details->progress ?>%" style="width: <?= $project_details->progress ?>%"></div>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <label class="control-label"><strong><?= lang('project_name') ?> :</strong> </label>
                                    <?php
                                    if (!empty($project_details->project_name)) {
                                        echo $project_details->project_name;
                                    }
                                    ?>                                    
                                </div>
                                <?php
                                $client_info = $this->db->where('client_id', $project_details->client_id)->get('tbl_client')->row();
                                if (!empty($client_info)) {
                                    if ($client_info->client_status == 1) {
                                        $status = 'Person';
                                    } else {
                                        $status = 'Company';
                                    }
                                    $name = $client_info->name . ' (' . $status . ')';
                                } else {
                                    $name = '-';
                                }
                                ?>  
                                <div class="col-sm-6">
                                    <label class="control-label"><strong><?= lang('client') ?> :</strong></label>
                                    <?php echo $name; ?>
                                </div>

                            </div>
                            <div class="form-group col-sm-12">
                                <div class="col-sm-6">
                                    <label class="control-label"><strong><?= lang('start_date') ?> :</strong> </label>
                                    <?= strftime(config_item('date_format'), strtotime($project_details->start_date)) ?>                                   
                                </div>                             
                                <div class="col-sm-6">
                                    <label class="control-label"><strong><?= lang('end_date') ?> :</strong> </label>
                                    <?= strftime(config_item('date_format'), strtotime($project_details->end_date)) ?>                                   
                                </div>                                                             
                            </div>                           
                            <div class="form-group col-sm-12">
                                <div class="col-sm-6">
                                    <label class="control-label"><strong><?= lang('demo_url') ?> :</strong> </label>
                                    <?php
                                    if (!empty($project_details->demo_url)) {
                                        ?>
                                        <a href="//<?php echo $project_details->demo_url; ?>" target="_blank" ><?php echo $project_details->demo_url ?></a>
                                        <?php
                                    } else {
                                        echo '-';
                                    }
                                    ?>
                                </div>
                            </div>
                            <div class="form-group col-sm-12">
                                <div class="col-sm-6">
                                    <label class="control-label"><strong><?= lang('project_cost') ?> :</strong> </label>

                                    <?php
                                    if (!empty($project_details->client_id)) {
                                        $currency = $this->items_model->client_currency_sambol($project_details->client_id);
                                    } else {
                                        $currency = $this->db->where('code', config_item('default_currency'))->get('tbl_currencies')->row();
                                    }
                                    ?>
                                    <strong><?= $currency->symbol ?>
                                        <?= number_format($project_details->project_cost, 2) ?>
                                    </strong>
                                </div>  
                                <div class="col-sm-6">
                                    <label class="control-label"><strong><?= lang('status') ?> :</strong></label>
                                    <?php
                                    if (!empty($project_details->project_status)) {
                                        if ($project_details->project_status == 'completed') {
                                            $status = "<span class='label label-success'>" . lang($project_details->project_status) . "</span>";
                                        } elseif ($project_details->project_status == 'in_progress') {
                                            $status = "<span class='label label-primary'>" . lang($project_details->project_status) . "</span>";
                                        } elseif ($project_details->project_status == 'cancel') {
                                            $status = "<span class='label label-danger'>" . lang($project_details->project_status) . "</span>";
                                        } else {
                                            $status = "<span class='label label-warning'>" . lang($project_details->project_status) . "</span>";
                                        }
                                        echo $status;
                                    }
                                    ?>                                    
                                    <div class="btn-group">
                                        <button class="btn btn-xs btn-success dropdown-toggle" data-toggle="dropdown">
                                            <?= lang('change_status') ?>
                                            <span class="caret"></span></button>
                                        <ul class="dropdown-menu">
                                            <li><a  href="<?= base_url() ?>admin/project/change_status/<?= $project_details->project_id . '/started' ?>"><?= lang('started') ?></a></li>                                                                                               
                                            <li><a  href="<?= base_url() ?>admin/project/change_status/<?= $project_details->project_id . '/in_progress' ?>"><?= lang('in_progress') ?></a></li>
                                            <li><a  href="<?= base_url() ?>admin/project/change_status/<?= $project_details->project_id . '/cancel' ?>"><?= lang('cancel') ?></a></li>
                                            <li><a  href="<?= base_url() ?>admin/project/change_status/<?= $project_details->project_id . '/completed' ?>"><?= lang('completed') ?></a></li>
                                        </ul>                                      
                                    </div>
                                </div>                               
                            </div>

                            <div class="form-group col-sm-12">
                                <label class="col-sm-2 control-label"><?= lang('short_note') ?></label>
                                <div class="col-sm-10">
                                    <blockquote style="font-size: 12px; height: 100px;"><?php
                                        if (!empty($project_details->description)) {
                                            echo $project_details->description;
                                        }
                                        ?></blockquote>
                                </div>
                            </div>
                            <?php if ($project_details->assign_to != '-') { ?>
                                <div class="form-group col-sm-12" id="border-none">
                                    <label class="col-sm-2 control-label"><?= lang('staff') ?></label>
                                    <div class="col-sm-10">
                                        <?php
                                        $assigned = unserialize($project_details->assign_to);
                                        ?>
                                        <table class="table table-bordered" style="background-color: #EEE;">
                                            <tbody>
                                                <?php
                                                if (!empty($assigned['assign_to'])) :
                                                    foreach ($assigned['assign_to'] as $v_assign) :
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
                            <?php } ?>

                        </div>                         

                    </div>        
                </div>
                <!-- Task Details tab Ends -->

                <!-- Task Comments Panel Starts --->
                <div class="tab-pane <?= $active == 2 ? 'active' : '' ?>" id="activities" style="position: relative;">
                    <div class="box" style="border: none; padding-top: 15px;" data-collapsed="0">                        
                        <div class="box-body chat" id="chat-box">
                            <div  id="activity">
                                <ul class="list-group no-radius   m-t-n-xxs list-group-lg no-border">
                                    <?php
                                    $activities_info = $this->db->where(array('module' => 'project', 'module_field_id' => $project_details->project_id))->order_by('activity_date', 'desc')->get('tbl_activities')->result();
                                    if (!empty($activities_info)) {
                                        foreach ($activities_info as $v_activities) {
                                            $profile_info = $this->db->where(array('user_id' => $v_activities->user))->get('tbl_account_details')->row();

                                            $user_info = $this->db->where(array('user_id' => $v_activities->user))->get('tbl_users')->row();
                                            ?>
                                            <li class="list-group-item">
                                                <a class="recect_task pull-left m-r-sm">

                                                    <?php if (!empty($profile_info)) {
                                                        ?>
                                                        <img style="width: 30px;margin-left: 18px;
                                                             height: 29px;
                                                             border: 1px solid #aaa;" src="<?= base_url() . $profile_info->avatar ?>" class="img-circle">
                                                         <?php } ?>                                 
                                                </a>


                                                <a  class="clear">
                                                    <small class="pull-right"><?= strftime(config_item('date_format') . " %H:%M:%S", strtotime($v_activities->activity_date)) ?></small>
                                                    <strong class="block"><?= ucfirst($user_info->username) ?></strong>
                                                    <small>
                                                        <?php
                                                        echo sprintf(lang($v_activities->activity) . ' <strong style="color:#000"><em>' . $v_activities->value1 . '</em>' . '<em>' . $v_activities->value2 . '</em></strong>');
                                                        ?> 
                                                    </small>
                                                </a>
                                            </li>
                                            <?php
                                        }
                                    }
                                    ?>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="tab-pane <?= $active == 3 ? 'active' : '' ?>" id="task_comments" style="position: relative;">
                    <div class="box" style="border: none; padding-top: 15px;" data-collapsed="0">                        
                        <div class="box-body chat" id="chat-box">

                            <form id="form_validation" action="<?php echo base_url() ?>admin/project/save_comments" method="post" class="form-horizontal">
                                <input type="hidden" name="project_id" value="<?php
                                if (!empty($project_details->project_id)) {
                                    echo $project_details->project_id;
                                }
                                ?>" class="form-control"   >  
                                <div class="form-group"> 
                                    <div class="col-sm-12">
                                        <textarea class="form-control col-sm-12" placeholder="<?= $project_details->project_name . ' ' . lang('comments') ?>" name="comment" style="height: 70px;" required ></textarea>
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
                            $comment_details = $this->db->where('project_id', $project_details->project_id)->get('tbl_task_comment')->result();
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
                                            <small class="text-muted pull-right"><i class="fa fa-clock-o"></i> <?= $this->items_model->get_time_different($today, $comment_time) ?> <?= lang('ago') ?>
                                                <?php if ($v_comment->user_id == $this->session->userdata('user_id')) { ?>
                                                    <?= btn_delete('admin/project/delete_comments/' . $v_comment->project_id . '/' . $v_comment->task_comment_id) ?>                                                
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
                <div class="tab-pane <?= $active == 4 ? 'active' : '' ?>" id="task_attachments" style="position: relative;">
                    <div class="box" style="border: none; padding-top: 15px;" data-collapsed="0">                        
                        <div class="panel-body">

                            <form action="<?= base_url() ?>admin/project/save_attachment/<?php
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
                                <input type="hidden" name="project_id" value="<?php
                                if (!empty($project_details->project_id)) {
                                    echo $project_details->project_id;
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
                                                                <?= btn_delete('admin/project/delete_files/' . $files_info[$key]->project_id . '/' . $files_info[$key]->task_attachment_id) ?>                                                
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
                <!-- // milestones-->
                <div class="tab-pane <?= $active == 5 ? 'active' : '' ?>" id="milestones" style="position: relative;">
                    <div class="box" style="border: none; padding-top: 15px;" data-collapsed="0">      
                        <div class="nav-tabs-custom">
                            <!-- Tabs within a box -->
                            <ul class="nav nav-tabs">
                                <li class="<?= $miles_active == 1 ? 'active' : ''; ?>"><a href="#manage_milestone" data-toggle="tab"><?= lang('milestones') ?></a></li>
                                <li class="<?= $miles_active == 2 ? 'active' : ''; ?>"><a href="#create_milestone" data-toggle="tab"><?= lang('add_milestone') ?></a></li>                                                                     
                            </ul>
                            <div class="tab-content no-padding">
                                <!-- ************** general *************-->
                                <div class="tab-pane <?= $miles_active == 1 ? 'active' : ''; ?>" id="manage_milestone">
                                    <div class="table-responsive">
                                        <table id="table-milestones" class="table table-striped     DataTables">
                                            <thead>
                                                <tr>
                                                    <th><?= lang('milestone_name') ?></th>
                                                    <th class="col-date"><?= lang('start_date') ?></th>
                                                    <th class="col-date"><?= lang('due_date') ?></th>
                                                    <th><?= lang('progress') ?></th>                                                    
                                                    <th ><?= lang('action') ?></th>                                                    
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php
                                                $all_milestones_info = $this->db->where('project_id', $project_details->project_id)->get('tbl_milestones')->result();

                                                if (!empty($all_milestones_info)) {
                                                    foreach ($all_milestones_info as $key => $v_milestones) {
                                                        $progress = $this->items_model->calculate_milestone_progress($v_milestones->milestones_id);
                                                        $all_milestones_task = $this->db->where('milestones_id', $v_milestones->milestones_id)->get('tbl_task')->result();
                                                        ?>
                                                        <tr>
                                                            <td><a class="text-info" href="#" data-original-title="<?= $v_milestones->description ?>" data-toggle="tooltip" data-placement="top" title = ""><?= $v_milestones->milestone_name ?></a></td>
                                                            <td><?= strftime(config_item('date_format'), strtotime($v_milestones->start_date)) ?></td>
                                                            <td><?php
                                                                $due_date = $v_milestones->end_date;
                                                                $due_time = strtotime($due_date);
                                                                $current_time = time();
                                                                ?>
                                                                <?= strftime(config_item('date_format'), strtotime($due_date)) ?>
                                                                <?php if ($current_time > $due_time && $progress < 100) { ?>
                                                                    <span class="label label-danger"><?= lang('overdue') ?></span>
                                                                <?php } ?>
                                                            </td>
                                                            <td>
                                                                <div class="inline ">
                                                                    <div class="easypiechart text-success" style="margin: 0px;" data-percent="<?= $progress ?>" data-line-width="5" data-track-Color="#f0f0f0" data-bar-color="#<?php
                                                                    if ($progress >= 100) {
                                                                        echo '8ec165';
                                                                    } else {
                                                                        echo 'fb6b5b';
                                                                    }
                                                                    ?>" data-rotate="270" data-scale-Color="false" data-size="50" data-animate="2000">
                                                                        <span class="small text-muted"><?= $progress ?>%</span>
                                                                    </div>
                                                                </div>

                                                            </td>                                                            
                                                            <td>
                                                                <?php echo btn_edit('admin/project/project_details/' . $v_milestones->project_id . '/milestone/' . $v_milestones->milestones_id) ?>
                                                                <?php echo btn_delete('admin/project/delete_milestones/' . $v_milestones->project_id . '/' . $v_milestones->milestones_id) ?>                                        
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
                                <div class="tab-pane <?= $miles_active == 2 ? 'active' : ''; ?>" id="create_milestone">
                                    <form role="form" enctype="multipart/form-data" id="form" action="<?php echo base_url(); ?>admin/project/save_milestones/<?php
                                    if (!empty($milestones_info)) {
                                        echo $milestones_info->milestones_id;
                                    }
                                    ?>" method="post" class="form-horizontal  ">

                                        <div class="form-group">
                                            <label class="col-lg-3 control-label"><?= lang('milestone_name') ?> <span class="text-danger">*</span></label>
                                            <div class="col-lg-6">
                                                <input type="hidden" class="form-control"  value="<?= $project_id ?>" name="project_id">
                                                <input type="text" class="form-control" value="<?php
                                                if (!empty($milestones_info)) {
                                                    echo $milestones_info->milestone_name;
                                                }
                                                ?>" placeholder="<?= lang('milestone_name') ?>" name="milestone_name" required>
                                            </div>
                                        </div>	

                                        <div class="form-group">
                                            <label class="col-lg-3 control-label"><?= lang('description') ?> <span class="text-danger">*</span></label>
                                            <div class="col-lg-6">
                                                <textarea name="description" class="form-control" placeholder="<?= lang('description') ?>" required><?php
                                                    if (!empty($milestones_info->description)) {
                                                        echo $milestones_info->description;
                                                    }
                                                    ?></textarea>
                                            </div>
                                        </div>	

                                        <div class="form-group">
                                            <label class="col-lg-3 control-label"><?= lang('start_date') ?> <span class="text-danger">*</span></label> 
                                            <div class="col-lg-6">
                                                <div class="input-group">
                                                    <input type="text" name="start_date"   class="form-control datepicker" value="<?php
                                                    if (!empty($milestones_info->start_date)) {
                                                        echo $milestones_info->start_date;
                                                    }
                                                    ?>" data-date-format="<?= config_item('date_picker_format'); ?>" required>
                                                    <div class="input-group-addon">
                                                        <a href="#"><i class="entypo-calendar"></i></a>
                                                    </div>
                                                </div>
                                            </div> 
                                        </div> 
                                        <div class="form-group">
                                            <label class="col-lg-3 control-label"><?= lang('end_date') ?> <span class="text-danger">*</span></label> 
                                            <div class="col-lg-6">
                                                <div class="input-group">
                                                    <input type="text" name="end_date"class="form-control datepicker" value="<?php
                                                    if (!empty($milestones_info->end_date)) {
                                                        echo $milestones_info->end_date;
                                                    }
                                                    ?>" data-date-format="<?= config_item('date_picker_format'); ?>"  required=""  >
                                                    <div class="input-group-addon">
                                                        <a href="#"><i class="entypo-calendar"></i></a>
                                                    </div>
                                                </div>
                                            </div> 
                                        </div> 
                                        <input type="hidden" name="project_id" value="<?php
                                        if (!empty($project_details->project_id)) {
                                            echo $project_details->project_id;
                                        }
                                        ?>" class="form-control"   >
                                        <div class="form-group">
                                            <label class="col-lg-3 control-label"><?= lang('responsible') ?> <span class="text-danger">*</span></label> 
                                            <div class="col-lg-6">
                                                <select  name="user_id" style="width: 100%" class="select_box" required="">                                                                                                  
                                                    <optgroup label="<?= lang('admin_staff') ?>"> 
                                                        <?php
                                                        $user_info = $this->db->where('role_id !=', 2)->get('tbl_users')->result();
                                                        if (!empty($user_info)) {
                                                            foreach ($user_info as $key => $v_user) {
                                                                ?>
                                                                <option value="<?= $v_user->user_id ?>" <?php
                                                                if (!empty($milestones_info->user_id)) {
                                                                    echo $v_user->user_id == $milestones_info->user_id ? 'selected' : '';
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
                                            <div class="col-lg-3"></div>
                                            <div class="col-lg-6">
                                                <button type="submit" class="btn btn-sm btn-primary"><?= lang('add_milestone') ?></button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- End milestones-->

                <!-- Start Tasks Management-->
                <div class="tab-pane <?= $active == 6 ? 'active' : '' ?>" id="task" style="position: relative;">
                    <div class="box" style="border: none; padding-top: 15px;" data-collapsed="0">      
                        <div class="nav-tabs-custom">
                            <!-- Tabs within a box -->
                            <ul class="nav nav-tabs">
                                <li class="<?= $task_active == 1 ? 'active' : ''; ?>"><a href="#manage_task" data-toggle="tab"><?= lang('task') ?></a></li>                                
                                <li class=""><a href="<?= base_url() ?>admin/tasks/all_task/project/<?= $project_details->project_id ?>" ><?= lang('new_task') ?></a></li>                                
                            </ul>
                            <div class="tab-content no-padding">
                                <!-- ************** general *************-->
                                <div class="tab-pane <?= $task_active == 1 ? 'active' : ''; ?>" id="manage_task">
                                    <div class="table-responsive">
                                        <table id="table-milestones" class="table table-striped     DataTables">
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
                                                $all_task_info = $this->db->where('project_id', $project_details->project_id)->get('tbl_task')->result();

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
                                                            <td>
                                                                <?php echo btn_view('admin/tasks/view_task_details/' . $v_task->task_id) ?>                                
                                                                <?php echo btn_edit('admin/tasks/all_task/' . $v_task->task_id) ?>
                                                            </td>                                
                                                        </tr>                
                                                    <?php endforeach; ?>
                                                <?php endif; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                <!-- End Tasks Management-->                

                            </div>  
                        </div>
                    </div>         
                </div>         
                <div class="tab-pane <?= $active == 7 ? 'active' : '' ?>" id="bugs" style="position: relative;">
                    <div class="box" style="border: none; padding-top: 15px;" data-collapsed="0">      
                        <div class="nav-tabs-custom">
                            <!-- Tabs within a box -->
                            <ul class="nav nav-tabs">
                                <li class="<?= $bugs_active == 1 ? 'active' : ''; ?>"><a href="#manage_bugs" data-toggle="tab"><?= lang('all_bugs') ?></a></li>                                
                                <li class=""><a href="<?= base_url() ?>admin/bugs/index/project/<?= $project_details->project_id ?>" ><?= lang('new_bugs') ?></a></li>                                
                            </ul>
                            <div class="tab-content no-padding">
                                <!-- ************** general *************-->
                                <div class="tab-pane <?= $task_active == 1 ? 'active' : ''; ?>" id="manage_task">
                                    <div class="table-responsive">
                                        <table id="table-milestones" class="table table-striped     DataTables">
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
                                                $all_bugs_info = $this->db->where('project_id', $project_details->project_id)->get('tbl_bug')->result();

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
                                                            </td>                                
                                                        </tr>                
                                                    <?php endforeach; ?>
                                                <?php endif; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                <!-- End Tasks Management-->                

                            </div>  
                        </div>
                    </div>         
                </div>         
            </div>         
        </div>         
    </div>
</div>         