<?= message_box('success'); ?>
<?= message_box('error'); ?>
<div class="nav-tabs-custom">
    <!-- Tabs within a box -->
    <ul class="nav nav-tabs">
        <li class="<?= $active == 1 ? 'active' : ''; ?>"><a href="#manage" data-toggle="tab"><?= lang('all_project') ?></a></li>
        <li class="<?= $active == 2 ? 'active' : ''; ?>"><a href="#create" data-toggle="tab"><?= lang('new_project') ?></a></li>                                                                     
        <li class="pull-right <?= $active == 3 ? 'active' : ''; ?>"><a href="#archived" data-toggle="tab"><?= lang('archived') ?></a></li>                                                                     
    </ul>
    <div class="tab-content no-padding">
        <!-- ************** general *************-->
        <div class="tab-pane <?= $active == 1 ? 'active' : ''; ?>" id="manage">

            <div class="table-responsive">
                <table class="table table-striped DataTables " id="DataTables">
                    <thead>
                        <tr>                            
                            <th><?= lang('project_name') ?></th>                            
                            <th><?= lang('client') ?></th>                                                                              
                            <th><?= lang('start_date') ?></th>                            
                            <th><?= lang('end_date') ?></th>                            
                            <th><?= lang('status') ?></th>                            
                            <th class="col-options no-sort" ><?= lang('action') ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $all_project = $this->db->where('project_status !=', 'completed')->get('tbl_project')->result();
                        if (!empty($all_project)):foreach ($all_project as $v_project):
                                ?>
                                <tr>                                    
                                    <?php
                                    $client_info = $this->db->where('client_id', $v_project->client_id)->get('tbl_client')->row();
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
                                    <td>
                                        <a class="text-info" href="<?= base_url() ?>admin/project/project_details/<?= $v_project->project_id ?>"><?= $v_project->project_name ?></a>
                                        <?php if (time() > strtotime($v_project->end_date) AND $v_project->progress < 100) { ?>
                                            <span class="label label-danger pull-right"><?= lang('overdue') ?></span>
                                        <?php } ?>

                                        <div class="progress progress-xs progress-striped active">
                                            <div class="progress-bar progress-bar-<?php echo ($v_project->progress >= 100 ) ? 'success' : 'primary'; ?>" data-toggle="tooltip" data-original-title="<?= $v_project->progress ?>%" style="width: <?= $v_project->progress; ?>%"></div>
                                        </div>

                                    </td>
                                    <td><?= $name ?></td>

                                    <td><?= strftime(config_item('date_format'), strtotime($v_project->start_date)) ?></td>
                                    <td><?= strftime(config_item('date_format'), strtotime($v_project->end_date)) ?></td>

                                    <td><?php
                                        if (!empty($v_project->project_status)) {
                                            if ($v_project->project_status == 'completed') {
                                                $status = "<span class='label label-success'>" . lang($v_project->project_status) . "</span>";
                                            } elseif ($v_project->project_status == 'in_progress') {
                                                $status = "<span class='label label-primary'>" . lang($v_project->project_status) . "</span>";
                                            } elseif ($v_project->project_status == 'cancel') {
                                                $status = "<span class='label label-danger'>" . lang($v_project->project_status) . "</span>";
                                            } else {
                                                $status = "<span class='label label-warning'>" . lang($v_project->project_status) . "</span>";
                                            }
                                            echo $status;
                                        }
                                        ?>      </td>     
                                    <td>
                                        <?= btn_view('admin/project/project_details/' . $v_project->project_id) ?>
                                        <?= btn_edit('admin/project/index/' . $v_project->project_id) ?>
                                        <?= btn_delete('admin/project/delete_project/' . $v_project->project_id) ?>
                                        <div class="btn-group">
                                            <button class="btn btn-xs btn-success dropdown-toggle" data-toggle="dropdown">
                                                <?= lang('change_status') ?>
                                                <span class="caret"></span></button>
                                            <ul class="dropdown-menu">                                                                                               
                                                <li><a  href="<?= base_url() ?>admin/project/change_status/<?= $v_project->project_id . '/started' ?>"><?= lang('started') ?></a></li>                                                                                               
                                                <li><a  href="<?= base_url() ?>admin/project/change_status/<?= $v_project->project_id . '/in_progress' ?>"><?= lang('in_progress') ?></a></li>                                                                                               
                                                <li><a  href="<?= base_url() ?>admin/project/change_status/<?= $v_project->project_id . '/cancel' ?>"><?= lang('cancel') ?></a></li>                                                                                               
                                                <li><a  href="<?= base_url() ?>admin/project/change_status/<?= $v_project->project_id . '/completed' ?>"><?= lang('completed') ?></a></li>                                                                                                                                               
                                            </ul>                                      
                                        </div>
                                    </td>
                                </tr>
                                <?php
                            endforeach;
                        endif;
                        ?>
                    </tbody>
                </table>
            </div>
        </div>        
        <div class="tab-pane <?= $active == 2 ? 'active' : ''; ?>" id="create">
            <form role="form" enctype="multipart/form-data" id="form" action="<?php echo base_url(); ?>admin/project/saved_project/<?php
            if (!empty($project_info)) {
                echo $project_info->project_id;
            }
            ?>" method="post" class="form-horizontal  ">
                <div class="panel-body">
                    <?php if (!empty($project_info)) { ?>
                        <div class="form-group">
                            <label class="col-lg-2 control-label"><?= lang('change_status') ?> <span class="text-danger">*</span></label>
                            <div class="col-lg-5">
                                <select name="project_status" class="form-control select_box" style="width: 100%" required="">                            
                                    <option value="started"><?= lang('started') ?></option>     
                                    <option value="in_progress"><?= lang('in_progress') ?></option>                                
                                    <option value="cancel"><?= lang('cancel') ?></option>                                
                                    <option value="completed"><?= lang('completed') ?></option>                                
                                </select>
                            </div>
                        </div>
                    <?php } ?>
                    <div class="form-group">
                        <label class="col-lg-2 control-label"><?= lang('project_name') ?> <span class="text-danger">*</span></label>
                        <div class="col-lg-5">
                            <input type="text" class="form-control" value="<?php
                            if (!empty($project_info)) {
                                echo $project_info->project_name;
                            }
                            ?>" name="project_name" required="">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-lg-2 control-label"><?= lang('select_client') ?> <span class="text-danger">*</span></label>
                        <div class="col-lg-5">
                            <select name="client_id" class="form-control select_box" style="width: 100%" required="">                            
                                <option value=""><?= lang('select_client') ?></option>
                                <?php
                                $all_client = $this->db->get('tbl_client')->result();
                                if (!empty($all_client)) {
                                    foreach ($all_client as $v_client) {
                                        ?>
                                        <option value="<?= $v_client->client_id ?>" <?php
                                        if (!empty($project_info) && $project_info->client_id == $v_client->client_id) {
                                            echo 'selected';
                                        }
                                        ?>><?= $v_client->name ?></option>
                                                <?php
                                            }
                                        }
                                        ?>
                            </select>
                        </div>
                    </div>    

                    <div class="form-group">
                        <label class="col-lg-2 control-label"><?= lang('assigned_to') ?> <span class="text-danger">*</span></label>
                        <div class="col-lg-5">
                            <!-- Build your select: -->
                            <select  multiple="multiple" class="select_multi"  required name="assign_to[]" style="width: 100%;height: 80%"> 
                                <optgroup label="<?= lang('admin_staff') ?>"> 
                                    <?php
                                    if (!empty($assign_user)) {
                                        foreach ($assign_user as $key => $v_user) {
                                            ?>
                                            <option value="<?= $v_user->user_id ?>" <?php
                                            if (!empty($project_info->assign_to)) {
                                                $user_id = unserialize($project_info->assign_to);
                                                foreach ($user_id['assign_to'] as $assding_id) {
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
                        <label class="col-lg-2 control-label"><?= lang('progress') ?></label>
                        <div class="col-lg-5">                             
                            <input type="text" name="progress" value="" class="slider form-control" data-slider-min="0" data-slider-max="100" data-slider-step="1" data-slider-value="<?php
                            if (!empty($project_info->progress)) {
                                echo $project_info->progress;
                            } else {
                                echo '0';
                            }
                            ?>" data-slider-orientation="horizontal"  data-slider-tooltip="show"  data-slider-id="red"/>                            

                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-lg-2 control-label"><?= lang('start_date') ?> <span class="text-danger">*</span></label> 
                        <div class="col-lg-5">
                            <div class="input-group">
                                <input type="text" name="start_date"  class="form-control datepicker" value="<?php
                                if (!empty($project_info->start_date)) {
                                    echo date('Y-m-d', strtotime($project_info->start_date));
                                }
                                ?>" data-date-format="<?= config_item('date_picker_format'); ?>">
                                <div class="input-group-addon">
                                    <a href="#"><i class="entypo-calendar"></i></a>
                                </div>
                            </div>
                        </div> 
                    </div>                    
                    <div class="form-group">
                        <label class="col-lg-2 control-label"><?= lang('end_date') ?> <span class="text-danger">*</span></label> 
                        <div class="col-lg-5">
                            <div class="input-group">
                                <input type="text" name="end_date"  class="form-control datepicker" value="<?php
                                if (!empty($project_info->end_date)) {
                                    echo date('Y-m-d', strtotime($project_info->end_date));
                                }
                                ?>" data-date-format="<?= config_item('date_picker_format'); ?>">
                                <div class="input-group-addon">
                                    <a href="#"><i class="entypo-calendar"></i></a>
                                </div>
                            </div>
                        </div> 
                    </div>                    
                    <div class="form-group">
                        <label class="col-lg-2 control-label"><?= lang('project_cost') ?></label>
                        <div class="col-lg-3">
                            <input type="text" value="<?php
                            if (!empty($project_info->project_cost)) {
                                echo $project_info->project_cost;
                            }
                            ?>" class="form-control" required placeholder="100" name="project_cost">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-lg-2 control-label"><?= lang('demo_url') ?></label>
                        <div class="col-lg-5">
                            <input type="text" value="<?php
                            if (!empty($project_info->demo_url)) {
                                echo $project_info->demo_url;
                            }
                            ?>" class="form-control" placeholder="http://www.demourl.com" name="demo_url">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-lg-2 control-label"><?= lang('description') ?> <span class="text-danger">*</span></label>
                        <div class="col-lg-8">
                            <textarea style="" name="description" class="form-control textarea" rows="5" placeholder="<?= lang('description') ?>" required><?php
                                if (!empty($project_info->description)) {
                                    echo $project_info->description;
                                }
                                ?></textarea>
                        </div>
                    </div>                    
                    <div class="form-group">
                        <label class="col-lg-2 control-label"></label> 
                        <div class="col-lg-5">
                            <button type="submit" class="btn btn-sm btn-primary"><?= lang('updates') ?></button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
        <div class="tab-pane <?= $active == 3 ? 'active' : ''; ?>" id="archived">

            <div class="table-responsive">
                <table class="table table-striped DataTables " id="DataTables">
                    <thead>
                        <tr>                            
                            <th><?= lang('project_name') ?></th>                            
                            <th><?= lang('client') ?></th>                                                                              
                            <th><?= lang('start_date') ?></th>                            
                            <th><?= lang('end_date') ?></th>                            
                            <th><?= lang('status') ?></th>                            
                            <th class="col-options no-sort" ><?= lang('action') ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $all_archived_project = $this->db->where('project_status', 'completed')->get('tbl_project')->result();
                        if (!empty($all_archived_project)):foreach ($all_archived_project as $v_project):
                                ?>
                                <tr>                                    
                                    <?php
                                    $client_info = $this->db->where('client_id', $v_project->client_id)->get('tbl_client')->row();
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
                                    <td>
                                        <a class="text-info" href="<?= base_url() ?>admin/project/project_details/<?= $v_project->project_id ?>"><?= $v_project->project_name ?></a>
                                        <?php if (time() > strtotime($v_project->end_date) AND $v_project->progress < 100) { ?>
                                            <span class="label label-danger pull-right"><?= lang('overdue') ?></span>
                                        <?php } ?>

                                        <div class="progress progress-xs progress-striped active">
                                            <div class="progress-bar progress-bar-<?php echo ($v_project->progress >= 100 ) ? 'success' : 'primary'; ?>" data-toggle="tooltip" data-original-title="<?= $v_project->progress ?>%" style="width: <?= $v_project->progress; ?>%"></div>
                                        </div>

                                    </td>
                                    <td><?= $name ?></td>

                                    <td><?= strftime(config_item('date_format'), strtotime($v_project->start_date)) ?></td>
                                    <td><?= strftime(config_item('date_format'), strtotime($v_project->end_date)) ?></td>

                                    <td><?php
                                        if (!empty($v_project->project_status)) {
                                            if ($v_project->project_status == 'completed') {
                                                $status = "<span class='label label-success'>" . lang($v_project->project_status) . "</span>";
                                            } elseif ($v_project->project_status == 'in_progress') {
                                                $status = "<span class='label label-primary'>" . lang($v_project->project_status) . "</span>";
                                            } elseif ($v_project->project_status == 'cancel') {
                                                $status = "<span class='label label-danger'>" . lang($v_project->project_status) . "</span>";
                                            } else {
                                                $status = "<span class='label label-warning'>" . lang($v_project->project_status) . "</span>";
                                            }
                                            echo $status;
                                        }
                                        ?>      </td>     
                                    <td>
                                        <?= btn_view('admin/project/project_details/' . $v_project->project_id) ?>
                                        <?= btn_edit('admin/project/index/' . $v_project->project_id) ?>
                                        <?= btn_delete('admin/project/delete_project/' . $v_project->project_id) ?>
                                        <div class="btn-group">
                                            <button class="btn btn-xs btn-success dropdown-toggle" data-toggle="dropdown">
                                                <?= lang('change_status') ?>
                                                <span class="caret"></span></button>
                                            <ul class="dropdown-menu">                                                                                               
                                                <li><a  href="<?= base_url() ?>admin/project/change_status/<?= $v_project->project_id . '/started' ?>"><?= lang('started') ?></a></li>                                                                                               
                                                <li><a  href="<?= base_url() ?>admin/project/change_status/<?= $v_project->project_id . '/in_progress' ?>"><?= lang('in_progress') ?></a></li>                                                                                               
                                                <li><a  href="<?= base_url() ?>admin/project/change_status/<?= $v_project->project_id . '/cancel' ?>"><?= lang('cancel') ?></a></li>                                                                                               
                                                <li><a  href="<?= base_url() ?>admin/project/change_status/<?= $v_project->project_id . '/completed' ?>"><?= lang('completed') ?></a></li>                                                                                                                                               
                                            </ul>                                      
                                        </div>
                                    </td>
                                </tr>
                                <?php
                            endforeach;
                        endif;
                        ?>
                    </tbody>
                </table>
            </div>
        </div>   
    </div>
</div>