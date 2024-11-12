<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Login extends CI_Controller {
    public function __construct() {
        parent::__construct();
        $this->load->model('m_user');
        $this->load->library('session');
        $this->load->library('email');
        date_default_timezone_set('Asia/Jakarta');
    }

    public function index() {
        if ($this->session->userdata('level') == 'Admin') {
            redirect('admin', 'refresh');
        } elseif ($this->session->userdata('level') == 'Petugas') {
            redirect('petugas', 'refresh');
        } else {
            $this->load->view('login');
        }
    }

    public function login_act() {
        if ($this->input->method() != 'post') {
            redirect('login');
        }
        
        $email = $this->input->post('email');
        $password = md5($this->input->post('password'));

        // Validasi format email
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->session->set_flashdata('peringatan', 'Format email salah');
            redirect('login');
        }

        // Cek email dan password di database
        $cekEmailUser = $this->m_user->getEmailUser($email);
        $cekPassUser = $this->m_user->getPassUser($password);

        if ($cekEmailUser->num_rows() == 0) {
            $this->session->set_flashdata('peringatan', 'Email tidak ditemukan');
            redirect('login');
        } elseif ($cekPassUser->num_rows() == 0) {
            $this->session->set_flashdata('peringatan', 'Password salah');
            redirect('login');
        } else {
            foreach ($cekEmailUser->result() as $data) {
                $data_user['id'] = $data->idUser;
                $data_user['nama'] = $data->nama;
                $data_user['email'] = $data->email;
                $data_user['level'] = $data->level;
                $this->session->set_userdata($data_user);

                if ($data->level == "Petugas") {
                    redirect('petugas');
                } elseif ($data->level == "Admin") {
                    redirect('admin');
                }
            }
        }
        $this->load->view('login');
    }

    public function lupaPassword() {
        if ($this->session->userdata('level') == 'Admin') {
            redirect('admin');
        } elseif ($this->session->userdata('level') == 'Petugas') {
            redirect('petugas');
        } else {
            $this->load->view('lupaPassword');
        }
    }

    public function lupaPassword_act() {
        $email = $this->input->post('email');
        $cekEmailUser = $this->m_user->getEmailUser($email);

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->session->set_flashdata('peringatan', 'Format email salah');
            redirect('lupaPassword');
        } elseif ($cekEmailUser->num_rows() == 0) {
            $this->session->set_flashdata('peringatan', 'Email tidak ditemukan');
            redirect('lupaPassword');
        } else {
            $pass = "129FAasdsk25kwBjakjDlff";
            $panjang = 8;
            $len = strlen($pass);
            $start = $len - $panjang;
            $xx = rand(0, $start);
            $yy = str_shuffle($pass);
            $passwordbaru = substr($yy, $xx, $panjang);

            // Konfigurasi email
            $config = array(
                'protocol' => 'smtp',
                'smtp_host' => 'smtp web',
                'smtp_port' => 'port SSL/TSL',
                'smtp_user' => '', // email Anda
                'smtp_pass' => '', // password Anda
                'mailtype' => 'html',
                'charset' => 'iso-8859-1'
            );

            $this->email->initialize($config);
            $this->email->set_newline("\r\n");

            // Alamat pengirim dan penerima email
            $this->email->from('email anda', 'Keterangan Anda');
            $this->email->to($email);
            $this->email->subject('Lupa Password');
            $this->email->message("
                <html>
                <head></head>
                <body>
                    <br>Kami telah mengatur ulang password Anda, berikut password baru Anda:
                    <br><br>
                    <p>Password Baru: <b>$passwordbaru</b></p>
                    <p>Anda dapat login kembali dengan password baru Anda <a href='" . base_url() . "login' target='_blank'>disini</a>.</p>
                </body>
                </html>
            ");

            if ($this->email->send()) {
                $data['password'] = md5($passwordbaru);
                $this->m_user->ubahpasswordUser($email, $data);
                $this->session->set_flashdata('peringatan', 'Email terkirim');
            } else {
                $this->session->set_flashdata('peringatan', 'Email gagal terkirim / Periksa koneksi Anda');
            }
            redirect('lupaPassword');
        }
    }
}
