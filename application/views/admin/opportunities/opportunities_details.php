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
                <h3 class="box-title"><?= lang('all_opportunities') ?></h3>
            </div>           
            <div class="box-body">
                <div class="scrollable">
                    <div class="slim-scroll" data-height="auto" data-disable-fade-out="true" data-distance="0" data-size="5px" data-color="#333333">
                        <ul class="nav" >
                            <?php
                            $opportunities_id = $this->uri->segment(4);
                            $all_opportunity = $this->db->get('tbl_opportunities')->result();
                            if (!empty($all_opportunity)):foreach ($all_opportunity as $v_opportunity):
                                    ?>
                                    <li class="<?= ($opportunities_id == $v_opportunity->opportunities_id ? 'active' : ' ') ?>" >
                                        <a href="<?= base_url() ?>admin/opportunities/opportunity_details/<?= $v_opportunity->opportunities_id ?>" data-toggle="tooltip" >
                                            <?= $v_opportunity->opportunity_name ?>
                                            <p><small class="text-muted">    
                                                    <?= lang('mettings') ?> 
                                                    <?php
                                                    $total_mettings = count($this->db->where('opportunities_id', $v_opportunity->opportunities_id)->get('tbl_mettings')->result());
                                                    if (!empty($total_mettings)) {
                                                        $metting = $total_mettings;
                                                    } else {
                                                        $metting = 0;
                                                    }
                                                    ?>
                                                    <strong style="margin-left: 5px" class="label label-danger">
                                                        <i class="fa fa-user"></i> <span style=""><?= $metting ?></span>
                                                    </strong>                                                
                                                </small>

                                                <small class="pull-right text-muted">                                               
                                                    <?= lang('call') ?> 
                                                    <?php
                                                    $total_calls = count($this->db->where('opportunities_id', $v_opportunity->opportunities_id)->get('tbl_calls')->result());
                                                    if (!empty($total_calls)) {
                                                        $calls = $total_calls;
                                                    } else {
                                                        $calls = 0;
                                                    }
                                                    ?>

                                                    <strong style="margin-left: 5px" class="pull-right label label-success">
                                                        <i class="fa fa-phone"> </i> <span > <?= $calls ?></span>
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
                <li class="<?= $active == 1 ? 'active' : '' ?>"><a href="#task_details" data-toggle="tab"><?= lang('opportunity_details') ?></a></li>
                <li class="<?= $active == 7 ? 'active' : '' ?>"><a href="#activities"  data-toggle="tab"><?= lang('activities') ?></a></li>
                <li class="<?= $active == 2 ? 'active' : '' ?>"><a href="#call"  data-toggle="tab"><?= lang('call') ?></a></li>
                <li class="<?= $active == 3 ? 'active' : '' ?>"><a href="#mettings"  data-toggle="tab"><?= lang('mettings') ?></a></li>
                <li class="<?= $active == 4 ? 'active' : '' ?>"><a href="#task_comments"  data-toggle="tab"><?= lang('comments') ?></a></li>
                <li class="<?= $active == 5 ? 'active' : '' ?>"><a href="#task_attachments"  data-toggle="tab"><?= lang('attachment') ?></a></li>                
                <li class="<?= $active == 6 ? 'active' : '' ?>"><a href="#task"  data-toggle="tab"><?= lang('tasks') ?></a></li>     
            </ul>
            <div class="tab-content no-padding">
                <!-- Task Details tab Starts -->
                <div class="tab-pane <?= $active == 1 ? 'active' : '' ?>" id="task_details" style="position: relative;">
                    <div class="box" style="border: none; padding-top: 15px;" data-collapsed="0">                         
                        <div class="box-body">
                            <div class="form-group col-sm-12">
                                <div class="col-sm-6">
                                    <label class="control-label"><strong><?= lang('opportunity_name') ?> :</strong> </label>
                                    <?php
                                    if (!empty($opportunity_details->opportunity_name)) {
                                        echo $opportunity_details->opportunity_name;
                                    }
                                    ?>                                    
                                </div>                               
                                <div class="col-sm-6">
                                    <label class="control-label"><strong><?= lang('stages') ?> :</strong></label>
                                    <?php
                                    if (!empty($opportunity_details->stages)) {
                                        echo $opportunity_details->stages;
                                    }
                                    ?>
                                </div>

                            </div>
                            <div class="form-group col-sm-12">
                                <div class="col-sm-6">
                                    <label class="control-label"><strong><?= lang('probability') ?> :</strong> </label>
                                    <?php
                                    if (!empty($opportunity_details->probability)) {
                                        echo $opportunity_details->probability . ' %';
                                    }
                                    ?>                                    
                                </div>                             
                                <div class="col-sm-6">
                                    <label class="control-label"><strong><?= lang('close_date') ?> :</strong></label>
                                    <?= strftime(config_item('date_format'), strtotime($opportunity_details->close_date)) ?>
                                </div>

                            </div>
                            <?php
                            $opportunities_state_info = $this->db->where('opportunities_state_reason_id', $opportunity_details->opportunities_state_reason_id)->get('tbl_opportunities_state_reason')->row();
                            if ($opportunities_state_info->opportunities_state == 'open') {
                                $label = 'primary';
                            } elseif ($opportunities_state_info->opportunities_state == 'won') {
                                $label = 'success';
                            } elseif ($opportunities_state_info->opportunities_state == 'suspended') {
                                $label = 'info';
                            } else {
                                $label = 'danger';
                            }
                            ?>
                            <div class="form-group col-sm-12">
                                <div class="col-sm-6">
                                    <label class="control-label"><strong><?= lang('state') ?> : </strong></label>
                                    <span class="label label-<?= $label ?>"><?= lang($opportunities_state_info->opportunities_state) ?></span>
                                </div>
                                <div class="col-sm-6">
                                    <label class="control-label"><strong><?= lang('expected_revenue') ?> : </strong></label>
                                    <strong>
                                        <?php
                                        $currency = $this->db->where('code', config_item('default_currency'))->get('tbl_currencies')->row();
                                        if (!empty($opportunity_details->expected_revenue)) {
                                            echo $currency->symbol . ' ' . number_format($opportunity_details->expected_revenue, 2);
                                        }
                                        ?>      
                                    </strong>
                                </div>
                            </div>
                            <div class="form-group col-sm-12">
                                <div class="col-sm-6">
                                    <label class="control-label"><strong><?= lang('new_link') ?> :</strong> </label>
                                    <?php
                                    if (!empty($opportunity_details->new_link)) {
                                        echo $opportunity_details->new_link;
                                    }
                                    ?>
                                </div>
                                <div class="col-sm-6">                                    
                                    <label class="control-label"><strong><?= lang('next_action') ?> :</strong></label>
                                    <?php
                                    if (!empty($opportunity_details->next_action)) {
                                        echo $opportunity_details->next_action;
                                    }
                                    ?>
                                </div>
                            </div>
                            <div class="form-group col-sm-12">
                                <div class="col-sm-6">
                                    <label class="control-label"><strong><?= lang('next_action_date') ?>: </strong></label>
                                    <?= strftime(config_item('date_format'), strtotime($opportunity_details->next_action_date)) ?>
                                </div>                                                    

                            </div>                                                      
                            <div class="form-group col-sm-12">
                                <label class="col-sm-2 control-label"><?= lang('short_note') ?></label>
                                <div class="col-sm-10">
                                    <blockquote style="font-size: 12px; height: 100px;"><?php
                                        if (!empty($opportunity_details->notes)) {
                                            echo $opportunity_details->notes;
                                        }
                                        ?></blockquote>
                                </div>
                            </div>
                            <?php if ($opportunity_details->user_id != '-') { ?>
                                <div class="form-group col-sm-12" id="border-none">
                                    <label class="col-sm-2 control-label"><?= lang('staff') ?></label>
                                    <div class="col-sm-10">
                                        <?php
                                        $assigned = unserialize($opportunity_details->user_id);
                                        ?>
                                        <table class="table table-bordered" style="background-color: #EEE;">
                                            <tbody>
                                                <?php
                                                if (!empty($assigned['user_id'])) :
                                                    foreach ($assigned['user_id'] as $v_assign) :
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
                <div class="tab-pane <?= $active == 7 ? 'active' : '' ?>" id="activities" style="position: relative;">
                    <div class="box" style="border: none; padding-top: 15px;" data-collapsed="0">                        
                        <div class="box-body chat" id="chat-box">
                            <div  id="activity">
                                <ul class="list-group no-radius   m-t-n-xxs list-group-lg no-border">
                                    <?php
                                    $activities_info = $this->db->where(array('module' => 'opportunity', 'module_field_id' => $opportunity_details->opportunities_id))->order_by('activity_date', 'desc')->get('tbl_activities')->result();
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

                <!-- Task Comments Panel Starts --->
                <div class="tab-pane <?= $active == 2 ? 'active' : '' ?>" id="call" style="position: relative;">
                    <div class="nav-tabs-custom ">
                        <!-- Tabs within a box -->
                        <ul class="nav nav-tabs">
                            <li class="<?= $sub_active == 1 ? 'active' : ''; ?>"><a href="#manage" data-toggle="tab"><?= lang('all_call') ?></a></li>
                            <li class="<?= $sub_active == 2 ? 'active' : ''; ?>"><a href="#create" data-toggle="tab"><?= lang('new_call') ?></a></li>                                                                     
                        </ul>
                        <div class="tab-content no-padding">
                            <!-- ************** general *************-->
                            <div class="tab-pane <?= $sub_active == 1 ? 'active' : ''; ?>" id="manage">

                                <div class="table-responsive">
                                    <table class="table table-striped DataTables " id="DataTables">
                                        <thead>
                                            <tr>                            
                                                <th><?= lang('date') ?></th>                            
                                                <th><?= lang('call_summary') ?></th>                            
                                                <th><?= lang('contact') ?></th>                            
                                                <th><?= lang('responsible') ?></th>                            
                                                <th class="col-options no-sort" ><?= lang('action') ?></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            $all_calls_info = $this->db->where('opportunities_id', $opportunity_details->opportunities_id)->get('tbl_calls')->result();

                                            if (!empty($all_calls_info)):
                                                foreach ($all_calls_info as $v_calls):
                                                    $client_info = $this->items_model->check_by(array('client_id' => $v_calls->client_id), 'tbl_client');
                                                    $user = $this->items_model->check_by(array('user_id' => $v_calls->client_id), 'tbl_users');
                                                    if ($client_info->client_status == 1) {
                                                        $status = 'Person';
                                                    } else {
                                                        $status = 'Company';
                                                    }
                                                    ?>
                                                    <tr>
                                                        <td><?= strftime(config_item('date_format'), strtotime($v_calls->date)) ?></td>
                                                        <td><?= $v_calls->call_summary ?></td>
                                                        <td><?= $client_info->name . ' (' . $status . ')'; ?></td>
                                                        <td><?= $user->username ?></td>     
                                                        <td>
                                                            <?= btn_edit('admin/opportunities/opportunity_details/' . $opportunity_details->opportunities_id . '/call/' . $v_calls->calls_id) ?>
                                                            <?= btn_delete('admin/opportunities/delete_opportunity_call/' . $opportunity_details->opportunities_id . '/' . $v_calls->calls_id) ?>
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
                            <div class="tab-pane <?= $sub_active == 2 ? 'active' : ''; ?>" id="create">
                                <form role="form" enctype="multipart/form-data" id="form" action="<?php echo base_url(); ?>admin/opportunities/saved_call/<?= $opportunity_details->opportunities_id ?>/<?php
                                if (!empty($call_info)) {
                                    echo $call_info->calls_id;
                                }
                                ?>" method="post" class="form-horizontal  ">
                                    <div class="form-group">
                                        <label class="col-lg-3 control-label"><?= lang('date') ?><span class="text-danger"> *</span></label>
                                        <div class="col-lg-5">
                                            <div class="input-group">
                                                <input type="text" required="" name="date"  class="form-control datepicker" value="<?php
                                                if (!empty($call_info->date)) {
                                                    echo $call_info->date;
                                                } else {
                                                    echo date('Y-m-d');
                                                }
                                                ?>" data-date-format="<?= config_item('date_picker_format'); ?>">
                                                <div class="input-group-addon">
                                                    <a href="#"><i class="entypo-calendar"></i></a>
                                                </div>
                                            </div> 
                                        </div>

                                    </div>               
                                    <!-- End discount Fields -->               
                                    <div class="form-group terms">
                                        <label class="col-lg-3 control-label"><?= lang('call_summary') ?><span class="text-danger"> *</span> </label>
                                        <div class="col-lg-5">
                                            <input type="text" required="" name="call_summary"  class="form-control" value="<?php
                                            if (!empty($call_info->call_summary)) {
                                                echo $call_info->call_summary;
                                            }
                                            ?>">                           
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="col-lg-3 control-label"><?= lang('contact') ?><span class="text-danger"> *</span></label>
                                        <div class="col-lg-5">
                                            <select name="client_id" class="form-control select_box" style="width: 100%" required="">                            
                                                <option value=""><?= lang('select_client') ?></option>
                                                <?php
                                                $all_client = $this->db->get('tbl_client')->result();
                                                if (!empty($all_client)) {
                                                    foreach ($all_client as $v_client) {
                                                        ?>
                                                        <option value="<?= $v_client->client_id ?>" <?php
                                                        if (!empty($call_info) && $call_info->client_id == $v_client->client_id) {
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
                                        <label class="col-lg-3 control-label"><?= lang('responsible') ?><span class="text-danger"> *</span></label>
                                        <div class="col-lg-5">
                                            <select name="user_id" class="form-control select_box" style="width: 100%"  required="">                            
                                                <option value=""><?= lang('admin_staff') ?></option>
                                                <?php
                                                $user_info = $this->db->where(array('role_id !=' => '2'))->get('tbl_users')->result();
                                                if (!empty($user_info)) {
                                                    foreach ($user_info as $key => $v_user) {
                                                        ?>
                                                        <option value="<?= $v_user->user_id ?>" <?php
                                                        if (!empty($call_info) && $call_info->user_id == $v_user->user_id) {
                                                            echo 'selected';
                                                        }
                                                        ?>><?= $v_user->username ?></option>
                                                                <?php
                                                            }
                                                        }
                                                        ?>
                                            </select>  
                                        </div>

                                    </div>                                     
                                    <div class="form-group">
                                        <label class="col-lg-3 control-label"></label> 
                                        <div class="col-lg-5">
                                            <button type="submit" class="btn btn-sm btn-primary"><?= lang('updates') ?></button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                </div> 
                <!-- Task Comments Panel Ends--->

                <!-- Task Attachment Panel Starts --->
                <div class="tab-pane <?= $active == 3 ? 'active' : '' ?>" id="mettings" style="position: relative;">

                    <div class="nav-tabs-custom ">
                        <!-- Tabs within a box -->
                        <ul class="nav nav-tabs">
                            <li class="<?= $sub_metting == 1 ? 'active' : ''; ?>"><a href="#all_metting" data-toggle="tab"><?= lang('all_metting') ?></a></li>
                            <li class="<?= $sub_metting == 2 ? 'active' : ''; ?>"><a href="#new_metting" data-toggle="tab"><?= lang('new_metting') ?></a></li>                                                                     
                        </ul>
                        <div class="tab-content no-padding">
                            <!-- ************** general *************-->
                            <div class="tab-pane <?= $sub_metting == 1 ? 'active' : ''; ?>" id="all_metting">

                                <div class="table-responsive">
                                    <table class="table table-striped DataTables " id="DataTables">
                                        <thead>
                                            <tr>                            
                                                <th><?= lang('subject') ?></th>                            
                                                <th><?= lang('start_date') ?></th>                            
                                                <th><?= lang('end_date') ?></th>                            
                                                <th><?= lang('responsible') ?></th>                            
                                                <th class="col-options no-sort" ><?= lang('action') ?></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            $all_mettings_info = $this->db->where('opportunities_id', $opportunity_details->opportunities_id)->get('tbl_mettings')->result();

                                            if (!empty($all_mettings_info)):
                                                foreach ($all_mettings_info as $v_mettings):
                                                    $user = $this->items_model->check_by(array('user_id' => $v_mettings->user_id), 'tbl_users');
                                                    ?>
                                                    <tr>
                                                        <td><?= $v_mettings->meeting_subject ?></td>                                                        
                                                        <td><?= strftime(config_item('date_format'), $v_mettings->start_date) . '<span style="color:#3c8dbc"> at </span>' . date('H:i A', strftime($v_mettings->start_date)) ?></td>
                                                        <td><?= strftime(config_item('date_format'), $v_mettings->end_date) . '<span style="color:#3c8dbc"> at </span>' . date('H:i A', strftime($v_mettings->end_date)) ?></td>
                                                        <td><?= $user->username ?></td>     
                                                        <td>
                                                            <?= btn_edit('admin/opportunities/opportunity_details/' . $opportunity_details->opportunities_id . '/metting/' . $v_mettings->mettings_id) ?>
                                                            <?= btn_delete('admin/opportunities/delete_opportunity_call/' . $opportunity_details->opportunities_id . '/' . $v_mettings->mettings_id) ?>
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
                            <div class="tab-pane <?= $sub_metting == 2 ? 'active' : ''; ?>" id="new_metting">
                                <form role="form" enctype="multipart/form-data" id="form" action="<?php echo base_url(); ?>admin/opportunities/saved_metting/<?= $opportunity_details->opportunities_id ?>/<?php
                                if (!empty($mettings_info)) {
                                    echo $mettings_info->mettings_id;
                                }
                                ?>" method="post" class="form-horizontal  ">
                                    <div class="form-group terms">
                                        <label class="col-lg-3 control-label"><?= lang('metting_subject') ?><span class="text-danger"> *</span> </label>
                                        <div class="col-lg-9">
                                            <input type="text" required="" name="meeting_subject"  class="form-control" value="<?php
                                            if (!empty($mettings_info->meeting_subject)) {
                                                echo $mettings_info->meeting_subject;
                                            }
                                            ?>">                           
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="col-lg-3 control-label"><?= lang('start_date') ?><span class="text-danger"> *</span></label>
                                        <div class="col-lg-4">
                                            <div class="input-group">
                                                <input type="text" required="" name="start_date"  class="form-control datepicker" value="<?php
                                                if (!empty($mettings_info->start_date)) {
                                                    echo date('Y-m-d', strftime($mettings_info->start_date));
                                                } else {
                                                    echo date('Y-m-d');
                                                }
                                                ?>" data-date-format="<?= config_item('date_picker_format'); ?>">
                                                <div class="input-group-addon">
                                                    <a href="#"><i class="entypo-calendar"></i></a>
                                                </div>
                                            </div> 
                                        </div>
                                        <label class="col-lg-2 control-label"><?= lang('start_time') ?><span class="text-danger"> *</span></label>
                                        <div class="col-lg-3">
                                            <div class="input-group">
                                                <input type="text" required="" name="start_time"  class="form-control timepicker" value="<?php
                                                if (!empty($mettings_info->start_date)) {
                                                    echo date('H:i A', strftime($mettings_info->start_date));
                                                }
                                                ?>">
                                                <div class="input-group-addon">
                                                    <a href="#"><i class="entypo-clock"></i></a>
                                                </div>
                                            </div> 
                                        </div>

                                    </div>               
                                    <div class="form-group">
                                        <label class="col-lg-3 control-label"><?= lang('end_date') ?><span class="text-danger"> *</span></label>
                                        <div class="col-lg-4">
                                            <div class="input-group">
                                                <input type="text" required="" name="end_date"  class="form-control datepicker" value="<?php
                                                if (!empty($mettings_info->end_date)) {
                                                    echo date('Y-m-d', strftime($mettings_info->end_date));
                                                } else {
                                                    echo date('Y-m-d');
                                                }
                                                ?>" data-date-format="<?= config_item('date_picker_format'); ?>">
                                                <div class="input-group-addon">
                                                    <a href="#"><i class="entypo-calendar"></i></a>
                                                </div>
                                            </div> 
                                        </div>
                                        <label class="col-lg-2 control-label"><?= lang('end_time') ?><span class="text-danger"> *</span></label>
                                        <div class="col-lg-3">
                                            <div class="input-group">
                                                <input type="text" required="" name="end_time"  class="form-control timepicker" value="<?php
                                                if (!empty($mettings_info->end_date)) {
                                                    echo date('H:i A', strftime($mettings_info->end_date));
                                                }
                                                ?>">
                                                <div class="input-group-addon">
                                                    <a href="#"><i class="entypo-clock"></i></a>
                                                </div>
                                            </div> 
                                        </div>

                                    </div>                                                   
                                    <div class="form-group">
                                        <label class="col-lg-3 control-label"><?= lang('attendess') ?><span class="text-danger"> *</span></label>
                                        <div class="col-lg-5">
                                            <select multiple="multiple" name="attendees[]" style="width: 100%" class="select_multi" required="">
                                                <option value=""><?= lang('select') . lang('attendess') ?></option>
                                                <?php
                                                $all_user_attendees = $this->db->get('tbl_users')->result();
                                                if (!empty($all_user_attendees)) {
                                                    foreach ($all_user_attendees as $v_user_attendees) {
                                                        ?>
                                                        <option value="<?= $v_user_attendees->user_id ?>" <?php
                                                        if (!empty($mettings_info->attendees)) {
                                                            $user_id = unserialize($mettings_info->attendees);
                                                            foreach ($user_id['attendees'] as $assding_id) {
                                                                echo $v_user_attendees->user_id == $assding_id ? 'selected' : '';
                                                            }
                                                        }
                                                        ?>><?= $v_user_attendees->username ?></option>
                                                                <?php
                                                            }
                                                        }
                                                        ?>
                                            </select>
                                        </div>

                                    </div> 
                                    <div class="form-group">
                                        <label class="col-lg-3 control-label"><?= lang('responsible') ?><span class="text-danger"> *</span></label>
                                        <div class="col-lg-5">
                                            <select name="user_id" class="form-control select_box" style="width: 100%"  required="">                            
                                                <option value=""><?= lang('admin_staff') ?></option>
                                                <?php
                                                $responsible_user_info = $this->db->where(array('role_id !=' => '2'))->get('tbl_users')->result();
                                                if (!empty($responsible_user_info)) {
                                                    foreach ($responsible_user_info as $v_responsible_user) {
                                                        ?>
                                                        <option value="<?= $v_responsible_user->user_id ?>" <?php
                                                        if (!empty($mettings_info) && $mettings_info->user_id == $v_responsible_user->user_id) {
                                                            echo 'selected';
                                                        }
                                                        ?>><?= $v_responsible_user->username ?></option>
                                                                <?php
                                                            }
                                                        }
                                                        ?>
                                            </select>  
                                        </div>

                                    </div>  
                                    <div class="form-group terms">
                                        <label class="col-lg-3 control-label"><?= lang('location') ?><span class="text-danger"> *</span> </label>
                                        <div class="col-lg-5">
                                            <input type="text" required="" name="location"  class="form-control" value="<?php
                                            if (!empty($mettings_info->location)) {
                                                echo $mettings_info->location;
                                            }
                                            ?>">                           
                                        </div>
                                    </div>
                                    <div class="form-group terms">
                                        <label class="col-lg-3 control-label"><?= lang('description') ?> </label>
                                        <div class="col-lg-8">
                                            <textarea name="description" class="form-control"><?php
                                                if (!empty($mettings_info)) {
                                                    echo $mettings_info->description;
                                                }
                                                ?></textarea>                        
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="col-lg-3 control-label"></label> 
                                        <div class="col-lg-5">
                                            <button type="submit" class="btn btn-sm btn-primary"><?= lang('updates') ?></button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Task Comments Panel Starts --->
                <div class="tab-pane <?= $active == 4 ? 'active' : '' ?>" id="task_comments" style="position: relative;">
                    <div class="box" style="border: none; padding-top: 15px;" data-collapsed="0">                        
                        <div class="box-body chat" id="chat-box">

                            <form id="form_validation" action="<?php echo base_url() ?>admin/opportunities/save_comments" method="post" class="form-horizontal">
                                <input type="hidden" name="opportunities_id" value="<?php
                                if (!empty($opportunity_details->opportunities_id)) {
                                    echo $opportunity_details->opportunities_id;
                                }
                                ?>" class="form-control"   >  
                                <div class="form-group"> 
                                    <div class="col-sm-12">
                                        <textarea class="form-control col-sm-12" placeholder="<?= $opportunity_details->opportunity_name . ' ' . lang('comments') ?>" name="comment" style="height: 70px;" required ></textarea>
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
                            $comment_details = $this->db->where('opportunities_id', $opportunity_details->opportunities_id)->get('tbl_task_comment')->result();
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
                                                    <?= btn_delete('admin/opportunities/delete_comments/' . $v_comment->opportunities_id . '/' . $v_comment->task_comment_id) ?>                                                
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
                <div class="tab-pane <?= $active == 5 ? 'active' : '' ?>" id="task_attachments" style="position: relative;">
                    <div class="box" style="border: none; padding-top: 15px;" data-collapsed="0">                        
                        <div class="panel-body">

                            <form action="<?= base_url() ?>admin/opportunities/save_attachment/<?php
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
                                                    <?php if (!empty($opportunity_files)):foreach ($opportunity_files as $v_files_image): ?>
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
                                <input type="hidden" name="opportunities_id" value="<?php
                                if (!empty($opportunity_details->opportunities_id)) {
                                    echo $opportunity_details->opportunities_id;
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

                            if (!empty($opportunity_files_info)) {
                                foreach ($opportunity_files_info as $key => $v_files_info) {
                                    ?>
                                    <div class="panel-group" id="accordion" style="margin:8px 5px" role="tablist" aria-multiselectable="true">
                                        <div class="box box-info" style="border-radius: 0px ">
                                            <div class="panel-heading"  role="tab" id="headingOne">
                                                <h4 class="panel-title">
                                                    <a data-toggle="collapse" data-parent="#accordion" href="#<?php echo $key ?>" aria-expanded="true" aria-controls="collapseOne">
                                                        <strong><?php echo $files_info[$key]->title; ?> </strong>
                                                        <small class="pull-right">
                                                            <?php if ($files_info[$key]->user_id == $this->session->userdata('user_id')) { ?>
                                                                <?= btn_delete('admin/opportunities/delete_files/' . $files_info[$key]->opportunities_id . '/' . $files_info[$key]->task_attachment_id) ?>                                                
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
                <!-- Start Tasks Management-->
                <div class="tab-pane <?= $active == 6 ? 'active' : '' ?>" id="task" style="position: relative;">
                    <div class="box" style="border: none; padding-top: 15px;" data-collapsed="0">      
                        <div class="nav-tabs-custom">
                            <!-- Tabs within a box -->
                            <ul class="nav nav-tabs">
                                <li class="<?= $task_active == 1 ? 'active' : ''; ?>"><a href="#manage_milestone" data-toggle="tab"><?= lang('task') ?></a></li>                                
                                <li class=""><a href="<?= base_url() ?>admin/tasks/all_task/opportunities/<?= $opportunity_details->opportunities_id ?>" ><?= lang('new_task') ?></a></li>                                
                            </ul>
                            <div class="tab-content no-padding">
                                <!-- ************** general *************-->
                                <div class="tab-pane <?= $task_active == 1 ? 'active' : ''; ?>" id="manage_milestone">
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
                                                $all_task_info = $this->db->where('opportunities_id', $opportunity_details->opportunities_id)->get('tbl_task')->result();

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
            </div>  
        </div>
    </div>         
</div>         