<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Sku extends HX_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('sku_model', 'm_sku');
    }

    public function action_add_sku()
    {
        $this->load->view('goods/sku/addForm');
    }

    public function add_sku()
    {
        $post = $this->input->post();
        $this->m_sku->insert_sku($post);

        $this->load->helper('url');
        redirect("success");
    }

    public function delete_sku($id)
    {
        $this->m_sku->sku_delete_by_id($id);

        $this->load->helper('url');
        redirect("success");
    }
}
