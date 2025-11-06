<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Salary extends CI_Controller {

    function __construct()
    {
        parent::__construct();
        $this->load->model('Salary_model', 'salary');
        $this->load->model('Employee_model', 'employee');
        $this->load->helper(array('url', 'download'));

        // Increase limits for large operations
        ini_set('memory_limit', '512M');
        ini_set('max_execution_time', '3000');
    }

    /**
     * Dashboard view
     */
    public function dashboard()
    {
        $this->load->view('dashboard');
    }

    /**
     * Generate salary sheets for all employees and download as ZIP
     */
    public function generate_all_salary_sheets()
    {
        // Start output buffering to prevent any output before PDF generation
        ob_start();

        // Disable error display (log instead)
        ini_set('display_errors', 0);
        error_reporting(0);

        // Set session for progress tracking
        $this->session->set_userdata('generation_status', array(
            'completed' => false,
            'progress' => 'Starting generation...',
            'success' => false,
            'count' => 0
        ));

        try {
            // Check if ZIP extension is available
            if (!class_exists('ZipArchive')) {
                throw new Exception("ZIP extension is not installed on this server");
            }

            // Get previous month and year
            $previous_month = date('m', strtotime('-1 month'));
            $previous_year = date('Y', strtotime('-1 month'));

            // Get all active employees
            $employees = $this->db->get_where('employee', array('IsDeleted' => 0))->result();

            if (empty($employees)) {
                $this->session->set_userdata('generation_status', array(
                    'completed' => true,
                    'progress' => 'No employees found',
                    'success' => false,
                    'message' => 'No active employees found',
                    'count' => 0
                ));
                echo "No employees found";
                return;
            }

            // Create temp directory for PDFs
            $temp_dir = FCPATH . 'temp_salary_sheets/';
            if (!is_dir($temp_dir)) {
                if (!mkdir($temp_dir, 0755, true)) {
                    throw new Exception("Failed to create temp directory: " . $temp_dir);
                }
            }

            $pdf_count = 0;
            $month_name = date('F_Y', strtotime('-1 month'));
            $pdf_files = array(); // Store PDF file paths

            // Check if TCPDF exists
            $tcpdf_path = APPPATH . 'third_party/tcpdf/tcpdf.php';
            if (!file_exists($tcpdf_path)) {
                throw new Exception("TCPDF library not found at: " . $tcpdf_path);
            }

            // Include TCPDF directly without loading library
            require_once($tcpdf_path);

            foreach ($employees as $employee) {
                // Update progress
                $this->session->set_userdata('generation_status', array(
                    'completed' => false,
                    'progress' => 'Generating sheet for ' . $employee->EmployeeName . '...',
                    'success' => false,
                    'count' => $pdf_count
                ));

                // Get attendance records for this employee
                $attendance_records = $this->salary->get_employee_attendance(
                    $employee->EmployeeId,
                    $previous_year,
                    $previous_month
                );

                // Skip if no attendance or filter out invalid records
                if (empty($attendance_records)) {
                    continue;
                }

                // Filter out records with missing critical data
                $valid_records = array();
                foreach ($attendance_records as $record) {
                    // Check if critical fields are not empty
                    if (!empty($record->ADate) &&
                        !empty($record->StartTime) &&
                        !empty($record->EndTime)) {

                        // Set default values for optional fields if they're null
                        $record->PerDaySalary = isset($record->PerDaySalary) ? $record->PerDaySalary : 0;
                        $record->OTPayment = isset($record->OTPayment) ? $record->OTPayment : 0;
                        $record->AdvanceAmount = isset($record->AdvanceAmount) ? $record->AdvanceAmount : 0;
                        $record->SpecialAmount = isset($record->SpecialAmount) ? $record->SpecialAmount : 0;
                        $record->OTRate = isset($record->OTRate) ? $record->OTRate : 0;
                        $record->TotalOTHours = isset($record->TotalOTHours) ? $record->TotalOTHours : 0;

                        $valid_records[] = $record;
                    }
                }

                // Skip if no valid records after filtering
                if (empty($valid_records)) {
                    continue;
                }

                // Generate PDF for this employee
                $pdf_path = $this->generate_employee_pdf(
                    $employee,
                    $valid_records,
                    $previous_month,
                    $previous_year,
                    $temp_dir
                );

                if ($pdf_path && file_exists($pdf_path)) {
                    // Store PDF path for later zipping
                    $pdf_files[] = $pdf_path;
                    $pdf_count++;

                    // Small delay to ensure file is completely written
                    usleep(100000); // 0.1 second delay
                }
            }

            // Ensure all files are written before creating ZIP
            clearstatcache();
            sleep(2); // Longer delay to ensure all writes complete

            if ($pdf_count == 0) {
                $this->session->set_userdata('generation_status', array(
                    'completed' => true,
                    'progress' => 'No attendance records found',
                    'success' => false,
                    'message' => 'No attendance records found for previous month',
                    'count' => 0
                ));
                $this->cleanup_temp_files($temp_dir);
                echo "No attendance records found for " . date('F Y', strtotime('-1 month'));
                return;
            }

            // Verify all PDF files exist and are valid
            $verified_files = array();
            foreach ($pdf_files as $pdf_file) {
                if (file_exists($pdf_file) && filesize($pdf_file) > 1000) {
                    $verified_files[] = $pdf_file;
                } else {
                    log_message('error', 'Invalid PDF file skipped: ' . basename($pdf_file));
                }
            }

            if (empty($verified_files)) {
                throw new Exception("No valid PDF files were generated");
            }

            // Create ZIP file using PHP's ZipArchive
            $zip_filename = 'Salary_Sheets_' . $month_name . '.zip';
            $zip_path = $temp_dir . $zip_filename;

            // Delete old ZIP if exists
            if (file_exists($zip_path)) {
                unlink($zip_path);
            }

            $zip = new ZipArchive();
            $zip_open_result = $zip->open($zip_path, ZipArchive::CREATE | ZipArchive::OVERWRITE);

            if ($zip_open_result !== TRUE) {
                throw new Exception("Failed to create ZIP archive. Error code: " . $zip_open_result);
            }

            // Add all verified PDF files to ZIP
            $files_added = 0;
            foreach ($verified_files as $pdf_file) {
                $basename = basename($pdf_file);

                // Add file using file contents instead of path (more reliable)
                $file_contents = file_get_contents($pdf_file);
                if ($file_contents !== false) {
                    $add_result = $zip->addFromString($basename, $file_contents);

                    if ($add_result) {
                        $files_added++;
                        log_message('info', 'Added to ZIP: ' . $basename . ' (' . strlen($file_contents) . ' bytes)');
                    } else {
                        log_message('error', 'Failed to add file to ZIP: ' . $basename);
                    }
                } else {
                    log_message('error', 'Could not read PDF file: ' . $basename);
                }
            }

            // Important: Close the ZIP to finalize writing
            $close_result = $zip->close();

            if (!$close_result) {
                throw new Exception("Failed to close ZIP archive");
            }

            // Wait for ZIP to be written
            clearstatcache();
            sleep(1);

            // Verify ZIP was created
            if (!file_exists($zip_path) || filesize($zip_path) == 0) {
                throw new Exception("ZIP file was not created or is empty. Files added: " . $files_added . " / " . count($verified_files));
            }

            log_message('info', 'ZIP created successfully: ' . $zip_filename . ' with ' . $files_added . ' files (' . filesize($zip_path) . ' bytes)');

            // Update session - completed
            $this->session->set_userdata('generation_status', array(
                'completed' => true,
                'progress' => 'Complete!',
                'success' => true,
                'count' => $pdf_count,
                'message' => 'Successfully generated ' . $pdf_count . ' salary sheets'
            ));

            // Force download
            if (file_exists($zip_path)) {
                // Clear all output buffers
                while (ob_get_level()) {
                    ob_end_clean();
                }

                // Clear any headers
                if (headers_sent($file, $line)) {
                    log_message('error', 'Headers already sent in ' . $file . ' line ' . $line);
                    echo "Error: Headers already sent. Cannot download file.";
                    return;
                }

                // Set headers for download
                header('Content-Type: application/zip');
                header('Content-Disposition: attachment; filename="' . $zip_filename . '"');
                header('Content-Length: ' . filesize($zip_path));
                header('Cache-Control: no-cache, must-revalidate');
                header('Pragma: public');
                header('Expires: 0');

                // Read and output file
                readfile($zip_path);

                // Clean up temp files after download
                $this->cleanup_temp_files($temp_dir);
                exit;
            } else {
                throw new Exception("ZIP file was not created");
            }

        } catch (Exception $e) {
            // Clear output buffer
            ob_end_clean();

            // Log error
            log_message('error', 'Salary Sheet Generation Error: ' . $e->getMessage());

            $this->session->set_userdata('generation_status', array(
                'completed' => true,
                'progress' => 'Error occurred',
                'success' => false,
                'message' => 'Error: ' . $e->getMessage(),
                'count' => 0
            ));

            echo "Error: " . $e->getMessage();
            return;
        }
    }

    /**
     * Generate PDF for a single employee
     */
    private function generate_employee_pdf($employee, $attendance_records, $month, $year, $temp_dir)
    {
        try {
            // Validate employee data
            if (empty($employee->EmployeeName) || empty($employee->EmployeeNumber)) {
                log_message('error', 'Invalid employee data for ID: ' . $employee->EmployeeId);
                return false;
            }

            // Suppress any warnings from TCPDF
            error_reporting(0);

            // Initialize TCPDF with minimal output
            $pdf = new TCPDF('P', 'mm', 'A4', true, 'UTF-8', false);

            // Disable TCPDF default output
            $pdf->SetPrintHeader(false);
            $pdf->SetPrintFooter(false);

            // Set document information
            $pdf->SetCreator('Attendance System');
            $pdf->SetAuthor('HR Department');
            $pdf->SetTitle('Salary Sheet - ' . $employee->EmployeeName);
            $pdf->SetSubject('Monthly Salary Sheet');

            // Set margins
            $pdf->SetMargins(15, 15, 15);
            $pdf->SetAutoPageBreak(TRUE, 15);

            // Add a page
            $pdf->AddPage();

            // Set font
            $pdf->SetFont('helvetica', '', 10);

            // Calculate totals
            $total_days = count($attendance_records);
            $total_basic_salary = 0;
            $total_ot_payment = 0;
            $total_advance = 0;
            $total_special = 0;

            foreach ($attendance_records as $record) {
                $total_basic_salary += floatval($record->PerDaySalary);
                $total_ot_payment += floatval($record->OTPayment);
                $total_advance += floatval($record->AdvanceAmount);
                $total_special += floatval($record->SpecialAmount);
            }

            $total_earnings = $total_basic_salary + $total_ot_payment + $total_special;
            $net_salary = $total_earnings - $total_advance;

            // Build HTML content
            $html = '
            
            <style>
                h1 { 
                    color: #333; 
                    text-align: center; 
                    font-size: 18px; 
                    margin-bottom: 20px;
                    font-weight: bold;
                }
                table { 
                    border-collapse: collapse; 
                    width: 100%; 
                    margin-top: 10px; 
                    font-size: 9px;
                }
                th { 
                    background-color: #f0f0f0; 
                    color: #000; 
                    padding: 8px; 
                    text-align: center; 
                    font-weight: bold;
                    border: 1px solid #000;
                }
                td { 
                    padding: 6px; 
                    border: 1px solid #000;
                    text-align: center;
                }
                .summary-row td {
                    font-weight: bold;
                    text-align: center;
                    padding-left: 10px;
                }
                .summary-value {
                    text-align: right !important;
                    padding-right: 10px;
                }
                .total-row {
                    background-color: #e8e8e8;
                }
            </style>
            
            <h1>' . htmlspecialchars($employee->EmployeeName) . ' Salary Report - ' . date('m - Y', strtotime($year . '-' . $month . '-01')) . '</h1>
            
            <table>
                <thead>
                    <tr>
                        <th style="width: 16%;">Date</th>
                        <th style="width: 12%;">In</th>
                        <th style="width: 12%;">Out</th>
                        <th style="width: 12%;">Per Day Salary</th>
                        <th style="width: 12%;">OT Hours</th>
                        <th style="width: 12%;">OT Payment</th>
                        <th style="width: 12%;">Advance</th>
                        <th style="width: 12%;">Special Amount</th>
                    </tr>
                </thead>
                <tbody>';

            // Generate all days of the month
            $days_in_month = cal_days_in_month(CAL_GREGORIAN, $month, $year);

            // Create array of attendance records indexed by date
            $attendance_by_date = array();
            foreach ($attendance_records as $record) {
                $date_key = date('Y-m-d', strtotime($record->ADate));
                $attendance_by_date[$date_key] = $record;
            }

            for ($day = 1; $day <= $days_in_month; $day++) {
                $current_date = sprintf('%04d-%02d-%02d', $year, $month, $day);
                $day_name = date('l', strtotime($current_date));
                $date_display = date('m-d', strtotime($current_date)) . ' ' . $day_name;

                // Check if attendance exists for this date
                if (isset($attendance_by_date[$current_date])) {
                    $record = $attendance_by_date[$current_date];

                    $start_time = !empty($record->StartTime) ? date('g:i a', strtotime($record->StartTime)) : '-';
                    $end_time = !empty($record->EndTime) ? date('g:i a', strtotime($record->EndTime)) : '-';
                    $per_day = isset($record->PerDaySalary) && $record->PerDaySalary > 0 ? 'Rs. ' . number_format($record->PerDaySalary, 2) : '-';
                    $ot_hours = isset($record->TotalOTHours) && $record->TotalOTHours > 0 ? number_format($record->TotalOTHours, 2) : '0.00';
                    $ot_payment = isset($record->OTPayment) && $record->OTPayment > 0 ? 'Rs. ' . number_format($record->OTPayment, 0) : 'Rs. 0';
                    $advance = isset($record->AdvanceAmount) && $record->AdvanceAmount > 0 ? 'Rs. ' . number_format($record->AdvanceAmount, 0) : 'Rs. 0';
                    $special = isset($record->SpecialAmount) && $record->SpecialAmount > 0 ? 'Rs. ' . number_format($record->SpecialAmount, 0) : 'Rs. 0';

                } else {
                    // Check if it's a Sunday or Holiday
                    if ($day_name == 'Sunday') {
                        $start_time = 'Holiday';
                        $end_time = '-';
                    } else {
                        $start_time = 'AB';
                        $end_time = '-';
                    }
                    $per_day = '-';
                    $ot_hours = '-';
                    $ot_payment = '-';
                    $advance = '-';
                    $special = '-';
                }

                $html .= '
                    <tr>
                        <td  style="width: 16%; font-size:8px; height:18px;">' . $date_display . '</td>
                        <td  style="width: 12%;">' . $start_time . '</td>
                        <td  style="width: 12%;">' . $end_time . '</td>
                        <td  style="width: 12%;">' . $per_day . '</td>
                        <td  style="width: 12%;">' . $ot_hours . '</td>
                        <td  style="width: 12%;">' . $ot_payment . '</td>
                        <td  style="width: 12%;">' . $advance . '</td>
                        <td  style="width: 12%;">' . $special . '</td>
                    </tr>';
            }

            $html .= '
                </tbody>
            </table>
            
            <table style="margin-top: 10px;">
                <tr class="summary-row">
                    <td style="width: 50%;">Basic Salary</td>
                    <td class="summary-value" style="width: 50%;">Rs. ' . number_format($total_basic_salary, 2) . '</td>
                </tr>
                <tr class="summary-row">
                    <td>Total OT Payment</td>
                    <td class="summary-value">Rs. ' . number_format($total_ot_payment, 2) . '</td>
                </tr>
                <tr class="summary-row">
                    <td>Total Special Amount</td>
                    <td class="summary-value">Rs. ' . number_format($total_special, 2) . '</td>
                </tr>
                <tr class="summary-row total-row">
                    <td><strong>Total</strong></td>
                    <td class="summary-value"><strong>Rs. ' . number_format($total_earnings, 2) . '</strong></td>
                </tr>
                <tr class="summary-row">
                    <td>Less Advance</td>
                    <td class="summary-value">Rs. ' . number_format($total_advance, 2) . '</td>
                </tr>
                <tr class="summary-row total-row">
                    <td><strong>Total</strong></td>
                    <td class="summary-value"><strong>Rs. ' . number_format($net_salary, 2) . '</strong></td>
                </tr>
                <tr class="summary-row">
                    <td>Less E.P.F. 8%</td>
                    <td class="summary-value"></td>
                </tr>
                <tr class="summary-row total-row">
                    <td><strong>Total</strong></td>
                    <td class="summary-value"></td>
                </tr>
                <tr class="summary-row">
                    <td>Less O/D</td>
                    <td class="summary-value"></td>
                </tr>
                <tr class="summary-row total-row">
                    <td><strong>Total</strong></td>
                    <td class="summary-value"></td>
                </tr>
            </table>
            ';

            // Write HTML to PDF
            $pdf->writeHTML($html, true, false, true, false, '');

            // Clean filename - remove special characters
            $clean_name = preg_replace('/[^A-Za-z0-9_\-]/', '_', $employee->EmployeeName);
            $clean_number = preg_replace('/[^A-Za-z0-9_\-]/', '_', $employee->EmployeeNumber);

            // Save PDF to temp directory
            $filename = $temp_dir . 'Salary_' . $clean_number . '_' . $clean_name . '_' . date('M_Y', strtotime($year . '-' . $month . '-01')) . '.pdf';

            // Output to file - using 'F' mode to save to disk
            $output_result = $pdf->Output($filename, 'F');

            // Give filesystem time to complete the write
            clearstatcache();
            usleep(50000); // 0.05 second delay

            // Verify file was created and has content
            if (file_exists($filename) && filesize($filename) > 1000) { // At least 1KB
                log_message('info', 'PDF created: ' . basename($filename) . ' (' . filesize($filename) . ' bytes)');
                return $filename;
            } else {
                $size = file_exists($filename) ? filesize($filename) : 0;
                log_message('error', 'PDF file not created properly for employee: ' . $employee->EmployeeName . ' (Size: ' . $size . ' bytes)');
                return false;
            }

        } catch (Exception $e) {
            log_message('error', 'Error generating PDF for employee ' . $employee->EmployeeId . ': ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Cleanup temporary files
     */
    private function cleanup_temp_files($temp_dir)
    {
        if (is_dir($temp_dir)) {
            $files = glob($temp_dir . '*');
            foreach ($files as $file) {
                if (is_file($file)) {
                    @unlink($file);
                }
            }
            @rmdir($temp_dir);
        }
    }

    /**
     * Check generation status via AJAX
     */
    public function check_generation_status()
    {
        $status = $this->session->userdata('generation_status');

        if (!$status) {
            $status = array(
                'completed' => false,
                'progress' => 'Initializing...',
                'success' => false,
                'count' => 0
            );
        }

        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode($status));
    }

    /**
     * Test single PDF generation (for debugging)
     * Access: http://localhost/masal/Salary/test_single_pdf
     */
    public function test_single_pdf()
    {
        // Get first employee with attendance
        $previous_month = date('m', strtotime('-1 month'));
        $previous_year = date('Y', strtotime('-1 month'));

        $employee = $this->db->get_where('employee', array('IsDeleted' => 0))->row();

        if (!$employee) {
            die("No employees found");
        }

        $attendance = $this->salary->get_employee_attendance($employee->EmployeeId, $previous_year, $previous_month);

        if (empty($attendance)) {
            die("No attendance found for employee: " . $employee->EmployeeName);
        }

        // Clean data
        foreach ($attendance as $record) {
            $record->PerDaySalary = isset($record->PerDaySalary) ? $record->PerDaySalary : 0;
            $record->OTPayment = isset($record->OTPayment) ? $record->OTPayment : 0;
            $record->AdvanceAmount = isset($record->AdvanceAmount) ? $record->AdvanceAmount : 0;
            $record->SpecialAmount = isset($record->SpecialAmount) ? $record->SpecialAmount : 0;
        }

        // Include TCPDF
        require_once(APPPATH . 'third_party/tcpdf/tcpdf.php');

        // Generate PDF
        $temp_dir = FCPATH . 'temp_test/';
        if (!is_dir($temp_dir)) {
            mkdir($temp_dir, 0755, true);
        }

        $pdf_path = $this->generate_employee_pdf($employee, $attendance, $previous_month, $previous_year, $temp_dir);

        if ($pdf_path && file_exists($pdf_path)) {
            // Download the PDF directly
            header('Content-Type: application/pdf');
            header('Content-Disposition: inline; filename="' . basename($pdf_path) . '"');
            header('Content-Length: ' . filesize($pdf_path));
            readfile($pdf_path);

            // Clean up
            unlink($pdf_path);
            rmdir($temp_dir);
            exit;
        } else {
            die("Failed to generate PDF");
        }
    }

    /**
     * Test ZIP creation with 2 PDFs (for debugging)
     * Access: http://localhost/masal/Salary/test_zip
     */
    public function test_zip()
    {
        ob_start();

        try {
            $previous_month = date('m', strtotime('-1 month'));
            $previous_year = date('Y', strtotime('-1 month'));

            // Get first 2 employees
            $employees = $this->db->get_where('employee', array('IsDeleted' => 0), 2)->result();

            if (count($employees) < 2) {
                die("Need at least 2 employees");
            }

            // Include TCPDF
            require_once(APPPATH . 'third_party/tcpdf/tcpdf.php');

            // Create temp directory
            $temp_dir = FCPATH . 'temp_test_zip/';
            if (!is_dir($temp_dir)) {
                mkdir($temp_dir, 0755, true);
            }

            $pdf_files = array();

            // Generate PDFs for 2 employees
            foreach ($employees as $employee) {
                $attendance = $this->salary->get_employee_attendance($employee->EmployeeId, $previous_year, $previous_month);

                if (empty($attendance)) {
                    continue;
                }

                // Clean data
                foreach ($attendance as $record) {
                    $record->PerDaySalary = isset($record->PerDaySalary) ? $record->PerDaySalary : 0;
                    $record->OTPayment = isset($record->OTPayment) ? $record->OTPayment : 0;
                    $record->AdvanceAmount = isset($record->AdvanceAmount) ? $record->AdvanceAmount : 0;
                    $record->SpecialAmount = isset($record->SpecialAmount) ? $record->SpecialAmount : 0;
                }

                $pdf_path = $this->generate_employee_pdf($employee, $attendance, $previous_month, $previous_year, $temp_dir);

                if ($pdf_path && file_exists($pdf_path)) {
                    $pdf_files[] = $pdf_path;
                }
            }

            if (count($pdf_files) == 0) {
                die("No PDFs generated");
            }

            echo "Generated " . count($pdf_files) . " PDFs<br><br>";

            foreach ($pdf_files as $f) {
                echo basename($f) . " - " . filesize($f) . " bytes<br>";
            }

            // Wait for files to be written
            sleep(2);
            clearstatcache();

            // Create ZIP
            $zip_filename = 'Test_Salary_Sheets.zip';
            $zip_path = $temp_dir . $zip_filename;

            $zip = new ZipArchive();
            $result = $zip->open($zip_path, ZipArchive::CREATE | ZipArchive::OVERWRITE);

            if ($result !== TRUE) {
                die("Failed to create ZIP. Error: " . $result);
            }

            echo "<br>Adding files to ZIP...<br>";

            foreach ($pdf_files as $pdf_file) {
                $contents = file_get_contents($pdf_file);
                $basename = basename($pdf_file);

                echo "Adding: " . $basename . " (" . strlen($contents) . " bytes)...<br>";

                $added = $zip->addFromString($basename, $contents);

                if ($added) {
                    echo "✓ Success<br>";
                } else {
                    echo "✗ Failed<br>";
                }
            }

            $zip->close();

            sleep(1);
            clearstatcache();

            if (file_exists($zip_path)) {
                echo "<br>ZIP created: " . filesize($zip_path) . " bytes<br>";
                echo "<br><a href='" . base_url('temp_test_zip/' . $zip_filename) . "'>Download ZIP</a>";
            } else {
                echo "<br>ZIP file not created!";
            }

        } catch (Exception $e) {
            echo "Error: " . $e->getMessage();
        }
    }
}