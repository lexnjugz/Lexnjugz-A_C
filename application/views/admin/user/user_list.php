<?php include_once 'asset/admin-ajax.php'; ?>

<link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>asset/css/kendo.default.min.css" />
<link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>asset/css/kendo.common.min.css" />
<script type="text/javascript" src="<?php echo base_url(); ?>asset/js/kendo.all.min.js"></script>
<?= message_box('success'); ?>
<?= message_box('error'); ?>
<div class="nav-tabs-custom">
    <!-- Tabs within a box -->
    <ul class="nav nav-tabs">
        <li class="<?= $active == 1 ? 'active' : ''; ?>"><a href="#manage" data-toggle="tab"><?= lang('all_users') ?></a></li>
        <li class="<?= $active == 2 ? 'active' : ''; ?>"><a href="#new" data-toggle="tab"><?= lang('new_user') ?></a></li>                                                                     
    </ul>
    <div class="tab-content no-padding">
        <!-- ************** general *************-->
        <div class="tab-pane <?= $active == 1 ? 'active' : ''; ?>" id="manage">
            <table class="table table-striped" id="DataTables">
                <thead>
                    <tr>
                        <th class="col-sm-1"><?= lang('photo') ?></th>
                        <th><?= lang('name') ?></th>
                        <th class="col-sm-2"><?= lang('username') ?></th>
                        <th class="col-sm-1"><?= lang('status') ?></th>
                        <th class="col-sm-1"><?= lang('user_type') ?></th>
                        <th class="col-sm-2"><?= lang('action') ?></th>

                    </tr>
                </thead>
                <tbody>                    
                    <?php
                    if (!empty($all_user_info)): foreach ($all_user_info as $v_user) :
                            $account_info = $this->user_model->check_by(array('user_id' => $v_user->user_id), 'tbl_account_details');
                            if (!empty($account_info)) {
                                ?>

                                <tr>
                                    <td><img style="width: 36px;margin-right: 10px;" src="<?= base_url() ?><?= $account_info->avatar ?>" class="img-circle"></td>
                                    <td><?php echo $account_info->fullname ?></td>
                                    <td><?= ucfirst($v_user->username) ?></td>                                
                                    <td>
                                        <?php if ($v_user->activated == 1): ?>
                                            <a data-toggle="tooltip" data-placement="top" title="Click to Deactive" href="<?php echo base_url() ?>admin/user/change_status/0/<?php echo $v_user->user_id; ?>"><?= lang('active') ?></a>                                            
                                        <?php else: ?>                                        
                                            <a data-toggle="tooltip" data-placement="top" title="Click to Active "  href="<?php echo base_url() ?>admin/user/change_status/1/<?php echo $v_user->user_id; ?>"><?= lang('deactive') ?></a>
                                        <?php endif; ?>                                        
                                        <?php
                                        if ($v_user->banned == 1) {
                                            ?>
                                            <span class="label label-danger" data-toggle='tooltip' data-placement='top' title="<?= $v_user->ban_reason ?>"><?= lang('banned') ?></span>
                                        <?php }
                                        ?></td>                                
                                    <td><?php
                                        if ($v_user->role_id == 1) {
                                            echo 'Admin';
                                        } elseif ($v_user->role_id == 3) {
                                            echo 'Staff';
                                        } else {
                                            echo 'Client';
                                        }
                                        ?></td>
                                    <td><?php if ($v_user->user_id != 1): ?>
                                            <?php if ($v_user->banned == 1): ?>                                            
                                                <a data-toggle="tooltip" data-placement="top" class="btn btn-success btn-xs" title="Click to <?= lang('unbanned') ?> "  href="<?php echo base_url() ?>admin/user/set_banned/0/<?php echo $v_user->user_id; ?>"><span class="fa fa-check"></span></a>
                                            <?php else: ?>                                              
                                                <?php echo btn_banned_modal('admin/user/change_banned/' . $v_user->user_id); ?>                                                
                                            <?php endif; ?>                       

                                            <?php echo btn_view('admin/user/user_details/' . $v_user->user_id); ?>
                                            <?php echo btn_edit('admin/user/user_list/edit_user/' . $v_user->user_id); ?>
                                            <?php echo btn_delete('admin/user/delete_user/' . $v_user->user_id); ?>
                                        <?php endif; ?>
                                    </td>


                                </tr>
                                <?php
                            };
                        endforeach;
                        ?>
                    <?php else : ?>
                    <td colspan="3">
                        <strong>There is no data to display</strong>
                    </td>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
        <div class="tab-pane <?= $active == 2 ? 'active' : ''; ?>" id="new">
            <form role="form" id="userform" enctype="multipart/form-data" action="<?php echo base_url(); ?>admin/user/save_user" method="post" class="form-horizontal form-groups-bordered">
                <div class="row">
                    <div class="col-sm-6">
                        <?php
                        if (!empty($login_info->user_id)) {
                            $profile_info = $this->user_model->check_by(array('user_id' => $login_info->user_id), 'tbl_account_details');
                        }
                        ?>
                        <input type="hidden" id="username_flag"  value="">
                        <input type="hidden" name="user_id"  value="<?php
                        if (!empty($login_info)) {
                            echo $login_info->user_id;
                        }
                        ?>">
                        <input type="hidden" name="account_details_id"  value="<?php
                        if (!empty($profile_info)) {
                            echo $profile_info->account_details_id;
                        }
                        ?>">

                        <div class="form-group">
                            <label class="col-sm-4 control-label"><strong><?= lang('full_name') ?> </strong><span class="text-danger">*</span></label>
                            <div class="col-sm-8">
                                <input type="text" class="input-sm form-control" value="<?php
                                if (!empty($profile_info)) {
                                    echo $profile_info->fullname;
                                }
                                ?>" placeholder="<?= lang('eg') ?> <?= lang('user_placeholder_name') ?>" name="fullname" required>
                            </div>
                        </div>
                        <?php if (empty($login_info->user_id)) { ?>
                            <div class = "form-group">
                                <label class = "col-sm-4 control-label"><strong> <?= lang('username'); ?></strong><span class="text-danger">*</span></label>
                                <div class="col-sm-8">
                                    <input type="text" name="username" placeholder="<?= lang('eg') ?> <?= lang('user_placeholder_username') ?>" value="<?php
                                    if (!empty($login_info)) {
                                        echo $login_info->username;
                                    }
                                    ?>" class="input-sm form-control" required>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-sm-4 control-label"><strong><?= lang('password') ?> </strong><span class="text-danger">*</span></label>
                                <div class="col-sm-8">
                                    <input type="password" value="" id="password" placeholder="<?= lang('password') ?>" name="password"  class="input-sm form-control">
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-4 control-label"><strong><?= lang('confirm_password') ?> </strong><span class="text-danger">*</span></label>
                                <div class="col-sm-8">
                                    <input type="password" placeholder="<?= lang('confirm_password') ?>"  name="confirm_password"  class="input-sm form-control">
                                </div>
                            </div>
                        <?php } else { ?>
                            <input type="hidden" name="username" placeholder="<?= lang('eg') ?> <?= lang('user_placeholder_username') ?>" value="<?php
                            if (!empty($login_info)) {
                                echo $login_info->username;
                            }
                            ?>" class="input-sm form-control" required>
                               <?php } ?>
                        <div class="form-group">
                            <label class="col-sm-4 control-label"><strong><?= lang('email') ?> </strong><span class="text-danger">*</span></label>
                            <div class="col-sm-8">
                                <input type="email" placeholder="<?= lang('eg') ?> <?= lang('user_placeholder_email') ?>" name="email" value="<?php
                                if (!empty($login_info)) {
                                    echo $login_info->email;
                                }
                                ?>" class="input-sm form-control" required>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-4 control-label"><strong><?= lang('company') ?> </strong></label>
                            <div class="col-sm-8">
                                <select class="form-control select_box" style="width: 100%"  name="company" >
                                    <optgroup label="<?= lang('default_company') ?>">
                                        <option value="-"><?= $this->config->item('company_name') ?></option>
                                    </optgroup>
                                    <optgroup label="<?= lang('other_companies') ?>">
                                        <?php
                                        if (!empty($all_client_info)) {
                                            foreach ($all_client_info as $v_client) {
                                                if ($v_client->client_status == 1) {
                                                    $client_status = '(Person)';
                                                } else {
                                                    $client_status = '(Company)';
                                                }
                                                ?>
                                                <option value="<?= $v_client->client_id ?>"
                                                <?php
                                                if (!empty($profile_info)) {
                                                    if ($profile_info->company == $v_client->client_id) {
                                                        echo 'selected';
                                                    }
                                                }
                                                ?>
                                                        ><?= $v_client->name . ' ' . $client_status ?></option>
                                                        <?php
                                                    }
                                                }
                                                ?>
                                    </optgroup>
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-4 control-label"><strong><?= lang('locale') ?></strong></label>
                            <div class="col-lg-8">
                                <select class="  form-control select_box" style="width: 100%" name="locale">

                                    <?php
                                    $locales = $this->db->get('tbl_locales')->result();
                                    foreach ($locales as $loc) :
                                        ?>
                                        <option lang="<?= $loc->code ?>" value="<?= $loc->locale ?>" <?php
                                        if (!empty($profile_info)) {
                                            if ($profile_info->locale == $loc->locale) {
                                                echo 'selected';
                                            }
                                        } else {
                                            echo ($this->config->item('locale') == $loc->locale ? 'selected="selected"' : '');
                                        }
                                        ?>><?= $loc->name ?></option>
                                            <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-4 control-label"><strong><?= lang('language') ?></strong></label>
                            <div class="col-sm-8">
                                <select name="language" class="form-control select_box" style="width: 100%">
                                    <?php foreach ($languages as $lang) : ?>
                                        <option value="<?= $lang->name ?>"<?php
                                        if (!empty($profile_info)) {
                                            if ($profile_info->language == $lang->name) {
                                                echo 'selected';
                                            }
                                        } else {
                                            echo ($this->config->item('language') == $lang->name ? ' selected="selected"' : '');
                                        }
                                        ?>><?= ucfirst($lang->name) ?></option>
                                            <?php endforeach; ?>
                                </select>        
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-4 control-label"><strong><?= lang('phone') ?> </strong></label>
                            <div class="col-sm-8">
                                <input type="text" class="input-sm form-control" value="<?php
                                if (!empty($profile_info)) {
                                    echo $profile_info->phone;
                                }
                                ?>" name="phone" placeholder="<?= lang('eg') ?> <?= lang('user_placeholder_phone') ?>">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-4 control-label"><strong><?= lang('mobile') ?> </strong></label>
                            <div class="col-sm-8">
                                <input type="text" class="input-sm form-control" value="<?php
                                if (!empty($profile_info)) {
                                    echo $profile_info->mobile;
                                }
                                ?>" name="mobile" placeholder="<?= lang('eg') ?> <?= lang('user_placeholder_mobile') ?>">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-4 control-label"><strong><?= lang('skype_id') ?> </strong></label>
                            <div class="col-sm-8">
                                <input type="text" class="input-sm form-control" value="<?php
                                if (!empty($profile_info)) {
                                    echo $profile_info->skype;
                                }
                                ?>" name="skype" placeholder="<?= lang('eg') ?> <?= lang('user_placeholder_skype') ?>">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-lg-4 control-label"><strong><?= lang('profile_photo') ?></strong><span class="text-danger">*</span></label>
                            <div class="col-lg-8" >                                                        
                                <div class="fileinput fileinput-new" data-provides="fileinput">
                                    <div class="fileinput-new thumbnail" style="width: 210px;" >
                                        <?php
                                        if (!empty($profile_info)) :
                                            ?>
                                            <img src="<?php echo base_url() . $profile_info->avatar; ?>" >  
                                        <?php else: ?>
                                            <img src="http://placehold.it/350x260" alt="Please Connect Your Internet">     
                                        <?php endif; ?>                                 
                                    </div>
                                    <div class="fileinput-preview fileinput-exists thumbnail" style="width: 210px;" ></div>
                                    <div>
                                        <span class="btn btn-default btn-file">
                                            <span class="fileinput-new">
                                                <input type="file" name="avatar"  value="upload"  data-buttonText="<?= lang('choose_file') ?>" id="myImg" />
                                                <span class="fileinput-exists"><?= lang('change') ?></span>    
                                            </span>
                                            <a href="#" class="btn btn-default fileinput-exists" data-dismiss="fileinput"><?= lang('remove') ?></a>

                                    </div>

                                    <div id="valid_msg" style="color: #e11221"></div>

                                </div>    
                            </div>
                        </div>    
                        <div class="form-group">
                            <label for="field-1" class="col-sm-4 control-label"><strong><?= lang('user_type') ?></strong><span class="required">*</span></label>
                            <div class="col-sm-8">
                                <select id="user_type" name="role_id" class="form-control" required>
                                    <option value="" ><?= lang('select_user_type') ?></option>
                                    <option <?php
                                    if (!empty($login_info)) {
                                        echo $login_info->role_id == 1 ? 'selected' : '';
                                    }
                                    ?> value="1"><?= lang('admin') ?></option>
                                    <option <?php
                                    if (!empty($login_info)) {
                                        echo $login_info->role_id == 3 ? 'selected' : '';
                                    }
                                    ?> value="3"><?= lang('staff') ?></option>
                                    <option <?php
                                    if (!empty($login_info)) {
                                        echo $login_info->role_id == 2 ? 'selected' : '';
                                    }
                                    ?> value="2"><?= lang('client') ?></option>
                                </select>
                            </div>                            
                        </div> 
                        <div class="form-group" id="department">
                            <label class="col-sm-4 control-label"><strong><?= lang('departments') ?> </strong><span class="text-danger">*</span></label>
                            <div class="col-sm-8">
                                <select class="form-control select_box" style="width: 100%"  name="departments_id" >                                                                        
                                    <option value=""><?= lang('select_department'); ?></option>
                                    <?php
                                    $all_dept_info = $this->db->get('tbl_departments')->result();
                                    if (!empty($all_dept_info)) {
                                        foreach ($all_dept_info as $v_dept) {
                                            ?>
                                            <option value="<?= $v_dept->departments_id ?>" <?php
                                            if (!empty($profile_info)) {
                                                if ($profile_info->departments_id == $v_dept->departments_id) {
                                                    echo 'selected';
                                                }
                                            }
                                            ?>><?= $v_dept->deptname; ?></option>
                                                    <?php
                                                }
                                            }
                                            ?>                                    
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-4"></label>
                            <div class="col-sm-8">
                                <button type="submit" id="sbtn" class="btn btn-primary"><?php echo!empty($user_id) ? lang('update_user') : lang('create_user') ?></button>
                            </div>
                        </div>

                    </div>
                    <div class="col-sm-6">

                        <div id="roll" class="list-group">
                            <a href="#" class="list-group-item disabled">
                                User Permission Level
                            </a>
                            <a href="#" class="list-group-item">
                                <div class="k-header">
                                    <div class="box-col">
                                        <div id="treeview"></div>
                                    </div>
                                </div>
                            </a>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
<script type="text/javascript">
            $(document).ready(function(){
    $('#department').hide();
            $("#roll").hide();
    });</script>
<script>
            $("#treeview").kendoTreeView({
    checkboxes: {
    checkChildren: true,
            template: "<input type='checkbox' #= item.check# name='menu[]' value='#= item.value #'  />"

    },
            check: onCheck,
            dataSource: [
<?php foreach ($result as $parent => $v_parent): ?>
    <?php if (is_array($v_parent)): ?>
        <?php foreach ($v_parent as $parent_id => $v_child): ?>
                        {
                        id: "", text: "<?php echo lang($parent); ?>", value: "<?php
            if (!empty($parent_id)) {
                echo $parent_id;
            }
            ?>", expanded: false, items: [
            <?php foreach ($v_child as $child => $v_sub_child) : ?>
                <?php if (is_array($v_sub_child)): ?>
                    <?php foreach ($v_sub_child as $sub_chld => $v_sub_chld): ?>
                                    {
                                    id: "", text: "<?php echo lang($child); ?>", value: "<?php
                        if (!empty($sub_chld)) {
                            echo $sub_chld;
                        }
                        ?>", expanded: false, items: [
                        <?php foreach ($v_sub_chld as $sub_chld_name => $sub_chld_id): ?>
                                        {
                                        id: "", text: "<?php echo lang($sub_chld_name); ?>",<?php
                            if (!empty($roll[$sub_chld_id])) {
                                echo $roll[$sub_chld_id] ? 'check: "checked",' : '';
                            }
                            ?> value: "<?php
                            if (!empty($sub_chld_id)) {
                                echo $sub_chld_id;
                            }
                            ?>",
                                        },
                        <?php endforeach; ?>
                                    ]
                                    },
                    <?php endforeach; ?>
                <?php else: ?>
                                {
                                id: "", text: "<?php echo lang($child); ?>", <?php
                    if (!is_array($v_sub_child)) {
                        if (!empty($roll[$v_sub_child])) {
                            echo $roll[$v_sub_child] ? 'check: "checked",' : '';
                        }
                    }
                    ?> value: "<?php
                    if (!empty($v_sub_child)) {
                        echo $v_sub_child;
                    }
                    ?>",
                                },
                <?php endif; ?>
            <?php endforeach; ?>
                        ]
                        },
        <?php endforeach; ?>
    <?php else: ?>
                    { <?php if ($parent == lang('dashboard')) {
            ?>
                        id: "", text: "<?php echo lang($parent) ?>", <?php echo 'check: "checked",';
            ?>  value: "<?php
            if (!is_array($v_parent)) {
                echo $v_parent;
            }
            ?>"
            <?php
        } else {
            ?>
                        id: "", text: "<?php echo lang($parent); ?>", <?php
            if (!is_array($v_parent)) {
                if (!empty($roll[$v_parent])) {
                    echo $roll[$v_parent] ? 'check: "checked",' : '';
                }
            }
            ?> value: "<?php
            if (!is_array($v_parent)) {
                echo $v_parent;
            }
            ?>"
        <?php }
        ?>
                    },
    <?php endif; ?>
<?php endforeach; ?>
            ]
    });
            // show checked node IDs on datasource change
                    function onCheck() {
                    var checkedNodes = [],
                            treeView = $("#treeview").data("kendoTreeView"),
                            message;
                            checkedNodeIds(treeView.dataSource.view(), checkedNodes);
                            $("#result").html(message);
                    }
</script>


<script type="text/javascript">

            $(function () {
            $("#treeview .k-checkbox input").eq(0).hide();
                    $('form').submit(function () {
            $('#treeview :checkbox').each(function () {
            if (this.indeterminate) {
            this.checked = true;
            }
            });
            })
            })
</script>

<script>
                    $(document).ready(function(){

            var user_flag = document.getElementById("user_type").value;
                    if (user_flag == '3')
            {
            $("#roll").show();
            }
            else
            {
            $("#roll").hide();
            }

            // on change user type select action
            $('#user_type').on('change', function() {

            if (this.value == '3')
                    //.....................^.......
                    {
                    $("#roll").show();
                            $("#department").show();
                    }
            else
            {
            $("#roll").hide();
                    $("#department").hide();
            }
            });
            });</script>

<script>

                    $().ready(function() {

            // validate signup form on keyup and submit
            $("#userform").validate({
            rules: {
            user_name: "required",
                    name: "required",
                    departments_id: "required",
                    user_name: {
                    required: true,
                            minlength: 4
                    },
                    password: {
                    required: true,
                            minlength: 6
                    },
                    confirm_password: {
                    required: true,
                            equalTo: "#password",
                    },
                    email: {
                    required: true,
                            email: true
                    }

            },
                    highlight: function(element) {
                    $(element).closest('.form-group').addClass('has-error');
                    },
                    unhighlight: function(element) {
                    $(element).closest('.form-group').removeClass('has-error');
                    },
                    errorElement: 'span',
                    errorClass: 'help-block',
                    errorPlacement: function(error, element) {
                    if (element.parent('.input-group').length) {
                    error.insertAfter(element.parent());
                    } else {
                    error.insertAfter(element);
                    }
                    },
                    messages: {
                    user_name: {
                    required: "Please enter a username",
                            minlength: "Your username must consist of at least 4 characters"
                    },
                            password: {
                            required: "Please provide a password",
                                    minlength: "Your password must be at least 6 characters long"
                            },
                            email: "Please enter a valid email address",
                            name: "Please enter your Name"

                    }

            });
            });
</script>