<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class User extends Admin_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('user_model');
    }

    /*     * * Create New User ** */

    public function create_user($id = null) {
        $data['title'] = 'Create User';

        if (!empty($id)) {
            $data['user_id'] = $id;
        } else {
            $data['user_id'] = null;
        }

        $this->user_model->_table_name = 'tbl_menu'; //table name
        $this->user_model->_order_by = 'menu_id';
        $menu_info = $this->user_model->get();

        foreach ($menu_info as $items) {
            $menu['parents'][$items->parent][] = $items;
        }

        $data['result'] = $this->buildChild(0, $menu);

        $this->user_model->_table_name = 'tbl_users'; //table name
        $this->user_model->_order_by = 'user_id';
        $data['user_login_details'] = $this->user_model->get_by(array('user_id' => $data['user_id']), true);

        if ($data['user_login_details']) {
            $role = $this->user_model->select_user_roll_by_id($data['user_id']);

            if ($role) {
                foreach ($role as $value) {
                    $result[$value->menu_id] = $value->menu_id;
                }

                $data['roll'] = $result;
            }
        } else {
            $data['user_login_details'] = $this->user_model->get_new_user();
        }

        $data['subview'] = $this->load->view('admin/user/create_user', $data, true);
        $this->load->view('admin/_layout_main', $data);
    }

    /*     * * User Permission Level tree Builder ** */

    public function buildChild($parent, $menu) {
        if (isset($menu['parents'][$parent])) {
            foreach ($menu['parents'][$parent] as $ItemID) {
                if (!isset($menu['parents'][$ItemID->menu_id])) {
                    $result[$ItemID->label] = $ItemID->menu_id;
                }
                if (isset($menu['parents'][$ItemID->menu_id])) {
                    $result[$ItemID->label][$ItemID->menu_id] = self::buildChild($ItemID->menu_id, $menu);
                }
            }
        }

        return $result;
    }

    public function user_list($action = NULL, $id = NULL) {

        $user_id = $id;

        if ($action == 'edit_user') {
            $data['active'] = 2;
            $this->user_model->_table_name = 'tbl_users'; //table name
            $this->user_model->_order_by = 'user_id';
            $data['login_info'] = $this->user_model->get_by(array('user_id' => $user_id), true);

            if ($data['login_info']) {
                $role = $this->user_model->select_user_roll_by_id($user_id);
                if ($role) {
                    foreach ($role as $value) {
                        $result[$value->menu_id] = $value->menu_id;
                    }

                    $data['roll'] = $result;
                }
            }
        } else {
            $data['active'] = 1;
        }
        $this->user_model->_table_name = 'tbl_menu'; //table name
        $this->user_model->_order_by = 'menu_id';
        $menu_info = $this->user_model->get();
        foreach ($menu_info as $items) {
            $menu['parents'][$items->parent][] = $items;
        }

        $data['result'] = $this->buildChild(0, $menu);


        $data['title'] = 'User List';

        $this->user_model->_table_name = 'tbl_client'; //table name
        $this->user_model->_order_by = 'client_id';
        $data['all_client_info'] = $this->user_model->get();

        // get all language
        $this->user_model->_table_name = 'tbl_languages';
        $this->user_model->_order_by = 'name';
        $data['languages'] = $this->user_model->get();

        $this->user_model->_table_name = 'tbl_users'; //table name
        $this->user_model->_order_by = 'user_id';
        $data['all_user_info'] = $this->user_model->get();

        $data['subview'] = $this->load->view('admin/user/user_list', $data, true);
        $this->load->view('admin/_layout_main', $data);
    }

    public function user_details($id) {
        $data['title'] = lang('user_details');
        $data['id'] = $id;
        $data['user_role'] = $this->user_model->select_user_roll_by_id($id);

        $data['subview'] = $this->load->view('admin/user/user_details', $data, TRUE);
        $this->load->view('admin/_layout_main', $data);
    }

    /*     * * Save New User ** */

    public function save_user() {

        $login_data = $this->user_model->array_from_post(array('username', 'email', 'role_id'));
        $user_id = $this->input->post('user_id', true);
        // update root category
        $where = array('username' => $login_data['username']);
        $email = array('email' => $login_data['email']);
        // duplicate value check in DB
        if (!empty($user_id)) { // if id exist in db update data
            $check_id = array('user_id !=' => $user_id);
        } else { // if id is not exist then set id as null
            $check_id = null;
        }
        // check whether this input data already exist or not
        $check_user = $this->user_model->check_update('tbl_users', $where, $check_id);
        $check_email = $this->user_model->check_update('tbl_users', $email, $check_id);
        if (!empty($check_user) || !empty($check_email)) { // if input data already exist show error alert
            if (!empty($check_user)) {
                $error = $login_data['username'];
            } else {
                $error = $login_data['email'];
            }
            // massage for user
            $type = 'error';
            $message = "<strong style='color:#000'>" . $error . '</strong>  ' . lang('already_exist');
        } else { // save and update query    
            $login_data['last_ip'] = $this->input->ip_address();


            if (empty($user_id)) {
                $password = $this->input->post('password', TRUE);
                $login_data['password'] = $this->hash($password);
                $login_data['online_status'] = 0;
            }

            //delete existing userroll by login id
            if (!empty($user_id)) {
                $this->user_model->_table_name = 'tbl_user_role'; //table name
                $this->user_model->_order_by = 'user_id';
                $this->user_model->_primary_key = 'user_role_id';
                $roll = $this->user_model->get_by(array('user_id' => $user_id), false);

                foreach ($roll as $v_roll) {
                    $this->user_model->delete($v_roll->user_role_id);
                }
            }

            $this->user_model->_table_name = 'tbl_users'; // table name
            $this->user_model->_primary_key = 'user_id'; // $id
            if (!empty($user_id)) {
                $id = $this->user_model->save($login_data, $user_id);
            } else {
                $id = $this->user_model->save($login_data);
            }

            $this->user_model->_table_name = 'tbl_user_role'; // table name
            $this->user_model->_primary_key = 'user_role_id'; // $id
            $menu = $this->user_model->array_from_post(array('menu'));
            if (!empty($menu['menu'])) {
                foreach ($menu as $v_menu) {
                    foreach ($v_menu as $value) {
                        $mdata['menu_id'] = $value;
                        $mdata['user_id'] = $id;
                        $this->user_model->save($mdata);
                    }
                }
            }

            // save into tbl_account details
            $profile_data = $this->user_model->array_from_post(array('fullname', 'company', 'locale', 'language', 'phone', 'mobile', 'skype', 'departments_id'));
            $account_details_id = $this->input->post('account_details_id', TRUE);
            if (!empty($_FILES['avatar']['name'])) {
                $val = $this->user_model->uploadImage('avatar');
                $val == TRUE || redirect('admin/user/user_list');
                $profile_data['avatar'] = $val['path'];
            }

            $profile_data['user_id'] = $id;

            $this->user_model->_table_name = 'tbl_account_details'; // table name
            $this->user_model->_primary_key = 'account_details_id'; // $id
            if (!empty($account_details_id)) {
                $this->user_model->save($profile_data, $account_details_id);
            } else {
                $this->user_model->save($profile_data);
            }

            $activities = array(
                'user' => $this->session->userdata('user_id'),
                'module' => 'user',
                'module_field_id' => $id,
                'activity' => 'activity_added_new_user',
                'icon' => 'fa-user',
                'value1' => $login_data['username']
            );
            $this->user_model->_table_name = 'tbl_activities';
            $this->user_model->_primary_key = "activities_id";
            $this->user_model->save($activities);

            if (!empty($user_id)) {
                $message = lang('update_user_info');
            } else {
                $message = lang('save_user_info');
            }
            $type = 'success';
        }
        set_message($type, $message);
        redirect('admin/user/user_list'); //redirect page
    }

    /*     * * Delete User ** */

    public function delete_user($id = null) {
        if (!empty($id)) {
            $id = $id;
            $user_id = $this->session->userdata('user_id');

            //checking login user trying delete his own account
            if ($id == $user_id) {
                //same user can not delete his own account
                // redirect with error msg
                $type = 'error';
                $message = 'Sorry You can not delete your own account!';
                set_message($type, $message);
                redirect('admin/user/user_list'); //redirect page
            } else {
                //delete procedure run
                // Check user in db or not
                $this->user_model->_table_name = 'tbl_users'; //table name
                $this->user_model->_order_by = 'user_id';
                $result = $this->user_model->get_by(array('user_id' => $id), true);

                if (!empty($result)) {
                    //delete user roll id
                    $this->user_model->_table_name = 'tbl_account_details';
                    $this->user_model->delete_multiple(array('user_id' => $id));

                    $this->user_model->_table_name = 'tbl_activities';
                    $this->user_model->delete_multiple(array('user' => $id));

                    $this->user_model->_table_name = 'tbl_payments';
                    $this->user_model->delete_multiple(array('paid_by' => $id));

                    // delete all tbl_quotations by id
                    $this->user_model->_table_name = 'tbl_quotations';
                    $this->user_model->_order_by = 'user_id';
                    $quotations_info = $this->user_model->get_by(array('user_id' => $id), FALSE);

                    if (!empty($quotations_info)) {
                        foreach ($quotations_info as $v_quotations) {
                            $this->user_model->_table_name = 'tbl_quotation_details';
                            $this->user_model->delete_multiple(array('quotations_id' => $v_quotations->quotations_id));
                        }
                    }
                    
                    $this->user_model->_table_name = 'tbl_quotations';
                    $this->user_model->delete_multiple(array('user_id' => $id));

                    $this->user_model->_table_name = 'tbl_quotationforms';
                    $this->user_model->delete_multiple(array('quotations_created_by_id' => $id));

                    $this->user_model->_table_name = 'tbl_users';
                    $this->user_model->delete_multiple(array('user_id' => $id));

                    $this->user_model->_table_name = 'tbl_user_role';
                    $this->user_model->delete_multiple(array('user_id' => $id));

                    $this->user_model->_table_name = 'tbl_inbox';
                    $this->user_model->delete_multiple(array('user_id' => $id));

                    $this->user_model->_table_name = 'tbl_sent';
                    $this->user_model->delete_multiple(array('user_id' => $id));

                    $this->user_model->_table_name = 'tbl_draft';
                    $this->user_model->delete_multiple(array('user_id' => $id));

                    $this->user_model->_table_name = 'tbl_tickets';
                    $this->user_model->delete_multiple(array('reporter' => $id));

                    $this->user_model->_table_name = 'tbl_tickets_replies';
                    $this->user_model->delete_multiple(array('replierid' => $id));
                    // xpm crm
                    // delete all leads by id
                    $this->user_model->_table_name = 'tbl_leads';
                    $this->user_model->_order_by = 'user_id';
                    $leads_info = $this->user_model->get_by(array('user_id' => $id), FALSE);
                    if (!empty($leads_info)) {
                        foreach ($leads_info as $v_leads) {
                            //delete data into table.
                            $this->user_model->_table_name = "tbl_calls"; // table name        
                            $this->user_model->delete_multiple(array('leads_id' => $v_leads->leads_id));

                            //delete data into table.
                            $this->user_model->_table_name = "tbl_mettings"; // table name        
                            $this->user_model->delete_multiple(array('leads_id' => $v_leads->leads_id));

                            //delete data into table.
                            $this->user_model->_table_name = "tbl_task_comment"; // table name        
                            $this->user_model->delete_multiple(array('leads_id' => $v_leads->leads_id));

                            $this->user_model->_table_name = "tbl_task_attachment"; //table name
                            $this->user_model->_order_by = "leads_id";
                            $files_info = $this->user_model->get_by(array('leads_id' => $v_leads->leads_id), FALSE);

                            if (!empty($files_info)) {
                                foreach ($files_info as $v_files) {
                                    //save data into table.
                                    $this->user_model->_table_name = "tbl_task_uploaded_files"; // table name        
                                    $this->user_model->delete_multiple(array('task_attachment_id' => $v_files->task_attachment_id));
                                }
                            }
                            //save data into table.
                            $this->user_model->_table_name = "tbl_task_attachment"; // table name        
                            $this->user_model->delete_multiple(array('leads_id' => $v_leads->leads_id));

                            $this->user_model->_table_name = 'tbl_leads';
                            $this->user_model->_primary_key = 'leads_id';
                            $this->user_model->delete($v_leads->leads_id);
                        }
                    }
                    //save data into table.
                    $this->user_model->_table_name = "tbl_milestones"; // table name        
                    $this->user_model->delete_multiple(array('user_id' => $id));
                    // todo
                    $this->user_model->_table_name = "tbl_todo"; // table name        
                    $this->user_model->delete_multiple(array('user_id' => $id));

                    // opportunity
                    $oppurtunity_info = $this->db->get('tbl_opportunities')->result();
                    if (!empty($oppurtunity_info)) {
                        foreach ($oppurtunity_info as $v_oppurtunity) {
                            $opp_assding_id = unserialize($v_oppurtunity->user_id);
                            if (!empty($opp_assding_id['user_id'])) {
                                foreach ($opp_assding_id['user_id'] as $op_user_id) {
                                    if ($op_user_id == $id)
                                    //delete data into table.
                                        $this->user_model->_table_name = "tbl_calls"; // table name        
                                    $this->user_model->delete_multiple(array('opportunities_id' => $v_oppurtunity->opportunities_id));

                                    //delete data into table.
                                    $this->user_model->_table_name = "tbl_mettings"; // table name        
                                    $this->user_model->delete_multiple(array('opportunities_id' => $v_oppurtunity->opportunities_id));

                                    //delete data into table.
                                    $this->user_model->_table_name = "tbl_task_comment"; // table name        
                                    $this->user_model->delete_multiple(array('opportunities_id' => $v_oppurtunity->opportunities_id));

                                    $this->user_model->_table_name = "tbl_task_attachment"; //table name
                                    $this->user_model->_order_by = "task_id";
                                    $files_info = $this->user_model->get_by(array('opportunities_id' => $v_oppurtunity->opportunities_id), FALSE);
                                    if (!empty($files_info)) {
                                        foreach ($files_info as $v_files) {
                                            //save data into table.
                                            $this->user_model->_table_name = "tbl_task_uploaded_files"; // table name        
                                            $this->user_model->delete_multiple(array('task_attachment_id' => $v_files->task_attachment_id));
                                        }
                                    }
                                    //save data into table.
                                    $this->user_model->_table_name = "tbl_task_attachment"; // table name        
                                    $this->user_model->delete_multiple(array('opportunities_id' => $v_oppurtunity->opportunities_id));

                                    //save data into table.
                                    $this->user_model->_table_name = "tbl_task"; // table name        
                                    $this->user_model->delete_multiple(array('opportunities_id' => $v_oppurtunity->opportunities_id));

                                    $this->user_model->_table_name = 'tbl_opportunities';
                                    $this->user_model->_primary_key = 'opportunities_id';
                                    $this->user_model->delete($v_oppurtunity->opportunities_id);
                                }
                            }
                        }
                    }
                    // project
                    $project_info = $this->db->get('tbl_project')->result();
                    if (!empty($project_info)) {
                        foreach ($project_info as $v_project) {
                            $assding_id = unserialize($v_project->assign_to);
                            if (!empty($assding_id['assign_to'])) {
                                foreach ($assding_id['assign_to'] as $user_id) {
                                    if ($user_id == $id) {
                                        //delete data into table.
                                        $this->user_model->_table_name = "tbl_task_comment"; // table name        
                                        $this->user_model->delete_multiple(array('project_id' => $v_project->project_id));

                                        $this->user_model->_table_name = "tbl_task_attachment"; //table name
                                        $this->user_model->_order_by = "task_id";
                                        $files_info = $this->user_model->get_by(array('project_id' => $v_project->project_id), FALSE);
                                        if (!empty($files_info)) {
                                            foreach ($files_info as $v_files) {
                                                //save data into table.
                                                $this->user_model->_table_name = "tbl_task_uploaded_files"; // table name        
                                                $this->user_model->delete_multiple(array('task_attachment_id' => $v_files->task_attachment_id));
                                            }
                                        }
                                        //save data into table.
                                        $this->user_model->_table_name = "tbl_task_attachment"; // table name        
                                        $this->user_model->delete_multiple(array('project_id' => $v_project->project_id));

                                        //save data into table.
                                        $this->user_model->_table_name = "tbl_milestones"; // table name        
                                        $this->user_model->delete_multiple(array('project_id' => $v_project->project_id));

                                        //save data into table.
                                        $this->user_model->_table_name = "tbl_task"; // table name        
                                        $this->user_model->delete_multiple(array('project_id' => $v_project->project_id));
                                        //save data into table.
                                        $this->user_model->_table_name = "tbl_bug"; // table name        
                                        $this->user_model->delete_multiple(array('project_id' => $v_project->project_id));

                                        $this->user_model->_table_name = 'tbl_project';
                                        $this->user_model->_primary_key = 'project_id';
                                        $this->user_model->delete($v_project->project_id);
                                    }
                                }
                            }
                        }
                    }

                    // tasks
                    $taskss_info = $this->db->get('tbl_task')->result();
                    if (!empty($taskss_info)) {
                        foreach ($taskss_info as $v_taskss) {
                            $tassding_id = unserialize($v_taskss->assigned_to);
                            if (!empty($tassding_id['assigned_to'])) {
                                foreach ($tassding_id['assigned_to'] as $task_user_id) {
                                    if ($task_user_id == $id) {

                                        $this->user_model->_table_name = "tbl_task_attachment"; //table name
                                        $this->user_model->_order_by = "task_id";
                                        $files_info = $this->user_model->get_by(array('task_id' => $v_taskss->task_id), FALSE);
                                        foreach ($files_info as $v_files) {
                                            $this->user_model->_table_name = "tbl_task_uploaded_files"; //table name
                                            $this->user_model->delete_multiple(array('task_attachment_id' => $v_files->task_attachment_id));
                                        }
                                        //delete into table.
                                        $this->user_model->_table_name = "tbl_task_attachment"; // table name        
                                        $this->user_model->delete_multiple(array('task_id' => $v_taskss->task_id));

                                        //delete data into table.
                                        $this->user_model->_table_name = "tbl_task_comment"; // table name        
                                        $this->user_model->delete_multiple(array('task_id' => $v_taskss->task_id));

                                        $this->user_model->_table_name = "tbl_task"; // table name
                                        $this->user_model->_primary_key = "task_id"; // $id
                                        $this->user_model->delete($v_taskss->task_id);
                                    }
                                }
                            }
                        }
                    }
                    //redirect successful msg
                    $type = 'success';
                    $message = 'User Delete Successfully!';
                    set_message($type, $message);
                    redirect('admin/user/user_list'); //redirect page
                } else {
                    //redirect error msg
                    $type = 'error';
                    $message = 'Sorry this user not find in database!';
                    set_message($type, $message);
                    redirect('admin/user/user_list'); //redirect page
                }
            }
        }
    }

    public function change_status($flag, $id) {
        // if flag == 1 it is active user else deactive user
        if ($flag == 1) {
            $msg = 'Active';
        } else {
            $msg = 'Deactive';
        }
        $where = array('user_id' => $id);
        $action = array('activated' => $flag);
        $this->user_model->set_action($where, $action, 'tbl_users');

        $activities = array(
            'user' => $this->session->userdata('user_id'),
            'module' => 'user',
            'module_field_id' => $id,
            'activity' => 'activity_change_status',
            'icon' => 'fa-user',
            'value1' => $flag,
        );
        $this->user_model->_table_name = 'tbl_activities';
        $this->user_model->_primary_key = "activities_id";
        $this->user_model->save($activities);

        $type = "success";
        $message = "User " . $msg . " Successfully!";
        set_message($type, $message);
        redirect('admin/user/user_list'); //redirect page
    }

    public function set_banned($flag, $id) {

        if ($flag == 1) {
            $msg = lang('banned');
            $action = array('activated' => 0, 'banned' => $flag, 'ban_reason' => $this->input->post('ban_reason', TRUE));
        } else {
            $msg = lang('unbanned');
            $action = array('activated' => 1, 'banned' => $flag);
        }
        $where = array('user_id' => $id);

        $this->user_model->set_action($where, $action, 'tbl_users');

        $activities = array(
            'user' => $this->session->userdata('user_id'),
            'module' => 'user',
            'module_field_id' => $id,
            'activity' => 'activity_change_status',
            'icon' => 'fa-user',
            'value1' => $flag,
        );
        $this->user_model->_table_name = 'tbl_activities';
        $this->user_model->_primary_key = "activities_id";
        $this->user_model->save($activities);

        $type = "success";
        $message = "User " . $msg . " Successfully!";
        set_message($type, $message);
        redirect('admin/user/user_list'); //redirect page
    }

    public function change_banned($id) {

        $data['user_id'] = $id;
        $data['modal_subview'] = $this->load->view('admin/user/_modal_banned_reson', $data, FALSE);
        $this->load->view('admin/_layout_modal', $data);
    }

    public function hash($string) {
        return hash('sha512', $string . config_item('encryption_key'));
    }

// crud for sidebar todo list
    function todo($task = '', $todo_id = '', $swap_with = '') {
        if ($task == 'add') {
            $this->add_todo();
        }
        if ($task == 'reload_incomplete_todo') {
            $this->get_incomplete_todo();
        }
        if ($task == 'mark_as_done') {
            $this->mark_todo_as_done($todo_id);
        }
        if ($task == 'mark_as_undone') {
            $this->mark_todo_as_undone($todo_id);
        }
        if ($task == 'swap') {

            $this->swap_todo($todo_id, $swap_with);
        }
        if ($task == 'delete') {
            $this->delete_todo($todo_id);
        }
        $todo['opened'] = 1;
        $this->session->set_userdata($todo);
        redirect('admin/dashboard/');
    }

    function add_todo() {
        $data['title'] = $this->input->post('title');
        $data['user_id'] = $this->session->userdata('user_id');

        $this->db->insert('tbl_todo', $data);
        $todo_id = $this->db->insert_id();

        $data['order'] = $todo_id;
        $this->db->where('todo_id', $todo_id);
        $this->db->update('tbl_todo', $data);
    }

    function mark_todo_as_done($todo_id = '') {
        $data['status'] = 1;
        $this->db->where('todo_id', $todo_id);
        $this->db->update('tbl_todo', $data);
    }

    function mark_todo_as_undone($todo_id = '') {
        $data['status'] = 0;
        $this->db->where('todo_id', $todo_id);
        $this->db->update('tbl_todo', $data);
    }

    function swap_todo($todo_id = '', $swap_with = '') {
        $counter = 0;
        $temp_order = $this->db->get_where('tbl_todo', array('todo_id' => $todo_id))->row()->order;
        $user = $this->session->userdata('user_id');

        // Move current todo up.
        if ($swap_with == 'up') {

            // Fetch all todo lists of current user in ascending order.
            $this->db->order_by('order', 'ASC');
            $todo_lists = $this->db->get_where('tbl_todo', array('user_id' => $user))->result_array();
            $array_length = count($todo_lists);

            // Create separate array for orders and todo_id's from above array.
            foreach ($todo_lists as $todo_list) {
                $id_list[] = $todo_list['todo_id'];
                $order_list[] = $todo_list['order'];
            }
        }

        // Move current todo down.
        if ($swap_with == 'down') {

            // Fetch all todo lists of current user in descending order.
            $this->db->order_by('order', 'DESC');
            $todo_lists = $this->db->get_where('tbl_todo', array('user_id' => $user))->result_array();
            $array_length = count($todo_lists);

            // Create separate array for orders and todo_id's from above array.
            foreach ($todo_lists as $todo_list) {
                $id_list[] = $todo_list['todo_id'];
                $order_list[] = $todo_list['order'];
            }
        }

        // Swap orders between current and next/previous todo.
        for ($i = 0; $i < $array_length; $i++) {
            if ($temp_order == $order_list[$i]) {
                if ($counter > 0) {
                    $swap_order = $order_list[$i - 1];
                    $swap_id = $id_list[$i - 1];

                    // Update order of current todo.
                    $data['order'] = $swap_order;
                    $this->db->where('todo_id', $todo_id);
                    $this->db->update('tbl_todo', $data);

                    // Update order of next/previous todo.
                    $data['order'] = $temp_order;
                    $this->db->where('todo_id', $swap_id);
                    $this->db->update('tbl_todo', $data);
                }
            } else
                $counter++;
        }
    }

    function delete_todo($todo_id = '') {
        $this->db->where('todo_id', $todo_id);
        $this->db->delete('tbl_todo');
    }

    function get_incomplete_todo() {
        $user = $this->session->userdata('user_id');
        $this->db->where('user_id', $user);
        $this->db->where('status', 0);
        $query = $this->db->get('tbl_todo');

        $incomplete_todo_number = $query->num_rows();
        if ($incomplete_todo_number > 0) {
            echo '<span class="badge badge-secondary">';
            echo $incomplete_todo_number;
            echo '</span>';
        }
    }

}
