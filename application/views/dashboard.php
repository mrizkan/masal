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
                                                    <div class="card-header-right">
                                                        <i class="icofont icofont-rounded-down"></i>
                                                    </div>
                                                </div>
                                                <div class="card-block">
                                                    <form action="<?= base_url('Attendance/CalculateSalary') ?>" method="post" enctype="multipart/form-data">
                                                        <div class="form-group row">
                                                            <input class="col-sm-3 col-form-label form-control form-bg-warning" name="form2[ADate]" value="2025-01-01" type="date" class="form-control">
                                                            <label class="col-sm-2 col-form-label form-txt-success"><b>Start Time</b></label>
                                                            <label class="col-sm-2 col-form-label form-txt-danger"><b>End Time</b></label>
                                                            <label class="col-sm-2 col-form-label form-txt-danger"><b>Advance</b></label>
                                                            <label class="col-sm-2 col-form-label form-txt-danger"><b>Special Amount</b></label>
                                                        </div>
                                                        <?php foreach ($records as $k => $row): ?>
                                                            <div class="form-group row">
                                                                <label class="col-sm-3 col-form-label"><?= $row->EmployeeName  ?></label>
                                                                <input type="hidden" name="form[<?= $k  ?>][EmployeeId]" value="<?= $row->EmployeeId?>" >
                                                                <div class="col-sm-2">
                                                                    <input type="time" class="form-control form-txt-success"  name="form[<?= $k  ?>][Start_Time]">

                                                                </div>
                                                                <div class="col-sm-2">
                                                                    <input type="time" class="form-control form-txt-danger"  name="form[<?= $k  ?>][End_Time]">
                                                                </div>
                                                                <div class="col-sm-2">
                                                                    <input type="number" class="form-control form-txt-danger"  name="form[<?= $k  ?>][Advance]">
                                                                </div>
                                                                <div class="col-sm-2">
                                                                    <input type="number" class="form-control form-txt-danger"  name="form[<?= $k  ?>][Special_Amount]">
                                                                </div>
                                                            </div>
                                                        <?php endforeach; ?>


                                                        <button type="submit" class="submit-btn btn btn-success btn-round">Submit Data</button>
                                                </div> <!-- End of card-block tag -->

                                                <?= form_close() ?>

                                            </div>




                                        </div>

                                        <div class="col-lg-2">
                                            <!--                                            Previous Data start-->
                                            <div class="card">
                                                <div class="card-header">
                                                    <h5>Previous Day's</h5>


                                                </div>
                                                <div class="card-block table-border-style">
                                                    <div class="table-responsive">
                                                        <table class="table">
                                                            <thead>
                                                            <tr>

                                                                <th>Day</th>

                                                            </tr>
                                                            </thead>
                                                            <tbody>
                                                            <?php foreach ($records2 as $k => $row2): ?>
                                                            <tr>
                                                                <td> <?= $row2->ADate?></td>
                                                            </tr>
                                                            <?php endforeach; ?>

                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>
                                            </div>
                                            <!--                                            Previus data end-->

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
