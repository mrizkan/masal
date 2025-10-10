<!DOCTYPE html>
<html lang="en">

<head>
    <?php include('inc/header_top.php'); ?>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.66/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.66/vfs_fonts.min.js"></script>
</head>
<!-- Menu horizontal icon fixed -->

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
        <?php include('inc/top_bar.php'); ?>
        <!-- Menu header end -->
        <div class="pcoded-main-container">
            <?php include('inc/navigation.php'); ?>
            <div class="pcoded-wrapper">
                <div class="pcoded-content">
                    <div class="pcoded-inner-content">
                        <!-- Main-body start -->
                        <div class="main-body">
                            <div class="page-wrapper">
                                <!-- Page header start -->
                                <div class="page-header">

<!--                                    <div class="page-header-title">-->
<!--                                        <h4>Emp</h4>-->
<!--                                    </div>-->

                                </div>
                                <!-- Page header end -->
                                <!-- Page body start -->
                                <div class="page-body">
                                    <div class="row">
                                        <div class="col-lg-4">
                                        </div>
                                        <div class="col-lg-4">
                                            <?php $this->view('inc/success_notification.php'); ?>
                                            <div id="status"></div>
                                            <!-- Basic Form Inputs card start -->
                                            <div class="card">
                                                <div class="card-header">
                                                    <h5>Mark Attendance</h5>
                                                    <div class="card-header-right">
                                                        <i class="icofont icofont-rounded-down"></i>
                                                    </div>
                                                </div>
                                                <div class="card-block">
<!--                                                    <form action="--><?php //= base_url('Attendance/CalculateSingleSalary') ?><!--"  method="post" enctype="multipart/form-data">-->
                                                    <form id="contactfrm">
                                                        <div class="form-group row">
                                                            <div class="col-sm-12">
                                                                <input type="date" name="form2[ADate]"
                                                                       class="form-control"  id="myDateInput">
                                                            </div>
                                                        </div>

                                                        <div class="form-group row">
                                                            <div class="col-sm-6">
                                                                <p>Start Time</p>
                                                                <input type="time" name="form[Start_Time]"
                                                                       class="form-control">
                                                            </div>
                                                            <div class="col-sm-6">
                                                                <p>End Time</p>
                                                                <input type="time" name="form[End_Time]"
                                                                       class="form-control">
                                                            </div>

                                                        </div>
                                                        <div class="form-group row">

                                                            <div class="col-sm-12">

                                                                <select name="form[EmployeeId]" class="form-control">
                                                                    <option value="">Select Employee</option>
                                                                    <?php foreach ($records as $k => $row): ?>
                                                                        <option value="<?= $row->EmployeeId ?>"><?= $row->EmployeeName ?></option>
                                                                    <?php endforeach; ?>
                                                                </select>

                                                            </div>

                                                        </div>

                                                        <div class="form-group row">
                                                            <!--<div class="col-sm-6">-->
                                                            <!--    <input type="checkbox" name="fullday" value="Pay Full Day"><label for="vehicle1"> Pay Full Day Salary</label>-->
                                                            <!--</div>-->
                                                            <div class="col-sm-6">
                                                                <p>Advance Amount</p>
                                                                <input type="number" name="form[Advance]"
                                                                       required class="form-control"
                                                                       placeholder="Advance" value="0">
                                                            </div>
                                                            <div class="col-sm-6">
                                                                <p>Special Amount</p>
                                                                <input type="number" name="form[Special_Amount]"
                                                                       class="form-control" placeholder="Special Amount"
                                                                       value="0">
                                                            </div>


                                                        </div>
                                                        <div class="form-group row">


                                                            <div class="col-sm-4">

                                                                <button class="btn btn-primary btn-round">Mark</button>
                                                            </div>
                                                        </div>

                                                </div> <!-- End of card-block tag -->


                                                <?= form_close() ?>


                                            </div>
                                            <!--                                            Edit Employee Card start here-->

                                            <!--                                            Edit Employee Card end here-->
                                        </div>


                                        <div class="col-lg-4">

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
</div>
</div>


<script src="<?= base_url() ?>assets/js/jquery-1.11.1.min.js"></script>
<script src="<?= base_url() ?>assets/js/bootstrap.min.js"></script>


<script>

    // this is the id of the form
    $(document).ready(function(){

        $("#contactfrm").submit(function(e) {
            //  e.preventDefault();
            $.ajax({
                type: "POST",
                url: 'Attendance/CalculateSingleSalary',
                data: $(this).serialize(), // serializes the form's elements.
                success: function(data)
                {
                    $('#status').html('<div class="alert alert-success background-success"> <button type="button" class="close" data-dismiss="alert" aria-label="Close"> <i class="icofont icofont-close-line-circled text-white"></i> </button> <strong>Attendance Marked Successfully</strong> </div>');
                    $('#contactfrm')[0].reset();
                }
            });
            return false;
        });

    });
</script>




<script>
    document.addEventListener('DOMContentLoaded', function() {
        const dateInput = document.getElementById('myDateInput');

        // Get today's date
        const today = new Date();

        // Format the date as YYYY-MM-DD
        const year = today.getFullYear();
        const month = String(today.getMonth() + 1).padStart(2, '0'); // Month is 0-indexed
        const day = String(today.getDate()).padStart(2, '0');

        const formattedDate = `${year}-${month}-${day}`;

        // Set the value of the input field
        dateInput.value = formattedDate;
    });
</script>

<?php include('inc/footer_below.php'); ?>
</body>

</html>
