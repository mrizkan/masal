<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Salary Model
 *
 * Handles all database operations related to salary and attendance
 *
 * @package     Attendance System
 * @subpackage  Models
 * @category    Salary Management
 * @author      HR Department
 */

class Salary_model extends CI_Model {

    /**
     * Constructor
     */
    public function __construct()
    {
        parent::__construct();
        $this->load->database();
    }

    /**
     * Get attendance records for a specific employee for a given month and year
     *
     * @param int $employee_id Employee ID
     * @param int $year Year (e.g., 2025)
     * @param int $month Month (1-12)
     * @return array Attendance records
     */
    public function get_employee_attendance($employee_id, $year, $month)
    {
        $this->db->select('
            ADate,
            StartTime,
            EndTime,
            EmployeeId,
            OTRate,
            PerDaySalary,
            TotalOTHours,
            OTPayment,
            AdvanceAmount,
            SpecialAmount
        ');
        $this->db->from('attendance');
        $this->db->where('EmployeeId', $employee_id);
        $this->db->where('YEAR(ADate)', $year);
        $this->db->where('MONTH(ADate)', $month);
        $this->db->where('IsDeleted', 0);
        $this->db->order_by('ADate', 'ASC');

        $query = $this->db->get();
        return $query->result();
    }

    /**
     * Get all employees with their attendance count for a specific month
     *
     * @param int $year Year (e.g., 2025)
     * @param int $month Month (1-12)
     * @return array Employees with attendance count
     */
    public function get_employees_with_attendance($year, $month)
    {
        $this->db->select('
            e.EmployeeId,
            e.EmployeeNumber,
            e.EmployeeName,
            e.FullDaySalary,
            e.OTPH,
            COUNT(a.AID) as attendance_count
        ');
        $this->db->from('employee e');
        $this->db->join('attendance a',
            'e.EmployeeId = a.EmployeeId AND YEAR(a.ADate) = ' . (int)$year . ' AND MONTH(a.ADate) = ' . (int)$month . ' AND a.IsDeleted = 0',
            'left'
        );
        $this->db->where('e.IsDeleted', 0);
        $this->db->group_by('e.EmployeeId');
        $this->db->having('attendance_count >', 0);
        $this->db->order_by('e.EmployeeName', 'ASC');

        $query = $this->db->get();
        return $query->result();
    }

    /**
     * Get salary summary for a specific employee for a given month
     *
     * @param int $employee_id Employee ID
     * @param int $year Year (e.g., 2025)
     * @param int $month Month (1-12)
     * @return object Salary summary
     */
    public function get_salary_summary($employee_id, $year, $month)
    {
        $this->db->select('
            COUNT(*) as total_days,
            SUM(PerDaySalary) as total_basic_salary,
            SUM(OTPayment) as total_ot_payment,
            SUM(AdvanceAmount) as total_advance,
            SUM(SpecialAmount) as total_special,
            SUM(PerDaySalary + OTPayment + SpecialAmount) as total_earnings,
            SUM(PerDaySalary + OTPayment + SpecialAmount - AdvanceAmount) as net_salary
        ');
        $this->db->from('attendance');
        $this->db->where('EmployeeId', $employee_id);
        $this->db->where('YEAR(ADate)', $year);
        $this->db->where('MONTH(ADate)', $month);
        $this->db->where('IsDeleted', 0);

        $query = $this->db->get();
        return $query->row();
    }

    /**
     * Get salary summaries for all employees for a specific month
     *
     * @param int $year Year (e.g., 2025)
     * @param int $month Month (1-12)
     * @return array Salary summaries for all employees
     */
    public function get_all_salary_summaries($year, $month)
    {
        $this->db->select('
            e.EmployeeId,
            e.EmployeeName,
            e.EmployeeNumber,
            e.FullDaySalary,
            e.OTPH,
            COUNT(a.AID) as total_days,
            SUM(a.PerDaySalary) as total_basic_salary,
            SUM(a.OTPayment) as total_ot_payment,
            SUM(a.AdvanceAmount) as total_advance,
            SUM(a.SpecialAmount) as total_special,
            SUM(a.PerDaySalary + a.OTPayment + a.SpecialAmount) as total_earnings,
            SUM(a.PerDaySalary + a.OTPayment + a.SpecialAmount - a.AdvanceAmount) as net_salary
        ');
        $this->db->from('employee e');
        $this->db->join('attendance a',
            'e.EmployeeId = a.EmployeeId AND YEAR(a.ADate) = ' . (int)$year . ' AND MONTH(a.ADate) = ' . (int)$month . ' AND a.IsDeleted = 0',
            'inner'
        );
        $this->db->where('e.IsDeleted', 0);
        $this->db->group_by('e.EmployeeId');
        $this->db->order_by('e.EmployeeName', 'ASC');

        $query = $this->db->get();
        return $query->result();
    }

    /**
     * Check if employee has attendance for a specific month
     *
     * @param int $employee_id Employee ID
     * @param int $year Year (e.g., 2025)
     * @param int $month Month (1-12)
     * @return bool True if attendance exists, false otherwise
     */
    public function has_attendance($employee_id, $year, $month)
    {
        $this->db->where('EmployeeId', $employee_id);
        $this->db->where('YEAR(ADate)', $year);
        $this->db->where('MONTH(ADate)', $month);
        $this->db->where('IsDeleted', 0);

        return $this->db->count_all_results('attendance') > 0;
    }

    /**
     * Get attendance records for date range
     *
     * @param int $employee_id Employee ID
     * @param string $start_date Start date (YYYY-MM-DD)
     * @param string $end_date End date (YYYY-MM-DD)
     * @return array Attendance records
     */
    public function get_attendance_by_date_range($employee_id, $start_date, $end_date)
    {
        $this->db->select('*');
        $this->db->from('attendance');
        $this->db->where('EmployeeId', $employee_id);
        $this->db->where('ADate >=', $start_date);
        $this->db->where('ADate <=', $end_date);
        $this->db->where('IsDeleted', 0);
        $this->db->order_by('ADate', 'ASC');

        $query = $this->db->get();
        return $query->result();
    }

    /**
     * Get total salary stats for all employees for a month
     *
     * @param int $year Year (e.g., 2025)
     * @param int $month Month (1-12)
     * @return object Total salary statistics
     */
    public function get_total_salary_stats($year, $month)
    {
        $this->db->select('
            COUNT(DISTINCT a.EmployeeId) as total_employees,
            COUNT(a.AID) as total_attendance_days,
            SUM(a.PerDaySalary) as total_basic_salary,
            SUM(a.OTPayment) as total_ot_payment,
            SUM(a.AdvanceAmount) as total_advance,
            SUM(a.SpecialAmount) as total_special,
            SUM(a.PerDaySalary + a.OTPayment + a.SpecialAmount) as total_gross,
            SUM(a.PerDaySalary + a.OTPayment + a.SpecialAmount - a.AdvanceAmount) as total_net
        ');
        $this->db->from('attendance a');
        $this->db->join('employee e', 'e.EmployeeId = a.EmployeeId', 'inner');
        $this->db->where('YEAR(a.ADate)', $year);
        $this->db->where('MONTH(a.ADate)', $month);
        $this->db->where('a.IsDeleted', 0);
        $this->db->where('e.IsDeleted', 0);

        $query = $this->db->get();
        return $query->row();
    }

    /**
     * Get employee details by ID
     *
     * @param int $employee_id Employee ID
     * @return object Employee details
     */
    public function get_employee($employee_id)
    {
        $this->db->select('*');
        $this->db->from('employee');
        $this->db->where('EmployeeId', $employee_id);
        $this->db->where('IsDeleted', 0);

        $query = $this->db->get();
        return $query->row();
    }

    /**
     * Get all active employees
     *
     * @return array List of active employees
     */
    public function get_all_employees()
    {
        $this->db->select('*');
        $this->db->from('employee');
        $this->db->where('IsDeleted', 0);
        $this->db->order_by('EmployeeName', 'ASC');

        $query = $this->db->get();
        return $query->result();
    }

    /**
     * Get attendance by specific date
     *
     * @param string $date Date (YYYY-MM-DD)
     * @return array Attendance records for the date
     */
    public function get_attendance_by_date($date)
    {
        $this->db->select('a.*, e.EmployeeName, e.EmployeeNumber');
        $this->db->from('attendance a');
        $this->db->join('employee e', 'e.EmployeeId = a.EmployeeId', 'left');
        $this->db->where('a.ADate', $date);
        $this->db->where('a.IsDeleted', 0);
        $this->db->where('e.IsDeleted', 0);
        $this->db->order_by('e.EmployeeName', 'ASC');

        $query = $this->db->get();
        return $query->result();
    }

    /**
     * Get months with attendance records
     *
     * @param int $limit Number of months to retrieve
     * @return array List of months with attendance
     */
    public function get_months_with_attendance($limit = 12)
    {
        $this->db->select('
            YEAR(ADate) as year,
            MONTH(ADate) as month,
            DATE_FORMAT(ADate, "%Y-%m") as year_month,
            DATE_FORMAT(ADate, "%M %Y") as month_name,
            COUNT(DISTINCT EmployeeId) as employee_count,
            COUNT(*) as attendance_count
        ');
        $this->db->from('attendance');
        $this->db->where('IsDeleted', 0);
        $this->db->group_by('YEAR(ADate), MONTH(ADate)');
        $this->db->order_by('ADate', 'DESC');
        $this->db->limit($limit);

        $query = $this->db->get();
        return $query->result();
    }

    /**
     * Get employee salary history
     *
     * @param int $employee_id Employee ID
     * @param int $limit Number of months to retrieve
     * @return array Salary history
     */
    public function get_employee_salary_history($employee_id, $limit = 6)
    {
        $this->db->select('
            YEAR(ADate) as year,
            MONTH(ADate) as month,
            DATE_FORMAT(ADate, "%M %Y") as month_name,
            COUNT(*) as total_days,
            SUM(PerDaySalary) as total_basic_salary,
            SUM(OTPayment) as total_ot_payment,
            SUM(AdvanceAmount) as total_advance,
            SUM(SpecialAmount) as total_special,
            SUM(PerDaySalary + OTPayment + SpecialAmount) as total_earnings,
            SUM(PerDaySalary + OTPayment + SpecialAmount - AdvanceAmount) as net_salary
        ');
        $this->db->from('attendance');
        $this->db->where('EmployeeId', $employee_id);
        $this->db->where('IsDeleted', 0);
        $this->db->group_by('YEAR(ADate), MONTH(ADate)');
        $this->db->order_by('ADate', 'DESC');
        $this->db->limit($limit);

        $query = $this->db->get();
        return $query->result();
    }
}