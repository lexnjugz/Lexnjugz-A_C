<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Global_Controller extends Admin_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('global_model');
        $this->load->model('admin_model');
    }

    public function get_milestone_by_project_id($project_id) {
        $milestone_info = $this->db->where(array('project_id' => $project_id))->get('tbl_milestones')->result();
        if ($milestone_info) {
            foreach ($milestone_info as $v_milestone) {
                $HTML.="<option value='" . $v_milestone->milestones_id . "'>" . $v_milestone->milestone_name . "</option>";
            }
        }
        echo $HTML;
    }

    public function check_current_password($val) {
        $password = $this->hash($val);
        $check_dupliaction_id = $this->admin_model->check_by(array('password' => $password), 'tbl_users');
        $result = NULL;
        if (empty($check_dupliaction_id)) {
            $result = '<small style="padding-left:10px;color:red;font-size:10px">Your Entered Password Do Not Match !<small>';
        }
        echo $result;
    }

    public function check_existing_user_name($user_name, $user_id = null) {
        $result = $this->admin_model->check_user_name($user_name, $user_id);
        if ($result) {
            echo 'This User Name is Exist!';
        }
    }

    public function hash($string) {
        return hash('sha512', $string . config_item('encryption_key'));
    }

}
