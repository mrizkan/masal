<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Employee Attendance Search</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            background-color: #f8f9fa;
            padding-top: 20px;
        }

        .card {
            border-radius: 10px;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);
        }

        .table th {
            background-color: #f1f5f9;
        }

        .totals-row {
            font-weight: bold;
            background-color: #e9ecef;
        }

        .search-container {
            position: relative;
        }

        .search-results {
            position: absolute;
            top: 100%;
            left: 0;
            right: 0;
            background: white;
            border: 1px solid #ced4da;
            border-top: none;
            border-radius: 0 0 5px 5px;
            z-index: 1000;
            max-height: 200px;
            overflow-y: auto;
            display: none;
        }

        .search-results .list-group-item {
            cursor: pointer;
        }

        .search-results .list-group-item:hover {
            background-color: #f8f9fa;
        }

        .loader {
            display: none;
            width: 20px;
            height: 20px;
            border: 3px solid #f3f3f3;
            border-top: 3px solid #3498db;
            border-radius: 50%;
            animation: spin 1s linear infinite;
            margin-left: 10px;
        }

        @keyframes spin {
            0% {
                transform: rotate(0deg);
            }
            100% {
                transform: rotate(360deg);
            }
        }
    </style>
</head>
<body>
<div class="container">
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0"><i class="fas fa-search me-2"></i>Employee Attendance Search</h4>
                </div>
                <div class="card-body">
                    <form id="searchForm">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="search-container">
                                    <label for="employeeSearch" class="form-label">Employee Name</label>
                                    <input type="text" class="form-control" id="employeeSearch"
                                           placeholder="Start typing to search employees...">
                                    <div class="search-results" id="searchResults"></div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <label for="monthSelect" class="form-label">Month</label>
                                <input type="month" class="form-control" id="monthSelect"
                                       value="<?php echo date('Y-m'); ?>">
                            </div>
                            <div class="col-md-2 d-flex align-items-end">
                                <button type="submit" class="btn btn-primary w-100">
                                    <i class="fas fa-search me-1"></i> Search
                                    <div class="loader" id="loader"></div>
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0"><i class="fas fa-list me-2"></i>Attendance Records - <span
                                id="currentMonth"><?php echo date('F Y'); ?></span></h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover" id="attendanceTable">
                            <thead>
                            <tr>
                                <th>Employee Name</th>
                                <th>Date</th>
                                <th>In Time</th>
                                <th>Out Time</th>
                                <th>Basic Salary</th>
                                <th>OT Payment</th>
                                <th>Advance Payment</th>
                                <th>Special Payment</th>
                            </tr>
                            </thead>
                            <tbody id="tableBody">
                            <tr>
                                <td colspan="8" class="text-center">Use the search form to find attendance records</td>
                            </tr>
                            </tbody>
                            <tfoot id="tableFoot" style="display: none;">
                            <tr class="totals-row">
                                <td colspan="4" class="text-end"><strong>Total:</strong></td>
                                <td id="totalBasic">0.00</td>
                                <td id="totalOT">0.00</td>
                                <td id="totalAdvance">0.00</td>
                                <td id="totalSpecial">0.00</td>
                            </tr>
                            <tr class="totals-row">
                                <td colspan="4" class="text-end"><strong>Total Earnings (Basic + OT + Special):</strong>
                                </td>
                                <td colspan="4" id="totalEarnings">0.00</td>
                            </tr>
                            <tr class="totals-row">
                                <td colspan="4" class="text-end"><strong>Less Advance:</strong></td>
                                <td colspan="4" id="lessAdvance">0.00</td>
                            </tr>
                            <tr class="totals-row table-primary">
                                <td colspan="4" class="text-end"><strong>Net Salary:</strong></td>
                                <td colspan="4" id="netSalary">0.00</td>
                            </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    $(document).ready(function () {
        // Employee search functionality
        $('#employeeSearch').on('input', function () {
            const query = $(this).val();
            if (query.length > 1) {
                $.ajax({
                    url: '<?php echo site_url("attendance/get_employee_suggestions"); ?>',
                    method: 'GET',
                    data: {query: query},
                    success: function (response) {
                        const results = response;
                        const resultsContainer = $('#searchResults');
                        resultsContainer.empty();

                        if (results.length > 0) {
                            results.forEach(function (employee) {
                                resultsContainer.append(`<div class='list-group-item' data-id='${employee.EmployeeId}'>${employee.EmployeeName}</div>`);
                            });
                            resultsContainer.show();
                        } else {
                            resultsContainer.hide();
                        }
                    }
                });
            } else {
                $('#searchResults').hide();
            }
        });

        // Handle click on search result
        $(document).on('click', '#searchResults .list-group-item', function () {
            const employeeName = $(this).text();
            $('#employeeSearch').val(employeeName);
            $('#searchResults').hide();
        });

        // Hide search results when clicking outside
        $(document).on('click', function (e) {
            if (!$(e.target).closest('.search-container').length) {
                $('#searchResults').hide();
            }
        });

        // Handle form submission
        $('#searchForm').on('submit', function (e) {
            e.preventDefault();

            const employeeName = $('#employeeSearch').val();
            const month = $('#monthSelect').val();

            // Show loader
            $('#loader').show();

            // Perform search
            $.ajax({
                url: '<?php echo site_url("attendance/search"); ?>',
                method: 'GET',
                dataType: 'json', // Expect JSON response
                data: {employee_name: employeeName, month: month},
                success: function (response) {
                    // No need to parse JSON as we're using dataType: 'json'
                    if (response.success) {
                        updateTable(response.records, response.totals, response.month);
                    } else {
                        alert('Error: No data returned');
                    }
                    $('#loader').hide();
                },
                error: function (xhr, status, error) {
                    console.error('AJAX Error:', status, error);
                    alert('Error fetching data. Please check console for details.');
                    $('#loader').hide();
                }
            });
        });

        // Format date function
        function formatDate(dateString) {
            const date = new Date(dateString);
            return date.toLocaleDateString('en-US', {
                year: 'numeric',
                month: 'short',
                day: 'numeric'
            });
        }

        // Format time function
        function formatTime(timeString) {
            if (timeString === '00:00:00') return 'AB';
            const time = timeString.split(':');
            return `${time[0]}:${time[1]}`;
        }

        // Update table with results
        function updateTable(records, totals, month) {
            const tableBody = $('#tableBody');
            tableBody.empty();

            if (records.length === 0) {
                tableBody.append(
                    `<tr><td colspan="8" class="text-center">No records found for the selected criteria</td></tr>`
                );
                $('#tableFoot').hide();
                return;
            }

            // Format month for display
            const monthDate = new Date(month + '-01');
            const monthName = monthDate.toLocaleDateString('en-US', {
                year: 'numeric',
                month: 'long'
            });
            $('#currentMonth').text(monthName);

            // Add records to table
            records.forEach(function (record) {
                tableBody.append(
                    `<tr>
                            <td>${record.EmployeeName}</td>
                            <td>${formatDate(record.ADate)}</td>
                            <td>${formatTime(record.StartTime)}</td>
                            <td>${formatTime(record.EndTime)}</td>
                            <td>${parseFloat(record.PerDaySalary).toFixed(2)}</td>
                            <td>${parseFloat(record.OTPayment).toFixed(2)}</td>
                            <td>${parseFloat(record.AdvanceAmount).toFixed(2)}</td>
                            <td>${parseFloat(record.SpecialAmount).toFixed(2)}</td>
                        </tr>`
                );
            });

            // Update totals
            $('#totalBasic').text(totals.basic_salary.toFixed(2));
            $('#totalOT').text(totals.ot_payment.toFixed(2));
            $('#totalAdvance').text(totals.advance_payment.toFixed(2));
            $('#totalSpecial').text(totals.special_payment.toFixed(2));
            $('#totalEarnings').text(totals.total_earnings.toFixed(2));
            $('#lessAdvance').text(totals.advance_payment.toFixed(2));
            $('#netSalary').text(totals.net_salary.toFixed(2));

            $('#tableFoot').show();
        }
    });
</script>
</body>
</html>