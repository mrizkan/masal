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



            // Define constants
            define('DAILY_SALARY', $EmployeeBasicSalary);
            define('OT_RATE_PER_HOUR', $EmployeeOTPH);
            define('WORK_START_TIME', '08:00');
            define('WORK_END_TIME', '18:00');
            define('LUNCH_START_TIME', '13:00');
            define('LUNCH_END_TIME', '14:00');

            function calculateDailySalary($employeeStartTime, $employeeEndTime) {
                // Convert times to minutes since midnight
                $workStart = strtotime(WORK_START_TIME) / 60;
                $workEnd = strtotime(WORK_END_TIME) / 60;
                $lunchStart = strtotime(LUNCH_START_TIME) / 60;
                $lunchEnd = strtotime(LUNCH_END_TIME) / 60;
                $empStart = strtotime($employeeStartTime) / 60;
                $empEnd = strtotime($employeeEndTime) / 60;

                // Calculate regular hours
                $regularHours = min($workEnd, $empEnd) - max($workStart, $empStart);

                // Deduct lunch hour only if employee works past lunch time
                if ($empEnd > $lunchEnd && $empStart < $lunchStart)  {
                    $regularHours -= ($lunchEnd - $lunchStart);
                }



                $regularHours = max(0, $regularHours / 60); // Convert to hours and ensure non-negative


                // Calculate overtime hours
                $overtimeHours = 0;

                if ($empStart < $workStart) {
                    $overtimeHours += $workStart - $empStart;
                }
                if ($empEnd > $workEnd) {
                    $overtimeHours += $empEnd - $workEnd;
                }
                $overtimeHours = $overtimeHours / 60; // Convert to hours


                $dailySalary=DAILY_SALARY/9;



                // Calculate salary
                $regularSalary = $dailySalary*$regularHours;
                $overtimeSalary = $overtimeHours * OT_RATE_PER_HOUR;
                $totalSalary = $regularSalary + $overtimeSalary;

                return [
                    'regularHours' => $regularHours,
                    'overtimeHours' => $overtimeHours,
                    'regularSalary' => $regularSalary,
                    'overtimeSalary' => $overtimeSalary,
                    'totalSalary' => $totalSalary
                ];
            }

// Example usage for Saman
            $starttime = $post['StartTime'];
            $endTime = $post['EndTime'];
            $employeeStartTime = $starttime;
            $employeeEndTime = $endTime;

            $result = calculateDailySalary($employeeStartTime, $employeeEndTime);

//            echo "Employee (Saman) Start Time: $employeeStartTime\n"; echo '<br/>';
//            echo "Employee (Saman) End Time: $employeeEndTime\n"; echo '<br/>';
//            echo "Regular Hours Worked: " . number_format($result['regularHours'], 2) . "\n"; echo '<br/>';
//            echo "Overtime Hours: " . number_format($result['overtimeHours'], 2) . "\n"; echo '<br/>';
//            echo "Regular Salary: " . number_format($result['regularSalary'], 2) . "\n"; echo '<br/>';
//            echo "Overtime Salary: " . number_format($result['overtimeSalary'], 2) . "\n"; echo '<br/>';
//            echo "Total Daily Salary: " . number_format($result['totalSalary'], 2) . "\n";

            $post['OTPayment'] = number_format($result['overtimeSalary'], 2);
            $post['PerDaySalary'] = number_format($result['regularSalary'], 2);
//

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


