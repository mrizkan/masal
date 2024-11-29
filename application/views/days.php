<?php
// Get the current year and month
$currentYear = date('Y');
$currentMonth = date('m');

// Create a DateTime object for the first day of the current month
$firstDay = new DateTime("$currentYear-$currentMonth-01");

// Get the number of days in the current month
$daysInMonth = $firstDay->format('t');

// Display header
echo "Days in " . $firstDay->format('F Y') . ":<br>";

// Loop through each day of the month
for ($day = 1; $day <= $daysInMonth; $day++) {
    // Create a new DateTime object for each day
    $currentDate = new DateTime("$currentYear-$currentMonth-" . str_pad($day, 2, '0', STR_PAD_LEFT));

    // Format and display the day
    echo $currentDate->format('j') . "<br>";
}
?>