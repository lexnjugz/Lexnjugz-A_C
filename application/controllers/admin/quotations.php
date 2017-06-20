<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of admistrator
 *
 * @author pc mart ltd
 */
class Quotations extends Admin_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('quotations_model');
    }

    public function index($action = NULL, $id = NULL) {
        $data['title'] = 'All Quatations';
        $data['page'] = lang('quotations');
        $data['sub_active'] = lang('quotations');
        if ($action == 'delete_quotations') {
            $this->quotations_model->_table_name = 'tbl_quotations';
            $this->quotations_model->_primary_key = 'quotations_id';
            $this->quotations_model->delete($id);
            $type = 'success';
            $message = lang('delete_quotation');
            set_message($type, $message);
            redirect('admin/quotations/');
        } else {

            $sub_view = 'all_quotations';
            //$sub_view = 'all_quotations';
            $data['active'] = 1;
        }
        $this->quotations_model->_table_name = 'tbl_quotations';
        $this->quotations_model->_order_by = 'quotations_id';
        $data['all_quatations'] = $this->quotations_model->get();

        $data['subview'] = $this->load->view('admin/quotations/' . $sub_view, $data, TRUE);
        $this->load->view('admin/_layout_main', $data);
    }

    public function quotations_form($action = NULL, $id = NULL) {
        $data['title'] = 'All Quatations';
        $data['page'] = lang('quotations');
        $data['quotationforms_info'] = $this->quotations_model->check_by(array('quotationforms_id' => $id), 'tbl_quotationforms');
        if ($action == 'edit_quotations_form') {
            $data['sub_active'] = lang('quotations_form');
            $form_data = json_decode($data['quotationforms_info']->quotationforms_code, true);

            $data['formbuilder_data'] = $form_data['fields'];

            $data['quotationforms_code'] = json_encode($form_data['fields']);


            $sub_view = 'quotations_form_details';

            $data['active'] = 2;
        } elseif ($action == 'delete_quotations_form') {
            // save into activities
            $activities = array(
                'user' => $this->session->userdata('user_id'),
                'module' => 'quotations',
                'module_field_id' => $id,
                'activity' => lang('activity_delete_quotations_form'),
                'icon' => 'fa-coffee',
                'value1' => $data['quotationforms_info']->quotationforms_title,
            );
            // Update into tbl_project
            $this->quotations_model->_table_name = "tbl_activities"; //table name
            $this->quotations_model->_primary_key = "activities_id";
            $this->quotations_model->save($activities);

            $this->quotations_model->_table_name = 'tbl_quotationforms';
            $this->quotations_model->_primary_key = 'quotationforms_id';
            $this->quotations_model->delete($id);
            $type = 'success';
            $message = lang('delete_quotation_form');
            set_message($type, $message);
            redirect('admin/quotations/');
        } else {
            $data['sub_active'] = lang('quotations_form');
            $sub_view = 'quotations_form';
            //$sub_view = 'all_quotations';
            $data['active'] = 1;
        }
        $this->quotations_model->_table_name = 'tbl_quotationforms';
        $this->quotations_model->_order_by = 'quotationforms_id';
        $data['all_quatations'] = $this->quotations_model->get();

        $data['subview'] = $this->load->view('admin/quotations/' . $sub_view, $data, TRUE);
        $this->load->view('admin/_layout_main', $data);
    }

    public function quotations_details($quotations_id) {
        $data['title'] = 'View  Quatations Form';
        $data['page'] = lang('quotations');
        $data['sub_active'] = lang('quotations');
        $data['quotations_info'] = $this->quotations_model->check_by(array('quotations_id' => $quotations_id), 'tbl_quotations');
        $this->quotations_model->_table_name = 'tbl_quotation_details';
        $this->quotations_model->_order_by = 'quotations_id';
        $data['quotation_details'] = $this->quotations_model->get_by(array('quotations_id' => $quotations_id), FALSE);
        $data['subview'] = $this->load->view('admin/quotations/quotation_details', $data, TRUE);
        $this->load->view('admin/_layout_main', $data);
    }

    public function set_price($quotations_id) {
        $data['quotations_id'] = $quotations_id;
        $data['quotations_info'] = $this->quotations_model->check_by(array('quotations_id' => $quotations_id), 'tbl_quotations');
        $data['subview'] = $this->load->view('admin/quotations/set_price', $data, FALSE);
        $this->load->view('admin/_layout_modal', $data);
    }

    public function set_price_quotations($id) {
        $data = $this->quotations_model->array_from_post(array('quotations_amount', 'notes'));
        $qtation_info = $this->quotations_model->check_by(array('quotations_id' => $id), 'tbl_quotations');
        $client_info = $this->quotations_model->check_by(array('client_id' => $qtation_info->client_id), 'tbl_client');
        $currency = $this->quotations_model->client_currency_sambol($qtation_info->client_id);
        $send_mail = $this->input->post('send_email', TRUE);
        if ($send_mail == 'on') {
            $email_template = $this->quotations_model->check_by(array('email_group' => 'quotations_form'), 'tbl_email_templates');

            $message = $email_template->template_body;
            $subject = $email_template->subject;
            $client_name = str_replace("{CLIENT}", $client_info->name, $message);

            $Date = str_replace("{DATE}", date('Y-m-d'), $client_name);
            $Currency = str_replace("{CURRENCY}", $currency->symbol, $Date);
            $Amount = str_replace("{AMOUNT}", $this->input->post('quotations_amount'), $Currency);
            $Notes = str_replace("{NOTES}", $this->input->post('notes'), $Amount);
            $link = str_replace("{QUOTATION LINK}", base_url() . 'client/quotations/quotations_details/' . $id, $Notes);
            $message = str_replace("{SITE_NAME}", config_item('company_name'), $link);

            $sdata['message'] = $message;
            $message = $this->load->view('email_template', $sdata, TRUE);


            $address = $client_info->email;
            $params['recipient'] = $address;
            $params['subject'] = '[ ' . config_item('company_name') . ' ]' . ' ' . $subject;
            $params['message'] = $message;
            $params['resourceed_file'] = '';
            $this->quotations_model->send_email($params);
        }

        // save into activities
        $activities = array(
            'user' => $this->session->userdata('user_id'),
            'module' => 'quotations',
            'module_field_id' => $id,
            'activity' => lang('activity_set_quotations_price'),
            'icon' => 'fa-coffee',
            'value1' => $qtation_info->quotations_form_title,
            'value2' => $client_info->name . '(' . $currency->symbol . $this->input->post('quotations_amount') . ')',
        );
        // Update into tbl_project
        $this->quotations_model->_table_name = "tbl_activities"; //table name
        $this->quotations_model->_primary_key = "activities_id";
        $this->quotations_model->save($activities);

        $data['reviewer_id'] = $this->session->userdata('user_id');
        $data['reviewed_date'] = date('Y-m-d H:i:s');
        $data['quotations_status'] = 'completed';

        $this->quotations_model->_table_name = 'tbl_quotations';
        $this->quotations_model->_primary_key = 'quotations_id';
        $this->quotations_model->save($data, $id);
        $type = 'success';
        $message = lang('save_quotation_form');
        set_message($type, $message);
        redirect('admin/quotations/');
    }

    public function quotations_form_details($id) {
        $data['title'] = 'View  Quatations Form';
        $data['page'] = lang('quotations');
        $data['sub_active'] = lang('quotations_form');
        $data['quotationforms_info'] = $this->quotations_model->check_by(array('quotationforms_id' => $id), 'tbl_quotationforms');

        $form_data = json_decode($data['quotationforms_info']->quotationforms_code, true);

        $data['formbuilder_data'] = $form_data['fields'];

        $data['quotationforms_code'] = json_encode($form_data['fields']);

        $data['subview'] = $this->load->view('admin/quotations/view_quotations_form', $data, TRUE);
        $this->load->view('admin/_layout_main', $data);
    }

    public function add_form($id = NULL) {
        $data['quotationforms_title'] = $this->input->post('quotationforms_title', TRUE);
        $data['quotationforms_code'] = $this->input->post('quotationforms_code', TRUE);
        if (!empty($id)) {
            $data['quotationforms_status'] = $this->input->post('quotationforms_status', TRUE);
        }
        $data['quotations_created_by_id'] = $this->session->userdata('user_id');

        $this->quotations_model->_table_name = 'tbl_quotationforms';
        $this->quotations_model->_primary_key = 'quotationforms_id';
        $this->quotations_model->save($data, $id);
        if (!empty($id)) {
            $action = lang('activity_update_quotation_form');
        } else {
            $action = lang('activity_save_quotation_form');
        }
        // save into activities
        $activities = array(
            'user' => $this->session->userdata('user_id'),
            'module' => 'quotations',
            'module_field_id' => $id,
            'activity' => $action,
            'icon' => 'fa-coffee',
            'value1' => $data['quotationforms_title'],
        );
        // Update into tbl_project
        $this->quotations_model->_table_name = "tbl_activities"; //table name
        $this->quotations_model->_primary_key = "activities_id";
        $this->quotations_model->save($activities);

        $type = 'success';
        $message = lang('save_quotation_form');
        set_message($type, $message);
        redirect('admin/quotations/');
    }

}