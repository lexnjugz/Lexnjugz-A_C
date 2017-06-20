<?= message_box('success') ?>
<?= message_box('error') ?>
<div class="row">    
    <div class="col-sm-3">
        <div class="panel panel-default">
            <div class="panel-heading">      
                <a style="margin-top: -5px" href="<?= base_url() ?>admin/tickets/index/edit_tickets" data-original-title="<?= lang('new_ticket') ?>" data-toggle="tooltip" data-placement="top" class="btn btn-icon btn-<?= config_item('button_color') ?> btn-sm pull-right"><i class="fa fa-plus"></i></a>
                <?= lang('all_tickets') ?>
            </div>                        
            <div class="panel-body">        
                <section class="scrollable  ">
                    <div class="slim-scroll" data-height="auto" data-disable-fade-out="true" data-distance="0" data-size="5px" data-color="#333333">
                        <?php
                        if (!empty($all_tickets_info)) :
                            foreach ($all_tickets_info as $v_tickets_info) :
                                ?>
                                <ul class="nav"><?php
                                    if ($v_tickets_info->status == 'open') {
                                        $s_label = 'danger';
                                    } elseif ($v_tickets_info->status == 'closed') {
                                        $s_label = 'success';
                                    } else {
                                        $s_label = 'default';
                                    }
                                    ?>
                                    <li class="<?php
                                    if ($v_tickets_info->tickets_id == $this->uri->segment(5)) {
                                        echo "active";
                                    }
                                    ?>">
                                        <a href="<?= base_url() ?>admin/tickets/index/tickets_details/<?= $v_tickets_info->tickets_id ?>">
                                            <?= $v_tickets_info->ticket_code ?>
                                            <?php
                                            if ($v_tickets_info->status == 'in_progress') {
                                                $status = 'In Progress';
                                            } else {
                                                $status = $v_tickets_info->status;
                                            }
                                            ?>
                                            <div class="pull-right">                                                
                                                <span class="label label-<?= $s_label ?>"><?= ucfirst($status) ?> </span>
                                            </div> <br>
                                            <?php $user_info = $this->db->where(array('user_id' => $v_tickets_info->reporter))->get('tbl_users')->row(); ?>
                                            <small class="block small text-muted"><?= ucfirst($user_info->username) ?> | <?= strftime(config_item('date_format'), strtotime($v_tickets_info->created)); ?> </small>
                                        </a> </li>                                    
                                </ul>
                                <?php
                            endforeach;
                        endif;
                        ?>
                    </div></section>
            </div>
        </div>
    </div>

    <section class="col-sm-9">                                       
        <header class="hidden-print">
            <div class="row ">
                <div class="col-sm-12">
                    <?php if ($role != '2') { ?>
                        <a href="<?= base_url() ?>tickets/edit/<?= $tickets_info->tickets_id ?>" class="btn btn-sm btn-primary">
                            <i class="fa fa-edit"></i></a>
                        <?php if ($role == '1') { ?>
                            <a href="<?= base_url() ?>tickets/delete/<?= $tickets_info->tickets_id ?>" class="btn btn-sm btn-danger" data-toggle="ajaxModal">
                                <i class="fa fa-trash-o"></i></a>
                        <?php } ?>

                        <div class="btn-group">
                            <button class="btn btn-sm btn-success dropdown-toggle" data-toggle="dropdown">
                                <?= lang('change_status') ?>
                                <span class="caret"></span></button>
                            <ul class="dropdown-menu">
                                <?php
                                $status_info = $this->db->get('tbl_status')->result();
                                if (!empty($status_info)) {
                                    foreach ($status_info as $v_status) {
                                        ?>
                                        <li><a data-toggle='modal' data-target='#myModal' href="<?= base_url() ?>admin/tickets/change_status/<?= $tickets_info->tickets_id ?>/<?= $v_status->status ?>"><?= ucfirst($v_status->status) ?></a></li>
                                        <?php
                                    }
                                }
                                ?>
                            </ul>
                        </div>
                    </div>
                <?php } ?>
            </div>
        </header>                

        <!-- Start Display Details -->
        <div class="row" style="margin-top: 10px;">                            
            <div class="col-lg-4">
                <ul class="list-group no-radius">
                    <?php
                    if ($tickets_info->status == 'open') {
                        $s_label = 'danger';
                    } elseif ($tickets_info->status == 'closed') {
                        $s_label = 'success';
                    } else {
                        $s_label = 'default';
                    }
                    ?>

                    <li class="list-group-item"><span class="pull-right"><?= $tickets_info->ticket_code ?></span><?= lang('ticket_code') ?></li>


                    <li class="list-group-item">
                        <?= lang('reporter') ?>
                        <span class="pull-right">
                            <a class="recect_task pull-left">
                                <?php
                                $profile_info = $this->db->where(array('user_id' => $tickets_info->reporter))->get('tbl_account_details')->row();
                                if (!empty($profile_info)) {
                                    ?>
                                    <img style="width: 18px;margin-left: 18px;
                                         height: 18px;
                                         border: 1px solid #aaa;" src="<?= base_url() . $profile_info->avatar ?>" class="img-circle">
                                     <?php } ?>

                                <?=
                                ($profile_info->fullname)
                                ?>
                            </a>
                        </span>
                    </li>

                    <li class="list-group-item">
                        <span class="pull-right">
                            <?php
                            $dept_info = $this->db->where(array('departments_id' => $tickets_info->departments_id))->get('tbl_departments')->row();
                            if (!empty($dept_info)) {
                                $dept_name = $dept_info->deptname;
                            } else {
                                $dept_name = '-';
                            }
                            echo $dept_name;
                            ?>
                        </span><?= lang('department') ?>
                    </li>
                    <?php
                    if ($tickets_info->status == 'in_progress') {
                        $status = 'In Progress';
                    } else {
                        $status = $tickets_info->status;
                    }
                    ?>
                    <li class="list-group-item">
                        <span class="pull-right"><label class="label label-<?= $s_label ?>"><?= ucfirst($status) ?></label>
                        </span><?= lang('status') ?>
                    </li>
                    <?php if (!empty($tickets_info->comment)): ?>
                        <div class="list-group-item">
                            <?= lang('comments') ?>:
                            <label class=""><?= $tickets_info->comment ?></label>
                        </div>
                    <?php endif; ?>

                    <li class="list-group-item"><span class="pull-right"><?= $tickets_info->priority ?></span><?= lang('priority') ?></li>

                    <?php if ($tickets_info->filename != NULL) { ?>
                        <li class="list-group-item"><span class="pull-right">
                                <a href="<?= base_url() ?>admin/tickets/index/download_file/<?= $tickets_info->tickets_id ?>"><?= lang('download') ?></a></span><?= lang('resourcement') ?></li>
                    <?php } ?>
                    <li class="list-group-item"><span class="pull-right"><?= $tickets_info->created ?></span><?= lang('created') ?></li>

                    <div class="list-group-item ">Subject: <?= $tickets_info->subject ?></div>                                    
                    <div class="list-group-item "><?= nl2br($tickets_info->body) ?></div>                                    
                </ul>
            </div>
            <!-- End details C1-->
            <div  class="col-lg-8">

                <?php
                $user_id = $this->session->userdata('user_id');
                $user_info = $this->db->where(array('user_id' => $user_id))->get('tbl_users')->row();
                $profile_info = $this->db->where(array('user_id' => $user_id))->get('tbl_account_details')->row();
                ?>                               
                <!-- comment form -->                    
                <a class="pull-left recect_task  ">
                    <?php if (!empty($profile_info)) { ?>
                        <img src="<?= base_url() . $profile_info->avatar ?>" class="img-circle">
                    <?php } ?> 
                </a>
                <section class="media-body">

                    <section class="panel panel-default">
                        <form method="post" action="<?= base_url() ?>admin/tickets/index/save_reply/<?= $v_tickets_info->tickets_id ?>">

                            <textarea class="form-control no-border" name="body" rows="3" placeholder="Ticket #<?= $v_tickets_info->ticket_code ?> reply"></textarea>

                            <footer class="panel-footer label-light lter">
                                <button class="btn btn-info pull-right btn-sm" type="submit"><?= lang('reply_ticket') ?></button>
                                <ul class="nav nav-pills nav-sm">
                                </ul>
                            </footer>
                        </form>
                    </section>

                </section>                    
                <?php
                $ticket_replies = $this->db->where(array('tickets_id' => $tickets_info->tickets_id))->get('tbl_tickets_replies')->result();
                if (!empty($ticket_replies)) {
                    foreach ($ticket_replies as $v_replies) {

                        $profile_info = $this->db->where(array('user_id' => $v_replies->replierid))->get('tbl_account_details')->row();

                        $user_info = $this->db->where(array('user_id' => $v_replies->replierid))->get('tbl_users')->row();
                        $username = $user_info->username;
                        if ($user_info->role_id == '1') {
                            $role_label = 'danger';
                            $user_role = 'admin';
                        } elseif ($user_info->role_id == '3') {
                            $user_role = 'staff';
                            $role_label = 'primary';
                        } else {
                            $role_label = 'info';
                            $user_role = 'client';
                        }
                        ?>

                        <ul style="overflow: hidden" class="timeline">                                   
                            <li>
                                <?php
                                if (!empty($profile_info)) {
                                    ?>
                                    <img style="width: 30px;margin-left: 18px;
                                         height: 29px;
                                         border: 1px solid #aaa;" src="<?= base_url() . $profile_info->avatar ?>" class="img-circle">
                                     <?php } ?>

                                <div class="timeline-item">
                                    <?php
                                    $today = time();
                                    $reply_day = strtotime($v_replies->time);
                                    ?> 
                                    <span class="time"><i class="fa fa-clock-o"></i> <?= $this->tickets_model->get_time_different($today, $reply_day) ?> <?= lang('ago') ?>
                                        <?php if ($v_replies->replierid == $this->session->userdata('user_id')) { ?>
                                            <?= btn_delete('admin/tickets/delete/delete_ticket_replay/' . $v_replies->tickets_id . '/' . $v_replies->tickets_replies_id) ?>                                                
                                        <?php } ?>
                                    </span>
                                    <h3 class="timeline-header" style="margin-top: -35px;"><a href="#"><?= ucfirst($username) ?></a>
                                        <label class="label label-<?= $role_label ?> " style="padding: 2px;font-size: 10px"><?= $user_role ?></label>
                                    </h3>
                                    <div class="timeline-body">                                                
                                        <?= $v_replies->body ?>
                                    </div>
                                </div>
                            </li>                                   
                        </ul> 
                        <?php
                    }
                } else {
                    ?>
                    <ul style="overflow-y: hidden" class="timeline">                                   
                        <li><p><?= lang('no_ticket_replies') ?></p></li>
                    </ul>
                <?php } ?>
                <!-- End ticket replies -->                
            </div>        
        </div>
    </section>    
</div>
<!-- End details -->