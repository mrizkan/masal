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
                <a class="button" href="<?= base_url() ?>Home/dashboard" >
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
                            <th>Employee Name</th>
                            <th>In</th>
                            <th>Out</th>

                            <th>Per Day Salary</th>

                            <th>OT Payment</th>
                            <th>Advance</th>
                            <th>Special Amount</th>
                        </tr>

                        <?php foreach ($records2 as $k2 => $row2):
                            foreach ($records as $k => $row):

                                if ($row2->EmployeeId == $row->EmployeeId) { ?>
                                <tr>
                                    <td><?php echo $row2->EmployeeName;  ?></td>
                                    <td><?php echo $row->StartTime;  ?></td>
                                    <td><?php echo $row->EndTime;  ?></td>
                                    <td><?php echo $row->PerDaySalary;  ?></td>
                                    <td><?php echo $row->OTPayment;  ?></td>
                                    <td><?php echo $row->AdvanceAmount;  ?></td>
                                    <td><?php echo $row->SpecialAmount;  ?></td>
                            </tr>


                             <?php } endforeach;
                                endforeach;  ?>





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