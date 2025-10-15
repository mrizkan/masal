<link rel="stylesheet" type="text/css" href="<?= base_url() ?>bower_components/bootstrap/css/deliverynote.css">

<!DOCTYPE html>
<html lang="en" >
<head>
    <meta charset="UTF-8">

    <link rel='stylesheet' href='https://fonts.googleapis.com/css?family=Lora:400,700|Montserrat:300,400,700'>
    <link rel='stylesheet' href='https://cdnjs.cloudflare.com/ajax/libs/foundation/6.3.1/css/foundation-flex.min.css'>
    <link rel='stylesheet' href='https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css'><link rel="stylesheet" href="./style.css">
    <style>
        @media print {
            .control-group {
                display: none;
            }
        }
    </style>
</head>
<body>
<!-- partial:index.partial.html -->

<div class="row expanded">
    <main class="columns">
        <div class="inner-container">
            <header class="row control-group">
                <a class="button" href="<?= base_url() ?>" >
                    <i class="ion-ios-home"></i>
                    Home
                </a>
                &nbsp;&nbsp;<a href="javascript:window.print()" class="button"><i class="ion-ios-paper-outline"></i> Print Report</a>

                <style>
                    table, th, td {
                        border: 1px solid black;
                        border-collapse: collapse;
                    }
                </style>
            </header>

            <section class="row" style="display:block;">

                <div class="large invoice-container">
                    <h2 style="font-size:21px; text-align:center;"> Attendance Report - <?= $selected_date ?> </h2>
                    <table>
                        <tr>
                            <!--                            <th>#</th>-->
                            <th>#</th>
                            <th>Employee Name</th>
                            <th>In</th>
                            <th>Out</th>

                            <th>Per Day Salary</th>

                            <th>OT Payment</th>
                            <th>Advance</th>
                            <th>Special Amount</th>
                        </tr>

                        <?php
                        // Create a lookup array that combines employee details with attendance
                        $attendanceLookup = [];
                        $numbercount=1;
                        // First, add all employees with null attendance (for handling missing records)
                        foreach ($records2 as $row2) {
                            $attendanceLookup[$row2->EmployeeId] = [
                                'employee' => $row2,
                                'attendance' => null
                            ];
                        }

                        // Then, add attendance records where they exist
                        foreach ($records as $row) {
                            if (isset($attendanceLookup[$row->EmployeeId])) {
                                $attendanceLookup[$row->EmployeeId]['attendance'] = $row;
                            }
                        }

                        // Now use the lookup array
                        foreach ($attendanceLookup as $data):
                            $row2 = $data['employee'];
                            $row = $data['attendance'];

                            if ($row): ?>
                                <?php if ($row->StartTime == '00:00:00'): ?>
                                    <tr>
                                        <td><?php echo $numbercount; ?></td>
                                        <td><?php echo $row2->EmployeeName; ?></td>
                                        <td>AB</td>
                                        <td>-</td>
                                        <td>-</td>
                                        <td>-</td>
                                        <td>-</td>
                                        <td>-</td>
                                    </tr>
                                <?php else: ?>
                                    <tr>
                                        <td><?php echo $numbercount; ?></td>
                                        <td><?php echo $row2->EmployeeName; ?></td>
                                        <td><?php echo $row->StartTime; ?></td>
                                        <td><?php echo $row->EndTime; ?></td>
                                        <td><?php echo number_format($row->PerDaySalary, 2); ?></td>
                                        <td><?php echo number_format($row->OTPayment, 2); ?></td>
                                        <td><?php echo $row->AdvanceAmount; ?></td>
                                        <td><?php echo $row->SpecialAmount; ?></td>
                                    </tr>
                                <?php endif;  $numbercount++; ?>
                            <?php else: ?>
                                <tr>
                                    <td><?php echo $numbercount; ?></td>
                                    <td><?php echo $row2->EmployeeName; ?></td>
                                    <td>AB</td>
                                    <td>-</td>
                                    <td>-</td>
                                    <td>-</td>
                                    <td>-</td>
                                    <td>-</td>
                                </tr>
                            <?php $numbercount++; endif;  ?>
                        <?php endforeach; ?>
                    </table>


                </div>
            </section>
        </div>
    </main>
</div>
<!-- partial -->
<script src='https://cdnjs.cloudflare.com/ajax/libs/jquery/3.1.1/jquery.min.js'></script>
<script src='https://cdnjs.cloudflare.com/ajax/libs/foundation/6.3.1/js/foundation.js'></script>
</body>
</html>