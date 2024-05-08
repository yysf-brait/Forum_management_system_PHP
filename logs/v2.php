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

    $date = explode(' ', $details['AT'])[0]; // 获取日期部分
    if (!isset($stats[$date])) {
        $stats[$date] = ['newUsers' => [], 'activeUsers' => []];
    }

    if ($details['Action'] == 'Register') {
        // 这是一个新注册的用户
        $stats[$date]['newUsers'][$details['User']] = true;
    }

    if ($details['Action'] == 'Login') {
        // 这是一个活跃的用户
        $stats[$date]['activeUsers'][$details['User']] = true;
    }
}

// 输出统计结果
foreach ($stats as $date => $data) {
    $newUserCount = count($data['newUsers']);
    $activeUserCount = count($data['activeUsers']);
    echo "Date: $date\t";
    echo "New Users: $newUserCount\t";
    echo "Active Users: $activeUserCount<br>";
    echo "--------------------------------------------<br>";
}
?>
