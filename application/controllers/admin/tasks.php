<?php

/**
 * Description of Tasks
 *
 * @author Nayeem
 */
class Tasks extends Admin_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('tasks_model');
        $this->load->helper('ckeditor');
        $this->data['ckeditor'] = array(
            'id' => 'ck_editor',
            'path' => 'asset/js/ckeditor',
            'config' => array(
                'toolbar' => "Full",
                'width' => "90%",
                'height' => "200px"
            )
        );
    }

    public function all_task($id = NULL, $opt_id = NULL) {
        $data['title'] = lang('all_task');
// get all assign_user
        $this->tasks_model->_table_name = 'tbl_users';
        $this->tasks_model->_order_by = 'user_id';
        $data['assign_user'] = $this->tasks_model->get_by(array('role_id !=' => '2'), FALSE);

        if ($id) { // retrive data from db by id
            $data['active'] = 2;
            if ($id == 'project') {
                $data['project_id'] = $opt_id;
            } elseif ($id == 'opportunities') {
                $data['opportunities_id'] = $opt_id;
                $data['all_opportunities_info'] = $this->db->get('tbl_opportunities')->result();
            } else {
//get all task information
                $data['task_info'] = $this->db->where('task_id', $id)->get('tbl_task')->row();
            }
        } else {
            $data['active'] = 1;
        }

        $data['editor'] = $this->data;
        $data['subview'] = $this->load->view('admin/tasks/tasks', $data, TRUE);
        $this->load->view('admin/_layout_main', $data);
    }

    public function save_task($id = NULL) {
        $data = $this->tasks_model->array_from_post(array(
            'task_name',
            'task_description',
            'task_start_date',
            'due_date',
            'task_hour',
            'task_progress',
            'task_status'));
        $project_id = $this->input->post('project_id', TRUE);
        if (!empty($project_id)) {
            $data['project_id'] = $project_id;
            $data['milestones_id'] = $this->input->post('milestones_id', TRUE);
        } else {
            $data['project_id'] = NULL;
            $data['milestones_id'] = NULL;
        }
        $opportunities_id = $this->input->post('opportunities_id', TRUE);
        if (!empty($opportunities_id)) {
            $data['opportunities_id'] = $opportunities_id;
        } else {
            $opportunities_id = NULL;
        }
        $assigned_to = $this->tasks_model->array_from_post(array('assigned_to'));
        if (!empty($assigned_to['assigned_to'])) {
            $data['assigned_to'] = serialize($assigned_to);
        } else {
            set_message('error', lang('assigned_to') . ' Field is required');
            redirect('admin/tasks/all_task/2');
        }

        //save data into table.
        $this->tasks_model->_table_name = "tbl_task"; // table name
        $this->tasks_model->_primary_key = "task_id"; // $id
        $id = $this->tasks_model->save($data, $id);
        if (!empty($id)) {
            $msg = lang('update_task');
            $activity = 'activity_update_task';
            $id = $id;
            if (!empty($assigned_to['assigned_to'])) {
                // send update
                $this->notify_assigned_tasks($assigned_to['assigned_to'], $id, TRUE);
            }
        } else {
            $msg = lang('save_task');
            $activity = 'activity_new_task';
            if (!empty($assigned_to['assigned_to'])) {
                $this->notify_assigned_tasks($assigned_to['assigned_to'], $id);
            }
        }
// save into activities
        $activities = array(
            'user' => $this->session->userdata('user_id'),
            'module' => 'tasks',
            'module_field_id' => $id,
            'activity' => $activity,
            'icon' => 'fa-ticket',
            'value1' => $data['task_name'],
        );
// Update into tbl_project
        $this->tasks_model->_table_name = "tbl_activities"; //table name
        $this->tasks_model->_primary_key = "activities_id";
        $this->tasks_model->save($activities);

        $type = "success";
        $message = $msg;
        set_message($type, $message);
        redirect('admin/tasks/all_task');
    }

    function notify_assigned_tasks($users, $task_id, $update = NULL) {
        if (!empty($update)) {
            $email_template = $this->tasks_model->check_by(array('email_group' => 'tasks_updated'), 'tbl_email_templates');
        } else {
            $email_template = $this->tasks_model->check_by(array('email_group' => 'task_assigned'), 'tbl_email_templates');
        }
        $tasks_info = $this->tasks_model->check_by(array('task_id' => $task_id), 'tbl_task');
        $message = $email_template->template_body;

        $subject = $email_template->subject;

        $task_name = str_replace("{TASK_NAME}", $tasks_info->task_name, $message);

        $assigned_by = str_replace("{ASSIGNED_BY}", ucfirst($this->session->userdata('name')), $task_name);
        $Link = str_replace("{TASK_URL}", base_url() . 'admin/tasks/view_task_details/' . $tasks_info->task_id, $assigned_by);
        $message = str_replace("{SITE_NAME}", config_item('company_name'), $Link);

        $data['message'] = $message;
        $message = $this->load->view('email_template', $data, TRUE);

        $params['subject'] = $subject;
        $params['message'] = $message;
        $params['resourceed_file'] = '';

        foreach ($users as $v_user) {
            $login_info = $this->tasks_model->check_by(array('user_id' => $v_user), 'tbl_users');
            $params['recipient'] = $login_info->email;
            $this->tasks_model->send_email($params);
        }
    }

    public function update_member($id) {
        $tasks_info = $this->tasks_model->check_by(array('task_id' => $id), 'tbl_task');

        $assigned_to = $this->tasks_model->array_from_post(array('assigned_to'));
        if (!empty($assigned_to['assigned_to'])) {
            $data['assigned_to'] = serialize($assigned_to);
        } else {
            set_message('error', lang('assigned_to') . ' Field is required');
            redirect('admin/tasks/all_task/2');
        }

//save data into table.
        $this->tasks_model->_table_name = "tbl_task"; // table name
        $this->tasks_model->_primary_key = "task_id"; // $id
        $this->tasks_model->save($data, $id);

        $msg = lang('update_task');
        $activity = 'activity_update_task';
        if (!empty($assigned_to['assigned_to'])) {
            $this->notify_assigned_tasks($assigned_to['assigned_to'], $id);
        }

// save into activities
        $activities = array(
            'user' => $this->session->userdata('user_id'),
            'module' => 'tasks',
            'module_field_id' => $id,
            'activity' => $activity,
            'icon' => 'fa-ticket',
            'value1' => $tasks_info->task_name,
        );
// Update into tbl_project
        $this->tasks_model->_table_name = "tbl_activities"; //table name
        $this->tasks_model->_primary_key = "activities_id";
        $this->tasks_model->save($activities);

        $type = "success";
        $message = $msg;
        set_message($type, $message);
        redirect('admin/tasks/view_task_details/' . $id);
    }

    public function update_status($id = NULL) {
        $tasks_info = $this->tasks_model->check_by(array('task_id' => $id), 'tbl_task');
        $assigned_to = unserialize($tasks_info->assigned_to);

        if (!empty($assigned_to['assigned_to'])) {
            $this->notify_assigned_tasks($assigned_to['assigned_to'], $id, TRUE);
        }

        $data = $this->tasks_model->array_from_post(array(
            'task_progress',
            'task_status'));

//save data into table.
        $this->tasks_model->_table_name = "tbl_task"; // table name
        $this->tasks_model->_primary_key = "task_id"; // $id
        $id = $this->tasks_model->save($data, $id);
// save into activities
        $activities = array(
            'user' => $this->session->userdata('user_id'),
            'module' => 'tasks',
            'module_field_id' => $id,
            'activity' => 'activity_update_task',
            'icon' => 'fa-ticket',
            'value1' => $data['task_progress'],
        );
// Update into tbl_project
        $this->tasks_model->_table_name = "tbl_activities"; //table name
        $this->tasks_model->_primary_key = "activities_id";
        $this->tasks_model->save($activities);

        $type = "success";
        $message = lang('update_task');
        set_message($type, $message);
        redirect('admin/tasks/view_task_details/' . $id);
    }

    public function save_tasks_notes($id) {

        $data = $this->tasks_model->array_from_post(array('tasks_notes'));

//save data into table.
        $this->tasks_model->_table_name = "tbl_task"; // table name
        $this->tasks_model->_primary_key = "task_id"; // $id
        $id = $this->tasks_model->save($data, $id);
// save into activities
        $activities = array(
            'user' => $this->session->userdata('user_id'),
            'module' => 'tasks',
            'module_field_id' => $id,
            'activity' => 'activity_update_task',
            'icon' => 'fa-ticket',
            'value1' => $data['tasks_notes'],
        );
// Update into tbl_project
        $this->tasks_model->_table_name = "tbl_activities"; //table name
        $this->tasks_model->_primary_key = "activities_id";
        $this->tasks_model->save($activities);

        $type = "success";
        $message = lang('update_task');
        set_message($type, $message);
        redirect('admin/tasks/view_task_details/' . $id . '/' . $data['active'] = 4);
    }

    public function save_comments() {

        $data['task_id'] = $this->input->post('task_id', TRUE);
        $data['comment'] = $this->input->post('comment', TRUE);
        $data['user_id'] = $this->session->userdata('user_id');

//save data into table.
        $this->tasks_model->_table_name = "tbl_task_comment"; // table name
        $this->tasks_model->_primary_key = "task_comment_id"; // $id
        $comment_id = $this->tasks_model->save($data);
// save into activities
        $activities = array(
            'user' => $this->session->userdata('user_id'),
            'module' => 'tasks',
            'module_field_id' => $comment_id,
            'activity' => 'activity_new_task_comment',
            'icon' => 'fa-ticket',
            'value1' => $data['comment'],
        );
// Update into tbl_project
        $this->tasks_model->_table_name = "tbl_activities"; //table name
        $this->tasks_model->_primary_key = "activities_id";
        $this->tasks_model->save($activities);

// send notification
        $this->notify_comments_tasks($comment_id);

        $type = "success";
        $message = lang('task_comment_save');
        set_message($type, $message);
        redirect('admin/tasks/view_task_details/' . $data['task_id'] . '/' . $data['active'] = 2);
    }

    function notify_comments_tasks($comment_id) {
        $email_template = $this->tasks_model->check_by(array('email_group' => 'tasks_comments'), 'tbl_email_templates');
        $tasks_comment_info = $this->tasks_model->check_by(array('task_comment_id' => $comment_id), 'tbl_task_comment');

        $tasks_info = $this->tasks_model->check_by(array('task_id' => $tasks_comment_info->task_id), 'tbl_task');
        $message = $email_template->template_body;

        $subject = $email_template->subject;

        $task_name = str_replace("{TASK_NAME}", $tasks_info->task_name, $message);
        $assigned_by = str_replace("{POSTED_BY}", ucfirst($this->session->userdata('name')), $task_name);
        $Link = str_replace("{COMMENT_URL}", base_url() . 'admin/tasks/view_task_details/' . $tasks_info->task_id . '/' . $data['active'] = 2, $assigned_by);
        $comments = str_replace("{COMMENT_MESSAGE}", $tasks_comment_info->comment, $Link);
        $message = str_replace("{SITE_NAME}", config_item('company_name'), $comments);

        $data['message'] = $message;
        $message = $this->load->view('email_template', $data, TRUE);

        $params['subject'] = $subject;
        $params['message'] = $message;
        $params['resourceed_file'] = '';
        $users = unserialize($tasks_info->assigned_to);
        foreach ($users['assigned_to'] as $v_user) {
            $login_info = $this->tasks_model->check_by(array('user_id' => $v_user), 'tbl_users');
            $params['recipient'] = $login_info->email;
            $this->tasks_model->send_email($params);
        }
    }

    public function delete_task_comments($task_id, $task_comment_id) {
//save data into table.
        $this->tasks_model->_table_name = "tbl_task_comment"; // table name
        $this->tasks_model->_primary_key = "task_comment_id"; // $id
        $this->tasks_model->delete($task_comment_id);

        $type = "success";
        $message = lang('task_comment_deleted');
        set_message($type, $message);
        redirect('admin/tasks/view_task_details/' . $task_id . '/' . $data['active'] = 2);
    }

    public function delete_task_files($task_id, $task_attachment_id) {
        $file_info = $this->tasks_model->check_by(array('task_attachment_id' => $task_attachment_id), 'tbl_task_attachment');
// save into activities
        $activities = array(
            'user' => $this->session->userdata('user_id'),
            'module' => 'tasks',
            'module_field_id' => $task_id,
            'activity' => 'activity_task_attachfile_deleted',
            'icon' => 'fa-ticket',
            'value1' => $file_info->title,
        );
// Update into tbl_project
        $this->tasks_model->_table_name = "tbl_activities"; //table name
        $this->tasks_model->_primary_key = "activities_id";
        $this->tasks_model->save($activities);

//save data into table.
        $this->tasks_model->_table_name = "tbl_task_attachment"; // table name        
        $this->tasks_model->delete_multiple(array('task_attachment_id' => $task_attachment_id));

        $type = "success";
        $message = lang('task_attachfile_deleted');
        set_message($type, $message);
        redirect('admin/tasks/view_task_details/' . $task_id . '/' . $data['active'] = 3);
    }

    public function save_task_attachment($task_attachment_id = NULL) {
        $data = $this->tasks_model->array_from_post(array('title', 'description', 'task_id'));
        $data['user_id'] = $this->session->userdata('user_id');

// save and update into tbl_files
        $this->tasks_model->_table_name = "tbl_task_attachment"; //table name
        $this->tasks_model->_primary_key = "task_attachment_id";
        if (!empty($task_attachment_id)) {
            $id = $task_attachment_id;
            $this->tasks_model->save($data, $id);
            $msg = lang('task_file_updated');
        } else {
            $id = $this->tasks_model->save($data);
            $msg = lang('task_file_added');
        }

        if (!empty($_FILES['task_files']['name']['0'])) {
            $old_path_info = $this->input->post('uploaded_path');
            if (!empty($old_path_info)) {
                foreach ($old_path_info as $old_path) {
                    unlink($old_path);
                }
            }
            $mul_val = $this->tasks_model->multi_uploadAllType('task_files');

            foreach ($mul_val as $val) {
                $val == TRUE || redirect('admin/tasks/view_task_details/3/' . $data['task_id']);
                $fdata['files'] = $val['path'];
                $fdata['file_name'] = $val['fileName'];
                $fdata['uploaded_path'] = $val['fullPath'];
                $fdata['size'] = $val['size'];
                $fdata['ext'] = $val['ext'];
                $fdata['is_image'] = $val['is_image'];
                $fdata['image_width'] = $val['image_width'];
                $fdata['image_height'] = $val['image_height'];
                $fdata['task_attachment_id'] = $id;
                $this->tasks_model->_table_name = "tbl_task_uploaded_files"; // table name
                $this->tasks_model->_primary_key = "uploaded_files_id"; // $id
                $this->tasks_model->save($fdata);
            }
        }
// save into activities
        $activities = array(
            'user' => $this->session->userdata('user_id'),
            'module' => 'tasks',
            'module_field_id' => $data['task_id'],
            'activity' => 'activity_new_task_attachment',
            'icon' => 'fa-ticket',
            'value1' => $data['title'],
        );
// Update into tbl_project
        $this->tasks_model->_table_name = "tbl_activities"; //table name
        $this->tasks_model->_primary_key = "activities_id";
        $this->tasks_model->save($activities);
// send notification message
        $this->notify_attchemnt_tasks($id);
// messages for user
        $type = "success";
        $message = $msg;
        set_message($type, $message);
        redirect('admin/tasks/view_task_details/' . $data['task_id'] . '/3');
    }

    function notify_attchemnt_tasks($task_attachment_id) {
        $email_template = $this->tasks_model->check_by(array('email_group' => 'tasks_attachment'), 'tbl_email_templates');
        $tasks_comment_info = $this->tasks_model->check_by(array('task_attachment_id' => $task_attachment_id), 'tbl_task_attachment');

        $tasks_info = $this->tasks_model->check_by(array('task_id' => $tasks_comment_info->task_id), 'tbl_task');
        $message = $email_template->template_body;

        $subject = $email_template->subject;

        $task_name = str_replace("{TASK_NAME}", $tasks_info->task_name, $message);
        $assigned_by = str_replace("{UPLOADED_BY}", ucfirst($this->session->userdata('name')), $task_name);
        $Link = str_replace("{TASK_URL}", base_url() . 'admin/tasks/view_task_details/' . $tasks_info->task_id . '/' . $data['active'] = 3, $assigned_by);
        $message = str_replace("{SITE_NAME}", config_item('company_name'), $Link);

        $data['message'] = $message;
        $message = $this->load->view('email_template', $data, TRUE);

        $params['subject'] = $subject;
        $params['message'] = $message;
        $params['resourceed_file'] = '';
        $users = unserialize($tasks_info->assigned_to);
        foreach ($users['assigned_to'] as $v_user) {
            $login_info = $this->tasks_model->check_by(array('user_id' => $v_user), 'tbl_users');
            $params['recipient'] = $login_info->email;
            $this->tasks_model->send_email($params);
        }
    }

    public function view_task_details($id, $active = NULL, $edit = NULL) {
        if (!empty($edit)) {
            $tasks_timer_id = $id;
            $id = $this->db->where(array('tasks_timer_id' => $id))->get('tbl_tasks_timer')->row()->task_id;
        } else {
            $id = $id;
        }
        $data['title'] = lang('task_details');
        $data['page_header'] = lang('task_management');

//get all task information
        $data['task_details'] = $this->tasks_model->check_by(array('task_id' => $id), 'tbl_task');

//        //get all comments info
//        $data['comment_details'] = $this->tasks_model->get_all_comment_info($id);
// get all assign_user
        $this->tasks_model->_table_name = 'tbl_users';
        $this->tasks_model->_order_by = 'user_id';
        $data['assign_user'] = $this->tasks_model->get_by(array('role_id !=' => '2'), FALSE);

        $this->tasks_model->_table_name = "tbl_task_attachment"; //table name
        $this->tasks_model->_order_by = "task_id";
        $data['files_info'] = $this->tasks_model->get_by(array('task_id' => $id), FALSE);

        foreach ($data['files_info'] as $key => $v_files) {
            $this->tasks_model->_table_name = "tbl_task_uploaded_files"; //table name
            $this->tasks_model->_order_by = "task_attachment_id";
            $data['project_files_info'][$key] = $this->tasks_model->get_by(array('task_attachment_id' => $v_files->task_attachment_id), FALSE);
        }


        if ($active == 2) {
            $data['active'] = 2;
            $data['time_active'] = 1;
        } elseif ($active == 3) {
            $data['active'] = 3;
            $data['time_active'] = 1;
        } elseif ($active == 4) {
            $data['active'] = 4;
            $data['time_active'] = 1;
        } elseif ($active == 5) {
            $data['active'] = 5;
            if (!empty($edit)) {
                $data['time_active'] = 2;
                $data['tasks_timer_info'] = $this->tasks_model->check_by(array('tasks_timer_id' => $tasks_timer_id), 'tbl_tasks_timer');
            } else {
                $data['time_active'] = 1;
            }
        } else {
            $data['active'] = 1;
            $data['time_active'] = 1;
        }

        $data['subview'] = $this->load->view('admin/tasks/view_task', $data, TRUE);
        $this->load->view('admin/_layout_main', $data);
    }

    public function update_tasks_timer($id = NULL, $action = NULL) {
        if (!empty($action)) {
            $t_data['task_id'] = $this->db->where(array('tasks_timer_id' => $id))->get('tbl_tasks_timer')->row()->task_id;
            $activity = 'activity_delete_tasks_timesheet';
            $msg = lang('delete_timesheet');
        } else {
            $activity = ('activity_update_task_timesheet');
            $msg = lang('timer_update');
        }
        if ($action != 'delete_task_timmer') {
            $t_data = $this->tasks_model->array_from_post(array('task_id', 'start_date', 'start_time', 'end_date', 'end_time'));

            $data['start_time'] = strtotime($t_data['start_date'] . ' ' . $t_data['start_time']);
            $data['end_time'] = strtotime($t_data['end_date'] . ' ' . $t_data['end_time']);
            $data['reason'] = $this->input->post('reason', TRUE);
            $data['edited_by'] = $this->session->userdata('user_id');

            $data['task_id'] = $t_data['task_id'];
            $data['user_id'] = $this->session->userdata('user_id');

            $this->tasks_model->_table_name = "tbl_tasks_timer"; //table name
            $this->tasks_model->_primary_key = "tasks_timer_id";
            if (!empty($id)) {
                $id = $this->tasks_model->save($data, $id);
            } else {
                $id = $this->tasks_model->save($data);
            }
        } else {
            $this->tasks_model->_table_name = "tbl_tasks_timer"; //table name
            $this->tasks_model->_primary_key = "tasks_timer_id";
            $this->tasks_model->delete($id);
        }
        $task_info = $this->tasks_model->check_by(array('task_id' => $t_data['task_id']), 'tbl_task');
        // save into activities
        $activities = array(
            'user' => $this->session->userdata('user_id'),
            'module' => 'Tasks',
            'module_field_id' => $id,
            'activity' => $activity,
            'icon' => 'fa-users',
            'value1' => $task_info->task_name,
        );
        $this->tasks_model->_table_name = "tbl_activities"; //table name
        $this->tasks_model->_primary_key = "activities_id";
        $this->tasks_model->save($activities);
        $type = "success";
        $message = $msg;
        set_message($type, $message);
        redirect('admin/tasks/view_task_details/' . $t_data['task_id'] . '/5');
    }

    public function download_files($task_id, $uploaded_files_id) {
        $this->load->helper('download');
        $uploaded_files_info = $this->tasks_model->check_by(array('uploaded_files_id' => $uploaded_files_id), 'tbl_task_uploaded_files');

        if ($uploaded_files_info->uploaded_path) {
            $data = file_get_contents($uploaded_files_info->uploaded_path); // Read the file's contents            
            force_download($uploaded_files_info->file_name, $data);
        } else {
            $type = "error";
            $message = lang('operation_failed');
            set_message($type, $message);
            redirect('admin/tasks/view_task_details/' . $task_id . '/3');
        }
    }

    public function delete_task($id) {
        $task_info = $this->tasks_model->check_by(array('task_id' => $id), 'tbl_task');

// save into activities
        $activities = array(
            'user' => $this->session->userdata('user_id'),
            'module' => 'tasks',
            'module_field_id' => $task_info->task_id,
            'activity' => 'activity_task_deleted',
            'icon' => 'fa-ticket',
            'value1' => $task_info->task_name,
        );
// Update into tbl_project
        $this->tasks_model->_table_name = "tbl_activities"; //table name
        $this->tasks_model->_primary_key = "activities_id";
        $this->tasks_model->save($activities);

        $this->tasks_model->_table_name = "tbl_task_attachment"; //table name
        $this->tasks_model->_order_by = "task_id";
        $files_info = $this->tasks_model->get_by(array('task_id' => $id), FALSE);
        foreach ($files_info as $v_files) {
            $this->tasks_model->_table_name = "tbl_task_uploaded_files"; //table name
            $this->tasks_model->delete_multiple(array('task_attachment_id' => $v_files->task_attachment_id));
        }
//delete into table.
        $this->tasks_model->_table_name = "tbl_task_attachment"; // table name        
        $this->tasks_model->delete_multiple(array('task_id' => $id));

//delete data into table.
        $this->tasks_model->_table_name = "tbl_task_comment"; // table name        
        $this->tasks_model->delete_multiple(array('task_id' => $id));

        $this->tasks_model->_table_name = "tbl_task"; // table name
        $this->tasks_model->_primary_key = "task_id"; // $id
        $this->tasks_model->delete($id);

        $type = "success";
        $message = lang('task_deleted');
        set_message($type, $message);
        redirect('admin/tasks/all_task');
    }

    public function tasks_timer($status, $task_id, $details = NULL) {
        $task_start = $this->tasks_model->check_by(array('task_id' => $task_id), 'tbl_task');
        if ($status == 'off') {
// check this user start time or this user is admin
// if true then off time
// else do not off time
            $check_user = $this->timer_started_by($task_id);
            if ($check_user == TRUE) {

                $task_logged_time = $this->tasks_model->task_spent_time_by_id($task_id);
                $time_logged = (time() - $task_start->start_time) + $task_logged_time; //time already logged

                $data = array(
                    'timer_status' => $status,
                    'logged_time' => $time_logged,
                    'start_time' => ''
                );
// Update into tbl_task
                $this->tasks_model->_table_name = "tbl_task"; //table name
                $this->tasks_model->_primary_key = "task_id";
                $this->tasks_model->save($data, $task_id);
// save into tbl_task_timer 
                $t_data = array(
                    'task_id' => $task_id,
                    'user_id' => $this->session->userdata('user_id'),
                    'start_time' => $task_start->start_time,
                    'end_time' => time()
                );

// insert into tbl_task_timer
                $this->tasks_model->_table_name = "tbl_tasks_timer"; //table name
                $this->tasks_model->_primary_key = "tasks_timer_id";
                $this->tasks_model->save($t_data);

// save into activities
                $activities = array(
                    'user' => $this->session->userdata('user_id'),
                    'module' => 'Tasks',
                    'module_field_id' => $task_id,
                    'activity' => ('activity_tasks_timer_off'),
                    'icon' => 'fa-copy',
                    'value1' => $task_start->task_name,
                );
// Update into tbl_project
                $this->tasks_model->_table_name = "tbl_activities"; //table name
                $this->tasks_model->_primary_key = "activities_id";
                $this->tasks_model->save($activities);
            }
        } else {
            $data = array(
                'timer_status' => $status,
                'timer_started_by' => $this->session->userdata('user_id'),
                'start_time' => time()
            );

// save into activities
            $activities = array(
                'user' => $this->session->userdata('user_id'),
                'module' => 'Project',
                'module_field_id' => $task_id,
                'activity' => 'activity_tasks_timer_on',
                'icon' => 'fa-copy',
                'value1' => $task_start->task_name,
            );
// Update into tbl_project
            $this->tasks_model->_table_name = "tbl_activities"; //table name
            $this->tasks_model->_primary_key = "activities_id";
            $this->tasks_model->save($activities);

// Update into tbl_task
            $this->tasks_model->_table_name = "tbl_task"; //table name
            $this->tasks_model->_primary_key = "task_id";
            $this->tasks_model->save($data, $task_id);
        }
// messages for user
        $type = "success";
        $message = lang('task_timer_' . $status);
        set_message($type, $message);
        if (!empty($details)) {
            redirect('admin/tasks/view_task_details/' . $task_id);
        } else {
            redirect('admin/tasks/all_task/');
        }
    }

    public function timer_started_by($task_id) {
        $user_id = $this->session->userdata('user_id');
        $user_info = $this->tasks_model->check_by(array('user_id' => $user_id), 'tbl_users');
        $timer_started_info = $this->tasks_model->check_by(array('task_id' => $task_id), 'tbl_task');

        if ($timer_started_info->timer_started_by == $user_id || $user_info->role_id == '1') {
            return TRUE;
        } else {
            return FALSE;
        }
    }

}
