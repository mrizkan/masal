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
                                        <h4>Salary Calculator </h4>
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
                                                    <h5>Mark Attendance</h5>
                                                    <div class="card-header-right">
                                                        <i class="icofont icofont-rounded-down"></i>
                                                    </div>
                                                </div>
                                                <div class="card-block">
                                                    <form action="<?= base_url('Attendance/AddAttendance') ?>" method="post" enctype="multipart/form-data">
                                                        <div class="form-group row">
                                                            <div class="col-sm-12">
                                                                <input type="date" name="form[adate]"  class="form-control">
                                                            </div>
                                                        </div>

                                                        <div class="form-group row">
                                                            <div class="col-sm-6">
                                                                <p>Start Time</p>
                                                                <input type="time" name="form[StartTime]"  class="form-control">
                                                            </div>
                                                            <div class="col-sm-6">
                                                                <p>End Time</p>
                                                                <input type="time" name="form[EndTime]"  class="form-control">
                                                            </div>

                                                        </div>
                                                        <div class="form-group row">

                                                            <div class="col-sm-12">

                                                                <select name="form[EmployeeId]" class="form-control">
                                                                    <option value="">Select Employee</option>
                                                                    <?php foreach ($records as $k => $row): ?>
                                                                        <option value="<?= $row->EmployeeId  ?>"><?= $row->EmployeeName  ?></option>
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
                                                                <input type="number" name="form[AdvanceAmount]"
                                                                       required class="form-control" placeholder="Advance" value="0">
                                                            </div>
                                                            <div class="col-sm-6">
                                                                <p>Special Amount</p>
                                                                <input type="number" name="form[SpecialAmount]"
                                                                       class="form-control" placeholder="Special Amount" value="0">
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
                                            <div class="card">
                                                <div class="card-header">
                                                    <h5>Edit Attendance</h5>
                                                    <div class="card-header-right">
                                                        <i class="icofont icofont-rounded-down"></i>
                                                    </div>
                                                </div>
                                                <div class="card-block">
                                                    <?php if (!empty($records2)) {?>
                                                    <form action="<?= base_url('Attendance/AddAttendance') ?>" method="post" enctype="multipart/form-data">
                                                        <?php }  else {?>
                                                        <form action="<?= base_url('Attendance/EditAttendance') ?>" method="post" enctype="multipart/form-data">
                                                            <?php } ?>
                                                        <div class="form-group row">
                                                            <div class="col-sm-12">
                                                                <input type="date" name="form[adate]"  class="form-control" required value="<?php  if (!empty($records2)) { foreach ($records2 as $k => $row){ echo $row->ADate;} } ?>" <?php  if (!empty($records2)) { echo "readonly";}?>>
                                                                <input type="hidden" name="form[AID]" value="<?php  if (!empty($records2)) { foreach ($records2 as $k => $row){ echo $row->AID;} } ?>">
                                                            </div>
                                                        </div>

                                                        <?php if (!empty($records2)) {?>
                                                            <div class="form-group row">
                                                                <div class="col-sm-6">
                                                                    <p>Start Time</p>
                                                                    <input type="time" name="form[StartTime]"  class="form-control" value="<?php foreach ($records2 as $k => $row){ echo $row->StartTime;} ?>">
                                                                </div>
                                                                <div class="col-sm-6">
                                                                    <p>End Time</p>
                                                                    <input type="time" name="form[EndTime]"  class="form-control" value="<?php foreach ($records2 as $k => $row){ echo $row->EndTime;} ?>">
                                                                </div>

                                                            </div>
                                                        <?php } ?>
                                                        <div class="form-group row">

                                                            <div class="col-sm-12">

                                                                <select name="form[EmployeeId]" class="form-control">
                                                                    <?php if (!empty($records2)) { foreach ($records2 as $k => $row){ ?>
                                                                    <option value="<?php foreach ($records2 as $k => $row){ echo $row->EmployeeId;} ?>"><?php echo $records3; ?></option>
                                                                    <?php }} else{?>
                                                                    <option value="">Select Employee</option>
                                                                    <?php foreach ($records as $k => $row): ?>
                                                                        <option value="<?= $row->EmployeeId  ?>"><?= $row->EmployeeName  ?></option>
                                                                    <?php endforeach; }?>

                                                                </select>

                                                            </div>

                                                        </div>

                                                        <?php if (!empty($records2)) {?>
                                                        <div class="form-group row">
                                                            <!--<div class="col-sm-6">-->
                                                            <!--    <input type="checkbox" name="fullday" value="Pay Full Day"><label for="vehicle1"> Pay Full Day Salary</label>-->
                                                            <!--</div>-->
                                                            <div class="col-sm-6">
                                                                <p>Advance Amount</p>
                                                                <input type="number" name="form[AdvanceAmount]"
                                                                       required class="form-control" placeholder="Advance"  value="<?php foreach ($records2 as $k => $row){ echo $row->AdvanceAmount;} ?>">
                                                            </div>
                                                            <div class="col-sm-6">
                                                                <p>Special Amount</p>
                                                                <input type="number" name="form[SpecialAmount]"
                                                                       class="form-control" placeholder="Special Amount"  value="<?php foreach ($records2 as $k => $row){ echo $row->SpecialAmount;} ?>">
                                                            </div>
                                                        </div>
                                                        <?php } ?>

                                                        <div class="form-group row">

                                                            <div class="col-sm-8">
                                                            </div>
                                                            <div class="col-sm-4">
                                                                <?php if (!empty($records2)) {?>
                                                                <button class="btn btn-danger btn-round">Update</button>
                                                                <?php } else {?>
                                                                    <button class="btn btn-warning btn-round">Edit</button>
                                                                <?php } ?>
                                                            </div>
                                                        </div>

                                                </div> <!-- End of card-block tag -->



                                                <?= form_close() ?>


                                            </div>
<!--                                            Edit Employee Card end here-->
                                        </div>





                                        <div class="col-lg-8">
                                            <div class="card">
                                                <div class="card-header">
                                                    <h5>Salary Report</h5>

                                                    <div class="card-header-right">
                                                        <i class="icofont icofont-rounded-down"></i>


                                                    </div>
                                                </div>
                                                <div class="card-block">
<!--                                                    New Talbe -->
                                                    <div class="dt-responsive table-responsive">
                                                        <table id="basic-btn" class="table table-striped table-bordered nowrap">
                                                            <thead>
                                                            <tr>
                                                                <th>Employee Name</th>

                                                                <th>Option</th>

                                                            </tr>
                                                            </thead>
                                                            <tbody>
                                                            <?php foreach ($records as $k => $row): ?>
                                                                <tr>
                                                                    <td><?= $row->EmployeeName  ?></td>
                                                                    <td>
                                                                        <a href="<?= base_url() ?>Attendance/currentsalary/<?=$row->EmployeeId ?>" > <button class="btn btn-warning btn-mini btn-round">Current Month Salary Report</button></a>
                                                                        <a href="<?= base_url() ?>Attendance/previoussalary/<?=$row->EmployeeId ?>" ><button class="btn btn-success btn-mini btn-round"> Previous Month Salary Report</button></a>
                                                                    </td>
                                                                </tr>
                                                            <?php endforeach; ?>


                                                            </tbody>
                                                            <tfoot>
                                                            <tr>
                                                                <th>Name</th>

                                                                <th>Option</th>

                                                            </tr>
                                                            </tfoot>
                                                        </table>
                                                    </div>
<!--                                                    New Talbe -->

                                                </div>
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
