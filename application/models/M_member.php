<?php if ( ! defined('BASEPATH')) exit('No direct script access
allowed');
class m_member extends CI_Model{

 function getMember(){
 $this->db->select('*');
 $this->db->from('member');
 $query = $this->db->get();
 return $query;
 }

    function upload_file($filename)
    {
        $this->load->library('upload'); // Load librari upload
        $config['upload_path'] = './excel/';
        $config['allowed_types'] = 'xlsx';
        $config['max_size'] = '2048';
        $config['overwrite'] = true;
        $config['file_name'] = $filename;
        $this->upload->initialize($config); // Load konfigurasi uploadnya
            // Jika gagal :
            $return = array('result' => 'failed', 'file' => '', 'error' => $this->upload->display_errors());
            return $return;
        }
    }

    public function tambah($data)
    {
        $this->db->insert_batch('member', $data);
    }
}
?>