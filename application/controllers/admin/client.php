<?php

/**
 * Description of client
 *
 * @author NaYeM
 */
class Client extends Admin_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('client_model');
        $this->load->model('invoice_model');
    }

    public function new_client($id = NULL) {
        $data['title'] = "New Client"; //Page title        
        $data['page'] = lang('client');
        $data['sub_active'] = lang('new_client');
        if (!empty($id)) {
            // get all Client info by client id
            $this->client_model->_table_name = "tbl_client"; //table name
            $this->client_model->_order_by = "client_id";
            $data['client_info'] = $this->client_model->get_by(array('client_id' => $id), TRUE);
        }
        if (!empty($data['client_info']) && $data['client_info']->client_status == 2) {
            $data['company'] = 1;
        } else {
            $data['person'] = 1;
        }
        // get all country
        $this->client_model->_table_name = "tbl_countries"; //table name
        $this->client_model->_order_by = "id";
        $data['countries'] = $this->client_model->get();

        // get all currencies
        $this->client_model->_table_name = 'tbl_currencies';
        $this->client_model->_order_by = 'name';
        $data['currencies'] = $this->client_model->get();
        // get all language
        $this->client_model->_table_name = 'tbl_languages';
        $this->client_model->_order_by = 'name';
        $data['languages'] = $this->client_model->get();
        // get all client
        $this->client_model->_table_name = 'tbl_client';
        $this->client_model->_order_by = 'client_id';
        $data['all_client_info'] = $this->client_model->get();

        $data['subview'] = $this->load->view('admin/client/new_client', $data, TRUE);
        $this->load->view('admin/_layout_main', $data); //page load
    }

    public function save_client($id = NULL) {

        $data = $this->client_model->array_from_post(array('name', 'email', 'short_note', 'website', 'phone', 'mobile', 'fax', 'address', 'city', 'zipcode', 'currency',
            'skype_id', 'linkedin', 'facebook', 'twitter', 'language', 'country', 'vat', 'hosting_company', 'hostname', 'port', 'password', 'username', 'client_status'));
        if (!empty($_FILES['profile_photo']['name'])) {
            $val = $this->client_model->uploadImage('profile_photo');
            $val == TRUE || redirect('admin/client/new_client');
            $data['profile_photo'] = $val['path'];
        }

        $this->client_model->_table_name = 'tbl_client';
        $this->client_model->_primary_key = "client_id";
        $return_id = $this->client_model->save($data, $id);
        if (!empty($id)) {
            $id = $id;
            $action = lang('activity_added_new_company');
        } else {
            $id = $return_id;
            $action = lang('activity_update_company');
        }
        $activities = array(
            'user' => $this->session->userdata('user_id'),
            'module' => 'client',
            'module_field_id' => $id,
            'activity' => $action,
            'icon' => 'fa-user',
            'value1' => $data['name']
        );
        $this->client_model->_table_name = 'tbl_activities';
        $this->client_model->_primary_key = "activities_id";
        $this->client_model->save($activities);
        // messages for user
        $type = "success";
        $message = lang('client_updated');
        set_message($type, $message);
        redirect('admin/client/manage_client');
    }

    public function manage_client() {
        $data['title'] = "Manage Client"; //Page title        
        $data['page'] = lang('client');
        $data['sub_active'] = lang('manage_client');

        // get all client
        $this->client_model->_table_name = 'tbl_client';
        $this->client_model->_order_by = 'client_id';
        $data['all_client_info'] = $this->client_model->get();

        $data['subview'] = $this->load->view('admin/client/manage_client', $data, TRUE);
        $this->load->view('admin/_layout_main', $data); //page load
    }

    public function client_details($id) {
        $data['title'] = "View Client Details"; //Page title        
        // get all client details
        $this->client_model->_table_name = "tbl_client"; //table name
        $this->client_model->_order_by = "client_id";
        $data['client_details'] = $this->client_model->get_by(array('client_id' => $id), TRUE);

        // get all invoice by client id
        $this->client_model->_table_name = "tbl_invoices"; //table name
        $this->client_model->_order_by = "client_id";
        $data['client_invoices'] = $this->client_model->get_by(array('client_id' => $id), FALSE);

        // get client contatc by client id
        $data['client_contacts'] = $this->client_model->get_client_contacts($id);

        $data['subview'] = $this->load->view('admin/client/client_details', $data, TRUE);
        $this->load->view('admin/_layout_main', $data); //page load
    }

    public function add_contacts($id, $user_id = NULL) {
        $data['title'] = lang('add_contact'); //Page title        
        $data['page'] = lang('client');
        $data['sub_active'] = lang('manage_client');
        $data['company'] = $id;
        if (!empty($user_id)) {
            // get all user_info by user id            
            $data['account_details'] = $this->client_model->check_by(array('user_id' => $user_id), 'tbl_account_details');

            $data['user_info'] = $this->client_model->check_by(array('user_id' => $user_id), 'tbl_users');
        }
        // get all language
        $this->client_model->_table_name = 'tbl_languages';
        $this->client_model->_order_by = 'name';
        $data['languages'] = $this->client_model->get();
        // get all location
        $this->client_model->_table_name = 'tbl_locales';
        $this->client_model->_order_by = 'name';
        $data['locales'] = $this->client_model->get();

        $data['subview'] = $this->load->view('admin/client/add_contacts', $data, TRUE);
        $this->load->view('admin/_layout_main', $data); //page load
    }

    public function save_contact($id = NULL) {
        $data = $this->client_model->array_from_post(array('fullname', 'company', 'phone', 'mobile', 'skype', 'language', 'locale'));

        if (!empty($id)) {
            $u_data['email'] = $this->input->post('email', TRUE);
            $u_data['last_ip'] = $this->input->ip_address();
            $this->client_model->_table_name = 'tbl_users';
            $this->client_model->_primary_key = 'user_id';
            $user_id = $this->client_model->save($u_data, $id);
            $data['user_id'] = $user_id;
            $acount_info = $this->client_model->check_by(array('user_id' => $id), 'tbl_account_details');

            $this->client_model->_table_name = 'tbl_account_details';
            $this->client_model->_primary_key = 'account_details_id';
            $return_id = $this->client_model->save($data, $acount_info->account_details_id);

            $activities = array(
                'user' => $this->session->userdata('user_id'),
                'module' => 'client',
                'module_field_id' => $id,
                'activity' => lang('activity_update_contact'),
                'icon' => 'fa-user',
                'value1' => $data['fullname']
            );
            $this->client_model->_table_name = 'tbl_activities';
            $this->client_model->_primary_key = "activities_id";
            $this->client_model->save($activities);
        } else {
            $user_data = $this->client_model->array_from_post(array('email', 'username', 'password'));
            $u_data['last_ip'] = $this->input->ip_address();
            $check_email = $this->client_model->check_by(array('email' => $user_data['email']), 'tbl_users');
            $check_username = $this->client_model->check_by(array('username' => $user_data['username']), 'tbl_users');

            if ($user_data['password'] == $this->input->post('confirm_password', TRUE)) {
                $u_data['password'] = $this->hash($user_data['password']);

                if (!empty($check_username)) {
                    $message['error'][] = 'This Username Already Used ! ';
                } else {
                    $u_data['username'] = $user_data['username'];
                }
                if (!empty($check_email)) {
                    $message['error'][] = 'This email Address Already Used ! ';
                } else {
                    $u_data['email'] = $user_data['email'];
                }
            } else {
                $message['error'][] = 'Sorry Your Password and Confirm Password Does not match !';
            }

            if (!empty($u_data['password']) && !empty($u_data['username']) && !empty($u_data['email'])) {
                $u_data['role_id'] = $this->input->post('role_id', true);

                $this->client_model->_table_name = 'tbl_users';
                $this->client_model->_primary_key = 'user_id';
                $user_id = $this->client_model->save($u_data, $id);

                $data['user_id'] = $user_id;

                $this->client_model->_table_name = 'tbl_account_details';
                $this->client_model->_primary_key = 'account_details_id';
                $return_id = $this->client_model->save($data, $id);
                // check primary contact
                $primary_contact = $this->client_model->check_by(array('client_id' => $data['company']), 'tbl_client');

                if ($primary_contact->primary_contact == 0) {
                    $c_data['primary_contact'] = $return_id;
                    $this->client_model->_table_name = 'tbl_client';
                    $this->client_model->_primary_key = 'client_id';
                    $this->client_model->save($c_data, $data['company']);
                }

                $activities = array(
                    'user' => $this->session->userdata('user_id'),
                    'module' => 'client',
                    'module_field_id' => $id,
                    'activity' => lang('activity_added_new_contact'),
                    'icon' => 'fa-user',
                    'value1' => $data['fullname']
                );
                $this->client_model->_table_name = 'tbl_activities';
                $this->client_model->_primary_key = "activities_id";
                $this->client_model->save($activities);
            }
        }
        // messages for user        
        $message['success'] = 'Contact Information Successfully Updated !';
        if (!empty($message['error'])) {
            $this->session->set_userdata($message);
            redirect('admin/client/add_contacts/' . $data['company']);
        } else {

            $this->session->set_userdata($message);
            redirect('admin/client/client_details/' . $data['company']);
        }
    }

    public function make_primary($user_id, $client_id) {
        $user_info = $this->client_model->check_by(array('user_id' => $user_id), 'tbl_account_details');

        $this->db->set('primary_contact', $user_id);
        $this->db->where('client_id', $client_id)->update('tbl_client');
        $activities = array(
            'user' => $this->session->userdata('user_id'),
            'module' => 'client',
            'module_field_id' => $client_id,
            'activity' => lang('activity_primary_contact'),
            'icon' => 'fa-user',
            'value1' => $user_info->fullname
        );
        $this->client_model->_table_name = 'tbl_activities';
        $this->client_model->_primary_key = "activities_id";
        $this->client_model->save($activities);

        // messages for user
        $type = "success";
        $message = lang('primary_contact_set');
        set_message($type, $message);
        redirect('admin/client/client_details/' . $client_id);
    }

    public function delete_contacts($client_id, $id) {
        // delete into user table by user id
        $this->client_model->_table_name = 'tbl_client';
        $this->client_model->_order_by = 'primary_contact';
        $primary_contact = $this->client_model->get_by(array('primary_contact' => $id), TRUE);
        if (!empty($primary_contact)) {
            // delete into user table by user id
            $this->client_model->_table_name = 'tbl_account_details';
            $this->client_model->_order_by = 'company';
            $client_info = $this->client_model->get_by(array('company' => $client_id), FALSE);
            $result = count($client_info);
            if ($result != '1') {
                $data['primary_contact'] = $client_info[1]->account_details_id;
            } else {
                $data['primary_contact'] = 0;
            }
            $this->client_model->_table_name = 'tbl_client';
            $this->client_model->_primary_key = 'primary_contact';
            $this->client_model->save($data, $client_id);
        }
        $user_info = $this->client_model->check_by(array('user_id' => $id), 'tbl_account_details');
        $activities = array(
            'user' => $this->session->userdata('user_id'),
            'module' => 'client',
            'module_field_id' => $id,
            'activity' => lang('activity_deleted_contact'),
            'icon' => 'fa-user',
            'value1' => $user_info->fullname
        );
        $this->client_model->_table_name = 'tbl_account_details';
        $this->client_model->delete_multiple(array('user_id' => $id));

        $this->client_model->_table_name = 'tbl_activities';
        $this->client_model->delete_multiple(array('user' => $id));

        $this->client_model->_table_name = 'tbl_expense';
        $this->client_model->_order_by = 'user_id';
        $expense_info = $this->client_model->get_by(array('user_id' => $id), FALSE);

        foreach ($expense_info as $v_expense) {
            $this->client_model->_table_name = 'tbl_expense_bill_copy';
            $this->client_model->delete_multiple(array('expense_id' => $v_expense->expense_id));
        }

        $this->client_model->_table_name = 'tbl_expense';
        $this->client_model->delete_multiple(array('user_id' => $id));

        $this->client_model->_table_name = 'tbl_payments';
        $this->client_model->delete_multiple(array('paid_by' => $id));

        // delete all tbl_quotations by id
        $this->client_model->_table_name = 'tbl_quotations';
        $this->client_model->_order_by = 'user_id';
        $quotations_info = $this->client_model->get_by(array('user_id' => $id), FALSE);

        if (!empty($quotations_info)) {
            foreach ($quotations_info as $v_quotations) {
                $this->client_model->_table_name = 'tbl_quotation_details';
                $this->client_model->delete_multiple(array('quotations_id' => $v_quotations->quotations_id));
            }
        }
        $this->client_model->_table_name = 'tbl_quotations';
        $this->client_model->delete_multiple(array('user_id' => $id));

        $this->client_model->_table_name = 'tbl_quotationforms';
        $this->client_model->delete_multiple(array('quotations_created_by_id' => $id));
        $this->client_model->_table_name = 'tbl_users';
        $this->client_model->delete_multiple(array('user_id' => $id));

        $this->client_model->_table_name = 'tbl_user_role';
        $this->client_model->delete_multiple(array('user_id' => $id));

        $this->client_model->_table_name = 'tbl_inbox';
        $this->client_model->delete_multiple(array('user_id' => $id));

        $this->client_model->_table_name = 'tbl_sent';
        $this->client_model->delete_multiple(array('user_id' => $id));

        $this->client_model->_table_name = 'tbl_draft';
        $this->client_model->delete_multiple(array('user_id' => $id));

        $this->client_model->_table_name = 'tbl_tickets';
        $this->client_model->delete_multiple(array('reporter' => $id));

        $this->client_model->_table_name = 'tbl_tickets_replies';
        $this->client_model->delete_multiple(array('replierid' => $id));

        // xpm crm
        // delete all leads by id
        $this->client_model->_table_name = 'tbl_leads';
        $this->client_model->_order_by = 'user_id';
        $leads_info = $this->client_model->get_by(array('user_id' => $id), FALSE);
        if (!empty($leads_info)) {
            foreach ($leads_info as $v_leads) {
                //delete data into table.
                $this->client_model->_table_name = "tbl_calls"; // table name        
                $this->client_model->delete_multiple(array('leads_id' => $v_leads->leads_id));

                //delete data into table.
                $this->client_model->_table_name = "tbl_mettings"; // table name        
                $this->client_model->delete_multiple(array('leads_id' => $v_leads->leads_id));

                //delete data into table.
                $this->client_model->_table_name = "tbl_task_comment"; // table name        
                $this->client_model->delete_multiple(array('leads_id' => $v_leads->leads_id));

                $this->client_model->_table_name = "tbl_task_attachment"; //table name
                $this->client_model->_order_by = "leads_id";
                $files_info = $this->client_model->get_by(array('leads_id' => $v_leads->leads_id), FALSE);

                if (!empty($files_info)) {
                    foreach ($files_info as $v_files) {
                        //save data into table.
                        $this->client_model->_table_name = "tbl_task_uploaded_files"; // table name        
                        $this->client_model->delete_multiple(array('task_attachment_id' => $v_files->task_attachment_id));
                    }
                }
                //save data into table.
                $this->client_model->_table_name = "tbl_task_attachment"; // table name        
                $this->client_model->delete_multiple(array('leads_id' => $v_leads->leads_id));

                $this->client_model->_table_name = 'tbl_leads';
                $this->client_model->_primary_key = 'leads_id';
                $this->client_model->delete($v_leads->leads_id);
            }
        }
        //save data into table.
        $this->client_model->_table_name = "tbl_milestones"; // table name        
        $this->client_model->delete_multiple(array('user_id' => $id));
        // todo
        $this->client_model->_table_name = "tbl_todo"; // table name        
        $this->client_model->delete_multiple(array('user_id' => $id));

        // opportunity
        $oppurtunity_info = $this->db->get('tbl_opportunities')->result();
        if (!empty($oppurtunity_info)) {
            foreach ($oppurtunity_info as $v_oppurtunity) {
                $opp_assding_id = unserialize($v_oppurtunity->user_id);
                if (!empty($opp_assding_id['user_id'])) {
                    foreach ($opp_assding_id['user_id'] as $op_user_id) {
                        if ($op_user_id == $id)
                        //delete data into table.
                            $this->client_model->_table_name = "tbl_calls"; // table name        
                        $this->client_model->delete_multiple(array('opportunities_id' => $v_oppurtunity->opportunities_id));

                        //delete data into table.
                        $this->client_model->_table_name = "tbl_mettings"; // table name        
                        $this->client_model->delete_multiple(array('opportunities_id' => $v_oppurtunity->opportunities_id));

                        //delete data into table.
                        $this->client_model->_table_name = "tbl_task_comment"; // table name        
                        $this->client_model->delete_multiple(array('opportunities_id' => $v_oppurtunity->opportunities_id));

                        $this->client_model->_table_name = "tbl_task_attachment"; //table name
                        $this->client_model->_order_by = "task_id";
                        $files_info = $this->client_model->get_by(array('opportunities_id' => $v_oppurtunity->opportunities_id), FALSE);
                        if (!empty($files_info)) {
                            foreach ($files_info as $v_files) {
                                //save data into table.
                                $this->client_model->_table_name = "tbl_task_uploaded_files"; // table name        
                                $this->client_model->delete_multiple(array('task_attachment_id' => $v_files->task_attachment_id));
                            }
                        }
                        //save data into table.
                        $this->client_model->_table_name = "tbl_task_attachment"; // table name        
                        $this->client_model->delete_multiple(array('opportunities_id' => $v_oppurtunity->opportunities_id));

                        //save data into table.
                        $this->client_model->_table_name = "tbl_task"; // table name        
                        $this->client_model->delete_multiple(array('opportunities_id' => $v_oppurtunity->opportunities_id));

                        $this->client_model->_table_name = 'tbl_opportunities';
                        $this->client_model->_primary_key = 'opportunities_id';
                        $this->client_model->delete($v_oppurtunity->opportunities_id);
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
                            $this->client_model->_table_name = "tbl_task_comment"; // table name        
                            $this->client_model->delete_multiple(array('project_id' => $v_project->project_id));

                            $this->client_model->_table_name = "tbl_task_attachment"; //table name
                            $this->client_model->_order_by = "task_id";
                            $files_info = $this->client_model->get_by(array('project_id' => $v_project->project_id), FALSE);
                            if (!empty($files_info)) {
                                foreach ($files_info as $v_files) {
                                    //save data into table.
                                    $this->client_model->_table_name = "tbl_task_uploaded_files"; // table name        
                                    $this->client_model->delete_multiple(array('task_attachment_id' => $v_files->task_attachment_id));
                                }
                            }
                            //save data into table.
                            $this->client_model->_table_name = "tbl_task_attachment"; // table name        
                            $this->client_model->delete_multiple(array('project_id' => $v_project->project_id));

                            //save data into table.
                            $this->client_model->_table_name = "tbl_milestones"; // table name        
                            $this->client_model->delete_multiple(array('project_id' => $v_project->project_id));

                            //save data into table.
                            $this->client_model->_table_name = "tbl_task"; // table name        
                            $this->client_model->delete_multiple(array('project_id' => $v_project->project_id));
                            //save data into table.
                            $this->client_model->_table_name = "tbl_bug"; // table name        
                            $this->client_model->delete_multiple(array('project_id' => $v_project->project_id));

                            $this->client_model->_table_name = 'tbl_project';
                            $this->client_model->_primary_key = 'project_id';
                            $this->client_model->delete($v_project->project_id);
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

                            $this->client_model->_table_name = "tbl_task_attachment"; //table name
                            $this->client_model->_order_by = "task_id";
                            $files_info = $this->client_model->get_by(array('task_id' => $v_taskss->task_id), FALSE);
                            foreach ($files_info as $v_files) {
                                $this->client_model->_table_name = "tbl_task_uploaded_files"; //table name
                                $this->client_model->delete_multiple(array('task_attachment_id' => $v_files->task_attachment_id));
                            }
//delete into table.
                            $this->client_model->_table_name = "tbl_task_attachment"; // table name        
                            $this->client_model->delete_multiple(array('task_id' => $v_taskss->task_id));

//delete data into table.
                            $this->client_model->_table_name = "tbl_task_comment"; // table name        
                            $this->client_model->delete_multiple(array('task_id' => $v_taskss->task_id));

                            $this->client_model->_table_name = "tbl_task"; // table name
                            $this->client_model->_primary_key = "task_id"; // $id
                            $this->client_model->delete($v_taskss->task_id);
                        }
                    }
                }
            }
        }
        // messages for user
        $type = "success";
        $message = lang('delete_contact');
        set_message($type, $message);
        redirect('admin/client/client_details/' . $client_id);
    }

    public
            function delete_client($client_id) {
        // delete into user table by user id
        $this->client_model->_table_name = 'tbl_account_details';
        $this->client_model->_order_by = 'company';
        $client_info = $this->client_model->get_by(array('company' => $client_id), FALSE);
        if (!empty($client_info)) {
            foreach ($client_info as $v_client) {
                $this->client_model->delete_multiple(array('account_details_id' => $v_client->account_details_id));
                $this->client_model->_table_name = 'tbl_users';
                $this->client_model->delete_multiple(array('user_id' => $v_client->user_id));
                $this->client_model->_table_name = 'tbl_inbox';
                $this->client_model->delete_multiple(array('user_id' => $v_client->user_id));

                $this->client_model->_table_name = 'tbl_sent';
                $this->client_model->delete_multiple(array('user_id' => $v_client->user_id));

                $this->client_model->_table_name = 'tbl_draft';
                $this->client_model->delete_multiple(array('user_id' => $v_client->user_id));

                $this->client_model->_table_name = 'tbl_expense';
                $this->client_model->delete_multiple(array('user_id' => $v_client->user_id));

                $this->client_model->_table_name = 'tbl_tickets';
                $this->client_model->delete_multiple(array('reporter' => $v_client->user_id));
                //save data into table.
                $this->user_model->_table_name = "tbl_bug"; // table name        
                $this->user_model->delete_multiple(array('reporter' => $v_client->user_id));
            }
        }
        // delete all leads by id
        $this->client_model->_table_name = 'tbl_leads';
        $this->client_model->_order_by = 'client_id';
        $leads_info = $this->client_model->get_by(array('client_id' => $client_id), FALSE);
        if (!empty($leads_info)) {
            foreach ($leads_info as $v_leads) {
                //delete data into table.
                $this->client_model->_table_name = "tbl_calls"; // table name        
                $this->client_model->delete_multiple(array('leads_id' => $v_leads->leads_id));

                //delete data into table.
                $this->client_model->_table_name = "tbl_mettings"; // table name        
                $this->client_model->delete_multiple(array('leads_id' => $v_leads->leads_id));

                //delete data into table.
                $this->client_model->_table_name = "tbl_task_comment"; // table name        
                $this->client_model->delete_multiple(array('leads_id' => $v_leads->leads_id));

                $this->client_model->_table_name = "tbl_task_attachment"; //table name
                $this->client_model->_order_by = "leads_id";
                $files_info = $this->client_model->get_by(array('leads_id' => $v_leads->leads_id), FALSE);

                if (!empty($files_info)) {
                    foreach ($files_info as $v_files) {
                        //save data into table.
                        $this->client_model->_table_name = "tbl_task_uploaded_files"; // table name        
                        $this->client_model->delete_multiple(array('task_attachment_id' => $v_files->task_attachment_id));
                    }
                }
                //save data into table.
                $this->client_model->_table_name = "tbl_task_attachment"; // table name        
                $this->client_model->delete_multiple(array('leads_id' => $v_leads->leads_id));

                $this->client_model->_table_name = 'tbl_leads';
                $this->client_model->_primary_key = 'leads_id';
                $this->client_model->delete($v_leads->leads_id);
            }
        }

        // project        
        // delete all leads by id
        $this->client_model->_table_name = 'tbl_project';
        $this->client_model->_order_by = 'client_id';
        $project_info = $this->client_model->get_by(array('client_id' => $client_id), FALSE);
        if (!empty($project_info)) {
            foreach ($project_info as $v_project) {
                //delete data into table.
                $this->client_model->_table_name = "tbl_task_comment"; // table name        
                $this->client_model->delete_multiple(array('project_id' => $v_project->project_id));

                $this->client_model->_table_name = "tbl_task_attachment"; //table name
                $this->client_model->_order_by = "task_id";
                $files_info = $this->client_model->get_by(array('project_id' => $v_project->project_id), FALSE);
                if (!empty($files_info)) {
                    foreach ($files_info as $v_files) {
                        //save data into table.
                        $this->client_model->_table_name = "tbl_task_uploaded_files"; // table name        
                        $this->client_model->delete_multiple(array('task_attachment_id' => $v_files->task_attachment_id));
                    }
                }
                //save data into table.
                $this->client_model->_table_name = "tbl_task_attachment"; // table name        
                $this->client_model->delete_multiple(array('project_id' => $v_project->project_id));

                //save data into table.
                $this->client_model->_table_name = "tbl_milestones"; // table name        
                $this->client_model->delete_multiple(array('project_id' => $v_project->project_id));

                //save data into table.
                $this->client_model->_table_name = "tbl_task"; // table name        
                $this->client_model->delete_multiple(array('project_id' => $v_project->project_id));
                //save data into table.
                $this->client_model->_table_name = "tbl_bug"; // table name        
                $this->client_model->delete_multiple(array('project_id' => $v_project->project_id));

                $this->client_model->_table_name = 'tbl_project';
                $this->client_model->_primary_key = 'project_id';
                $this->client_model->delete($v_project->project_id);
            }
        }
        // delete all invoice by id
        $this->client_model->_table_name = 'tbl_invoices';
        $this->client_model->_order_by = 'client_id';
        $invoice_info = $this->client_model->get_by(array('client_id' => $client_id), FALSE);
        if (!empty($invoice_info)) {
            foreach ($invoice_info as $v_invoice) {
                $this->client_model->delete_multiple(array('invoices_id' => $v_invoice->invoices_id));
                // delete all payment info by id
                $this->client_model->_table_name = 'tbl_payments';
                $this->client_model->delete_multiple(array('invoices_id' => $v_invoice->invoices_id));
            }
        }

        // delete all project by id
        $this->client_model->_table_name = 'tbl_estimates';
        $this->client_model->_order_by = 'client_id';
        $estimates_info = $this->client_model->get_by(array('client_id' => $client_id), FALSE);
        if (!empty($estimates_info)) {
            foreach ($estimates_info as $v_estimates) {
                $this->client_model->delete_multiple(array('estimates_id' => $v_estimates->estimates_id));

                $this->client_model->_table_name = 'tbl_estimate_items';
                $this->client_model->delete_multiple(array('estimates_id' => $v_estimates->estimates_id));
            }
        }
        // delete all tbl_quotations by id
        $this->client_model->_table_name = 'tbl_quotations';
        $this->client_model->_order_by = 'client_id';
        $quotations_info = $this->client_model->get_by(array('client_id' => $client_id), FALSE);

        if (!empty($quotations_info)) {
            foreach ($quotations_info as $v_quotations) {
                $this->client_model->delete_multiple(array('client_id' => $v_quotations->client_id));

                $this->client_model->_table_name = 'tbl_quotation_details';
                $this->client_model->delete_multiple(array('quotations_id' => $v_quotations->quotations_id));
            }
        }
        $user_info = $this->client_model->check_by(array('client_id' => $client_id), 'tbl_client');
        $activities = array(
            'user' => $this->session->userdata('user_id'),
            'module' => 'client',
            'module_field_id' => $this->session->userdata('user_id'),
            'activity' => lang('activity_deleted_client'),
            'icon' => 'fa-user',
            'value1' => $user_info->name
        );
        $this->client_model->_table_name = 'tbl_activities';
        $this->client_model->_primary_key = "activities_id";
        $this->client_model->save($activities);

        // deletre into tbl_account details by user id
        $this->client_model->_table_name = 'tbl_client';
        $this->client_model->_primary_key = 'client_id';
        $this->client_model->delete($client_id);


        // messages for user
        $type = "success";
        $message = lang('delete_client');
        set_message($type, $message);
        redirect('admin/client/manage_client');
    }

    function hash($string) {
        return hash('sha512', $string . config_item('encryption_key'));
    }

}
