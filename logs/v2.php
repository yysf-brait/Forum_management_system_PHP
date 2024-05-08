<?php
session_start();


$logFile = 'user.txt';
$lines = file($logFile);
$stats = [];

foreach ($lines as $line) {
    $parts = explode(' ', $line);
    $details = [];
    foreach ($parts as $part) {
        list($key, $value) = explode(':', $part);
        $details[$key] = $value;
    }

    $date = explode(' ', $details['AT'])[0];     if (!isset($stats[$date])) {
        $stats[$date] = ['newUsers' => [], 'activeUsers' => []];
    }

    if ($details['Action'] == 'Register') {
                $stats[$date]['newUsers'][$details['User']] = true;
    }

    if ($details['Action'] == 'Login') {
                $stats[$date]['activeUsers'][$details['User']] = true;
    }
}

foreach ($stats as $date => $data) {
    $newUserCount = count($data['newUsers']);
    $activeUserCount = count($data['activeUsers']);
    echo "Date: $date\t";
    echo "New Users: $newUserCount\t";
    echo "Active Users: $activeUserCount<br>";
    echo "--------------------------------------------<br>";
}
?>
