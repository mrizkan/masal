<?php

include_once APPPATH . "core/MY_Controller.php";

class Attendance extends MY_Controller
{

    function __construct()
    {
        parent::__construct();
        $this->load->model('Attendance_model', 'attendance');
        $this->load->model('Employee_model', 'employee');
        $this->controller = get_class();
    }

    function AddAttendance()
    {

        $this->form_validation->set_rules("form[EmployeeId]", "Employee Name", "required");


        if ($this->form_validation->run()) {

            $post = $this->input->post('form');
            $EmployeeID = $post[EmployeeId];
            $EmployeeOTPH = $this->employee->get($EmployeeID)->OTPH;
            $EmployeeBasicSalary = $this->employee->get($EmployeeID)->FullDaySalary;
            $post['OTRate'] = $EmployeeOTPH;
            $EmployeeOTH = 0;
            $MorningOT = 0;
            $EveningOT = 0;
            $TotalOTHoursPerDay = 0;

            $starttime = $post['StartTime'];
            $first = new DateTime($starttime);
            $StartHour = $first->format('H');
            if ($StartHour < 8) {
                $MorningOT = 8 - $StartHour;

            }

            $endTime = $post['EndTime'];
            $EndHour = date('h', strtotime($endTime));
            if ($EndHour > 6) {
                $EveningOT = $EndHour - 6;
            }


            $TotalOTHoursPerDay = $MorningOT + $EveningOT;
            if (isset($_POST['fullday'])) {
                $WorkedTime = 10;
            } else {
                $first = new DateTime($starttime);
                $second = new DateTime($endTime);
                $diff = $first->diff($second);
                $diff->format('%H');
                $WorkedTime = $diff->format('%H');

            }

            $WorkedTime2 = $WorkedTime - $TotalOTHoursPerDay;
            $OTHours = $TotalOTHoursPerDay;

            if ($OTHours > 0) {
                $currentyear = date('Y');
                $currentmonth = date('m');

                $EmployeeOTPayment = $OTHours * $EmployeeOTPH;
                $post['OTPayment'] = $EmployeeOTPayment;



                $EmployeePerDaySalary = $EmployeeBasicSalary;
                $FinalDaySalary = number_format((float)$EmployeePerDaySalary, 2, '.', '');
                $post['PerDaySalary'] = $FinalDaySalary;
            }

            if ($WorkedTime2 <= 10) {

                $currentyear = date('Y');
                $currentmonth = date('m');

//                $NumberOfDaysOnThisMonth = cal_days_in_month(CAL_GREGORIAN, $currentmonth, $currentyear);



//                $EmployeePerDaySalary = $EmployeeBasicSalary / $NumberOfDaysOnThisMonth;
                $EmployeePerDaySalary = $EmployeeBasicSalary;
                $EmployeePerHourSalary = $EmployeePerDaySalary / 10;
                $PerDaySalary = $EmployeePerHourSalary * $WorkedTime2;

                $FinalDaySalary = number_format((float)$PerDaySalary, 2, '.', '');

                $post['PerDaySalary'] = $FinalDaySalary;


            }


            $this->attendance->insert($post);

            $d = '<div class="alert alert-success background-success"> <button type="button" class="close" data-dismiss="alert" aria-label="Close"> <i class="icofont icofont-close-line-circled text-white"></i> </button> <strong>Attendance Marked Successfully</strong> </div>';
            $this->session->set_flashdata('notification', $d);
            redirect('Home/dashboard');
        } else {

            echo "error go back";
        }
        redirect('Home/dashboard');
    }


    function currentsalary($_id = 0)
    {

        $currentyear = date('Y');
        $currentmonth = date('m');


        $d['month'] = $currentmonth;
        $d['year'] = $currentyear;
        $d['records2'] = $this->employee->get($_id);

        $d['records'] = $this->db->query("select * from attendance where EmployeeId=" . $_id . " AND year(ADate)=$currentyear AND month(ADate)=$currentmonth Order by AID ASC")->result();

//                    p($this->db->last_query());
//
//        p($d);
//        exit;

        $this->load->view('salary_report', $d);
    }

    function previoussalary($_id = 0)
    {

        $currentyear = date('Y');
        $currentmonth = date('m');

        if ($currentmonth == 1) {

            $searchmonth = 12;
            $searchyear = $currentyear - 1;
        } else {
            $searchmonth = $currentmonth - 1;
            $searchyear = $currentyear;


        }

        $d['records2'] = $this->employee->get($_id);
        $d['month'] = $searchmonth;
        $d['year'] = $searchyear;

        $d['records'] = $this->db->query("select * from attendance where EmployeeId='1' AND year(ADate)=$searchyear AND month(ADate)=$searchmonth Order by AID ASC")->result();

//                    p($this->db->last_query());
// Print items
//        p($d);
//        exit;

        $this->load->view('salary_report', $d);
    }

    function Reports(){

        $d['records'] = $this->employee->get_all();
        $this->load->view('old_salary_report',$d);
    }


}


