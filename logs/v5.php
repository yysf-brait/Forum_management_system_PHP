<?php
$logFile = 'user.txt';
$lines = file($logFile);
$osUsage = [];
$browserUsage = [];
$hostUsage = [];

foreach ($lines as $line) {
    $parts = explode(' ', $line);
    $details = [];
    foreach ($parts as $part) {
        list($key, $value) = explode(':', $part, 2);
        $details[$key] = $value;
    }

    if (isset($details['OS'])) {
        $os = $details['OS'];
        if (!isset($osUsage[$os])) {
            $osUsage[$os] = 0;
        }
        $osUsage[$os]++;
    }

    if (isset($details['Browser'])) {
        $browser = $details['Browser'];
        if (!isset($browserUsage[$browser])) {
            $browserUsage[$browser] = 0;
        }
        $browserUsage[$browser]++;
    }

    if (isset($details['Host'])) {
        $host = $details['Host'];
        if (!isset($hostUsage[$host])) {
            $hostUsage[$host] = 0;
        }
        $hostUsage[$host]++;
    }
}

// 输出统计结果
echo "OS usage:<br>";
$total = array_sum($osUsage);
foreach ($osUsage as $os => $count) {
    echo "  $os: $count time (" . number_format($count / $total * 100, 2) . "%)<br>";
}
echo "--------------------------------------------<br>";

echo "Browser usage:<br>";
$total = array_sum($browserUsage);
foreach ($browserUsage as $browser => $count) {
    echo "  $browser: $count time (" . number_format($count / $total * 100, 2) . "%)<br>";
}
echo "--------------------------------------------<br>";

echo "Host usage:<br>";
$total = array_sum($hostUsage);
foreach ($hostUsage as $host => $count) {
    echo "  $host: $count time (" . number_format($count / $total * 100, 2) . "%)<br>";
}
?>
