<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Settings extends Admin_Controller {

    public function __construct() {
        parent::__construct();
        ini_set('max_input_vars', '3000');
        $this->load->model('settings_model');
        $this->auth_key = config_item('api_key'); // Set our API KEY

        $this->load->helper('ckeditor');
        $this->data['ckeditor'] = array(
            'id' => 'ck_editor',
            'path' => 'asset/js/ckeditor',
            'config' => array(
                'toolbar' => "Full",
                'width' => "100%",
                'height' => "400px"
            )
        );
    }

    public function index() {
        $settings = $this->input->get('settings', TRUE) ? $this->input->get('settings', TRUE) : 'general';

        $data['title'] = lang('company_details'); //Page title        

        $data['load_setting'] = $settings;

        $data['page'] = lang('settings');

        $this->settings_model->_table_name = "tbl_countries"; //table name
        $this->settings_model->_order_by = "id";
        $data['countries'] = $this->settings_model->get();

        $data['translations'] = $this->settings_model->translations();

        $data['subview'] = $this->load->view('admin/settings/settings', $data, TRUE);
        $this->load->view('admin/_layout_main', $data); //page load
    }

    public function save_settings() {
        $input_data = $this->settings_model->array_from_post(array('company_name', 'company_legal_name',
            'contact_person', 'company_address', 'company_city', 'company_zip_code',
            'company_country', 'company_phone', 'company_email', 'company_domain', 'company_vat'));

        foreach ($input_data as $key => $value) {
            $data = array('value' => $value);
            $this->db->where('config_key', $key)->update('tbl_config', $data);
            $exists = $this->db->where('config_key', $key)->get('tbl_config');
            if ($exists->num_rows() == 0) {
                $this->db->insert('tbl_config', array("config_key" => $key, "value" => $value));
            }
        }
        $activity = array(
            'user' => $this->session->userdata('user_id'),
            'module' => 'settings',
            'module_field_id' => $this->session->userdata('user_id'),
            'activity' => lang('activity_save_general_settings'),
            'value1' => $input_data['company_name']
        );
        $this->settings_model->_table_name = 'tbl_activities';
        $this->settings_model->_primary_key = 'activities_id';
        $this->settings_model->save($activity);
        // messages for user
        $type = "success";
        $message = lang('save_general_settings');
        set_message($type, $message);
        redirect('admin/settings');
    }

    public function system() {
        $data['page'] = lang('settings');
        $data['load_setting'] = 'system';
        $data['title'] = lang('system_settings'); //Page title     
        $data['languages'] = $this->settings_model->get_active_languages();
        // get all location
        $this->settings_model->_table_name = 'tbl_locales';
        $this->settings_model->_order_by = 'name';
        $data['locales'] = $this->settings_model->get();

        // get all timezone
        $data['timezones'] = $this->settings_model->timezones();
        // get all currencies
        $this->settings_model->_table_name = 'tbl_currencies';
        $this->settings_model->_order_by = 'name';
        $data['currencies'] = $this->settings_model->get();

        $data['subview'] = $this->load->view('admin/settings/settings', $data, TRUE);
        $this->load->view('admin/_layout_main', $data); //page load
    }

    public function save_system() {

        $input_data = $this->settings_model->array_from_post(array('default_language', 'locale',
            'timezone', 'default_currency', 'default_tax', 'date_format', 'decimal_separator', 'enable_languages'));
        foreach ($input_data as $key => $value) {
            if (strtolower($value) == 'on') {
                $value = 'TRUE';
            } elseif (strtolower($value) == 'off') {
                $value = 'FALSE';
            }
            $data = array('value' => $value);
            $this->db->where('config_key', $key)->update('tbl_config', $data);
            $exists = $this->db->where('config_key', $key)->get('tbl_config');
            if ($exists->num_rows() == 0) {
                $this->db->insert('tbl_config', array("config_key" => $key, "value" => $value));
            }
        }
        $date_format = $this->input->post('date_format', true);
        //Set date format for date picker
        switch ($date_format) {
            case "%d-%m-%Y": $picker = "dd-mm-yyyy";
                $phptime = "d-m-Y";
                break;
            case "%m-%d-%Y": $picker = "mm-dd-yyyy";
                $phptime = "m-d-Y";
                break;
            case "%Y-%m-%d": $picker = "yyyy-mm-dd";
                $phptime = "Y-m-d";
                break;
        }
        $this->db->where('config_key', 'date_picker_format')->update('tbl_config', array("value" => $picker));
        $this->db->where('config_key', 'date_php_format')->update('tbl_config', array("value" => $phptime));

        $activity = array(
            'user' => $this->session->userdata('user_id'),
            'module' => 'settings',
            'module_field_id' => $this->session->userdata('user_id'),
            'activity' => lang('activity_save_system_settings'),
            'value1' => $input_data['default_language']
        );
        $this->settings_model->_table_name = 'tbl_activities';
        $this->settings_model->_primary_key = 'activities_id';
        $this->settings_model->save($activity);

        // messages for user
        $type = "success";
        $message = lang('save_system_settings');
        set_message($type, $message);
        redirect('admin/settings/system');
    }

    public function payments($payment = NULL) {
        $data['page'] = lang('settings');
        $data['load_setting'] = 'payments';
        $data['title'] = "Payments"; //Page title              
        $data['payment'] = $payment;
        $data['subview'] = $this->load->view('admin/settings/settings', $data, TRUE);
        $this->load->view('admin/_layout_main', $data); //page load
    }

    public function save_payments($payment) {
        if ($payment == 'paypal') {
            $input_data = $this->settings_model->array_from_post(array('paypal_email', 'paypal_ipn_url', 'paypal_cancel_url', 'paypal_success_url', 'paypal_live', 'paypal_status'));
        } elseif ($payment == '2checkout') {
            $input_data = $this->settings_model->array_from_post(array('2checkout_publishable_key', '2checkout_private_key', '2checkout_seller_id', '2checkout_status'));
        } elseif ($payment == 'Stripe') {
            $input_data = $this->settings_model->array_from_post(array('stripe_private_key', 'stripe_public_key', 'bitcoin_address', 'stripe_status'));
        } elseif ($payment == 'bitcoin') {
            $input_data = $this->settings_model->array_from_post(array('bitcoin_address', 'bitcoin_status'));
        } elseif ($payment == 'Authorize.net') {
            $input_data = $this->settings_model->array_from_post(array('authorize', 'authorize_transaction_key', 'authorize_status'));
        } elseif ($payment == 'CCAvenue') {
            $input_data = $this->settings_model->array_from_post(array('ccavenue_merchant_id', 'ccavenue_key', 'ccavenue_status'));
        } else {
            $input_data = $this->settings_model->array_from_post(array('braintree_merchant_id', 'braintree_private_key', 'braintree_public_key', 'braintree_default_account', 'braintree_live_or_sandbox', 'braintree_status'));
        }

        foreach ($input_data as $key => $value) {
            if (strtolower($value) == 'on') {
                $value = 'TRUE';
            } elseif (strtolower($value) == 'off') {
                $value = 'FALSE';
            }
            $data = array('value' => $value);
            $this->db->where('config_key', $key)->update('tbl_config', $data);
            $exists = $this->db->where('config_key', $key)->get('tbl_config');
            if ($exists->num_rows() == 0) {
                $this->db->insert('tbl_config', array("config_key" => $key, "value" => $value));
            }
        }

        // messages for user
        $type = "success";
        $message = lang('payment_update_success');
        set_message($type, $message);
        redirect('admin/settings/payments');
    }

    public function theme() {
        $data['page'] = lang('settings');
        $data['load_setting'] = 'theme';
        $data['title'] = lang('theme_settings'); //Page title              
        $data['subview'] = $this->load->view('admin/settings/settings', $data, TRUE);
        $this->load->view('admin/_layout_main', $data); //page load
    }

    public function email() {
        $data['page'] = lang('settings');
        $data['load_setting'] = 'email_settings';
        $data['title'] = lang('email_settings'); //Page title              
        $data['subview'] = $this->load->view('admin/settings/settings', $data, TRUE);
        $this->load->view('admin/_layout_main', $data); //page load
    }

    public function update_email() {
        $input_data = $this->settings_model->array_from_post(array('company_email', 'use_postmark',
            'postmark_api_key', 'postmark_from_address', 'protocol', 'smtp_host', 'smtp_user', 'smtp_port'));
        foreach ($input_data as $key => $value) {
            if (strtolower($value) == 'on') {
                $value = 'TRUE';
            } elseif (strtolower($value) == 'off') {
                $value = 'FALSE';
            }
            $data = array('value' => $value);
            $this->db->where('config_key', $key)->update('tbl_config', $data);
            $exists = $this->db->where('config_key', $key)->get('tbl_config');
            if ($exists->num_rows() == 0) {
                $this->db->insert('tbl_config', array("config_key" => $key, "value" => $value));
            }
        }
        $smtp_pass = $this->input->post('smtp_pass', true);

        if (!empty($smtp_pass)) {
            $this->load->library('encrypt');
            $raw_smtp_pass = $this->input->post('smtp_pass');
            $smtp_pass = $raw_smtp_pass;

            $data = array('value' => $smtp_pass);
            $this->db->where('config_key', 'smtp_pass')->update('tbl_config', $data);
            $exists = $this->db->where('config_key', 'smtp_pass')->get('tbl_config');
            if ($exists->num_rows() == 0) {
                $this->db->insert('tbl_config', array("config_key" => $key, "value" => $value));
            }
        }
        $activity = array(
            'user' => $this->session->userdata('user_id'),
            'module' => 'settings',
            'module_field_id' => $this->session->userdata('user_id'),
            'activity' => lang('activity_save_email_settings'),
            'value1' => $input_data['company_email']
        );
        $this->settings_model->_table_name = 'tbl_activities';
        $this->settings_model->_primary_key = 'activities_id';
        $this->settings_model->save($activity);
        // messages for user
        $type = "success";
        $message = lang('save_email_settings');
        set_message($type, $message);
        redirect('admin/settings/email');
    }

    public function save_theme() {
        $input_data = $this->settings_model->array_from_post(array('website_name', 'logo_or_icon', 'sidebar_theme'));

        //image Process

        if (!empty($_FILES['company_logo']['name'])) {
            $val = $this->settings_model->uploadImage('company_logo');
            $val == TRUE || redirect('admin/settings/theme');
            $input_data['company_logo'] = $val['path'];
        }
        foreach ($input_data as $key => $value) {
            $data = array('value' => $value);
            $this->db->where('config_key', $key)->update('tbl_config', $data);
            $exists = $this->db->where('config_key', $key)->get('tbl_config');
            if ($exists->num_rows() == 0) {
                $this->db->insert('tbl_config', array("config_key" => $key, "value" => $value));
            }
        }
        $activity = array(
            'user' => $this->session->userdata('user_id'),
            'module' => 'settings',
            'module_field_id' => $this->session->userdata('user_id'),
            'activity' => lang('activity_save_theme_settings'),
            'value1' => $input_data['website_name']
        );
        $this->settings_model->_table_name = 'tbl_activities';
        $this->settings_model->_primary_key = 'activities_id';
        $this->settings_model->save($activity);
        // messages for user
        $type = "success";
        $message = lang('save_theme_settings');
        set_message($type, $message);
        redirect('admin/settings/theme');
    }

    public function estimate() {
        $data['page'] = lang('settings');
        $data['load_setting'] = 'estimate';
        $data['title'] = lang('estimate_settings'); //Page title              
        $data['subview'] = $this->load->view('admin/settings/settings', $data, TRUE);
        $this->load->view('admin/_layout_main', $data); //page load
    }

    public function save_estimate() {
        $input_data = $this->settings_model->array_from_post(array('estimate_prefix', 'show_item_tax', 'estimate_terms'));
        foreach ($input_data as $key => $value) {
            if (strtolower($value) == 'on') {
                $value = 'TRUE';
            } elseif (strtolower($value) == 'off') {
                $value = 'FALSE';
            }
            $data = array('value' => $value);
            $this->db->where('config_key', $key)->update('tbl_config', $data);
            $exists = $this->db->where('config_key', $key)->get('tbl_config');
            if ($exists->num_rows() == 0) {
                $this->db->insert('tbl_config', array("config_key" => $key, "value" => $value));
            }
        }

        $activity = array(
            'user' => $this->session->userdata('user_id'),
            'module' => 'settings',
            'module_field_id' => $this->session->userdata('user_id'),
            'activity' => lang('activity_save_estimate_settings'),
            'value1' => $input_data['estimate_prefix']
        );
        $this->settings_model->_table_name = 'tbl_activities';
        $this->settings_model->_primary_key = 'activities_id';
        $this->settings_model->save($activity);
        // messages for user
        $type = "success";
        $message = lang('save_estimate_settings');
        set_message($type, $message);
        redirect('admin/settings/estimate');
    }

    public function invoice() {
        $data['page'] = lang('settings');
        $data['load_setting'] = 'invoice';
        $data['title'] = lang('invoice_settings'); //Page title              
        $data['subview'] = $this->load->view('admin/settings/settings', $data, TRUE);
        $this->load->view('admin/_layout_main', $data); //page load
    }

    public function save_invoice() {
        $input_data = $this->settings_model->array_from_post(array('invoice_prefix', 'invoices_due_after', 'invoice_start_no', 'show_invoice_tax', 'default_terms'));
        //image Process

        if (!empty($_FILES['invoice_logo']['name'])) {
            $val = $this->settings_model->uploadImage('invoice_logo');
            $val == TRUE || redirect('admin/settings/invoice');
            $input_data['invoice_logo'] = $val['path'];
        }

        foreach ($input_data as $key => $value) {
            if (strtolower($value) == 'on') {
                $value = 'TRUE';
            } elseif (strtolower($value) == 'off') {
                $value = 'FALSE';
            }
            $data = array('value' => $value);
            $this->db->where('config_key', $key)->update('tbl_config', $data);
            $exists = $this->db->where('config_key', $key)->get('tbl_config');
            if ($exists->num_rows() == 0) {
                $this->db->insert('tbl_config', array("config_key" => $key, "value" => $value));
            }
        }
        $activity = array(
            'user' => $this->session->userdata('user_id'),
            'module' => 'settings',
            'module_field_id' => $this->session->userdata('user_id'),
            'activity' => lang('activity_save_invoice_settings'),
            'value1' => $input_data['invoice_prefix']
        );
        $this->settings_model->_table_name = 'tbl_activities';
        $this->settings_model->_primary_key = 'activities_id';
        $this->settings_model->save($activity);
        // messages for user
        $type = "success";
        $message = lang('save_invoice_settings');
        set_message($type, $message);
        redirect('admin/settings/invoice');
    }

    public function templates() {
        if ($_POST) {
            $data = array(
                'subject' => $this->input->post('subject'),
                'template_body' => $this->input->post('email_template'),
            );

            $this->db->where(array('email_group' => $_POST['email_group']))->update('tbl_email_templates', $data);
            $return_url = $_POST['return_url'];
            redirect($return_url);
        } else {
            $data['page'] = lang('settings');
            $data['load_setting'] = 'templates';
            $data['title'] = lang('email_templates'); //Page title              
            $data['subview'] = $this->load->view('admin/settings/settings', $data, TRUE);
            $this->load->view('admin/_layout_main', $data); //page load
        }
    }

    public function translations() {
        $data['page'] = lang('settings');

        $data['language'] = $this->settings_model->get_active_languages();
        $data['availabe_language'] = $this->settings_model->available_translations();
        $data['translation_stats'] = $this->settings_model->translation_stats(array('en_lang.php' => "./application/language/"));

        $data['load_setting'] = 'translations';
        $data['title'] = lang('translations'); //Page title        
        $data['subview'] = $this->load->view('admin/settings/settings', $data, TRUE);
        $this->load->view('admin/_layout_main', $data); //page load
    }

    public function translations_status($language, $status) {
        $data['active'] = $status;
        $this->db->where('name', $language)->update('tbl_languages', $data);
        $type = 'success';
        if ($status == 1) {
            $message = lang('language_active_successfully');
        } else {
            $message = lang('language_deactive_successfully');
        }
        set_message($type, $message);
        redirect('admin/settings/translations');
    }

    public function add_language() {
        $language = $this->input->post('language', TRUE);

        $this->settings_model->add_language($language, array('en_lang.php' => "./application/language/"));
        $type = 'success';
        $message = lang('language_added_successfully');
        set_message($type, $message);
        redirect('admin/settings/translations');
    }

    public function edit_translations($lang) {

        $data['load_setting'] = 'translations';
        $path = array($lang . "_lang.php" => "./system/language/");

        $data['current_languages'] = $lang;
        $data['english'] = $this->lang->load('en.php', 'english', TRUE, $path);

        if ($lang == 'english') {
            $data['translation'] = $data['english'];
        } else {
            $data['translation'] = $this->lang->load($lang, $lang, TRUE, TRUE);
        }
        $data['language_files'] = $lang;
        $data['title'] = "Edit Translations"; //Page title        
        $data['subview'] = $this->load->view('admin/settings/settings', $data, TRUE);
        $this->load->view('admin/_layout_main', $data); //page load
    }

    public function set_translations($lang, $file) {
        $this->settings_model->save_translation($lang, $file);
// messages for user
        $type = "success";
        $message = '<strong style=color:#000>' . $lang . '</strong>' . " Information Successfully Update!";
        set_message($type, $message);
        redirect('admin/settings/translations');
    }

    public function department($action = NULL, $id = NULL) {
        $data['page'] = lang('settings');
        if ($action == 'edit_dept') {
            $data['active'] = 2;
            if (!empty($id)) {
                $data['dept_info'] = $this->settings_model->check_by(array('departments_id' => $id), 'tbl_departments');
            }
        } else {
            $data['active'] = 1;
        }
        $data['page'] = lang('settings');
        $data['sub_active'] = lang('department');
        if ($action == 'update_dept') {
            $dept_data['deptname'] = $this->input->post('deptname', TRUE);
            $this->settings_model->_table_name = 'tbl_departments';
            $this->settings_model->_primary_key = 'departments_id';
            $id = $this->settings_model->save($dept_data, $id);

            $activity = array(
                'user' => $this->session->userdata('user_id'),
                'module' => 'settings',
                'module_field_id' => $id,
                'activity' => lang('activity_added_a_department'),
                'value1' => $dept_data['deptname']
            );
            $this->settings_model->_table_name = 'tbl_activities';
            $this->settings_model->_primary_key = 'activities_id';
            $this->settings_model->save($activity);

            // messages for user
            $type = "success";
            $message = lang('department_added');
            set_message($type, $message);
            redirect('admin/settings/department');
        } else {
            $data['title'] = lang('department'); //Page title                  
            $data['load_setting'] = 'department';
        }

        $this->settings_model->_table_name = 'tbl_departments';
        $this->settings_model->_order_by = 'departments_id';
        $data['all_dept_info'] = $this->settings_model->get();

        $user_id = $this->session->userdata('user_id');
        $user_info = $this->settings_model->check_by(array('user_id' => $user_id), 'tbl_users');
        $data['role'] = $user_info->role_id;

        $data['subview'] = $this->load->view('admin/settings/settings', $data, TRUE);
        $this->load->view('admin/_layout_main', $data); //page load
    }

    public function delete_dept($id) {
        $dept_info = $this->settings_model->check_by(array('departments_id' => $id), 'tbl_departments');
        $activity = array(
            'user' => $this->session->userdata('user_id'),
            'module' => 'settings',
            'module_field_id' => $id,
            'activity' => lang('activity_delete_a_department'),
            'value1' => $dept_info->deptname,
        );
        $this->settings_model->_table_name = 'tbl_activities';
        $this->settings_model->_primary_key = 'activities_id';
        $this->settings_model->save($activity);

        $this->settings_model->_table_name = 'tbl_departments';
        $this->settings_model->_primary_key = 'departments_id';
        $this->settings_model->delete($id);
        // messages for user
        $type = "success";
        $message = lang('department_deleted');
        set_message($type, $message);
        redirect('admin/settings/department');
    }

    public function income_category($action = NULL, $id = NULL) {
        $data['page'] = lang('settings');
        if ($action == 'edit_income_category') {
            $data['active'] = 2;
            if (!empty($id)) {
                $data['income_category_info'] = $this->settings_model->check_by(array('income_category_id' => $id), 'tbl_income_category');
            }
        } else {
            $data['active'] = 1;
        }
        $data['page'] = lang('settings');
        $data['sub_active'] = lang('income_category');
        if ($action == 'update_income_category') {
            $this->settings_model->_table_name = 'tbl_income_category';
            $this->settings_model->_primary_key = 'income_category_id';

            $cate_data['income_category'] = $this->input->post('income_category', TRUE);
            $cate_data['description'] = $this->input->post('description', TRUE);

            // update root category
            $where = array('income_category' => $cate_data['income_category']);
            // duplicate value check in DB
            if (!empty($id)) { // if id exist in db update data
                $income_category_id = array('income_category_id !=' => $id);
            } else { // if id is not exist then set id as null
                $income_category_id = null;
            }
            // check whether this input data already exist or not
            $check_category = $this->settings_model->check_update('tbl_income_category', $where, $income_category_id);
            if (!empty($check_category)) { // if input data already exist show error alert
                // massage for user
                $type = 'error';
                $msg = "<strong style='color:#000'>" . $cate_data['income_category'] . '</strong>  ' . lang('already_exist');
            } else { // save and update query                        
                $id = $this->settings_model->save($cate_data, $id);

                $activity = array(
                    'user' => $this->session->userdata('user_id'),
                    'module' => 'settings',
                    'module_field_id' => $id,
                    'activity' => lang('activity_added_a_income_category'),
                    'value1' => $cate_data['income_category']
                );
                $this->settings_model->_table_name = 'tbl_activities';
                $this->settings_model->_primary_key = 'activities_id';
                $this->settings_model->save($activity);

                // messages for user
                $type = "success";
                $msg = lang('income_category_added');
            }
            $message = $msg;
            set_message($type, $message);
            redirect('admin/settings/income_category');
        } else {
            $data['title'] = lang('income_category'); //Page title                  
            $data['load_setting'] = 'income_category';
        }

        $this->settings_model->_table_name = 'tbl_income_category';
        $this->settings_model->_order_by = 'income_category_id';
        $data['all_income_category'] = $this->settings_model->get();

        $user_id = $this->session->userdata('user_id');
        $user_info = $this->settings_model->check_by(array('user_id' => $user_id), 'tbl_users');
        $data['role'] = $user_info->role_id;

        $data['subview'] = $this->load->view('admin/settings/settings', $data, TRUE);
        $this->load->view('admin/_layout_main', $data); //page load
    }

    public function lead_status($action = NULL, $id = NULL) {
        $data['page'] = lang('settings');
        if ($action == 'edit_lead_status') {
            $data['active'] = 2;
            if (!empty($id)) {
                $data['lead_status_info'] = $this->settings_model->check_by(array('lead_status_id' => $id), 'tbl_lead_status');
            }
        } else {
            $data['active'] = 1;
        }
        $data['page'] = lang('settings');
        $data['sub_active'] = lang('lead_status');
        if ($action == 'update_lead_status') {
            $this->settings_model->_table_name = 'tbl_lead_status';
            $this->settings_model->_primary_key = 'lead_status_id';

            $cate_data['lead_status'] = $this->input->post('lead_status', TRUE);
            $cate_data['lead_type'] = $this->input->post('lead_type', TRUE);

            // update root category
            $where = array('lead_status' => $cate_data['lead_status']);
            // duplicate value check in DB
            if (!empty($id)) { // if id exist in db update data
                $lead_status_id = array('lead_status_id !=' => $id);
            } else { // if id is not exist then set id as null
                $lead_status_id = null;
            }
            // check whether this input data already exist or not
            $check_lead_status = $this->settings_model->check_update('tbl_lead_status', $where, $lead_status_id);
            if (!empty($check_lead_status)) { // if input data already exist show error alert
                // massage for user
                $type = 'error';
                $msg = "<strong style='color:#000'>" . $cate_data['lead_status'] . '</strong>  ' . lang('already_exist');
            } else { // save and update query                        
                $id = $this->settings_model->save($cate_data, $id);

                $activity = array(
                    'user' => $this->session->userdata('user_id'),
                    'module' => 'settings',
                    'module_field_id' => $id,
                    'activity' => lang('activity_added_a_lead_status'),
                    'value1' => $cate_data['lead_status']
                );
                $this->settings_model->_table_name = 'tbl_activities';
                $this->settings_model->_primary_key = 'activities_id';
                $this->settings_model->save($activity);

                // messages for user
                $type = "success";
                $msg = lang('lead_status_added');
            }
            $message = $msg;
            set_message($type, $message);
            redirect('admin/settings/lead_status');
        } else {
            $data['title'] = lang('lead_status'); //Page title                  
            $data['load_setting'] = 'lead_status';
        }

        $this->settings_model->_table_name = 'tbl_lead_status';
        $this->settings_model->_order_by = 'lead_status_id';
        $data['all_lead_status'] = $this->settings_model->get();

        $user_id = $this->session->userdata('user_id');
        $user_info = $this->settings_model->check_by(array('user_id' => $user_id), 'tbl_users');
        $data['role'] = $user_info->role_id;

        $data['subview'] = $this->load->view('admin/settings/settings', $data, TRUE);
        $this->load->view('admin/_layout_main', $data); //page load
    }

    public function delete_lead_status($id) {
        $dept_info = $this->settings_model->check_by(array('lead_status_id' => $id), 'tbl_lead_status');
        $activity = array(
            'user' => $this->session->userdata('user_id'),
            'module' => 'settings',
            'module_field_id' => $id,
            'activity' => lang('activity_delete_a_lead_status'),
            'value1' => $dept_info->lead_status,
        );
        $this->settings_model->_table_name = 'tbl_activities';
        $this->settings_model->_primary_key = 'activities_id';
        $this->settings_model->save($activity);

        $this->settings_model->_table_name = 'tbl_lead_status';
        $this->settings_model->_order_by = 'lead_status_id';
        $this->settings_model->delete($id);
        // messages for user
        $type = "success";
        $message = lang('lead_status_deleted');
        set_message($type, $message);
        redirect('admin/settings/lead_status');
    }
     public function lead_source($action = NULL, $id = NULL) {
        $data['page'] = lang('settings');
        if ($action == 'edit_lead_source') {
            $data['active'] = 2;
            if (!empty($id)) {
                $data['lead_source_info'] = $this->settings_model->check_by(array('lead_source_id' => $id), 'tbl_lead_source');
            }
        } else {
            $data['active'] = 1;
        }
        $data['page'] = lang('settings');
        $data['sub_active'] = lang('lead_source');
        if ($action == 'update_lead_source') {
            $this->settings_model->_table_name = 'tbl_lead_source';
            $this->settings_model->_primary_key = 'lead_source_id';

            $source_data['lead_source'] = $this->input->post('lead_source', TRUE);
            // update root category
            $where = array('lead_source' => $source_data['lead_source']);
            // duplicate value check in DB
            if (!empty($id)) { // if id exist in db update data
                $lead_source_id = array('lead_source_id !=' => $id);
            } else { // if id is not exist then set id as null
                $lead_source_id = null;
            }
            // check whether this input data already exist or not
            $check_lead_status = $this->settings_model->check_update('tbl_lead_source', $where, $lead_source_id);
            if (!empty($check_lead_status)) { // if input data already exist show error alert
                // massage for user
                $type = 'error';
                $msg = "<strong style='color:#000'>" . $source_data['lead_source'] . '</strong>  ' . lang('already_exist');
            } else { // save and update query                        
                $id = $this->settings_model->save($source_data, $id);

                $activity = array(
                    'user' => $this->session->userdata('user_id'),
                    'module' => 'settings',
                    'module_field_id' => $id,
                    'activity' => lang('activity_added_a_lead_source'),
                    'value1' => $source_data['lead_source']
                );
                $this->settings_model->_table_name = 'tbl_activities';
                $this->settings_model->_primary_key = 'activities_id';
                $this->settings_model->save($activity);

                // messages for user
                $type = "success";
                $msg = lang('lead_source_added');
            }
            $message = $msg;
            set_message($type, $message);
            redirect('admin/settings/lead_source');
        } else {
            $data['title'] = lang('lead_source'); //Page title                  
            $data['load_setting'] = 'lead_source';
        }

        $this->settings_model->_table_name = 'tbl_lead_source';
        $this->settings_model->_order_by = 'lead_source_id';
        $data['all_lead_source'] = $this->settings_model->get();

        $user_id = $this->session->userdata('user_id');
        $user_info = $this->settings_model->check_by(array('user_id' => $user_id), 'tbl_users');
        $data['role'] = $user_info->role_id;

        $data['subview'] = $this->load->view('admin/settings/settings', $data, TRUE);
        $this->load->view('admin/_layout_main', $data); //page load
    }

    public function delete_lead_source($id) {
        $lead_source = $this->settings_model->check_by(array('lead_source_id' => $id), 'tbl_lead_source');
        $activity = array(
            'user' => $this->session->userdata('user_id'),
            'module' => 'settings',
            'module_field_id' => $id,
            'activity' => lang('activity_delete_a_lead_source'),
            'value1' => $lead_source->lead_source,
        );
        $this->settings_model->_table_name = 'tbl_activities';
        $this->settings_model->_primary_key = 'activities_id';
        $this->settings_model->save($activity);
        
        $this->settings_model->_table_name = 'tbl_lead_source';
        $this->settings_model->_primary_key = 'lead_source_id';
        $this->settings_model->delete($id);

        // messages for user
        $type = "success";
        $message = lang('lead_source_deleted');
        set_message($type, $message);
        redirect('admin/settings/lead_source');
    }

    public function opportunities_state_reason($id = NULL) {
        $flag = $this->input->post('flag', TRUE);
        if (!empty($flag)) {
            $input['opportunities_state_reason'] = $this->input->post("opportunities_state_reason_" . $id, TRUE);
            $this->settings_model->_table_name = 'tbl_opportunities_state_reason';
            $this->settings_model->_primary_key = 'opportunities_state_reason_id';
            $this->settings_model->save($input, $id);
        }

        $data['page'] = lang('settings');
        $data['sub_active'] = lang('lead_status');
        $data['title'] = lang('opportunities_state_reason'); //Page title                  
        $data['load_setting'] = 'opportunities_state_reason';
        $user_id = $this->session->userdata('user_id');
        $user_info = $this->settings_model->check_by(array('user_id' => $user_id), 'tbl_users');
        $data['role'] = $user_info->role_id;

        $data['subview'] = $this->load->view('admin/settings/settings', $data, TRUE);
        $this->load->view('admin/_layout_main', $data); //page load
    }

    public function delete_income_category($id) {
        $dept_info = $this->settings_model->check_by(array('income_category_id' => $id), 'tbl_income_category');
        $activity = array(
            'user' => $this->session->userdata('user_id'),
            'module' => 'settings',
            'module_field_id' => $id,
            'activity' => lang('activity_delete_a_department'),
            'value1' => $dept_info->deptname,
        );
        $this->settings_model->_table_name = 'tbl_activities';
        $this->settings_model->_primary_key = 'activities_id';
        $this->settings_model->save($activity);

        $this->settings_model->_table_name = 'tbl_income_category';
        $this->settings_model->_order_by = 'income_category_id';
        $this->settings_model->delete($id);
        // messages for user
        $type = "success";
        $message = lang('income_category_deleted');
        set_message($type, $message);
        redirect('admin/settings/income_category');
    }

    public function payment_method($action = NULL, $id = NULL) {
        $data['page'] = lang('settings');
        if ($action == 'edit_payment_method') {
            $data['active'] = 2;
            if (!empty($id)) {
                $data['method_info'] = $this->settings_model->check_by(array('payment_methods_id' => $id), 'tbl_payment_methods');
            }
        } else {
            $data['active'] = 1;
        }
        $data['page'] = lang('settings');
        $data['sub_active'] = lang('payment_method');
        if ($action == 'update_payment_method') {
            $this->settings_model->_table_name = 'tbl_payment_methods';
            $this->settings_model->_primary_key = 'payment_methods_id';
            $cate_data['method_name'] = $this->input->post('method_name', TRUE);
            // update root category
            $where = array('method_name' => $cate_data['method_name']);
            // duplicate value check in DB
            if (!empty($id)) { // if id exist in db update data
                $payment_methods_id = array('payment_methods_id !=' => $id);
            } else { // if id is not exist then set id as null
                $payment_methods_id = null;
            }
            // check whether this input data already exist or not
            $check_category = $this->settings_model->check_update('tbl_payment_methods', $where, $payment_methods_id);
            if (!empty($check_category)) { // if input data already exist show error alert
                // massage for user
                $type = 'error';
                $msg = "<strong style='color:#000'>" . $cate_data['method_name'] . '</strong>  ' . lang('already_exist');
            } else { // save and update query                        
                $id = $this->settings_model->save($cate_data, $id);

                $activity = array(
                    'user' => $this->session->userdata('user_id'),
                    'module' => 'settings',
                    'module_field_id' => $id,
                    'activity' => lang('activity_added_a_payment_method'),
                    'value1' => $cate_data['method_name']
                );
                $this->settings_model->_table_name = 'tbl_activities';
                $this->settings_model->_primary_key = 'activities_id';
                $this->settings_model->save($activity);

                // messages for user
                $type = "success";
                $msg = lang('payment_method_added');
            }
            $message = $msg;
            set_message($type, $message);
            redirect('admin/settings/payment_method');
        } else {
            $data['title'] = lang('payment_method'); //Page title                  
            $data['load_setting'] = 'payment_method';
        }

        $this->settings_model->_table_name = 'tbl_payment_methods';
        $this->settings_model->_order_by = 'payment_methods_id';
        $data['all_method_info'] = $this->settings_model->get();

        $user_id = $this->session->userdata('user_id');
        $user_info = $this->settings_model->check_by(array('user_id' => $user_id), 'tbl_users');
        $data['role'] = $user_info->role_id;

        $data['subview'] = $this->load->view('admin/settings/settings', $data, TRUE);
        $this->load->view('admin/_layout_main', $data); //page load
    }

    public function delete_payment_method($id) {
        $method_info = $this->settings_model->check_by(array('payment_methods_id' => $id), 'tbl_payment_methods');
        $activity = array(
            'user' => $this->session->userdata('user_id'),
            'module' => 'settings',
            'module_field_id' => $id,
            'activity' => lang('activity_delete_a_method'),
            'value1' => $method_info->method_name,
        );
        $this->settings_model->_table_name = 'tbl_activities';
        $this->settings_model->_primary_key = 'activities_id';
        $this->settings_model->save($activity);

        $this->settings_model->_table_name = 'tbl_payment_methods';
        $this->settings_model->_order_by = 'payment_methods_id';
        $this->settings_model->delete($id);
        // messages for user
        $type = "success";
        $message = lang('income_category_deleted');
        set_message($type, $message);
        redirect('admin/settings/payment_method');
    }

    public function expense_category($action = NULL, $id = NULL) {
        $data['page'] = lang('settings');
        if ($action == 'edit_expense_category') {
            $data['active'] = 2;
            if (!empty($id)) {
                $data['expense_category_info'] = $this->settings_model->check_by(array('expense_category_id' => $id), 'tbl_expense_category');
            }
        } else {
            $data['active'] = 1;
        }
        $data['page'] = lang('settings');
        $data['sub_active'] = lang('expense_category');
        if ($action == 'update_expense_category') {
            $this->settings_model->_table_name = 'tbl_expense_category';
            $this->settings_model->_primary_key = 'expense_category_id';

            $cate_data['expense_category'] = $this->input->post('expense_category', TRUE);
            $cate_data['description'] = $this->input->post('description', TRUE);

            // update root category
            $where = array('expense_category' => $cate_data['expense_category']);
            // duplicate value check in DB
            if (!empty($id)) { // if id exist in db update data
                $expense_category_id = array('expense_category_id !=' => $id);
            } else { // if id is not exist then set id as null
                $expense_category_id = null;
            }
            // check whether this input data already exist or not
            $check_category = $this->settings_model->check_update('tbl_expense_category', $where, $expense_category_id);
            if (!empty($check_category)) { // if input data already exist show error alert
                // massage for user
                $type = 'error';
                $msg = "<strong style='color:#000'>" . $cate_data['expense_category'] . '</strong>  ' . lang('already_exist');
            } else { // save and update query                        
                $id = $this->settings_model->save($cate_data, $id);

                $activity = array(
                    'user' => $this->session->userdata('user_id'),
                    'module' => 'settings',
                    'module_field_id' => $id,
                    'activity' => lang('activity_added_a_expense_category'),
                    'value1' => $cate_data['expense_category']
                );
                $this->settings_model->_table_name = 'tbl_activities';
                $this->settings_model->_primary_key = 'activities_id';
                $this->settings_model->save($activity);

                // messages for user
                $type = "success";
                $msg = lang('expense_category_added');
            }
            $message = $msg;
            set_message($type, $message);
            redirect('admin/settings/expense_category');
        } else {
            $data['title'] = lang('expense_category'); //Page title                  
            $data['load_setting'] = 'expense_category';
        }

        $this->settings_model->_table_name = 'tbl_expense_category';
        $this->settings_model->_order_by = 'expense_category_id';
        $data['all_expense_category'] = $this->settings_model->get();

        $user_id = $this->session->userdata('user_id');
        $user_info = $this->settings_model->check_by(array('user_id' => $user_id), 'tbl_users');
        $data['role'] = $user_info->role_id;

        $data['subview'] = $this->load->view('admin/settings/settings', $data, TRUE);
        $this->load->view('admin/_layout_main', $data); //page load
    }

    public function delete_expense_category($id) {
        $dept_info = $this->settings_model->check_by(array('expense_category_id' => $id), 'tbl_expense_category');
        $activity = array(
            'user' => $this->session->userdata('user_id'),
            'module' => 'settings',
            'module_field_id' => $id,
            'activity' => lang('activity_delete_a_expense_category'),
            'value1' => $dept_info->deptname,
        );
        $this->settings_model->_table_name = 'tbl_activities';
        $this->settings_model->_primary_key = 'activities_id';
        $this->settings_model->save($activity);

        $this->settings_model->_table_name = 'tbl_income_category';
        $this->settings_model->_order_by = 'income_category_id';
        $this->settings_model->delete($id);
        // messages for user
        $type = "success";
        $message = lang('expense_category_deleted');
        set_message($type, $message);
        redirect('admin/settings/expense_category');
    }

    public function notification() {
        $data['page'] = lang('settings');
        $data['title'] = lang('notification_settings');
        // check notififation status by where
        $where = array('notify_me' => '1');
        // check email notification status
        $data['email'] = $this->settings_model->check_by($where, 'tbl_inbox');
        $data['load_setting'] = 'notification';
        $data['subview'] = $this->load->view('admin/settings/settings', $data, TRUE);
        $this->load->view('admin/_layout_main', $data);
    }

    public function set_noticifation() {
// get input data
        $email = $this->input->post('email', TRUE);

        $accounting_snapshot = $this->input->post('accounting_snapshot', TRUE);
        $recurring_invoice = $this->input->post('recurring_invoice', TRUE);
        $where = array('notify_me' => '0');
        $action = array('notify_me' => '1');
// set notifucation into tbl inox
// notify status 1= on and 0=off
        if (!empty($email)) {
// check existing mail 
            $this->settings_model->_table_name = "tbl_inbox"; //table name        
            $this->settings_model->_order_by = "inbox_id";    //id
            $check_email = $this->settings_model->get();

            if (empty($check_email)) {
                $type = "danger";
                $message = lang('no_email_notify');
                $error_message['error_type'][] = $type;
                $error_message['error_message'][] = $message;
            }
            $status['notify_me'] = $email;

            $this->settings_model->set_action($where, $status, 'tbl_inbox'); // get result
        } else {
            $this->settings_model->set_action($action, $where, 'tbl_inbox'); // get result
        }

        // set notification into tbl Notice
        if (!empty($accounting_snapshot)) {
            $data = array('value' => $accounting_snapshot);
            $key = 'accounting_snapshot';
            $this->db->where('config_key', $key)->update('tbl_config', $data);
            $exists = $this->db->where('config_key', $key)->get('tbl_config');
            if ($exists->num_rows() == 0) {
                $this->db->insert('tbl_config', array("config_key" => $key, "value" => $accounting_snapshot));
            }
        } else {
            $data = array('value' => 0);
            $key = 'accounting_snapshot';
            $this->db->where('config_key', $key)->update('tbl_config', $data);
            $exists = $this->db->where('config_key', $key)->get('tbl_config');
        }
        if (!empty($recurring_invoice)) {
            $data = array('value' => $recurring_invoice);
            $key = 'recurring_invoice';
            $this->db->where('config_key', $key)->update('tbl_config', $data);
            $exists = $this->db->where('config_key', $key)->get('tbl_config');
            if ($exists->num_rows() == 0) {
                $this->db->insert('tbl_config', array("config_key" => $key, "value" => recurring_invoice));
            }
        } else {
            $data = array('value' => 0);
            $key = 'recurring_invoice';
            $this->db->where('config_key', $key)->update('tbl_config', $data);
            $exists = $this->db->where('config_key', $key)->get('tbl_config');
        }

        $type = "success";
        $message = lang('notification_settings_changes');
        $error_message['error_type'][] = $type;
        $error_message['error_message'][] = $message;
        $this->session->set_userdata($error_message);
        redirect('admin/settings/notification'); //redirect page
    }

    public function update_profile() {
        $data['title'] = lang('update_profile');
        $data['subview'] = $this->load->view('admin/settings/update_profile', $data, TRUE);
        $this->load->view('admin/_layout_main', $data);
    }

    public function profile_updated() {
        $user_id = $this->session->userdata('user_id');
        $profile_data = $this->settings_model->array_from_post(array('fullname', 'phone', 'language', 'locale'));

        if (!empty($_FILES['avatar']['name'])) {
            $val = $this->settings_model->uploadImage('avatar');
            $val == TRUE || redirect('admin/settings/update_profile');
            $profile_data['avatar'] = $val['path'];
        }

        $this->settings_model->_table_name = 'tbl_account_details';
        $this->settings_model->_primary_key = 'user_id';
        $this->settings_model->save($profile_data, $user_id);

        $activity = array(
            'user' => $this->session->userdata('user_id'),
            'module' => 'settings',
            'module_field_id' => $user_id,
            'activity' => lang('activity_update_profile'),
            'value1' => $profile_data['fullname'],
        );
        $this->settings_model->_table_name = 'tbl_activities';
        $this->settings_model->_primary_key = 'activities_id';
        $this->settings_model->save($activity);

        $client_id = $this->input->post('client_id', TRUE);
        if (!empty($client_id)) {
            $client_data = $this->settings_model->array_from_post(array('name', 'email', 'address'));
            $this->settings_model->_table_name = 'tbl_client';
            $this->settings_model->_primary_key = 'client_id';
            $this->settings_model->save($client_data, $client_id);
        }
        $type = "success";
        $message = lang('profile_updated');
        set_message($type, $message);
        redirect('admin/settings/update_profile'); //redirect page
    }

    public function set_password() {
        $user_id = $this->session->userdata('user_id');
        $password = $this->hash($this->input->post('old_password', TRUE));
        $check_old_pass = $this->admin_model->check_by(array('password' => $password), 'tbl_users');
        $user_info = $this->admin_model->check_by(array('user_id' => $user_id), 'tbl_users');
        if (!empty($check_old_pass)) {
            $data['password'] = $this->hash($this->input->post('new_password'));
            $this->settings_model->_table_name = 'tbl_users';
            $this->settings_model->_primary_key = 'user_id';
            $this->settings_model->save($data, $user_id);
            $type = "success";
            $message = lang('password_updated');
            $action = lang('activity_password_update');
        } else {
            $type = "error";
            $message = lang('password_error');
            $action = lang('activity_password_error');
        }
        $activity = array(
            'user' => $this->session->userdata('user_id'),
            'module' => 'settings',
            'module_field_id' => $user_id,
            'activity' => $action,
            'value1' => $user_info->username,
        );
        $this->settings_model->_table_name = 'tbl_activities';
        $this->settings_model->_primary_key = 'activities_id';
        $this->settings_model->save($activity);
        set_message($type, $message);
        redirect('admin/settings/update_profile'); //redirect page
    }

    public function change_email() {
        $user_id = $this->session->userdata('user_id');
        $password = $this->hash($this->input->post('password', TRUE));
        $check_old_pass = $this->settings_model->check_by(array('password' => $password), 'tbl_users');
        $user_info = $this->admin_model->check_by(array('user_id' => $user_id), 'tbl_users');
        if (!empty($check_old_pass)) {
            $new_email = $this->input->post('email', TRUE);
            if ($check_old_pass->email == $new_email) {
                $type = 'error';
                $message = lang('current_email');
                $action = lang('trying_update_email');
            } elseif ($this->is_email_available($new_email)) {
                $data = array(
                    'new_email' => $new_email,
                    'new_email_key' => md5(rand() . microtime()),
                );

                $this->settings_model->_table_name = 'tbl_users';
                $this->settings_model->_primary_key = 'user_id';
                $this->settings_model->save($data, $user_id);
                $data['user_id'] = $user_id;
                $this->send_email_change_email($new_email, $data);
                $type = "success";
                $message = lang('succesffuly_change_email');
                $action = lang('activity_updated_email');
            } else {
                $type = "error";
                $message = lang('duplicate_email');
                $action = lang('trying_update_email');
            }
        } else {
            $type = "error";
            $message = lang('password_error');
            $action = lang('trying_update_email');
        }
        $activity = array(
            'user' => $this->session->userdata('user_id'),
            'module' => 'settings',
            'module_field_id' => $user_id,
            'activity' => $action,
            'value1' => $user_info->email,
            'value2' => $new_email,
        );
        $this->settings_model->_table_name = 'tbl_activities';
        $this->settings_model->_primary_key = 'activities_id';
        $this->settings_model->save($activity);
        set_message($type, $message);
        redirect('admin/settings/update_profile'); //redirect page
    }

    function send_email_change_email($email, $data) {
        $email_template = $this->settings_model->check_by(array('email_group' => 'change_email'), 'tbl_email_templates');
        $message = $email_template->template_body;
        $subject = $email_template->subject;

        $email_key = str_replace("{NEW_EMAIL_KEY_URL}", base_url() . 'login/reset_email/' . $data['user_id'] . '/' . $data['new_email_key'], $message);
        $new_email = str_replace("{NEW_EMAIL}", $data['new_email'], $email_key);
        $site_url = str_replace("{SITE_URL}", base_url(), $new_email);
        $message = str_replace("{SITE_NAME}", config_item('company_name'), $site_url);

        $params['recipient'] = $email;

        $params['subject'] = '[ ' . config_item('company_name') . ' ]' . ' ' . $subject;
        $params['message'] = $message;

        $params['resourceed_file'] = '';
        $this->settings_model->send_email($params);
    }

    function is_email_available($email) {

        $this->db->select('1', FALSE);
        $this->db->where('LOWER(email)=', strtolower($email));
        $this->db->or_where('LOWER(new_email)=', strtolower($email));
        $query = $this->db->get('tbl_users');
        return $query->num_rows() == 0;
    }

    public function hash($string) {
        return hash('sha512', $string . config_item('encryption_key'));
    }

    public function change_username() {
        $user_id = $this->session->userdata('user_id');
        $password = $this->hash($this->input->post('password', TRUE));
        $check_old_pass = $this->admin_model->check_by(array('password' => $password), 'tbl_users');
        $user_info = $this->admin_model->check_by(array('user_id' => $user_id), 'tbl_users');
        if (!empty($check_old_pass)) {
            $data['username'] = $this->input->post('username');
            $this->settings_model->_table_name = 'tbl_users';
            $this->settings_model->_primary_key = 'user_id';
            $this->settings_model->save($data, $user_id);
            $type = "success";
            $message = lang('username_updated');
            $action = lang('activity_username_updated');
        } else {
            $type = "error";
            $message = lang('password_error');
            $action = lang('username_changed_error');
        }
        $activity = array(
            'user' => $this->session->userdata('user_id'),
            'module' => 'settings',
            'module_field_id' => $user_id,
            'activity' => $action,
            'value1' => $user_info->username,
            'value2' => $this->input->post('username'),
        );
        $this->settings_model->_table_name = 'tbl_activities';
        $this->settings_model->_primary_key = 'activities_id';
        $this->settings_model->save($activity);
        set_message($type, $message);
        redirect('admin/settings/update_profile'); //redirect page
    }

    public function database_backup() {
        $this->load->dbutil();
        $prefs = array(
            'format' => 'zip', // gzip, zip, txt
            'filename' => 'BACS_DB_Backup' . date('Y-m-d') . '.zip',
            'add_drop' => TRUE, // Whether to add DROP TABLE statements to backup file
            'add_insert' => TRUE, // Whether to add INSERT data to backup file
            'newline' => "\n"               // Newline character used in backup file
        );
        $backup = & $this->dbutil->backup($prefs);
        $this->load->helper('download');
        force_download('BACS_DB_Backup' . date('Y-m-d') . '.zip', $backup);

        $activity = array(
            'user' => $this->session->userdata('user_id'),
            'module' => 'settings',
            'module_field_id' => $this->session->userdata('user_id'),
            'activity' => 'activity_database_backup',
            'value1' => $prefs['filename']
        );
        $this->settings_model->_table_name = 'tbl_activities';
        $this->settings_model->_primary_key = 'activities_id';
        $this->settings_model->save($activity);
    }

    public function activities() {
        $data['title'] = lang('activities');
        $data['activities_info'] = $this->db->where(array('user' => $this->session->userdata('user_id')))->order_by('activity_date', 'DESC')->get('tbl_activities')->result();

        $data['subview'] = $this->load->view('admin/settings/activities', $data, TRUE);
        $this->load->view('admin/_layout_main', $data);
    }

    public function clear_activities() {
        $this->db->where(array('user' => $this->session->userdata('user_id')))->delete('tbl_activities');
        $activity = array(
            'user' => $this->session->userdata('user_id'),
            'module' => 'settings',
            'module_field_id' => $this->session->userdata('user_id'),
            'activity' => 'activity_deleted',
            'value1' => lang('all_activity') . date('Y-m-d')
        );
        $this->settings_model->_table_name = 'tbl_activities';
        $this->settings_model->_primary_key = 'activities_id';
        $this->settings_model->save($activity);

        $type = "success";
        $message = lang('activities_deleted');
        set_message($type, $message);
        redirect('admin/dashboard');
    }

}
