<header class="main-header">
    <?php
    $user_id = $this->session->userdata('user_id');
    $profile_info = $this->db->where('user_id', $user_id)->get('tbl_account_details')->row();
    $user_info = $this->db->where('user_id', $user_id)->get('tbl_users')->row();
    ?>
    <?php $display = config_item('logo_or_icon'); ?>

    <a href="<?= base_url() ?>" class="logo">
        <?php $display = config_item('logo_or_icon'); ?>
        <!-- mini logo for sidebar mini 50x50 pixels -->
        <?php if ($display == 'logo' || $display == 'logo_title') { ?>
            <span class="logo-mini"><img style="width: 48px;height: 48px;border-radius: 50px" src="<?= base_url() . config_item('company_logo') ?>" class="m-r-sm"></span>
            <!-- logo for regular state and mobile devices -->
            <span class="logo-lg">
                <img style="width: 100px;height: 30px;" src="<?= base_url() . config_item('company_logo') ?>" >
            </span>
        <?php }
        ?>
    </a>
    <!-- Header Navbar: style can be found in header.less -->
    <nav class="navbar navbar-static-top" role="navigation">
        <!-- Sidebar toggle button-->
        <a href="#" class="sidebar-toggle" data-toggle="offcanvas" role="button">
            <span class="sr-only">Toggle navigation</span>
        </a>        
        <div class="pull-left hidden-xs">
            <ul class="nav">
                <li style="">
                    <a href="" class="text-center" style="vertical-align: middle;color: #FFFFFF;font-size: 20px;"><?php
                        if ($display == 'logo_title' || $display == 'icon_title') {
                            if (config_item('website_name') == '') {
                                echo config_item('company_name');
                            } else {
                                echo config_item('website_name');
                            }
                        }
                        ?></a>        
                </li>
            </ul>
        </div>
        <!-- Navbar Right Menu -->
        <div class="navbar-custom-menu">
            <ul class="nav navbar-nav">
                <?php if (config_item('enable_languages') == 'TRUE') { ?>
                    <li class="dropdown">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                            <i class="fa fa-flag"></i> <?= lang('languages') ?>                            
                        </a>
                        <ul class="dropdown-menu">   

                            <?php
                            $languages = $this->db->order_by('name', 'ASC')->get('tbl_languages')->result();

                            foreach ($languages as $lang) : if ($lang->active == 1) :
                                    ?>
                                    <li>
                                        <a href="<?= base_url() ?>admin/dashboard/set_language/<?= $lang->name ?>" title="<?= ucwords(str_replace("_", " ", $lang->name)) ?>">
                                            <img src="<?= base_url() ?>asset/images/flags/<?= $lang->icon ?>.gif" alt="<?= ucwords(str_replace("_", " ", $lang->name)) ?>"  /> <?= ucwords(str_replace("_", " ", $lang->name)) ?>
                                        </a>
                                    </li>
                                    <?php
                                endif;
                            endforeach;
                            ?>

                        </ul>
                    </li>
                <?php } ?>
                <!-- Messages: style can be found in dropdown.less-->
                <li class="dropdown messages-menu">
                    <?php
                    // check notififation status by where
                    $where = array('user_id' => $this->session->userdata('user_id'), 'to' => $this->session->userdata('email'), 'notify_me' => '1', 'view_status' => '2');
                    // check email notification status
                    $this->admin_model->_table_name = 'tbl_inbox';
                    $this->admin_model->_order_by = 'inbox_id';
                    $total_email = count($this->admin_model->get_by($where, FALSE));
                    $email_info = $this->admin_model->get_by($where, FALSE);
                    ?>
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                        <i class="fa fa-envelope-o"></i>
                        <span class="label label-success"><?php
                            if (!empty($total_email)) {
                                echo $total_email;
                            } else {
                                echo '0';
                            }
                            ?></span>
                    </a>
                    <ul class="dropdown-menu">
                        <li class="header" style="padding: 11px"><?= lang('you_have') ?> <?php
                            if (!empty($total_email)) {
                                echo $total_email;
                            } else {
                                echo '0';
                            }
                            ?> <?= lang('messages') ?></li>
                        <li>
                            <!-- inner menu: contains the actual data -->
                            <ul class="menu">
                                <?php if (!empty($email_info)):foreach ($email_info as $v_email): ?>
                                        <li><!-- start message -->
                                            <a href="<?php echo base_url() ?>admin/mailbox/index/read_inbox_mail/<?php echo $v_email->inbox_id ?>">                                                
                                                <h4 style="margin: 0px">
                                                    <span style="font-size: 12px">
                                                        <?= (strlen($v_email->subject) > 25) ? substr($v_email->subject, 0, 25) . '...' : $v_email->subject; ?></span>
                                                    <small><i class="fa fa-clock-o"></i> 
                                                        <?php
                                                        //$oldTime = date('h:i:s', strtotime($v_inbox_msg->send_time));
                                                        // Past time as MySQL DATETIME value
                                                        $oldtime = date('Y-m-d H:i:s', strtotime($v_email->message_time));

                                                        // Current time as MySQL DATETIME value
                                                        $csqltime = date('Y-m-d H:i:s');
                                                        // Current time as Unix timestamp
                                                        $ptime = strtotime($oldtime);
                                                        $ctime = strtotime($csqltime);

                                                        //Now calc the difference between the two
                                                        $timeDiff = floor(abs($ctime - $ptime) / 60);

                                                        //Now we need find out whether or not the time difference needs to be in
                                                        //minutes, hours, or days
                                                        if ($timeDiff < 2) {
                                                            $timeDiff = "Just now";
                                                        } elseif ($timeDiff > 2 && $timeDiff < 60) {
                                                            $timeDiff = floor(abs($timeDiff)) . " minutes ago";
                                                        } elseif ($timeDiff > 60 && $timeDiff < 120) {
                                                            $timeDiff = floor(abs($timeDiff / 60)) . " hour ago";
                                                        } elseif ($timeDiff < 1440) {
                                                            $timeDiff = floor(abs($timeDiff / 60)) . " hours ago";
                                                        } elseif ($timeDiff > 1440 && $timeDiff < 2880) {
                                                            $timeDiff = floor(abs($timeDiff / 1440)) . " day ago";
                                                        } elseif ($timeDiff > 2880) {
                                                            $timeDiff = floor(abs($timeDiff / 1440)) . " days ago";
                                                        }
                                                        echo $timeDiff;
                                                        ?>
                                                    </small>
                                                </h4>
                                                <p<?php echo $v_email->to ?></p>
                                            </a>
                                        </li><!-- end message -->           
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <li class="text-center"><p>
                                            <strong><?= lang('no_message') ?></strong>  
                                        </p>
                                    </li>
                                <?php endif; ?>
                            </ul>
                        </li>
                        <li class="footer"><a href="<?= base_url() ?>admin/mailbox"><?= lang('view_all') ?></a></li>
                    </ul>
                </li>

                <!-- User Account: style can be found in dropdown.less -->
                <li class="dropdown user user-menu">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown">

                        <img src="<?= base_url() . $profile_info->avatar ?>" class="user-image" alt="User Image" />
                        <span class="hidden-xs"><?= $profile_info->fullname ?></span>
                    </a>
                    <ul class="dropdown-menu">
                        <!-- User image -->
                        <li class="user-header">
                            <img src="<?= base_url() . $profile_info->avatar ?>" class="img-circle" alt="User Image" />
                            <p>
                                <?= $profile_info->fullname ?>
                                <small><?= lang('last_login') . ':' ?>
                                    <?php
                                    if ($user_info->last_login == '0000-00-00 00:00:00') {
                                        $login_time = "-";
                                    } else {
                                        $login_time = strftime(config_item('date_format') . " %H:%M:%S", strtotime($user_info->last_login));
                                    }
                                    echo $login_time;
                                    ?>
                                </small>
                            </p>
                        </li>
                        <!-- Menu Body -->
                        <li class="user-body">
                            <div class="col-xs-4 text-center">                                                                
                                <a href="<?= base_url() ?>admin/settings/activities" ><?= lang('activities') ?></a>
                            </div>
                            <div class="col-xs-4 text-center">

                            </div>
                            <div class="col-xs-4 text-center">                                
                                <a href="<?= base_url() ?>locked/lock_screen" ><?= lang('lock_screen') ?></a>
                            </div>
                        </li>
                        <!-- Menu Footer-->
                        <li class="user-footer">
                            <div class="pull-left">
                                <a href="<?= base_url() ?>admin/settings/update_profile" class="btn btn-default btn-flat"><?= lang('update_profile') ?></a>
                            </div>
                            <div class="pull-right">
                                <a href="<?= base_url() ?>login/logout" class="btn btn-default btn-flat"><?= lang('logout') ?></a>
                            </div>
                        </li>
                    </ul>
                </li>
                <!-- Control Sidebar Toggle Button -->

                <li>
                    <a href="#" data-toggle="control-sidebar"><i class="fa fa-bars"></i>
                        <span class="label label-danger"><?php
                            $user = $this->session->userdata('user_id');
                            $this->db->where('user_id', $user);
                            $this->db->where('status', 0);
                            $query = $this->db->get('tbl_todo');

                            $incomplete_todo_number = $query->num_rows();
                            if ($incomplete_todo_number > 0) {
                                echo $incomplete_todo_number;
                            }
                            ?></span>
                    </a>
                </li>
            </ul>
        </div>

    </nav>
</header>
<!-- Control Sidebar -->
<?php
$opened = $this->session->userdata('opened');
$this->session->unset_userdata('opened');
?>
<aside class="control-sidebar control-sidebar-dark <?php
if (!empty($opened)) {
    echo 'control-sidebar-open';
}
?>">
    <style>
        .active{
            background:none;
        }
    </style>
    <!-- Create the tabs -->    
    <!-- Tab panes -->
    <div class="tab-content">
        <!-- Home tab content -->
        <div class="tab-pane active" style="background:none;" id="control-sidebar-home-tab">
            <h2 style="color: #EFF3F4;font-weight: 100;text-align: center;">
                <?php echo date("l"); ?>
                <br />
                <?php echo date("jS F, Y"); ?>
            </h2>
            <form action="<?= base_url() ?>admin/user/todo/add" method="post" class="form-horizontal form-groups" style="margin-top: 40px">
                <div class="form-group">
                    <div class="col-sm-9 col-sm-offset-1">
                        <textarea class="form-control" type="text" name="title" placeholder="+<?= lang('add_todo') ?>" 
                                  style="background-color: #364559;border: 1px solid #4F595E;color: rgba(170,170,170 ,1);"
                                  data-validate="required"></textarea>
                    </div>
                    <input type="submit" value="<?= lang('add') ?>" class="btn btn-success btn-xs"  />
                </div>
            </form>
            <table style="width: 83%;margin-left: 22px;">
                <?php
                $this->db->where('user_id', $user_id);
                $this->db->order_by('order', 'asc');
                $todos = $this->db->get('tbl_todo')->result_array();
                foreach ($todos as $row):
                    ?>
                    <tr>
                        <td>
                    <li id="todo_1" 
                        style="<?php if ($row['status'] == 1): ?>text-decoration: line-through;<?php endif; ?>font-size: 13px;
                        <?php if ($row['status'] == 0): ?>color: #fff;<?php endif; ?>;">
                        <?php echo $row['title']; ?>
                    </li>
                    </td>
                    <td style="text-align:right;">
                        <div class="btn-group">
                            <button type="button" class="btn btn-default btn-sm dropdown-toggle " data-toggle="dropdown"
                                    style="padding:0px;background-color: #303641;border: 0px;-ms-transform: rotate(90deg); /* IE 9 */
                                    -webkit-transform: rotate(90deg); /* Chrome, Safari, Opera */
                                    transform: rotate(90deg);">
                                <i class="entypo-dot-2" style="color:#B4BCBE;"></i> 
                                <span class="" style="visibility:hidden; width:0px;"></span>
                            </button>
                            <ul class="dropdown-menu dropdown-default pull-right" role="menu" style="text-align:left;">
                                <li>
                                    <?php if ($row['status'] == 0): ?>
                                        <a href="<?= base_url() ?>admin/user/todo/mark_as_done/<?php echo $row['todo_id']; ?>">
                                            <i class="entypo-check"></i>
                                            <?php echo lang('mark_completed'); ?>
                                        </a>
                                    <?php endif; ?>
                                    <?php if ($row['status'] == 1): ?>
                                        <a href="<?= base_url() ?>admin/user/todo/mark_as_undone/<?php echo $row['todo_id']; ?>">
                                            <i class="entypo-cancel"></i>
                                            <?php echo lang('mark_incomplete'); ?>
                                        </a>
                                    <?php endif; ?>
                                </li>


                                <li>
                                    <a href="<?= base_url() ?>admin/user/todo/swap/<?php echo $row['todo_id']; ?>/up">
                                        <i class="entypo-up"></i>
                                        <?php echo lang('move_up'); ?>
                                    </a>
                                    <a href="<?= base_url() ?>admin/user/todo/swap/<?php echo $row['todo_id']; ?>/down">
                                        <i class="entypo-down"></i>
                                        <?php echo lang('move_down'); ?>
                                    </a>
                                </li>
                                <li class="divider"></li>


                                <li>
                                    <a href="<?= base_url() ?>admin/user/todo/delete/<?php echo $row['todo_id']; ?>">
                                        <i class="entypo-trash"></i>
                                        <?= lang('delete'); ?>
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </td>
                    </tr>
                <?php endforeach; ?>
            </table>

            <div id="idCalculadora"></div>

        </div><!-- /.tab-pane -->                
    </div>
</aside><!-- /.control-sidebar -->
<link rel="stylesheet" href="<?= base_url() ?>asset/js/plugins/calculator/SimpleCalculadorajQuery.css">
<script src="<?= base_url() ?>asset/js/plugins/calculator/SimpleCalculadorajQuery.js"></script>
<script>
    $("#idCalculadora").Calculadora({'EtiquetaBorrar': 'Clear', TituloHTML: ''});
</script>