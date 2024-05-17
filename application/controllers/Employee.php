<?php

include_once APPPATH ."core/MY_Controller.php" ;

class Employee extends MY_Controller
{

    function __construct()
    {
        parent::__construct();
        $this->load->model('Employee_model', 'employee');
        $this->controller = get_class();
    }


    function AddEmployee($_id = 0)
    {
        $d['records'] = '';
        $this->_form_body($d);
    }

    function _form_body($d, $_id = 0)
    {

        $this->form_validation->set_rules("form[EmployeeName]", "Employee Name", "required");
        if ($this->form_validation->run()) {

            $post = $this->input->post('form');
            $SalaryPerDay=$post['BasicSalary'];
            $SalaryPerHour=$SalaryPerDay/9;

            if ($_id) {
                //update Query()
                $this->employee->update($_id, $post);
                $d = '<div class="alert alert-warning background-warning"> <button type="button" class="close" data-dismiss="alert" aria-label="Close"> <i class="icofont icofont-close-line-circled text-white"></i> </button> <strong> Successfully Updated</strong> </div>';
                $this->session->set_flashdata('notification', $d);
                redirect('Employee/AddEmployee');
            } else {
                $this->employee->insert($post);

                $d = '<div class="alert alert-success background-success"> <button type="button" class="close" data-dismiss="alert" aria-label="Close"> <i class="icofont icofont-close-line-circled text-white"></i> </button> <strong>  Successfully Added</strong> </div>';
                $this->session->set_flashdata('notification', $d);
                redirect(current_url());
            }
        }
        $d['records'] = $this->employee->get_all();

        $this->load->view('Add_Employee', $d);
    }


    function edit($_id = 0)
    {
        if ($_id) {
            $r['records2'] = $this->employee->get($_id);
            $this->_form_body($r, $_id);
//            $this->load->view('create_sales_rep', $d);
        }
    }

    function Delete($_id = 0)
    {
        $this->employee->delete($_id);
        redirect('Employee/AddEmployee');
    }



}


