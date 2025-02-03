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
            $AID = $post[AID];
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


            $starttime = $post['StartTime'];
            $endTime = $post['EndTime'];
            $employeeStartTime = $starttime;
            $employeeEndTime = $endTime;

            $result = calculateDailySalary($employeeStartTime, $employeeEndTime);


            $post['OTPayment'] = $result['overtimeSalary'];
            $post['PerDaySalary'] = $result['regularSalary'];
            echo "$AID";

//            p($post);
//            exit;
//
            if (!empty($AID)){
                $d = '<div class="alert alert-warning background-warning"> <button type="button" class="close" data-dismiss="alert" aria-label="Close"> <i class="icofont icofont-close-line-circled text-white"></i> </button> <strong>Attendance Updated Successfully</strong> </div>';
                $this->attendance->update($AID,$post);

            }
            else {

                $d = '<div class="alert alert-success background-success"> <button type="button" class="close" data-dismiss="alert" aria-label="Close"> <i class="icofont icofont-close-line-circled text-white"></i> </button> <strong>Attendance Marked Successfully</strong> </div>';
                $this->attendance->insert($post);
            }


            $this->session->set_flashdata('notification', $d);
            redirect('Home/dashboard');
        } else {

            echo "error go back";
        }
        redirect('Home/dashboard');
    }

    function EditAttendance(){
        $post = $this->input->post('form');
        $EmployeeID = $post[EmployeeId];
        $DateToEdit = $post[adate];
        $d['records3'] = $this->employee->get($EmployeeID)->EmployeeName;
        $d['records2'] = $this->db->query("DELETE from attendance where EmployeeId=" . $EmployeeID . " AND ADate='" . $DateToEdit . "'");
        $d = '<div class="alert alert-danger background-danger"> <button type="button" class="close" data-dismiss="alert" aria-label="Close"> <i class="icofont icofont-close-line-circled text-white"></i> </button> <strong>Attendance Deleted Successfully</strong> </div>';
        $this->session->set_flashdata('notification', $d);
        redirect('Home/dashboard');


    }


    function currentsalary($_id = 0)
    {

        $currentyear = date('Y');
        $currentmonth = date('m');


        $d['month'] = $currentmonth;
        $d['year'] = $currentyear;
        $d['records2'] = $this->employee->get($_id);

        $d['records'] = $this->db->query("select * from attendance where IsDeleted=0 AND EmployeeId=" . $_id . " AND year(ADate)=$currentyear AND month(ADate)=$currentmonth Order by AID ASC")->result();

//        foreach ($data as $row){
//
//        }

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

    function Delete_attendance(){

        $d['records'] = $this->employee->get_all();
        $this->load->view('delete_attendance',$d);
    }

    function OldSalary($_id = 0){
        $post = $this->input->post('form');
        $EmployeeID = $post[EmployeeId];
        $Month = $post[adate];

        $d['records2'] = $this->employee->get($EmployeeID);


        // Breaking the year and Month
        $searchyear = substr($Month, 0, 4);
        $searchmonth = substr($Month,  -2);
        $d['month'] = $searchmonth;
        $d['year'] = $searchyear;

        // SQl Query to get the records
        $d['records'] = $this->db->query("SELECT * FROM `attendance` WHERE ADate LIKE '$Month%' AND EmployeeId='$EmployeeID';")->result();
//        p($this->db->last_query());
//        p($d);
//        exit;
        $this->load->view('salary_report', $d);
    }

    public function days(){
        $this->load->view('days');
    }
    public function MarkAttendance(){
        $d['records'] = $this->employee->order_by('EmployeeNumber', 'ASC')->get_all();
        $this->load->view('mark_attendance',$d);

    }

    public function CalculateSalary()
    {
        $post2 = $this->input->post('form2');
        $post3 = $this->input->post('form');
        $SelectedDate = $post2[ADate];


        foreach($post3 as $record) {
//            p($record);
            $EmployeeId= $record['EmployeeId'];
            $Start_Time= $record['Start_Time'];
            $End_Time= $record['End_Time'];
            $Advance= $record['Advance'];
            $Special_Amount= $record['Special_Amount'];
            $employeeStartTime = $Start_Time;
            $employeeEndTime = $End_Time;
            $AID = $SelectedDate;
            $EmployeeOTPH = $this->employee->get($EmployeeId)->OTPH;
            $EmployeeBasicSalary = $this->employee->get($EmployeeId)->FullDaySalary;

            //setting Array Data's
            $post['adate'] = $SelectedDate;
            $post['StartTime'] = $Start_Time;
            $post['EndTime'] = $End_Time;
            $post['EmployeeId'] = $EmployeeId;
            $post['AdvanceAmount'] = $Advance;
            $post['SpecialAmount'] = $Special_Amount;


            if (empty($employeeStartTime)){
                $employeeStartTime='08:00';
                $employeeEndTime='08:00';
            }

//                echo $employeeStartTime." TIME ".$employeeEndTime;





           $result = $this->calculateDailySalary($employeeStartTime, $employeeEndTime, $EmployeeBasicSalary, $EmployeeOTPH);

         //   p($result);

            $post['OTRate'] = $EmployeeOTPH;
            $post['OTPayment'] = $result['overtimeSalary'];
            $post['PerDaySalary'] = $result['regularSalary'];


            $d = '<div class="alert alert-success background-success"> <button type="button" class="close" data-dismiss="alert" aria-label="Close"> <i class="icofont icofont-close-line-circled text-white"></i> </button> <strong>Attendance Marked Successfully</strong> </div>';
            $this->attendance->insert($post);






        } /*end of Foreach of Form Submitted Data*/
        $this->session->set_flashdata('notification', $d);
        redirect('Home/dashboard');




    }


        public function calculateDailySalary($employeeStartTime, $employeeEndTime, $EmployeeBasicSalary, $EmployeeOTPH) {


            // Define constants
            define('DAILY_SALARY', $EmployeeBasicSalary);
            define('OT_RATE_PER_HOUR', $EmployeeOTPH);
            define('WORK_START_TIME', '08:00');
            define('WORK_END_TIME', '18:00');
            define('LUNCH_START_TIME', '13:00');
            define('LUNCH_END_TIME', '14:00');

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


            if ($empStart=='28975350' && $empEnd=='28975350'){
                $overtimeHours=0;
            }
            else {

                if ($empStart < $workStart) {
                    $overtimeHours += $workStart - $empStart;
                }
                if ($empEnd > $workEnd) {
                    $overtimeHours += $empEnd - $workEnd;
                }
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



}


