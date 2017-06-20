<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Project extends Admin_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('items_model');

        $this->load->helper('ckeditor');
        $this->data['ckeditor'] = array(
            'id' => 'ck_editor',
            'path' => 'asset/js/ckeditor',
            'config' => array(
                'toolbar' => "Full",
                'width' => "99.8%",
                'height' => "400px"
            )
        );
    }

    public function index($id = NULL) {
        $data['title'] = lang('all_project');
        // get all assign_user
        $this->items_model->_table_name = 'tbl_users';
        $this->items_model->_order_by = 'user_id';
        $data['assign_user'] = $this->items_model->get_by(array('role_id !=' => '2'), FALSE);
        if (!empty($id)) {
            $data['active'] = 2;
            $data['project_info'] = $this->items_model->check_by(array('project_id' => $id), 'tbl_project');
        } else {
            $data['active'] = 1;
        }
        $data['subview'] = $this->load->view('admin/project/all_project', $data, TRUE);
        $this->load->view('admin/_layout_main', $data); //page load
    }

    public function saved_project($id = NULL) {
        $this->items_model->_table_name = 'tbl_project';
        $this->items_model->_primary_key = 'project_id';

        $data = $this->items_model->array_from_post(array('project_name', 'client_id', 'progress', 'start_date', 'end_date', 'project_cost', 'demo_url', 'description'));

        $user_id = $this->items_model->array_from_post(array('assign_to'));
        if (!empty($user_id['assign_to'])) {
            $data['assign_to'] = serialize($user_id);
        } else {
            set_message('error', lang('assigned_to') . ' Field is required');
            redirect('admin/project/2');
        }
        if (empty($id)) {
            $data['project_status'] = 'started';
        } else {
            $data['project_status'] = $this->input->post('project_status', TRUE);
        }
        // update root category
        $where = array('client_id' => $data['client_id'], 'project_name' => $data['project_name']);
        // duplicate value check in DB
        if (!empty($id)) { // if id exist in db update data
            $project_id = array('project_id !=' => $id);
        } else { // if id is not exist then set id as null
            $project_id = null;
        }

        // check whether this input data already exist or not
        $check_project = $this->items_model->check_update('tbl_project', $where, $project_id);
        if (!empty($check_project)) { // if input data already exist show error alert
            // massage for user
            $type = 'error';
            $msg = "<strong style='color:#000'>" . $data['project_name'] . '</strong>  ' . lang('already_exist');
        } else { // save and update query                                    
            $return_id = $this->items_model->save($data, $id);
            if (!empty($id)) {
                $id = $id;
                $action = 'activity_update_project';
                $msg = lang('update_project');
            } else {
                $id = $return_id;
                $action = 'activity_save_project';
                $msg = lang('save_project');
                $this->send_project_notify_client($return_id);
                $this->send_project_notify_assign_user($return_id, $user_id['assign_to']);
            }
            $activity = array(
                'user' => $this->session->userdata('user_id'),
                'module' => 'project',
                'module_field_id' => $id,
                'activity' => $action,
                'icon' => 'fa-circle-o',
                'value1' => $data['project_name']
            );
            $this->items_model->_table_name = 'tbl_activities';
            $this->items_model->_primary_key = 'activities_id';
            $this->items_model->save($activity);
            // messages for user
            $type = "success";
            if ($this->input->post('progress') == '100') {
                $this->send_project_notify_client($id, TRUE);
            }
        }
        $message = $msg;
        set_message($type, $message);
        redirect('admin/project');
    }

    public function send_project_notify_assign_user($project_id, $users) {

        $email_template = $this->items_model->check_by(array('email_group' => 'assigned_project'), 'tbl_email_templates');
        $project_info = $this->items_model->check_by(array('project_id' => $project_id), 'tbl_project');
        $message = $email_template->template_body;

        $subject = $email_template->subject;

        $project_name = str_replace("{PROJECT_NAME}", $project_info->project_name, $message);

        $assigned_by = str_replace("{ASSIGNED_BY}", ucfirst($this->session->userdata('name')), $project_name);
        $Link = str_replace("{PROJECT_LINK}", base_url() . 'admin/project/project_details/' . $project_id, $assigned_by);
        $message = str_replace("{SITE_NAME}", config_item('company_name'), $Link);

        $data['message'] = $message;
        $message = $this->load->view('email_template', $data, TRUE);

        $params['subject'] = $subject;
        $params['message'] = $message;
        $params['resourceed_file'] = '';

        foreach ($users as $v_user) {
            $login_info = $this->items_model->check_by(array('user_id' => $v_user), 'tbl_users');
            $params['recipient'] = $login_info->email;
            $this->items_model->send_email($params);
        }
    }

    public function send_project_notify_client($project_id, $complete = NULL) {
        if (!empty($complete)) {
            $email_template = $this->items_model->check_by(array('email_group' => 'complete_projects'), 'tbl_email_templates');
        } else {
            $email_template = $this->items_model->check_by(array('email_group' => 'client_notification'), 'tbl_email_templates');
        }
        $project_info = $this->items_model->check_by(array('project_id' => $project_id), 'tbl_project');
        $client_info = $this->items_model->check_by(array('client_id' => $project_info->client_id), 'tbl_client');
        $message = $email_template->template_body;

        $subject = $email_template->subject;

        $clientName = str_replace("{CLIENT_NAME}", $client_info->name, $message);
        $project_name = str_replace("{PROJECT_NAME}", $project_info->project_name, $clientName);

        $Link = str_replace("{PROJECT_LINK}", base_url() . 'admin/project/project_details/' . $project_id, $project_name);
        $message = str_replace("{SITE_NAME}", config_item('company_name'), $Link);

        $data['message'] = $message;
        $message = $this->load->view('email_template', $data, TRUE);

        $params['subject'] = $subject;
        $params['message'] = $message;
        $params['resourceed_file'] = '';

        $params['recipient'] = $client_info->email;
        $this->items_model->send_email($params);
    }

    public function invoice($id) {
        echo config_item('invoice_prefix');
        if (config_item('increment_invoice_number') == 'FALSE') {
            $this->load->helper('string');
            $reference_no = random_string('nozero', 6);
        } else {
            $reference_no = $this->items_model->generate_invoice_number();
        }
        $this->items_model->_table_name = "tbl_project"; //table name
        $this->items_model->_order_by = "project_id";
        $project_info = $this->items_model->get_by(array('project_id' => $id), TRUE);

        $currency = $this->items_model->client_currency_sambol($project_info->client_id);
        // save into invoice table
        $new_invoice = array(
            'reference_no' => $reference_no,
            'client_id' => $project_info->client_id,
            'currency' => $currency->code,
            'due_date' => $project_info->end_date,
        );
        $this->items_model->_table_name = "tbl_invoices"; //table name
        $this->items_model->_primary_key = "invoices_id";
        $new_invoice_id = $this->items_model->save($new_invoice);

        $items = array(
            'invoices_id' => $new_invoice_id,
            'item_name' => $project_info->project_name,
            'item_desc' => $project_info->description,
            'unit_cost' => $project_info->project_cost,
            'quantity' => 1,
            'total_cost' => $project_info->project_cost,
        );
        $this->items_model->_table_name = "tbl_items"; //table name
        $this->items_model->_primary_key = "items_id";
        $this->items_model->save($items);

        // save into activities
        $activities = array(
            'user' => $this->session->userdata('user_id'),
            'module' => 'invoice',
            'module_field_id' => $new_invoice_id,
            'activity' => lang('activity_new_invoice_form_project'),
            'icon' => 'fa-copy',
            'value1' => $reference_no,
        );
        // Update into tbl_project
        $this->items_model->_table_name = "tbl_activities"; //table name
        $this->items_model->_primary_key = "activities_id";
        $this->items_model->save($activities);

        // messages for user
        $type = "success";
        $message = lang('invoice_created');
        set_message($type, $message);
        redirect('admin/invoice/manage_invoice/invoice_details/' . $new_invoice_id);
    }

    public function project_details($id, $active = NULL, $op_id = NULL) {
        $data['title'] = lang('project_details');
        $data['page_header'] = lang('task_management');
        //get all task information
        $data['project_details'] = $this->items_model->check_by(array('project_id' => $id), 'tbl_project');

        $this->items_model->_table_name = "tbl_task_attachment"; //table name
        $this->items_model->_order_by = "project_id";
        $data['files_info'] = $this->items_model->get_by(array('project_id' => $id), FALSE);

        if (!empty($data['files_info'])) {
            foreach ($data['files_info'] as $key => $v_files) {
                $this->items_model->_table_name = "tbl_task_uploaded_files"; //table name
                $this->items_model->_order_by = "task_attachment_id";
                $data['project_files_info'][$key] = $this->items_model->get_by(array('task_attachment_id' => $v_files->task_attachment_id), FALSE);
            }
        }
        if ($active == 2) {
            $data['active'] = 2;
            $data['miles_active'] = 1;
            $data['task_active'] = 1;
            $data['bugs_active'] = 1;
        } elseif ($active == 3) {
            $data['active'] = 3;
            $data['miles_active'] = 1;
            $data['task_active'] = 1;
            $data['bugs_active'] = 1;
        } elseif ($active == 4) {
            $data['active'] = 4;
            $data['miles_active'] = 1;
            $data['task_active'] = 1;
            $data['bugs_active'] = 1;
        } elseif ($active == 5) {
            $data['active'] = 5;
            $data['miles_active'] = 1;
            $data['task_active'] = 1;
            $data['bugs_active'] = 1;
        } elseif ($active == 'milestone') {
            $data['active'] = 5;
            $data['miles_active'] = 2;
            $data['task_active'] = 1;
            $data['bugs_active'] = 1;
            $data['milestones_info'] = $this->items_model->check_by(array('milestones_id' => $op_id), 'tbl_milestones');
        } elseif ($active == 6) {
            $data['active'] = 6;
            $data['miles_active'] = 2;
            $data['task_active'] = 1;
            $data['bugs_active'] = 1;
        } else {
            $data['active'] = 1;
            $data['miles_active'] = 1;
            $data['task_active'] = 1;
            $data['bugs_active'] = 1;
        }

        $data['subview'] = $this->load->view('admin/project/project_details', $data, TRUE);
        $this->load->view('admin/_layout_main', $data);
    }

    public function change_status($project_id, $status) {
        $uri = $this->uri->segment(3);

        $data['project_status'] = $status;
        $this->items_model->_table_name = 'tbl_project';
        $this->items_model->_primary_key = 'project_id';
        $this->items_model->save($data, $project_id);
        // messages for user
        $type = "success";
        $message = lang('change_status');
        set_message($type, $message);
        if (!empty($uri)) {
            redirect('admin/project/project_details/' . $project_id);
        } else {
            redirect('admin/project/');
        }
    }

    public function save_comments() {

        $data['project_id'] = $this->input->post('project_id', TRUE);
        $data['comment'] = $this->input->post('comment', TRUE);
        $data['user_id'] = $this->session->userdata('user_id');

        //save data into table.
        $this->items_model->_table_name = "tbl_task_comment"; // table name
        $this->items_model->_primary_key = "task_comment_id"; // $id
        $comment_id = $this->items_model->save($data);

        // save into activities
        $activities = array(
            'user' => $this->session->userdata('user_id'),
            'module' => 'project',
            'module_field_id' => $data['project_id'],
            'activity' => 'activity_new_project_comment',
            'icon' => 'fa-ticket',
            'value1' => $data['comment'],
        );
        // Update into tbl_project
        $this->items_model->_table_name = "tbl_activities"; //table name
        $this->items_model->_primary_key = "activities_id";
        $this->items_model->save($activities);

// send notification
        $this->notify_comments_project($comment_id);

        $type = "success";
        $message = lang('project_comment_save');
        set_message($type, $message);
        redirect('admin/project/project_details/' . $data['project_id'] . '/' . '3');
    }

    function notify_comments_project($comment_id) {

        $email_template = $this->items_model->check_by(array('email_group' => 'project_comments'), 'tbl_email_templates');
        $comment_info = $this->items_model->check_by(array('task_comment_id' => $comment_id), 'tbl_task_comment');

        $project_info = $this->items_model->check_by(array('project_id' => $comment_info->project_id), 'tbl_project');
        $message = $email_template->template_body;

        $subject = $email_template->subject;

        $projectName = str_replace("{PROJECT_NAME}", $project_info->project_name, $message);
        $assigned_by = str_replace("{POSTED_BY}", ucfirst($this->session->userdata('name')), $projectName);
        $Link = str_replace("{COMMENT_URL}", base_url() . 'admin/project/project_details/' . $project_info->project_id . '/' . $data['active'] = 3, $assigned_by);
        $comments = str_replace("{COMMENT_MESSAGE}", $comment_info->comment, $Link);
        $message = str_replace("{SITE_NAME}", config_item('company_name'), $comments);

        $data['message'] = $message;
        $message = $this->load->view('email_template', $data, TRUE);

        $params['subject'] = $subject;
        $params['message'] = $message;
        $params['resourceed_file'] = '';
        $users = unserialize($project_info->assign_to);

        foreach ($users['assign_to'] as $v_user) {
            $login_info = $this->items_model->check_by(array('user_id' => $v_user), 'tbl_users');
            $params['recipient'] = $login_info->email;
            $this->items_model->send_email($params);
        }
    }

    public function delete_comments($project_id, $task_comment_id) {
        //save data into table.
        $this->items_model->_table_name = "tbl_task_comment"; // table name
        $this->items_model->_primary_key = "task_comment_id"; // $id
        $this->items_model->delete($task_comment_id);

        $type = "success";
        $message = lang('task_comment_deleted');
        set_message($type, $message);
        redirect('admin/project/project_details/' . $project_id . '/' . '3');
    }

    public function save_attachment($task_attachment_id = NULL) {
        $data = $this->items_model->array_from_post(array('title', 'description', 'project_id'));
        $data['user_id'] = $this->session->userdata('user_id');

        // save and update into tbl_files
        $this->items_model->_table_name = "tbl_task_attachment"; //table name
        $this->items_model->_primary_key = "task_attachment_id";
        if (!empty($task_attachment_id)) {
            $id = $task_attachment_id;
            $this->items_model->save($data, $id);
            $msg = lang('project_file_updated');
        } else {
            $id = $this->items_model->save($data);
            $msg = lang('project_file_added');
        }

        if (!empty($_FILES['task_files']['name']['0'])) {
            $old_path_info = $this->input->post('uploaded_path');
            if (!empty($old_path_info)) {
                foreach ($old_path_info as $old_path) {
                    unlink($old_path);
                }
            }
            $mul_val = $this->items_model->multi_uploadAllType('task_files');

            foreach ($mul_val as $val) {
                $val == TRUE || redirect('admin/project/project_details/' . $data['project_id'] . '/' . '4');
                $fdata['files'] = $val['path'];
                $fdata['file_name'] = $val['fileName'];
                $fdata['uploaded_path'] = $val['fullPath'];
                $fdata['size'] = $val['size'];
                $fdata['ext'] = $val['ext'];
                $fdata['is_image'] = $val['is_image'];
                $fdata['image_width'] = $val['image_width'];
                $fdata['image_height'] = $val['image_height'];
                $fdata['task_attachment_id'] = $id;
                $this->items_model->_table_name = "tbl_task_uploaded_files"; // table name
                $this->items_model->_primary_key = "uploaded_files_id"; // $id
                $this->items_model->save($fdata);
            }
        }
        // save into activities
        $activities = array(
            'user' => $this->session->userdata('user_id'),
            'module' => 'project',
            'module_field_id' => $data['project_id'],
            'activity' => 'activity_new_project_attachment',
            'icon' => 'fa-ticket',
            'value1' => $data['title'],
        );
        // Update into tbl_project
        $this->items_model->_table_name = "tbl_activities"; //table name
        $this->items_model->_primary_key = "activities_id";
        $this->items_model->save($activities);
        // send notification message
        $this->notify_attchemnt_project($id);
        // messages for user
        $type = "success";
        $message = $msg;
        set_message($type, $message);
        redirect('admin/project/project_details/' . $data['project_id'] . '/' . '4');
    }

    function notify_attchemnt_project($task_attachment_id) {
        $email_template = $this->items_model->check_by(array('email_group' => 'project_attachment'), 'tbl_email_templates');
        $comment_info = $this->items_model->check_by(array('task_attachment_id' => $task_attachment_id), 'tbl_task_attachment');

        $project_info = $this->items_model->check_by(array('project_id' => $comment_info->project_id), 'tbl_project');
        $message = $email_template->template_body;

        $subject = $email_template->subject;
        $projectName = str_replace("{PROJECT_NAME}", $project_info->project_name, $message);
        $assigned_by = str_replace("{UPLOADED_BY}", ucfirst($this->session->userdata('name')), $projectName);
        $Link = str_replace("{PROJECT_URL}", base_url() . 'admin/project/project_details/' . $comment_info->project_id . '/' . $data['active'] = 4, $assigned_by);
        $message = str_replace("{SITE_NAME}", config_item('company_name'), $Link);

        $data['message'] = $message;
        $message = $this->load->view('email_template', $data, TRUE);

        $params['subject'] = $subject;
        $params['message'] = $message;
        $params['resourceed_file'] = '';
        $users = unserialize($project_info->assign_to);
        foreach ($users['assign_to'] as $v_user) {
            $login_info = $this->items_model->check_by(array('user_id' => $v_user), 'tbl_users');
            $params['recipient'] = $login_info->email;
            $this->items_model->send_email($params);
        }
    }

    public function delete_files($project_id, $task_attachment_id) {
        $file_info = $this->items_model->check_by(array('task_attachment_id' => $task_attachment_id), 'tbl_task_attachment');
        // save into activities
        $activities = array(
            'user' => $this->session->userdata('user_id'),
            'module' => 'project',
            'module_field_id' => $project_id,
            'activity' => 'activity_project_attachfile_deleted',
            'icon' => 'fa-ticket',
            'value1' => $file_info->title,
        );
        // Update into tbl_project
        $this->items_model->_table_name = "tbl_activities"; //table name
        $this->items_model->_primary_key = "activities_id";
        $this->items_model->save($activities);

        //save data into table.
        $this->items_model->_table_name = "tbl_task_attachment"; // table name        
        $this->items_model->delete_multiple(array('task_attachment_id' => $task_attachment_id));

        //save data into table.
        $this->items_model->_table_name = "tbl_task_uploaded_files"; // table name        
        $this->items_model->delete_multiple(array('task_attachment_id' => $task_attachment_id));

        $type = "success";
        $message = lang('project_attachfile_deleted');
        set_message($type, $message);
        redirect('admin/project/project_details/' . $project_id . '/' . '4');
    }

    public function save_milestones($milestones_id = NULL) {
        $data = $this->items_model->array_from_post(array('project_id', 'milestone_name', 'description', 'start_date', 'end_date', 'user_id'));
        // Update into tbl_project
        $this->items_model->_table_name = "tbl_milestones"; //table name
        $this->items_model->_primary_key = "milestones_id";
        if (!empty($milestones_id)) {
            $id = $milestones_id;
            $this->items_model->save($data, $milestones_id);
            $action = lang('activity_updated_milestones');
            $msg = lang('update_milestone');
        } else {
            $id = $this->items_model->save($data);
            $action = lang('activity_added_new_milestones');
            $msg = lang('create_milestone');
        }
        // save into activities
        $activities = array(
            'user' => $this->session->userdata('user_id'),
            'module' => 'project',
            'module_field_id' => $id,
            'activity' => $action,
            'icon' => 'fa-rocket',
            'value1' => $data['milestone_name'],
        );
        // Update into tbl_project
        $this->items_model->_table_name = "tbl_activities"; //table name
        $this->items_model->_primary_key = "activities_id";
        $this->items_model->save($activities);
        $this->send_project_notify_milestone($id);
        // messages for user
        $type = "success";
        $message = $msg;
        set_message($type, $message);
        redirect('admin/project/project_details/' . $data['project_id'] . '/' . '5');
    }

    public function send_project_notify_milestone($milestones_id) {

        $email_template = $this->items_model->check_by(array('email_group' => 'responsible_milestone'), 'tbl_email_templates');
        $milestone_info = $this->items_model->check_by(array('milestones_id' => $milestones_id), 'tbl_milestones');
        $project_info = $this->items_model->check_by(array('project_id' => $milestone_info->project_id), 'tbl_project');
        $user_info = $this->items_model->check_by(array('user_id' => $milestone_info->user_id), 'tbl_users');
        $message = $email_template->template_body;

        $subject = $email_template->subject;

        $milestone = str_replace("{MILESTONE_NAME}", $milestone_info->milestone_name, $message);
        $assigned_by = str_replace("{ASSIGNED_BY}", ucfirst($this->session->userdata('name')), $milestone);
        $project_name = str_replace("{PROJECT_NAME}", $project_info->project_name, $assigned_by);

        $Link = str_replace("{PROJECT_URL}", base_url() . 'admin/project/project_details/' . $milestone_info->project_id . '/' . $data['active'] = 5, $project_name);
        $message = str_replace("{SITE_NAME}", config_item('company_name'), $Link);

        $data['message'] = $message;
        $message = $this->load->view('email_template', $data, TRUE);

        $params['subject'] = $subject;
        $params['message'] = $message;
        $params['resourceed_file'] = '';

        $params['recipient'] = $user_info->email;
        $this->items_model->send_email($params);
    }

    public function delete_milestones($project_id, $milestones_id) {

        $this->items_model->_table_name = "tbl_milestones"; //table name
        $this->items_model->_order_by = "milestones_id";
        $milestones_info = $this->items_model->get_by(array('milestones_id' => $milestones_id), TRUE);
        // save into activities
        $activities = array(
            'user' => $this->session->userdata('user_id'),
            'module' => 'project',
            'module_field_id' => $project_id,
            'activity' => lang('activity_delete_milestones'),
            'icon' => 'fa-rocket',
            'value1' => $milestones_info->milestone_name,
        );
        // Update into tbl_project
        $this->items_model->_table_name = "tbl_activities"; //table name
        $this->items_model->_primary_key = "activities_id";
        $this->items_model->save($activities);

        //save data into table.
        $this->items_model->_table_name = "tbl_milestones"; // table name        
        $this->items_model->delete_multiple(array('milestones_id' => $milestones_id));

        // delete into tbl_milestones
        $this->items_model->_table_name = "tbl_milestones"; //table name
        $this->items_model->_primary_key = "milestones_id";
        $this->items_model->delete($milestones_id);
        // Update into tbl_tasks

        $this->items_model->_table_name = "tbl_task"; //table name                
        $this->items_model->delete_multiple(array('milestones_id' => $milestones_id));
        // messages for user
        $type = "success";
        $message = lang('delete_milestone');
        set_message($type, $message);
        redirect('admin/project/project_details/' . $project_id . '/' . '5');
    }

    public function delete_project($id) {
        $project_info = $this->items_model->check_by(array('project_id' => $id), 'tbl_project');
        $activity = array(
            'user' => $this->session->userdata('user_id'),
            'module' => 'project',
            'module_field_id' => $id,
            'activity' => 'activity_project_deleted',
            'icon' => 'fa-circle-o',
            'value1' => $project_info->project_name
        );
        $this->items_model->_table_name = 'tbl_activities';
        $this->items_model->_primary_key = 'activities_id';
        $this->items_model->save($activity);

        //delete data into table.
        $this->items_model->_table_name = "tbl_task_comment"; // table name        
        $this->items_model->delete_multiple(array('project_id' => $id));

        $this->items_model->_table_name = "tbl_task_attachment"; //table name
        $this->items_model->_order_by = "project_id";
        $files_info = $this->items_model->get_by(array('project_id' => $id), FALSE);

        foreach ($files_info as $v_files) {
            //save data into table.
            $this->items_model->_table_name = "tbl_task_uploaded_files"; // table name        
            $this->items_model->delete_multiple(array('task_attachment_id' => $v_files->task_attachment_id));
        }
        //save data into table.
        $this->items_model->_table_name = "tbl_task_attachment"; // table name        
        $this->items_model->delete_multiple(array('project_id' => $id));

        //save data into table.
        $this->items_model->_table_name = "tbl_milestones"; // table name        
        $this->items_model->delete_multiple(array('project_id' => $id));

        //save data into table.
        $this->items_model->_table_name = "tbl_task"; // table name        
        $this->items_model->delete_multiple(array('project_id' => $id));
        //save data into table.
        $this->items_model->_table_name = "tbl_bug"; // table name        
        $this->items_model->delete_multiple(array('project_id' => $id));

        $this->items_model->_table_name = 'tbl_project';
        $this->items_model->_primary_key = 'project_id';
        $this->items_model->delete($id);
        $type = 'success';
        $message = lang('project_deleted');
        set_message($type, $message);
        redirect('admin/project');
    }

}
