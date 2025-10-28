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



    function MarkAttendanceSingle()
    {
        redirect('/');
    }


    public function CalculateSalary()
    {
        $post2 = $this->input->post('form2');
        $post3 = $this->input->post('form');
        $SelectedDate = $post2[ADate];


        foreach ($post3 as $record) {

            $EmployeeBasicSalary = 0;
            $EmployeeOTPH = 0;
            $EmployeeId = $record['EmployeeId'];
            $Start_Time = $record['Start_Time'];
            $End_Time = $record['End_Time'];
            $Advance = $record['Advance'];
            $Special_Amount = $record['Special_Amount'];
            $employeeStartTime = $Start_Time;
            $employeeEndTime = $End_Time;
            $AID = $SelectedDate;
            $EmployeeOTPH = $this->employee->get($EmployeeId)->OTPH;
            $EmployeeBasicSalary = $this->employee->get($EmployeeId)->FullDaySalary;
//        p($EmployeeOTPH);
//            p($this->db->last_query());
//        exit;

            //setting Array Data's
            $post['adate'] = $SelectedDate;
            $post['StartTime'] = $Start_Time;
            $post['EndTime'] = $End_Time;
            $post['EmployeeId'] = $EmployeeId;
            $post['AdvanceAmount'] = $Advance;
            $post['SpecialAmount'] = $Special_Amount;

            if (empty($employeeStartTime)) {
                $employeeStartTime = '08:00';
                $employeeEndTime = '08:00';
            }

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

    public function CalculateSalarySingle()
    {
        // Disable any profiler or debug output
        $this->output->enable_profiler(FALSE);

        // Set JSON header
        header('Content-Type: application/json');

        try {
            // Get POST data
            $EmployeeId = $this->input->post('EmployeeId');
            $SelectedDate = $this->input->post('ADate');
            $Start_Time = $this->input->post('Start_Time');
            $End_Time = $this->input->post('End_Time');
            $Advance = $this->input->post('Advance');
            $Special_Amount = $this->input->post('Special_Amount');

            // Validation
            if (empty($SelectedDate) || empty($EmployeeId) || empty($Start_Time) || empty($End_Time)) {
                echo json_encode([
                    'status' => 'error',
                    'message' => 'All fields are required'
                ]);
                exit;
            }

            // Get employee details
            $employeeData = $this->employee->get($EmployeeId);

            if (!$employeeData) {
                echo json_encode([
                    'status' => 'error',
                    'message' => 'Employee not found'
                ]);
                exit;
            }

            $EmployeeOTPH = $employeeData->OTPH;
            $EmployeeBasicSalary = $employeeData->FullDaySalary;

            $employeeStartTime = $Start_Time;
            $employeeEndTime = $End_Time;

            // Set default times if empty
            if (empty($employeeStartTime)) {
                $employeeStartTime = '08:00';
                $employeeEndTime = '08:00';
            }

            // Calculate salary
            $result = $this->calculateDailySalary($employeeStartTime, $employeeEndTime, $EmployeeBasicSalary, $EmployeeOTPH);

            // Prepare data for insertion
            $post = array(
                'adate' => $SelectedDate,
                'StartTime' => $Start_Time,
                'EndTime' => $End_Time,
                'EmployeeId' => $EmployeeId,
                'AdvanceAmount' => $Advance ? $Advance : 0,
                'SpecialAmount' => $Special_Amount ? $Special_Amount : 0,
                'OTRate' => $EmployeeOTPH,
                'OTPayment' => $result['overtimeSalary'],
                'PerDaySalary' => $result['regularSalary'],
                'TotalOTHours' => $result['overtimeHours']
            );

            // Check if attendance already exists for this employee on this date
            $existing = $this->db->query("SELECT * FROM attendance WHERE EmployeeId = ? AND ADate = ?",
                array($EmployeeId, $SelectedDate))->row();

            if ($existing) {
                // Update existing record
                $this->db->where('EmployeeId', $EmployeeId);
                $this->db->where('ADate', $SelectedDate);
                $inserted = $this->db->update('attendance', $post);
                $message = 'Attendance updated successfully';
            } else {
                // Insert new record
                $inserted = $this->attendance->insert($post);
                $message = '<button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                                                        <i class="icofont icofont-close-line-circled"></i>
                                                                    </button>
                                                                    <strong>Success!</strong>';
            }

            if ($inserted) {
                echo json_encode([
                    'status' => 'success',
                    'message' => $message,
                    'data' => $post
                ]);
            } else {
                echo json_encode([
                    'status' => 'error',
                    'message' => 'Failed to save attendance'
                ]);
            }

        } catch (Exception $e) {
            echo json_encode([
                'status' => 'error',
                'message' => 'Error: ' . $e->getMessage()
            ]);
        }

        exit;
    }


    public function calculateDailySalary($employeeStartTime, $employeeEndTime, $EmployeeBasicSalary, $EmployeeOTPH)
    {


        // Define constants
        define('DAILY_SALARY', $EmployeeBasicSalary);
        define('OT_RATE_PER_HOUR', $EmployeeOTPH);
        define('WORK_START_TIME', '08:00');
        define('WORK_END_TIME', '18:00');
        define('LUNCH_START_TIME', '13:00');
        define('LUNCH_END_TIME', '14:00');
        $overtimeHours = 0;
        $regularHours = 0;
        $dailySalary = 0;


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
        if ($empEnd > $lunchEnd && $empStart < $lunchStart) {
            $regularHours -= ($lunchEnd - $lunchStart);
        }


        $regularHours = max(0, $regularHours / 60); // Convert to hours and ensure non-negative


        // Calculate overtime hours

        if ($empStart < $workStart) {
            $overtimeHours += $workStart - $empStart;
        }
        if ($empEnd > $workEnd) {
            $overtimeHours += $empEnd - $workEnd;
        }

        $overtimeHours = $overtimeHours / 60; // Convert to hours


        $maxRegularHours = 9;


        // Calculate salary

        $regularSalary = ($regularHours / $maxRegularHours) * $EmployeeBasicSalary;
        $overtimeSalary = $overtimeHours * $EmployeeOTPH;
        $totalSalary = $regularSalary + $overtimeSalary;


        return [
            'regularHours' => $regularHours,
            'overtimeHours' => $overtimeHours,
            'regularSalary' => $regularSalary,
            'overtimeSalary' => $overtimeSalary,
            'totalSalary' => $totalSalary
        ];
    }


    function EditAttendance()
    {
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
//        p($d);q
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

    function Reports()
    {

        $d['records'] = $this->employee->get_all();
        $this->load->view('old_salary_report', $d);
    }

    function Delete_attendance()
    {

        $d['records'] = $this->employee->get_all();
        $this->load->view('delete_attendance', $d);
    }

    function OldSalary($_id = 0)
    {
        $post = $this->input->post('form');
        $EmployeeID = $post[EmployeeId];
        $Month = $post[adate];

        $d['records2'] = $this->employee->get($EmployeeID);


        // Breaking the year and Month
        $searchyear = substr($Month, 0, 4);
        $searchmonth = substr($Month, -2);
        $d['month'] = $searchmonth;
        $d['year'] = $searchyear;

        // SQl Query to get the records
        $d['records'] = $this->db->query("SELECT * FROM `attendance` WHERE ADate LIKE '$Month%' AND EmployeeId='$EmployeeID';")->result();
//        p($this->db->last_query());
//        p($d);
//        exit;
        $this->load->view('salary_report', $d);
    }

    function marked_salary($_id = 0)
    {

        $d['selected_date'] = $this->uri->segment(3);

        $d['records'] = $this->db->query("SELECT * FROM `attendance` WHERE ADate LIKE '$_id';")->result();
        $d['records2'] = $this->employee->get_all();
//        p($this->db->last_query());
//        p($d);
//        exit;

        $this->load->view('marked_salary', $d);
    }


    function marked_salary_report($_id = 0)
    {

        $d['selected_date'] = $this->input->post('form[adate]');

        $_id = $this->input->post('form[adate]');


        $d['records'] = $this->db->query("SELECT * FROM `attendance` WHERE ADate LIKE '$_id';")->result();
        $d['records2'] = $this->employee->get_all();
//        p($this->db->last_query());
//        p($d);
//        exit;

        $this->load->view('marked_salary', $d);
    }


    public function days()
    {
        $this->load->view('days');
    }

    public function MarkAttendance()
    {
        $d['records'] = $this->employee->order_by('EmployeeNumber', 'ASC')->get_all();
        $this->load->view('mark_attendance', $d);

    }


    ///tests

    public function tests()
    {
        $data['employees'] = $this->employee->get_all();
        $this->load->view('view_test', $data);
        //  $this->load->view('view_test');
    }

    public function search()
    {
        $employee_name = $this->input->get('employee_name');
        $month = $this->input->get('month', TRUE) ? $this->input->get('month', TRUE) : date('Y-m');

        // Get attendance records
        $records = $this->Attendance_model->search_attendance($employee_name, $month);

        // Calculate totals
        $totals = [
            'basic_salary' => 0,
            'ot_payment' => 0,
            'advance_payment' => 0,
            'special_payment' => 0,
            'total_earnings' => 0,
            'net_salary' => 0
        ];

        foreach ($records as $record) {
            $totals['basic_salary'] += floatval($record->PerDaySalary);
            $totals['ot_payment'] += floatval($record->OTPayment);
            $totals['advance_payment'] += floatval($record->AdvanceAmount);
            $totals['special_payment'] += floatval($record->SpecialAmount);
        }

        $totals['total_earnings'] = $totals['basic_salary'] + $totals['ot_payment'] + $totals['special_payment'];
        $totals['net_salary'] = $totals['total_earnings'] - $totals['advance_payment'];

        // Return JSON response - make sure it's properly formatted
        header('Content-Type: application/json');
        echo json_encode([
            'success' => true,
            'records' => $records,
            'totals' => $totals,
            'month' => $month
        ]);
        exit;
    }


    public function get_employee_suggestions()
    {
        $query = $this->input->get('query');
        $this->load->model('Employee_model');

        $this->db->select('EmployeeId, EmployeeName');
        $this->db->from('employee');
        $this->db->like('EmployeeName', $query);
        $this->db->where('IsDeleted', 0);
        $this->db->order_by('EmployeeName', 'ASC');
        $this->db->limit(10);

        $results = $this->db->get()->result();

        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode($results));
    }

    //// test end


}


