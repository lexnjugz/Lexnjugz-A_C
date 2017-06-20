<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Invoice extends Admin_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('invoice_model');

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

    public function manage_invoice($action = NULL, $id = NULL, $item_id = NULL) {

        $data['page'] = lang('sales');

        if ($action == 'all_payments') {
            $data['sub_active'] = lang('payments_received');
        } else {
            $data['sub_active'] = lang('invoice');
        }
        if (!empty($item_id)) {
            $data['item_info'] = $this->invoice_model->check_by(array('items_id' => $item_id), 'tbl_items');
        }
        if (!empty($id)) {
            // get all invoice info by id                
            $data['invoice_info'] = $this->invoice_model->check_by(array('invoices_id' => $id), 'tbl_invoices');
        }
        if ($action == 'create_invoice') {
            $data['active'] = 2;
        } else {
            $data['active'] = 1;
        }
        // get all client
        $this->invoice_model->_table_name = 'tbl_client';
        $this->invoice_model->_order_by = 'client_id';
        $data['all_client'] = $this->invoice_model->get();

        // get all client
        $this->invoice_model->_table_name = 'tbl_invoices';
        $this->invoice_model->_order_by = 'invoices_id';
        $data['all_invoice_info'] = $this->invoice_model->get();
        if ($action == 'invoice_details') {
            $data['title'] = "Invoice Details"; //Page title    
            $data['client_info'] = $this->invoice_model->check_by(array('client_id' => $data['invoice_info']->client_id), 'tbl_client');
            $data['language_info'] = $this->lang->load('en_lang', $data['client_info']->language, TRUE, FALSE, '', TRUE);

            $subview = 'invoice_details';
        } elseif ($action == 'payment') {
            $data['title'] = "Invoice Payment"; //Page title      
            $subview = 'payment';
        } elseif ($action == 'payments_details') {
            $data['title'] = "Payments Details"; //Page title      
            $subview = 'payments_details';

            // get payment info
            $this->invoice_model->_table_name = 'tbl_payments';
            $this->invoice_model->_order_by = 'invoices_id';
            $data['all_payments_info'] = $this->invoice_model->get_by(array('invoices_id !=' => '0'), FALSE);
            // get payment info by id
            $this->invoice_model->_table_name = 'tbl_payments';
            $this->invoice_model->_order_by = 'payments_id';
            $data['payments_info'] = $this->invoice_model->get_by(array('payments_id' => $id), TRUE);
        } elseif ($action == 'invoice_history') {
            $data['title'] = "Invoice History"; //Page title      
            $subview = 'invoice_history';
        } elseif ($action == 'email_invoice') {
            $data['title'] = "Email Invoice"; //Page title      
            $subview = 'email_invoice';
            $data['editor'] = $this->data;
        } elseif ($action == 'send_reminder') {
            $data['title'] = "Send Remainder"; //Page title      
            $subview = 'send_reminder';
            $data['editor'] = $this->data;
        } elseif ($action == 'send_overdue') {
            $data['title'] = lang('send_invoice_overdue'); //Page title      
            $subview = 'send_overdue';
            $data['editor'] = $this->data;
        } elseif ($action == 'pdf_invoice') {
            $data['title'] = "Invoice PDF"; //Page title                             
            $this->load->helper('dompdf');
            $viewfile = $this->load->view('admin/invoice/invoice_pdf', $data, TRUE);
            pdf_create($viewfile, 'Invoice  # ' . $data['invoice_info']->reference_no);
        } else {
            $data['title'] = "Manage Invoice"; //Page title      
            $subview = 'manage_invoice';
        }
        $user_id = $this->session->userdata('user_id');

        $user_info = $this->invoice_model->check_by(array('user_id' => $user_id), 'tbl_users');
        $data['role'] = $user_info->role_id;


        $data['subview'] = $this->load->view('admin/invoice/' . $subview, $data, TRUE);
        $this->load->view('admin/_layout_main', $data); //page load
    }

    public function all_payments($id = NULL) {
        if (!empty($id)) {
            $data['invoice_info'] = $this->invoice_model->check_by(array('invoices_id' => $id), 'tbl_invoices');
            $data['title'] = "Edit Payments"; //Page title      
            $subview = 'edit_payments';
        } else {
            $data['title'] = "All Payments"; //Page title      
            $subview = 'all_payments';
        }
        // get payment info
        $this->invoice_model->_table_name = 'tbl_payments';
        $this->invoice_model->_order_by = 'invoices_id';
        $data['all_payments_info'] = $this->invoice_model->get_by(array('invoices_id !=' => '0'), FALSE);

        // get payment info by id
        $this->invoice_model->_table_name = 'tbl_payments';
        $this->invoice_model->_order_by = 'payments_id';
        $data['payments_info'] = $this->invoice_model->get_by(array('payments_id' => $id), TRUE);

        $user_id = $this->session->userdata('user_id');
        $user_info = $this->invoice_model->check_by(array('user_id' => $user_id), 'tbl_users');
        $data['role'] = $user_info->role_id;

        $data['subview'] = $this->load->view('admin/invoice/' . $subview, $data, TRUE);
        $this->load->view('admin/_layout_main', $data); //page load
    }

    public function save_invoice($id = NULL) {

        $data = $this->invoice_model->array_from_post(array('reference_no', 'client_id', 'tax', 'discount'));
        $data['allow_paypal'] = ($this->input->post('allow_paypal') == 'Yes') ? 'Yes' : 'No';
        $data['allow_stripe'] = ($this->input->post('allow_stripe') == 'Yes') ? 'Yes' : 'No';
        $data['allow_2checkout'] = ($this->input->post('allow_2checkout') == 'Yes') ? 'Yes' : 'No';
        $data['allow_authorize'] = ($this->input->post('allow_authorize') == 'Yes') ? 'Yes' : 'No';
        $data['allow_ccavenue'] = ($this->input->post('allow_ccavenue') == 'Yes') ? 'Yes' : 'No';
        $data['allow_braintree'] = ($this->input->post('allow_braintree') == 'Yes') ? 'Yes' : 'No';


        $data['due_date'] = date('Y-m-d', strtotime($this->input->post('due_date', TRUE)));

        $data['notes'] = $this->input->post('notes', TRUE);

        $currency = $this->invoice_model->client_currency_sambol($data['client_id']);
        $data['currency'] = $currency->code;

        // get all client
        $this->invoice_model->_table_name = 'tbl_invoices';
        $this->invoice_model->_primary_key = 'invoices_id';
        if (!empty($id)) {
            $invoice_id = $id;
            $this->invoice_model->save($data, $id);
            $action = lang('activity_invoice_updated');
            $msg = lang('invoice_updated');
        } else {
            $invoice_id = $this->invoice_model->save($data);
            $action = lang('activity_invoice_created');
            $msg = lang('invoice_created');
        }
        $recuring_frequency = $this->input->post('recuring_frequency', TRUE);

        if (!empty($recuring_frequency) && $recuring_frequency != 'none') {
            $recur_data = $this->invoice_model->array_from_post(array('recur_start_date', 'recur_end_date'));
            $recur_data['recuring_frequency'] = $recuring_frequency;
            $this->get_recuring_frequency($invoice_id, $recur_data); // set recurring
        }
        $activity = array(
            'user' => $this->session->userdata('user_id'),
            'module' => 'invoice',
            'module_field_id' => $invoice_id,
            'activity' => $action,
            'icon' => 'fa-circle-o',
            'value1' => $data['reference_no']
        );
        $this->invoice_model->_table_name = 'tbl_activities';
        $this->invoice_model->_primary_key = 'activities_id';
        $this->invoice_model->save($activity);

        // messages for user
        $type = "success";
        $message = $msg;
        set_message($type, $message);
        redirect('admin/invoice/manage_invoice');
    }

    public function recurring_invoice($id = NULL) {
        $data['title'] = lang('recurring_invoice');
        if (!empty($id)) {
            $data['invoice_info'] = $this->invoice_model->check_by(array('invoices_id' => $id), 'tbl_invoices');
            $data['active'] = 2;
        } else {
            $data['active'] = 1;
        }
        // get all client
        $this->invoice_model->_table_name = 'tbl_client';
        $this->invoice_model->_order_by = 'client_id';
        $data['all_client'] = $this->invoice_model->get();

        $user_id = $this->session->userdata('user_id');
        $user_info = $this->invoice_model->check_by(array('user_id' => $user_id), 'tbl_users');
        $data['role'] = $user_info->role_id;
        $data['subview'] = $this->load->view('admin/invoice/recurring_invoice', $data, TRUE);
        $this->load->view('admin/_layout_main', $data); //page load
    }

    function get_recuring_frequency($invoices_id, $recur_data) {
        $recur_days = $this->get_calculate_recurring_days($recur_data['recuring_frequency']);
        $due_date = $this->invoice_model->get_table_field('tbl_invoices', array('invoices_id' => $invoices_id), 'due_date');
        $next_date = date("Y-m-d", strtotime($due_date . "+ " . $recur_days . " days"));
        if ($recur_data['recur_end_date'] == '') {
            $recur_end_date = '0000-00-00';
        } else {
            $recur_end_date = date('Y-m-d', strtotime($recur_data['recur_end_date']));
        }
        $update_invoice = array(
            'recurring' => 'Yes',
            'recuring_frequency' => $recur_days,
            'recur_frequency' => $recur_data['recuring_frequency'],
            'recur_start_date' => date('Y-m-d', strtotime($recur_data['recur_start_date'])),
            'recur_end_date' => $recur_end_date,
            'recur_next_date' => $next_date
        );
        $this->invoice_model->_table_name = 'tbl_invoices';
        $this->invoice_model->_primary_key = 'invoices_id';
        $this->invoice_model->save($update_invoice, $invoices_id);
        return TRUE;
    }

    function get_calculate_recurring_days($recuring_frequency) {
        switch ($recuring_frequency) {
            case '7D':
                return 7;
                break;
            case '1M':
                return 31;
                break;
            case '3M':
                return 90;
                break;
            case '6M':
                return 182;
                break;
            case '1Y':
                return 365;
                break;
        }
    }

    public function stop_recurring($invoices_id) {
        $update_recur = array(
            'recurring' => 'No',
            'recur_end_date' => date('Y-m-d'),
            'recur_next_date' => '0000-00-00'
        );
        $this->invoice_model->_table_name = 'tbl_invoices';
        $this->invoice_model->_primary_key = 'invoices_id';
        $this->invoice_model->save($update_recur, $invoices_id);
        // Log Activity
        $activity = array(
            'user' => $this->session->userdata('user_id'),
            'module' => 'invoice',
            'module_field_id' => $invoices_id,
            'activity' => 'activity_recurring_stopped',
            'icon' => 'fa-plus'
        );
        $this->invoice_model->_table_name = 'tbl_activities';
        $this->invoice_model->_primary_key = 'activities_id';
        $this->invoice_model->save($activity);
        // messages for user
        $type = "success";
        $message = lang('recurring_invoice_stopped');
        set_message($type, $message);
        redirect('admin/invoice/manage_invoice');
    }

    public function insert_items($invoices_id) {
        $data['invoices_id'] = $invoices_id;
        $data['modal_subview'] = $this->load->view('admin/invoice/_modal_insert_items', $data, FALSE);
        $this->load->view('admin/_layout_modal', $data);
    }

    public function add_insert_items($invoices_id) {
        $saved_items_id = $this->input->post('saved_items_id', TRUE);
        if (!empty($saved_items_id)) {
            foreach ($saved_items_id as $v_items_id) {
                $items_info = $this->invoice_model->check_by(array('saved_items_id' => $v_items_id), 'tbl_saved_items');

                $data['quantity'] = $items_info->quantity;
                $data['invoices_id'] = $invoices_id;
                $data['item_name'] = $items_info->item_name;
                $data['item_desc'] = $items_info->item_desc;
                $data['unit_cost'] = $items_info->unit_cost;
                $data['item_tax_rate'] = $items_info->item_tax_rate;
                $data['item_tax_total'] = $items_info->item_tax_total;
                $data['total_cost'] = $items_info->total_cost;
                // get all client
                $this->invoice_model->_table_name = 'tbl_items';
                $this->invoice_model->_primary_key = 'items_id';
                $items_id = $this->invoice_model->save($data);
                $action = lang('activity_invoice_items_added');
                $msg = lang('invoice_item_added');
                $activity = array(
                    'user' => $this->session->userdata('user_id'),
                    'module' => 'invoice',
                    'module_field_id' => $items_id,
                    'activity' => $action,
                    'icon' => 'fa-circle-o',
                    'value1' => $items_info->item_name
                );
                $this->invoice_model->_table_name = 'tbl_activities';
                $this->invoice_model->_primary_key = 'activities_id';
                $this->invoice_model->save($activity);
            }
            $type = "success";
            $msg = lang('invoice_item_added');
        } else {
            $type = "error";
            $msg = 'please Select an items';
        }
        $message = $msg;
        set_message($type, $message);
        redirect('admin/invoice/manage_invoice/invoice_details/' . $invoices_id);
    }

    public function add_item($id = NULL) {

        $data = $this->invoice_model->array_from_post(array('invoices_id', 'item_order'));
        $quantity = $this->input->post('quantity', TRUE);
        $array_data = $this->invoice_model->array_from_post(array('item_name', 'item_desc', 'item_tax_rate', 'unit_cost'));
        if (!empty($quantity)) {
            foreach ($quantity as $key => $value) {
                $data['quantity'] = $value;
                $data['item_name'] = $array_data['item_name'][$key];
                $data['item_desc'] = $array_data['item_desc'][$key];
                $data['unit_cost'] = $array_data['unit_cost'][$key];
                $data['item_tax_rate'] = $array_data['item_tax_rate'][$key];
                $sub_total = $data['unit_cost'] * $data['quantity'];

                $data['item_tax_total'] = ($data['item_tax_rate'] / 100) * $sub_total;
                $data['total_cost'] = $sub_total + $data['item_tax_total'];

                // get all client
                $this->invoice_model->_table_name = 'tbl_items';
                $this->invoice_model->_primary_key = 'items_id';
                if (!empty($id)) {
                    $items_id = $id;
                    $this->invoice_model->save($data, $id);
                    $action = lang('activity_invoice_items_updated');
                    $msg = lang('invoice_item_updated');
                } else {
                    $items_id = $this->invoice_model->save($data);
                    $action = lang('activity_invoice_items_added');
                    $msg = lang('invoice_item_added');
                }
                $activity = array(
                    'user' => $this->session->userdata('user_id'),
                    'module' => 'invoice',
                    'module_field_id' => $items_id,
                    'activity' => $action,
                    'icon' => 'fa-circle-o',
                    'value1' => $data['item_name']
                );
                $this->invoice_model->_table_name = 'tbl_activities';
                $this->invoice_model->_primary_key = 'activities_id';
                $this->invoice_model->save($activity);
            }
        }
        $type = "success";
        $message = $msg;
        set_message($type, $message);
        redirect('admin/invoice/manage_invoice/invoice_details/' . $data['invoices_id']);
    }

    public function change_status($action, $id) {
        $where = array('invoices_id' => $id);
        if ($action == 'hide') {
            $data = array('show_client' => 'No');
        } else {
            $data = array('show_client' => 'Yes');
        }
        $this->invoice_model->set_action($where, $data, 'tbl_invoices');
        // messages for user
        $type = "success";
        $message = lang('invoice_' . $action);
        set_message($type, $message);
        redirect('admin/invoice/manage_invoice/invoice_details/' . $id);
    }

    public function delete($action, $invoices_id, $item_id = NULL) {
        $invoices_info = $this->invoice_model->check_by(array('invoices_id' => $invoices_id), 'tbl_invoices');

        if ($action == 'delete_item') {
            $this->invoice_model->_table_name = 'tbl_items';
            $this->invoice_model->_primary_key = 'items_id';
            $this->invoice_model->delete($item_id);
        } elseif ($action == 'delete_invoice') {
            $this->invoice_model->_table_name = 'tbl_items';
            $this->invoice_model->delete_multiple(array('invoices_id' => $invoices_id));

            $this->invoice_model->_table_name = 'tbl_payments';
            $this->invoice_model->delete_multiple(array('invoices_id' => $invoices_id));

            $this->invoice_model->_table_name = 'tbl_invoices';
            $this->invoice_model->_primary_key = 'invoices_id';
            $this->invoice_model->delete($invoices_id);
        } elseif ($action == 'delete_payment') {
            $this->invoice_model->_table_name = 'tbl_payments';
            $this->invoice_model->_primary_key = 'payments_id';
            $this->invoice_model->delete($invoices_id);
        }
        $type = "success";
        $activity = array(
            'user' => $this->session->userdata('user_id'),
            'module' => 'invoice',
            'module_field_id' => $invoices_id,
            'activity' => lang('activity_invoice' . $action),
            'icon' => 'fa-circle-o',
            'value1' => $invoices_info->reference_no,
        );
        $this->invoice_model->_table_name = 'tbl_activities';
        $this->invoice_model->_primary_key = 'activities_id';
        $this->invoice_model->save($activity);

        if ($action == 'delete_item') {
            $text = lang('invoice_item_deleted');
            set_message($type, $text);
            redirect('admin/invoice/manage_invoice/invoice_details/' . $invoices_id);
        } elseif ($action == 'delete_payment') {
            $text = lang('payment_deleted');
            set_message($type, $text);
            redirect('admin/invoice/manage_invoice/all_payments');
        } else {
            $text = lang('deleted_invoice');
            set_message($type, $text);
            redirect('admin/invoice/manage_invoice');
        }
    }

    public function get_payemnt($invoices_id) {

        $due = round($this->invoice_model->calculate_to('invoice_due', $invoices_id), 2);

        $paid_amount = $this->input->post('amount', TRUE);

        if ($paid_amount != 0) {
            if ($paid_amount > $due) {
                // messages for user
                $type = "error";
                $message = lang('overpaid_amount');
                set_message($type, $message);
                redirect('admin/invoice/manage_invoice/payment/' . $invoices_id);
            } else {
                $inv_info = $this->invoice_model->check_by(array('invoices_id' => $invoices_id), 'tbl_invoices');
                $data = array(
                    'invoices_id' => $invoices_id,
                    'paid_by' => $inv_info->client_id,
                    'payment_method' => $this->input->post('payment_method', TRUE),
                    'currency' => $this->input->post('currency', TRUE),
                    'amount' => $paid_amount,
                    'payment_date' => date('Y-m-d', strtotime($this->input->post('payment_date', TRUE))),
                    'trans_id' => $this->input->post('trans_id'),
                    'notes' => $this->input->post('notes'),
                    'month_paid' => date("m", strtotime($this->input->post('payment_date', TRUE))),
                    'year_paid' => date("Y", strtotime($this->input->post('payment_date', TRUE))),
                );

                $this->invoice_model->_table_name = 'tbl_payments';
                $this->invoice_model->_primary_key = 'payments_id';
                $this->invoice_model->save($data);

                $currency = $this->invoice_model->client_currency_sambol($inv_info->client_id);
                $activity = array(
                    'user' => $this->session->userdata('user_id'),
                    'module' => 'invoice',
                    'module_field_id' => $invoices_id,
                    'activity' => lang('activity_new_payment'),
                    'icon' => 'fa-usd',
                    'value1' => $currency->symbol . ' ' . $paid_amount,
                    'value2' => $inv_info->reference_no,
                );
                $this->invoice_model->_table_name = 'tbl_activities';
                $this->invoice_model->_primary_key = 'activities_id';
                $this->invoice_model->save($activity);

                if ($this->input->post('send_thank_you') == 'on') {
                    $this->send_payment_email($invoices_id, $paid_amount); //send thank you email
                }
            }
        }
        // messages for user
        $type = "success";
        $message = lang('generate_payment');
        set_message($type, $message);
        redirect('admin/invoice/manage_invoice/invoice_details/' . $invoices_id);
    }

    public function update_payemnt($payments_id) {
        $data = array(
            'amount' => $this->input->post('amount', TRUE),
            'payment_method' => $this->input->post('payment_method', TRUE),
            'payment_date' => date('Y-m-d', strtotime($this->input->post('payment_date', TRUE))),
            'notes' => $this->input->post('notes', TRUE),
            'month_paid' => date("m", strtotime($this->input->post('payment_date', TRUE))),
            'year_paid' => date("Y", strtotime($this->input->post('payment_date', TRUE))),
        );
        $this->invoice_model->_table_name = 'tbl_payments';
        $this->invoice_model->_primary_key = 'payments_id';
        $this->invoice_model->save($data, $payments_id);

        $activity = array(
            'user' => $this->session->userdata('user_id'),
            'module' => 'invoice',
            'module_field_id' => $payments_id,
            'activity' => lang('activity_update_payment'),
            'icon' => 'fa-usd',
            'value1' => $data['amount'],
            'value2' => $data['payment_date'],
        );
        $this->invoice_model->_table_name = 'tbl_activities';
        $this->invoice_model->_primary_key = 'activities_id';
        $this->invoice_model->save($activity);

        // messages for user
        $type = "success";
        $message = lang('generate_payment');
        set_message($type, $message);
        redirect('admin/invoice/manage_invoice/all_payments');
    }

    function send_payment_email($invoices_id, $paid_amount) {
        $email_template = $this->invoice_model->check_by(array('email_group' => 'payment_email'), 'tbl_email_templates');
        $message = $email_template->template_body;
        $subject = $email_template->subject;

        $inv_info = $this->invoice_model->check_by(array('invoices_id' => $invoices_id), 'tbl_invoices');
        $currency = $inv_info->currency;
        $reference = $inv_info->reference_no;

        $invoice_currency = str_replace("{INVOICE_CURRENCY}", $currency, $message);
        $reference = str_replace("{INVOICE_REF}", $reference, $invoice_currency);
        $amount = str_replace("{PAID_AMOUNT}", $paid_amount, $reference);
        $message = str_replace("{SITE_NAME}", config_item('company_name'), $amount);

        $data['message'] = $message;
        $message = $this->load->view('email_template', $data, TRUE);
        $client_info = $this->invoice_model->check_by(array('client_id' => $inv_info->client_id), 'tbl_client');

        $address = $client_info->email;

        $params['recipient'] = $address;

        $params['subject'] = '[ ' . config_item('company_name') . ' ]' . ' ' . $subject;
        $params['message'] = $message;
        $params['resourceed_file'] = '';

        $activity = array(
            'user' => $this->session->userdata('user_id'),
            'module' => 'invoice',
            'module_field_id' => $invoices_id,
            'activity' => lang('activity_send_payment'),
            'icon' => 'fa-usd',
            'value1' => $reference,
            'value2' => $currency . ' ' . $amount,
        );
        $this->invoice_model->_table_name = 'tbl_activities';
        $this->invoice_model->_primary_key = 'activities_id';
        $this->invoice_model->save($activity);

        $this->invoice_model->send_email($params);
    }

    public function send_invoice_email($invoice_id) {

        $ref = $this->input->post('ref', TRUE);
        $subject = $this->input->post('subject', TRUE);
        $message = $this->input->post('message', TRUE);

        $client_name = str_replace("{CLIENT}", $this->input->post('client_name', TRUE), $message);
        $Ref = str_replace("{REF}", $ref, $client_name);
        $Amount = str_replace("{AMOUNT}", $this->input->post('amount'), $Ref);
        $Currency = str_replace("{CURRENCY}", $this->input->post('currency', TRUE), $Amount);
        $Due_date = str_replace("{DUE_DATE}", $this->input->post('due_date', TRUE), $Currency);
        if (!empty($Due_date)) {
            $Due_date = $Due_date;
        } else {
            $Due_date = $Currency;
        }
        $link = str_replace("{INVOICE_LINK}", base_url() . 'client/invoice/manage_invoice/invoice_details/' . $invoice_id, $Due_date);
        $message = str_replace("{SITE_NAME}", config_item('company_name'), $link);

        $this->send_email_invoice($invoice_id, $message, $subject); // Email Invoice

        $data = array('emailed' => 'Yes', 'date_sent' => date("Y-m-d H:i:s", time()));

        $this->invoice_model->_table_name = 'tbl_invoices';
        $this->invoice_model->_primary_key = 'invoices_id';
        $this->invoice_model->save($data, $invoice_id);

        // Log Activity
        $activity = array(
            'user' => $this->session->userdata('user_id'),
            'module' => 'invoice',
            'module_field_id' => $invoice_id,
            'activity' => lang('activity_invoice_sent'),
            'icon' => 'fa-envelope',
            'value1' => $ref,
            'value2' => $this->input->post('currency', TRUE) . ' ' . $this->input->post('amount'),
        );
        $this->invoice_model->_table_name = 'tbl_activities';
        $this->invoice_model->_primary_key = 'activities_id';
        $this->invoice_model->save($activity);
        // messages for user
        $type = "success";
        $imessage = lang('send_invoice_successfully');
        set_message($type, $imessage);
        redirect('admin/invoice/manage_invoice/invoice_details/' . $invoice_id);
    }

    function send_email_invoice($invoice_id, $message, $subject) {
        $invoice_info = $this->invoice_model->check_by(array('invoices_id' => $invoice_id), 'tbl_invoices');
        $client_info = $this->invoice_model->check_by(array('client_id' => $invoice_info->client_id), 'tbl_client');

        $recipient = $client_info->email;

        $data['message'] = $message;

        $message = $this->load->view('email_template', $data, TRUE);
        $params = array(
            'recipient' => $recipient,
            'subject' => $subject,
            'message' => $message
        );
        $params['resourceed_file'] = '';
        $this->invoice_model->send_email($params);
    }

    public function tax_rates($action = NULL, $id = NULL) {

        $data['page'] = lang('sales');
        $data['sub_active'] = lang('tax_rates');
        if ($action == 'edit_tax_rates') {
            $data['active'] = 2;
            if (!empty($id)) {
                $data['tax_rates_info'] = $this->invoice_model->check_by(array('tax_rates_id' => $id), 'tbl_tax_rates');
            }
        } else {
            $data['active'] = 1;
        }
        if ($action == 'delete_tax_rates') {
            // Log Activity
            $activity = array(
                'user' => $this->session->userdata('user_id'),
                'module' => 'invoice',
                'module_field_id' => $id,
                'activity' => lang('activity_taxt_rate_deleted'),
                'icon' => 'fa-circle-o',
                'value1' => $data['tax_rates_info']->tax_rate_name,
            );
            $this->invoice_model->_table_name = 'tbl_activities';
            $this->invoice_model->_primary_key = 'activities_id';
            $this->invoice_model->save($activity);

            $this->invoice_model->_table_name = 'tbl_tax_rates';
            $this->invoice_model->_primary_key = 'tax_rates_id';
            $this->invoice_model->delete($id);
            // messages for user
            $type = "success";
            $message = lang('tax_deleted');
            set_message($type, $message);
            redirect('admin/invoice/tax_rates');
        } else {
            $data['title'] = "Tax Rates Info"; //Page title      
            $subview = 'tax_rates';
        }
        $user_id = $this->session->userdata('user_id');
        $user_info = $this->invoice_model->check_by(array('user_id' => $user_id), 'tbl_users');
        $data['role'] = $user_info->role_id;

        $data['subview'] = $this->load->view('admin/invoice/' . $subview, $data, TRUE);
        $this->load->view('admin/_layout_main', $data); //page load
    }

    public function save_tax_rate($id = NULL) {
        $data = $this->invoice_model->array_from_post(array('tax_rate_name', 'tax_rate_percent'));

        $this->invoice_model->_table_name = 'tbl_tax_rates';
        $this->invoice_model->_primary_key = 'tax_rates_id';
        $id = $this->invoice_model->save($data, $id);

        // Log Activity
        $activity = array(
            'user' => $this->session->userdata('user_id'),
            'module' => 'invoice',
            'module_field_id' => $id,
            'activity' => lang('activity_taxt_rate_add'),
            'icon' => 'fa-circle-o',
            'value1' => $data['tax_rate_name'],
        );
        $this->invoice_model->_table_name = 'tbl_activities';
        $this->invoice_model->_primary_key = 'activities_id';
        $this->invoice_model->save($activity);

        // messages for user
        $type = "success";
        $message = lang('tax_added');
        set_message($type, $message);
        redirect('admin/invoice/tax_rates');
    }

}
