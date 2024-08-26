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
            $DayWorkedHours=0;
            $DaySalaryAmount=0;
            $WorkStartedTimeDifference=0;
            $WorkEndTimeDifference=0;
            $MorningWorkStartTime='08:00:00';
            $EveningWorkEndTime='06:00:00';
            $TotalWorkedHours=0;
            $TotalLateMinutes=0;
            $FinalWorkedHours=0;
            $InSideTheOfficeTime=0;


            ///// Start Time Calculation

          $starttime = $post['StartTime'];
          $first = new DateTime($starttime);
          $StartHour = $first->format('H:i:s');

            if ($StartHour < $MorningWorkStartTime ) {
                $t1 = strtotime($MorningWorkStartTime);
                $t2 = strtotime($StartHour);
                $MorningOT = ($t1 - $t2)/3600*60;
            }
            else{
                $t1 = strtotime($MorningWorkStartTime);
                $t2 = strtotime($StartHour);
                $WorkStartedTimeDifference = ($t2 - $t1)/3600*60;
            }

            /////END Time Calculation

            $endTime = $post['EndTime'];
            $EndHour = date('h:i:s', strtotime($endTime));
          if ($EndHour< $EveningWorkEndTime) {

              $t1 = strtotime($EveningWorkEndTime);
              $t2 = strtotime($EndHour);
              $WorkEndTimeDifference = ($t1 - $t2)/3600*60;

           }

       $TotalLateMinutes = $WorkStartedTimeDifference + $WorkEndTimeDifference;


        ///// Inside the Office Hours Calculation
            $datetime_1 = $starttime;
            $datetime_2 = $endTime;

           $InSideTheOfficeTime= round(abs($datetime_2 - $datetime_1)*60);



            if ($EndHour > $EveningWorkEndTime) {
                $t1 = strtotime($EveningWorkEndTime);
                $t2 = strtotime($EndHour);
                $EveningOT = ($t2 - $t1)/3600*60;
            }

            ///// Total OT Hour Calculation
         $TotalOTHoursPerDay = $MorningOT + $EveningOT;



         $TotalOTHoursInMinutes =$TotalOTHoursPerDay;



            ///// Pay Full Day Pressed
            if (isset($_POST['fullday'])) {
                $TotalLateMinutes=0;
                $InSideTheOfficeTime=600+$TotalOTHoursInMinutes+$TotalOTHoursInMinutes;
                $TotalWorkedHours =$InSideTheOfficeTime;

            }
            if ($TotalLateMinutes>0){
                $TotalWorkedHours = 600-$TotalLateMinutes;

            }
            else{
              $TotalWorkedHours = $InSideTheOfficeTime - $TotalOTHoursInMinutes;
            }


           $WorkedTime2 = $TotalWorkedHours;


           $OTHours = $TotalOTHoursPerDay/60;


            if ($OTHours > 0) {

               echo $EmployeeOTPayment = $OTHours * $EmployeeOTPH;
                $EmployeeOTPayment = round($EmployeeOTPayment,2);
                $post['OTPayment'] = $EmployeeOTPayment;



               $EmployeePerDaySalary = $EmployeeBasicSalary;
               $FinalDaySalary = number_format((float)$EmployeePerDaySalary, 2, '.', '');

               $post['PerDaySalary'] = $FinalDaySalary;
            }

            if ($WorkedTime2 <= 600) {

                $EmployeePerDaySalary = $EmployeeBasicSalary;
                $EmployeePerHourSalary = $EmployeePerDaySalary / 9;
                $PerDaySalary = $EmployeePerHourSalary * $WorkedTime2/60;

               $FinalDaySalary = number_format((float)$PerDaySalary, 2, '.', '');

                $post['PerDaySalary'] = $FinalDaySalary;


            }
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


