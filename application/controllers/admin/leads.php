<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Leads extends Admin_Controller {

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
        $data['title'] = lang('all_leads');
        // get all assign_user
        $this->items_model->_table_name = 'tbl_users';
        $this->items_model->_order_by = 'user_id';
        $data['user_info'] = $this->items_model->get_by(array('role_id !=' => '2'), FALSE);
        if (!empty($id)) {
            $data['active'] = 2;
            $data['leads_info'] = $this->items_model->check_by(array('leads_id' => $id), 'tbl_leads');
        } else {
            $data['active'] = 1;
        }
        $data['subview'] = $this->load->view('admin/leads/all_leads', $data, TRUE);
        $this->load->view('admin/_layout_main', $data); //page load
    }

    public function saved_leads($id = NULL) {
        $this->items_model->_table_name = 'tbl_leads';
        $this->items_model->_primary_key = 'leads_id';

        $data = $this->items_model->array_from_post(array('client_id', 'lead_name', 'organization', 'lead_status_id','lead_source_id','contact_name', 'email', 'phone', 'mobile', 'address', 'city', 'state', 'country', 'facebook', 'skype', 'twitter', 'notes'));
        
        $user_id = serialize($this->items_model->array_from_post(array('user_id')));
        if (!empty($user_id)) {
            $data['user_id'] = $user_id;
        } else {
            $data['user_id'] = '-';
        }
        // update root category
        $where = array('client_id' => $data['client_id'], 'lead_name' => $data['lead_name']);
        // duplicate value check in DB
        if (!empty($id)) { // if id exist in db update data
            $leads_id = array('leads_id !=' => $id);
        } else { // if id is not exist then set id as null
            $leads_id = null;
        }

        // check whether this input data already exist or not
        $check_leads = $this->items_model->check_update('tbl_leads', $where, $leads_id);
        if (!empty($check_leads)) { // if input data already exist show error alert
            // massage for user
            $type = 'error';
            $msg = "<strong style='color:#000'>" . $data['lead_name'] . '</strong>  ' . lang('already_exist');
        } else { // save and update query                                    
            $return_id = $this->items_model->save($data, $id);
            if (!empty($id)) {
                $id = $id;
                $action = 'activity_update_leads';
                $msg = lang('update_leads');
            } else {
                $id = $return_id;
                $action = 'activity_save_leads';
                $msg = lang('save_leads');
            }
            $activity = array(
                'user' => $this->session->userdata('user_id'),
                'module' => 'leads',
                'module_field_id' => $id,
                'activity' => $action,
                'icon' => 'fa-circle-o',
                'value1' => $data['lead_name']
            );
            $this->items_model->_table_name = 'tbl_activities';
            $this->items_model->_primary_key = 'activities_id';
            $this->items_model->save($activity);
            // messages for user
            $type = "success";
        }
        $message = $msg;
        set_message($type, $message);
        redirect('admin/leads');
    }

    public function leads_details($id, $active = NULL, $op_id = NULL) {
        $data['title'] = lang('leads_details');
        $data['page_header'] = lang('task_management');
        //get all task information
        $data['leads_details'] = $this->items_model->check_by(array('leads_id' => $id), 'tbl_leads');

        $this->items_model->_table_name = "tbl_task_attachment"; //table name
        $this->items_model->_order_by = "task_id";
        $data['files_info'] = $this->items_model->get_by(array('leads_id' => $id), FALSE);

        foreach ($data['files_info'] as $key => $v_files) {
            $this->items_model->_table_name = "tbl_task_uploaded_files"; //table name
            $this->items_model->_order_by = "task_attachment_id";
            $data['project_files_info'][$key] = $this->items_model->get_by(array('task_attachment_id' => $v_files->task_attachment_id), FALSE);
        }

        if ($active == 2) {
            $data['active'] = 2;
            $data['sub_active'] = 1;
            $data['sub_metting'] = 1;
        } elseif ($active == 3) {
            $data['active'] = 3;
            $data['sub_active'] = 1;
            $data['sub_metting'] = 1;
        } elseif ($active == 4) {
            $data['active'] = 4;
            $data['sub_active'] = 1;
            $data['sub_metting'] = 1;
        } elseif ($active == 5) {
            $data['active'] = 5;
            $data['sub_active'] = 1;
            $data['sub_metting'] = 1;
        } elseif ($active == 'metting') {
            $data['active'] = 3;
            $data['sub_active'] = 1;
            $data['sub_metting'] = 2;
            $data['mettings_info'] = $this->items_model->check_by(array('mettings_id' => $op_id), 'tbl_mettings');
        } elseif ($active == 'call') {
            $data['active'] = 2;
            $data['sub_active'] = 2;
            $data['call_info'] = $this->items_model->check_by(array('calls_id' => $op_id), 'tbl_calls');
            $data['sub_metting'] = 1;
        } else {
            $data['active'] = 1;
            $data['sub_active'] = 1;
            $data['sub_metting'] = 1;
        }

        $data['subview'] = $this->load->view('admin/leads/leads_details', $data, TRUE);
        $this->load->view('admin/_layout_main', $data);
    }

    public function change_status($leads_id, $lead_status_id) {
        $data['lead_status_id'] = $lead_status_id;
        $this->items_model->_table_name = 'tbl_leads';
        $this->items_model->_primary_key = 'leads_id';
        $this->items_model->save($data, $leads_id);
        // messages for user
        $type = "success";
        $message = lang('change_status');
        set_message($type, $message);
        redirect('admin/leads/leads_details/' . $leads_id . '/' . '1');
    }

    public function saved_call($leads_id, $id = NULL) {
        $data = $this->items_model->array_from_post(array('date', 'call_summary', 'client_id', 'user_id'));
        $data['leads_id'] = $leads_id;
        $this->items_model->_table_name = 'tbl_calls';
        $this->items_model->_primary_key = 'calls_id';
        $return_id = $this->items_model->save($data, $id);
        if (!empty($id)) {
            $id = $id;
            $action = 'activity_update_leads_call';
            $msg = lang('update_leads_call');
        } else {
            $id = $return_id;
            $action = 'activity_save_leads_call';
            $msg = lang('save_leads_call');
        }
        $activity = array(
            'user' => $this->session->userdata('user_id'),
            'module' => 'leads',
            'module_field_id' => $id,
            'activity' => $action,
            'icon' => 'fa-circle-o',
            'value1' => $data['call_summary']
        );
        $this->items_model->_table_name = 'tbl_activities';
        $this->items_model->_primary_key = 'activities_id';
        $this->items_model->save($activity);
        // messages for user
        $type = "success";
        $message = $msg;
        set_message($type, $message);
        redirect('admin/leads/leads_details/' . $leads_id . '/' . '2');
    }

    public function delete_leads_call($leads_id, $id) {

        $calls_info = $this->items_model->check_by(array('calls_id' => $id), 'tbl_calls');
        $activity = array(
            'user' => $this->session->userdata('user_id'),
            'module' => 'leads',
            'module_field_id' => $id,
            'activity' => 'activity_leads_call_deleted',
            'icon' => 'fa-circle-o',
            'value1' => $calls_info->call_summary
        );
        $this->items_model->_table_name = 'tbl_activities';
        $this->items_model->_primary_key = 'activities_id';
        $this->items_model->save($activity);
        $this->items_model->_table_name = 'tbl_calls';
        $this->items_model->_primary_key = 'calls_id';
        $this->items_model->delete($id);
        $type = 'success';
        $message = lang('leads_call_deleted');
        set_message($type, $message);
        redirect('admin/leads/leads_details/' . $leads_id . '/' . '2');
    }

    public function saved_metting($leads_id, $id = NULL) {
        $this->items_model->_table_name = 'tbl_mettings';
        $this->items_model->_primary_key = 'mettings_id';

        $data = $this->items_model->array_from_post(array('meeting_subject', 'user_id', 'location', 'description'));
        $data['start_date'] = strtotime($this->input->post('start_date') . ' ' . date('H:i', strtotime($this->input->post('start_time'))));
        $data['end_date'] = strtotime($this->input->post('end_date') . ' ' . date('H:i', strtotime($this->input->post('end_time'))));
        $data['leads_id'] = $leads_id;
        $user_id = serialize($this->items_model->array_from_post(array('attendees')));
        if (!empty($user_id)) {
            $data['attendees'] = $user_id;
        } else {
            $data['attendees'] = '-';
        }
        $return_id = $this->items_model->save($data, $id);

        if (!empty($id)) {
            $id = $id;
            $action = 'activity_update_leads_metting';
            $msg = lang('update_leads_metting');
        } else {
            $id = $return_id;
            $action = 'activity_save_leads_metting';
            $msg = lang('save_leads_metting');
        }
        $activity = array(
            'user' => $this->session->userdata('user_id'),
            'module' => 'leads',
            'module_field_id' => $id,
            'activity' => $action,
            'icon' => 'fa-circle-o',
            'value1' => $data['meeting_subject']
        );

        $this->items_model->_table_name = 'tbl_activities';
        $this->items_model->_primary_key = 'activities_id';
        $this->items_model->save($activity);
        // messages for user
        $type = "success";
        $message = $msg;
        set_message($type, $message);
        redirect('admin/leads/leads_details/' . $leads_id . '/' . '3');
    }

    public function save_comments() {

        $data['leads_id'] = $this->input->post('leads_id', TRUE);
        $data['comment'] = $this->input->post('comment', TRUE);
        $data['user_id'] = $this->session->userdata('user_id');

        //save data into table.
        $this->items_model->_table_name = "tbl_task_comment"; // table name
        $this->items_model->_primary_key = "task_comment_id"; // $id
        $this->items_model->save($data);

        // save into activities
        $activities = array(
            'user' => $this->session->userdata('user_id'),
            'module' => 'leads',
            'module_field_id' => $data['leads_id'],
            'activity' => 'activity_new_leads_comment',
            'icon' => 'fa-ticket',
            'value1' => $data['comment'],
        );
        // Update into tbl_project
        $this->items_model->_table_name = "tbl_activities"; //table name
        $this->items_model->_primary_key = "activities_id";
        $this->items_model->save($activities);


        $type = "success";
        $message = lang('leads_comment_save');
        set_message($type, $message);
        redirect('admin/leads/leads_details/' . $data['leads_id'] . '/' . '4');
    }

    public function delete_comments($leads_id, $task_comment_id) {
        //save data into table.
        $this->items_model->_table_name = "tbl_task_comment"; // table name
        $this->items_model->_primary_key = "task_comment_id"; // $id
        $this->items_model->delete($task_comment_id);

        $type = "success";
        $message = lang('task_comment_deleted');
        set_message($type, $message);
        redirect('admin/leads/leads_details/' . $leads_id . '/' . '4');
    }

    public function save_attachment($task_attachment_id = NULL) {
        $data = $this->items_model->array_from_post(array('title', 'description', 'leads_id'));
        $data['user_id'] = $this->session->userdata('user_id');

        // save and update into tbl_files
        $this->items_model->_table_name = "tbl_task_attachment"; //table name
        $this->items_model->_primary_key = "task_attachment_id";
        if (!empty($task_attachment_id)) {
            $id = $task_attachment_id;
            $this->items_model->save($data, $id);
            $msg = lang('leads_file_updated');
        } else {
            $id = $this->items_model->save($data);
            $msg = lang('leads_file_added');
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
                $val == TRUE || redirect('admin/leads/leads_details/' . $data['leads_id'] . '/' . '5');
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
            'module' => 'leads',
            'module_field_id' => $data['task_id'],
            'activity' => 'activity_new_leads_attachment',
            'icon' => 'fa-ticket',
            'value1' => $data['title'],
        );
        // Update into tbl_project
        $this->items_model->_table_name = "tbl_activities"; //table name
        $this->items_model->_primary_key = "activities_id";
        $this->items_model->save($activities);
        // messages for user
        $type = "success";
        $message = $msg;
        set_message($type, $message);
        redirect('admin/leads/leads_details/' . $data['leads_id'] . '/' . '5');
    }

    public function delete_files($leads_id, $task_attachment_id) {
        $file_info = $this->items_model->check_by(array('task_attachment_id' => $task_attachment_id), 'tbl_task_attachment');
        // save into activities
        $activities = array(
            'user' => $this->session->userdata('user_id'),
            'module' => 'leads',
            'module_field_id' => $leads_id,
            'activity' => 'activity_leads_attachfile_deleted',
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

        $type = "success";
        $message = lang('leads_attachfile_deleted');
        set_message($type, $message);
        redirect('admin/leads/leads_details/' . $leads_id . '/' . '5');
    }

    public function delete_leads($id) {
        $leads_info = $this->items_model->check_by(array('leads_id' => $id), 'tbl_leads');

        $activity = array(
            'user' => $this->session->userdata('user_id'),
            'module' => 'leads',
            'module_field_id' => $id,
            'activity' => 'activity_leads_deleted',
            'icon' => 'fa-circle-o',
            'value1' => $leads_info->leads_name
        );
        $this->items_model->_table_name = 'tbl_activities';
        $this->items_model->_primary_key = 'activities_id';
        $this->items_model->save($activity);

        //delete data into table.
        $this->items_model->_table_name = "tbl_calls"; // table name        
        $this->items_model->delete_multiple(array('leads_id' => $id));

        //delete data into table.
        $this->items_model->_table_name = "tbl_mettings"; // table name        
        $this->items_model->delete_multiple(array('leads_id' => $id));

        //delete data into table.
        $this->items_model->_table_name = "tbl_task_comment"; // table name        
        $this->items_model->delete_multiple(array('leads_id' => $id));

        $this->items_model->_table_name = "tbl_task_attachment"; //table name
        $this->items_model->_order_by = "task_id";
        $files_info = $this->items_model->get_by(array('leads_id' => $id), FALSE);

        foreach ($files_info as $v_files) {
            //save data into table.
            $this->items_model->_table_name = "tbl_task_uploaded_files"; // table name        
            $this->items_model->delete_multiple(array('task_attachment_id' => $v_files->task_attachment_id));
        }
        //save data into table.
        $this->items_model->_table_name = "tbl_task_attachment"; // table name        
        $this->items_model->delete_multiple(array('leads_id' => $id));

        $this->items_model->_table_name = 'tbl_leads';
        $this->items_model->_primary_key = 'leads_id';
        $this->items_model->delete($id);
        
        $type = 'success';
        $message = lang('leads_deleted');
        set_message($type, $message);
        redirect('admin/leads');
    }

}
