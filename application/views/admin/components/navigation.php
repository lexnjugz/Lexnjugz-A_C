<!-- Left side column. contains the logo and sidebar -->
<?php
$user_permission = $_SESSION["user_roll"];

foreach ($user_permission as $v_permission) {
    $user_roll[$v_permission->menu_id] = $v_permission->menu_id;
}
?>

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
                <a href="#"><i class="fa fa-circle text-success"></i> <?= lang('online') ?></a>
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
                echo $page == 'message' ? 'active' : '';
            }
            ?>">
                <a href="<?php echo base_url(); ?>admin/message/"> <i class="fa fa-envelope"></i><span><?= lang('private_chat') ?></span></a>
            </li>
            <?php
            $this->db->select("tbl_task.*", FALSE);
            $this->db->select("tbl_users.*", FALSE);
            $this->db->select("tbl_account_details.*", FALSE);
            $this->db->join('tbl_users', 'tbl_users.user_id = tbl_task.timer_started_by');
            $this->db->join('tbl_account_details', 'tbl_account_details.user_id = tbl_task.timer_started_by');
            $this->db->where(array('timer_status' => 'On'));
            $task_timers = $this->db->get('tbl_task')->result_array();
            $user_id = $this->session->userdata('user_id');
            $role = $this->admin_model->check_by(array('user_id' => $user_id), 'tbl_users');
            ?>
            <?php
            if (!empty($task_timers)):
                ?>
                <li class="content-header" style="font-size: 13px;font-weight: bold;color: #fff"><?= lang('tasks') . ' ' . lang('start') ?> </li>
                <?php
                foreach ($task_timers as $v_task_timer):
                    if ($role->role_id == 1 || ($role->role_id == 2 && $user_id == $v_task_timer['user_id'])) :
                        ?>
                        <li class="timer recect_task" start="<?php echo $v_task_timer['timer_status']; ?>">
                            <a title="<?php echo $v_task_timer['task_name'] . " (" . $v_task_timer['username'] . ")"; ?>" data-placement="top" data-toggle="tooltip" href="<?= base_url() ?>admin/tasks/view_task_details/<?= $v_task_timer['task_id'] ?>"> 
                                <img  src="<?= base_url() . $v_task_timer['avatar'] ?>" class="img-circle">                                                                
                                <span id="tasks_hour_timer_<?= $v_task_timer['task_id'] ?>"> 0 </span>                        
                                <!-- SEPARATOR -->
                                :
                                <!-- MINUTE TIMER -->
                                <span id="tasks_minute_timer_<?= $v_task_timer['task_id'] ?>"> 0 </span>                                    
                                <!-- SEPARATOR -->                        
                                :
                                <!-- SECOND TIMER -->
                                <span id="tasks_second_timer_<?= $v_task_timer['task_id'] ?>"> 0 </span>                        
                                <b class="label label-danger pull-right"> <i class="fa fa-clock-o fa-spin"></i></b>
                            </a>
                        </li>
                        <?php
                        //RUNS THE TIMER IF ONLY TIMER_STATUS = 1
                        if ($v_task_timer['timer_status'] == 'on') :

                            $task_current_moment_timestamp = strtotime(date("H:i:s"));
                            $task_timer_starting_moment_timestamp = $this->db->get_where('tbl_task', array('task_id' => $v_task_timer['task_id']))->row()->start_time;
                            $task_total_duration = $task_current_moment_timestamp - $task_timer_starting_moment_timestamp;

                            $task_total_hour = intval($task_total_duration / 3600);
                            $task_total_duration -= $task_total_hour * 3600;
                            $task_total_minute = intval($task_total_duration / 60);
                            $task_total_second = intval($task_total_duration % 60);
                            ?>

                            <script type="text/javascript">
                                // SET THE INITIAL VALUES TO TIMER PLACES
                                var timer_starting_hour = <?php echo $task_total_hour; ?>;
                                document.getElementById("tasks_hour_timer_<?= $v_task_timer['task_id'] ?>").innerHTML = timer_starting_hour;
                                var timer_starting_minute = <?php echo $task_total_minute; ?>;
                                document.getElementById("tasks_minute_timer_<?= $v_task_timer['task_id'] ?>").innerHTML = timer_starting_minute;
                                var timer_starting_second = <?php echo $task_total_second; ?>;
                                document.getElementById("tasks_second_timer_<?= $v_task_timer['task_id'] ?>").innerHTML = timer_starting_second;

                                // INITIALIZE THE TIMER WITH SECOND DELAY
                                var timer = timer_starting_second;
                                var mytimer = setInterval(function () {
                                    task_run_timer()
                                }, 1000);

                                function task_run_timer() {
                                    timer++;

                                    if (timer > 59)
                                    {
                                        timer = 0;
                                        timer_starting_minute++;
                                        document.getElementById("tasks_minute_timer_<?= $v_task_timer['task_id'] ?>").innerHTML = timer_starting_minute;
                                    }

                                    if (timer_starting_minute > 59)
                                    {
                                        timer_starting_minute = 0;
                                        timer_starting_hour++;
                                        document.getElementById("tasks_hour_timer_<?= $v_task_timer['task_id'] ?>").innerHTML = timer_starting_hour;
                                    }

                                    document.getElementById("tasks_second_timer_<?= $v_task_timer['task_id'] ?>").innerHTML = timer;
                                }
                            </script>

                        <?php endif; ?>
                    <?php endif; ?>
                <?php endforeach; ?>
            <?php endif; ?>

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
                        } elseif ($v_online_user->role_id == 3) {
                            $user = 'Staff';
                        } else {
                            $user = 'Client';
                        }
                        ?>                            
                        <li class="" >
                            <a title="<?php echo $user ?>" data-placement="top" data-toggle="tooltip" class="dker" href="<?php echo base_url(); ?>admin/message/get_chat/<?php echo $v_online_user->user_id ?>">
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