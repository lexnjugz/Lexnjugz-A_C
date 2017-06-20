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
class Message extends Admin_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('message_model');
    }

    public function index() {
        $data['title'] = lang('private_chat');
        $data['page'] = 'message';
        $data['subview'] = $this->load->view('admin/chat', $data, TRUE);
        $this->load->view('admin/_layout_main', $data);
    }

    public function get_chat() {
        $data['title'] = config_item('company_name');
        $data['subview'] = $this->load->view('admin/chat', $data, TRUE);
        $this->load->view('admin/_layout_main', $data);
    }

    public function send_message() {
        $data = $this->message_model->array_from_post(array('message', 'receive_user_id'));
        if (!empty($data['receive_user_id'])) {
            $data['send_user_id'] = $this->session->userdata('user_id');
            $this->message_model->_table_name = 'tbl_private_message_send';
            $this->message_model->_primary_key = 'private_message_send_id';
            $this->message_model->save($data);
        }
        redirect('admin/message/get_chat/' . $data['receive_user_id']);
    }

}
