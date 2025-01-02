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
            <?php
            $currentYear = date('Y');
            $currentMonth = date('m');
            $firstDay = new DateTime("$currentYear-$currentMonth-01");
            $daysInMonth = $firstDay->format('t');



            ?>
            <section class="row" style="display:block;">

                <div class="large invoice-container">
                    <h2 style="font-size:21px; text-align:center;"> <?= $records2->EmployeeName ?> Salary Report - <?= $firstDay->format('F Y')?> </h2>
                    <table>
                        <tr>
                            <!--                            <th>#</th>-->
                            <th>Date</th>
                            <th>In</th>
                            <th>Out</th>

                            <th>Per Day Salary</th>

                            <th>OT Payment</th>
                            <th>Advance</th>
                            <th>Special Amount</th>
                        </tr>

                        <?php
                        $TotalAdvance=0;
                        $TotalOTPayent=0;
                        $TotalOTHours=0;
                        $TotalWOrkingHours=0;
                        $OTHours=0;
                        $TotalPerDaySalary=0;
                        $TotalSpecialAmount=0;
                        $PreviousDate=0;

                        //                        p(number_format((float)$a+$b,2));

                        for ($day = 1; $day <= $daysInMonth; $day++) {
                            // Create a new DateTime object for each day
                            $currentDate = new DateTime("$currentYear-$currentMonth-" . str_pad($day, 2, '0', STR_PAD_LEFT));
                            $originalDate = $currentDate->format('Y-m-j');
                            $dayname= $currentDate->format('l');
                            $newDate = date("Y-m-d", strtotime($originalDate));


                            foreach ($records as $k => $row):




                                // Format and display the day

                                if ($newDate == $str=$row->ADate){
                                    ?>
                                    <tr>

                                        <!--                                <td>--><?php //$str=$row->ADate;  echo $str2 = substr($str, 5);  ?><!--</td>-->

                                        <td><?php echo $currentDate->format('m-j')."  ".$dayname= $currentDate->format('l'); ?></td>
                                        <td><?=  date("g:i a", strtotime("$row->StartTime"))  ?></td>
                                        <td><?=  date("g:i a", strtotime("$row->EndTime"))  ?></td>
                                        <td>Rs. <?= number_format($row->PerDaySalary,2);  ?></td>



                                        <td style="text-align: center">Rs. <?php if(!empty($row->OTPayment)){ echo number_format($row->OTPayment,2);} else {echo "0";}  ?></td>
                                        <td style="text-align: center">Rs. <?= $row->AdvanceAmount  ?></td>
                                        <td style="text-align: center">Rs. <?= $row->SpecialAmount  ?></td>

                                        <?php
                                        $TotalOTHours+=$OTHours;
                                        $TotalOTPayent+=$row->OTPayment;
                                        $TotalAdvance+=$row->AdvanceAmount;
                                        $TotalPerDaySalary+=$row->PerDaySalary;
                                        $TotalSpecialAmount+=$row->SpecialAmount;
                                        $PreviousDate = $newDate;?>
                                    </tr>
                                <?php  } endforeach;  ?>

                            <?php if ($newDate != $str=$row->ADate){ if ($PreviousDate<$newDate){ if ($dayname=='Sunday'){?>
                                <tr>
                                    <td><?php echo $currentDate->format('m-j')."  ".$dayname= $currentDate->format('l'); ; ?></td>
                                    <td> Holiday </td>
                                    <td> - </td>
                                    <td> - </td>
                                    <td> - </td>
                                    <td> - </td>
                                    <td> - </td>

                                </tr>
                            <?php } else{ ?>

                                <tr>
                                    <td><?php echo $currentDate->format('m-j')."  ".$dayname= $currentDate->format('l'); ; ?></td>
                                    <td> AB </td>
                                    <td> - </td>
                                    <td> - </td>
                                    <td> - </td>
                                    <td> - </td>
                                    <td> - </td>

                                </tr>


                            <?php }
                            } } }?>

                        <tr>
                            <td colspan="4"  style="text-align: center">Basic Salary</td>


                            <td  colspan="4"  style="text-align: center">Rs. <?php echo  number_format($TotalPerDaySalary,2); ?></td>

                        </tr>
                        <tr>
                            <td colspan="4"  style="text-align: center">Total OT Payment</td>
                            <td  colspan="4"  style="text-align: center">Rs. <?php echo number_format($TotalOTPayent,2); ?></td>
                        </tr>
                        <tr>
                            <td colspan="4"  style="text-align: center">Total Special Amount</td>
                            <td  colspan="4"  style="text-align: center">Rs. <?php echo number_format($TotalSpecialAmount,2); ?></td>
                        </tr>
                        <tr>
                            <td colspan="4"  style="text-align: center"><b>Total</b> </td>
                            <td  colspan="4"  style="text-align: center"><b>Rs. <?php $Final= $TotalPerDaySalary+ $TotalOTPayent + $TotalSpecialAmount;  echo number_format($Final,2); ?></b></td>
                        </tr>
                        <tr>
                            <td colspan="4"  style="text-align: center">Less Advance </td>
                            <td  colspan="4"  style="text-align: center">Rs. <?= number_format($TotalAdvance,2); ?></td>
                        </tr>
                        <tr>
                            <td colspan="4"  style="text-align: center">Total </td>
                            <td  colspan="4"  style="text-align: center">Rs. <?php $Final= $TotalPerDaySalary+ $TotalOTPayent + $TotalSpecialAmount - $TotalAdvance; echo number_format($Final,2); ?></td>
                        </tr>
                        <tr>
                            <td colspan="4"  style="text-align: center">Less E.P.F. 8% </td>
                            <td  colspan="4"  style="text-align: center"></td>
                        </tr>
                        <tr>
                            <td colspan="4"  style="text-align: center">Total </td>
                            <td  colspan="4"  style="text-align: center"></td></td>
                        </tr>
                        <tr>
                            <td colspan="4"  style="text-align: center">Less O/D </td>
                            <td  colspan="4"  style="text-align: center"> </td>
                        </tr>
                        <tr>
                            <td colspan="4"  style="text-align: center">Total </td>
                            <td  colspan="4"  style="text-align: center"> </td>
                        </tr>

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