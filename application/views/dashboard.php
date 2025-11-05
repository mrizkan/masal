<!DOCTYPE html>
<html lang="en">

<head>
    <?php include('inc/header_top.php');?>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.66/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.66/vfs_fonts.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <style>
        .download-section {
            min-height: 400px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .download-card {
            text-align: center;
            padding: 50px;
        }

        .loading-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.7);
            display: none;
            z-index: 9999;
            align-items: center;
            justify-content: center;
        }
        .loading-content {
            background: white;
            padding: 30px;
            border-radius: 10px;
            text-align: center;
        }
        .spinner {
            border: 4px solid #f3f3f3;
            border-top: 4px solid #3498db;
            border-radius: 50%;
            width: 50px;
            height: 50px;
            animation: spin 1s linear infinite;
            margin: 0 auto 20px;
        }
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
    </style>


</head>

<body class="horizontal-icon-fixed">
<!-- Pre-loader start -->
<div class="theme-loader">
    <div class="ball-scale">
        <div></div>
    </div>
</div>
<!-- Pre-loader end -->
<div id="pcoded" class="pcoded">

    <div class="pcoded-container">
        <!-- Menu header start -->
        <?php include('inc/top_bar.php');?>
        <!-- Menu header end -->
        <div class="pcoded-main-container">
            <?php include('inc/navigation.php');?>
            <div class="pcoded-wrapper">
                <div class="pcoded-content">
                    <div class="pcoded-inner-content">
                        <!-- Main-body start -->
                        <div class="main-body">
                            <div class="page-wrapper">
                                <!-- Page header start -->
                                <div class="page-header">
                                    <?php $this->view('inc/success_notification.php'); ?>
                                    <div class="page-header-title">
                                        <h4>Mark Attendance </h4>
                                    </div>

                                </div>
                                <!-- Page header end -->
                                <!-- Page body start -->
                                <div class="page-body">
                                    <div class="row">
                                        <div class="col-lg-10">
                                            <!-- Basic Form Inputs card start -->
                                            <div class="card">
                                                <div class="card-header">
                                                    <h5>Mark Attendance</h5>
                                                    <button type="button" class="btn btn-danger btn-round download-btn btn-mini" id="downloadSalarySheets" style="float: right; margin-right: 4%;">
                                                        <i class="icofont icofont-download"></i> Download Employee Salary Sheets
                                                    </button>
                                                    <div class="card-header-right">
                                                        <i class="icofont icofont-rounded-down"></i>
                                                    </div>
                                                </div>
                                                <div class="card-block">
                                                    <div class="form-group row">
                                                        <input id="attendance_date" class="col-sm-3 col-form-label form-control form-bg-warning" type="date">
                                                        <label class="col-sm-2 col-form-label form-txt-success"><b>Start Time</b></label>
                                                        <label class="col-sm-2 col-form-label form-txt-danger"><b>End Time</b></label>
                                                        <label class="col-sm-1 col-form-label form-txt-danger"><b>Advance</b></label>
                                                        <label class="col-sm-1 col-form-label form-txt-danger"><b>Special Amount</b></label>
                                                        <label class="col-sm-1 col-form-label"><b>Action</b></label>
                                                    </div>
                                                    <?php foreach ($records as $k => $row): ?>
                                                        <div class="form-group row employee-row" id="employee-row-<?= $row->EmployeeId ?>">
                                                            <label class="col-sm-3 col-form-label"><?= $row->EmployeeName  ?></label>
                                                            <input type="hidden" class="employee-id" value="<?= $row->EmployeeId?>">
                                                            <div class="col-sm-2">
                                                                <input type="time" class="form-control form-txt-success start-time" id="start-time-<?= $row->EmployeeId ?>" data-employee="<?= $row->EmployeeId ?>">
                                                            </div>
                                                            <div class="col-sm-2">
                                                                <input type="time" class="form-control form-txt-danger end-time" id="end-time-<?= $row->EmployeeId ?>" data-employee="<?= $row->EmployeeId ?>">
                                                            </div>
                                                            <div class="col-sm-1">
                                                                <input type="number" class="form-control form-txt-danger advance" id="advance-<?= $row->EmployeeId ?>" value="0" data-employee="<?= $row->EmployeeId ?>">
                                                            </div>
                                                            <div class="col-sm-1">
                                                                <input type="number" class="form-control form-txt-danger special-amount" id="special-amount-<?= $row->EmployeeId ?>" value="0" data-employee="<?= $row->EmployeeId ?>">
                                                            </div>
                                                            <div class="col-sm-1">
                                                                <button type="button" class="btn btn-success btn-sm submit-single" data-employee="<?= $row->EmployeeId ?>">
                                                                    <i class="icofont icofont-check"></i> Submit
                                                                </button>
                                                            </div>
                                                            <div class="col-sm-2">
                                                                <div class="alert alert-success" id="success-msg-<?= $row->EmployeeId ?>" style="display: none;"></div>
                                                                <div class="alert alert-danger" id="error-msg-<?= $row->EmployeeId ?>" style="display: none; margin-top: 10px;"></div>
                                                            </div>

                                                        </div>
                                                    <?php endforeach; ?>

                                                </div> <!-- End of card-block tag -->

                                            </div>

                                        </div>

                                        <div class="col-lg-2">
                                            <!--Previous Data start-->
                                            <div class="card">
                                                <div class="card-header">
                                                    <h5>Previous Day's</h5>
                                                </div>
                                                <div class="card-block table-border-style">
                                                    <div class="table-responsive">
                                                        <table class="table">
                                                            <thead>
                                                            <tr>
                                                            </tr>
                                                            </thead>
                                                            <tbody>
                                                            <?php foreach ($records2 as $k => $row2): ?>
                                                                <tr>
                                                                    <td> <?= $row2->ADate?>
                                                                        <div class="btn-group " role="group" data-toggle="tooltip" data-placement="top" title="" data-original-title=".btn-xlg">
                                                                            <button type="button" class="btn btn-primary btn-mini waves-effect waves-light">
                                                                                <a href="<?= base_url() ?>Attendance/marked_salary/<?= $row2->ADate?>" target="_blank" style="color: white;">View </a>
                                                                            </button>
                                                                        </div>
                                                                    </td>
                                                                </tr>
                                                            <?php endforeach; ?>
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>
                                            </div>
                                            <!--Previous data end-->
                                        </div>

                                    </div>

                                </div>
                            </div>
                            <!-- Page body end -->
                        </div>
                    </div>
                    <!-- Main-body end -->

                </div>
            </div>
        </div>
    </div>
</div>



<!-- Loading Overlay -->
<div class="loading-overlay" id="loadingOverlay">
    <div class="loading-content">
        <div class="spinner"></div>
        <h4>Generating Salary Sheets...</h4>
        <p id="progress-text">Please wait while we generate PDFs for all employees</p>
    </div>
</div>

<!-- Don't include footer yet, we need to add script first -->

<script>
    jQuery(document).ready(function($) {
        $('#downloadSalarySheets').on('click', function() {
            var $btn = $(this);
            $btn.prop('disabled', true);

            // Show loading overlay
            $('#loadingOverlay').css('display', 'flex');
            $('#status-message').html('');

            // Trigger download by opening in new window
            var downloadUrl = '<?= base_url("Salary/generate_all_salary_sheets") ?>';
            window.open(downloadUrl, '_blank');

            // Check for completion
            var checkCount = 0;
            var checkInterval = setInterval(function() {
                checkCount++;

                $.ajax({
                    url: '<?= base_url("Salary/check_generation_status") ?>',
                    type: 'GET',
                    dataType: 'json',
                    success: function(response) {
                        if (response.completed) {
                            clearInterval(checkInterval);
                            $('#loadingOverlay').hide();
                            $btn.prop('disabled', false);

                            if (response.success) {
                                $('#status-message').html(
                                    '<div class="alert alert-success">' +
                                    '<i class="icofont icofont-check-circled"></i> ' +
                                    'Successfully generated ' + response.count + ' salary sheets!' +
                                    '</div>'
                                );
                            } else {
                                $('#status-message').html(
                                    '<div class="alert alert-danger">' +
                                    '<i class="icofont icofont-close-circled"></i> ' +
                                    (response.message || 'An error occurred') +
                                    '</div>'
                                );
                            }
                        } else if (response.progress) {
                            $('#progress-text').text(response.progress);
                        }
                    },
                    error: function() {
                        // Continue checking even on error
                        if (checkCount > 120) { // Stop after 2 minutes
                            clearInterval(checkInterval);
                            $('#loadingOverlay').hide();
                            $btn.prop('disabled', false);
                            $('#status-message').html(
                                '<div class="alert alert-warning">' +
                                'Generation is taking longer than expected. Please check downloads folder.' +
                                '</div>'
                            );
                        }
                    }
                });
            }, 1000);

            // Timeout after 2 minutes
            setTimeout(function() {
                clearInterval(checkInterval);
                $('#loadingOverlay').hide();
                $btn.prop('disabled', false);
            }, 120000);
        });
    });
</script>

<?php include('inc/footer_below.php');?>

<script>
    $(document).ready(function() {
        // Handle individual submit button click
        $('.submit-single').click(function() {
            var employeeId = $(this).data('employee');
            var attendanceDate = $('#attendance_date').val();
            var startTime = $('#start-time-' + employeeId).val();
            var endTime = $('#end-time-' + employeeId).val();
            var advance = $('#advance-' + employeeId).val();
            var specialAmount = $('#special-amount-' + employeeId).val();

            // Hide previous messages
            $('#success-msg-' + employeeId).hide();
            $('#error-msg-' + employeeId).hide();

            // Validation
            if (!attendanceDate) {
                $('#error-msg-' + employeeId).text('Please select a date').show();
                return;
            }

            if (!startTime || !endTime) {
                $('#error-msg-' + employeeId).text('Please enter both start and end time').show();
                return;
            }

            // Disable submit button
            var $submitBtn = $(this);
            $submitBtn.prop('disabled', true).html('<i class="icofont icofont-spinner icofont-spin"></i> Submitting...');

            // Prepare data
            var formData = {
                ADate: attendanceDate,
                EmployeeId: employeeId,
                Start_Time: startTime,
                End_Time: endTime,
                Advance: advance || 0,
                Special_Amount: specialAmount || 0,
                <?= $this->security->get_csrf_token_name(); ?>: '<?= $this->security->get_csrf_hash(); ?>'
            };

            console.log('Submitting data:', formData); // Debug log

            // AJAX request
            $.ajax({
                url: '<?= base_url("Attendance/CalculateSalarySingle") ?>',
                type: 'POST',
                data: formData,
                cache: false,
                success: function(response) {
                    console.log('Raw response:', response); // Debug log
                    console.log('Response type:', typeof response); // Debug log

                    // If response is string, try to parse it
                    if (typeof response === 'string') {
                        try {
                            response = JSON.parse(response);
                        } catch (e) {
                            console.error('JSON parse error:', e);
                            console.error('Response text:', response);
                            $('#error-msg-' + employeeId).text('Invalid response from server. Check console for details.').show();
                            $submitBtn.prop('disabled', false).html('<i class="icofont icofont-check"></i> Submit');
                            return;
                        }
                    }

                    if (response.status === 'success') {
                        $('#success-msg-' + employeeId).html(response.message).show();

                        // Disable all input fields for this employee
                        $('#start-time-' + employeeId).prop('readonly', true);
                        $('#end-time-' + employeeId).prop('readonly', true);
                        $('#advance-' + employeeId).prop('readonly', true);
                        $('#special-amount-' + employeeId).prop('readonly', true);

                        // Change button text and keep it disabled
                        $submitBtn.html('<i class="icofont icofont-check-circled"></i> Submitted')
                            .removeClass('btn-success')
                            .addClass('btn-secondary');
                    } else {
                        $('#error-msg-' + employeeId).text(response.message || 'An error occurred').show();
                        // Re-enable submit button
                        $submitBtn.prop('disabled', false).html('<i class="icofont icofont-check"></i> Submit');
                    }
                },
                error: function(xhr, status, error) {
                    console.error('AJAX Error:', {
                        status: status,
                        error: error,
                        response: xhr.responseText
                    }); // Debug log

                    var errorMessage = 'An error occurred. Please try again.';

                    // Try to parse error response
                    try {
                        var errorResponse = JSON.parse(xhr.responseText);
                        if (errorResponse.message) {
                            errorMessage = errorResponse.message;
                        }
                    } catch (e) {
                        // If response is not JSON, show generic error
                        if (xhr.responseText) {
                            errorMessage = 'Server error: ' + xhr.status;
                        }
                    }

                    $('#error-msg-' + employeeId).text(errorMessage).show();
                    // Re-enable submit button
                    $submitBtn.prop('disabled', false).html('<i class="icofont icofont-check"></i> Submit');
                }
            });
        });
    });
</script>

</body>

</html>