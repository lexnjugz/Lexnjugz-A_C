<?php

class Global_Model extends MY_Model {

    protected $_table_name;
    protected $_order_by;

    public function select_user_roll($user_id) {
        $this->db->select('tbl_user_role.*', FALSE);
        $this->db->select('tbl_menu.link, tbl_menu.label', FALSE);
        $this->db->from('tbl_user_role');
        $this->db->join('tbl_menu', 'tbl_user_role.menu_id = tbl_menu.menu_id', 'left');
        $this->db->where('tbl_user_role.user_id', $user_id);
        $query_result = $this->db->get();
        $result = $query_result->result();
        return $result;
    }

}
