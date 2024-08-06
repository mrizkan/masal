<!DOCTYPE html>
<html lang="en">

<head>
    <?php include('inc/header_top.php');?>
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
                                        <h4>Salary Finder </h4>
                                    </div>

                                </div>
                                <!-- Page header end -->
                                <!-- Page body start -->
                                <div class="page-body">
                                    <div class="row">
                                        <div class="col-lg-4">
                                            <!-- Basic Form Inputs card start -->
                                            <div class="card">
                                                <div class="card-header">
                                                    <h5>Find Previous Month Salary</h5>
                                                    <div class="card-header-right">
                                                        <i class="icofont icofont-rounded-down"></i>
                                                    </div>
                                                </div>
                                                <div class="card-block">
                                                    <form action="<?= base_url('Attendance/AddAttendance') ?>" method="post" enctype="multipart/form-data">
                                                        <div class="form-group row">
                                                            <div class="col-sm-6">
                                                                <input type="month" name="form[adate]"  class="form-control">
                                                            </div>
                                                            <div class="col-sm-6">
                                                            <select name="form[EmployeeId]" class="form-control">
                                                                <option value="">Select Employee</option>
                                                                <?php foreach ($records as $k => $row): ?>
                                                                    <option value="<?= $row->EmployeeId  ?>"><?= $row->EmployeeName  ?></option>
                                                                <?php endforeach; ?>
                                                            </select>
                                                            </div>
                                                        </div>





                                                        <div class="form-group row">

                                                            <div class="col-sm-8">
                                                            </div>
                                                            <div class="col-sm-4">

                                                                <button class="btn btn-primary btn-round">Search</button>
                                                            </div>
                                                        </div>

                                                </div> <!-- End of card-block tag -->



                                                <?= form_close() ?>


                                            </div>
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

<?php include('inc/footer_below.php');?>
</body>

</html>
