<?php
//include_once APPPATH . "modules/admin/core/MY_Controller.php";
include_once APPPATH ."core/MY_Controller.php" ;
class Home extends CI_Controller
//class Home extends MY_Controller
{


    function __construct()
    {
        parent::__construct();

        $this->load->model('Employee_model', 'employee');
        $this->controller = get_class();
    }


    function index()
    {
        if ($this->session->has_userdata('user') == FALSE) {
            $d['error'] = "";
            $this->form_validation->set_rules('username', '', 'required');
            $this->form_validation->set_rules('password', '', 'required|sha1');
            if ($this->form_validation->run()) {
                $this->load->model('user_model', 'user');

                $user = $this->user->get_by([
                    'Username' => $this->input->post('username'),
                    'Password' => $this->input->post('password')
                ]);

                if (is_object($user)) {
                    $this->session->set_userdata("user", $user);
                    if ($this->session->userdata('url'))
                        redirect($this->session->userdata('url'));
                    redirect(base_url(''));
                } else {
                    $d['error'] = "Invalid Username or Password";
                }
            }
            $this->load->view('login', $d);
        } else {
            if ($this->session->has_userdata('url'))
                redirect($this->session->userdata('url'));
            $this->controller = 'Home';

            
            $User= $this->session->user;
           $d['records'] = $this->employee->get_all();
//        p($d['records']);
//        p($this->db->last_query());
//        exit;

            $this->load->view('dashboard',$d);

        }
    }

    function logout()
    {
        $this->session->sess_destroy();
        $this->load->view('login');
    }

    function dashboard()
    {
        $d['records'] = $this->employee->order_by('EmployeeNumber', 'ASC')->get_all();

//        p($d['records']);
//        p($this->db->last_query());
//        exit;
        $this->load->view('dashboard',$d);
    }

}