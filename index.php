<?php

ini_set('display_errors', 'On');
ini_set('error_reporting', E_ALL);

include_once 'worktime.php';
include_once 'src/autoload.php';

define('NIGHTTIME_START', $SETTINGS_nighttime_start);
define('NIGHTTIME_END', $SETTINGS_nighttime_end);
define('TIME_STEP', 15);

use Worktime\Shift;
use Worktime\ShiftTime;

echo '<table border="1px">';
echo '
    <tr>
        <th>ID</th>
        <th>Name</th>
        <th>Start and end time</th>
        <th>Length</th>
        <th>Daytime hours</th>
        <th>Nighttime hours</th>
    </tr>
';

foreach ($EMPLOYEES as $id => $employee) {
    $shift = new Shift(new ShiftTime($employee['shift_start']), new ShiftTime($employee['shift_end']));

    echo "
    <tr>
        <td>$id</td>
        <td>{$employee['name']}</td>
        
        <td align='center'>{$shift->getStart()} - {$shift->getEnd()}</td>
        
        <td align='center'>{$shift->getLength()->format('%H:%I')}</td>
        
        <td align='center'>{$shift->getDaytime()->format('%H:%I')}</td>
                
        <td align='center'>{$shift->getNighttime()->format('%H:%I')}</td>

    </tr>
    ";
}

echo '</table>';
