<?= message_box('success'); ?>
<?= message_box('error'); ?>
<div class="nav-tabs-custom">
    <!-- Tabs within a box -->
    <ul class="nav nav-tabs">
        <li class="<?= $active == 1 ? 'active' : ''; ?>"><a href="#manage" data-toggle="tab"><?= lang('all_opportunities') ?></a></li>
        <li class="<?= $active == 2 ? 'active' : ''; ?>"><a href="#create" data-toggle="tab"><?= lang('new_opportunities') ?></a></li>                                                                     
    </ul>
    <div class="tab-content no-padding">
        <!-- ************** general *************-->
        <div class="tab-pane <?= $active == 1 ? 'active' : ''; ?>" id="manage">

            <div class="table-responsive">
                <table class="table table-striped DataTables " id="DataTables">
                    <thead>
                        <tr>                            
                            <th><?= lang('opportunity_name') ?></th>                                                        
                            <th><?= lang('state') ?></th>                            
                            <th><?= lang('stages') ?></th>                            
                            <th><?= lang('expected_revenue') ?></th>                            
                            <th><?= lang('next_action') ?></th>                            
                            <th><?= lang('next_action_date') ?></th>                            
                            <th class="col-options no-sort" ><?= lang('action') ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $all_opportunity = $this->db->get('tbl_opportunities')->result();
                        if (!empty($all_opportunity)):foreach ($all_opportunity as $v_opportunity):
                                $opportunities_state_info = $this->db->where('opportunities_state_reason_id', $v_opportunity->opportunities_state_reason_id)->get('tbl_opportunities_state_reason')->row();
                                if ($opportunities_state_info->opportunities_state == 'open') {
                                    $label = 'primary';
                                } elseif ($opportunities_state_info->opportunities_state == 'won') {
                                    $label = 'success';
                                } elseif ($opportunities_state_info->opportunities_state == 'suspended') {
                                    $label = 'info';
                                } else {
                                    $label = 'danger';
                                }
                                $currency = $this->db->where('code', config_item('default_currency'))->get('tbl_currencies')->row();
                                ?>
                                <tr>
                                    <td>                                        
                                        <a class="text-info" href="<?= base_url() ?>admin/opportunities/opportunity_details/<?= $v_opportunity->opportunities_id ?>"><?= $v_opportunity->opportunity_name ?></a>
                                        <?php if (time() > strtotime($v_opportunity->close_date) AND $v_opportunity->probability < 100) { ?>
                                            <span class="label label-danger pull-right"><?= lang('overdate') ?></span>
                                        <?php } ?>

                                        <div class="progress progress-xs progress-striped active">
                                            <div class="progress-bar progress-bar-<?php echo ($v_opportunity->probability >= 100 ) ? 'success' : 'primary'; ?>" data-toggle="tooltip" data-original-title="<?= lang('probability') . ' ' . $v_opportunity->probability ?>%" style="width: <?= $v_opportunity->probability ?>%"></div>
                                        </div>
                                    </td>                                    
                                    <td><?= lang($v_opportunity->stages) ?></td>
                                    <td><span class="label label-<?= $label ?>"><?= lang($opportunities_state_info->opportunities_state) ?></span></td>
                                    <td><?php
                                        if (!empty($v_opportunity->expected_revenue)) {
                                            echo $currency->symbol . ' ' . number_format($v_opportunity->expected_revenue, 2);
                                        }
                                        ?></td>     
                                    <td><?= $v_opportunity->next_action ?></td>                                         
                                    <td><?= strftime(config_item('date_format'), strtotime($v_opportunity->next_action_date)) ?></td>
                                    <td>
                                        <?= btn_view('admin/opportunities/opportunity_details/' . $v_opportunity->opportunities_id) ?>
                                        <?= btn_edit('admin/opportunities/index/' . $v_opportunity->opportunities_id) ?>
                                        <?= btn_delete('admin/opportunities/delete_opportunity/' . $v_opportunity->opportunities_id) ?>
                                        <div class="btn-group">
                                            <button class="btn btn-xs btn-success dropdown-toggle" data-toggle="dropdown">
                                                <?= lang('change_state') ?>
                                                <span class="caret"></span></button>
                                            <ul class="dropdown-menu">
                                                <?php
                                                $all_opportunities_state = $this->db->get('tbl_opportunities_state_reason')->result();
                                                if (!empty($all_opportunities_state)) {
                                                    foreach ($all_opportunities_state as $v_opportunities_state) {
                                                        ?>
                                                        <li><a  href="<?= base_url() ?>admin/opportunities/change_state/<?= $v_opportunity->opportunities_id ?>/<?= $v_opportunities_state->opportunities_state_reason_id ?>"><?= lang($v_opportunities_state->opportunities_state) . ' (' . $v_opportunities_state->opportunities_state_reason . ')' ?></a></li>
                                                        <?php
                                                    }
                                                }
                                                ?>
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
            <form role="form" enctype="multipart/form-data" id="form" action="<?php echo base_url(); ?>admin/opportunities/saved_opportunity/<?php
            if (!empty($opportunity_info)) {
                echo $opportunity_info->opportunities_id;
            }
            ?>" method="post" class="form-horizontal  ">
                <div class="panel-body">
                    <div class="form-group">
                        <label class="col-lg-2 control-label"><?= lang('opportunity_name') ?> <span class="text-danger">*</span></label>
                        <div class="col-lg-4">
                            <input type="text" class="form-control" value="<?php
                            if (!empty($opportunity_info)) {
                                echo $opportunity_info->opportunity_name;
                            }
                            ?>" name="opportunity_name" required="">
                        </div>
                        <label class="col-lg-2 control-label"><?= lang('stages') ?> </label>
                        <div class="col-lg-4">
                            <select name="stages" class="form-control select_box" style="width: 100%;" required="">                            
                                <option value="new" <?= (!empty($opportunity_info) && $opportunity_info->stages == 'new' ? 'selected' : '') ?>><?= lang('new') ?></option>                                
                                <option value="qualification" <?= (!empty($opportunity_info) && $opportunity_info->stages == 'qualification' ? 'selected' : '') ?>><?= lang('qualification') ?></option>                                
                                <option value="proposition" <?= (!empty($opportunity_info) && $opportunity_info->stages == 'proposition' ? 'selected' : '') ?>><?= lang('proposition') ?></option>                                
                                <option value="won" <?= (!empty($opportunity_info) && $opportunity_info->stages == 'won' ? 'selected' : '') ?>><?= lang('won') ?></option>                                
                                <option value="lost" <?= (!empty($opportunity_info) && $opportunity_info->stages == 'lost' ? 'selected' : '') ?>><?= lang('lost') ?></option>                                
                                <option value="dead" <?= (!empty($opportunity_info) && $opportunity_info->stages == 'dead' ? 'selected' : '') ?>><?= lang('dead') ?></option>                                
                            </select>
                        </div>
                    </div>                 
                    <div class="form-group">
                        <label class="col-lg-2 control-label"><?= lang('probability') ?> %</label>
                        <div class="col-lg-4">
                            <input type="text" name="probability" value="" class="slider form-control" data-slider-min="0" data-slider-max="100" data-slider-step="1" data-slider-value="<?php
                            if (!empty($opportunity_info->probability)) {
                                echo $opportunity_info->probability;
                            } else {
                                echo '0';
                            }
                            ?>" data-slider-orientation="horizontal"  data-slider-tooltip="show"  data-slider-id="red"/>                            

                        </div>
                        <div class="form-group">
                            <label class="col-lg-2 control-label"><?= lang('close_date') ?></label>
                            <?php
                            if (!empty($opportunity_info)) {
                                $close_date = date('Y-m-d', strtotime($opportunity_info->close_date));
                                $next_action_date = date('Y-m-d', strtotime($opportunity_info->next_action_date));
                            } else {
                                $close_date = date('Y-m-d');
                                $next_action_date = date('Y-m-d');
                            }
                            ?>
                            <div class="col-lg-4">
                                <div class="input-group">
                                    <input class="form-control datepicker" type="text" value="<?= $close_date; ?>" name="close_date" data-date-format="<?= config_item('date_picker_format'); ?>" >
                                    <div class="input-group-addon">
                                        <a href="#"><i class="entypo-calendar"></i></a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>    
                    <div class="form-group" id="border-none">
                        <label for="field-1" class="col-sm-2 control-label"><?= lang('who_responsible') ?> <span class="required">*</span></label>
                        <div class="col-sm-4"> 
                            <select multiple="multiple" name="user_id[]" style="width: 100%" class="select_multi" required="">                                              
                                <option value=""><?= lang('select_user') ?></option>
                                <optgroup label="<?= lang('admin_staff') ?>"> 
                                    <?php
                                    if (!empty($user_info)) {
                                        foreach ($user_info as $key => $v_user) {
                                            ?>
                                            <option value="<?= $v_user->user_id ?>" <?php
                                            if (!empty($opportunity_info->user_id)) {
                                                $user_id = unserialize($opportunity_info->user_id);
                                                foreach ($user_id['user_id'] as $assding_id) {
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
                        <label for="field-1" class="col-sm-2 control-label"><?= lang('current_state') ?> <span class="required">*</span></label>
                        <div class="col-sm-4"> 
                            <select  name="opportunities_state_reason_id" style="width: 100%" class="select_box" required="">                                                                              
                                <?php
                                $opportunities_state_reason = $this->db->get('tbl_opportunities_state_reason')->result();
                                if (!empty($opportunities_state_reason)) {
                                    foreach ($opportunities_state_reason as $opportunities_state) {
                                        ?>
                                        <option value="<?= $opportunities_state->opportunities_state_reason_id ?>" <?php
                                        if (!empty($opportunity_info->opportunities_state_reason_id)) {
                                            echo $opportunities_state->opportunities_state_reason_id == $opportunity_info->opportunities_state_reason_id ? 'selected' : '';
                                        }
                                        ?>><?= lang($opportunities_state->opportunities_state) . ' (' . $opportunities_state->opportunities_state_reason . ')' ?></option>
                                                <?php
                                            }
                                        }
                                        ?>	                                
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-lg-2 control-label"><?= lang('expected_revenue') ?></label>
                        <div class="col-lg-4">

                            <input type="number" class="form-control" value="<?php
                            if (!empty($opportunity_info)) {
                                echo $opportunity_info->expected_revenue;
                            }
                            ?>" name="expected_revenue" >
                        </div>                
                        <label class="col-lg-2 control-label"><?= lang('new_link') ?></label>
                        <div class="col-lg-4">
                            <input type="text" class="form-control" value="<?php
                            if (!empty($opportunity_info)) {
                                echo $opportunity_info->new_link;
                            }
                            ?>" name="new_link" />
                        </div>

                    </div>               
                    <!-- End discount Fields -->               
                    <div class="form-group terms">

                        <label class="col-lg-2 control-label"><?= lang('next_action') ?> </label>
                        <div class="col-lg-4">
                            <input type="text" class="form-control" value="<?php
                            if (!empty($opportunity_info)) {
                                echo $opportunity_info->next_action;
                            }
                            ?>" name="next_action" >
                        </div>
                        <label class="col-lg-2 control-label"><?= lang('next_action_date') ?></label>
                        <div class="col-lg-4">
                            <div class="input-group">
                                <input class="form-control datepicker" type="text" value="<?= $next_action_date; ?>" name="next_action_date" data-date-format="<?= config_item('date_picker_format'); ?>" >
                                <div class="input-group-addon">
                                    <a href="#"><i class="entypo-calendar"></i></a>
                                </div>
                            </div>
                        </div>                    


                    </div>
                    <div class="form-group">                        
                        <label class="col-lg-2 control-label"><?= lang('short_note') ?> </label>
                        <div class="col-lg-8">
                            <textarea name="notes" class="form-control"><?php
                                if (!empty($opportunity_info)) {
                                    echo $opportunity_info->notes;
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
            </form>
        </div>
    </div>
</div>