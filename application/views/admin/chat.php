<style>
    .active{
        background: #C8CAC9;
        color: #000;
    }
</style>
<div class="row">
    <div class="col-sm-3">
        <div class="panel panel-default">
            <div class="panel-heading">                      
                <?= lang('all_users') ?>
            </div>                        
            <div class="panel-body">        
                <section class="scrollable  ">
                    <div class="slim-scroll" data-height="auto" data-disable-fade-out="true" data-distance="0" data-size="5px" data-color="#333333">
                        <?php
                        $all_user_info = $this->db->where(array('activated' => 1))->get('tbl_users')->result();
                        if (!empty($all_user_info)): foreach ($all_user_info as $v_user) :

                                $account_info = $this->message_model->check_by(array('user_id' => $v_user->user_id), 'tbl_account_details');
                                if (!empty($account_info) && $account_info->user_id != $this->session->userdata('user_id')) {
                                    ?>
                                    <ul class="nav"><?php
                                        if ($v_user->role_id == 1) {
                                            $user = 'Admin';
                                        } elseif ($v_user->role_id == 3) {
                                            $user = 'Staff';
                                        } else {
                                            $user = 'Client';
                                        }
                                        ?>
                                        <li class="<?php
                                        if ($v_user->user_id == $this->uri->segment(4)) {
                                            echo "active";
                                        }
                                        ?>">
                                            <a href="<?php echo base_url(); ?>admin/message/get_chat/<?php echo $v_user->user_id ?>">
                                                <?= $account_info->fullname ?>  <small><?= $user ?></small>                                          

                                            </a> </li>                                    
                                    </ul>
                                    <?php
                                };
                            endforeach;
                        endif;
                        ?>
                    </div></section>
            </div>
        </div>
    </div>
    <div class="col-md-9">
        <!-- DIRECT CHAT PRIMARY -->
        <div class="box box-primary direct-chat direct-chat-primary">
            <div class="box-header with-border">
                <?php
                $user_profile = $this->message_model->check_by(array('user_id' => $this->uri->segment(4)), 'tbl_account_details');
                $user_info = $this->message_model->check_by(array('user_id' => $this->uri->segment(4)), 'tbl_users');
                $my_profile = $this->message_model->check_by(array('user_id' => $this->session->userdata('user_id')), 'tbl_account_details');
                $my_info = $this->message_model->check_by(array('user_id' => $this->session->userdata('user_id')), 'tbl_users');
                if (!empty($user_profile)) {
                    ?>
                    <h3 class="box-title"><?= $user_profile->fullname ?></h3>
                    <div class="box-tools pull-right">                    
                        <button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>                    
                        <button class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
                    </div>
                <?php } ?>
            </div><!-- /.box-header -->
            <div class="box-body">
                <!-- Conversations are loaded here -->
                <div class="direct-chat-messages chat-slim-scroll">
                    <!-- Message. Default to the left -->
                    <?php
                    $reciver_info = $this->db->where(array('receive_user_id' => $this->session->userdata('user_id'), 'send_user_id' => $this->uri->segment(4)))->order_by('message_time', 'DESC')->get('tbl_private_message_send')->result();
                    if (!empty($reciver_info)):foreach ($reciver_info as $v_receiver_info):
                            ?>
                            <div class="direct-chat-msg">
                                <div class="direct-chat-info clearfix">
                                    <span class="direct-chat-name pull-left"><?= $user_profile->fullname ?></span>
                                    <span class="direct-chat-timestamp pull-right"><?= strftime(config_item('date_format'), strtotime($v_receiver_info->message_time)); ?> at <?= date('h:i:A', strtotime($v_receiver_info->message_time)) ?></span>
                                </div><!-- /.direct-chat-info -->
                                <img class="direct-chat-img" src="<?= base_url() . $user_profile->avatar ?>" alt="message user image" /><!-- /.direct-chat-img -->
                                <div class="direct-chat-text">
                                    <?= $v_receiver_info->message ?>
                                </div><!-- /.direct-chat-text -->
                            </div><!-- /.direct-chat-msg -->
                        <?php endforeach; ?>
                    <?php endif; ?>
                    <!-- Message to the right -->
                    <?php
                    $sender_info = $this->db->where(array('receive_user_id' => $this->uri->segment(4), 'send_user_id' => $this->session->userdata('user_id')))->order_by('message_time', 'DESC')->get('tbl_private_message_send')->result();
                    if (!empty($sender_info)):foreach ($sender_info as $v_sender_info):
                            ?>
                            <div class="direct-chat-msg right">
                                <div class="direct-chat-info clearfix">
                                    <span class="direct-chat-name pull-right"><?= $my_profile->fullname ?></span>
                                    <span class="direct-chat-timestamp pull-left"><?= strftime(config_item('date_format'), strtotime($v_sender_info->message_time)); ?> at <?= date('h:i:A', strtotime($v_sender_info->message_time)) ?></span>
                                </div><!-- /.direct-chat-info -->
                                <img class="direct-chat-img" src="<?= base_url() . $my_profile->avatar ?>" alt="message user image" /><!-- /.direct-chat-img -->
                                <div class="direct-chat-text">
                                    <?= $v_sender_info->message ?>
                                </div><!-- /.direct-chat-text -->
                            </div><!-- /.direct-chat-msg -->
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div><!--/.direct-chat-messages-->

            </div><!-- /.box-body -->
            <div class="box-footer">
                <form action="<?= base_url() ?>admin/message/send_message" method="post">
                    <div class="input-group">
                        <input type="text" required="" name="message" placeholder="Type Message ..." class="form-control" />
                        <input type="hidden" name="receive_user_id" value="<?= $this->uri->segment(4) ?>" class="form-control" />
                        <span class="input-group-btn">
                            <button type="submit" class="btn btn-primary btn-flat">Send</button>
                        </span>
                    </div>
                </form>
            </div><!-- /.box-footer-->
        </div><!--/.direct-chat -->
    </div><!-- /.col -->
</div>