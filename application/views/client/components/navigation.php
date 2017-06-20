<!-- Left side column. contains the logo and sidebar -->


<aside class="main-sidebar">
    <!-- sidebar: style can be found in sidebar.less -->
    <?php
    $user_id = $this->session->userdata('user_id');
    $profile_info = $this->db->where('user_id', $user_id)->get('tbl_account_details')->row();
    $user_info = $this->db->where('user_id', $user_id)->get('tbl_users')->row();
    ?>
    <section class="sidebar">    
        <div class="user-panel">
            <div class="pull-left image">
                <img src="<?= base_url() . $profile_info->avatar ?>" class="img-circle" alt="User Image" />
            </div>
            <div class="pull-left info">
                <p><?= $profile_info->fullname ?></p>
                <a href="#"><i class="fa fa-circle text-success"></i> Online</a>
            </div>
        </div>        
        <br/>
        <!-- sidebar menu: : style can be found in sidebar.less -->        

        <?php
        echo $this->menu->dynamicMenu();
        ?>         
        <ul class="sidebar-menu">
            <li class="<?php
            if (!empty($page)) {
                echo $page == lang('dashboard') ? 'active' : '';
            }
            ?>">
                <a href="<?php echo base_url(); ?>client/dashboard/"> <i class="fa fa-dashboard"></i><span><?= lang('dashboard') ?></span></a>
            </li>           
            <li class="<?php
            if (!empty($page)) {
                echo $page == lang('mailbox') ? 'active' : '';
            }
            ?>">
                <a href="<?php echo base_url(); ?>client/mailbox/"> <i class="fa fa-envelope"></i><span><?= lang('mailbox') ?></span></a>
            </li>
            <li class="<?php
            if (!empty($page)) {
                echo $page == lang('project') ? 'active' : '';
            }
            ?>">
                <a href="<?php echo base_url(); ?>client/project"> <i class="fa fa-folder-open-o"></i><span><?= lang('project') ?></span></a>
            </li>
            <li class="<?php
            if (!empty($page)) {
                echo $page == lang('leads') ? 'active' : '';
            }
            ?>">
                <a href="<?php echo base_url(); ?>client/leads"> <i class="fa fa-rocket"></i><span><?= lang('leads') ?></span></a>
            </li>
            <li class="<?php
            if (!empty($page)) {
                echo $page == lang('invoice') ? 'active' : '';
            }
            ?>">
                <a href="<?php echo base_url(); ?>client/invoice/manage_invoice"> <i class="fa fa-shopping-cart"></i><span><?= lang('invoice') ?></span></a>
            </li>
            <li class="<?php
            if (!empty($page)) {
                echo $page == lang('estimates') ? 'active' : '';
            }
            ?>">
                <a href="<?php echo base_url(); ?>client/estimates/"> <i class="fa fa-tachometer"></i><span><?= lang('estimates') ?></span></a>
            </li>
            <li class="<?php
            if (!empty($page)) {
                echo $page == lang('payments') ? 'active' : '';
            }
            ?>">
                <a href="<?php echo base_url(); ?>client/invoice/all_payments"> <i class="fa fa-money"></i><span><?= lang('payments') ?></span></a>
            </li>
            <li class="treeview  <?php
            if (!empty($page)) {
                echo $page == lang('tickets') ? 'active' : '';
            }
            ?>">
                <a href="#"> <i class="fa fa-ticket"></i><span><?= lang('tickets') ?></span><i class="fa fa-angle-right pull-right"></i></a>
                <ul  class="treeview-menu">
                    <li class="<?= (!empty($sub) && $sub == 1 ? 'active' : ' ') ?>">
                        <a href="<?= base_url() ?>client/tickets/answered"> <i class="fa fa-circle-o"></i><span><?= lang('answered') ?></span></a>
                    </li> 
                    <li class="<?= (!empty($sub) && $sub == 2 ? 'active' : ' ') ?>">
                        <a href="<?= base_url() ?>client/tickets/open"> <i class="fa fa-circle-o"></i><span><?= lang('open') ?></span></a>
                    </li> 
                    <li class="<?= (!empty($sub) && $sub == 3 ? 'active' : ' ') ?>">
                        <a href="<?= base_url() ?>client/tickets/in_progress"> <i class="fa fa-circle-o"></i><span><?= lang('in_progress') ?></span></a>
                    </li> 
                    <li class="<?= (!empty($sub) && $sub == 4 ? 'active' : ' ') ?>">
                        <a href="<?= base_url() ?>client/tickets/closed"> <i class="fa fa-circle-o"></i><span><?= lang('closed') ?></span></a>
                    </li> 
                    <li class="<?= (!empty($sub) && $sub == 5 ? 'active' : ' ') ?>">
                        <a href="<?= base_url() ?>client/tickets"> <i class="fa fa-ticket"></i><span><?= lang('all_tickets') ?></span></a>
                    </li> 
                </ul> 
            </li>            
            <li class="<?php
            if (!empty($page)) {
                echo $page == lang('quotations') ? 'active' : '';
            }
            ?>">
                <a href="<?php echo base_url(); ?>client/quotations/"> <i class="fa fa-paste"></i><span><?= lang('quotations') ?></span></a>
            </li>
            <li class="<?php
            if (!empty($page)) {
                echo $page == lang('users') ? 'active' : '';
            }
            ?>">
                <a href="<?php echo base_url(); ?>client/user/user_list"> <i class="fa fa-users"></i><span><?= lang('users') ?></span></a>
            </li>
            <li class="<?php
            if (!empty($page)) {
                echo $page == lang('settings') ? 'active' : '';
            }
            ?>">
                <a href="<?php echo base_url(); ?>client/settings/"> <i class="fa fa-cogs"></i><span><?= lang('settings') ?></span></a>
            </li>
            <li class="<?php
            if (!empty($page)) {
                echo $page == lang('private_chat') ? 'active' : '';
            }
            ?>">
                <a href="<?php echo base_url(); ?>client/message/"> <i class="fa fa-envelope"></i><span><?= lang('private_chat') ?></span></a>
            </li>

            <?php
            $online_user = $this->db->where(array('online_status' => '1'))->get('tbl_users')->result();

            if (!empty($online_user)):
                ?>
                <li class="content-header" style=";font-weight: bold;color: #fff;font-size: 14px;"><?= lang('online') ?></li>
                <?php
                foreach ($online_user as $v_online_user):
                    if ($v_online_user->user_id != $this->session->userdata('user_id')) {
                        if ($v_online_user->role_id == 1) {
                            $user = 'Admin';
                        } elseif ($v_online_user->role_id == 2) {
                            $user = 'Staff';
                        } else {
                            $user = 'Client';
                        }
                        ?>                            
                        <li class="" >
                            <a title="<?php echo $user ?>" data-placement="top" data-toggle="tooltip" class="dker" href="<?php echo base_url(); ?>client/message/get_chat/<?php echo $v_online_user->user_id ?>">
                                <?php echo $v_online_user->username ?>
                                <b class="label label-success pull-right"> <i class="fa fa-dot-circle-o fa-spin"></i></b>
                            </a>
                        </li>
                        <?php
                    }
                endforeach;
                ?>
            <?php endif ?>
        </ul>
    </section>
    <!-- /.sidebar -->
</aside>