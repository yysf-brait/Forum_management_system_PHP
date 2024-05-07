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
echo "操作系统使用情况:<br>";
foreach ($osUsage as $os => $count) {
    echo "  $os: $count 次<br>";
}
echo "--------------------------------------------<br>";

echo "浏览器使用情况:<br>";
foreach ($browserUsage as $browser => $count) {
    echo "  $browser: $count 次<br>";
}
echo "--------------------------------------------<br>";

echo "主机使用情况:<br>";
foreach ($hostUsage as $host => $count) {
    echo "  $host: $count 次<br>";
}
?>
